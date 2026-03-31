<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'name', 'status', 'token', 'ip', 'confirmed_at'];

    protected $casts = ['confirmed_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->token)) {
                $model->token = Str::random(64);
            }
        });
    }
}
