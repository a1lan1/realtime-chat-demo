<?php

use App\Contracts\MessageRepositoryInterface;
use App\DTO\MessageData;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use App\Repositories\MessageRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('implements the MessageRepositoryInterface', function () {
    $repository = new MessageRepository;
    expect($repository)->toBeInstanceOf(MessageRepositoryInterface::class);
});

describe('getLastMessages', function () {
    it('returns the last messages for a given room with a default limit', function () {
        $room = Room::factory()->create();
        Message::factory()->count(60)->create(['room_id' => $room->id]);

        $repository = new MessageRepository;
        $messages = $repository->getLastMessages($room->id);

        expect($messages)->toBeInstanceOf(EloquentCollection::class)
            ->toHaveCount(50)
            ->every(fn ($message) => $message->room_id === $room->id);
    });

    it('returns the specified number of last messages for a given room', function () {
        $room = Room::factory()->create();
        Message::factory()->count(30)->create(['room_id' => $room->id]);

        $limit = 15;
        $repository = new MessageRepository;
        $messages = $repository->getLastMessages($room->id, $limit);

        expect($messages)->toBeInstanceOf(EloquentCollection::class)
            ->toHaveCount($limit)
            ->every(fn ($message) => $message->room_id === $room->id);
    });

    it('returns an empty collection if there are no messages for the room', function () {
        $room = Room::factory()->create();
        $repository = new MessageRepository;
        $messages = $repository->getLastMessages($room->id);

        expect($messages)->toBeInstanceOf(EloquentCollection::class)->toBeEmpty();
    });
});

describe('getMessagesFrom', function () {
    it('returns messages for a given room created on or after a specific date', function () {
        $room = Room::factory()->create();
        $user = User::factory()->create();

        // Messages before the specified date
        Message::factory()->create(['room_id' => $room->id, 'user_id' => $user->id, 'created_at' => Carbon::yesterday()]);
        Message::factory()->create(['room_id' => $room->id, 'user_id' => $user->id, 'created_at' => Carbon::yesterday()->subHours(2)]);

        // Messages after or on a specified date
        $fromDate = Carbon::now()->subDays(2)->toDateTimeString();
        $message1 = Message::factory()->create(['room_id' => $room->id, 'user_id' => $user->id, 'created_at' => Carbon::now()->subDays(1)]);
        $message2 = Message::factory()->create(['room_id' => $room->id, 'user_id' => $user->id, 'created_at' => Carbon::now()]);

        $repository = new MessageRepository;
        $messages = $repository->getMessagesFrom($room->id, $fromDate);

        expect($messages)->toBeInstanceOf(EloquentCollection::class)
            ->toHaveCount(4)
            ->and($messages->every(fn ($message) => $message->room_id === $room->id))
            ->and($messages->every(fn ($message) => Carbon::parse($message->created_at)->gte(Carbon::parse($fromDate))))
            ->and($messages->every(fn ($message) => $message->relationLoaded('user')))
            ->and($messages->sortByDesc('created_at')->values()->toArray())->toEqual($messages->sortByDesc('created_at')->values()->toArray())
            ->and($messages->pluck('id'))->toContain($message1->id, $message2->id);
    });

    it('returns an empty collection if there are no messages for the room after the given date', function () {
        $room = Room::factory()->create();
        $fromDate = Carbon::now()->toDateTimeString();
        $repository = new MessageRepository;
        $messages = $repository->getMessagesFrom($room->id, $fromDate);

        expect($messages)->toBeInstanceOf(EloquentCollection::class)->toBeEmpty();
    });
});

describe('createMessage', function () {
    it('creates a new message in the database', function () {
        $room = Room::factory()->create();
        $user = User::factory()->create();

        $messageData = new MessageData(
            room_id: $room->id,
            user_id: $user->id,
            content: 'Hello World'
        );
        $repository = new MessageRepository;
        $message = $repository->createMessage($messageData);

        expect($message)->toBeInstanceOf(Message::class)
            ->room_id->toBe($room->id)
            ->user_id->toBe($user->id)
            ->content->toBe('Hello World')
            ->id->toBeGreaterThan(0);

        $this->assertDatabaseHas('messages', ['room_id' => $room->id, 'user_id' => $user->id, 'content' => 'Hello World']);
    });
});
