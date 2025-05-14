<?php

use App\Contracts\RoomRepositoryInterface;
use App\DTO\RoomData;
use App\Models\Message;
use App\Models\Room;
use App\Repositories\RoomRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('implements the RoomRepositoryInterface', function () {
    $repository = new RoomRepository;
    expect($repository)->toBeInstanceOf(RoomRepositoryInterface::class);
});

describe('getRoomsWithLastMessage', function () {
    it('returns a collection of rooms with their last message and message count', function () {
        $room1 = Room::factory()->create();
        $room2 = Room::factory()->create();
        Message::factory()->create(['room_id' => $room1->id]);
        Message::factory()->create(['room_id' => $room2->id]);
        $lastMessageRoom1 = Message::factory()->create(['room_id' => $room1->id]);
        $lastMessageRoom2 = Message::factory()->create(['room_id' => $room2->id]);

        $repository = new RoomRepository;
        $rooms = $repository->getRoomsWithLastMessage();

        expect($rooms)->toBeInstanceOf(EloquentCollection::class)
            ->toHaveCount(2)
            ->and($rooms->firstWhere('id', $room1->id)->lastMessage->id)->toBe($lastMessageRoom1->id)
            ->and($rooms->firstWhere('id', $room2->id)->lastMessage->id)->toBe($lastMessageRoom2->id)
            ->and($rooms->firstWhere('id', $room1->id)->messages_count)->toBe(2)
            ->and($rooms->firstWhere('id', $room2->id)->messages_count)->toBe(2);
    });

    it('returns an empty collection if there are no rooms', function () {
        $repository = new RoomRepository;
        $rooms = $repository->getRoomsWithLastMessage();

        expect($rooms)->toBeInstanceOf(EloquentCollection::class)->toBeEmpty();
    });
});

describe('getRoomWithMessages', function () {
    it('returns a room with its messages and associated users', function () {
        $room = Room::factory()->create();
        $message1 = Message::factory()->create(['room_id' => $room->id]);
        $message2 = Message::factory()->create(['room_id' => $room->id]);

        $repository = new RoomRepository;
        $foundRoom = $repository->getRoomWithMessages($room->id);

        expect($foundRoom)->toBeInstanceOf(Room::class)
            ->id->toBe($room->id)
            ->messages->toBeInstanceOf(EloquentCollection::class)
            ->toHaveCount(2)
            ->and($foundRoom->messages->pluck('id'))->toContain($message1->id, $message2->id)
            ->and($foundRoom->messages->pluck('user_id'))->toContain($message1->user_id, $message2->user_id);
    });

    it('throws a ModelNotFoundException if the room does not exist', function () {
        $repository = new RoomRepository;

        $this->expectException(ModelNotFoundException::class);

        $repository->getRoomWithMessages(999);
    });
});

describe('createRoom', function () {
    it('creates a new room in the database', function () {
        $roomData = new RoomData(
            name: 'Test Room',
            description: null
        );

        $repository = new RoomRepository;
        $room = $repository->createRoom($roomData);

        expect($room)->toBeInstanceOf(Room::class)
            ->name->toBe('Test Room')
            ->id->toBeGreaterThan(0);

        $this->assertDatabaseHas('rooms', ['name' => 'Test Room']);
    });

    it('creates a room with all provided data', function () {
        $roomData = new RoomData(
            name: 'Another Room',
            description: 'A detailed description'
        );

        $repository = new RoomRepository;
        $room = $repository->createRoom($roomData);

        expect($room)->toBeInstanceOf(Room::class)
            ->name->toBe('Another Room')
            ->description->toBe('A detailed description')
            ->id->toBeGreaterThan(0);

        $this->assertDatabaseHas('rooms', ['name' => 'Another Room', 'description' => 'A detailed description']);
    });
});
