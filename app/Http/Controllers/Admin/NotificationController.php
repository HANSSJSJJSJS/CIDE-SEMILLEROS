<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
{
    // GET /admin/notifications
    public function index(Request $request)
    {
        $user = Auth::user();
        // Si tienes Notifiable y base de datos de notifications, reemplaza este bloque por consultas reales.
        // Por ahora devolvemos estructura segura vacía (evita errores en UI) o demo si se pasa ?demo=1
        $demo = (bool) $request->query('demo');

        $items = [];
        if ($demo) {
            $items = [
                [
                    'id' => '1',
                    'title' => 'Nuevo usuario registrado',
                    'body' => 'Se creó la cuenta de Juan Pérez',
                    'time' => 'hace 2 min',
                    'read' => false,
                    'url' => url('/admin/usuarios'),
                ],
                [
                    'id' => '2',
                    'title' => 'Documento pendiente',
                    'body' => 'Hay 3 entregas por revisar',
                    'time' => 'hace 1 h',
                    'read' => true,
                    'url' => url('/admin/semilleros'),
                ],
            ];
        }

        return response()->json([
            'unread_count' => collect($items)->where('read', false)->count(),
            'notifications' => $items,
        ]);
    }

    // POST /admin/notifications/read-all
    public function readAll()
    {
        // Si usas DB notifications, marca todas como leídas aquí.
        return response()->json(['ok' => true]);
    }
}
