<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\IAuthService;
use App\Services\IUserService;
use App\Services\IVerificacaoEmailService;
use App\Services\UserService;
use App\Services\VerificacaoEmailService;
use App\Strategies\Emails\IVerificacaoEmailStrategy;
use App\Strategies\Emails\VerificacaoEmailStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(IVerificacaoEmailService::class, VerificacaoEmailService::class);
        $this->app->bind(IVerificacaoEmailStrategy::class, VerificacaoEmailStrategy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
