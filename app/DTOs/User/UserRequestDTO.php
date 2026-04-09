<?php

namespace App\DTOs\User;


use App\Http\Requests\User\StoreUserRequest;

 readonly class UserRequestDTO
{
    public function __construct(
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

    public static function fromRequest(StoreUserRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            last_name: $request->validated('last_name'),
            cuil: $request->validated('cuil'),
            email: $request->validated('email'),
            phone: $request->validated('phone') ?? null,
            phone_opt: $request->validated('phone_opt') ?? null,
            national_md_lic: $request->validated('national_md_lic') ?? null,
            provincial_md_lic: $request->validated('provincial_md_lic') ?? null,
            speciality: $request->validated('speciality') ?? null,
            is_active: (bool) ($request->validated('is_active') ?? true),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
