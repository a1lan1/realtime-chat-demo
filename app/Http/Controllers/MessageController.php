<?php

namespace App\Http\Controllers;

use App\Actions\SendMessageAction;
use App\Contracts\MessageServiceInterface;
use App\DTO\MessageData;
use App\Http\Requests\Chat\StoreMessageRequest;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function __construct(private readonly MessageServiceInterface $messageService) {}

    public function index(int $roomId): JsonResponse
    {
        return response()->json(
            $this->messageService->getLastMessages($roomId)
        );
    }

    public function store(StoreMessageRequest $request, SendMessageAction $action): JsonResponse
    {
        $message = $action->execute(
            new MessageData(
                room_id: $request->validated('room_id'),
                user_id: $request->user()->id,
                content: $request->validated('content'),
            )
        );

        return response()->json($message, 201);
    }
}
