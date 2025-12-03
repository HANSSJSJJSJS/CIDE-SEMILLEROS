<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $levels = [
        //
    ];

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        //
    }

    public function render($request, Throwable $e)
    {
        // ⭐ Manejar error 419 (Token CSRF)
        if ($e instanceof TokenMismatchException) {
            return redirect()
                ->back()
                ->with('error', 'Tu sesión ha expirado. Recarga la página e intenta nuevamente.');
        }

        return parent::render($request, $e);
    }
}
