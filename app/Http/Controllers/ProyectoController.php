<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    // Muestra la lista de proyectos asignados al aprendiz
    public function index()
    {
        $proyectos = Proyecto::where('aprendiz_id', auth()->id())->get();
        return view('aprendiz.proyectos.index', compact('proyectos'));
    }

    // Muestra los detalles de un proyecto específico
    public function show($id)
    {
        $proyecto = Proyecto::findOrFail($id);
        return view('aprendiz.proyectos.show', compact('proyecto'));
    }
}
