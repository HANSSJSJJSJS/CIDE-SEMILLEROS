<?php

use Illuminate\Foundation\Application;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware): void {
        // Registrar alias de middlewares aquí
        $middleware->alias([
            'lider.semillero' => \App\Http\Middleware\LiderSemilleroMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class, // opcional: registra también 'role'
        ]);
    })
    ->withExceptions(function ($exceptions): void {
        //
    })
    ->create();

return $app;