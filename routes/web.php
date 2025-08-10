<?php

use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Services\VerificacaoEmailService;

Route::get('/testar-email', function () {
    $user = User::first(); // Pegue um usuário existente
    app(VerificacaoEmailService::class)->sendEmailVerification($user);
    return "E-mail de teste enviado! Verifique o Mailtrap.";
});

Route::get('/email/verify', function () {
    return response()->json(['message' => 'Verifique seu e-mail antes de continuar.']);
})->name('verification.notice');
