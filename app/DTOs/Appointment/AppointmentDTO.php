<?php

namespace App\Dtos\Appointment;

class AppointmentDTO
{
    public function __construct(
        public readonly int $slotId,
        public readonly int $patientId,
        public readonly ?string $notes,
        public readonly ?float $cost,
        public readonly ?string $modality
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            slotId: (int) $request->validated('available_slot_id'),
            patientId: (int) $request->validated('patient_id'),
            notes: $request->validated('notes'),
            cost: $request->validated('cost') ? (float) $request->validated('cost') : null,
            modality: $request->validated('modality')
        );
    }
}