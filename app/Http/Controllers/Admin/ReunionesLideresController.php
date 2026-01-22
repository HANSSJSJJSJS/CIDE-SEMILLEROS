<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

            $baseQuery->where(function ($q) use ($ids) {
                $q->whereIn('id_evento', $ids);
                if (Schema::hasColumn('eventos', 'id_lider_semi')) {
                    $q->orWhereNotNull('id_lider_semi');
                }
            });
        }

        $eventos = $baseQuery->get();

        $eventoIds = $eventos->pluck('id_evento')->filter()->values();

        $aprRowsByEvento = collect();
        $leaderIdsFromParts = collect();
        if ($eventoIds->isNotEmpty() && Schema::hasTable('evento_participantes')) {
            try {
                $aprCols = [];
                if (Schema::hasTable('aprendices')) {
                    if (Schema::hasColumn('aprendices', 'nombres')) { $aprCols[] = 'a.nombres'; }
                    if (Schema::hasColumn('aprendices', 'apellidos')) { $aprCols[] = 'a.apellidos'; }
                    if (Schema::hasColumn('aprendices', 'correo_institucional')) { $aprCols[] = 'a.correo_institucional'; }
                }

                $aprQuery = DB::table('evento_participantes as ep')
                    ->whereIn('ep.id_evento', $eventoIds);

                if (Schema::hasTable('aprendices')) {
                    $aprQuery->leftJoin('aprendices as a', 'a.id_aprendiz', '=', 'ep.id_aprendiz');
                }

                $aprRows = $aprQuery
                    ->select(array_merge(['ep.id_evento', 'ep.id_aprendiz', 'ep.asistencia', 'ep.id_lider_semi'], $aprCols))
                    ->get();

                $aprRowsByEvento = $aprRows->groupBy('id_evento');
                $leaderIdsFromParts = $aprRows->pluck('id_lider_semi')->filter()->values();
            } catch (\Throwable $e) {
                $aprRowsByEvento = collect();
                $leaderIdsFromParts = collect();
            }
        }

        $leaderUserById = collect();
        try {
            $leaderIds = collect();
            if (Schema::hasColumn('eventos', 'id_lider_semi')) {
                $leaderIds = $leaderIds->merge($eventos->pluck('id_lider_semi'));
            }
            $leaderIds = $leaderIds->merge($leaderIdsFromParts)->filter()->unique()->values();

            if ($leaderIds->isNotEmpty() && Schema::hasTable('users')) {
                $cols = ['id'];
                foreach (['nombre', 'apellidos', 'name', 'email'] as $c) {
                    if (Schema::hasColumn('users', $c)) { $cols[] = $c; }
                }
                $leaderUserById = DB::table('users')->select(array_values(array_unique($cols)))->whereIn('id', $leaderIds)->get()->keyBy('id');
            }
        } catch (\Throwable $e) {
            $leaderUserById = collect();
        }

        $asignaciones = $eventoIds->isEmpty()
            ? collect()
            : DB::table('evento_asignaciones')
                ->whereIn('evento_id', $eventoIds)
                ->whereIn('tipo_destino', ['LIDER_SEMILLERO', 'LIDER_INVESTIGACION'])
                ->select('evento_id', 'destino_id')
                ->get()
                ->groupBy('evento_id');

        $eventos = $eventos->map(function ($ev) use ($asignaciones, $aprRowsByEvento, $leaderUserById) {
            $parts = $asignaciones->get($ev->id_evento, collect())->pluck('destino_id')->values();
            $ev->participantes = $parts;

            $aprParts = collect($aprRowsByEvento->get($ev->id_evento, collect()))->map(function ($r) {
                $full = trim(((string)($r->nombres ?? '')) . ' ' . ((string)($r->apellidos ?? '')));
                $name = $full !== '' ? $full : trim((string)($r->correo_institucional ?? ''));
                if ($name === '') { $name = 'Aprendiz #' . (string)($r->id_aprendiz ?? ''); }
                $st = strtoupper(trim((string)($r->asistencia ?? 'PENDIENTE')));
                $asistencia = $st === 'ASISTIO' ? 'asistio' : ($st === 'NO_ASISTIO' ? 'no_asistio' : 'pendiente');
                return [
                    'id_aprendiz' => (int)($r->id_aprendiz ?? 0),
                    'nombre' => $name,
                    'asistencia' => $asistencia,
                    'id_lider_semi' => (int)($r->id_lider_semi ?? 0),
                ];
            })->filter(function ($p) {
                return (int)($p['id_aprendiz'] ?? 0) > 0;
            })->values();

            $ev->participantes_detalle = $aprParts->values()->all();

            $lid = null;
            if (Schema::hasColumn('eventos', 'id_lider_semi') && !empty($ev->id_lider_semi)) {
                $lid = (int)$ev->id_lider_semi;
            } elseif ($aprParts->isNotEmpty()) {
                $lid = (int)($aprParts->first()['id_lider_semi'] ?? 0);
            }

            $ev->lider_semillero = null;
            if ($lid && $leaderUserById->has($lid)) {
                $u = $leaderUserById->get($lid);
                $nombre = trim(((string)($u->nombre ?? ($u->name ?? ''))) . ' ' . ((string)($u->apellidos ?? '')));
                if ($nombre === '') { $nombre = trim((string)($u->email ?? '')); }
                if ($nombre === '') { $nombre = 'Usuario #' . (string)$lid; }
                $ev->lider_semillero = [
                    'id' => $lid,
                    'nombre' => $nombre,
                    'email' => $u->email ?? null,
                ];
            }

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
            'recordatorio'    => 'nullable|string|max:50',
        ]);

        if (!Schema::hasColumn('eventos', 'linea_investigacion')) {
            unset($data['linea_investigacion']);
        }

        if (array_key_exists('tipo', $data)) {
            $allowedTipos = ['REUNION', 'CAPACITACION', 'SEGUIMIENTO', 'ENTREGA', 'OTRO'];
            $t = strtoupper(trim((string) ($data['tipo'] ?? '')));
            if ($t === '') {
                unset($data['tipo']);
            } elseif (!in_array($t, $allowedTipos, true)) {
                $data['tipo'] = 'REUNION';
            } else {
                $data['tipo'] = $t;
            }
        }

        if (Schema::hasColumn('eventos', 'id_admin')) {
            $data['id_admin'] = Auth::id();
        }
        if (Schema::hasColumn('eventos', 'creado_por')) {
            $data['creado_por'] = Auth::id();
        }
        if (Schema::hasColumn('eventos', 'recordatorio')) {
            $rec = $data['recordatorio'] ?? null;
            if ($rec === null || $rec === '') {
                $data['recordatorio'] = 'none';
            }
        }

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

        if (Schema::hasColumn('eventos', 'id_lider_semi') && !is_null($evento->id_lider_semi)) {
            if (!Schema::hasColumn('eventos', 'id_admin') || is_null($evento->id_admin)) {
                abort(403);
            }
        }

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
            'recordatorio'    => 'sometimes|nullable|string|max:50',
        ]);

        if (!Schema::hasColumn('eventos', 'linea_investigacion')) {
            unset($data['linea_investigacion']);
        }

        if (array_key_exists('tipo', $data)) {
            $allowedTipos = ['REUNION', 'CAPACITACION', 'SEGUIMIENTO', 'ENTREGA', 'OTRO'];
            $t = strtoupper(trim((string) ($data['tipo'] ?? '')));
            if ($t === '') {
                unset($data['tipo']);
            } elseif (!in_array($t, $allowedTipos, true)) {
                $data['tipo'] = 'REUNION';
            } else {
                $data['tipo'] = $t;
            }
        }

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

        if (Schema::hasColumn('eventos', 'id_lider_semi') && !is_null($evento->id_lider_semi)) {
            if (!Schema::hasColumn('eventos', 'id_admin') || is_null($evento->id_admin)) {
                abort(403);
            }
        }

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

        if (Schema::hasColumn('eventos', 'id_lider_semi') && !is_null($evento->id_lider_semi)) {
            if (!Schema::hasColumn('eventos', 'id_admin') || is_null($evento->id_admin)) {
                abort(403);
            }
        }

        // Ejemplo simple: genera un enlace único ficticio.
        $evento->link_virtual = 'https://teams.microsoft.com/l/reunion/' . uniqid();
        $evento->save();

        return response()->json(['link' => $evento->link_virtual]);
    }
}
