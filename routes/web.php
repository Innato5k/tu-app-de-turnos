<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;

// Ruta para mostrar el formulario de login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Ruta para procesar el envío del formulario de login
Route::post('/login', [AuthController::class, 'login'])->name('login.post');


Route::get('/dashboard', function () {
    return view('/dashboard'); 
})->name('dashboard');

// web.php

Route::prefix('patients')->group(function () {

    // GET /patients
    Route::get('/', function () {
        return view('patients.index');
    })->name('patients.index');

    // GET /patients/create
    Route::get('/create', function () {
        return view('patients.create');
    })->name('patients.create');

    // GET /patients/{id}/edit
    Route::get('/{id}/edit', function ($id) {
        return view('patients.edit', ['id' => $id]);
    })->name('patients.edit');

});

