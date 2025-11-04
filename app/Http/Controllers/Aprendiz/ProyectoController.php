<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\LiderSemillero;
use App\Models\Evidencia;
use App\Models\Archivo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Obtener proyectos donde el aprendiz está asignado
        $proyectos = Proyecto::whereHas('aprendices', function($q) use ($user) {
            $q->where('aprendices.id_usuario', $user->id);
        })
        ->with(['semillero'])
        ->get();

        return view('aprendiz.proyectos.index', compact('proyectos'));
    }

    public function show($id)
    {
        $user = Auth::user();
        // Cargar proyecto solo si el usuario está asignado
        $proyecto = Proyecto::with(['semillero', 'aprendices', 'evidencias'])
            ->where('id_proyecto', $id)
            ->whereHas('aprendices', function($q) use ($user) {
                $q->where('aprendices.id_usuario', $user->id);
            })
            ->firstOrFail();

        // Compañeros (otros aprendices asignados al proyecto)
        $companeros = $proyecto->aprendices
            ->where('id_usuario', '!=', $user->id)
            ->values();

        // Líder del semillero (si el proyecto pertenece a un semillero con líder asociado)
        $lider = null;
        if ($proyecto->semillero && !empty($proyecto->semillero->id_lider_usuario)) {
            $lider = LiderSemillero::with('user')
                ->where('id_usuario', $proyecto->semillero->id_lider_usuario)
                ->first();
        }

        // Filtros de evidencias: por fecha exacta (created_at) y por nombre del compañero (autor)
        $fecha = request('fecha');
        $nombre = request('nombre');

        // Validación del nombre contra compañeros del proyecto (incluye al propio usuario si tiene registro en aprendices)
        $nombreError = null;
        $aplicarFiltroNombre = false;
        if ($nombre) {
            $aprendizActual = \App\Models\Aprendiz::where('id_usuario', $user->id)->first();
            $lista = $companeros->values();
            if ($aprendizActual) { $lista = $lista->push($aprendizActual); }

            $hayMatch = $lista->filter(function ($ap) use ($nombre) {
                $val = null;
                if (method_exists($ap, 'getAttribute')) {
                    $val = $ap->nombre_completo ?? trim(($ap->nombres ?? '') . ' ' . ($ap->apellidos ?? ''));
                }
                return $val && stripos($val, $nombre) !== false;
            })->isNotEmpty();

            if ($hayMatch) {
                $aplicarFiltroNombre = true;
            } else {
                $nombreError = 'No se encontró ningún compañero con ese nombre en este proyecto.';
            }
        }

        $evidencias = Evidencia::with(['autor'])
            ->where('id_proyecto', $proyecto->id_proyecto)
            ->when($fecha, function ($q) use ($fecha) {
                $q->whereDate('created_at', $fecha);
            })
            ->when($aplicarFiltroNombre && $nombre, function ($q) use ($nombre) {
                $q->whereHas('autor', function ($s) use ($nombre) {
                    $s->whereRaw("CONCAT(COALESCE(nombres,''),' ',COALESCE(apellidos,'')) LIKE ?", ["%$nombre%"]); 
                });
            })
            ->orderByDesc('created_at')
            ->get();

        // Documentos del proyecto (para listarlos en el detalle)
        $archivos = Archivo::with('user')
            ->where('proyecto_id', $proyecto->id_proyecto)
            ->orderByDesc('subido_en')
            ->get();

        return view('aprendiz.proyectos.show', compact('proyecto', 'companeros', 'lider', 'evidencias', 'fecha', 'nombre', 'nombreError', 'archivos'));
    }
}

