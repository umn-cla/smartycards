<?php

use App\Models\Card;
use App\Models\Deck;
use App\Models\DeckMembership;
use App\Models\User;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->deck = Deck::factory()->create(['name' => 'Test Deck']);
    DeckMembership::create([
        'deck_id' => $this->deck->id,
        'user_id' => $this->owner->id,
        'role' => 'owner',
    ]);
});

test('deck owner can view audit history', function () {
    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history");

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data',
        'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        'links' => ['prev', 'next'],
    ]);
});

test('non-owner cannot view audit history', function () {
    $otherUser = User::factory()->create();

    $response = $this->actingAs($otherUser)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history");

    $response->assertForbidden();
});

test('unauthenticated user cannot view audit history', function () {
    $response = $this->getJson("/api/decks/{$this->deck->id}/reports/audit-history");

    $response->assertUnauthorized();
});

test('validates object parameter must be Deck or Card', function () {
    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?object=InvalidType");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['object']);
});

test('validates action parameter must be a valid event type', function () {
    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?action=invalid");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['action']);
});

test('validates id parameter must be a positive integer', function () {
    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?id=-1");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['id']);
});

test('validates from parameter must be a valid date', function () {
    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?from=not-a-date");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['from']);
});

test('validates to parameter must be after or equal to from', function () {
    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?from=2025-01-15&to=2025-01-10");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['to']);
});

test('validates sort parameter must be a valid field', function () {
    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?sort=invalid_field");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['sort']);
});

test('validates direction parameter must be asc or desc', function () {
    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?direction=invalid");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['direction']);
});

test('accepts valid filter parameters', function () {
    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?".http_build_query([
            'object' => 'Card',
            'action' => 'created',
            'id' => 1,
            'user' => 'test',
            'from' => '2025-01-01',
            'to' => '2025-12-31',
            'sort' => 'created_at',
            'direction' => 'desc',
        ]));

    $response->assertSuccessful();
});

test('filters by object type', function () {
    Card::factory()->for($this->deck)->create();

    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?object=Card");

    $response->assertSuccessful();
    $data = $response->json('data');

    foreach ($data as $audit) {
        expect($audit['auditable_type'])->toBe('Card');
    }
});

test('filters by action type', function () {
    Card::factory()->for($this->deck)->create();

    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?action=created");

    $response->assertSuccessful();
    $data = $response->json('data');

    foreach ($data as $audit) {
        expect($audit['event'])->toBe('created');
    }
});

test('filters by auditable id', function () {
    $card = Card::factory()->for($this->deck)->create();

    $response = $this->actingAs($this->owner)
        ->getJson("/api/decks/{$this->deck->id}/reports/audit-history?id={$card->id}&object=Card");

    $response->assertSuccessful();
    $data = $response->json('data');

    foreach ($data as $audit) {
        expect($audit['auditable_id'])->toBe($card->id);
    }
});
