<?php

namespace App\DTOs\Appointment;

class AppointmentUpdateDTO
{
    public function __construct(
        public readonly string $status,
        public readonly string $payment_status,
        public readonly ?string $notes,
        public readonly ?float $cost,
        public readonly string $modality
    ) {}

    public static function fromRequest(Array $request): self
    {
        return new self(
            status: $request['status'],
            payment_status: $request['payment_status'],
            modality: $request['modality'],
            cost: (int) $request['cost'],
            notes: $request['notes'] ?? null,
        );
    }
}