<?php

namespace App\Services;

use App\Contracts\RoomRepositoryInterface;
use App\Contracts\RoomServiceInterface;
use App\DTO\RoomData;
use App\Models\Room;
use Illuminate\Support\Collection;

readonly class RoomService implements RoomServiceInterface
{
    public function __construct(private RoomRepositoryInterface $roomRepository) {}

    public function getRooms(): Collection
    {
        return $this->roomRepository->getRoomsWithLastMessage();
    }

    public function getRoom(int $roomId): Room
    {
        return $this->roomRepository->getRoomWithMessages($roomId);
    }

    public function createRoom(array $data): Room
    {
        return $this->roomRepository->createRoom(
            RoomData::from($data)
        );
    }
}
