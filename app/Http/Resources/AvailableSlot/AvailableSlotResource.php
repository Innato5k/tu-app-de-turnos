<?php

namespace App\Http\Resources\AvailableSlot;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableSlotResource extends JsonResource
{
    public function toArray($request): array
    {
        $title = 'Disponible';
        if ($this->appointment && $this->appointment->patient) {
            $title = $this->appointment->patient->name . ' ' . $this->appointment->patient->last_name;
        }

        return [
            'id'    => $this->id,
            'title' => $title,
            'start' => $this->start_time->toIso8601String(),
            'end'   => $this->end_time->toIso8601String(),
            'backgroundColor' => $this->status === 'booked' ? '#f87171' : '#34d399',
            'extendedProps' => [
                'status'         => $this->status,
                'appointment_id' => $this->appointment?->id,
                'patient_id'     => $this->appointment?->patient_id,
            ],
        ];
    }
}
