<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'video_url',
        'category_id',
        'author_id',
        'status',
        'is_featured',
        'published_at',
        'views',
    ];

    protected $appends = ['video_embed_url'];

    protected $casts = [
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
        'views'        => 'integer',
    ];

    /** Convert any supported video page URL → embeddable iframe src */
    public function getVideoEmbedUrlAttribute(): ?string
    {
        return static::resolveEmbedUrl($this->video_url);
    }

    public static function resolveEmbedUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        // Already an embed URL — return as-is
        if (str_contains($url, 'youtube.com/embed/')
            || str_contains($url, 'player.vimeo.com')
            || str_contains($url, 'dailymotion.com/embed/')
        ) {
            return $url;
        }

        // YouTube watch  /  youtu.be short link  /  YouTube Shorts
        if (preg_match(
            '/(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            $url, $m
        )) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        // Dailymotion
        if (preg_match('/dailymotion\.com\/video\/([a-zA-Z0-9]+)/', $url, $m)) {
            return 'https://www.dailymotion.com/embed/video/' . $m[1];
        }

        return null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
