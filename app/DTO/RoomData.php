<?php

namespace App\DTO;

use Spatie\LaravelData\Attributes\Validation;
use Spatie\LaravelData\Data;

class RoomData extends Data
{
    public function __construct(
        #[Validation\Required, Validation\StringType, Validation\Max(255)]
        public string $name,

        #[Validation\Nullable, Validation\StringType, Validation\Max(1000)]
        public ?string $description,
    ) {}
}
