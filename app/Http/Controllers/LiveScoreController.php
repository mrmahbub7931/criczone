<?php

namespace App\Http\Controllers;

use App\Models\LiveScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveScoreController extends Controller
{
    /** GET /api/live-scores — public */
    public function publicList(): JsonResponse
    {
        $scores = LiveScore::orderByDesc('is_live')
            ->orderBy('sort_order')
            ->orderByDesc('match_time')
            ->get();

        return response()->json($scores);
    }

    /** GET /api/admin/live-scores — admin */
    public function index(): JsonResponse
    {
        return response()->json(
            LiveScore::orderByDesc('is_live')->orderBy('sort_order')->get()
        );
    }

    /** POST /api/admin/live-scores */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'team_a'     => 'required|string|max:60',
            'team_b'     => 'required|string|max:60',
            'score_a'    => 'nullable|string|max:40',
            'score_b'    => 'nullable|string|max:40',
            'overs'      => 'nullable|string|max:20',
            'status'     => 'nullable|string|max:100',
            'match_type' => 'nullable|string|max:60',
            'venue'      => 'nullable|string|max:150',
            'match_time' => 'nullable|date',
            'is_live'    => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        return response()->json(LiveScore::create($data), 201);
    }

    /** PUT /api/admin/live-scores/{score} */
    public function update(Request $request, LiveScore $score): JsonResponse
    {
        $data = $request->validate([
            'team_a'     => 'sometimes|string|max:60',
            'team_b'     => 'sometimes|string|max:60',
            'score_a'    => 'nullable|string|max:40',
            'score_b'    => 'nullable|string|max:40',
            'overs'      => 'nullable|string|max:20',
            'status'     => 'nullable|string|max:100',
            'match_type' => 'nullable|string|max:60',
            'venue'      => 'nullable|string|max:150',
            'match_time' => 'nullable|date',
            'is_live'    => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $score->update($data);
        return response()->json($score);
    }

    /** DELETE /api/admin/live-scores/{score} */
    public function destroy(LiveScore $score): JsonResponse
    {
        $score->delete();
        return response()->json(null, 204);
    }
}
