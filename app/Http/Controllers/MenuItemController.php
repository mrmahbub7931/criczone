<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    /** POST /api/menu-items */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'menu_id'   => 'required|exists:menus,id',
            'parent_id' => 'nullable|exists:menu_items,id',
            'page_id'   => 'nullable|exists:pages,id',
            'label'     => 'required|string|max:100',
            'type'      => 'required|in:page,custom',
            'url'       => 'nullable|string|max:500',
            'target'    => 'in:_self,_blank',
            'order'     => 'integer|min:0',
        ]);

        $item = MenuItem::create($data);
        $item->load('page:id,title,slug');
        return response()->json($item, 201);
    }

    /** PUT /api/menu-items/{item} */
    public function update(Request $request, MenuItem $item): JsonResponse
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:menu_items,id',
            'page_id'   => 'nullable|exists:pages,id',
            'label'     => 'sometimes|string|max:100',
            'type'      => 'sometimes|in:page,custom',
            'url'       => 'nullable|string|max:500',
            'target'    => 'in:_self,_blank',
            'order'     => 'integer|min:0',
        ]);

        $item->update($data);
        $item->load('page:id,title,slug');
        return response()->json($item);
    }

    /** DELETE /api/menu-items/{item} */
    public function destroy(MenuItem $item): JsonResponse
    {
        $item->delete(); // cascades to children
        return response()->json(null, 204);
    }

    /** POST /api/menu-items/reorder — bulk reorder */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'items'          => 'required|array',
            'items.*.id'     => 'required|exists:menu_items,id',
            'items.*.order'  => 'required|integer|min:0',
            'items.*.parent_id' => 'nullable|exists:menu_items,id',
        ]);

        foreach ($request->items as $row) {
            MenuItem::where('id', $row['id'])->update([
                'order'     => $row['order'],
                'parent_id' => $row['parent_id'] ?? null,
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
