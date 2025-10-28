<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * La ruta a la que los usuarios son redirigidos después de iniciar sesión.
     *
     * Por defecto Laravel usa /home o /dashboard,
     * pero aquí la personalizamos según el rol del usuario.
     */
    public const HOME = '/login'; // valor por defecto (no se usa si tienes lógica abajo)

    /**
     * Define las rutas de tu aplicación.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Devuelve la ruta de redirección después del login.
     * Puedes usar esto si tu login depende del rol del usuario.
     */
    public static function redirectToByRole(): string
    {
        $user = Auth::user();

        if (!$user) {
            return '/login';
        }

        // Determinar la ruta según el rol
        switch (strtoupper(trim($user->rol ?? ''))) {
            case 'ADMIN':
                return route('admin.dashboard');
            case 'LIDER GENERAL':
                return route('lider_general.dashboard');
            case 'LIDER SEMILLERO':
                return route('lider_semi.dashboard');
            case 'APRENDIZ':
                return route('aprendiz.dashboard');
            default:
                return '/dashboard';
        }
    }

    /**
     * Configuración de límites de solicitudes (rate limiting).
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
