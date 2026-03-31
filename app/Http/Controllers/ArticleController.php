<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $articles = Article::with(['category:id,name', 'author:id,name'])
            ->when($request->search, fn ($q, $s) =>
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('excerpt', 'like', "%{$s}%")
            )
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($articles);
    }

    /** GET /api/articles/trending — public, top by views */
    public function trending(Request $request): JsonResponse
    {
        $default = (int) \App\Models\Setting::where('key', 'trending_count')->value('value') ?: 7;
        $limit   = max(1, min($request->integer('limit', $default), 20));

        $articles = Article::with(['category:id,name,color'])
            ->where('status', 'published')
            ->orderByDesc('views')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'views', 'category_id']);

        return response()->json($articles);
    }

    /** GET /api/search?q= — public */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->string('q'));

        if (strlen($q) < 2) {
            return response()->json(['data' => [], 'query' => $q]);
        }

        $results = Article::with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->where(fn ($query) =>
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%")
            )
            ->orderByDesc('published_at')
            ->limit(20)
            ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'category_id', 'author_id']);

        return response()->json(['data' => $results, 'query' => $q]);
    }

    /** GET /api/articles/latest — public, paginated, optional ?category=IPL */
    public function latest(Request $request): JsonResponse
    {
        $default = (int) \App\Models\Setting::where('key', 'latest_per_page')->value('value') ?: 6;
        $perPage = max(1, min((int) ($request->per_page ?? $default), 48));

        $articles = Article::with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->when($request->category, fn ($q, $cat) =>
                $q->whereHas('category', fn ($q2) => $q2->where('name', $cat))
            )
            ->orderByDesc('published_at')
            ->paginate($perPage, ['id', 'title', 'slug', 'excerpt', 'featured_image', 'views', 'published_at', 'category_id', 'author_id']);

        return response()->json($articles);
    }

    /** GET /api/articles/{slug} — public, single article by slug */
    public function showBySlug(string $slug): JsonResponse
    {
        $article = Article::with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views
        $article->increment('views');

        return response()->json($article);
    }

    /** GET /api/articles/featured — public, no auth */
    public function featured(Request $request): JsonResponse
    {
        $limit = (int) \App\Models\Setting::where('key', 'featured_articles_count')->value('value') ?: 5;
        $limit = max(1, min($limit, 20));

        $articles = Article::with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'is_featured', 'views', 'published_at', 'category_id', 'author_id']);

        return response()->json($articles);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'slug'           => ['required', 'string', 'max:255', 'unique:articles,slug'],
            'excerpt'        => ['nullable', 'string'],
            'content'        => ['required', 'string'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'category_id'    => ['required', 'integer', 'exists:categories,id'],
            'status'         => ['required', Rule::in(['draft', 'published'])],
            'is_featured'    => ['boolean'],
            'published_at'   => ['nullable', 'date'],
        ]);

        $data['author_id']    = $request->user()->id;
        $data['published_at'] = $data['status'] === 'published'
            ? ($data['published_at'] ?? now())
            : null;

        return response()->json(
            Article::create($data)->load(['category:id,name', 'author:id,name']),
            201
        );
    }

    public function update(Request $request, Article $article): JsonResponse
    {
        $data = $request->validate([
            'title'          => ['sometimes', 'string', 'max:255'],
            'slug'           => ['sometimes', 'string', 'max:255', Rule::unique('articles')->ignore($article->id)],
            'excerpt'        => ['nullable', 'string'],
            'content'        => ['sometimes', 'string'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'category_id'    => ['sometimes', 'integer', 'exists:categories,id'],
            'status'         => ['sometimes', Rule::in(['draft', 'published'])],
            'is_featured'    => ['boolean'],
            'published_at'   => ['nullable', 'date'],
        ]);

        if (isset($data['status']) && $data['status'] === 'published' && !$article->published_at) {
            $data['published_at'] = now();
        }

        $article->update($data);

        return response()->json($article->load(['category:id,name', 'author:id,name']));
    }

    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json(['message' => 'Article deleted.']);
    }

    /** GET /api/articles/{slug}/related — public */
    public function related(string $slug): JsonResponse
    {
        $article = Article::where('slug', $slug)->where('status', 'published')->first();
        if (! $article) return response()->json([]);

        $related = Article::with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'views', 'category_id', 'author_id']);

        return response()->json($related);
    }

    /** GET /api/articles/most-read-week — public */
    public function mostReadWeek(Request $request): JsonResponse
    {
        $default = (int) \App\Models\Setting::where('key', 'most_read_count')->value('value') ?: 5;
        $limit   = max(1, min($request->integer('limit', $default), 20));

        $articles = Article::with(['category:id,name,color'])
            ->where('status', 'published')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('views')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'views', 'category_id', 'published_at']);

        // Fallback: if fewer than 3 results in last 7 days, expand to 30 days
        if ($articles->count() < 3) {
            $articles = Article::with(['category:id,name,color'])
                ->where('status', 'published')
                ->where('created_at', '>=', now()->subDays(30))
                ->orderByDesc('views')
                ->limit($limit)
                ->get(['id', 'title', 'slug', 'views', 'category_id', 'published_at']);
        }

        return response()->json($articles);
    }
}
