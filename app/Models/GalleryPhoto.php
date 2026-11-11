<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryPhoto extends Model
{
    protected $fillable = ['gallery_id', 'image_path', 'caption', 'caption_en', 'photographer', 'sort_order'];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(PhotoGallery::class, 'gallery_id');
    }
}
