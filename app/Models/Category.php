<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_pinned',
        'sort_order',
        'show_in_filter',
    ];

    protected $casts = [
        'is_pinned'      => 'boolean',
        'show_in_filter' => 'boolean',
        'sort_order'     => 'integer',
    ];

    public function articles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function articlesPivot(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }
}
