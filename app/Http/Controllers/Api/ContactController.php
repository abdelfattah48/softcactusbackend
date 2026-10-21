<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\ContactUniverseImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    // -------------------------------------------------------------------------
    // Public — receive a contact message from the website form
    // -------------------------------------------------------------------------

    /**
     * POST /api/contact-messages
     * The "fill-in-the-blanks" sentence fields from the Contact page.
     */
    public function storeMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'message' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Rebuild the full sentence server-side so the inbox shows the same
        // text the visitor wrote, even if the client didn't send one.
        $message = $request->message ?: sprintf(
            'Bonjour, je m’appelle %s et je représente la société %s. Je vous contacte pour voir avec vous la possibilité de collaborer sur un projet de %s. Vous pouvez me contacter sur %s ou %s.',
            $request->name, $request->company, $request->project, $request->email, $request->phone
        );

        $contact = ContactMessage::create([
            'name'    => $request->name,
            'company' => $request->company,
            'project' => $request->project,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'message' => $message,
        ]);

        Log::info('New contact message received', [
            'id'    => $contact->id,
            'email' => $contact->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact message received',
            'data' => ['id' => $contact->id],
        ], 201);
    }

    // -------------------------------------------------------------------------
    // Public — "L'univers de notre agence" gallery
    // -------------------------------------------------------------------------

    /**
     * GET /api/contact/universe
     * Enabled gallery images (ordered) for the website. An empty list means the
     * website keeps its bundled default gallery.
     */
    public function universePublic()
    {
        $images = ContactUniverseImage::enabled()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $images->map(fn($i) => [
                'id'    => $i->id,
                'image' => $this->fixStorageUrl($i->image),
            ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // Admin — messages inbox
    // -------------------------------------------------------------------------

    /**
     * GET /api/admin/contact/messages?q=...&unread=1
     * Paginated list, newest first.
     */
    public function messages(Request $request)
    {
        $query = ContactMessage::query()->latest();

        if ($request->boolean('unread')) {
            $query->unread();
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orWhere('project', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $page = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $page->getCollection()->map(fn($m) => $this->formatMessage($m)),
            'meta' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/admin/contact/messages/stats
     */
    public function messagesStats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total'  => ContactMessage::count(),
                'unread' => ContactMessage::unread()->count(),
            ],
        ]);
    }

    /**
     * GET /api/admin/contact/messages/unread
     * Unread messages feed the header notification bell (same shape as
     * the candidatures notifications endpoint).
     */
    public function unreadNotifications()
    {
        $unread = ContactMessage::unread()->latest()->limit(20)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => ContactMessage::unread()->count(),
                'items' => $unread->map(fn ($m) => [
                    'id'         => $m->id,
                    'nom'        => $m->name,
                    'project'    => $m->project,
                    'created_at' => $m->created_at?->toISOString(),
                ]),
            ],
        ]);
    }

    /**
     * GET /api/admin/contact/messages/{id}
     */
    public function showMessage($id)
    {
        $message = ContactMessage::find($id);
        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Contact message not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $this->formatMessage($message)]);
    }

    /**
     * PATCH /api/admin/contact/messages/{id}/read
     */
    public function markMessageRead($id)
    {
        $message = ContactMessage::find($id);
        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Contact message not found'], 404);
        }
        if (!$message->read_at) {
            $message->update(['read_at' => now()]);
        }
        return response()->json(['success' => true, 'data' => $this->formatMessage($message)]);
    }

    /**
     * POST /api/admin/contact/messages/read-all
     */
    public function markAllMessagesRead()
    {
        $updated = ContactMessage::unread()->update(['read_at' => now()]);
        return response()->json(['success' => true, 'data' => ['marked' => $updated]]);
    }

    /**
     * DELETE /api/admin/contact/messages/{id}
     */
    public function destroyMessage($id)
    {
        $message = ContactMessage::find($id);
        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Contact message not found'], 404);
        }
        $message->delete();
        return response()->json(['success' => true, 'message' => 'Contact message deleted']);
    }

    // -------------------------------------------------------------------------
    // Admin — universe gallery CRUD
    // -------------------------------------------------------------------------

    /**
     * GET /api/admin/contact/universe
     * All rows (incl. disabled) for the backoffice editor.
     */
    public function universeIndex()
    {
        $images = ContactUniverseImage::ordered()->get();
        return response()->json([
            'success' => true,
            'data' => $images->map(fn($i) => $this->formatImage($i)),
        ]);
    }

    /**
     * POST /api/admin/contact/universe
     * Accepts an uploaded file (image_file) or a base64 data URI (image),
     * same convention as the recruitment talents.
     */
    public function universeStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'      => 'nullable|string',   // URL, path, or base64 data URI
            'image_file' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $maxOrder = ContactUniverseImage::max('sort_order') ?? 0;
        $image = ContactUniverseImage::create([
            'image'      => $this->resolveImage($request),
            'enabled'    => $request->boolean('enabled', true),
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json(['success' => true, 'data' => $this->formatImage($image)], 201);
    }

    /**
     * PUT /api/admin/contact/universe/{id}
     */
    public function universeUpdate(Request $request, $id)
    {
        $image = ContactUniverseImage::find($id);
        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Universe image not found'], 404);
        }

        $image->update($request->only(['enabled', 'sort_order']));

        // An uploaded file (multipart) or a base64 data URI overrides the image.
        if ($request->hasFile('image_file')) {
            $image->update(['image' => $this->storeFile($request->file('image_file'))]);
        } elseif ($request->has('image')) {
            $image->update(['image' => $this->saveBase64Image($request->image)]);
        }

        return response()->json(['success' => true, 'data' => $this->formatImage($image)]);
    }

    /**
     * PATCH /api/admin/contact/universe/{id}/toggle
     */
    public function universeToggle($id)
    {
        $image = ContactUniverseImage::find($id);
        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Universe image not found'], 404);
        }
        $image->update(['enabled' => !$image->enabled]);
        return response()->json(['success' => true, 'data' => $this->formatImage($image)]);
    }

    /**
     * DELETE /api/admin/contact/universe/{id}
     * Also removes the uploaded file from storage.
     */
    public function universeDestroy($id)
    {
        $image = ContactUniverseImage::find($id);
        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Universe image not found'], 404);
        }

        if ($image->image && str_contains($image->image, '/storage/')) {
            $relative = Str::after($image->image, '/storage/');
            Storage::disk('public')->delete($relative);
        }
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Universe image deleted']);
    }

    /**
     * PATCH /api/admin/contact/universe/reorder
     */
    public function universeReorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image_ids'   => 'required|array',
            'image_ids.*' => 'required|integer|exists:contact_universe_images,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        foreach ($request->image_ids as $i => $id) {
            ContactUniverseImage::where('id', $id)->update(['sort_order' => $i + 1]);
        }

        $images = ContactUniverseImage::ordered()->get();
        return response()->json(['success' => true, 'data' => $images->map(fn($i) => $this->formatImage($i))]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function resolveImage(Request $request): ?string
    {
        if ($request->hasFile('image_file')) {
            return $this->storeFile($request->file('image_file'));
        }
        return $this->saveBase64Image($request->image);
    }

    private function storeFile($file): string
    {
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('contact', $filename, 'public');
        return Storage::disk('public')->url($path);
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
            'image/webp' => 'webp',
        ];
        $extension = $mimeTypes[$matches[1]] ?? 'png';

        $path = 'contact/' . Str::random(40) . '.' . $extension;
        Storage::disk('public')->put($path, $fileData);

        return Storage::disk('public')->url($path);
    }

    private function fixStorageUrl(?string $url): ?string
    {
        if (!$url) return $url;
        // Normalize storage URLs to work with APP_URL (same as other controllers).
        $appUrl = rtrim(config('app.url'), '/');
        return preg_replace('#^https?://[^/]*/storage#', $appUrl . '/storage', $url);
    }

    private function formatMessage(ContactMessage $m): array
    {
        return [
            'id'         => $m->id,
            'name'       => $m->name,
            'company'    => $m->company,
            'project'    => $m->project,
            'email'      => $m->email,
            'phone'      => $m->phone,
            'message'    => $m->message,
            'read_at'    => $m->read_at?->toISOString(),
            'created_at' => $m->created_at?->toISOString(),
        ];
    }

    private function formatImage(ContactUniverseImage $i): array
    {
        return [
            'id'         => $i->id,
            'image'      => $this->fixStorageUrl($i->image),
            'enabled'    => $i->enabled,
            'sort_order' => $i->sort_order,
        ];
    }
}
