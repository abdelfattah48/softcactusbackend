<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecruitmentSetting;
use App\Models\RecruitmentJobOffer;
use App\Models\RecruitmentTalent;
use App\Models\RecruitmentWhyCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RecruitmentController extends Controller
{
    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    /**
     * GET /api/recruitment
     * Full payload for the backoffice (all languages, all rows incl. disabled).
     */
    public function index()
    {
        $settings = RecruitmentSetting::instance();
        $jobs     = RecruitmentJobOffer::ordered()->get();
        $talents  = RecruitmentTalent::ordered()->get();
        $whys     = RecruitmentWhyCard::ordered()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'settings' => $this->formatSettings($settings),
                'jobs'     => $jobs->map(fn($j) => $this->formatJob($j)),
                'talents'  => $talents->map(fn($t) => $this->formatTalent($t)),
                'whys'     => $whys->map(fn($w) => $this->formatWhy($w)),
            ],
        ]);
    }

    /**
     * GET /api/recruitment/public?locale=fr
     * Localized payload for the website (enabled rows only).
     */
    public function publicData(Request $request)
    {
        $locale   = $request->get('locale', 'fr');
        $settings = RecruitmentSetting::instance();
        $jobs     = RecruitmentJobOffer::enabled()->ordered()->get();
        $talents  = RecruitmentTalent::enabled()->ordered()->get();
        $whys     = RecruitmentWhyCard::enabled()->ordered()->get();

        $pick = function (array $vals) use ($locale) {
            if ($locale === 'en') {
                return $vals['en'] ?: ($vals['fr'] ?: ($vals['base'] ?: ''));
            }
            return $vals['fr'] ?: ($vals['base'] ?: ($vals['en'] ?: ''));
        };

        // Settings fields, handled generically from the fillable list
        // (each base field has a _fr / _en variant).
        $baseFields = [
            'hero_title', 'hero_paragraph', 'hero_lead', 'hero_cta',
            'jobs_title_first', 'jobs_title_highlight',
            'talents_title_first', 'talents_title_highlight',
            'talents_box_title', 'talents_box_text', 'talents_side_text',
            'why_title_first', 'why_title_highlight',
            'cta_title', 'cta_text', 'cta_button',
        ];
        $data = [];
        foreach ($baseFields as $f) {
            $data[$f] = $pick([
                'base' => $settings->{$f},
                'fr'   => $settings->{$f . '_fr'},
                'en'   => $settings->{$f . '_en'},
            ]);
        }

        $data['jobs'] = $jobs->map(fn($j) => [
            'id'          => $j->id,
            'num'         => $j->num,
            'title'       => $j->getLocalizedTitle($locale),
            'description' => $j->getLocalizedDescription($locale),
        ]);

        $data['talents'] = $talents->map(fn($t) => [
            'id'        => $t->id,
            'first_name' => $t->first_name,
            'last_name' => $t->last_name,
            'role'      => $t->getLocalizedRole($locale),
            'image'     => $this->fixStorageUrl($t->image),
        ]);

        $data['whys'] = $whys->map(fn($w) => [
            'id'    => $w->id,
            'num'   => $w->num,
            'title' => $w->getLocalizedTitle($locale),
            'text'  => $w->getLocalizedText($locale),
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    // -------------------------------------------------------------------------
    // Settings
    // -------------------------------------------------------------------------

    /**
     * PATCH /api/recruitment/settings
     */
    public function updateSettings(Request $request)
    {
        $fields = RecruitmentSetting::firstOrCreate(['id' => 1])->getFillable();

        $rules = [];
        foreach ($fields as $f) {
            $rules[$f] = 'sometimes|nullable|string';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $settings = RecruitmentSetting::instance();
        $settings->update($request->only($fields));

        return response()->json(['success' => true, 'data' => $this->formatSettings($settings)]);
    }

    // -------------------------------------------------------------------------
    // Job offers ("Nos opportunités")
    // -------------------------------------------------------------------------

    public function storeJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'num'            => 'nullable|string|max:10',
            'title'          => 'nullable|string|max:255',
            'title_fr'       => 'nullable|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $maxOrder = RecruitmentJobOffer::max('sort_order') ?? 0;
        $job = RecruitmentJobOffer::create([
            'num'            => $request->num,
            'title'          => $request->title ?? ($request->title_fr ?? ''),
            'title_fr'       => $request->title_fr,
            'title_en'       => $request->title_en,
            'description'    => $request->description ?? ($request->description_fr ?? ''),
            'description_fr' => $request->description_fr,
            'description_en' => $request->description_en,
            'enabled'        => $request->boolean('enabled', true),
            'sort_order'     => $maxOrder + 1,
        ]);

        return response()->json(['success' => true, 'data' => $this->formatJob($job)], 201);
    }

    public function updateJob(Request $request, $id)
    {
        $job = RecruitmentJobOffer::find($id);
        if (!$job) {
            return response()->json(['success' => false, 'message' => 'Job offer not found'], 404);
        }

        $job->update($request->only([
            'num',
            'title', 'title_fr', 'title_en',
            'description', 'description_fr', 'description_en',
            'enabled', 'sort_order',
        ]));

        return response()->json(['success' => true, 'data' => $this->formatJob($job)]);
    }

    public function toggleJob($id)
    {
        $job = RecruitmentJobOffer::find($id);
        if (!$job) {
            return response()->json(['success' => false, 'message' => 'Job offer not found'], 404);
        }
        $job->update(['enabled' => !$job->enabled]);
        return response()->json(['success' => true, 'data' => $this->formatJob($job)]);
    }

    public function destroyJob($id)
    {
        $job = RecruitmentJobOffer::find($id);
        if (!$job) {
            return response()->json(['success' => false, 'message' => 'Job offer not found'], 404);
        }
        $job->delete();
        return response()->json(['success' => true, 'message' => 'Job offer deleted']);
    }

    public function reorderJobs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_ids'   => 'required|array',
            'job_ids.*' => 'required|integer|exists:recruitment_job_offers,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        foreach ($request->job_ids as $i => $id) {
            RecruitmentJobOffer::where('id', $id)->update(['sort_order' => $i + 1]);
        }

        $jobs = RecruitmentJobOffer::ordered()->get();
        return response()->json(['success' => true, 'data' => $jobs->map(fn($j) => $this->formatJob($j))]);
    }

    // -------------------------------------------------------------------------
    // Talents (stagiaires carousel)
    // -------------------------------------------------------------------------

    public function storeTalent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'role'       => 'nullable|string|max:255',
            'role_fr'    => 'nullable|string|max:255',
            'role_en'    => 'nullable|string|max:255',
            'image'      => 'nullable|string',   // URL, path, or base64 data URI
            'image_file' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:5120',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $maxOrder = RecruitmentTalent::max('sort_order') ?? 0;
        $talent = RecruitmentTalent::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'role'       => $request->role ?? ($request->role_fr ?? ''),
            'role_fr'    => $request->role_fr,
            'role_en'    => $request->role_en,
            'image'      => $this->resolveTalentImage($request),
            'enabled'    => $request->boolean('enabled', true),
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json(['success' => true, 'data' => $this->formatTalent($talent)], 201);
    }

    public function updateTalent(Request $request, $id)
    {
        $talent = RecruitmentTalent::find($id);
        if (!$talent) {
            return response()->json(['success' => false, 'message' => 'Talent not found'], 404);
        }

        $talent->update($request->only([
            'first_name', 'last_name',
            'role', 'role_fr', 'role_en',
            'enabled', 'sort_order',
        ]));

        // An uploaded file (multipart) or a base64 data URI overrides the image.
        if ($request->hasFile('image_file')) {
            $talent->update(['image' => $this->storeTalentImage($request->file('image_file'))]);
        } elseif ($request->has('image')) {
            $talent->update(['image' => $this->saveBase64Image($request->image)]);
        }

        return response()->json(['success' => true, 'data' => $this->formatTalent($talent)]);
    }

    public function uploadTalentImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg,png,webp,svg|max:5120',
        ]);

        return response()->json([
            'success' => true,
            'url' => $this->fixStorageUrl($this->storeTalentImage($request->file('file'))),
        ]);
    }

    public function toggleTalent($id)
    {
        $talent = RecruitmentTalent::find($id);
        if (!$talent) {
            return response()->json(['success' => false, 'message' => 'Talent not found'], 404);
        }
        $talent->update(['enabled' => !$talent->enabled]);
        return response()->json(['success' => true, 'data' => $this->formatTalent($talent)]);
    }

    public function destroyTalent($id)
    {
        $talent = RecruitmentTalent::find($id);
        if (!$talent) {
            return response()->json(['success' => false, 'message' => 'Talent not found'], 404);
        }
        $talent->delete();
        return response()->json(['success' => true, 'message' => 'Talent deleted']);
    }

    public function reorderTalents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'talent_ids'   => 'required|array',
            'talent_ids.*' => 'required|integer|exists:recruitment_talents,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        foreach ($request->talent_ids as $i => $id) {
            RecruitmentTalent::where('id', $id)->update(['sort_order' => $i + 1]);
        }

        $talents = RecruitmentTalent::ordered()->get();
        return response()->json(['success' => true, 'data' => $talents->map(fn($t) => $this->formatTalent($t))]);
    }

    // -------------------------------------------------------------------------
    // Why cards ("Pourquoi nous rejoindre")
    // -------------------------------------------------------------------------

    public function storeWhy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'num'      => 'nullable|string|max:10',
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

        $maxOrder = RecruitmentWhyCard::max('sort_order') ?? 0;
        $why = RecruitmentWhyCard::create([
            'num'      => $request->num,
            'title'    => $request->title ?? ($request->title_fr ?? ''),
            'title_fr' => $request->title_fr,
            'title_en' => $request->title_en,
            'text'     => $request->text ?? ($request->text_fr ?? ''),
            'text_fr'  => $request->text_fr,
            'text_en'  => $request->text_en,
            'enabled'  => $request->boolean('enabled', true),
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json(['success' => true, 'data' => $this->formatWhy($why)], 201);
    }

    public function updateWhy(Request $request, $id)
    {
        $why = RecruitmentWhyCard::find($id);
        if (!$why) {
            return response()->json(['success' => false, 'message' => 'Why card not found'], 404);
        }

        $why->update($request->only([
            'num',
            'title', 'title_fr', 'title_en',
            'text', 'text_fr', 'text_en',
            'enabled', 'sort_order',
        ]));

        return response()->json(['success' => true, 'data' => $this->formatWhy($why)]);
    }

    public function toggleWhy($id)
    {
        $why = RecruitmentWhyCard::find($id);
        if (!$why) {
            return response()->json(['success' => false, 'message' => 'Why card not found'], 404);
        }
        $why->update(['enabled' => !$why->enabled]);
        return response()->json(['success' => true, 'data' => $this->formatWhy($why)]);
    }

    public function destroyWhy($id)
    {
        $why = RecruitmentWhyCard::find($id);
        if (!$why) {
            return response()->json(['success' => false, 'message' => 'Why card not found'], 404);
        }
        $why->delete();
        return response()->json(['success' => true, 'message' => 'Why card deleted']);
    }

    public function reorderWhys(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'why_ids'   => 'required|array',
            'why_ids.*' => 'required|integer|exists:recruitment_why_cards,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        foreach ($request->why_ids as $i => $id) {
            RecruitmentWhyCard::where('id', $id)->update(['sort_order' => $i + 1]);
        }

        $whys = RecruitmentWhyCard::ordered()->get();
        return response()->json(['success' => true, 'data' => $whys->map(fn($w) => $this->formatWhy($w))]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function resolveTalentImage(Request $request): ?string
    {
        if ($request->hasFile('image_file')) {
            return $this->storeTalentImage($request->file('image_file'));
        }
        return $this->saveBase64Image($request->image);
    }

    /**
     * When the admin sends a data URI (base64 image), store it and return the
     * public URL. Plain URL/path strings are kept as-is.
     */
    private function saveBase64Image(?string $value): ?string
    {
        if (!$value || !preg_match('/^data:(image\/[\w+-]+);base64,/', $value, $matches)) {
            return $value;
        }

        $fileData = base64_decode(preg_replace('/^data:image\/[\w+-]+;base64,/', '', $value));
        $mimeTypes = [
            'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/png' => 'png',
            'image/gif' => 'gif', 'image/webp' => 'webp', 'image/svg+xml' => 'svg',
        ];
        $extension = $mimeTypes[$matches[1]] ?? 'png';

        $path = 'recruitment/' . Str::random(40) . '.' . $extension;
        Storage::disk('public')->put($path, $fileData);

        return Storage::disk('public')->url($path);
    }

    private function storeTalentImage($file): string
    {
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('recruitment', $filename, 'public');
        return Storage::disk('public')->url($path);
    }

    private function fixStorageUrl(?string $url): ?string
    {
        if (!$url) return $url;
        // Normalize storage URLs to work with APP_URL (same as other controllers).
        $appUrl = rtrim(config('app.url'), '/');
        return preg_replace('#^https?://[^/]*/storage#', $appUrl . '/storage', $url);
    }

    private function formatSettings(RecruitmentSetting $s): array
    {
        return $s->only($s->getFillable());
    }

    private function formatJob(RecruitmentJobOffer $j): array
    {
        return [
            'id'            => $j->id,
            'num'           => $j->num,
            'title'         => $j->title,
            'title_fr'      => $j->title_fr,
            'title_en'      => $j->title_en,
            'description'   => $j->description,
            'description_fr' => $j->description_fr,
            'description_en' => $j->description_en,
            'enabled'       => $j->enabled,
            'sort_order'    => $j->sort_order,
        ];
    }

    private function formatTalent(RecruitmentTalent $t): array
    {
        return [
            'id'         => $t->id,
            'first_name' => $t->first_name,
            'last_name'  => $t->last_name,
            'role'       => $t->role,
            'role_fr'    => $t->role_fr,
            'role_en'    => $t->role_en,
            'image'      => $this->fixStorageUrl($t->image),
            'enabled'    => $t->enabled,
            'sort_order' => $t->sort_order,
        ];
    }

    private function formatWhy(RecruitmentWhyCard $w): array
    {
        return [
            'id'         => $w->id,
            'num'        => $w->num,
            'title'      => $w->title,
            'title_fr'   => $w->title_fr,
            'title_en'   => $w->title_en,
            'text'       => $w->text,
            'text_fr'    => $w->text_fr,
            'text_en'    => $w->text_en,
            'enabled'    => $w->enabled,
            'sort_order' => $w->sort_order,
        ];
    }
}
