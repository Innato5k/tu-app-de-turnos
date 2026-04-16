<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\DTOs\User\UserRequestDTO;
use App\DTOs\User\UserUpdateRequestDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserService
{


    /**
     * Obtiene todos los usuarios.
     *
     * @return \Illuminate\Database\Eloquent\Collection<User>
     */
    public function getAllUsers(Request $request, ?string $searchQuery = null, ?string $orderBy = null): LengthAwarePaginator
    {
        $query = User::withTrashed()->orderBy($orderBy ?? 'name');

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%');
            });
        }

        $perPage = $request->input('per_page', 5);
        $page = $request->input('page', 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Encuentra un usuario por su ID.
     *
     * @param int $id
     * @return \App\Models\User|null
     */
    public function findUserById(int $id): ?User
    {
        return User::withTrashed()->findOrFail($id);
    }

    /**
     * Actualiza un usuario existente.
     *
     * @param int $id
     * @param array $data Los datos a actualizar. Puede incluir 'name', 'email', 'password'.
     * @return \App\Models\User|null El usuario actualizado, o null si no se encontró.
     */
    public function updateUser(int $id, UserUpdateRequestDTO $dto): ?User
    {
        $user = $this->findUserById($id);

        if (Auth::id() === $user->id && ($dto->role !== $user->roles->first()->name || isset($dto->is_active))) {
            throw new \Exception("No puedes actualizar el estado o rol de tu propio usuario.");
        }
        $user->update($dto->toArray());

        if ($dto->role ) {
            $user->syncRoles($dto->role);
        }
        if (isset($dto->is_active)) {
            if ($dto->is_active) {
                $user->restore();
            } else {
                $user->delete();
            }
        }


        return $user;
    }

    /**
     * Registra un nuevo usuario.
     *
     * @param \App\DTOs\User\UserRequestDTO $dto
     * @return User El usuario registrado.
     */
    public function registerUser(UserRequestDTO $dto): User
    {
        $plainPassword = Str::password(12);

        $user = User::create([
            'name'               => $dto->name,
            'last_name'          => $dto->last_name,
            'cuil'               => $dto->cuil,
            'email'              => $dto->email,
            'phone'              => $dto->phone,
            'phone_opt'          => $dto->phone_opt,
            'password'           => Hash::make($plainPassword),
            'national_md_lic'    => $dto->national_md_lic,
            'provincial_md_lic'  => $dto->provincial_md_lic,
            'speciality'         => $dto->speciality,
            'birth_date'         => $dto->birth_date,
            'gender'             => $dto->gender,
        ]);

        $user->assignRole('professional');
        $user->temporary_password = $plainPassword;

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
        if (auth()->id() === $id) {
            return false;
        }
        $user = $this->findUserById($id);
        return $user->delete();
    }
}
