<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

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
