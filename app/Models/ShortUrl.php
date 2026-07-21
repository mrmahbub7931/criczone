<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    protected $fillable = ['article_id', 'code', 'visits'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public static function generateCode(): string
    {
        do {
            $code = Str::random(6);
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function getFullUrlAttribute(): string
    {
        return url('/s/' . $this->code);
    }
}
