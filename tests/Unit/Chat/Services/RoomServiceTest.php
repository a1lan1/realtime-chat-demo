<?php

use App\Contracts\RoomRepositoryInterface;
use App\Contracts\RoomServiceInterface;
use App\DTO\RoomData;
use App\Models\Room;
use App\Services\RoomService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('RoomService', function () {
    it('implements the RoomServiceInterface', function () {
        $roomRepositoryMock = mock(RoomRepositoryInterface::class);
        $roomService = new RoomService($roomRepositoryMock);

        expect($roomService)->toBeInstanceOf(RoomServiceInterface::class);
    });

    describe('getRooms', function () {
        it('calls the getRoomsWithLastMessage method of the repository and returns the result', function () {
            $roomCollection = Room::factory(3)->make();
            $roomRepositoryMock = mock(RoomRepositoryInterface::class);

            $roomRepositoryMock->shouldReceive('getRoomsWithLastMessage')
                ->once()
                ->andReturn($roomCollection);

            $roomService = new RoomService($roomRepositoryMock);
            $rooms = $roomService->getRooms();

            expect($rooms)->toBe($roomCollection);
        });
    });

    describe('getRoom', function () {
        it('calls the getRoomWithMessages method of the repository with the given id and returns the result', function () {
            $room = Room::factory()->create();
            $roomRepositoryMock = mock(RoomRepositoryInterface::class);

            $roomRepositoryMock->shouldReceive('getRoomWithMessages')
                ->once()
                ->with($room->id)
                ->andReturn($room);

            $roomService = new RoomService($roomRepositoryMock);
            $foundRoom = $roomService->getRoom($room->id);

            expect($foundRoom)->toBe($room);
        });
    });

    describe('createRoom', function () {
        it('calls the createRoom method of the repository with a RoomData object created from the given array and returns the result', function () {
            $roomDataArray = ['name' => 'Test Room', 'description' => 'A description'];
            $room = Room::factory()->make();
            $roomRepositoryMock = mock(RoomRepositoryInterface::class);

            $roomRepositoryMock->shouldReceive('createRoom')
                ->once()
                ->with(\Mockery::on(function (RoomData $data) use ($roomDataArray) {
                    return $data->name === $roomDataArray['name']
                        && $data->description === $roomDataArray['description'];
                }))
                ->andReturn($room);

            $service = new RoomService($roomRepositoryMock);
            $createdRoom = $service->createRoom($roomDataArray);

            expect($createdRoom)->toBe($room);
        });

        it('calls the createRoom method of the repository with a RoomData object even if description is null', function () {
            $roomDataArray = ['name' => 'Another Room', 'description' => null];
            $room = Room::factory()->make();
            $roomRepositoryMock = mock(RoomRepositoryInterface::class);

            $roomRepositoryMock->shouldReceive('createRoom')
                ->once()
                ->with(\Mockery::on(function (RoomData $data) use ($roomDataArray) {
                    return $data->name === $roomDataArray['name']
                        && $data->description === $roomDataArray['description'];
                }))
                ->andReturn($room);

            $roomService = new RoomService($roomRepositoryMock);
            $createdRoom = $roomService->createRoom($roomDataArray);

            expect($createdRoom)->toBe($room);
        });

        it('creates a RoomData object with the correct data types and values', function () {
            $roomDataArray = ['name' => 'Valid Room Name', 'description' => 'Optional description'];
            $roomRepositoryMock = mock(RoomRepositoryInterface::class);

            $roomRepositoryMock->shouldReceive('createRoom')
                ->once()
                ->with(\Mockery::on(function (RoomData $data) use ($roomDataArray) {
                    return $data->name === $roomDataArray['name'] &&
                        $data->description === $roomDataArray['description'] &&
                        is_string($data->name) &&
                        (is_string($data->description) || is_null($data->description));
                }))
                ->andReturn(Room::factory()->make());

            $roomService = new RoomService($roomRepositoryMock);
            $roomService->createRoom($roomDataArray);

            expect(true)->toBeTrue(); // Assertion inside with()
        });
    });
});
