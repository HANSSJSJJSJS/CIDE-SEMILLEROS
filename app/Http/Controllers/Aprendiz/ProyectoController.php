<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Support\Facades\Auth;

class ProyectoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Obtener proyectos donde el aprendiz está asignado
        $proyectos = Proyecto::whereHas('aprendices', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        return view('aprendiz.proyectos.index', compact('proyectos'));
    }

    public function show($id)
    {
        $user = Auth::user();

        $proyecto = Proyecto::where('id_proyecto', $id)
            ->whereHas('aprendices', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        return view('aprendiz.proyectos.show', compact('proyecto'));
    }
}
