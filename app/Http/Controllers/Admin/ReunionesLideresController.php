<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Semillero;
use App\Models\LiderSemillero;
use App\Models\Evento;

class ReunionesLideresController extends Controller
{
    /**
     * Página principal del calendario (vista).
     */
    public function index()
    {
        // Si quieres precargar algo a la vista (opcional):
        $lideres = LiderSemillero::all();

        return view('Admin.Reuniones.calendario_scml', compact('lideres'));
    }

    /**
     * Devuelve todos los semilleros para llenar el <select>.
     * GET /admin/reuniones-lideres/semilleros
     * Respuesta: { data: [ {id, nombre}, ... ] }
     */
    public function semilleros()
    {
        $data = Semillero::select('id_semillero as id', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json(['data' => $data]);
    }

    /**
     * Devuelve el/los líderes del semillero seleccionado.
     * GET /admin/reuniones-lideres/lideres?semillero_id=#
     * Respuesta: { data: [ {id, nombre}, ... ] }
     *
     * Nota: según tu SQL, cada semillero tiene una FK id_lider_semi (un único líder).
     * Si más adelante hay varios líderes por semillero, aquí se puede cambiar a ->get()
     */
    public function lideres(Request $request)
    {
        $sid = (int) $request->query('semillero_id');
        if (!$sid) {
            return response()->json(['data' => []]);
        }

        $lider = DB::table('semilleros as s')
            ->join('lideres_semillero as l', 'l.id_lider_semi', '=', 's.id_lider_semi')
            ->select(
                'l.id_lider_semi as id',
                DB::raw("CONCAT(l.nombres, ' ', l.apellidos) as nombre")
            )
            ->where('s.id_semillero', $sid)
            ->first();

        return response()->json([
            'data' => $lider ? [ $lider ] : []
        ]);
    }

    /**
     * Obtiene reuniones del mes/año para pintar el calendario.
     * GET /admin/reuniones-lideres/obtener?mes=&anio=
     * Respuesta: { eventos: [...] }
     */
    public function obtener(Request $request)
    {
        $mes  = (int) $request->query('mes');
        $anio = (int) $request->query('anio');

        // Si no vienen, usa mes/año actuales.
        if (!$mes || !$anio) {
            $now = now();
            $mes  = $mes  ?: (int) $now->format('m');
            $anio = $anio ?: (int) $now->format('Y');
        }

        $eventos = Evento::whereMonth('fecha_hora', $mes)
            ->whereYear('fecha_hora', $anio)
            ->get();

        return response()->json(['eventos' => $eventos]);
    }

    /**
     * Guarda una nueva reunión.
     * POST /admin/reuniones-lideres
     * Body JSON esperado por tu JS.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'fecha_hora'      => 'required|date',
            'duracion'        => 'required|integer|min:15',
            'ubicacion'       => 'nullable|string|max:255',
            'link_virtual'    => 'nullable|url',
            // En tu formulario lo llamas id_proyecto, pero representa el semillero elegido.
            'id_proyecto'     => 'nullable|integer',
            // participantes: arreglo de IDs (opcional)
            'participantes'   => 'nullable|array',
            'participantes.*' => 'integer',
            'tipo'            => 'nullable|string|max:50',
            'linea_investigacion' => 'nullable|string|max:255',
            'recordatorio'    => 'nullable|string|max:50',
        ]);

        $evento = Evento::create($data);

        // Si luego agregas tabla pivote evento_participante, aquí haces attach.
        // if (!empty($data['participantes'])) { $evento->participantes()->sync($data['participantes']); }

        return response()->json([
            'success' => true,
            'evento'  => $evento,
        ]);
    }

    /**
     * Actualiza una reunión existente.
     * PUT /admin/reuniones-lideres/{id}
     */
    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        $data = $request->validate([
            'titulo'          => 'sometimes|required|string|max:255',
            'descripcion'     => 'sometimes|nullable|string',
            'fecha_hora'      => 'sometimes|required|date',
            'duracion'        => 'sometimes|required|integer|min:15',
            'ubicacion'       => 'sometimes|nullable|string|max:255',
            'link_virtual'    => 'sometimes|nullable|url',
            'id_proyecto'     => 'sometimes|nullable|integer',
            'tipo'            => 'sometimes|nullable|string|max:50',
            'linea_investigacion' => 'sometimes|nullable|string|max:255',
            'recordatorio'    => 'sometimes|nullable|string|max:50',
        ]);

        $evento->update($data);

        // Participantes si aplica:
        // if ($request->has('participantes')) {
        //     $evento->participantes()->sync($request->input('participantes', []));
        // }

        return response()->json([
            'success' => true,
            'evento'  => $evento,
        ]);
    }

    /**
     * Elimina una reunión.
     * DELETE /admin/reuniones-lideres/{id}
     */
    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        $evento->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Genera/guarda un enlace de reunión (ej. Teams/Meet) para un evento.
     * POST /admin/reuniones-lideres/{id}/generar-enlace
     * Respuesta: { link: "..." }
     */
    public function generarEnlace($id)
    {
        $evento = Evento::findOrFail($id);

        // Ejemplo simple: genera un enlace único ficticio.
        $evento->link_virtual = 'https://teams.microsoft.com/l/reunion/' . uniqid();
        $evento->save();

        return response()->json(['link' => $evento->link_virtual]);
    }
}
