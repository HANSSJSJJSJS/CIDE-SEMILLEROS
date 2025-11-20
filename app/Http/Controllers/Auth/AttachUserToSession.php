<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttachUserToSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // SOLO si hay un usuario autenticado
        if (Auth::check()) {
            $sessionId = $request->session()->getId();
            $userId    = Auth::id();

            // Log para verificar que entra aquí
            Log::info('AttachUserToSession', [
                'session_id' => $sessionId,
                'user_id'    => $userId,
            ]);

            DB::table('sessions')
                ->where('id', $sessionId)
                ->update(['user_id' => $userId]);
        }

        return $response;
    }
}
