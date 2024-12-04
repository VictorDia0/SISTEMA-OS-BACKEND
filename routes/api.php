<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StripeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'users'], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::group(['prefix' => 'employees'], function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/', [EmployeeController::class, 'store']);
    Route::get('/{id}', [EmployeeController::class, 'show']);
    Route::delete('/{id}', [EmployeeController::class, 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession']);
    Route::get('/checkout-success', [StripeController::class, 'checkoutSuccess'])->name('checkout.success');
    Route::get('/checkout-cancel', [StripeController::class, 'checkoutCancel'])->name('checkout.cancel');
});
