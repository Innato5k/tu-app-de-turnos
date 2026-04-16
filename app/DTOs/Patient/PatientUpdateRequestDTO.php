<?php

namespace App\DTOs\Patient;

readonly class PatientUpdateRequestDTO
{
    public function __construct(
        public string $name,
        public string $last_name,
        public string $cuil,
        public string $email,
        public ?string $phone = null,
        public ?string $phone_opt = null,
        public ?string $observations = null,
        public ?string $birth_date = null,
        public ?string $gender = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $province = null,
        public ?string $postal_code = null,
        public ?string $medical_coverage = null,
        public ?string $preferred_modality = null,
        public ?bool $is_active = null,
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
            observations: $validated['observations'] ?? null,
            birth_date: isset($validated['birth_date'])
                ? \Carbon\Carbon::parse($validated['birth_date'])->format('Y-m-d')
                : null,
            gender: $validated['gender'] ?? null,
            address: $validated['address'] ?? null,
            city: $validated['city'] ?? null,
            province: $validated['province'] ?? null,
            postal_code: $validated['postal_code'] ?? null,
            medical_coverage: $validated['medical_coverage'] ?? null,
            preferred_modality: $validated['preferred_modality'] ?? null,
            is_active: (bool) ($validated['is_active'] ?? true),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
