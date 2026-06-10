<?php

namespace App\Library\OpenAIService;

/**
 * A single chat completion result.
 *
 * `finishReason` lets callers tell a complete response ("stop") apart from a
 * truncated one ("length") before trusting the content.
 */
final readonly class ChatResponse
{
    public function __construct(
        public string $content,
        public string $finishReason,
    ) {}
}
