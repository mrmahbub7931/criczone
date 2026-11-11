<?php

namespace App\Http\Controllers;

use App\Jobs\TranslateArticleJob;
use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    /** GET /api/admin/translations — list all with translation status */
    public function index(Request $request): JsonResponse
    {
        $articles = Article::with(['translations'])
            ->when($request->search, fn ($q, $s) =>
                $q->where('title', 'like', "%{$s}%")
            )
            ->latest()
            ->paginate($request->integer('per_page', 20));

        $translatedCount = \App\Models\ArticleTranslation::where('status', 'done')->count();

        return response()->json(array_merge($articles->toArray(), [
            'translated_count' => $translatedCount,
        ]));
    }

    /** POST /api/admin/translations/{article}/translate — queue translation job */
    public function translate(Request $request, Article $article): JsonResponse
    {
        $targetLang = $article->language === 'bn' ? 'en' : 'bn';

        // Always delete existing translation so the job runs fresh
        // (this regenerates en_slug and picks up any content changes)
        ArticleTranslation::where('article_id', $article->id)
            ->where('language', $targetLang)
            ->delete();

        TranslateArticleJob::dispatch($article->id, $targetLang);

        return response()->json(['message' => 'Translation queued.', 'target_language' => $targetLang]);
    }

    /** POST /api/admin/translations/batch — queue multiple */
    public function batch(Request $request): JsonResponse
    {
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer'])['ids'];

        $articles = Article::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($articles as $article) {
            $targetLang = $article->language === 'bn' ? 'en' : 'bn';
            $exists = ArticleTranslation::where('article_id', $article->id)
                ->where('language', $targetLang)
                ->where('status', 'done')
                ->exists();

            if (! $exists) {
                TranslateArticleJob::dispatch($article->id, $targetLang);
                $count++;
            }
        }

        return response()->json(['message' => "{$count} translations queued."]);
    }

    /** PUT /api/admin/translations/{article}/{lang} — manually edit a translation */
    public function update(Request $request, Article $article, string $lang): JsonResponse
    {
        $data = $request->validate([
            'title'    => 'required|string|max:500',
            'excerpt'  => 'nullable|string|max:1000',
            'content'  => 'required|string',
            'en_slug'  => 'nullable|string|max:255',
        ]);

        $translation = ArticleTranslation::where('article_id', $article->id)
            ->where('language', $lang)
            ->firstOrFail();

        $translation->update([
            'title'   => $data['title'],
            'excerpt' => $data['excerpt'] ?? $translation->excerpt,
            'content' => $data['content'],
            'en_slug' => $data['en_slug'] ?? $translation->en_slug,
            'status'  => 'done',
        ]);

        return response()->json(['message' => 'Translation updated.', 'translation' => $translation]);
    }

    /** DELETE /api/admin/translations/{article}/{lang} — delete a translation */
    public function destroy(Article $article, string $lang): JsonResponse
    {
        ArticleTranslation::where('article_id', $article->id)
            ->where('language', $lang)
            ->delete();

        return response()->json(['message' => 'Translation deleted.']);
    }

    /** GET /api/article/{slug}/translation/{lang} — public: get translated article */
    public function show(string $slug, string $lang): JsonResponse
    {
        // Try original slug first, then en_slug stored in article_translations
        $article = Article::where('slug', $slug)->where('status', 'published')->first();

        if (! $article) {
            $trans = ArticleTranslation::where('en_slug', $slug)->where('status', 'done')->first();
            if ($trans) {
                $article = Article::where('id', $trans->article_id)->where('status', 'published')->first();
            }
        }

        abort_if(! $article, 404);

        // If requested language matches article's own language, return article itself
        if ($article->language === $lang) {
            return response()->json([
                'title'    => $article->title,
                'excerpt'  => $article->excerpt,
                'content'  => $article->content,
                'language' => $lang,
                'is_original' => true,
            ]);
        }

        $translation = ArticleTranslation::where('article_id', $article->id)
            ->where('language', $lang)
            ->where('status', 'done')
            ->first();

        if (! $translation) {
            return response()->json(['error' => 'Translation not available.'], 404);
        }

        return response()->json([
            'title'       => $translation->title,
            'excerpt'     => $translation->excerpt,
            'content'     => $translation->content,
            'language'    => $lang,
            'is_original' => false,
        ]);
    }
}
