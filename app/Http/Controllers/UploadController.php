<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UploadController extends Controller
{
    public function image(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        $file = $request->file('image');
        $dest = public_path('uploads/articles');

        if (! is_dir($dest)) {
            mkdir($dest, 0775, true);
        }

        $manager = new ImageManager(new Driver());
        $img     = $manager->read($file->getRealPath());

        // Scale down to max 1920px wide (only shrinks, never enlarges)
        if ($img->width() > 1920) {
            $img->scaleDown(width: 1920);
        }

        $filename = Str::uuid() . '.jpg';
        $img->toJpeg(85)->save($dest . '/' . $filename);

        return response()->json([
            'url' => '/uploads/articles/' . $filename,
        ]);
    }
}
