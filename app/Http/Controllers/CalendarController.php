<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CalendarController extends Controller
{
    /**
     * Listar eventos (opcionalmente filtrado por mes/año)
     * GET /eventos?mes=10&anio=2025
     */
    public function index(Request $request)
    {
        $query = Evento::query()->with(['participantes', 'proyecto']);

        // Filtrado por mes/año (para el calendario)
        $mes = (int) $request->query('mes');
        $anio = (int) $request->query('anio');
        if ($mes && $anio) {
            $inicio = Carbon::create($anio, $mes, 1)->startOfDay();
            $fin = Carbon::create($anio, $mes, 1)->endOfMonth()->endOfDay();
            $query->whereBetween('fecha_hora', [$inicio, $fin]);
        }

        $eventos = $query->orderBy('fecha_hora')->get();

        return response()->json([
            'success' => true,
            'eventos' => $eventos,
        ]);
    }

    /**
     * Mostrar un evento
     */
    public function show($id)
    {
        $evento = Evento::with(['participantes', 'proyecto'])->findOrFail($id);

        // Acceso: creador o participante
        if ($evento->id_usuario != Auth::id() && !$evento->participantes->contains(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a este evento',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'evento' => $evento,
        ]);
    }

    /**
     * Crear un nuevo evento con generación automática de enlace (opcional)
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string|in:general,proyecto,personal',
            'descripcion' => 'nullable|string',
            'id_proyecto' => 'nullable|exists:proyectos,id_proyecto',
            'fecha_hora' => 'required|date',
            'duracion' => 'required|integer|min:15|max:480',
            'ubicacion' => 'required|string|in:presencial,virtual,hibrido',
            'link_virtual' => 'nullable|url|required_if:ubicacion,virtual,hibrido',
            'recordatorio' => 'required|string|in:none,5min,15min,30min,1h,1d',
            'participantes' => 'nullable|array',
            'participantes.*' => 'exists:users,id',
            'generar_enlace' => 'nullable|string|in:teams,meet,personalizado',
        ]);

        try {
            $linkVirtual = $request->link_virtual;

            // Generar enlace automáticamente si se solicita y no se proporcionó uno
            if (($request->ubicacion === 'virtual' || $request->ubicacion === 'hibrido') && empty($linkVirtual) && $request->filled('generar_enlace')) {
                $linkVirtual = $this->generarEnlaceReunion($request->generar_enlace, $request->titulo);
            }

            $evento = Evento::create([
                'titulo' => $request->titulo,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'id_proyecto' => $request->id_proyecto,
                'id_usuario' => Auth::id(),
                'fecha_hora' => $request->fecha_hora,
                'duracion' => $request->duracion,
                'ubicacion' => $request->ubicacion,
                'link_virtual' => $linkVirtual,
                'recordatorio' => $request->recordatorio,
                'codigo_reunion' => $this->generarCodigoReunion(),
            ]);

            // Sincronizar participantes
            if ($request->has('participantes')) {
                $evento->participantes()->sync($request->participantes);
            }

            return response()->json([
                'success' => true,
                'message' => 'Evento creado exitosamente',
                'evento' => $evento->load('participantes'),
                'enlace_generado' => !empty($linkVirtual),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el evento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar evento con generación de enlace (opcional)
     */
    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        if ($evento->id_usuario != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar este evento',
            ], 403);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string|in:general,proyecto,personal',
            'descripcion' => 'nullable|string',
            'id_proyecto' => 'nullable|exists:proyectos,id_proyecto',
            'fecha_hora' => 'required|date',
            'duracion' => 'required|integer|min:15|max:480',
            'ubicacion' => 'required|string|in:presencial,virtual,hibrido',
            'link_virtual' => 'nullable|url|required_if:ubicacion,virtual,hibrido',
            'recordatorio' => 'required|string|in:none,5min,15min,30min,1h,1d',
            'participantes' => 'nullable|array',
            'participantes.*' => 'exists:users,id',
            'generar_enlace' => 'nullable|string|in:teams,meet,personalizado',
        ]);

        try {
            $linkVirtual = $request->link_virtual;

            if (($request->ubicacion === 'virtual' || $request->ubicacion === 'hibrido') && empty($linkVirtual) && $request->filled('generar_enlace')) {
                $linkVirtual = $this->generarEnlaceReunion($request->generar_enlace, $request->titulo);
            }

            $evento->update([
                'titulo' => $request->titulo,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'id_proyecto' => $request->id_proyecto,
                'fecha_hora' => $request->fecha_hora,
                'duracion' => $request->duracion,
                'ubicacion' => $request->ubicacion,
                'link_virtual' => $linkVirtual,
                'recordatorio' => $request->recordatorio,
                'codigo_reunion' => $evento->codigo_reunion ?: $this->generarCodigoReunion(),
            ]);

            if ($request->has('participantes')) {
                $evento->participantes()->sync($request->participantes);
            }

            return response()->json([
                'success' => true,
                'message' => 'Evento actualizado exitosamente',
                'evento' => $evento->load('participantes'),
                'enlace_generado' => !empty($linkVirtual),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el evento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** Eliminar evento */
    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);

        if ($evento->id_usuario != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar este evento',
            ], 403);
        }

        try {
            $evento->participantes()->detach();
            $evento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Evento eliminado',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el evento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** Generar enlace de reunión para un evento existente */
    public function generarEnlace(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        if ($evento->id_usuario != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para generar enlaces para este evento',
            ], 403);
        }

        $request->validate([
            'plataforma' => 'required|string|in:teams,meet,personalizado',
        ]);

        try {
            $enlace = $this->generarEnlaceReunion($request->plataforma, $evento->titulo);

            $evento->update([
                'link_virtual' => $enlace,
                'codigo_reunion' => $evento->codigo_reunion ?: $this->generarCodigoReunion(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Enlace de reunión generado exitosamente',
                'enlace' => $enlace,
                'evento' => $evento,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el enlace: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** Obtener información resumida de la reunión */
    public function getInfoReunion($id)
    {
        $evento = Evento::with('participantes')->findOrFail($id);

        if ($evento->id_usuario != Auth::id() && !$evento->participantes->contains(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a este evento',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'evento' => [
                'id' => $evento->id,
                'titulo' => $evento->titulo,
                'link_virtual' => $evento->link_virtual,
                'codigo_reunion' => $evento->codigo_reunion,
                'ubicacion' => $evento->ubicacion,
                'fecha_hora' => $evento->fecha_hora,
                'duracion' => $evento->duracion,
                'tiene_enlace' => !empty($evento->link_virtual),
            ],
        ]);
    }

    /** Eventos de hoy del usuario */
    public function today(Request $request)
    {
        $inicio = Carbon::today();
        $fin = Carbon::today()->endOfDay();

        $eventos = Evento::where('id_usuario', Auth::id())
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->orderBy('fecha_hora')
            ->get();

        return response()->json(['success' => true, 'eventos' => $eventos]);
    }

    /** Próximos eventos (siguientes N días) del usuario */
    public function upcoming(Request $request)
    {
        $dias = max(1, (int) $request->query('dias', 7));
        $inicio = Carbon::now();
        $fin = Carbon::now()->addDays($dias);

        $eventos = Evento::where('id_usuario', Auth::id())
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->orderBy('fecha_hora')
            ->get();

        return response()->json(['success' => true, 'eventos' => $eventos]);
    }

    /** Helper: generar enlace según plataforma */
    private function generarEnlaceReunion($plataforma, $titulo)
    {
        $codigo = $this->generarCodigoReunion();
        $tituloCodificado = urlencode($titulo);

        switch ($plataforma) {
            case 'teams':
                // Enlace de Teams con ID único simulado
                $tenant = config('app.teams_tenant_id', 'public');
                $oid = Auth::id();
                return "https://teams.microsoft.com/l/meetup-join/19%3Ameeting_{$codigo}%40thread.v2/0?context=%7B%22Tid%22%3A%22{$tenant}%22%2C%22Oid%22%3A%22{$oid}%22%7D&subject={$tituloCodificado}";
            case 'meet':
                return "https://meet.google.com/{$codigo}";
            case 'personalizado':
                return "https://your-platform.com/meeting/{$codigo}";
            default:
                return null;
        }
    }

    /** Helper: código único */
    private function generarCodigoReunion()
    {
        return Str::random(10);
    }
}
