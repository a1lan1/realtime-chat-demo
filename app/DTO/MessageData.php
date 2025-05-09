<?php

namespace App\DTO;

use Spatie\LaravelData\Attributes\Validation;
use Spatie\LaravelData\Data;

class MessageData extends Data
{
    public function __construct(
        #[Validation\Exists('rooms', 'id')]
        public int $room_id,

        #[Validation\Exists('users', 'id')]
        public int $user_id,

        #[Validation\Required, Validation\StringType, Validation\Max(1000)]
        public string $content,
    ) {}
}
