<?php

namespace App\DTOs\User;

use App\Http\Requests\User\UpdateUserRequest;

readonly class UserUpdateRequestDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $last_name = null,
        public ?string $cuil = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $phone_opt = null,
        public ?int $national_md_lic = null,
        public ?int $provincial_md_lic = null,
        public ?String $speciality = null,
        public ?bool $is_active = null,
        public ?string $birth_date = null,
        public ?string $gender = null,
        public ?string $role = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            last_name: $validated['last_name'],
            cuil: $validated['cuil'],
            email: $validated['email'],
            phone: $validated['phone'] ?? null,
            phone_opt: $validated['phone_opt'] ?? null,
            national_md_lic: $validated['national_md_lic'] ?? null,
            provincial_md_lic: $validated['provincial_md_lic'] ?? null,
            speciality: $validated['speciality'] ?? null,
            is_active: (bool) ($validated['is_active'] ?? true),
            birth_date: isset($validated['birth_date'])
                ? \Carbon\Carbon::parse($validated['birth_date'])->format('Y-m-d')
                : null,
            gender: $validated['gender'] ?? null,          
            role: $validated['role'] ?? null,  
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
