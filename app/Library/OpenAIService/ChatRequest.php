<?php

namespace App\Library\OpenAIService;

class ChatRequest
{
    private array $messages = [];

    private float $temperature = 0.7;

    private int $maxTokens;

    private float $topP = 0.95;

    private array $responseSchema = [];

    public function __construct(string $prompt, string $systemText, array $responseSchema, int $maxTokens = 800)
    {
        if ($systemText) {
            $this->addMessage('system', $systemText);
        }

        $this->addMessage('user', $prompt);

        $this->responseSchema = $responseSchema;
        $this->maxTokens = $maxTokens;
    }

    public function addMessage(string $role, string $content): void
    {
        $this->messages[] = [
            'role' => $role,
            'content' => [
                [
                    'type' => 'text',
                    'text' => $content,
                ],
            ],
        ];
    }

    public function toArray()
    {
        return [
            'messages' => $this->messages,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'top_p' => $this->topP,
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => $this->responseSchema,
            ],
        ];
    }

    public static function createPayload(string $prompt, string $systemText, array $responseSchema, int $maxTokens = 800): array
    {
        $chatRequest = new self(
            prompt: $prompt,
            systemText: $systemText,
            responseSchema: $responseSchema,
            maxTokens: $maxTokens,
        );

        return $chatRequest->toArray();
    }
}
