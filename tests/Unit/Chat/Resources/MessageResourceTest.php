<?php

use App\Http\Resources\MessageResource;
use App\Http\Resources\RoomResource;
use App\Http\Resources\UserResource;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('MessageResource returns correct data when user and room relations are not loaded', function () {
    $message = Message::factory()->make([
        'id' => 1,
        'content' => 'Hello World',
        'user_id' => 2,
        'room_id' => 3,
        'created_at' => now()->subHour(),
        'updated_at' => null,
    ]);

    $resource = MessageResource::make($message);
    $request = Request::create('/');

    $expected = [
        'id' => 1,
        'content' => 'Hello World',
        'user_id' => 2,
        'room_id' => 3,
        'created_at' => $message->created_at->toISOString(),
        'updated_at' => null,
    ];

    expect($resource->toArray($request))->toMatchArray($expected);
});

test('MessageResource includes user and room when relations are loaded', function () {
    $user = User::factory()->make([
        'id' => 2,
        'created_at' => now()->subHour(),
    ]);
    $room = Room::factory()->make([
        'id' => 3,
        'created_at' => now()->subHour(),
    ]);
    $message = Message::factory()->make([
        'id' => 1,
        'content' => 'Hello World',
        'user_id' => 2,
        'room_id' => 3,
        'created_at' => now()->subHour(),
        'updated_at' => now(),
    ]);

    $message->setRelation('user', $user);
    $message->setRelation('room', $room);

    $resource = MessageResource::make($message);
    $request = Request::create('/');

    $expected = [
        'id' => 1,
        'content' => 'Hello World',
        'user_id' => 2,
        'room_id' => 3,
        'created_at' => $message->created_at->toISOString(),
        'updated_at' => $message->updated_at->toISOString(),
        'user' => UserResource::make($user),
        'room' => RoomResource::make($room),
    ];

    expect($resource->toArray($request))->toMatchArray($expected);
});
