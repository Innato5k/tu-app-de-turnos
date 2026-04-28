<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreExtraAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'start_time'        => 'required|date:Y-m-d',
            'patient_id'        => 'required|exists:patients,id',
            'notes'             => 'nullable|string|max:500',
            'cost'              => 'nullable|numeric|min:0',
            'modality'          => 'required|in:Presencial,Virtual',
            'duration'          => 'required|integer|min:1',
        ];
    }
}