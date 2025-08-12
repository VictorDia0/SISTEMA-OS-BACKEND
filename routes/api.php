<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/refresh', 'refresh');
    Route::post('/logout', 'logout')->middleware('jwt.auth');
    Route::get('/me', 'getDadosUsuarioAutenticado')->middleware('jwt.auth');
    Route::post('/register', 'register');

    // =====================
    // EMAIL VERIFICATION
    // =====================
    Route::get('/verify', 'verificarEmailUsuario');
    Route::post('/verify/resend', 'enviarEmailVerificacao');
});

Route::prefix('users')->middleware('jwt.auth')->controller(UserController::class)->group(function () {
    Route::get('/', 'buscarTodosUsuarios')->can('getAllUsers', User::class);
    Route::get('/ordens', 'getAllOrdersByUser');
    Route::get('/{id}', 'buscarUsuarioPorId')->can('getUserById', User::class);
});

Route::prefix('clients')->middleware('jwt.auth')->controller(ClientController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
});
