<?php

namespace App\Repositories;

use App\Contracts\MessageRepositoryInterface;
use App\DTO\MessageData;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    public function getLastMessages(int $roomId, int $limit = 50): Collection
    {
        return Message::where(['room_id' => $roomId])
            ->limit($limit)
            ->get();
    }

    public function getMessagesFrom(int $roomId, string $fromDate): Collection
    {
        return Message::query()
            ->with(['user' => fn ($q) => $q->select('id', 'name')])
            ->where('room_id', $roomId)
            ->where('created_at', '>=', Carbon::parse($fromDate))
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get(['id', 'room_id', 'user_id', 'content', 'created_at']);
    }

    public function createMessage(MessageData $messageData): Message
    {
        return Message::create(
            $messageData->toArray()
        );
    }
}
