<?php

namespace App\Http\Resources\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'user_id' => $this->user_id,
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

            // Relaciones
            'patient' => new \App\Http\Resources\Patient\PatientResource($this->whenLoaded('patient')),
            'professional' => new \App\Http\Resources\User\UserResource($this->whenLoaded('professional')),            
        ];
    }
}