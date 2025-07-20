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

Route::get('/patients', function () {
    return view('patients.index');
})->name('patients.index'); 

Route::get('/patients/create', function () {
    return view('patients.create');
})->name('patients.create'); 

Route::get('/patients/{id}/edit/', function ($id) {
    return view('patients.edit', ['id' => $id]);
})->name('patients.edit');

