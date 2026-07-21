<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ShortUrl;
use Illuminate\Http\Request;

class ShortUrlController extends Controller
{
    // Known social/bot crawlers that scrape OG tags but don't execute JS
    private const BOT_AGENTS = [
        'facebookexternalhit', 'facebot', 'twitterbot', 'whatsapp',
        'telegrambot', 'linkedinbot', 'slackbot', 'discordbot',
        'applebot', 'googlebot', 'bingbot', 'ia_archiver',
    ];

    /** GET /s/{code} — public redirect with OG meta page */
    public function redirect(Request $request, string $code)
    {
        $short = ShortUrl::where('code', $code)->firstOrFail();

        $article = $short->article()->with(['category:id,name,color'])->firstOrFail();

        $title   = e($article->title);
        $excerpt = e($article->excerpt ?? $article->title);
        $image   = $article->featured_image
            ? (str_starts_with($article->featured_image, 'http')
                ? $article->featured_image
                : url($article->featured_image))
            : '';
        $dest    = url('/article/' . rawurlencode($article->slug));
        $siteUrl = url('/s/' . $code);

        $isBot = $this->isBot($request->userAgent() ?? '');

        if (! $isBot) {
            $short->increment('visits');
            // Real browser — instant JS redirect (bots don't execute JS)
            return response(
                "<!DOCTYPE html><html><head><meta charset=\"UTF-8\">
<title>{$title}</title>
<script>window.location.replace(" . json_encode($dest) . ");</script>
</head><body><a href=\"{$dest}\">Click here if not redirected</a></body></html>",
                200,
                ['Content-Type' => 'text/html']
            );
        }

        // Bot/crawler — serve full OG tags, NO redirect so bot reads this page
        $siteName = e(config('app.name', 'CricZone'));

        $html = "<!DOCTYPE html>
<html lang=\"bn\">
<head>
<meta charset=\"UTF-8\">
<title>{$title}</title>

<!-- Open Graph -->
<meta property=\"og:type\"        content=\"article\">
<meta property=\"og:site_name\"   content=\"{$siteName}\">
<meta property=\"og:url\"         content=\"{$siteUrl}\">
<meta property=\"og:title\"       content=\"{$title}\">
<meta property=\"og:description\" content=\"{$excerpt}\">
";

        if ($image) {
            $html .= "<meta property=\"og:image\"        content=\"{$image}\">
<meta property=\"og:image:width\"  content=\"1200\">
<meta property=\"og:image:height\" content=\"630\">
<meta property=\"og:image:alt\"    content=\"{$title}\">
";
        }

        $html .= "
<!-- Twitter Card -->
<meta name=\"twitter:card\"        content=\"summary_large_image\">
<meta name=\"twitter:title\"       content=\"{$title}\">
<meta name=\"twitter:description\" content=\"{$excerpt}\">
";

        if ($image) {
            $html .= "<meta name=\"twitter:image\" content=\"{$image}\">
";
        }

        $html .= "
<!-- Canonical -->
<link rel=\"canonical\" href=\"{$siteUrl}\">
</head>
<body>
<h1>{$title}</h1>
<p>{$excerpt}</p>
<a href=\"{$dest}\">Read full article</a>
</body>
</html>";

        return response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    private function isBot(string $userAgent): bool
    {
        $ua = strtolower($userAgent);
        foreach (self::BOT_AGENTS as $bot) {
            if (str_contains($ua, $bot)) {
                return true;
            }
        }
        return false;
    }

    /** POST /api/articles/{article}/short-url — generate or return existing */
    public function generate(Article $article)
    {
        $short = $article->shortUrl ?? ShortUrl::create([
            'article_id' => $article->id,
            'code'       => ShortUrl::generateCode(),
        ]);

        return response()->json([
            'short_url' => $short->full_url,
            'code'      => $short->code,
            'visits'    => $short->visits,
        ]);
    }
}
