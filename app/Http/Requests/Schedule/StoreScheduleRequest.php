<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'professional_id' => 'nullable|integer|exists:users,id',
            'days_of_week'   => 'required|array',
            'days_of_week.*' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'effective_start_date' => 'nullable|date:Y-m-d',
            'effective_end_date' => 'nullable|date:Y-m-d|after:effective_start_date',
            'slot_duration' => 'nullable|integer|min:5|max:120',
            'observations' => 'nullable|string|max:255',
        ];
    }
}
