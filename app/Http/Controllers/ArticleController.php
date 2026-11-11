<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\Setting;
use App\Models\ShortUrl;
use App\Models\Tag;
use App\Traits\HandlesArticleTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    use HandlesArticleTranslation;
    public function index(Request $request): JsonResponse
    {
        $articles = Article::with(['category:id,name', 'categories:id,name', 'author:id,name', 'tags'])
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
        $default = (int) Setting::where('key', 'trending_count')->value('value') ?: 7;
        $limit   = max(1, min($request->integer('limit', $default), 20));

        $articles = Article::with(['category:id,name,color'])
            ->where('status', 'published')
            ->when(in_array($request->lang, ['bn', 'en']), fn ($q) => $q->where('language', $request->lang))
            ->orderByDesc('views')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'views', 'category_id', 'language']);

        return response()->json($articles);
    }

    /** GET /api/search?q=&lang= — public */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->string('q'));

        if (strlen($q) < 2) {
            return response()->json(['data' => [], 'query' => $q]);
        }

        $langEn = $request->lang === 'en';

        $query = Article::with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->limit(20);

        if ($langEn) {
            // Match English originals by their own text, OR Bangla articles by translated text
            $query->where(function ($sub) use ($q) {
                $sub->where(function ($a) use ($q) {
                        $a->where('language', 'en')
                          ->where(fn ($t) => $t->where('title', 'like', "%{$q}%")->orWhere('excerpt', 'like', "%{$q}%"));
                    })
                    ->orWhereHas('translations', fn ($t) =>
                        $t->where('language', 'en')->where('status', 'done')
                          ->where(fn ($ts) => $ts->where('title', 'like', "%{$q}%")->orWhere('excerpt', 'like', "%{$q}%"))
                    );
            });
            $query->with(['translations' => fn ($t) =>
                $t->where('language', 'en')->where('status', 'done')
                  ->select('article_id', 'title', 'excerpt', 'en_slug')
            ]);
        } else {
            $query->when($request->lang === 'bn', fn ($q2) => $q2->where('language', 'bn'))
                  ->where(fn ($query) =>
                      $query->where('title', 'like', "%{$q}%")
                            ->orWhere('excerpt', 'like', "%{$q}%")
                  );
        }

        $results = $query->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'category_id', 'author_id', 'language']);

        if ($langEn) {
            $results->transform(fn ($a) => $this->applyTranslation($a));
        }

        return response()->json(['data' => $results, 'query' => $q]);
    }

    /** GET /api/articles/latest — public, paginated, optional ?category=IPL&lang=bn */
    public function latest(Request $request): JsonResponse
    {
        $default = (int) Setting::where('key', 'latest_per_page')->value('value') ?: 6;
        $perPage = max(1, min((int) ($request->per_page ?? $default), 48));
        $langEn  = $request->lang === 'en';

        $query = Article::with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('video_url')->orWhere('video_url', ''))
            ->when($langEn, fn ($q) => $this->withTranslatedEn($q))
            ->when($request->lang === 'bn', fn ($q) => $q->where('language', 'bn'))
            ->when($request->category, fn ($q, $cat) =>
                $q->whereHas('category', fn ($q2) => $q2->where('name', $cat))
            )
            ->orderByDesc('published_at');

        if ($langEn) {
            $query->with(['translations' => fn ($t) =>
                $t->where('language', 'en')->where('status', 'done')
                  ->select('article_id', 'title', 'excerpt', 'en_slug')
            ]);
        }

        $articles = $query->paginate($perPage, ['id', 'title', 'slug', 'excerpt', 'featured_image', 'language', 'views', 'published_at', 'category_id', 'author_id']);

        if ($langEn) {
            $articles->through(fn ($a) => $this->applyTranslation($a));
        }

        return response()->json($articles);
    }

    /** GET /api/articles/{slug} — public, single article by slug or en_slug */
    public function showBySlug(string $slug): JsonResponse
    {
        // Try original slug first, then en_slug from translations
        $article = Article::with(['category:id,name,color', 'categories:id,name,slug,color', 'author:id,name', 'tags', 'shortUrl'])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->first();

        if (! $article) {
            $trans = ArticleTranslation::where('en_slug', $slug)->where('status', 'done')->first();
            if ($trans) {
                $article = Article::with(['category:id,name,color', 'categories:id,name,slug,color', 'author:id,name', 'tags', 'shortUrl'])
                    ->where('status', 'published')
                    ->find($trans->article_id);
            }
        }

        abort_if(! $article, 404);

        $article->increment('views');

        // Ensure a short URL exists for every viewed article
        if (! $article->shortUrl) {
            ShortUrl::create([
                'article_id' => $article->id,
                'code'       => ShortUrl::generateCode(),
            ]);
            $article->load('shortUrl');
        }

        return response()->json($article);
    }

    /** GET /api/articles/featured — public, no auth */
    public function featured(Request $request): JsonResponse
    {
        $limit  = (int) Setting::where('key', 'featured_articles_count')->value('value') ?: 5;
        $limit  = max(1, min($limit, 20));
        $langEn = $request->lang === 'en';

        $query = Article::with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->when($langEn, fn ($q) => $this->withTranslatedEn($q))
            ->when($request->lang === 'bn', fn ($q) => $q->where('language', 'bn'))
            ->orderByDesc('published_at')
            ->limit($limit);

        if ($langEn) {
            $query->with(['translations' => fn ($t) =>
                $t->where('language', 'en')->where('status', 'done')
                  ->select('article_id', 'title', 'excerpt', 'en_slug')
            ]);
        }

        $articles = $query->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'is_featured', 'views', 'published_at', 'category_id', 'author_id', 'language']);

        if ($langEn) {
            $articles->transform(fn ($a) => $this->applyTranslation($a));
        }

        return response()->json($articles);
    }

    /** GET /api/articles/photo-of-day — public, latest photo-of-day articles */
    public function photoOfDay(Request $request): JsonResponse
    {
        $perPage = max(1, min($request->integer('per_page', 12), 48));
        $langEn  = $request->lang === 'en';

        $query = Article::with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->where('is_photo_day', true)
            ->when($langEn, fn ($q) => $this->withTranslatedEn($q))
            ->when($request->lang === 'bn', fn ($q) => $q->where('language', 'bn'))
            ->orderByDesc('published_at');

        if ($langEn) {
            $query->with(['translations' => fn ($t) =>
                $t->where('language', 'en')->where('status', 'done')
                  ->select('article_id', 'title', 'excerpt', 'en_slug')
            ]);
        }

        $articles = $query->paginate($perPage, ['id', 'title', 'slug', 'excerpt', 'featured_image', 'is_photo_day', 'views', 'published_at', 'category_id', 'author_id', 'language']);

        if ($langEn) {
            $articles->through(fn ($a) => $this->applyTranslation($a));
        }

        return response()->json($articles);
    }

    /** GET /api/articles/videos — public, paginated list of video articles */
    public function videos(Request $request): JsonResponse
    {
        $perPage = max(1, min($request->integer('per_page', 12), 48));
        $langEn  = $request->lang === 'en';

        $query = Article::with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->when($langEn, fn ($q) => $this->withTranslatedEn($q))
            ->when($request->lang === 'bn', fn ($q) => $q->where('language', 'bn'))
            ->orderByDesc('published_at');

        if ($langEn) {
            $query->with(['translations' => fn ($t) =>
                $t->where('language', 'en')->where('status', 'done')
                  ->select('article_id', 'title', 'excerpt', 'en_slug')
            ]);
        }

        $articles = $query->paginate($perPage, ['id', 'title', 'slug', 'excerpt', 'featured_image', 'video_url', 'views', 'published_at', 'category_id', 'author_id', 'language']);

        if ($langEn) {
            $articles->through(fn ($a) => $this->applyTranslation($a));
        }

        return response()->json($articles);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'slug'           => ['required', 'string', 'max:255', 'unique:articles,slug'],
            'excerpt'        => ['nullable', 'string'],
            'content'        => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'video_url'      => ['nullable', 'string', 'max:500'],
            'category_id'    => ['required', 'integer', 'exists:categories,id'],
            'category_ids'   => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'status'         => ['required', Rule::in(['draft', 'published'])],
            'language'       => ['sometimes', Rule::in(['bn', 'en'])],
            'is_featured'    => ['boolean'],
            'is_photo_day'   => ['boolean'],
            'published_at'   => ['nullable', 'date'],
            'tags'           => ['nullable', 'array'],
            'tags.*'         => ['string', 'max:50'],
        ]);

        $data['content']      = $data['content'] ?? '';
        $data['author_id']    = $request->user()->id;
        $data['published_at'] = $data['status'] === 'published'
            ? ($data['published_at'] ?? now())
            : null;

        $tagNames    = $data['tags'] ?? [];
        $categoryIds = $data['category_ids'] ?? [$data['category_id']];
        unset($data['tags'], $data['category_ids']);

        $article = Article::create($data);
        $article->tags()->sync($this->resolveTagIds($tagNames));
        $article->categories()->sync($this->resolveCategoryIds($categoryIds, $data['category_id']));

        if ($article->status === 'published') {
            ShortUrl::create(['article_id' => $article->id, 'code' => ShortUrl::generateCode()]);
        }

        return response()->json(
            $article->load(['category:id,name', 'categories:id,name,slug,color', 'author:id,name', 'tags', 'shortUrl']),
            201
        );
    }

    public function update(Request $request, Article $article): JsonResponse
    {
        $data = $request->validate([
            'title'          => ['sometimes', 'string', 'max:255'],
            'slug'           => ['sometimes', 'string', 'max:255', Rule::unique('articles')->ignore($article->id)],
            'excerpt'        => ['nullable', 'string'],
            'content'        => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'video_url'      => ['nullable', 'string', 'max:500'],
            'category_id'    => ['sometimes', 'integer', 'exists:categories,id'],
            'category_ids'   => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'status'         => ['sometimes', Rule::in(['draft', 'published'])],
            'language'       => ['sometimes', Rule::in(['bn', 'en'])],
            'is_featured'    => ['boolean'],
            'is_photo_day'   => ['boolean'],
            'published_at'   => ['nullable', 'date'],
            'tags'           => ['nullable', 'array'],
            'tags.*'         => ['string', 'max:50'],
        ]);

        if (isset($data['status']) && $data['status'] === 'published' && ! $article->published_at) {
            $data['published_at'] = now();
        }

        $tagNames    = $data['tags'] ?? null;
        $categoryIds = $data['category_ids'] ?? null;
        $primaryId   = $data['category_id'] ?? $article->category_id;
        unset($data['tags'], $data['category_ids']);

        $article->update($data);

        if ($tagNames !== null) {
            $article->tags()->sync($this->resolveTagIds($tagNames));
        }

        if ($categoryIds !== null) {
            $article->categories()->sync($this->resolveCategoryIds($categoryIds, $primaryId));
        } elseif (isset($data['category_id'])) {
            // primary changed but no explicit multi-select sent — keep existing extra cats + update primary
            $existing = $article->categories()->pluck('categories.id')->toArray();
            $article->categories()->sync($this->resolveCategoryIds(
                array_unique(array_merge([$primaryId], $existing)),
                $primaryId
            ));
        }

        return response()->json($article->load(['category:id,name', 'categories:id,name,slug,color', 'author:id,name', 'tags']));
    }

    /** Find-or-create tags by name and return their IDs. */
    private function resolveTagIds(array $names): array
    {
        return collect($names)
            ->filter()
            ->map(fn ($name) => trim($name))
            ->unique()
            ->map(fn ($name) => Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            )->id)
            ->values()
            ->all();
    }

    /**
     * Merge submitted category IDs with the auto-add default category from settings.
     * Always includes the primary category_id.
     */
    private function resolveCategoryIds(array $ids, int $primaryId): array
    {
        $defaultId = (int) Setting::where('key', 'default_category_id')->value('value');

        return collect($ids)
            ->push($primaryId)
            ->when($defaultId > 0, fn ($c) => $c->push($defaultId))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json(['message' => 'Article deleted.']);
    }

    /** GET /api/articles/{slug}/related — public */
    public function related(Request $request, string $slug): JsonResponse
    {
        // Resolve article by original slug or en_slug
        $article = Article::where('slug', $slug)->where('status', 'published')->first();
        if (! $article) {
            $trans = ArticleTranslation::where('en_slug', $slug)->where('status', 'done')->first();
            if ($trans) {
                $article = Article::where('id', $trans->article_id)->where('status', 'published')->first();
            }
        }
        if (! $article) return response()->json([]);

        $langEn = $request->lang === 'en';

        $query = Article::with(['category:id,name,color'])
            ->where('status', 'published')
            ->where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->when($langEn, fn ($q) => $this->withTranslatedEn($q))
            ->when(! $langEn, fn ($q) => $q->where('language', $article->language ?? 'bn'))
            ->orderByDesc('published_at')
            ->limit(3);

        if ($langEn) {
            $query->with(['translations' => fn ($t) =>
                $t->where('language', 'en')->where('status', 'done')
                  ->select('article_id', 'title', 'excerpt', 'en_slug')
            ]);
        }

        $related = $query->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'category_id', 'language']);

        if ($langEn) {
            $related->transform(fn ($a) => $this->applyTranslation($a));
        }

        return response()->json($related);
    }

    /** GET /api/articles/most-read-week — public */
    public function mostReadWeek(Request $request): JsonResponse
    {
        $default = (int) Setting::where('key', 'most_read_count')->value('value') ?: 5;
        $limit   = max(1, min($request->integer('limit', $default), 20));

        $articles = Article::with(['category:id,name,color'])
            ->where('status', 'published')
            ->when(in_array($request->lang, ['bn', 'en']), fn ($q) => $q->where('language', $request->lang))
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('views')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'views', 'category_id', 'published_at', 'language']);

        if ($articles->count() < 3) {
            $articles = Article::with(['category:id,name,color'])
                ->where('status', 'published')
                ->when(in_array($request->lang, ['bn', 'en']), fn ($q) => $q->where('language', $request->lang))
                ->where('created_at', '>=', now()->subDays(30))
                ->orderByDesc('views')
                ->limit($limit)
                ->get(['id', 'title', 'slug', 'views', 'category_id', 'published_at', 'language']);
        }

        return response()->json($articles);
    }

}
