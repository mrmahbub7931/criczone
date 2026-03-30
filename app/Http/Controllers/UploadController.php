<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function image(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:4096'],
        ]);

        $path = $request->file('image')->store('articles', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
