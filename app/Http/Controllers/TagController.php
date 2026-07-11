<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
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

        $articles = $tag->articles()
            ->with(['category:id,name,color', 'author:id,name'])
            ->where('status', 'published')
            ->when(in_array($request->lang, ['bn', 'en']), fn ($q) => $q->where('language', $request->lang))
            ->orderByDesc('published_at')
            ->paginate($perPage, ['id', 'title', 'slug', 'excerpt', 'featured_image', 'views', 'published_at', 'category_id', 'author_id', 'language']);

        return response()->json(['tag' => $tag, 'articles' => $articles]);
    }

    /** DELETE /api/tags/{tag} — admin */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted.']);
    }
}
