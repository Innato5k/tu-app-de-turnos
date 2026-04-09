<?php

namespace App\DTOs\User;

use App\Models\User;

readonly class UserFullResponseDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $last_name,
        public string $cuil,
        public string $email,
        public ?string $phone,
        public ?string $phone_opt,
        public int $national_md_lic,
        public int $provincial_md_lic,
        public String $speciality,
        public bool $is_active,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            last_name: $user->last_name,
            cuil: $user->cuil,
            email: $user->email,
            phone: $user->phone,
            phone_opt: $user->phone_opt,
            national_md_lic: $user->national_md_lic,
            provincial_md_lic: $user->provincial_md_lic,
            speciality: $user->speciality,
            is_active: (bool) $user->is_active,
        );
    }
}