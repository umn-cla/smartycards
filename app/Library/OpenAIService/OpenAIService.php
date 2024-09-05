<?php

namespace App\Library\OpenAIService;

use Exception;
use OpenAI;
use OpenAI\Client as OpenAIClient;

class OpenAIService
{
    private OpenAIClient $client;

    private string $systemText = '';

    public function __construct(?OpenAIClient $client = null)
    {
        if ($client) {
            $this->client = $client;
        } else {
            $baseUri = config('openai.resource_name')
                .'.openai.azure.com/openai/deployments/'
                .config('openai.deployment_id');

            $this->client = OpenAI::factory()
                ->withBaseUri($baseUri)
                ->withHttpHeader('api-key', config('openai.api_key'))
                ->withQueryParam('api-version', config('openai.api_version'))
                ->make();
        }
    }

    public function setSystemText(string $systemText): void
    {
        $this->systemText = $systemText;
    }

    /**
     * Sends a chat request to the OpenAI service.
     */
    public function request(string $prompt, ?string $systemText = null): string
    {
        $systemText = $systemText ?? $this->systemText;
        $payload = ChatRequest::createPayload($prompt, $systemText);

        try {
            $response = $this->client->chat()->create($payload);

            return $response->choices[0]->message->content;
        } catch (Exception $e) {
            // Handle the exception or log it
            throw new Exception('Failed to communicate with OpenAI: '.$e->getMessage());
        }
    }
}
