<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // ── General ────────────────────────────────────────────────────
            ['key' => 'site_name',           'value' => 'CricZone',                            'group' => 'general'],
            ['key' => 'site_tagline',         'value' => 'Your #1 Cricket News Portal',          'group' => 'general'],
            ['key' => 'site_description',     'value' => 'Latest cricket news, live scores and match updates from around the world.', 'group' => 'general'],
            ['key' => 'site_logo',            'value' => '',                                    'group' => 'general'],
            ['key' => 'site_favicon',         'value' => '',                                    'group' => 'general'],
            ['key' => 'articles_per_page',    'value' => '10',                                  'group' => 'general'],
            ['key' => 'timezone',             'value' => 'UTC',                                 'group' => 'general'],
            ['key' => 'show_ticker',          'value' => '1',                                   'group' => 'general'],
            ['key' => 'maintenance_mode',     'value' => '0',                                   'group' => 'general'],

            // ── Contact ─────────────────────────────────────────────────────
            ['key' => 'contact_email',        'value' => 'info@criczone.com',                   'group' => 'contact'],
            ['key' => 'contact_phone',        'value' => '',                                    'group' => 'contact'],
            ['key' => 'contact_address',      'value' => '',                                    'group' => 'contact'],
            ['key' => 'contact_city',         'value' => '',                                    'group' => 'contact'],
            ['key' => 'contact_country',      'value' => '',                                    'group' => 'contact'],

            // ── Social Links ─────────────────────────────────────────────────
            ['key' => 'social_facebook',      'value' => '',                                    'group' => 'social'],
            ['key' => 'social_twitter',       'value' => '',                                    'group' => 'social'],
            ['key' => 'social_instagram',     'value' => '',                                    'group' => 'social'],
            ['key' => 'social_youtube',       'value' => '',                                    'group' => 'social'],
            ['key' => 'social_linkedin',      'value' => '',                                    'group' => 'social'],
            ['key' => 'social_whatsapp',      'value' => '',                                    'group' => 'social'],
            ['key' => 'social_telegram',      'value' => '',                                    'group' => 'social'],
            ['key' => 'social_tiktok',        'value' => '',                                    'group' => 'social'],

            // ── SEO ──────────────────────────────────────────────────────────
            ['key' => 'meta_title',           'value' => 'CricZone – Cricket News & Live Scores', 'group' => 'seo'],
            ['key' => 'meta_description',     'value' => 'Stay up to date with the latest cricket news, live scores and match analysis on CricZone.', 'group' => 'seo'],
            ['key' => 'meta_keywords',        'value' => 'cricket, news, live scores, IPL, T20, ODI, Test match',  'group' => 'seo'],
            ['key' => 'google_analytics_id',  'value' => '',                                    'group' => 'seo'],
            ['key' => 'google_tag_manager_id','value' => '',                                    'group' => 'seo'],
            ['key' => 'robots_txt',           'value' => "User-agent: *\nAllow: /",             'group' => 'seo'],

            // ── Homepage ─────────────────────────────────────────────────────
            ['key' => 'homepage_hero_title',      'value' => 'Welcome to CricZone',             'group' => 'homepage'],
            ['key' => 'homepage_hero_subtitle',   'value' => 'Your #1 Source for Cricket News & Live Scores', 'group' => 'homepage'],
            ['key' => 'featured_articles_count',  'value' => '5',                               'group' => 'homepage'],
            ['key' => 'breaking_news_enabled',    'value' => '1',                               'group' => 'homepage'],
            ['key' => 'breaking_news_text',       'value' => '',                                'group' => 'homepage'],
            ['key' => 'articles_layout',          'value' => 'grid',                            'group' => 'homepage'],

            // ── Footer ───────────────────────────────────────────────────────
            ['key' => 'footer_description',   'value' => 'CricZone is your go-to source for cricket news, live scores, and in-depth match analysis.', 'group' => 'footer'],
            ['key' => 'footer_copyright',     'value' => '© 2025 CricZone. All rights reserved.', 'group' => 'footer'],
            ['key' => 'footer_email',         'value' => 'info@criczone.com',                   'group' => 'footer'],
            ['key' => 'footer_phone',         'value' => '',                                    'group' => 'footer'],
        ];

        foreach ($defaults as $row) {
            Setting::updateOrCreate(['key' => $row['key']], $row);
        }
    }
}
