<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhyUsCard;
use App\Models\WhyUsFeature;
use App\Models\WhyUsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WhyUsController extends Controller
{
    // -------------------------------------------------------------------------
    // Main content (single settings row)
    // -------------------------------------------------------------------------

    /**
     * GET /api/why-us
     * Returns settings + features + cards in one payload (used by both
     * backoffice and the public frontend).
     */
    public function index()
    {
        $settings = WhyUsSetting::instance();
        $features = WhyUsFeature::orderBy('sort_order')->orderBy('id')->get();
        $cards    = WhyUsCard::orderBy('sort_order')->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'description'          => $settings->description,
                'description_fr'       => $settings->description_fr,
                'description_en'       => $settings->description_en,
                'description_bold'     => $settings->description_bold,
                'description_bold_fr'  => $settings->description_bold_fr,
                'description_bold_en'  => $settings->description_bold_en,
                'features'             => $features,
                'cards'                => $cards->map(fn($c) => $this->formatCard($c)),
            ],
        ]);
    }

    /**
     * PATCH /api/why-us/settings
     * Updates the description text.
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description'          => 'sometimes|nullable|string',
            'description_fr'       => 'sometimes|nullable|string',
            'description_en'       => 'sometimes|nullable|string',
            'description_bold'     => 'sometimes|nullable|string',
            'description_bold_fr'  => 'sometimes|nullable|string',
            'description_bold_en'  => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $settings = WhyUsSetting::instance();
        $settings->update($request->only([
            'description', 'description_fr', 'description_en',
            'description_bold', 'description_bold_fr', 'description_bold_en',
        ]));

        return response()->json([
            'success' => true,
            'data'    => $settings,
        ]);
    }

    /**
     * GET /api/why-us/icons
     * Returns the list of available Lucide icon names the backoffice can pick.
     */
    public function icons()
    {
        $icons = [
            'Award', 'Star', 'Shield', 'Zap', 'Target', 'TrendingUp',
            'Users', 'Heart', 'CheckCircle', 'Lightbulb', 'Rocket',
            'Globe', 'Clock', 'ThumbsUp', 'Smile', 'Lock',
        ];

        return response()->json(['success' => true, 'data' => $icons]);
    }

    // -------------------------------------------------------------------------
    // Features
    // -------------------------------------------------------------------------

    /**
     * POST /api/why-us/features
     */
    public function storeFeature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'          => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'icon'           => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $maxOrder = WhyUsFeature::max('sort_order') ?? 0;

        $feature = WhyUsFeature::create([
            'title'          => $request->title ?? '',
            'description'    => $request->description ?? ($request->description_fr ?? ''),
            'description_fr' => $request->description_fr,
            'description_en' => $request->description_en,
            'icon'           => $request->icon ?? 'Award',
            'enabled'        => true,
            'sort_order'     => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $feature,
        ], 201);
    }

    /**
     * PUT /api/why-us/features/{id}
     */
    public function updateFeature(Request $request, $id)
    {
        $feature = WhyUsFeature::find($id);

        if (!$feature) {
            return response()->json(['success' => false, 'message' => 'Feature not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'          => 'sometimes|nullable|string|max:255',
            'description'    => 'sometimes|nullable|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'icon'           => 'nullable|string|max:50',
            'enabled'        => 'sometimes|boolean',
            'sort_order'     => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $feature->update($request->only([
            'title', 'description', 'description_fr', 'description_en',
            'icon', 'enabled', 'sort_order',
        ]));

        return response()->json(['success' => true, 'data' => $feature]);
    }

    /**
     * PATCH /api/why-us/features/{id}/toggle
     * Toggles the enabled flag.
     */
    public function toggleFeature($id)
    {
        $feature = WhyUsFeature::find($id);

        if (!$feature) {
            return response()->json(['success' => false, 'message' => 'Feature not found'], 404);
        }

        $feature->update(['enabled' => !$feature->enabled]);

        return response()->json(['success' => true, 'data' => $feature]);
    }

    /**
     * DELETE /api/why-us/features/{id}
     */
    public function destroyFeature($id)
    {
        $feature = WhyUsFeature::find($id);

        if (!$feature) {
            return response()->json(['success' => false, 'message' => 'Feature not found'], 404);
        }

        $feature->delete();

        return response()->json(['success' => true, 'message' => 'Feature deleted']);
    }

    // -------------------------------------------------------------------------
    // Cards (video testimonials)
    // -------------------------------------------------------------------------

    /**
     * POST /api/why-us/cards
     * Accepts multipart/form-data with optional cover and video file uploads.
     */
    public function storeCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'role'       => 'nullable|string|max:255',
            'cover'      => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'video'      => 'nullable|file|mimes:mp4,mov,avi,webm|max:204800',
            'cover_url'  => 'nullable|string',
            'video_url'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $coverUrl = $request->cover_url;
        $videoUrl = $request->video_url;

        if ($request->hasFile('cover')) {
            $coverUrl = Storage::disk('public')->url(
                $request->file('cover')->store('why-us/covers', 'public')
            );
        }

        if ($request->hasFile('video')) {
            $videoUrl = Storage::disk('public')->url(
                $request->file('video')->store('why-us/videos', 'public')
            );
        }

        $maxOrder = WhyUsCard::max('sort_order') ?? 0;

        $card = WhyUsCard::create([
            'name'       => $request->name,
            'role'       => $request->role,
            'cover_url'  => $coverUrl,
            'video_url'  => $videoUrl,
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $this->formatCard($card),
        ], 201);
    }

    /**
     * PUT /api/why-us/cards/{id}
     */
    public function updateCard(Request $request, $id)
    {
        $card = WhyUsCard::find($id);

        if (!$card) {
            return response()->json(['success' => false, 'message' => 'Card not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'      => 'sometimes|required|string|max:255',
            'role'      => 'nullable|string|max:255',
            'cover'     => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'video'     => 'nullable|file|mimes:mp4,mov,avi,webm|max:204800',
            'cover_url' => 'nullable|string',
            'video_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('cover')) {
            $request->merge(['cover_url' => Storage::disk('public')->url(
                $request->file('cover')->store('why-us/covers', 'public')
            )]);
        }

        if ($request->hasFile('video')) {
            $request->merge(['video_url' => Storage::disk('public')->url(
                $request->file('video')->store('why-us/videos', 'public')
            )]);
        }

        $card->update($request->only(['name', 'role', 'cover_url', 'video_url', 'sort_order']));

        return response()->json(['success' => true, 'data' => $this->formatCard($card)]);
    }

    /**
     * DELETE /api/why-us/cards/{id}
     */
    public function destroyCard($id)
    {
        $card = WhyUsCard::find($id);

        if (!$card) {
            return response()->json(['success' => false, 'message' => 'Card not found'], 404);
        }

        $card->delete();

        return response()->json(['success' => true, 'message' => 'Card deleted']);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function formatCard(WhyUsCard $card): array
    {
        return [
            'id'        => $card->id,
            'name'      => $card->name,
            'role'      => $card->role,
            'cover'     => $card->cover_url ? ['url' => $this->fixStorageUrl($card->cover_url)] : null,
            'video'     => $card->video_url ? ['url' => $this->fixStorageUrl($card->video_url)] : null,
            'sort_order' => $card->sort_order,
        ];
    }

    private function fixStorageUrl(?string $url): string
    {
        if (!$url) return '';
        // When APP_URL includes a port, Storage::url() may generate the wrong base.
        // Normalise to the APP_URL host so the React app can always reach the file.
        $appUrl = rtrim(config('app.url'), '/');
        return preg_replace('#^https?://[^/]*/storage#', $appUrl . '/storage', $url);
    }
}
