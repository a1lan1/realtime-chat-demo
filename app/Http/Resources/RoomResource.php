<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'messages_count' => $this->messages_count,
            'messages' => $this->whenLoaded('messages', fn () => MessageResource::collection($this->messages)),
            'last_message' => $this->whenLoaded('lastMessage', fn () => MessageResource::make($this->lastMessage)),
        ];
    }
}
