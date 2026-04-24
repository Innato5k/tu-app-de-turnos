<?php

namespace App\DTOs\Patient;

readonly class PatientUpdatePreferredRequestDTO
{
    public function __construct(
        
        public ?string $preferred_modality = null,
        public ?float $preferred_cost = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
           
            preferred_modality: $validated['preferred_modality'] ?? null,
            preferred_cost: $validated['preferred_cost'] ?? null,
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
