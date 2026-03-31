<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /** GET /api/menus — list all menus (admin) */
    public function index(): JsonResponse
    {
        $menus = Menu::withCount('allItems')->orderBy('location')->get();
        return response()->json($menus);
    }

    /** POST /api/menus */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'location' => 'required|string|max:60|unique:menus,location',
        ]);

        $menu = Menu::create($data);
        return response()->json($menu, 201);
    }

    /** PUT /api/menus/{menu} */
    public function update(Request $request, Menu $menu): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'sometimes|string|max:100',
            'location' => 'sometimes|string|max:60|unique:menus,location,' . $menu->id,
        ]);

        $menu->update($data);
        return response()->json($menu);
    }

    /** DELETE /api/menus/{menu} */
    public function destroy(Menu $menu): JsonResponse
    {
        $menu->delete(); // cascades to menu_items
        return response()->json(null, 204);
    }

    // ── Public endpoint ──────────────────────────────────────────────────────

    /** GET /api/menus/{location}/items — public, nested tree */
    public function publicMenu(string $location): JsonResponse
    {
        $menu = Menu::where('location', $location)->first();

        if (! $menu) {
            return response()->json(['items' => []]);
        }

        $items = MenuItem::with(['children.page', 'page'])
            ->where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get()
            ->map(fn ($item) => $this->formatItem($item));

        return response()->json([
            'id'       => $menu->id,
            'name'     => $menu->name,
            'location' => $menu->location,
            'items'    => $items,
        ]);
    }

    private function formatItem(MenuItem $item): array
    {
        return [
            'id'       => $item->id,
            'label'    => $item->label,
            'url'      => $item->resolved_url,
            'target'   => $item->target,
            'type'     => $item->type,
            'children' => $item->children->map(fn ($c) => $this->formatItem($c))->values()->all(),
        ];
    }

    /** GET /api/menus/{menu}/items — admin: all items flat for editing */
    public function items(Menu $menu): JsonResponse
    {
        $items = MenuItem::with('page:id,title,slug')
            ->where('menu_id', $menu->id)
            ->orderBy('parent_id')
            ->orderBy('order')
            ->get();

        return response()->json($items);
    }
}
