<?php

namespace App\Traits;

use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Database\Eloquent\Builder;

trait HandlesArticleTranslation
{
    /**
     * Extend the query to include originally-English articles
     * OR Bangla articles that have a completed English translation.
     */
    protected function withTranslatedEn(Builder $q): Builder
    {
        return $q->where(function ($sub) {
            $sub->where('language', 'en')
                ->orWhereHas('translations', fn ($t) =>
                    $t->where('language', 'en')->where('status', 'done')
                );
        });
    }

    /**
     * Eager-load only the English translation fields needed for listing.
     */
    protected function withEnTranslationEager($query): void
    {
        $query->with(['translations' => fn ($t) =>
            $t->where('language', 'en')->where('status', 'done')
              ->select('article_id', 'title', 'excerpt', 'en_slug')
        ]);
    }

    /**
     * If a Bangla article has an English translation, overwrite
     * title / excerpt / slug with translated values.
     */
    protected function applyTranslation(Article $article): Article
    {
        if ($article->language === 'en' || ! isset($article->translations)) {
            return $article;
        }

        $trans = $article->translations->first();
        if (! $trans) {
            return $article;
        }

        $article->title   = $trans->title;
        $article->excerpt = $trans->excerpt ?? $article->excerpt;

        if ($trans->en_slug) {
            $article->slug = $trans->en_slug;
        }

        return $article;
    }
}
