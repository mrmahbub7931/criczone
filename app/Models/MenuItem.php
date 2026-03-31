<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'parent_id', 'page_id',
        'label', 'type', 'url', 'target', 'order',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /** Resolve the URL: page type uses /pages/{slug}, custom uses raw url */
    public function getResolvedUrlAttribute(): string
    {
        if ($this->type === 'page' && $this->page) {
            return '/pages/' . $this->page->slug;
        }
        return $this->url ?? '#';
    }
}
