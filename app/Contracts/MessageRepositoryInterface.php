<?php

namespace App\Contracts;

use App\DTO\MessageData;
use App\Models\Message;
use Illuminate\Support\Collection;

interface MessageRepositoryInterface
{
    public function getLastMessages(int $roomId, int $limit = 100): Collection;

    public function createMessage(MessageData $messageData): Message;

    public function getMessagesFrom(int $roomId, string $fromDate): Collection;
}
