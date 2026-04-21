<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'available_slot_id' => 'required|exists:available_slots,id',
            'patient_id'        => 'required|exists:patients,id',
            'notes'             => 'nullable|string|max:500',
            'cost'              => 'nullable|numeric|min:0',
            'modality'          => 'required|in:presencial,virtual',
        ];
    }
}