<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('UserResource returns correct data', function () {
    $user = User::factory()->make([
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $resource = new UserResource($user);
    $request = Request::create('/');

    $expected = [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ];

    expect($resource->toArray($request))->toMatchArray($expected);
});
