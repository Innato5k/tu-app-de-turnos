<?php

namespace App\Http\Resources\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $now = \Carbon\Carbon::now();
        $startTime = \Carbon\Carbon::parse($this->start_time);

        // Calculamos la diferencia
        $diff = $now->diff($startTime);

        // Formateamos como HH:mm (asegurando el cero a la izquierda)
        $remaining = $startTime->isPast()
            ? 'En curso'
            : sprintf('%02d:%02d', ($diff->days * 24) + $diff->h, $diff->i);
            if ($remaining > '24:00') {
                $remaining = 'Más de 1 día';
            }
            
        return [
            'id' => $this->id,
            'available_slot_id' => $this->available_slot_id,
            'duration' => (int) ($this->start_time->diffInMinutes($this->end_time)) ?? 30,
            'start_time' => $this->start_time?->format('Y-m-d H:i'),
            'end_time' => $this->end_time?->format('Y-m-d H:i'),
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'is_extra' => $this->is_extra,
            'title' => $this->title,
            'full_name' => $this->patient->full_name ?? null,
            'notes' => $this->notes,
            'cost' => $this->cost,
            'modality' => $this->modality,
            'remaining_time' =>  $remaining,

            // Relaciones
            'patient' => new \App\Http\Resources\Patient\PatientResource($this->whenLoaded('patient')),
            'professional' => new \App\Http\Resources\User\UserResource($this->whenLoaded('professional')),
        ];
    }
}
