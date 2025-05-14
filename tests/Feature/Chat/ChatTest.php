<?php

use App\Models\Room;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get('/chat/inertia');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the chat', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chat/inertia');
    $response->assertStatus(200);
});

test('chat routes require authentication and verification', function () {
    $this->get('/chat/inertia')->assertRedirect('/login');
    $this->post('/chat/inertia/message')->assertRedirect('/login');
    $this->get('/chat/api')->assertRedirect('/login');
    $this->get('/chat/api/rooms')->assertRedirect('/login');
});

test('inertia chat routes', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();

    $this->actingAs($user)
        ->get('/chat/inertia')
        ->assertInertia(fn ($page) => $page
            ->component('chat/ChatInertia')
            ->has('rooms')
            ->has('room')
        );

    $this->actingAs($user)
        ->get("/chat/inertia/{$room->id}")
        ->assertInertia(fn ($page) => $page
            ->where('room.id', $room->id)
        );

    $this->actingAs($user)
        ->post('/chat/inertia/message', [
            'content' => 'Test message',
            'room_id' => $room->id,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
});

test('api chat routes', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();

    $this->actingAs($user)
        ->get('/chat/api')
        ->assertInertia(fn ($page) => $page->component('chat/ChatApi'));

    $this->actingAs($user)
        ->get('/chat/api/rooms')
        ->assertOk()
        ->assertJsonStructure([
            '*' => ['id', 'name', 'created_at'],
        ]);

    $this->actingAs($user)
        ->get("/chat/api/rooms/{$room->id}")
        ->assertOk()
        ->assertJsonFragment([
            'id' => $room->id,
            'name' => $room->name,
        ]);

    $this->actingAs($user)
        ->post('/chat/api/messages', [
            'content' => 'API test message',
            'room_id' => $room->id,
        ])
        ->assertCreated();
});

test('message validation', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();

    $this->actingAs($user)
        ->post('/chat/inertia/message', [
            'content' => '',
            'room_id' => $room->id,
        ])
        ->assertSessionHasErrors(['content']);

    $this->actingAs($user)
        ->post('/chat/api/messages', [
            'content' => 'Valid message',
            'room_id' => 9999,
        ])
        ->assertSessionHasErrors(['room_id']);
});

test('room validation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/chat/api/rooms', ['name' => 'New Room'])
        ->assertCreated();

    $this->actingAs($user)
        ->post('/chat/api/rooms', ['description' => 'Room description'])
        ->assertSessionHasErrors(['name']);
});
