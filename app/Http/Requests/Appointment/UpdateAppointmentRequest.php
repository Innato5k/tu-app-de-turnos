<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status'            => 'required|in:booked,attended,absent,cancelled',
            'payment_status'    => 'required|in:paid,pending',
            'notes'             => 'nullable|string|max:500',
            'cost'              => 'nullable|numeric|min:0',
            'modality'          => 'required|in:Presencial,Virtual',
        ];
    }
}