<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/email/verify', function () {
    return response()->json(['message' => 'Verifique seu e-mail antes de continuar.']);
})->name('verification.notice');
