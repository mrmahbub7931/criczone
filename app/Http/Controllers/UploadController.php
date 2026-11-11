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
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ]);

        $file = $request->file('image');
        $dest = public_path('uploads/articles');

        if (! is_dir($dest)) {
            mkdir($dest, 0775, true);
        }

        $ext      = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $filename = Str::uuid() . '.' . $ext;

        // Try Intervention Image v3 (resize + compress)
        try {
            if (! extension_loaded('gd')) {
                throw new \RuntimeException('GD not available');
            }

            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $img = $manager->read($file->getRealPath());

            if ($img->width() > 1920) {
                $img->scaleDown(width: 1920);
            }

            if ($ext === 'webp') {
                $img->toWebp(85)->save($dest . '/' . $filename);
            } else {
                $img->toJpeg(85)->save($dest . '/' . ($filename = Str::uuid() . '.jpg'));
            }

        } catch (\Throwable $e) {
            // Fallback: save original file as-is
            $file->move($dest, $filename);
        }

        return response()->json([
            'url' => '/uploads/articles/' . $filename,
        ]);
    }
}
