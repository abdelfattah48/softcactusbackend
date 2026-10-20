<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CandidatureController extends Controller
{
    // -------------------------------------------------------------------------
    // Public — receive an application from the recruitment page
    // -------------------------------------------------------------------------

    /**
     * POST /api/candidatures (multipart, CV upload)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'poste'   => 'nullable|string|max:255',
            'nom'     => 'required|string|max:255',
            'prenom'  => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'tel'     => 'required|string|max:50',
            'message' => 'nullable|string',
            'cv'      => 'required|file|mimes:pdf,doc,docx|max:10240', // 10 Mo
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cvPath = null;
        $cvOriginalName = null;
        if ($request->hasFile('cv')) {
            $cvOriginalName = $request->file('cv')->getClientOriginalName();
            $cvPath = $request->file('cv')->store('candidatures', 'public');
        }

        $candidature = Candidature::create([
            'poste'            => $request->poste,
            'nom'              => $request->nom,
            'prenom'           => $request->prenom,
            'email'            => $request->email,
            'tel'              => $request->tel,
            'message'          => $request->message,
            'cv_path'          => $cvPath,
            'cv_original_name' => $cvOriginalName,
            'status'           => 'new',
        ]);

        Log::info('New candidature received', [
            'id'    => $candidature->id,
            'poste' => $candidature->poste,
            'email' => $candidature->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Candidature received',
            'data' => ['id' => $candidature->id],
        ], 201);
    }

    // -------------------------------------------------------------------------
    // Admin — inbox
    // -------------------------------------------------------------------------

    /**
     * GET /api/admin/candidatures?status=new&poste=...&q=...
     * Paginated list, newest first.
     */
    public function index(Request $request)
    {
        $query = Candidature::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('poste')) {
            $query->where('poste', $request->poste);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nom', 'like', "%{$q}%")
                    ->orWhere('prenom', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('tel', 'like', "%{$q}%");
            });
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $page = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $page->getCollection()->map(fn($c) => $this->format($c)),
            'meta' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/admin/candidatures/stats
     * Counts per status + per job offer (for the inbox header / dashboard).
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => Candidature::count(),
                'by_status' => Candidature::selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'by_poste' => Candidature::selectRaw('poste, COUNT(*) as count')
                    ->whereNotNull('poste')
                    ->groupBy('poste')
                    ->pluck('count', 'poste'),
            ],
        ]);
    }

    /**
     * GET /api/admin/candidatures/postes
     * Distinct job offers present in the inbox (for the filter dropdown).
     */
    public function postes()
    {
        $postes = Candidature::whereNotNull('poste')
            ->distinct()
            ->orderBy('poste')
            ->pluck('poste');

        return response()->json(['success' => true, 'data' => $postes]);
    }

    /**
     * GET /api/admin/candidatures/{id}
     */
    public function show($id)
    {
        $candidature = Candidature::find($id);
        if (!$candidature) {
            return response()->json(['success' => false, 'message' => 'Candidature not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $this->format($candidature)]);
    }

    /**
     * PATCH /api/admin/candidatures/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $candidature = Candidature::find($id);
        if (!$candidature) {
            return response()->json(['success' => false, 'message' => 'Candidature not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', Candidature::STATUSES),
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $candidature->update(['status' => $request->status]);
        return response()->json(['success' => true, 'data' => $this->format($candidature)]);
    }

    /**
     * DELETE /api/admin/candidatures/{id}
     */
    public function destroy($id)
    {
        $candidature = Candidature::find($id);
        if (!$candidature) {
            return response()->json(['success' => false, 'message' => 'Candidature not found'], 404);
        }
        $candidature->delete();
        return response()->json(['success' => true, 'message' => 'Candidature deleted']);
    }

    // -------------------------------------------------------------------------
    // Notifications — new (unread) applications feed the header bell
    // -------------------------------------------------------------------------

    /**
     * GET /api/admin/notifications
     * Unread candidatures (most recent first) + total unread count.
     */
    public function notifications()
    {
        $unread = Candidature::whereNull('read_at')
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => Candidature::whereNull('read_at')->count(),
                'items' => $unread->map(fn ($c) => [
                    'id'      => $c->id,
                    'nom'     => trim($c->prenom . ' ' . $c->nom),
                    'poste'   => $c->poste,
                    'message' => $c->message,
                    'read_at' => null,
                    'created_at' => $c->created_at?->toISOString(),
                ]),
            ],
        ]);
    }

    /**
     * PATCH /api/admin/candidatures/{id}/read
     * Mark a single candidature as read (dismiss its notification).
     */
    public function markRead($id)
    {
        $candidature = Candidature::find($id);
        if (!$candidature) {
            return response()->json(['success' => false, 'message' => 'Candidature not found'], 404);
        }
        if (!$candidature->read_at) {
            $candidature->update(['read_at' => now()]);
        }
        return response()->json(['success' => true, 'data' => $this->format($candidature)]);
    }

    /**
     * POST /api/admin/notifications/read-all
     * Mark every unread candidature as read.
     */
    public function markAllRead()
    {
        $updated = Candidature::whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true, 'data' => ['marked' => $updated]]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function format(Candidature $c): array
    {
        return [
            'id'               => $c->id,
            'poste'            => $c->poste,
            'nom'              => $c->nom,
            'prenom'           => $c->prenom,
            'email'            => $c->email,
            'tel'              => $c->tel,
            'message'          => $c->message,
            'cv_url'           => $this->fixStorageUrl($c->cvUrl()),
            'cv_original_name' => $c->cv_original_name,
            'status'           => $c->status,
            'read_at'          => $c->read_at?->toISOString(),
            'created_at'       => $c->created_at?->toISOString(),
        ];
    }

    private function fixStorageUrl(?string $url): ?string
    {
        if (!$url) return $url;
        // Normalize storage URLs to work with APP_URL (same as other controllers).
        $appUrl = rtrim(config('app.url'), '/');
        return preg_replace('#^https?://[^/]*/storage#', $appUrl . '/storage', $url);
    }
}
