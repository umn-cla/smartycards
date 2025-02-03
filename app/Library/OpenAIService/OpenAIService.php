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
            $resourceName = config('services.azure.openai.resource_name');
            $deploymentId = config('services.azure.openai.deployment_id');

            $this->client = OpenAI::factory()
                ->withBaseUri("https://{$resourceName}.openai.azure.com/openai/deployments/{$deploymentId}")
                ->withHttpHeader('api-key', config('services.azure.openai.api_key'))
                ->withQueryParam('api-version', config('services.azure.openai.api_version'))
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
    public function request(string $prompt, ?string $systemText, array $responseSchema): string
    {
        $systemText = $systemText ?? $this->systemText;
        $payload = ChatRequest::createPayload(
            prompt: $prompt,
            systemText: $systemText,
            responseSchema: $responseSchema
        );

        try {
            $response = $this->client->chat()->create($payload);

            return $response->choices[0]->message->content;
        } catch (Exception $e) {
            // Handle the exception or log it
            throw new Exception('Failed to communicate with OpenAI: '.$e->getMessage());
        }
    }
}
