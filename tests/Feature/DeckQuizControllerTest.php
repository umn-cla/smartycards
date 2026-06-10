<?php

use App\Library\OpenAIService\ChatResponse;
use App\Library\OpenAIService\OpenAIService;
use App\Models\Card;
use App\Models\Deck;
use App\Models\DeckMembership;
use App\Models\User;

use function Pest\Laravel\mock;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->deck = Deck::factory()->create(['name' => 'Test Deck']);
    DeckMembership::create([
        'deck_id' => $this->deck->id,
        'user_id' => $this->owner->id,
        'role' => 'owner',
    ]);
    Card::factory()->count(3)->create(['deck_id' => $this->deck->id]);
});

test('returns 422 when the AI response is truncated instead of 500ing', function () {
    mock(OpenAIService::class)
        ->shouldReceive('request')
        ->once()
        ->andReturn(new ChatResponse(
            // truncated mid-JSON, as happens when the token limit is hit
            content: '{"difficulty":"undergrad","questions":[{"sourceCardId":1,"prompt":"What',
            finishReason: 'length',
        ));

    $response = $this->actingAs($this->owner)
        ->postJson("/api/decks/{$this->deck->id}/quiz", [
            'cardSide' => 'front',
            'numberOfQuestions' => 10,
        ]);

    $response->assertUnprocessable();
});

test('returns a quiz when the AI response is complete', function () {
    $card = Card::factory()->create(['deck_id' => $this->deck->id]);

    mock(OpenAIService::class)
        ->shouldReceive('request')
        ->once()
        ->andReturn(new ChatResponse(
            content: json_encode([
                'difficulty' => 'undergrad',
                'questions' => [[
                    'sourceCardId' => $card->id,
                    'prompt' => 'What is 2 + 2?',
                    'choices' => ['3', '4', '5', '6'],
                    'correctChoiceIndex' => 1,
                ]],
            ]),
            finishReason: 'stop',
        ));

    $response = $this->actingAs($this->owner)
        ->postJson("/api/decks/{$this->deck->id}/quiz", [
            'cardSide' => 'front',
            'numberOfQuestions' => 10,
        ]);

    $response->assertSuccessful();
    $response->assertJsonStructure(['questions']);
});
