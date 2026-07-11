<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = Category::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($categories);
    }

    /** GET /api/categories/{slug}/articles — public */
    public function categoryArticles(Request $request, string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $default = (int) \App\Models\Setting::where('key', 'category_per_page')->value('value') ?: 9;
        $perPage = max(1, min((int) ($request->per_page ?? $default), 48));

        $articles = $category->articles()
            ->with(['author:id,name'])
            ->where('status', 'published')
            ->when(in_array($request->lang, ['bn', 'en']), fn ($q) => $q->where('language', $request->lang))
            ->orderByDesc('published_at')
            ->paginate($perPage, ['id', 'title', 'slug', 'excerpt', 'featured_image', 'views', 'published_at', 'category_id', 'author_id', 'language']);

        return response()->json([
            'category' => $category,
            'articles' => $articles,
        ]);
    }

    /** GET /api/categories/public — public, no auth */
    public function publicList(): JsonResponse
    {
        $categories = Category::withCount(['articles' => fn ($q) => $q->where('status', 'published')])
            ->having('articles_count', '>', 0)
            ->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->orderByDesc('articles_count')
            ->get(['id', 'name', 'slug', 'color', 'is_pinned', 'sort_order', 'show_in_filter']);

        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['required', 'string', 'max:100', 'unique:categories,slug', 'regex:/^[a-z0-9\-]+$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'color'       => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_pinned'      => ['sometimes', 'boolean'],
            'sort_order'     => ['sometimes', 'integer', 'min:0', 'max:999'],
            'show_in_filter' => ['sometimes', 'boolean'],
        ]);

        return response()->json(Category::create($data), 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['sometimes', 'string', 'max:100'],
            'slug'        => ['sometimes', 'string', 'max:100', Rule::unique('categories')->ignore($category->id), 'regex:/^[a-z0-9\-]+$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'color'       => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_pinned'      => ['sometimes', 'boolean'],
            'sort_order'     => ['sometimes', 'integer', 'min:0', 'max:999'],
            'show_in_filter' => ['sometimes', 'boolean'],
        ]);

        $category->update($data);

        return response()->json($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted.']);
    }
}
