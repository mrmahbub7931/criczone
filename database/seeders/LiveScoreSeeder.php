<?php

namespace Database\Seeders;

use App\Models\LiveScore;
use Illuminate\Database\Seeder;

class LiveScoreSeeder extends Seeder
{
    public function run(): void
    {
        $matches = [
            [
                'team_a' => 'IND', 'team_b' => 'AUS',
                'score_a' => '287/4', 'score_b' => '—',
                'overs' => '78.3', 'status' => 'Day 2 – Session 2',
                'match_type' => 'Test', 'venue' => 'MCG, Melbourne',
                'is_live' => true, 'sort_order' => 0,
            ],
            [
                'team_a' => 'ENG', 'team_b' => 'NZ',
                'score_a' => '195/10', 'score_b' => '142/6',
                'overs' => '52.1', 'status' => 'Day 3 – Tea',
                'match_type' => 'Test', 'venue' => "Lord's, London",
                'is_live' => true, 'sort_order' => 1,
            ],
            [
                'team_a' => 'PAK', 'team_b' => 'SA',
                'score_a' => '—', 'score_b' => '—',
                'overs' => null, 'status' => 'Upcoming – Tomorrow 14:00',
                'match_type' => 'ODI', 'venue' => 'Lahore',
                'is_live' => false, 'sort_order' => 2,
            ],
        ];

        foreach ($matches as $m) {
            LiveScore::create($m);
        }
    }
}
