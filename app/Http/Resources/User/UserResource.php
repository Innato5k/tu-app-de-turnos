<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->when($request->routeIs('users.show'), $this->name),
            'last_name' => $this->when($request->routeIs('users.show'), $this->last_name),
            'full_name' => "{$this->name} {$this->last_name}", // Campo calculado útil
            'email' => $this->email,
            'cuil' => $this->cuil,
            'phone' => $this->phone,
            'phone_opt' => $this->when($request->routeIs('users.show'),$this->phone_opt),
            'speciality' => $this->when($request->routeIs('users.show'),$this->speciality),
            'national_md_lic' => $this->when($request->routeIs('users.show'),$this->national_md_lic),
            'provincial_md_lic' => $this->when($request->routeIs('users.show'),$this->provincial_md_lic),
            'is_active' => $this->when($request->routeIs('users.show'),(bool) $this->is_active),
            'temporary_password' => $this->when(isset($this->temporary_password), $this->temporary_password),
            'role'  => $this->roles->pluck('name')->first() ?? 'no-role',
        ];
    }
}