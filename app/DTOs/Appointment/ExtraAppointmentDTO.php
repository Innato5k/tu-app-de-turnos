<?php

namespace App\DTOs\Appointment;

class ExtraAppointmentDTO
{
    public function __construct(
        public readonly string $start_time,
        public readonly int $patient_id,
        public readonly ?string $notes,
        public readonly ?float $cost,
        public readonly ?string $modality,
        public readonly int $duration
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            start_time: $request['start_time'] ?? null,
            patient_id: (int) $request->validated('patient_id'),
            notes: $request->validated('notes'),
            cost: $request->validated('cost') ? (float) $request->validated('cost') : null,
            modality: $request->validated('modality'),
            duration: (int) $request->validated('duration')
        );
    }
}