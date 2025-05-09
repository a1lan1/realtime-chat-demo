<?php

namespace App\Repositories;

use App\Contracts\RoomRepositoryInterface;
use App\DTO\RoomData;
use App\Models\Room;
use Illuminate\Support\Collection;

class RoomRepository implements RoomRepositoryInterface
{
    public function getRoomsWithLastMessage(): Collection
    {
        return Room::with(['lastMessage'])
            ->withCount(['messages'])
            ->get();
    }

    public function getRoomWithMessages(int $roomId): Room
    {
        return Room::with('messages.user')
            ->findOrFail($roomId);
    }

    public function createRoom(RoomData $roomData): Room
    {
        return Room::create(
            $roomData->toArray()
        );
    }
}
