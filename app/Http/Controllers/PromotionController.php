<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /** GET /api/ads/public — public endpoint, returns active promos keyed by position */
    public function publicIndex(): JsonResponse
    {
        $promos = Promotion::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->orderByDesc('priority')
            ->get()
            ->groupBy('position')
            ->map(fn ($group) => $group->first()); // top-priority per position

        return response()->json($promos);
    }

    /** GET /api/admin/promotions */
    public function index(): JsonResponse
    {
        return response()->json(Promotion::orderByDesc('id')->get());
    }

    /** POST /api/admin/promotions */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'image_url'  => 'required|string',
            'link_url'   => 'nullable|url',
            'position'   => 'required|string|in:' . implode(',', array_keys(Promotion::POSITIONS)),
            'new_tab'    => 'boolean',
            'is_active'  => 'boolean',
            'priority'   => 'integer|min:0',
            'starts_at'  => 'nullable|date',
            'ends_at'    => 'nullable|date|after_or_equal:starts_at',
        ]);

        return response()->json(Promotion::create($data), 201);
    }

    /** PUT /api/admin/promotions/{promotion} */
    public function update(Request $request, Promotion $promotion): JsonResponse
    {
        $data = $request->validate([
            'title'      => 'sometimes|string|max:255',
            'image_url'  => 'sometimes|string',
            'link_url'   => 'nullable|url',
            'position'   => 'sometimes|string|in:' . implode(',', array_keys(Promotion::POSITIONS)),
            'new_tab'    => 'boolean',
            'is_active'  => 'boolean',
            'priority'   => 'integer|min:0',
            'starts_at'  => 'nullable|date',
            'ends_at'    => 'nullable|date',
        ]);

        $promotion->update($data);
        return response()->json($promotion->fresh());
    }

    /** DELETE /api/admin/promotions/{promotion} */
    public function destroy(Promotion $promotion): JsonResponse
    {
        $promotion->delete();
        return response()->json(null, 204);
    }
}
