<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfessionalScheduleController;

/*

|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Rutas protegidas
Route::group(['middleware' => ['jwt.auth'], 'prefix' => 'auth'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']); 
    Route::get('me', [AuthController::class, 'me']); // Para obtener info del usuario autenticado
});

// Rutas protegidas para la gestión de usuarios
Route::group(['middleware' => ['jwt.auth'], 'prefix' => 'usuarios'], function () {
    Route::get('/', [UserController::class, 'index']); 
    Route::get('/{id}', [UserController::class, 'show']); 
    Route::put('/{id}', [UserController::class, 'update']); 
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

// Rutas protegidas para la gestión de Pacientes
Route::group(['middleware' => ['jwt.auth'], 'prefix' => 'patients'], function () {
    Route::get('/', [PatientController::class, 'index']); 
    Route::get('/{id}', [PatientController::class, 'show']); 
    Route::put('/{id}', [PatientController::class, 'update']); 
    Route::delete('/{id}', [PatientController::class, 'destroy']);
    Route::post('/', [PatientController::class, 'store']);
});

// Rutas protegidas para la gestión de Profesional Schedules
Route::group(['middleware' => ['jwt.auth'], 'prefix' => 'professionalSchedule'], function () {
    Route::get('/', [ProfessionalScheduleController::class, 'index']); 
    Route::get('/{id}', [ProfessionalScheduleController::class, 'show']); 
    Route::get('/user/{id}', [ProfessionalScheduleController::class, 'showByUserId']); 
    Route::put('/{id}', [ProfessionalScheduleController::class, 'update']); 
    Route::delete('/{id}', [ProfessionalScheduleController::class, 'destroy']);
    Route::post('/', [ProfessionalScheduleController::class, 'store']);
});
// Ejemplo de una ruta protegida adicional
Route::middleware(['jwt.auth'])->get('/user-profile', function (Request $request) {
    return response()->json(['message' => '¡Bienvenido! Eres un usuario autenticado.', 'user' => $request->user()]);
});