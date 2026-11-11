<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Promotion extends Model
{
    protected $fillable = [
        'title', 'image_url', 'link_url', 'position',
        'new_tab', 'is_active', 'priority', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'new_tab'    => 'boolean',
        'is_active'  => 'boolean',
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
    ];

    public const POSITIONS = [
        'topbar'              => 'Top Bar (above navbar)',
        'after_ticker'        => 'After Breaking News Ticker',
        'home_after_featured' => 'Home — After Featured News',
        'home_sidebar_top'    => 'Home Sidebar — Top',
        'home_sidebar_mid'    => 'Home Sidebar — Middle',
        'home_sidebar_bottom' => 'Home Sidebar — Bottom (below newsletter)',
        'article_below_title' => 'Article — Below Title',
        'article_middle'      => 'Article — Middle (after 2nd paragraph)',
        'article_bottom'      => 'Article — Bottom',
        'footer_top'          => 'Above Footer',
        'popup'               => 'Popup (center screen)',
        'floating'            => 'Floating (bottom-right corner)',
    ];

    /** Active promotions for a given position right now */
    public static function activeFor(string $position): \Illuminate\Database\Eloquent\Collection
    {
        $now = Carbon::now();
        return static::where('position', $position)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderByDesc('priority')
            ->get();
    }
}
