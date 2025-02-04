<?php

namespace App\Library\OpenAIService;

class ChatRequest
{
    private array $messages = [];

    private float $temperature = 0.7;

    private float $maxTokens = 800;

    private float $topP = 0.95;

    private array $responseSchema = [];

    public function __construct(string $prompt, string $systemText, array $responseSchema)
    {
        if ($systemText) {
            $this->addMessage('system', $systemText);
        }

        $this->addMessage('user', $prompt);

        $this->responseSchema = $responseSchema;
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

    public static function createPayload(string $prompt, string $systemText, $responseSchema): array
    {
        $chatRequest = new self(prompt: $prompt, systemText: $systemText, responseSchema: $responseSchema);

        return $chatRequest->toArray();
    }
}
