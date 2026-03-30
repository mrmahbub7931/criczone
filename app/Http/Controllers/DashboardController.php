<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user    = $request->user();
        $isAdmin = $user->hasRole('admin');

        // ── Article aggregates ────────────────────────────────────────────────
        $totalArticles  = Article::count();
        $publishedCount = Article::where('status', 'published')->count();
        $draftCount     = Article::where('status', 'draft')->count();
        $featuredCount  = Article::where('is_featured', true)->count();
        $totalViews     = (int) Article::sum('views');

        // ── Category + user counts ────────────────────────────────────────────
        $categoriesCount = Category::count();
        $usersCount      = $isAdmin ? User::count() : null;

        // ── Recent articles (last 6 published) ────────────────────────────────
        $recentArticles = Article::with([
                'category:id,name,color',
                'author:id,name',
            ])
            ->latest('published_at')
            ->limit(6)
            ->get(['id', 'title', 'slug', 'status', 'views',
                   'published_at', 'category_id', 'author_id']);

        // ── Chart: articles published + views per day, last 7 days ────────────
        $chartData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date  = Carbon::today()->subDays($i);
            $dayRows = Article::whereDate('published_at', $date)->get(['views']);
            $chartData->push([
                'day'      => $date->format('D'),   // Mon, Tue …
                'date'     => $date->toDateString(),
                'articles' => $dayRows->count(),
                'views'    => $dayRows->sum('views'),
            ]);
        }

        // ── Top categories by article count ───────────────────────────────────
        $categories = Category::withCount('articles')
            ->orderByDesc('articles_count')
            ->get(['id', 'name', 'slug', 'color']);

        return response()->json([
            'stats' => [
                'totalArticles'  => $totalArticles,
                'publishedCount' => $publishedCount,
                'draftCount'     => $draftCount,
                'featuredCount'  => $featuredCount,
                'totalViews'     => $totalViews,
                'categories'     => $categoriesCount,
                'users'          => $usersCount,
            ],
            'recentArticles' => $recentArticles,
            'chartData'      => $chartData,
            'categories'     => $categories,
        ]);
    }
}
