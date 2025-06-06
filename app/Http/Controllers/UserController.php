<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService; // Importa tu UserService
use App\Models\User; // Para el type-hinting en respuestas
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $userService;

    /**
     * Constructor que inyecta el servicio de usuarios.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('jwt.auth');
    }

    /**
     * Muestra una lista de todos los usuarios.
     * Requiere autenticación JWT.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = $this->userService->getAllUsers();
        return response()->json($users);
    }

    /**
     * Muestra un usuario específico.
     * Requiere autenticación JWT.
     *
     * @param int $id El ID del usuario.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $user = $this->userService->findUserById($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * Actualiza un usuario específico.
     * Requiere autenticación JWT.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id El ID del usuario a actualizar.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'cuil' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        $user = $this->userService->updateUser($id, $request->all());

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Elimina un usuario específico.
     * Requiere autenticación JWT.
     *
     * @param int $id El ID del usuario a eliminar.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $deleted = $this->userService->deleteUser($id);

        if (!$deleted) {
            return response()->json(['message' => 'User not found or could not be deleted'], 404);
        }

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}