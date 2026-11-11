<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TranslateArticleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(
        public readonly int $articleId,
        public readonly string $targetLanguage  // 'en' or 'bn'
    ) {}

    public function handle(): void
    {
        $article = Article::findOrFail($this->articleId);

        $translation = ArticleTranslation::firstOrCreate(
            ['article_id' => $article->id, 'language' => $this->targetLanguage],
            ['title' => '', 'content' => '', 'status' => 'pending']
        );

        // Skip if already done
        if ($translation->status === 'done') {
            return;
        }

        $translation->update(['status' => 'processing', 'error_message' => null]);

        $provider = Setting::where('key', 'translation_provider')->value('value') ?? 'claude';

        try {
            [$title, $excerpt, $content] = $this->translate($provider, $article, $this->targetLanguage);

            $updates = [
                'title'    => $title,
                'excerpt'  => $excerpt,
                'content'  => $content,
                'status'   => 'done',
                'provider' => $provider,
            ];

            // Generate English slug for translated articles
            if ($this->targetLanguage === 'en' && $title) {
                $baseSlug = Str::slug($title);
                $enSlug   = $baseSlug;
                $i = 1;
                while (ArticleTranslation::where('en_slug', $enSlug)
                    ->where('article_id', '!=', $article->id)
                    ->exists()
                ) {
                    $enSlug = "{$baseSlug}-{$i}";
                    $i++;
                }
                $updates['en_slug'] = $enSlug ?: null;
            }

            $translation->update($updates);
        } catch (\Throwable $e) {
            $translation->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error("TranslateArticleJob failed: {$e->getMessage()}", [
                'article_id' => $this->articleId,
                'provider'   => $provider,
            ]);
            throw $e;
        }
    }

    private function translate(string $provider, Article $article, string $targetLang): array
    {
        $langName = $targetLang === 'en' ? 'English' : 'Bengali';
        $sourceLangName = $article->language === 'en' ? 'English' : 'Bengali';

        $prompt = <<<PROMPT
You are a professional sports news translator specializing in cricket.

Translate the following cricket news article from {$sourceLangName} to {$langName}.

Rules:
- Preserve all cricket terminology accurately
- Keep player names and team names as-is (do not translate proper nouns)
- Keep the same tone (news/journalistic style)
- Translate HTML content — keep all HTML tags intact, only translate the text inside tags
- Return JSON with keys: title, excerpt, content

Article Title: {$article->title}
Article Excerpt: {$article->excerpt}
Article Content (HTML):
{$article->content}

Return only valid JSON, no extra text.
PROMPT;

        return match ($provider) {
            'openai' => $this->translateWithOpenAI($prompt),
            default  => $this->translateWithClaude($prompt),
        };
    }

    private function translateWithClaude(string $prompt): array
    {
        $apiKey = Setting::where('key', 'anthropic_api_key')->value('value')
            ?? config('services.anthropic.key')
            ?? env('ANTHROPIC_API_KEY');

        if (! $apiKey) {
            throw new \RuntimeException('Anthropic API key not configured.');
        }

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
        ])->timeout(90)->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-haiku-4-5',
            'max_tokens' => 4096,
            'messages'   => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Claude API error: ' . $response->body());
        }

        $text = $response->json('content.0.text');
        return $this->parseJson($text);
    }

    private function translateWithOpenAI(string $prompt): array
    {
        $apiKey = Setting::where('key', 'openai_api_key')->value('value')
            ?? config('services.openai.key')
            ?? env('OPENAI_API_KEY');

        if (! $apiKey) {
            throw new \RuntimeException('OpenAI API key not configured.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->timeout(90)->post('https://api.openai.com/v1/chat/completions', [
            'model'    => 'gpt-4.1-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('OpenAI API error: ' . $response->body());
        }

        $text = $response->json('choices.0.message.content');
        return $this->parseJson($text);
    }

    private function parseJson(string $text): array
    {
        // Strip markdown code fences if present
        $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
        $text = preg_replace('/```\s*$/m', '', $text);

        $data = json_decode(trim($text), true);

        if (! is_array($data) || ! isset($data['title'], $data['content'])) {
            throw new \RuntimeException('Invalid JSON response from AI: ' . substr($text, 0, 200));
        }

        return [$data['title'], $data['excerpt'] ?? null, $data['content']];
    }
}
