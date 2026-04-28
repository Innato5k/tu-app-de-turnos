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
                : 'Disponible',

            'start' => $this->start_time->toIso8601String(),
            'end'   => $this->end_time->toIso8601String(),

            'extendedProps' => [
                'appointment_id' => $isAppointment ? $this->id : null,
                'slot_id'        => $isAppointment ? $this->available_slot_id : $this->id,
                'status'         => $isAppointment ? $this->status : 'available', // <-- ACÁ LA MAGIA
                'payment_status' => $isAppointment ? $this->payment_status : 'n/a',
                'modality'       => $isAppointment ? $this->modality : null,
                'cost'           => $isAppointment ? $this->cost : 0,
                'notes'          => $isAppointment ? $this->notes : '',
                'is_extra'       => $isAppointment ? $this->is_extra : false,
            ],
        ];
    }
}
