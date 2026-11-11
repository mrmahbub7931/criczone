<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Traits\HandlesArticleTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    use HandlesArticleTranslation;
    /** GET /api/tags?search=  — public, used for autocomplete + admin list */
    public function index(Request $request): JsonResponse
    {
        $tags = Tag::withCount('articles')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name', 'slug']);

        return response()->json($tags);
    }

    /** POST /api/tags — admin */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:tags,name'],
        ]);

        $tag = Tag::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return response()->json($tag->loadCount('articles'), 201);
    }

    /** PUT /api/tags/{tag} — admin */
    public function update(Request $request, Tag $tag): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('tags', 'name')->ignore($tag->id)],
        ]);

        $tag->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return response()->json($tag->loadCount('articles'));
    }

    /** GET /api/tags/{slug}/articles — public, tag archive */
    public function articles(Request $request, string $slug): JsonResponse
    {
        $tag     = Tag::where('slug', $slug)->firstOrFail();
        $perPage = max(1, min($request->integer('per_page', 12), 48));
        $langEn  = $request->lang === 'en';

        $query = $tag->articles()
            ->with(['category:id,name,color'])
            ->where('status', 'published')
            ->when($langEn, fn ($q) => $this->withTranslatedEn($q))
            ->when($request->lang === 'bn', fn ($q) => $q->where('language', 'bn'))
            ->orderByDesc('published_at');

        if ($langEn) {
            $query->with(['translations' => fn ($t) =>
                $t->where('language', 'en')->where('status', 'done')
                  ->select('article_id', 'title', 'excerpt', 'en_slug')
            ]);
        }

        $articles = $query->paginate($perPage, ['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'category_id', 'language']);

        if ($langEn) {
            $articles->through(fn ($a) => $this->applyTranslation($a));
        }

        return response()->json(['tag' => $tag, 'articles' => $articles]);
    }

    /** DELETE /api/tags/{tag} — admin */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted.']);
    }
}
