<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuiSommesNousService;
use App\Models\QuiSommesNousSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuiSommesNousController extends Controller
{
    // -------------------------------------------------------------------------
    // Main content (single settings row + services)
    // -------------------------------------------------------------------------

    /**
     * GET /api/qui-sommes-nous
     * Returns settings + services in one payload (used by both backoffice and frontend)
     */
    public function index()
    {
        $settings = QuiSommesNousSetting::instance();
        $services = QuiSommesNousService::ordered()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'description'    => $settings->description,
                'description_fr' => $settings->description_fr,
                'description_en' => $settings->description_en,
                'team_image_url' => $this->fixStorageUrl($settings->team_image_url),
                'services'       => $services->map(fn($service) => $this->formatService($service)),
            ],
        ]);
    }

    /**
     * GET /api/qui-sommes-nous/public
     * Public endpoint for frontend (returns only enabled services with localized content)
     */
    public function publicData(Request $request)
    {
        $locale = $request->get('locale', 'fr'); // Default to French
        $settings = QuiSommesNousSetting::instance();
        $services = QuiSommesNousService::enabled()->ordered()->get();

        // Get localized description
        $description = match($locale) {
            'en' => $settings->description_en ?: $settings->description_fr ?: $settings->description ?: '',
            'fr' => $settings->description_fr ?: $settings->description ?: $settings->description_en ?: '',
            default => $settings->description ?: $settings->description_fr ?: $settings->description_en ?: '',
        };

        return response()->json([
            'success' => true,
            'data' => [
                'description'    => $description,
                'team_image_url' => $this->fixStorageUrl($settings->team_image_url),
                'services' => $services->map(function($service) use ($locale) {
                    return [
                        'id' => $service->id,
                        'title' => $service->getLocalizedTitle($locale),
                        'text' => $service->getLocalizedText($locale),
                    ];
                }),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Settings management
    // -------------------------------------------------------------------------

    /**
     * PATCH /api/qui-sommes-nous/settings
     * Updates settings: descriptions, team image (base64 or URL), etc.
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description'    => 'sometimes|nullable|string',
            'description_fr' => 'sometimes|nullable|string',
            'description_en' => 'sometimes|nullable|string',
            'team_image_url' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $settings = QuiSommesNousSetting::instance();
        $updateData = $request->only([
            'description', 'description_fr', 'description_en'
        ]);

        if ($request->has('team_image_url')) {
            $newImage = $request->input('team_image_url');
            if (is_string($newImage) && preg_match('/^data:(\w+\/[\w+-]+);base64,/', $newImage, $matches)) {
                if ($settings->team_image_url) {
                    $this->deleteStoredImage($settings->team_image_url);
                }
                $updateData['team_image_url'] = $this->saveBase64Image($newImage, $matches[1]);
            } elseif ($newImage === null || $newImage === '') {
                if ($settings->team_image_url) {
                    $this->deleteStoredImage($settings->team_image_url);
                }
                $updateData['team_image_url'] = null;
            } else {
                $updateData['team_image_url'] = $newImage;
            }
        }

        $settings->update($updateData);

        return response()->json([
            'success' => true,
            'data'    => [
                'description'    => $settings->description,
                'description_fr' => $settings->description_fr,
                'description_en' => $settings->description_en,
                'team_image_url' => $this->fixStorageUrl($settings->team_image_url),
            ],
        ]);
    }

    /**
     * PATCH /api/qui-sommes-nous/description
     * Backwards-compatible alias of updateSettings (for old backoffice versions).
     */
    public function updateDescription(Request $request)
    {
        return $this->updateSettings($request);
    }

    // -------------------------------------------------------------------------
    // Services management
    // -------------------------------------------------------------------------

    /**
     * POST /api/qui-sommes-nous/services
     */
    public function storeService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'nullable|string|max:255',
            'title_fr'  => 'nullable|string|max:255',
            'title_en'  => 'nullable|string|max:255',
            'text'      => 'nullable|string',
            'text_fr'   => 'nullable|string',
            'text_en'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $maxOrder = QuiSommesNousService::max('sort_order') ?? 0;

        $service = QuiSommesNousService::create([
            'title'      => $request->title ?? ($request->title_fr ?? ''),
            'title_fr'   => $request->title_fr,
            'title_en'   => $request->title_en,
            'text'       => $request->text ?? ($request->text_fr ?? ''),
            'text_fr'    => $request->text_fr,
            'text_en'    => $request->text_en,
            'enabled'    => $request->boolean('enabled', true),
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $this->formatService($service),
        ], 201);
    }

    /**
     * PUT /api/qui-sommes-nous/services/{id}
     */
    public function updateService(Request $request, $id)
    {
        $service = QuiSommesNousService::find($id);

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'      => 'sometimes|nullable|string|max:255',
            'title_fr'   => 'sometimes|nullable|string|max:255',
            'title_en'   => 'sometimes|nullable|string|max:255',
            'text'       => 'sometimes|nullable|string',
            'text_fr'    => 'sometimes|nullable|string',
            'text_en'    => 'sometimes|nullable|string',
            'enabled'    => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $updateData = $request->only([
            'title', 'title_fr', 'title_en',
            'text', 'text_fr', 'text_en',
            'enabled', 'sort_order'
        ]);

        $service->update($updateData);

        return response()->json(['success' => true, 'data' => $this->formatService($service)]);
    }

    /**
     * PATCH /api/qui-sommes-nous/services/{id}/toggle
     * Toggles the enabled flag
     */
    public function toggleService($id)
    {
        $service = QuiSommesNousService::find($id);

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service not found'], 404);
        }

        $service->update(['enabled' => !$service->enabled]);

        return response()->json(['success' => true, 'data' => $this->formatService($service)]);
    }

    /**
     * DELETE /api/qui-sommes-nous/services/{id}
     */
    public function destroyService($id)
    {
        $service = QuiSommesNousService::find($id);

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service not found'], 404);
        }

        $service->delete();

        return response()->json(['success' => true, 'message' => 'Service deleted']);
    }

    /**
     * PATCH /api/qui-sommes-nous/services/reorder
     * Reorder services
     */
    public function reorderServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_ids' => 'required|array',
            'service_ids.*' => 'required|integer|exists:qui_sommes_nous_services,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        foreach ($request->service_ids as $index => $serviceId) {
            QuiSommesNousService::where('id', $serviceId)
                ->update(['sort_order' => $index + 1]);
        }

        $services = QuiSommesNousService::ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $services->map(fn($service) => $this->formatService($service)),
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function formatService(QuiSommesNousService $service): array
    {
        return [
            'id'         => $service->id,
            'title'      => $service->title,
            'title_fr'   => $service->title_fr,
            'title_en'   => $service->title_en,
            'text'       => $service->text,
            'text_fr'    => $service->text_fr,
            'text_en'    => $service->text_en,
            'enabled'    => $service->enabled,
            'sort_order' => $service->sort_order,
            'created_at' => $service->created_at,
            'updated_at' => $service->updated_at,
        ];
    }

    private function fixStorageUrl(?string $url): string
    {
        if (!$url) return '';
        // Normalize storage URLs to work with APP_URL
        $appUrl = rtrim(config('app.url'), '/');
        return preg_replace('#^https?://[^/]*/storage#', $appUrl . '/storage', $url);
    }

    /**
     * Save a base64 data-URL image to storage (same pattern as ProjectController).
     */
    private function saveBase64Image(string $base64String, string $mimeType): string
    {
        $base64String = preg_replace('/^data:\w+\/[\w+-]+;base64,/', '', $base64String);
        $fileData = base64_decode($base64String);

        $extension = 'bin';
        $mimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
        ];
        if (isset($mimeTypes[$mimeType])) {
            $extension = $mimeTypes[$mimeType];
        } else {
            $parts = explode('/', $mimeType);
            if (count($parts) === 2) $extension = $parts[1];
        }

        $filename = Str::random(40) . '.' . $extension;
        $path = 'qui-sommes-nous/' . $filename;
        Storage::disk('public')->put($path, $fileData);

        return Storage::disk('public')->url($path);
    }

    /**
     * Delete a previously stored image if it lives in the public disk storage path.
     */
    private function deleteStoredImage(string $url): void
    {
        $relative = preg_replace('#^https?://[^/]+/storage/?#', '', $url);
        if ($relative && $relative !== $url) {
            try {
                Storage::disk('public')->delete($relative);
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
}