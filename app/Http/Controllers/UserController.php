<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\DTOs\User\UserUpdateRequestDTO;
use App\DTOs\User\UserRequestDTO;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;

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
    public function index(Request $request)
    {
        $searchQuery = $request->query('search');
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);

        $users = $this->userService->getAllUsers($request, $searchQuery, 'name');
        //return response()->json($users);
        return UserResource::collection($users);
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
        try {
            $user = $this->userService->findUserById($id);
            return new UserResource($user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $dto = UserRequestDTO::fromRequest($request);
        $user = $this->userService->registerUser($dto);
        return new UserResource($user);
    }

    /**
     * Actualiza un usuario específico.
     * Requiere autenticación JWT.
     *
     * @param \App\Http\Requests\User\UpdateUserRequest $request
     * @param int $id El ID del usuario a actualizar.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        try {
            $dto = UserUpdateRequestDTO::fromRequest($request);
            $user = $this->userService->updateUser($id, $dto);
            return new UserResource($user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
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
        try {
            if (!$this->userService->deleteUser($id)) {
                return response()->json(['message' => 'Cannot delete yourself'], 403);
            }
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }
}
