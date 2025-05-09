<?php

namespace App\Contracts;

use App\DTO\RoomData;
use App\Models\Room;
use Illuminate\Support\Collection;

interface RoomRepositoryInterface
{
    public function getRoomsWithLastMessage(): Collection;

    public function getRoomWithMessages(int $roomId): Room;

    public function createRoom(RoomData $roomData): Room;
}
