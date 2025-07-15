<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{

    
    /**
     * Obtiene todos los usuarios.
     *
     * @return \Illuminate\Database\Eloquent\Collection<User>
     */
    public function getAllUsers(): Collection
    {
        return User::all();
    }

    /**
     * Encuentra un usuario por su ID.
     *
     * @param int $id
     * @return \App\Models\User|null
     */
    public function findUserById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Actualiza un usuario existente.
     *
     * @param int $id
     * @param array $data Los datos a actualizar. Puede incluir 'name', 'email', 'password'.
     * @return \App\Models\User|null El usuario actualizado, o null si no se encontró.
     */
    public function updateUser(int $id, array $data): ?User
    {
        $user = $this->findUserById($id);

        if (!$user) {
            return null;
        }
        // Validación de datos
        if (empty($data['national_md_lic']) && empty($data['provincial_md_lic'])) {
            throw ValidationException::withMessages([
            'license' => ['National or Provincial medical license is required.'],
            ]);
        }
        if (User::where('cuil', $data['cuil'])->where('id', '!=', $id)->exists() || User::where('email', $data['email'])->where('id', '!=', $id)->exists()) {
            throw ValidationException::withMessages([
            'license' => ['Cuil or email already registered.'],]); 
        }
        if (User::where('national_md_lic', $data['national_md_lic'])->where('id', '!=', $id)->exists() || User::where('provincial_md_lic', $data['provincial_md_lic'])->where('id', '!=', $id)->exists()) {
            throw ValidationException::withMessages([
            'license' => ['National or Provincial medical license already registered.'],]); 
        }
               


        // Actualización de los campos del usuario
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['lastname'])) {
            $user->last_name = $data['lastname'];
        }
        if (isset($data['cuil'])) {
            $user->cuil = $data['cuil'];
        }
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }
        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        if (isset($data['national_md_lic'])) {
            $user->national_md_lic = $data['national_md_lic'];
        }
        if (isset($data['provincial_md_lic'])) {
            $user->provincial_md_lic = $data['provincial_md_lic'];
        }
        if (isset($data['phone'])) {
            $user->phone = $data['phone'];
        }
        if (isset($data['phone_opt'])) {
            $user->phone_opt = $data['phone_opt'];
        }
        if (isset($data['speciality'])) {
            $user->speciality = $data['speciality'];
        }
        if (isset($data['picture'])) {
            $user->picture = $data['picture'];
        }

        $user->save();

        return $user;
    }

    /**
     * Elimina un usuario.
     *
     * @param int $id
     * @return bool True si se eliminó, false si no se encontró.
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->findUserById($id);

        if (!$user || auth('api')->id() == $id) {
            return false;
        }

        return $user->delete();
    }
}
