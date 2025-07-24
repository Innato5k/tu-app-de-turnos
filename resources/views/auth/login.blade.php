@extends('layouts.app')
@section('title', 'Iniciar Sesión') 
@section('content') 
   <div class="bg-white p-4 rounded-3 shadow-lg w-100" style="max-width: 450px;">
            <div class="text-center mb-3">
                <!-- Icono o logo representativo de psicología -->
                <svg class="mx-auto text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="height: 3.5rem; width: 3.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="mt-2 fs-4 fw-bold text-dark">
                    Bienvenido de nuevo
                </h2>
                <p class="mt-1 text-muted small">
                    Inicia sesión para gestionar tus turnos
                </p>
            </div>

            <form id="loginForm">
                @csrf 
                <div class="mb-3">
                    <label for="email" class="form-label text-dark small">
                        Correo Electrónico
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="tu@correo.com"
                        class="form-control form-control-sm @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        required
                    >
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label text-dark small">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        class="form-control form-control-sm @error('password') is-invalid @enderror"
                        required
                    >
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check small">
                        <input
                            id="remember-me"
                            name="remember"
                            type="checkbox"
                            class="form-check-input"
                        >
                        <label class="form-check-label text-dark" for="remember-me">
                            Recordarme
                        </label>
                    </div>
                    <div>
                        <a href="#" class="text-info fw-medium text-decoration-none small">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="btn btn-info w-100 py-2 fs-6 fw-semibold text-white" id="loginButton"
                    >
                        Iniciar Sesión
                    </button>
                </div>
            </form>

            <div id="appMessage" class="mt-3 text-center small fw-medium"></div>

            

            <div class="mt-3 text-center">
                <p class="text-muted small">
                    ¿No tienes una cuenta?
                    <a href="#" class="text-info fw-medium text-decoration-none">
                        Regístrate aquí
                    </a>
                </p>
            </div>
        </div>
@endsection {{-- Cierra la sección 'content' --}}

@section('scripts') {{-- Abre la sección 'scripts' para scripts específicos --}}
    @vite('resources/js/login.js') 
@endsection {{-- Cierra la sección 'scripts' --}}