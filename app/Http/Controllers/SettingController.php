<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // Map of group → allowed keys (whitelist to avoid arbitrary DB writes)
    private const GROUPS = [
        'general'    => ['site_name', 'site_tagline', 'site_description', 'site_logo', 'site_favicon',
                          'articles_per_page', 'timezone', 'show_ticker', 'maintenance_mode'],
        'contact'    => ['contact_email', 'contact_phone', 'contact_address', 'contact_city', 'contact_country'],
        'social'     => ['social_facebook', 'social_twitter', 'social_instagram', 'social_youtube',
                          'social_linkedin', 'social_whatsapp', 'social_telegram', 'social_tiktok'],
        'seo'        => ['meta_title', 'meta_description', 'meta_keywords',
                          'google_analytics_id', 'google_tag_manager_id', 'robots_txt'],
        'homepage'   => ['homepage_hero_title', 'homepage_hero_subtitle', 'featured_articles_count',
                          'breaking_news_enabled', 'breaking_news_text', 'articles_layout',
                          'latest_per_page', 'category_per_page', 'trending_count', 'most_read_count'],
        'footer'     => ['footer_description', 'footer_copyright', 'footer_email', 'footer_phone'],
        'smtp'       => ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
                          'smtp_encryption', 'smtp_from_email', 'smtp_from_name'],
        'newsletter' => ['newsletter_enabled', 'newsletter_subject_prefix',
                          'newsletter_welcome_subject', 'newsletter_welcome_body'],
        'poll'       => ['poll_enabled'],
    ];

    /** GET /api/settings/public — no auth, safe groups only (no smtp passwords) */
    public function publicIndex(): JsonResponse
    {
        $excluded = ['smtp'];
        $all      = Setting::getAllGrouped();

        foreach ($excluded as $group) {
            unset($all[$group]);
        }

        return response()->json($all);
    }

    /** GET /api/settings — admin auth required, all groups */
    public function index(): JsonResponse
    {
        return response()->json(Setting::getAllGrouped());
    }

    /** PUT /api/settings */
    public function update(Request $request): JsonResponse
    {
        foreach (self::GROUPS as $group => $keys) {
            $payload = $request->input($group);
            if (! is_array($payload)) {
                continue;
            }

            foreach ($keys as $key) {
                if (array_key_exists($key, $payload)) {
                    // Cast booleans/toggles to '1'/'0' for consistent storage
                    $value = is_bool($payload[$key]) ? ($payload[$key] ? '1' : '0') : $payload[$key];
                    Setting::set($key, $value, $group);
                }
            }
        }

        return response()->json(['message' => 'Settings saved successfully.']);
    }

    /** POST /api/settings/upload-logo  (multipart — logo or favicon) */
    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'max:2048'],
            'type' => ['required', 'in:logo,favicon'],
        ]);

        $path = $request->file('file')->store('settings', 'public');
        $url  = Storage::disk('public')->url($path);

        // Persist immediately so it survives even if the full form isn't re-saved
        $key = $request->input('type') === 'favicon' ? 'site_favicon' : 'site_logo';
        Setting::set($key, $url, 'general');

        return response()->json(['url' => $url]);
    }
}
