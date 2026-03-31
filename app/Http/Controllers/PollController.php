<?php
namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PollController extends Controller
{
    /** GET /api/poll/active — public */
    public function active(Request $request): JsonResponse
    {
        $poll = Poll::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->latest()
            ->first();

        if (! $poll) {
            return response()->json(null);
        }

        $voterId  = hash('sha256', $request->ip() . $request->userAgent());
        $hasVoted = PollVote::where('poll_id', $poll->id)->where('voter_id', $voterId)->exists();

        return response()->json([
            'id'          => $poll->id,
            'question'    => $poll->question,
            'options'     => $poll->voteCounts(),
            'total_votes' => $poll->totalVotes(),
            'has_voted'   => $hasVoted,
            'ends_at'     => $poll->ends_at?->toISOString(),
        ]);
    }

    /** POST /api/poll/{poll}/vote — public */
    public function vote(Request $request, Poll $poll): JsonResponse
    {
        if (! $poll->is_active || ($poll->ends_at && $poll->ends_at->isPast())) {
            return response()->json(['message' => 'Poll is closed.'], 422);
        }

        $data = $request->validate([
            'option_index' => 'required|integer|min:0|max:' . (count($poll->options) - 1),
        ]);

        $voterId = hash('sha256', $request->ip() . $request->userAgent());

        try {
            PollVote::create([
                'poll_id'      => $poll->id,
                'option_index' => $data['option_index'],
                'voter_id'     => $voterId,
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return response()->json(['message' => 'You have already voted.'], 422);
        }

        return response()->json([
            'options'     => $poll->fresh()->voteCounts(),
            'total_votes' => $poll->fresh()->totalVotes(),
            'has_voted'   => true,
        ]);
    }

    /** Admin CRUD ── GET /api/admin/polls */
    public function index(): JsonResponse
    {
        return response()->json(Poll::withCount('votes')->latest()->get());
    }

    /** POST /api/admin/polls */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'question'  => 'required|string|max:255',
            'options'   => 'required|array|min:2|max:6',
            'options.*' => 'required|string|max:100',
            'is_active' => 'boolean',
            'ends_at'   => 'nullable|date',
        ]);
        // Deactivate others if this is active
        if (! empty($data['is_active'])) {
            Poll::where('is_active', true)->update(['is_active' => false]);
        }
        return response()->json(Poll::create($data), 201);
    }

    /** PUT /api/admin/polls/{poll} */
    public function update(Request $request, Poll $poll): JsonResponse
    {
        $data = $request->validate([
            'question'  => 'required|string|max:255',
            'options'   => 'required|array|min:2|max:6',
            'options.*' => 'required|string|max:100',
            'is_active' => 'boolean',
            'ends_at'   => 'nullable|date',
        ]);
        if (! empty($data['is_active']) && ! $poll->is_active) {
            Poll::where('is_active', true)->where('id', '!=', $poll->id)->update(['is_active' => false]);
        }
        $poll->update($data);
        return response()->json($poll->fresh());
    }

    /** DELETE /api/admin/polls/{poll} */
    public function destroy(Poll $poll): JsonResponse
    {
        $poll->delete();
        return response()->json(null, 204);
    }
}
