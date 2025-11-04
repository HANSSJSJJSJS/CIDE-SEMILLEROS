<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Proyecto;
use App\Models\Documento;
use Illuminate\Support\Facades\DB;

class AprendizController extends Controller
{
    public function index(){ return view('usuarios.index'); }
    public function create(){ return view('usuarios.create'); }
    public function store(Request $r){ /* ... */ }
    public function show($id){ /* ... */ }
    public function edit($id){ /* ... */ }
    public function update(Request $r,$id){ /* ... */ }
    public function destroy($id){ /* ... */ }

    public function dashboard()
    {
        // Obtener el ID del aprendiz autenticado
        $aprendizId = Auth::id();

        // Contar proyectos asignados al aprendiz
        $proyectosCount = Proyecto::whereHas('aprendices', function($query) use ($aprendizId) {
            $query->where('user_id', $aprendizId);
        })->count();

        // Obtener documentos pendientes
        $documentosPendientes = Documento::whereHas('proyecto.aprendices', function($query) use ($aprendizId) {
            $query->where('user_id', $aprendizId);
        })->where('estado', 'pendiente')->count();

        // Obtener documentos completados
        $documentosCompletados = Documento::whereHas('proyecto.aprendices', function($query) use ($aprendizId) {
            $query->where('user_id', $aprendizId);
        })->where('estado', 'completado')->count();

        // Calcular promedio de efectividad
        $progresoPromedio = Documento::whereHas('proyecto.aprendices', function($query) use ($aprendizId) {
            $query->where('user_id', $aprendizId);
        })
        ->where('created_at', '>=', now()->subDays(30))
        ->where('estado', 'completado')
        ->count();

        if ($documentosCompletados + $documentosPendientes > 0) {
            $progresoPromedio = round(($documentosCompletados / ($documentosCompletados + $documentosPendientes)) * 100);
        } else {
            $progresoPromedio = 0;
        }

        return view('aprendiz.dashboard-aprendiz', compact(
            'proyectosCount',
            'documentosPendientes',
            'documentosCompletados',
            'progresoPromedio'
        ));
    }
}
