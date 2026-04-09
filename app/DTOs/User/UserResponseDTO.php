<?php

namespace App\DTOs\User;

use App\Models\User;

readonly class UserResponseDTO
{
    public function __construct(
        public int $id,
        public string $name,  
        public string $email,
        public ?string $phone,
        public bool $is_active,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: "{$user->last_name}, {$user->name}",
            email: $user->email,
            phone: $user->phone,
            is_active: (bool) $user->is_active,
        );
    }
}