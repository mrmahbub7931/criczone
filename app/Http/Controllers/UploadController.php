<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function image(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:4096'],
        ]);

        $file      = $request->file('image');
        $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $dest      = public_path('uploads/articles');

        if (! is_dir($dest)) {
            mkdir($dest, 0775, true);
        }

        $file->move($dest, $filename);

        return response()->json([
            'url' => '/uploads/articles/' . $filename,
        ]);
    }
}
