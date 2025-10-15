<?php

namespace App\Http\Controllers;

use App\Actions\SendMessageAction;
use App\Contracts\MessageServiceInterface;
use App\Contracts\RoomServiceInterface;
use App\DTO\MessageData;
use App\Http\Requests\Chat\StoreMessageRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function __construct(private readonly RoomServiceInterface $roomService) {}

    public function index(?int $roomId = null): Response
    {
        return Inertia::render('chat/ChatInertia', [
            'rooms' => $this->roomService->getRooms(),
            'room' => $roomId
                ? $this->roomService->getRoom($roomId)
                : null,
        ]);
    }

    public function store(StoreMessageRequest $request, SendMessageAction $action): RedirectResponse
    {
        $message = $action->execute(
            new MessageData(
                room_id: $request->validated('room_id'),
                user_id: $request->user()->id,
                content: $request->validated('content'),
            )
        );

        return back()->with('success', $message);
    }
}
