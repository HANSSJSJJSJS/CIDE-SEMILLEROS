<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarioController extends Controller
{
    public function index()
    {
        return view('calendario.index');
    }

    public function eventos(Request $request)
    {
        $query = Evento::query();

        if ($request->has('mes') && $request->has('anio')) {
            $mes = $request->input('mes');
            $anio = $request->input('anio');
            
            $inicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
            $fin = Carbon::createFromDate($anio, $mes, 1)->endOfMonth();
            
            $query->whereBetween('fecha_hora', [$inicio, $fin]);
        } elseif ($request->has('anio')) {
            $anio = $request->input('anio');
            
            $inicio = Carbon::createFromDate($anio, 1, 1)->startOfYear();
            $fin = Carbon::createFromDate($anio, 12, 31)->endOfYear();
            
            $query->whereBetween('fecha_hora', [$inicio, $fin]);
        }

        $eventos = $query->orderBy('fecha_hora')->get();

        return response()->json([
            'success' => true,
            'eventos' => $eventos
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_hora' => 'required|date',
            'duracion' => 'required|integer|min:15|max:480',
            'tipo' => 'required|in:presencial,virtual,hibrida',
            'ubicacion' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'participantes' => 'nullable|array',
            'participantes.*' => 'exists:users,id'
        ]);

        $evento = Evento::create($validatedData);

        if ($request->has('participantes')) {
            $evento->participantes()->sync($request->participantes);
        }

        return response()->json([
            'success' => true,
            'message' => 'Evento creado exitosamente',
            'evento' => $evento
        ]);
    }

    public function update(Request $request, Evento $evento)
    {
        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_hora' => 'required|date',
            'duracion' => 'required|integer|min:15|max:480',
            'tipo' => 'required|in:presencial,virtual,hibrida',
            'ubicacion' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'participantes' => 'nullable|array',
            'participantes.*' => 'exists:users,id'
        ]);

        $evento->update($validatedData);

        if ($request->has('participantes')) {
            $evento->participantes()->sync($request->participantes);
        }

        return response()->json([
            'success' => true,
            'message' => 'Evento actualizado exitosamente',
            'evento' => $evento
        ]);
    }

    public function destroy(Evento $evento)
    {
        $evento->participantes()->detach();
        $evento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evento eliminado exitosamente'
        ]);
    }
}