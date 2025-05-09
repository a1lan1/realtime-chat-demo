<?php

namespace App\Services;

use App\Contracts\MessageRepositoryInterface;
use App\Contracts\MessageServiceInterface;
use App\DTO\MessageData;
use App\Events\NewMessage;
use App\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

readonly class MessageService implements MessageServiceInterface
{
    public function __construct(private MessageRepositoryInterface $messageRepository) {}

    public function getLastMessages(int $roomId): Collection
    {
        return Cache::remember("room:{$roomId}:messages", 3600, function () use ($roomId) {
            return $this->messageRepository->getLastMessages($roomId);
        });
    }

    public function sendMessage(int $roomId, int $userId, string $content): Message
    {
        $message = $this->messageRepository->createMessage(
            new MessageData(
                room_id: $roomId,
                user_id: $userId,
                content: $content
            )
        );

        event(new NewMessage($message));

        return $message;
    }
}
