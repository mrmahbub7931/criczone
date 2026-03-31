<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $fillable = ['question', 'options', 'is_active', 'ends_at'];
    protected $casts    = ['options' => 'array', 'is_active' => 'boolean', 'ends_at' => 'datetime'];

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function voteCounts(): array
    {
        $counts = $this->votes()->selectRaw('option_index, count(*) as total')
            ->groupBy('option_index')->pluck('total', 'option_index')->toArray();

        $result = [];
        foreach ($this->options as $i => $option) {
            $result[] = ['option' => $option, 'votes' => $counts[$i] ?? 0];
        }
        return $result;
    }

    public function totalVotes(): int
    {
        return $this->votes()->count();
    }
}
