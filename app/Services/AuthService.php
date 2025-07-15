<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth; 
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Token;
use Trowable;


class AuthService
{
    /**
     * Registra un nuevo usuario.
     *
     * @param array $data Los datos del usuario (name, email, password, password_confirmation)
     * @return \App\Models\User
     */
    public function registerUser(array $data): ?User
    {
        if (empty($data['national_md_lic']) && empty($data['provincial_md_lic'])) {
            throw ValidationException::withMessages([
            'license' => ['National or Provincial medical license is required.'],
            ]);
        }
        if (User::where('national_md_lic', $data['national_md_lic'])->exists() || User::where('provincial_md_lic', $data['provincial_md_lic'])->exists()) {
            throw ValidationException::withMessages([
            'license' => ['National or Provincial medical license already registered.'],]); 
        }
        
        return User::create([
            'name' => $data['name'],
            'last_name' => $data['lastname'],
            'cuil' => $data['cuil'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'national_md_lic' => $data['national_md_lic'] ?? null,
            'provincial_md_lic' => $data['provincial_md_lic'] ?? null,
            'phone' => $data['phone'] ?? null,
            'phone_opt' => $data['phone_opt'] ?? null,
            'speciality' => $data['speciality'] ?? null,
            'picture' => $data['picture'] ?? null,
        ]);
    }

    /**
     * Intenta loguear un usuario y devuelve el token JWT.
     *
     * @param string $email
     * @param string $password
     * @return string El token JWT
     * @throws \Illuminate\Validation\ValidationException Si las credenciales son inválidas.
     */
    public function attemptLogin(string $email, string $password): string
    {
        $credentials = ['email' => $email, 'password' => $password];

        if (! $token = JWTAuth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        return $token;
    }

    /**
     * Invalida el token JWT actual.
     *
     * @return void
     */
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Refresca un token JWT inválido (si es posible).
     *
     * @return string El nuevo token JWT
     */
    public function refresh(): string
    {
        return JWTAuth::refresh();
    }

    /**
     * Obtiene el usuario autenticado a través del token JWT.
     *
     * @return \App\Models\User|null
     */
    public function getAuthenticatedUser(): ?User
    {
        try {
            return JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Obtiene el usuario a partir de un token JWT.
     *
     * @param string $tokenString El token JWT en formato string.
     * @return \App\Models\User|null
     */
    public function getUserFromToken(string $tokenString): ?User
    {
        try {
            $token = new Token($tokenString);
            $payload = JWTAuth::decode($token);
            return User::find($payload->get('sub'));
        } catch (\Exception $e) {
            // Si el token es inválido, expirado, etc.
            return null;
        }
    }
}