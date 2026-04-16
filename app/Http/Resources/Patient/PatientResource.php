<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // datos del paciente
            'name' => $this->when($request->routeIs('Patients.show'), $this->name),
            'last_name' => $this->when($request->routeIs('Patients.show'), $this->last_name),
            'full_name' => "{$this->name} {$this->last_name}", // Campo calculado útil
            'cuil' => $this->cuil,
            'birth_date' => $this->when($request->routeIs('Patients.show'), $this->birth_date?->format('Y-m-d')),
            'address' => $this->when($request->routeIs('Patients.show'), $this->address),
            'city' => $this->when($request->routeIs('Patients.show'), $this->city),
            'province' => $this->when($request->routeIs('Patients.show'), $this->province),
            'postal_code' => $this->when($request->routeIs('Patients.show'), $this->postal_code),
            'gender' => $this->when($request->routeIs('Patients.show'), $this->gender),
            
            //datos de contacto
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_opt' => $this->when($request->routeIs('Patients.show'), $this->phone_opt),

            //datos médicos
            'medical_coverage' => $this->when($request->routeIs('Patients.show'), $this->medical_coverage),
            'affiliate_number' => $this->when($request->routeIs('Patients.show'), $this->affiliate_number),

            //datos adicionales     
            'preferred_modality' => $this->when($request->routeIs('Patients.show'), $this->preferred_modality),
            'observations' => $this->when($request->routeIs('Patients.show'), $this->observations),
            'is_active' => !$this->deleted_at,
            'medical_history' => $this->when($request->routeIs('Patients.show'), $this->medical_history),

            // relaciones
            'institution_id' => $this->when($request->routeIs('Patients.show'), $this->institution_id),
            'created_by_id' => $this->when($request->routeIs('Patients.show'), $this->created_by_id),
        ];
    }
}
