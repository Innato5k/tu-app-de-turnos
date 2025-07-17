<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;

// Ruta para mostrar el formulario de login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Ruta para procesar el envío del formulario de login
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Ruta de ejemplo para el dashboard (a donde redirigirás)
Route::get('/dashboard', function () {
    return view('/dashboard'); // Esto sería tu vista real del dashboard
})->name('dashboard'); // Asegúrate de que solo usuarios autenticados puedan acceder