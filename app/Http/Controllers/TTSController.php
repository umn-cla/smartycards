<?php

namespace App\Http\Controllers;

use App\Library\TTSService;
use Illuminate\Http\Request;

class TTSController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, TTSService $tts)
    {

        $validated = $request->validate([
            'text' => 'required|string|max:200',
            'lang' => 'string|nullable',
        ]);

        $audioBlob = $tts->getSpeech($validated['text'], $validated['lang'] ?? null);

        return response($audioBlob, 200, ['Content-Type' => 'audio/mpeg']);
    }
}
