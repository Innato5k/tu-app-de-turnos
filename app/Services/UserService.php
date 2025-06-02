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
    //TODO: contemplar el cambio de passpara un usuario con id definido
    
     //TODO: chequear todos los campos

    //TODO: contemplar el cambio de passsolo usuario logeado
    
     //TODO: chequear todos los campos

    public function updateUser(int $id, array $data): ?User
    {
        
        $user = $this->findUserById($id);

        if (!$user) {
            return null;
        }

        // Actualiza los campos específicos
        if (isset($data['name'])) {
            $user->name = $data['name'];
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
    //TODO: contemplar no poder eliminarte
    public function deleteUser(int $id): bool
    {
        $user = $this->findUserById($id);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }
}
