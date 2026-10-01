<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuiSommesNousService;
use App\Models\QuiSommesNousSetting;
use Illuminate\Http\Request;
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
                'description' => $description,
                'services' => $services->map(function($service) use ($locale) {
                    return [
                        'id' => $service->id,
                        'title' => $service->getLocalizedTitle($locale),
                        'text' => $service->getLocalizedText($locale),
                        'icon_url' => $service->icon_url ? $this->fixStorageUrl($service->icon_url) : null,
                    ];
                }),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Settings management
    // -------------------------------------------------------------------------

    /**
     * PATCH /api/qui-sommes-nous/description
     * Updates the main description
     */
    public function updateDescription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description'    => 'sometimes|nullable|string',
            'description_fr' => 'sometimes|nullable|string',
            'description_en' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $settings = QuiSommesNousSetting::instance();
        $settings->update($request->only([
            'description', 'description_fr', 'description_en'
        ]));

        return response()->json([
            'success' => true,
            'data'    => $settings,
        ]);
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
            'icon'      => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $maxOrder = QuiSommesNousService::max('sort_order') ?? 0;

        $iconUrl = null;
        if ($request->hasFile('icon')) {
            $iconUrl = Storage::disk('public')->url(
                $request->file('icon')->store('qui-sommes-nous/icons', 'public')
            );
        }

        $service = QuiSommesNousService::create([
            'title'      => $request->title ?? ($request->title_fr ?? ''),
            'title_fr'   => $request->title_fr,
            'title_en'   => $request->title_en,
            'text'       => $request->text ?? ($request->text_fr ?? ''),
            'text_fr'    => $request->text_fr,
            'text_en'    => $request->text_en,
            'icon_url'   => $iconUrl,
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
            'icon'       => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
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

        if ($request->hasFile('icon')) {
            $updateData['icon_url'] = Storage::disk('public')->url(
                $request->file('icon')->store('qui-sommes-nous/icons', 'public')
            );
        }

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

        // Delete associated icon file if exists
        if ($service->icon_url) {
            $path = str_replace(Storage::disk('public')->url(''), '', $service->icon_url);
            Storage::disk('public')->delete($path);
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
            'icon_url'   => $service->icon_url ? $this->fixStorageUrl($service->icon_url) : null,
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
}