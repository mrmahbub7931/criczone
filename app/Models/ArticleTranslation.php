<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleTranslation extends Model
{
    protected $fillable = [
        'article_id',
        'language',
        'title',
        'en_slug',
        'excerpt',
        'content',
        'status',
        'provider',
        'error_message',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
