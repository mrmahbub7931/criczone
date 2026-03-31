<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // ── Header menu ───────────────────────────────────────────────────────
        $header = Menu::firstOrCreate(
            ['location' => 'header'],
            ['name' => 'Header Navigation']
        );
        $header->allItems()->delete();

        $headerLinks = [
            ['label' => 'Home',      'type' => 'custom', 'url' => '/',                  'order' => 0],
            ['label' => 'News',      'type' => 'custom', 'url' => '/category/news',      'order' => 1],
            ['label' => 'Matches',   'type' => 'custom', 'url' => '/category/matches',   'order' => 2],
            ['label' => 'Players',   'type' => 'custom', 'url' => '/category/players',   'order' => 3],
            ['label' => 'Series',    'type' => 'custom', 'url' => '/category/series',    'order' => 4],
            ['label' => 'Rankings',  'type' => 'custom', 'url' => '/category/rankings',  'order' => 5],
        ];
        foreach ($headerLinks as $link) {
            MenuItem::create(array_merge($link, ['menu_id' => $header->id]));
        }

        // ── Footer — Quick Links ──────────────────────────────────────────────
        $footerQuick = Menu::firstOrCreate(
            ['location' => 'footer_quick'],
            ['name' => 'Footer Quick Links']
        );
        $footerQuick->allItems()->delete();

        $quickLinks = [
            ['label' => 'Home',            'type' => 'custom', 'url' => '/',                  'order' => 0],
            ['label' => 'Latest News',     'type' => 'custom', 'url' => '/category/news',      'order' => 1],
            ['label' => 'Live Scores',     'type' => 'custom', 'url' => '#',                   'order' => 2],
            ['label' => 'Match Schedule',  'type' => 'custom', 'url' => '/category/matches',   'order' => 3],
            ['label' => 'Player Rankings', 'type' => 'custom', 'url' => '/category/players',   'order' => 4],
        ];
        foreach ($quickLinks as $link) {
            MenuItem::create(array_merge($link, ['menu_id' => $footerQuick->id]));
        }

        // ── Footer — Categories ───────────────────────────────────────────────
        $footerCats = Menu::firstOrCreate(
            ['location' => 'footer_categories'],
            ['name' => 'Footer Categories']
        );
        $footerCats->allItems()->delete();

        $catLinks = [
            ['label' => 'International',  'type' => 'custom', 'url' => '/category/international', 'order' => 0],
            ['label' => 'IPL',            'type' => 'custom', 'url' => '/category/ipl',            'order' => 1],
            ['label' => 'T20 World Cup',  'type' => 'custom', 'url' => '/category/t20-world-cup',  'order' => 2],
            ['label' => 'Test Cricket',   'type' => 'custom', 'url' => '/category/test-cricket',   'order' => 3],
            ['label' => 'Domestic',       'type' => 'custom', 'url' => '/category/domestic',       'order' => 4],
        ];
        foreach ($catLinks as $link) {
            MenuItem::create(array_merge($link, ['menu_id' => $footerCats->id]));
        }

        // ── Footer — Legal ────────────────────────────────────────────────────
        $footerLegal = Menu::firstOrCreate(
            ['location' => 'footer_legal'],
            ['name' => 'Footer Legal Links']
        );
        $footerLegal->allItems()->delete();

        // Try to link to pages if they exist
        $privacyPage  = Page::where('slug', 'privacy-policy')->first();
        $termsPage    = Page::where('slug', 'terms-of-service')->first();
        $cookiePage   = Page::where('slug', 'cookie-policy')->first();

        $legalLinks = [
            [
                'label'   => 'Privacy Policy',
                'type'    => $privacyPage ? 'page' : 'custom',
                'url'     => $privacyPage ? null : '/pages/privacy-policy',
                'page_id' => $privacyPage?->id,
                'order'   => 0,
            ],
            [
                'label'   => 'Terms of Service',
                'type'    => $termsPage ? 'page' : 'custom',
                'url'     => $termsPage ? null : '/pages/terms-of-service',
                'page_id' => $termsPage?->id,
                'order'   => 1,
            ],
            [
                'label'   => 'Cookie Policy',
                'type'    => $cookiePage ? 'page' : 'custom',
                'url'     => $cookiePage ? null : '/pages/cookie-policy',
                'page_id' => $cookiePage?->id,
                'order'   => 2,
            ],
        ];
        foreach ($legalLinks as $link) {
            MenuItem::create(array_merge($link, ['menu_id' => $footerLegal->id]));
        }
    }
}
