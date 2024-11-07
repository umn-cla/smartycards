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
            'text' => 'required|string|max:1000',
            'lang' => 'string',
        ]);

        $lang = $validated['lang'] ?? 'en-US';

        $audioBlob = $tts->getSpeech($validated['text'], $lang);

        return response($audioBlob, 200, ['Content-Type' => 'audio/mpeg']);
    }
}
