<?php

namespace App\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TTSService
{
    private $key;

    private $endpoint;

    private $voice;

    const MAX_TEXT_LENGTH = 5000;

    public function __construct()
    {
        $this->key = config('services.azure.tts.key');
        $this->endpoint = config('services.azure.tts.endpoint');
        $this->voice = config('services.azure.tts.voice');
    }

    protected function toSSML(string $text, string $lang = 'auto'): string
    {

        // if a language is specified, wrap text in a lang tag
        if ($lang !== 'auto') {
            $text = "<lang xml:lang='{$lang}'>{$text}</lang>";
        }

        return "<speak version='1.0' xml:lang='en-US'>"
            ."<voice name='{$this->voice}' xml:lang='{$lang}'>"
            .$text
            .'</voice>'
            .'</speak>';
    }

    protected function getCacheKey(string $text, string $lang = 'auto'): string
    {
        return md5($text.$lang.$this->voice);
    }

    public function getSpeech(string $text, string $lang = 'auto')
    {

        if (strlen($text) > self::MAX_TEXT_LENGTH) {
            throw new \Exception('SSML is too long');
        }

        $cacheKey = $this->getCacheKey($text, $lang);
        $cachePath = "tts/{$cacheKey}.mp3";

        if (Storage::exists($cachePath)) {
            return Storage::get($cachePath);
        }

        $ssml = $this->toSSML($text, $lang);

        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->key,
            'Content-Type' => 'application/ssml+xml',
            'X-Microsoft-OutputFormat' => 'audio-16khz-128kbitrate-mono-mp3',
        ])
            ->baseUrl($this->endpoint)
            ->withBody($ssml, 'application/ssml+xml')
            ->post('/cognitiveservices/v1', $ssml);

        $audioContent = $response->body();

        Storage::put($cachePath, $audioContent);

        return $audioContent;
    }

    public function getVoices()
    {
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->key,
        ])
            ->baseUrl($this->endpoint)
            ->get('/cognitiveservices/voices/list');

        return $response->json();
    }
}
