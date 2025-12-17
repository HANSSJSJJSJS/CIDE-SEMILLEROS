<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return view('Admin.Reuniones.calendario_admin', compact('lideres'));
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

        $investigacion = DB::table('lideres_investigacion as li')
            ->join('users as u', 'u.id', '=', 'li.user_id')
            ->select(
                'u.id as id',
                DB::raw("CONCAT(u.nombre, ' ', u.apellidos) as nombre"),
                'u.email',
                'u.role'
            )
            ->orderBy('u.nombre');

        if (!$sid) {
            // Todos los líderes con su nombre y correo (desde users)
            $rows = DB::table('lideres_semillero as l')
                ->join('users as u', 'u.id', '=', 'l.id_lider_semi')
                ->select(
                    'l.id_lider_semi as id',
                    DB::raw("CONCAT(u.nombre, ' ', u.apellidos) as nombre"),
                    'u.email',
                    'u.role'
                )
                ->orderBy('u.nombre')
                ->get();

            $inv = $investigacion->get();

            $all = $rows->merge($inv)->unique('id')->values();

            return response()->json(['data' => $all]);
        }

        // Líder del semillero seleccionado
        $lider = DB::table('lideres_semillero as l')
            ->join('users as u', 'u.id', '=', 'l.id_lider_semi')
            ->select(
                'l.id_lider_semi as id',
                DB::raw("CONCAT(u.nombre, ' ', u.apellidos) as nombre"),
                'u.email',
                'u.role'
            )
            ->where('l.id_semillero', $sid)
            ->first();

        $base = $lider ? collect([ $lider ]) : collect();
        $inv = $investigacion->get();
        $all = $base->merge($inv)->unique('id')->values();

        return response()->json(['data' => $all]);
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

        $baseQuery = Evento::query()
            ->whereMonth('fecha_hora', $mes)
            ->whereYear('fecha_hora', $anio);

        $user = Auth::user();
        if ($user && $user->role === 'LIDER_INVESTIGACION') {
            $ids = DB::table('evento_asignaciones')
                ->where('tipo_destino', 'LIDER_INVESTIGACION')
                ->where('destino_id', $user->id)
                ->pluck('evento_id');

            $baseQuery->whereIn('id_evento', $ids);
        }

        $eventos = $baseQuery->get();

        $eventoIds = $eventos->pluck('id_evento')->filter()->values();
        $asignaciones = $eventoIds->isEmpty()
            ? collect()
            : DB::table('evento_asignaciones')
                ->whereIn('evento_id', $eventoIds)
                ->whereIn('tipo_destino', ['LIDER_SEMILLERO', 'LIDER_INVESTIGACION'])
                ->select('evento_id', 'destino_id')
                ->get()
                ->groupBy('evento_id');

        $eventos = $eventos->map(function ($ev) use ($asignaciones) {
            $parts = $asignaciones->get($ev->id_evento, collect())->pluck('destino_id')->values();
            $ev->participantes = $parts;
            return $ev;
        });

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

        $participantes = $data['participantes'] ?? [];
        unset($data['participantes']);

        $evento = null;
        DB::transaction(function () use ($data, $participantes, &$evento) {
            $evento = Evento::create($data);

            $uids = collect($participantes)->filter()->unique()->values();
            if ($uids->isNotEmpty()) {
                $roles = DB::table('users')->whereIn('id', $uids)->pluck('role', 'id');
                $rows = [];
                foreach ($uids as $uid) {
                    $role = (string) ($roles[$uid] ?? '');
                    if ($role === 'LIDER_SEMILLERO') {
                        $tipo = 'LIDER_SEMILLERO';
                    } elseif ($role === 'LIDER_INVESTIGACION') {
                        $tipo = 'LIDER_INVESTIGACION';
                    } else {
                        continue;
                    }
                    $rows[] = [
                        'evento_id' => $evento->id_evento,
                        'tipo_destino' => $tipo,
                        'destino_id' => (int) $uid,
                        'asignado_por' => Auth::id(),
                        'created_at' => now(),
                    ];
                }
                if (!empty($rows)) {
                    DB::table('evento_asignaciones')->insert($rows);
                }
            }
        });

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
            'participantes'   => 'sometimes|nullable|array',
            'participantes.*' => 'integer',
            'tipo'            => 'sometimes|nullable|string|max:50',
            'linea_investigacion' => 'sometimes|nullable|string|max:255',
            'recordatorio'    => 'sometimes|nullable|string|max:50',
        ]);

        $participantes = null;
        if (array_key_exists('participantes', $data)) {
            $participantes = $data['participantes'] ?? [];
            unset($data['participantes']);
        }

        DB::transaction(function () use ($evento, $data, $participantes) {
            $evento->update($data);

            if ($participantes !== null) {
                DB::table('evento_asignaciones')
                    ->where('evento_id', $evento->id_evento)
                    ->whereIn('tipo_destino', ['LIDER_SEMILLERO', 'LIDER_INVESTIGACION'])
                    ->delete();

                $uids = collect($participantes)->filter()->unique()->values();
                if ($uids->isNotEmpty()) {
                    $roles = DB::table('users')->whereIn('id', $uids)->pluck('role', 'id');
                    $rows = [];
                    foreach ($uids as $uid) {
                        $role = (string) ($roles[$uid] ?? '');
                        if ($role === 'LIDER_SEMILLERO') {
                            $tipo = 'LIDER_SEMILLERO';
                        } elseif ($role === 'LIDER_INVESTIGACION') {
                            $tipo = 'LIDER_INVESTIGACION';
                        } else {
                            continue;
                        }
                        $rows[] = [
                            'evento_id' => $evento->id_evento,
                            'tipo_destino' => $tipo,
                            'destino_id' => (int) $uid,
                            'asignado_por' => Auth::id(),
                            'created_at' => now(),
                        ];
                    }
                    if (!empty($rows)) {
                        DB::table('evento_asignaciones')->insert($rows);
                    }
                }
            }
        });

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

        DB::transaction(function () use ($evento) {
            DB::table('evento_asignaciones')->where('evento_id', $evento->id_evento)->delete();
            $evento->delete();
        });

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
