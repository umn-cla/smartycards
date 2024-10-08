<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadFileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,image/bmp,image/gif,image/svg+xml,image/webp,audio/mpeg,audio/ogg,audio/vorbis,audio/mp4,audio/aac,audio/midi,audio/m4a,audio/x-m4a',
            ],
        ]);

        $file = $validated['file'];
        $fileName = $file->store('files', 'public');

        return response()->json([
            'name' => $fileName,
            'url' => secure_asset(Storage::url($fileName)),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }
}
