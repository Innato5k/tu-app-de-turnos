<?php

namespace App\DTOs\Patient;

readonly class PatientRequestDTO
{
    public function __construct(
        public string $name,
        public string $lastName,
        public string $cuil,
        public string $email,
        public ?string $phone = null,
        public ?string $phoneOpt = null,
        public ?string $observations = null,
        public ?string $birthDate = null,
        public ?string $gender = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $province = null,
        public ?string $postalCode = null,
        public ?string $medicalCoverage = null,
        public ?string $preferredModality = null,
        public bool $isActive = true,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            lastName: $validated['last_name'],
            cuil: $validated['cuil'],
            email: $validated['email'],
            phone: $validated['phone'] ?? null,
            phoneOpt: $validated['phone_opt'] ?? null,
            observations: $validated['observations'] ?? null,
            birthDate: isset($validated['birth_date'])
                ? \Carbon\Carbon::parse($validated['birth_date'])->format('Y-m-d')
                : null,
            gender: $validated['gender'] ?? null,
            address: $validated['address'] ?? null,
            city: $validated['city'] ?? null,
            province: $validated['province'] ?? null,
            postalCode: $validated['postal_code'] ?? null,
            medicalCoverage: $validated['medical_coverage'] ?? null,
            preferredModality: $validated['preferred_modality'] ?? null,
            isActive: (bool) ($validated['is_active'] ?? true),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
