<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [];

    protected $middlewareGroups = [];

    protected $middlewareAliases = [
        'verified' => \App\Http\Middleware\HasEmailVerificadoMiddleware::class,
        'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
    ];
}
