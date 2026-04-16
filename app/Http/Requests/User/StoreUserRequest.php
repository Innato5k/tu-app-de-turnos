<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cuil' => 'required|string|max:20|unique:users,cuil',
            'birth_date' => 'required|date', 
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'phone_opt' => 'nullable|string|max:20',
            'national_md_lic' => 'required|string|max:10',
            'provincial_md_lic' => 'required|string|max:10',
            'speciality' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:10',
        ];
    }
}