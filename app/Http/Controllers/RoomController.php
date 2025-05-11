<?php

namespace App\Http\Controllers;

use App\Contracts\RoomServiceInterface;
use App\Http\Resources\RoomResource;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    public function __construct(private readonly RoomServiceInterface $roomService) {}

    public function index(): JsonResponse
    {
        return response()->json(
            RoomResource::collection(
                $this->roomService->getRooms()
            )
        );
    }

    public function show(int $roomId): JsonResponse
    {
        return response()->json(
            RoomResource::make(
                $this->roomService->getRoom($roomId)
            )
        );
    }
}
