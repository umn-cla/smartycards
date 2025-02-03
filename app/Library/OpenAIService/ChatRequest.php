<?php

namespace App\Library\OpenAIService;

class ChatRequest
{
    private array $messages = [];

    private float $temperature = 0.7;

    private float $maxTokens = 800;

    private float $topP = 0.95;

    public function __construct(string $prompt, ?string $systemText = null)
    {
        if ($systemText) {
            $this->addMessage('system', $systemText);
        }

        $this->addMessage('user', $prompt);
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
            'response_format' => ['type' => 'json_object'],
        ];
    }

    public static function createPayload(string $prompt, ?string $systemText = null)
    {
        $chatRequest = new self($prompt, $systemText);

        return $chatRequest->toArray();
    }
}
