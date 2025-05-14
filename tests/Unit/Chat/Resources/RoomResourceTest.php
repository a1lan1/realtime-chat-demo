<?php

use App\Http\Resources\MessageResource;
use App\Http\Resources\RoomResource;
use App\Models\Message;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('RoomResource returns correct data when relations are not loaded', function () {
    $room = Room::factory()->make([
        'id' => 1,
        'name' => 'General Chat',
        'description' => 'Main room',
        'created_at' => now(),
        'updated_at' => null,
        'messages_count' => 10,
    ]);

    $resource = RoomResource::make($room);
    $request = Request::create('/');

    $expected = [
        'id' => 1,
        'name' => 'General Chat',
        'description' => 'Main room',
        'created_at' => $room->created_at->toISOString(),
        'updated_at' => null,
        'messages_count' => 10,
    ];

    expect($resource->toArray($request))->toMatchArray($expected);
});

test('RoomResource includes messages and last message when relations are loaded', function () {
    $room = Room::factory()->make([
        'id' => 1,
        'name' => 'General Chat',
        'description' => 'Main room',
        'created_at' => now(),
        'updated_at' => now(),
        'messages_count' => 2,
    ]);
    $messages = Message::factory(2)->make(['created_at' => now()]);
    $lastMessage = Message::factory()->make(['created_at' => now()]);

    $room->setRelation('messages', $messages);
    $room->setRelation('lastMessage', $lastMessage);

    $resource = RoomResource::make($room);
    $request = Request::create('/');

    $expected = [
        'id' => 1,
        'name' => 'General Chat',
        'description' => 'Main room',
        'created_at' => $room->created_at->toISOString(),
        'updated_at' => $room->updated_at->toISOString(),
        'messages_count' => 2,
        'messages' => MessageResource::collection($messages),
        'last_message' => MessageResource::make($lastMessage),
    ];

    expect($resource->toArray($request))->toMatchArray($expected);
});
