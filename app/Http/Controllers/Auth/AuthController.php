<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    /**
     * Constructor que inyecta el servicio de autenticación.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        // Opcional: Aplicar middleware a ciertos métodos del controlador
        // $this->middleware('jwt.auth', ['except' => ['login', 'register']]);
    }

    /**
     * Registra un nuevo usuario.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
    }

    /**
     * Loguea un usuario y devuelve el token JWT.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validación de la solicitud
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        $token = $this->authService->attemptLogin($request->email, $request->password);
        
        if (!$token){
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }      

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 7200,
            'user' => new UserResource($this->authService->getUserFromToken($token)),
        ]);
    }

    /**
     * Invalida el token JWT del usuario (log out).
     * Requiere un token válido para ser ejecutado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->authService->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresca un token JWT.
     * Requiere un token válido, pero que puede estar expirado (no invalidado).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json([
            'access_token' => $this->authService->refresh(),
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Obtiene los datos del usuario autenticado.
     * Requiere un token JWT válido.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        
        return response()->json($this->authService->getAuthenticatedUser());
    }
}
