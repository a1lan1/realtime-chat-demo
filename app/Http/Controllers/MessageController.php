<?php

namespace App\Http\Controllers;

use App\Contracts\MessageServiceInterface;
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

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $message = $this->messageService->sendMessage(
            $request->validated('room_id'),
            $request->user()->id,
            $request->validated('content'),
        );

        return response()->json($message, 201);
    }
}
