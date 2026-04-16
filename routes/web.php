<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;

// Ruta para mostrar el formulario de login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Ruta para mostrar el formulario de login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Ruta para procesar el envío del formulario de login
Route::post('/login', [AuthController::class, 'login'])->name('login.post');


Route::get('/dashboard', function () {
    return view('/dashboard'); 
})->name('dashboard');


// web.php

Route::prefix('users')->group(function () {

    // GET /users
    Route::get('/', function () {
        return view('users.index');
    })->name('users.index');

    // GET /users/create
    Route::get('/create', function () {
        return view('users.create');
    })->name('users.create');

    // GET /users/{id}/edit
    Route::get('/{id}/edit', function ($id) {
        return view('users.edit', ['id' => $id]);
    })->name('users.edit');

});

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

Route::prefix('schedules')->group(function () {

    
    Route::get('/', function () {
        return view('schedules.index');
    })->name('schedules.index');

    // GET /patients/create
    Route::get('/create', function () {
        return view('schedules.create');
    })->name('schedules.create');

    // GET /patients/{id}/edit
    Route::get('/{id}/edit', function ($id) {
        return view('patients.edit', ['id' => $id]);
    })->name('patients.edit');

});

Route::prefix('professionalSchedules')->group(function () {

    
    Route::get('/', function () {
        return view('professionalSchedules.index');
    })->name('professionalSchedules.index');

    // GET /patients/create
    Route::get('/create', function () {
        return view('professionalSchedules.create');
    })->name('professionalSchedules.create');

    // GET /patients/{id}/edit
    Route::get('/{id}/edit', function ($id) {
        return view('professionalSchedules.edit', ['id' => $id]);
    })->name('professionalSchedules.edit');

});

