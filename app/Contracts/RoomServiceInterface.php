<?php

namespace App\Contracts;

use App\Models\Room;
use Illuminate\Support\Collection;

interface RoomServiceInterface
{
    public function getRooms(): Collection;

    public function getRoom(int $roomId): Room;

    public function createRoom(array $data): Room;
}
