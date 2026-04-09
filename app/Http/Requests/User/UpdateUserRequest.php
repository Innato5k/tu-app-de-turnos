<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $UserId = $this->route('id') instanceof \App\Models\User 
        ? $this->route('id')->id 
        : $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cuil' => 'required|string|max:20|unique:users,cuil,' . $UserId,
            'email' => 'required|email|unique:users,email,' . $UserId,
            'phone' => 'nullable|string|max:20',
            'phone_opt' => 'nullable|string|max:20',
            'national_md_lic' => 'required|string|max:10',
            'provincial_md_lic' => 'required|string|max:10',
            'speciality' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ];
    }
}