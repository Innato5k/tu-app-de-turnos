<?php

namespace App\Http\Resources\AvailableSlot;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableSlotResource extends JsonResource
{
    public function toArray($request): array
    {
        // ¿Es un Turno o es un Slot?
        $isAppointment = $this->resource instanceof \App\Models\Appointment;

        return [
            'id'    => $this->id,
            // Si es turno, nombre del paciente. Si es slot, su estado (Disponible/Bloqueado)
            'title' => $isAppointment
                ? ($this->patient?->last_name.' '. $this->patient?->name ?? 'Turno Ocupado')
                : ($this->status === 'available' ? 'Disponible' : 'Bloqueado'),

            'start' => $this->start_time->toIso8601String(),
            'end'   => $this->end_time->toIso8601String(),

            'backgroundColor' => $isAppointment ? '#28a745' : ($this->status === 'available' ? '#007bff' : '#6c757d'),

            'extendedProps' => [
                'is_appointment' => $isAppointment,
                'status' => $isAppointment ? 'booked' : $this->status,
                'appointment_id' => $isAppointment ? $this->id : null,
            ],
        ];
    }
}
