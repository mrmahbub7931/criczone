<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveScore extends Model
{
    protected $fillable = [
        'team_a', 'team_b', 'score_a', 'score_b',
        'overs', 'status', 'match_type', 'venue',
        'match_time', 'is_live', 'sort_order',
    ];

    protected $casts = [
        'is_live'    => 'boolean',
        'match_time' => 'datetime',
    ];
}
