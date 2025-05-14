<?php

use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->room = Room::factory()->create();

    $this->actingAs($this->user);

    Cache::flush();
});

it('stores a new message', function () {
    $response = $this->postJson(route('messages.store'), [
        'content' => 'Hello world',
        'room_id' => $this->room->id,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('messages', [
        'content' => 'Hello world',
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
    ]);
});

it('returns last messages with caching', function () {
    $messages = Message::factory()
        ->count(10)
        ->for($this->room)
        ->create(['user_id' => $this->user->id]);

    $response1 = $this->getJson(route('messages.index', $this->room));

    $response1->assertOk()
        ->assertJsonCount(10)
        ->assertJsonStructure([
            '*' => ['id', 'content', 'created_at']
        ]);

    $cached = Cache::get("room:{$this->room->id}:messages");
    expect($cached)->toHaveCount(10)
        ->and($cached->first()->id)->toBe($messages->first()->id);

    Message::query()->delete();

    $response2 = $this->getJson(route('messages.index', $this->room));
    $response2->assertOk()
        ->assertJsonCount(10);
});

it('returns empty array when no messages', function () {
    $response = $this->getJson(route('messages.index', $this->room));
    $response->assertOk()
        ->assertJsonCount(0);
});

it('uses correct cache key', function () {
    Message::factory()
        ->for($this->room)
        ->create(['user_id' => $this->user->id]);

    $this->getJson(route('messages.index', $this->room));

    expect(Cache::has("room:{$this->room->id}:messages"))->toBeTrue();
});

it('respects cache ttl', function () {
    Message::factory()
        ->for($this->room)
        ->create(['user_id' => $this->user->id]);

    $this->travelTo(now()->subMinutes(59));
    $this->getJson(route('messages.index', $this->room))
        ->assertJsonCount(1);

    $this->travelBack();
    $this->getJson(route('messages.index', $this->room))
        ->assertJsonCount(1);

    $this->travelTo(now()->addHours(2));
    $this->getJson(route('messages.index', $this->room))
        ->assertJsonCount(1);

    Cache::forget("room:{$this->room->id}:messages");

    $this->getJson(route('messages.index', $this->room))
        ->assertJsonCount(1);
});
