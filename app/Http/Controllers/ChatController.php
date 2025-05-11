<?php

namespace App\Http\Controllers;

use App\Contracts\MessageServiceInterface;
use App\Contracts\RoomServiceInterface;
use App\Http\Requests\Chat\StoreMessageRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function __construct(
        private readonly RoomServiceInterface $roomService,
        private readonly MessageServiceInterface $messageService
    ) {}

    public function index(?int $roomId = null): Response
    {
        return Inertia::render('chat/ChatInertia', [
            'rooms' => $this->roomService->getRooms(),
            'room' => $roomId
                ? $this->roomService->getRoom($roomId)
                : null,
        ]);
    }

    public function store(StoreMessageRequest $request): RedirectResponse
    {
        $message = $this->messageService->sendMessage(
            $request->validated('room_id'),
            $request->user()->id,
            $request->validated('content'),
        );

        return back()->with('success', $message);
    }
}
