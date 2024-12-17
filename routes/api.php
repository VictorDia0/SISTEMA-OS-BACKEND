<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show')->whereUuid('id');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update')->whereUuid('id');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy')->whereUuid('id');
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
    Route::post('/logout-all', [AuthController::class, 'logoutFromAllDevices']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/sessions', [AuthController::class, 'sessions']);
});

Route::post('/users', [UserController::class, 'store'])->name('users.store');
