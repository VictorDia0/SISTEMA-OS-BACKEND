<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/me', 'getDadosUsuarioAutenticado')->middleware('jwt.auth');
        Route::post('/login', 'login');
        Route::post('/refresh', 'refresh');
    });
});

Route::group(['prefix' => 'users', 'middleware' => 'jwt.verify'], function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/', 'index')->can('getAllUsers', User::class);
        Route::get('/ordens', 'getAllOrdersByUser');
    });

    Route::group(['prefix' => 'clients'], function () {
        Route::controller(ClientController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
        });
    });
});

Route::group(['prefix' => 'employees'], function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/', [EmployeeController::class, 'store']);
    Route::get('/{id}', [EmployeeController::class, 'show']);
    Route::delete('/{id}', [EmployeeController::class, 'destroy']);
});

Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession']);
Route::get('/checkout-success', [StripeController::class, 'checkoutSuccess'])->name('checkout.success');
Route::get('/checkout-cancel', [StripeController::class, 'checkoutCancel'])->name('checkout.cancel');

Route::post('/logout/{id}', [AuthController::class, 'logout']);
//Route::post('/logout-all', [AuthController::class, 'logoutFromAllDevices']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::get('/me', [AuthController::class, 'me']);

Route::get('/sessions', [AuthController::class, 'sessions']);

Route::post('/users', [UserController::class, 'store'])->name('users.store');

Route::get('/preview-email', function () {
    $user = 'Victor';
    return view('emails\sendWelcomeTextEmail.blade.php', compact('user'));
});
