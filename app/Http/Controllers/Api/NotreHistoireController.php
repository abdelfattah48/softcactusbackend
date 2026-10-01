<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotreHistoire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotreHistoireController extends Controller
{
    /**
     * GET /api/notre-histoire
     * Returns all history entries (used by backoffice)
     */
    public function index()
    {
        $entries = NotreHistoire::ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $entries->map(fn($entry) => $this->formatEntry($entry)),
        ]);
    }

    /**
     * GET /api/notre-histoire/public
     * Public endpoint for frontend (returns only enabled entries with localized content)
     */
    public function publicData(Request $request)
    {
        $locale = $request->get('locale', 'fr');
        $entries = NotreHistoire::enabled()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $entries->map(function($entry) use ($locale) {
                return [
                    'id' => $entry->id,
                    'year' => $entry->year,
                    'description' => $entry->getLocalizedDescription($locale),
                ];
            }),
        ]);
    }

    /**
     * POST /api/notre-histoire
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:1900|max:2100|unique:notre_histoire,year',
            'description' => 'nullable|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'enabled' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $maxOrder = NotreHistoire::max('sort_order') ?? 0;

        $entry = NotreHistoire::create([
            'year' => $request->year,
            'description' => $request->description ?? ($request->description_fr ?? ''),
            'description_fr' => $request->description_fr,
            'description_en' => $request->description_en,
            'enabled' => $request->boolean('enabled', true),
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->formatEntry($entry),
        ], 201);
    }

    /**
     * PUT /api/notre-histoire/{id}
     */
    public function update(Request $request, $id)
    {
        $entry = NotreHistoire::find($id);

        if (!$entry) {
            return response()->json(['success' => false, 'message' => 'Entry not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'year' => 'sometimes|integer|min:1900|max:2100|unique:notre_histoire,year,' . $id,
            'description' => 'sometimes|nullable|string',
            'description_fr' => 'sometimes|nullable|string',
            'description_en' => 'sometimes|nullable|string',
            'enabled' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $entry->update($request->only([
            'year', 'description', 'description_fr', 'description_en', 'enabled', 'sort_order'
        ]));

        return response()->json(['success' => true, 'data' => $this->formatEntry($entry)]);
    }

    /**
     * PATCH /api/notre-histoire/{id}/toggle
     * Toggles the enabled flag
     */
    public function toggle($id)
    {
        $entry = NotreHistoire::find($id);

        if (!$entry) {
            return response()->json(['success' => false, 'message' => 'Entry not found'], 404);
        }

        $entry->update(['enabled' => !$entry->enabled]);

        return response()->json(['success' => true, 'data' => $this->formatEntry($entry)]);
    }

    /**
     * DELETE /api/notre-histoire/{id}
     */
    public function destroy($id)
    {
        $entry = NotreHistoire::find($id);

        if (!$entry) {
            return response()->json(['success' => false, 'message' => 'Entry not found'], 404);
        }

        $entry->delete();

        return response()->json(['success' => true, 'message' => 'Entry deleted']);
    }

    /**
     * PATCH /api/notre-histoire/reorder
     * Reorder entries
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entry_ids' => 'required|array',
            'entry_ids.*' => 'required|integer|exists:notre_histoire,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->entry_ids as $index => $entryId) {
            NotreHistoire::where('id', $entryId)
                ->update(['sort_order' => $index + 1]);
        }

        $entries = NotreHistoire::ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $entries->map(fn($entry) => $this->formatEntry($entry)),
        ]);
    }

    /**
     * Format entry for API response
     */
    private function formatEntry(NotreHistoire $entry): array
    {
        return [
            'id' => $entry->id,
            'year' => $entry->year,
            'description' => $entry->description,
            'description_fr' => $entry->description_fr,
            'description_en' => $entry->description_en,
            'enabled' => $entry->enabled,
            'sort_order' => $entry->sort_order,
            'created_at' => $entry->created_at,
            'updated_at' => $entry->updated_at,
        ];
    }
}