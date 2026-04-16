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
        public ?string $birth_date,
        public ?string $gender,
    ) {}

    public static function fromRequest(array $request): self
    {        
        return new self(
            name: $request['name'],
            last_name: $request['last_name'],
            cuil: $request['cuil'],
            email: $request['email'],
            phone: $request['phone'] ?? null,
            phone_opt: $request['phone_opt'] ?? null,
            national_md_lic: $request['national_md_lic'] ?? null,
            provincial_md_lic: $request['provincial_md_lic'] ?? null,
            speciality: $request['speciality'] ?? null,
            birth_date: isset($request['birth_date'])
                ? \Carbon\Carbon::parse($request['birth_date'])->format('Y-m-d')
                : null,
            gender: $request['gender'] ?? null,
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
