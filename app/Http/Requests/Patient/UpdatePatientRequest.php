<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $patientId = $this->route('id') instanceof \App\Models\Patient 
        ? $this->route('id')->id 
        : $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cuil' => 'required|string|max:20|unique:patients,cuil,' . $patientId,
            'email' => 'required|email|unique:patients,email,' . $patientId,
            'phone' => 'nullable|string|max:20',
            'phone_opt' => 'nullable|string|max:20',
            'observations' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'medical_coverage' => 'nullable|string|max:255',
            'preferred_modality' => 'nullable|string|max:50',
            'preferred_cost' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
        ];
    }
}