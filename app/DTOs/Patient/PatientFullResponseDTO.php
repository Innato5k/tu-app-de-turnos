<?php

namespace App\DTOs\Patient;

use App\Models\Patient;

readonly class PatientFullResponseDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $last_name,
        public string $cuil,
        public string $email,
        public ?string $phone,
        public ?string $phone_opt,
        public ?string $observations,
        public ?string $birth_date,
        public ?string $gender,
        public ?string $address,
        public ?string $city,
        public ?string $province,
        public ?string $postal_code,
        public ?string $medical_coverage,
        public ?string $preferred_modality,
        public bool $is_active,
    ) {}

    public static function fromModel(Patient $patient): self
    {
        return new self(
            id: $patient->id,
            name: $patient->name,
            last_name: $patient->last_name,
            cuil: $patient->cuil,
            email: $patient->email,
            phone: $patient->phone,
            phone_opt: $patient->phone_opt,
            observations: $patient->observations,
            birth_date: $patient->birth_date,
            gender: $patient->gender,
            address: $patient->address,
            city: $patient->city,
            province: $patient->province,
            postal_code: $patient->postal_code,
            medical_coverage: $patient->medical_coverage,
            preferred_modality: $patient->preferred_modality,
            is_active: (bool) $patient->is_active,
        );
    }
}