<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServicesSectionSetting;
use App\Models\ServiceStep;
use App\Models\ServiceCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServicesSectionController extends Controller
{
    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    /**
     * GET /api/services-section
     * Full payload for the backoffice (all languages, all rows incl. disabled).
     */
    public function index()
    {
        $settings = ServicesSectionSetting::instance();
        $steps    = ServiceStep::ordered()->get();
        $cards    = ServiceCard::ordered()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'settings' => $this->formatSettings($settings),
                'steps'    => $steps->map(fn($s) => $this->formatStep($s)),
                'cards'    => $cards->map(fn($c) => $this->formatCard($c)),
            ],
        ]);
    }

    /**
     * GET /api/services-section/public?locale=fr
     * Localized payload for the website (enabled rows only).
     */
    public function publicData(Request $request)
    {
        $locale   = $request->get('locale', 'fr');
        $settings = ServicesSectionSetting::instance();
        $steps    = ServiceStep::enabled()->ordered()->get();
        $cards    = ServiceCard::enabled()->ordered()->get();

        $pick = function (array $vals) use ($locale) {
            if ($locale === 'en') {
                return $vals['en'] ?: ($vals['fr'] ?: ($vals['base'] ?: ''));
            }
            return $vals['fr'] ?: ($vals['base'] ?: ($vals['en'] ?: ''));
        };

        return response()->json([
            'success' => true,
            'data' => [
                'intro_prefix'    => $pick(['base' => $settings->intro_prefix, 'fr' => $settings->intro_prefix_fr, 'en' => $settings->intro_prefix_en]),
                'intro_highlight' => $pick(['base' => $settings->intro_highlight, 'fr' => $settings->intro_highlight_fr, 'en' => $settings->intro_highlight_en]),
                'lead'            => $pick(['base' => $settings->lead, 'fr' => $settings->lead_fr, 'en' => $settings->lead_en]),
                'approach_title'  => $pick(['base' => $settings->approach_title, 'fr' => $settings->approach_title_fr, 'en' => $settings->approach_title_en]),
                'button_label'    => $pick(['base' => $settings->button_label, 'fr' => $settings->button_label_fr, 'en' => $settings->button_label_en]),
                'steps' => $steps->map(fn($s) => [
                    'id'    => $s->id,
                    'title' => $s->getLocalizedTitle($locale),
                    'text'  => $s->getLocalizedText($locale),
                ]),
                'cards' => $cards->map(fn($c) => [
                    'id'    => $c->id,
                    'num'   => $c->num,
                    'title' => $c->getLocalizedTitle($locale),
                    'sub'   => $c->getLocalizedSub($locale),
                    'text'  => $c->getLocalizedText($locale),
                    'short' => $c->short,
                ]),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Settings
    // -------------------------------------------------------------------------

    /**
     * PATCH /api/services-section/settings
     */
    public function updateSettings(Request $request)
    {
        $fields = [
            'intro_prefix', 'intro_prefix_fr', 'intro_prefix_en',
            'intro_highlight', 'intro_highlight_fr', 'intro_highlight_en',
            'lead', 'lead_fr', 'lead_en',
            'approach_title', 'approach_title_fr', 'approach_title_en',
            'button_label', 'button_label_fr', 'button_label_en',
        ];

        $rules = [];
        foreach ($fields as $f) {
            $rules[$f] = 'sometimes|nullable|string';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $settings = ServicesSectionSetting::instance();
        $settings->update($request->only($fields));

        return response()->json(['success' => true, 'data' => $this->formatSettings($settings)]);
    }

    // -------------------------------------------------------------------------
    // Steps (approach nodes)
    // -------------------------------------------------------------------------

    public function storeStep(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'nullable|string|max:255',
            'title_fr' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'text'     => 'nullable|string',
            'text_fr'  => 'nullable|string',
            'text_en'  => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $maxOrder = ServiceStep::max('sort_order') ?? 0;
        $step = ServiceStep::create([
            'title'    => $request->title ?? ($request->title_fr ?? ''),
            'title_fr' => $request->title_fr,
            'title_en' => $request->title_en,
            'text'     => $request->text ?? ($request->text_fr ?? ''),
            'text_fr'  => $request->text_fr,
            'text_en'  => $request->text_en,
            'enabled'  => $request->boolean('enabled', true),
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json(['success' => true, 'data' => $this->formatStep($step)], 201);
    }

    public function updateStep(Request $request, $id)
    {
        $step = ServiceStep::find($id);
        if (!$step) {
            return response()->json(['success' => false, 'message' => 'Step not found'], 404);
        }

        $step->update($request->only([
            'title', 'title_fr', 'title_en',
            'text', 'text_fr', 'text_en',
            'enabled', 'sort_order',
        ]));

        return response()->json(['success' => true, 'data' => $this->formatStep($step)]);
    }

    public function toggleStep($id)
    {
        $step = ServiceStep::find($id);
        if (!$step) {
            return response()->json(['success' => false, 'message' => 'Step not found'], 404);
        }
        $step->update(['enabled' => !$step->enabled]);
        return response()->json(['success' => true, 'data' => $this->formatStep($step)]);
    }

    public function destroyStep($id)
    {
        $step = ServiceStep::find($id);
        if (!$step) {
            return response()->json(['success' => false, 'message' => 'Step not found'], 404);
        }
        $step->delete();
        return response()->json(['success' => true, 'message' => 'Step deleted']);
    }

    public function reorderSteps(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'step_ids'   => 'required|array',
            'step_ids.*' => 'required|integer|exists:service_steps,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        foreach ($request->step_ids as $i => $id) {
            ServiceStep::where('id', $id)->update(['sort_order' => $i + 1]);
        }

        $steps = ServiceStep::ordered()->get();
        return response()->json(['success' => true, 'data' => $steps->map(fn($s) => $this->formatStep($s))]);
    }

    // -------------------------------------------------------------------------
    // Cards (service rows)
    // -------------------------------------------------------------------------

    public function storeCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'num'      => 'nullable|string|max:10',
            'title'    => 'nullable|string|max:255',
            'title_fr' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'sub'      => 'nullable|string|max:255',
            'sub_fr'   => 'nullable|string|max:255',
            'sub_en'   => 'nullable|string|max:255',
            'text'     => 'nullable|string',
            'text_fr'  => 'nullable|string',
            'text_en'  => 'nullable|string',
            'short'    => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $maxOrder = ServiceCard::max('sort_order') ?? 0;
        $card = ServiceCard::create([
            'num'      => $request->num,
            'title'    => $request->title ?? ($request->title_fr ?? ''),
            'title_fr' => $request->title_fr,
            'title_en' => $request->title_en,
            'sub'      => $request->sub ?? ($request->sub_fr ?? ''),
            'sub_fr'   => $request->sub_fr,
            'sub_en'   => $request->sub_en,
            'text'     => $request->text ?? ($request->text_fr ?? ''),
            'text_fr'  => $request->text_fr,
            'text_en'  => $request->text_en,
            'short'    => $request->boolean('short', false),
            'enabled'  => $request->boolean('enabled', true),
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json(['success' => true, 'data' => $this->formatCard($card)], 201);
    }

    public function updateCard(Request $request, $id)
    {
        $card = ServiceCard::find($id);
        if (!$card) {
            return response()->json(['success' => false, 'message' => 'Card not found'], 404);
        }

        $card->update($request->only([
            'num',
            'title', 'title_fr', 'title_en',
            'sub', 'sub_fr', 'sub_en',
            'text', 'text_fr', 'text_en',
            'short', 'enabled', 'sort_order',
        ]));

        return response()->json(['success' => true, 'data' => $this->formatCard($card)]);
    }

    public function toggleCard($id)
    {
        $card = ServiceCard::find($id);
        if (!$card) {
            return response()->json(['success' => false, 'message' => 'Card not found'], 404);
        }
        $card->update(['enabled' => !$card->enabled]);
        return response()->json(['success' => true, 'data' => $this->formatCard($card)]);
    }

    public function destroyCard($id)
    {
        $card = ServiceCard::find($id);
        if (!$card) {
            return response()->json(['success' => false, 'message' => 'Card not found'], 404);
        }
        $card->delete();
        return response()->json(['success' => true, 'message' => 'Card deleted']);
    }

    public function reorderCards(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_ids'   => 'required|array',
            'card_ids.*' => 'required|integer|exists:service_cards,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        foreach ($request->card_ids as $i => $id) {
            ServiceCard::where('id', $id)->update(['sort_order' => $i + 1]);
        }

        $cards = ServiceCard::ordered()->get();
        return response()->json(['success' => true, 'data' => $cards->map(fn($c) => $this->formatCard($c))]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function formatSettings(ServicesSectionSetting $s): array
    {
        return [
            'intro_prefix'    => $s->intro_prefix,    'intro_prefix_fr'    => $s->intro_prefix_fr,    'intro_prefix_en'    => $s->intro_prefix_en,
            'intro_highlight' => $s->intro_highlight, 'intro_highlight_fr' => $s->intro_highlight_fr, 'intro_highlight_en' => $s->intro_highlight_en,
            'lead'            => $s->lead,            'lead_fr'            => $s->lead_fr,            'lead_en'            => $s->lead_en,
            'approach_title'  => $s->approach_title,  'approach_title_fr'  => $s->approach_title_fr,  'approach_title_en'  => $s->approach_title_en,
            'button_label'    => $s->button_label,    'button_label_fr'    => $s->button_label_fr,    'button_label_en'    => $s->button_label_en,
        ];
    }

    private function formatStep(ServiceStep $s): array
    {
        return [
            'id'         => $s->id,
            'title'      => $s->title,
            'title_fr'   => $s->title_fr,
            'title_en'   => $s->title_en,
            'text'       => $s->text,
            'text_fr'    => $s->text_fr,
            'text_en'    => $s->text_en,
            'enabled'    => $s->enabled,
            'sort_order' => $s->sort_order,
        ];
    }

    private function formatCard(ServiceCard $c): array
    {
        return [
            'id'         => $c->id,
            'num'        => $c->num,
            'title'      => $c->title,
            'title_fr'   => $c->title_fr,
            'title_en'   => $c->title_en,
            'sub'        => $c->sub,
            'sub_fr'     => $c->sub_fr,
            'sub_en'     => $c->sub_en,
            'text'       => $c->text,
            'text_fr'    => $c->text_fr,
            'text_en'    => $c->text_en,
            'short'      => $c->short,
            'enabled'    => $c->enabled,
            'sort_order' => $c->sort_order,
        ];
    }
}
