<?php

namespace App\Exceptions;

use App\Library\OpenAIService\ChatResponse;
use RuntimeException;

/**
 * Thrown when the AI returns a response we can't build a quiz from —
 * most often a truncated payload that no longer parses as JSON.
 */
class QuizGenerationException extends RuntimeException
{
    public static function fromResponse(ChatResponse $response): self
    {
        $reason = $response->finishReason === 'length'
            ? 'the response was truncated before completing (token limit reached)'
            : "the response was not valid quiz JSON (finish reason: {$response->finishReason})";

        return new self("Quiz generation failed: {$reason}.");
    }
}
