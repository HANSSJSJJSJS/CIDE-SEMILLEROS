<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TablaGestionUsuario;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // Cargar últimos registros (puedes cambiar por tu tabla)
        $logs = TablaGestionUsuario::latest('created_at')->paginate(10);

        // Renderizar la vista del panel admin con los logs
        return view('admin.dashboard', compact('logs'));
    }
}
