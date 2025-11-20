<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\AttachUserToSession;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
             \App\Http\Middleware\AttachUserToSession::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    // ✅ Solo UNA vez
    protected $routeMiddleware = [
<<<<<<< HEAD
        'auth'                => \App\Http\Middleware\Authenticate::class,
        'verified'            => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role'                => \App\Http\Middleware\RoleMiddleware::class,
        'lider.semillero'     => \App\Http\Middleware\LiderSemilleroMiddleware::class,
        'prevent-back-history'=> \App\Http\Middleware\PreventBackHistory::class, // <-- aquí
=======
        'auth' => \App\Http\Middleware\Authenticate::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class, // <-- registrar alias 'role'
        'lider.semillero' => \App\Http\Middleware\LiderSemilleroMiddleware::class,
        'admin.readonly' => \App\Http\Middleware\AdminReadOnlyMiddleware::class,
>>>>>>> PreFu
    ];
}
