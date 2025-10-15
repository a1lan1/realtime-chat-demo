<?php

namespace App\Actions;

use App\DTO\MessageData;
use App\Events\NewMessage;
use App\Models\Message;
use App\Repositories\MessageRepository;

class SendMessageAction
{
    public function __construct(protected MessageRepository $messageRepository) {}

    public function execute(MessageData $data): Message
    {
        $message = $this->messageRepository->createMessage($data);

        $message->load('user');

        event(new NewMessage($message));

        return $message;
    }
}
