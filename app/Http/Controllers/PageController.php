<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleTranslation;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    /**
     * Render the article page with server-side OG meta tags.
     * Crawlers (Facebook, WhatsApp, etc.) receive correct title/description
     * regardless of whether the slug is an original or an English en_slug.
     */
    public function article(string $slug): Response
    {
        // Resolve the article: try original slug first, then en_slug
        $article     = Article::where('slug', $slug)->where('status', 'published')->first();
        $translation = null;

        if (! $article) {
            $translation = ArticleTranslation::where('en_slug', $slug)->where('status', 'done')->first();
            if ($translation) {
                $article = Article::where('id', $translation->article_id)
                    ->where('status', 'published')
                    ->first();
            }
        }

        if ($article) {
            $ogTitle       = $translation ? $translation->title       : $article->title;
            $ogDescription = $translation ? ($translation->excerpt ?? $article->excerpt) : $article->excerpt;

            view()->share('og', [
                'title'       => $ogTitle . ' – CricZone',
                'description' => $ogDescription ?: $ogTitle,
                'image'       => $article->featured_image,
                'url'         => url("/article/{$slug}"),
            ]);
        }

        return Inertia::render('Article/Show', ['slug' => $slug]);
    }
}
