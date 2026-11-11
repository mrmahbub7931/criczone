<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use App\Models\PhotoGallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PhotoGalleryController extends Controller
{
    // ── Public ────────────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $galleries = PhotoGallery::where('status', 'published')
            ->with(['photos' => fn ($q) => $q->limit(1)])
            ->withCount('photos')
            ->latest('published_at')
            ->paginate($request->integer('per_page', 12));

        return response()->json($galleries);
    }

    public function show(string $slug): JsonResponse
    {
        $gallery = PhotoGallery::where('slug', $slug)
            ->where('status', 'published')
            ->with('photos')
            ->firstOrFail();

        return response()->json($gallery);
    }

    // ── Admin ─────────────────────────────────────────────────────────────────

    public function adminShow(PhotoGallery $gallery): JsonResponse
    {
        $gallery->load('photos')->loadCount('photos');

        // Auto-fix: if cover_image points to a deleted photo, reset to first available
        $paths = $gallery->photos->pluck('image_path')->all();
        if ($gallery->cover_image && ! in_array($gallery->cover_image, $paths, true)) {
            $first = $gallery->photos->first();
            $gallery->update(['cover_image' => $first?->image_path]);
            $gallery->cover_image = $first?->image_path;
        }

        return response()->json($gallery);
    }

    public function setCover(Request $request, PhotoGallery $gallery): JsonResponse
    {
        $data = $request->validate(['image_path' => 'required|string']);
        $gallery->update(['cover_image' => $data['image_path']]);
        return response()->json(['cover_image' => $gallery->cover_image]);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $galleries = PhotoGallery::with(['photos' => fn ($q) => $q->limit(1)])
            ->withCount('photos')
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return response()->json($galleries);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:300',
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|string',
            'status'      => 'in:draft,published',
        ]);

        $data['slug']       = $this->uniqueSlug($data['title']);
        $data['created_by'] = $request->user()->id;

        if (($data['status'] ?? 'draft') === 'published') {
            $data['published_at'] = now();
        }

        $gallery = PhotoGallery::create($data);

        return response()->json($gallery->load('photos'), 201);
    }

    public function update(Request $request, PhotoGallery $gallery): JsonResponse
    {
        $data = $request->validate([
            'title'       => 'sometimes|required|string|max:300',
            'title_en'    => 'nullable|string|max:300',
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|string',
            'status'      => 'in:draft,published',
        ]);

        if (isset($data['title']) && $data['title'] !== $gallery->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $gallery->id);
        }

        if (isset($data['status']) && $data['status'] === 'published' && ! $gallery->published_at) {
            $data['published_at'] = now();
        }

        $gallery->update($data);

        return response()->json($gallery->load('photos')->loadCount('photos'));
    }

    public function destroy(PhotoGallery $gallery): JsonResponse
    {
        $gallery->delete();
        return response()->json(['message' => 'Gallery deleted.']);
    }

    public function addPhoto(Request $request, PhotoGallery $gallery): JsonResponse
    {
        $data = $request->validate([
            'image_path'   => 'required|string',
            'caption'      => 'nullable|string|max:300',
            'photographer' => 'nullable|string|max:200',
        ]);

        $data['sort_order'] = $gallery->photos()->max('sort_order') + 1;

        $photo = $gallery->photos()->create($data);

        // Auto-set cover_image if gallery has none
        if (! $gallery->cover_image) {
            $gallery->update(['cover_image' => $data['image_path']]);
        }

        return response()->json($photo, 201);
    }

    public function updatePhoto(Request $request, PhotoGallery $gallery, GalleryPhoto $photo): JsonResponse
    {
        abort_if($photo->gallery_id !== $gallery->id, 403);

        $data = $request->validate([
            'caption'      => 'nullable|string|max:500',
            'caption_en'   => 'nullable|string|max:500',
            'photographer' => 'nullable|string|max:200',
            'sort_order'   => 'nullable|integer|min:0',
        ]);

        $photo->update($data);
        return response()->json($photo);
    }

    public function removePhoto(PhotoGallery $gallery, GalleryPhoto $photo): JsonResponse
    {
        abort_if($photo->gallery_id !== $gallery->id, 403);

        $deletedPath = $photo->image_path;
        $photo->delete();

        // If this was the cover image, replace with the next available photo
        if ($gallery->cover_image === $deletedPath) {
            $next = $gallery->photos()->orderBy('sort_order')->first();
            $gallery->update(['cover_image' => $next?->image_path]);
        }

        return response()->json(['message' => 'Photo removed.', 'cover_image' => $gallery->fresh()->cover_image]);
    }

    public function translateCaptions(PhotoGallery $gallery): JsonResponse
    {
        $photos = $gallery->photos()->whereNotNull('caption')->where('caption', '!=', '')->get();

        if ($photos->isEmpty()) {
            return response()->json(['message' => 'No captions to translate.', 'updated' => 0]);
        }

        $apiKey = \App\Models\Setting::where('key', 'anthropic_api_key')->value('value')
            ?? config('services.anthropic.key')
            ?? env('ANTHROPIC_API_KEY');

        if (! $apiKey) {
            return response()->json(['error' => 'Anthropic API key not configured.'], 422);
        }

        $captionMap = $photos->mapWithKeys(fn ($p) => [$p->id => $p->caption])->toArray();

        $payload = [
            '__title__' => $gallery->title,
        ] + $captionMap;

        $prompt = "Translate these Bengali texts to English. Keep cricket terminology, player names, and place names accurate. The key '__title__' is the gallery title. Other keys are numeric photo IDs with their captions. Return ONLY a JSON object with the same keys and English translations as values. No extra text.\n\n" . json_encode($payload, JSON_UNESCAPED_UNICODE);

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-haiku-4-5',
            'max_tokens' => 2048,
            'messages'   => [['role' => 'user', 'content' => $prompt]],
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Translation API error: ' . $response->body()], 502);
        }

        $text = $response->json('content.0.text');
        $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
        $text = preg_replace('/```\s*$/m', '', $text);
        $translations = json_decode(trim($text), true);

        if (! is_array($translations)) {
            return response()->json(['error' => 'Could not parse translation response.'], 502);
        }

        // Save gallery title translation
        if (! empty($translations['__title__'])) {
            $gallery->update(['title_en' => $translations['__title__']]);
        }

        $updated = 0;
        foreach ($photos as $photo) {
            $en = $translations[(string) $photo->id] ?? $translations[$photo->id] ?? null;
            if ($en) {
                $photo->update(['caption_en' => $en]);
                $updated++;
            }
        }

        return response()->json([
            'message'  => "{$updated} captions translated.",
            'updated'  => $updated,
            'title_en' => $gallery->fresh()->title_en,
            'photos'   => $gallery->photos()->get(),
        ]);
    }

    public function reorder(Request $request, PhotoGallery $gallery): JsonResponse
    {
        $items = $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer',
        ])['order'];

        foreach ($items as $index => $photoId) {
            GalleryPhoto::where('id', $photoId)
                ->where('gallery_id', $gallery->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['message' => 'Reordered.']);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function uniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 1;

        while (
            PhotoGallery::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-" . $i++;
        }

        return $slug;
    }
}
