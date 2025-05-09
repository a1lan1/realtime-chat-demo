<?php

namespace App\Contracts;

use App\Models\Message;
use Illuminate\Support\Collection;

interface MessageServiceInterface
{
    public function sendMessage(int $roomId, int $userId, string $content): Message;

    public function getLastMessages(int $roomId): Collection;
}
