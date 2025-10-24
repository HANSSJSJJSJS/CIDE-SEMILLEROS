<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Archivo;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 🔹 Contar proyectos del aprendiz
        $proyectos = $user->proyectos;
        $proyectosCount = $proyectos->count();

        // 🔹 Usar scopes del modelo Archivo
        $documentosPendientes = Archivo::delUsuario($user->id)->pendientes()->count();
        $documentosCompletados = Archivo::delUsuario($user->id)->completados()->count();

        // 🔹 Calcular progreso promedio
        $totalDocumentos = $documentosPendientes + $documentosCompletados;
        $progresoPromedio = $totalDocumentos > 0
            ? round(($documentosCompletados / $totalDocumentos) * 100, 2)
            : 0;

        return view('aprendiz.dashboard-aprendiz', compact(
            'user',
            'proyectosCount',
            'documentosPendientes',
            'documentosCompletados',
            'progresoPromedio'
        ));
    }

    // 🔹 Endpoint dinámico (AJAX)
    public function stats()
    {
        $user = Auth::user();

        $proyectos = $user->proyectos;
        $proyectosCount = $proyectos->count();

        $documentosPendientes = Archivo::delUsuario($user->id)->pendientes()->count();
        $documentosCompletados = Archivo::delUsuario($user->id)->completados()->count();

        $totalDocumentos = $documentosPendientes + $documentosCompletados;
        $progresoPromedio = $totalDocumentos > 0
            ? round(($documentosCompletados / $totalDocumentos) * 100, 2)
            : 0;

        return response()->json([
            'proyectosCount' => $proyectosCount,
            'documentosPendientes' => $documentosPendientes,
            'documentosCompletados' => $documentosCompletados,
            'progresoPromedio' => $progresoPromedio,
        ]);
    }
}
