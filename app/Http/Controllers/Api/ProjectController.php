<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Project::orderBy('created_at', 'desc')->get()
        ]);
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'status' => 'required|string',
            'public_card' => 'nullable|array',
            'details' => 'nullable|array',
        ]);

        $data['public_card'] = $this->processBase64Media($data['public_card'] ?? []);
        $data['details'] = $this->processBase64Media($data['details'] ?? []);

        $project = Project::create($data);

        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $data = $request->validate([
            'status' => 'nullable|string',
            'public_card' => 'nullable|array',
            'details' => 'nullable|array',
        ]);

        if (isset($data['public_card'])) {
            $data['public_card'] = $this->processBase64Media($data['public_card']);
        }
        
        if (isset($data['details'])) {
            $data['details'] = $this->processBase64Media($data['details']);
        }

        $project->update($data);

        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json([
            'success' => true
        ]);
    }

    private function processBase64Media($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => &$value) {
                if (is_array($value)) {
                    $value = $this->processBase64Media($value);
                } elseif (is_string($value) && preg_match('/^data:(\w+\/[\w+-]+);base64,/', $value, $matches)) {
                    $value = $this->saveBase64File($value, $matches[1]);
                }
            }
        }
        return $data;
    }

    private function saveBase64File($base64String, $mimeType)
    {
        // Remove the data URI scheme prefix
        $base64String = preg_replace('/^data:\w+\/[\w+-]+;base64,/', '', $base64String);
        $fileData = base64_decode($base64String);

        // Map mime type to extension
        $extension = 'bin';
        $mimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
        ];

        if (isset($mimeTypes[$mimeType])) {
            $extension = $mimeTypes[$mimeType];
        } else {
            // fallback
            $parts = explode('/', $mimeType);
            if (count($parts) === 2) {
                $extension = $parts[1];
            }
        }

        $filename = Str::random(40) . '.' . $extension;
        $path = 'projects/' . $filename;
        
        Storage::disk('public')->put($path, $fileData);

        return Storage::disk('public')->url($path);
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // up to 100MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('projects', $filename, 'public');
            return response()->json([
                'success' => true,
                'url' => Storage::disk('public')->url($path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }
}
