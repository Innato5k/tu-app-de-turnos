<?php

namespace App\DTOs\Patient;

use App\Models\Patient;

readonly class PatientResponseDTO
{
    public function __construct(
        public int $id,
        public string $name,  
        public string $email,
        public ?string $phone,        
        public bool $is_active,
    ) {}

    public static function fromModel(Patient $patient): self
    {
        return new self(
            id: $patient->id,
            name: "{$patient->last_name}, {$patient->name}",
            email: $patient->email,
            phone: $patient->phone,
            is_active: (bool) $patient->is_active,
        );
    }
}