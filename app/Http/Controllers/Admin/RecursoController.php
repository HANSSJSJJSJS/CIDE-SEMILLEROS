<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recurso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class RecursoController extends Controller
{
    // ======================================================
    // INDEX – LISTA DE SEMILLEROS + ESTADISTICAS
    // ======================================================
    public function index()
    {
        $semilleros = DB::table('semilleros as s')
            ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 's.id_lider_semi')
            ->leftJoin('users as u', 'u.id', '=', 'ls.id_usuario')
            ->select(
                's.id_semillero',
                's.nombre',
                DB::raw("'Sin descripción' as descripcion"),
                DB::raw("'ACTIVO' as estado"),
                's.id_lider_semi',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellidos,''))) as lider_nombre")
            )
            ->orderBy('s.nombre')
            ->get();

        foreach ($semilleros as $s) {

            $query = Recurso::where('dirigido_a', 'lideres')
                ->where('semillero_id', $s->id_semillero);

            $s->actividades_total      = (clone $query)->count();
            $s->actividades_pendientes = (clone $query)->where('estado', 'pendiente')->count();
            $s->actividades_aprobadas  = (clone $query)->where('estado', 'aprobado')->count();
            $s->actividades_rechazadas = (clone $query)->where('estado', 'rechazado')->count();
        }

        return view('admin.recursos.index', compact('semilleros'));
    }

    // ======================================================
    // GET PROYECTOS – PARA EL MODAL UNIVERSAL
    // ======================================================
    public function getProyectos($id)
    {
        $proyectos = DB::table('proyectos')
            ->where('id_semillero', $id)
            ->select('id_proyecto', 'nombre_proyecto')
            ->get();

        return response()->json($proyectos);
    }

    // ======================================================
    // VER RECURSOS POR SEMILLERO
    // ======================================================
    public function porSemillero($semilleroId)
    {
        if (!Schema::hasTable('recursos')) {
            return response()->json(['actividades' => []]);
        }

        $cols = Schema::getColumnListing('recursos');
        $idCol = null;
        foreach (['id', 'id_recurso', 'id_recursos', 'id_recurso_lider', 'id_recurso_semillero'] as $cand) {
            if (in_array($cand, $cols, true)) {
                $idCol = $cand;
                break;
            }
        }
        if (!$idCol) {
            foreach ($cols as $c) {
                if ($c === 'id' || str_starts_with($c, 'id_')) {
                    $idCol = $c;
                    break;
                }
            }
        }
        if (!$idCol) {
            $idCol = $cols[0] ?? null;
        }

        $q = DB::table('recursos as r');

        // Join users (tolerante a diferentes nombres de columnas)
        $hasUserId = Schema::hasColumn('recursos', 'user_id');
        $hasUsersTable = Schema::hasTable('users');
        $nombreCol = null;
        $apellidoCol = null;
        if ($hasUsersTable) {
            $nombreCol = Schema::hasColumn('users', 'nombre') ? 'nombre' : (Schema::hasColumn('users', 'name') ? 'name' : null);
            $apellidoCol = Schema::hasColumn('users', 'apellidos') ? 'apellidos' : (Schema::hasColumn('users', 'apellido') ? 'apellido' : null);
        }

        if ($hasUserId && $hasUsersTable) {
            $q->leftJoin('users as u', 'u.id', '=', 'r.user_id');
        }

        // Filtros (tolerante)
        if (Schema::hasColumn('recursos', 'semillero_id')) {
            $q->where('r.semillero_id', $semilleroId);
        } elseif (Schema::hasColumn('recursos', 'id_semillero')) {
            $q->where('r.id_semillero', $semilleroId);
        }

        if (Schema::hasColumn('recursos', 'dirigido_a')) {
            $q->where('r.dirigido_a', 'lideres');
        }

        // SELECT (tolerante)
        $select = [];
        if ($idCol) {
            $select[] = DB::raw("r.$idCol as id");
        } else {
            $select[] = DB::raw('NULL as id');
        }

        if (Schema::hasColumn('recursos', 'nombre_archivo')) {
            $select[] = DB::raw('r.nombre_archivo as titulo');
        } elseif (Schema::hasColumn('recursos', 'titulo')) {
            $select[] = DB::raw('r.titulo as titulo');
        } else {
            $select[] = DB::raw("'' as titulo");
        }

        if (Schema::hasColumn('recursos', 'descripcion')) {
            $select[] = 'r.descripcion';
        } else {
            $select[] = DB::raw("'' as descripcion");
        }

        if (Schema::hasColumn('recursos', 'tipo_documento')) {
            $select[] = 'r.tipo_documento';
        } elseif (Schema::hasColumn('recursos', 'tipo_recurso')) {
            $select[] = DB::raw('r.tipo_recurso as tipo_documento');
        } else {
            $select[] = DB::raw("'' as tipo_documento");
        }

        if (Schema::hasColumn('recursos', 'estado')) {
            $select[] = 'r.estado';
        } else {
            $select[] = DB::raw("'pendiente' as estado");
        }

        if (Schema::hasColumn('recursos', 'archivo')) {
            $select[] = 'r.archivo';
        } else {
            $select[] = DB::raw('NULL as archivo');
        }

        if (Schema::hasColumn('recursos', 'archivo_respuesta')) {
            $select[] = 'r.archivo_respuesta';
        } else {
            $select[] = DB::raw('NULL as archivo_respuesta');
        }

        if (Schema::hasColumn('recursos', 'enlace_respuesta')) {
            $select[] = 'r.enlace_respuesta';
        } else {
            $select[] = DB::raw('NULL as enlace_respuesta');
        }

        if (Schema::hasColumn('recursos', 'comentarios')) {
            $select[] = 'r.comentarios';
        } else {
            $select[] = DB::raw('NULL as comentarios');
        }

        if (Schema::hasColumn('recursos', 'respondido_en')) {
            $select[] = 'r.respondido_en';
        } else {
            $select[] = DB::raw('NULL as respondido_en');
        }

        if (Schema::hasColumn('recursos', 'fecha_vencimiento')) {
            $select[] = DB::raw('DATE(r.fecha_vencimiento) as fecha_limite');
        } elseif (Schema::hasColumn('recursos', 'fecha_limite')) {
            $select[] = DB::raw('DATE(r.fecha_limite) as fecha_limite');
        } else {
            $select[] = DB::raw('NULL as fecha_limite');
        }

        if ($hasUserId && $hasUsersTable && $nombreCol) {
            $apellidoExpr = $apellidoCol ? "COALESCE(u.$apellidoCol, '')" : "''";
            $select[] = DB::raw("TRIM(CONCAT(COALESCE(u.$nombreCol,''),' ',{$apellidoExpr})) as lider_nombre");
        } else {
            $select[] = DB::raw("'N/A' as lider_nombre");
        }

        $q->select($select);

        // Orden
        if (Schema::hasColumn('recursos', 'fecha_vencimiento')) {
            $q->orderBy('r.fecha_vencimiento', 'desc');
        } elseif (Schema::hasColumn('recursos', 'created_at')) {
            $q->orderBy('r.created_at', 'desc');
        } elseif ($idCol) {
            $q->orderBy('r.' . $idCol, 'desc');
        }

        $recursos = $q->get()->map(function ($r) {
            $hoy = now()->toDateString();

            $r->estado = strtolower($r->estado ?? 'pendiente');
            $r->fecha_limite = $r->fecha_limite ?: '—';

            // Evitar que el front intente descargar un placeholder
            if (isset($r->archivo) && ($r->archivo === 'sin_archivo' || $r->archivo === '')) {
                $r->archivo = null;
            }

            if (isset($r->archivo_respuesta) && ($r->archivo_respuesta === '' || $r->archivo_respuesta === 'sin_archivo')) {
                $r->archivo_respuesta = null;
            }

            if (isset($r->enlace_respuesta) && $r->enlace_respuesta === '') {
                $r->enlace_respuesta = null;
            }

            if ($r->estado === 'pendiente' && $r->fecha_limite !== '—' && $r->fecha_limite < $hoy) {
                $r->estado = 'vencido';
            }

            return $r;
        });

        return response()->json(['actividades' => $recursos]);
    }

    // ======================================================
    // CREAR RECURSO (ADMIN / LÍDER GENERAL)
    // ======================================================
    public function storeActividad(Request $request)
    {
        $request->validate([
            'semillero_id' => 'required|exists:semilleros,id_semillero',
            'proyecto_id'  => 'required|exists:proyectos,id_proyecto',
            'categoria'    => 'required|in:plantillas,manuales,otros',
            'tipo_documento' => 'required|in:pdf,enlace,documento,presentacion,video,imagen,otro',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'fecha_limite' => 'required|date|after_or_equal:today'
        ]);

        try {
            if (Schema::hasTable('recursos') && !Schema::hasColumn('recursos', 'tipo_documento')) {
                Schema::table('recursos', function ($table) {
                    $table->string('tipo_documento', 50)->nullable()->after('categoria');
                });
            }
        } catch (\Throwable $e) {
            // continuar sin alterar esquema
        }

        $data = [
            'nombre_archivo'    => $request->titulo,
            'archivo'           => 'sin_archivo',
            'categoria'         => $request->categoria,
            'dirigido_a'        => 'lideres',
            'estado'            => 'pendiente',
            'semillero_id'      => $request->semillero_id,
            'proyecto_id'       => $request->proyecto_id,
            'fecha_vencimiento' => $request->fecha_limite,
            'descripcion'       => $request->descripcion,
            'user_id'           => Auth::id(),
        ];

        if (Schema::hasTable('recursos') && Schema::hasColumn('recursos', 'tipo_documento')) {
            $data['tipo_documento'] = $request->tipo_documento;
        }

        Recurso::create($data);

        return response()->json(['success' => true]);
    }

    public function destroy($recurso)
    {
        if (!Schema::hasTable('recursos')) {
            return response()->json(['success' => false, 'message' => 'Tabla recursos no encontrada'], 404);
        }

        $cols = Schema::getColumnListing('recursos');
        $idCol = null;
        foreach (['id_recurso', 'id', 'id_recursos', 'id_recurso_lider', 'id_recurso_semillero'] as $cand) {
            if (in_array($cand, $cols, true)) {
                $idCol = $cand;
                break;
            }
        }
        if (!$idCol) {
            foreach ($cols as $c) {
                if ($c === 'id' || str_starts_with($c, 'id_')) {
                    $idCol = $c;
                    break;
                }
            }
        }
        if (!$idCol) {
            return response()->json(['success' => false, 'message' => 'No se pudo determinar la llave del recurso'], 500);
        }

        $row = DB::table('recursos')->where($idCol, $recurso)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Recurso no encontrado'], 404);
        }

        $estado = strtolower((string)($row->estado ?? 'pendiente'));
        $vencido = ($estado === 'vencido');

        $hoy = now()->toDateString();
        if (!$vencido) {
            if ($estado === 'pendiente') {
                $fechaCol = null;
                foreach (['fecha_vencimiento', 'fecha_limite'] as $cand) {
                    if (in_array($cand, $cols, true)) {
                        $fechaCol = $cand;
                        break;
                    }
                }
                if ($fechaCol) {
                    try {
                        $f = (string)($row->{$fechaCol} ?? '');
                        $f = $f ? substr($f, 0, 10) : '';
                        if ($f !== '' && $f < $hoy) {
                            $vencido = true;
                        }
                    } catch (\Throwable $e) {
                        $vencido = false;
                    }
                }
            }
        }

        if (!$vencido) {
            return response()->json(['success' => false, 'message' => 'Solo se pueden eliminar recursos vencidos'], 403);
        }

        try {
            $paths = [];
            if (in_array('archivo', $cols, true) && !empty($row->archivo) && $row->archivo !== 'sin_archivo') {
                $paths[] = (string)$row->archivo;
            }
            if (in_array('archivo_respuesta', $cols, true) && !empty($row->archivo_respuesta) && $row->archivo_respuesta !== 'sin_archivo') {
                $paths[] = (string)$row->archivo_respuesta;
            }

            foreach ($paths as $p) {
                if ($p && Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }
        } catch (\Throwable $e) {
            // continuar
        }

        DB::table('recursos')->where($idCol, $recurso)->delete();

        return response()->json(['success' => true]);
    }

    // ======================================================
    // ACTUALIZAR ESTADO DEL RECURSO
    // ======================================================
    public function actualizarEstadoActividad(Request $request, $recurso)
    {
        $request->validate([
            'estado'      => 'required|in:pendiente,aprobado,rechazado',
            'comentarios' => 'nullable|string|max:500',
        ]);

        if (!Schema::hasTable('recursos')) {
            return response()->json(['success' => false, 'message' => 'Tabla recursos no encontrada'], 404);
        }

        $cols = Schema::getColumnListing('recursos');
        $idCol = null;
        foreach (['id', 'id_recurso', 'id_recursos', 'id_recurso_lider', 'id_recurso_semillero'] as $cand) {
            if (in_array($cand, $cols, true)) {
                $idCol = $cand;
                break;
            }
        }
        if (!$idCol) {
            foreach ($cols as $c) {
                if ($c === 'id' || str_starts_with($c, 'id_')) {
                    $idCol = $c;
                    break;
                }
            }
        }
        if (!$idCol) {
            return response()->json(['success' => false, 'message' => 'No se pudo determinar la llave del recurso'], 500);
        }

        $row = DB::table('recursos')->where($idCol, $recurso)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Recurso no encontrado'], 404);
        }

        if (Schema::hasColumn('recursos', 'dirigido_a') && (($row->dirigido_a ?? null) !== 'lideres')) {
            return response()->json(['success' => false, 'message' => 'No es un recurso de líder'], 400);
        }

        $update = [];
        if (Schema::hasColumn('recursos', 'estado')) {
            $update['estado'] = $request->estado;
        }
        if (Schema::hasColumn('recursos', 'comentarios')) {
            $update['comentarios'] = $request->comentarios;
        }

        if (empty($update)) {
            return response()->json(['success' => false, 'message' => 'No hay columnas actualizables'], 400);
        }

        DB::table('recursos')->where($idCol, $recurso)->update($update);

        return response()->json(['success' => true]);
    }

    // ======================================================
    // EDITAR RECURSO (SOLO SI NO ESTÁ APROBADO)
    // ======================================================
    public function actualizarRecurso(Request $request, $recurso)
    {
        $request->validate([
            'tipo_documento' => 'required|in:pdf,enlace,documento,presentacion,video,imagen,otro',
            'fecha_limite'   => 'required|date',
            'descripcion'    => 'required|string',
        ]);

        if (!Schema::hasTable('recursos')) {
            return response()->json(['success' => false, 'message' => 'Tabla recursos no encontrada'], 404);
        }

        $cols = Schema::getColumnListing('recursos');
        $idCol = null;
        foreach (['id', 'id_recurso', 'id_recursos', 'id_recurso_lider', 'id_recurso_semillero'] as $cand) {
            if (in_array($cand, $cols, true)) {
                $idCol = $cand;
                break;
            }
        }
        if (!$idCol) {
            foreach ($cols as $c) {
                if ($c === 'id' || str_starts_with($c, 'id_')) {
                    $idCol = $c;
                    break;
                }
            }
        }
        if (!$idCol) {
            return response()->json(['success' => false, 'message' => 'No se pudo determinar la llave del recurso'], 500);
        }

        $row = DB::table('recursos')->where($idCol, $recurso)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Recurso no encontrado'], 404);
        }

        $estado = strtolower((string)($row->estado ?? ''));
        if ($estado === 'aprobado') {
            return response()->json(['success' => false, 'message' => 'No puedes editar un recurso aprobado'], 403);
        }

        $fechaColActual = null;
        foreach (['fecha_vencimiento', 'fecha_limite'] as $cand) {
            if (in_array($cand, $cols, true)) {
                $fechaColActual = $cand;
                break;
            }
        }

        $fechaAntes = '';
        if ($fechaColActual) {
            try {
                $f = (string)($row->{$fechaColActual} ?? '');
                $fechaAntes = $f ? substr($f, 0, 10) : '';
            } catch (\Throwable $e) {
                $fechaAntes = '';
            }
        }

        $fechaNueva = '';
        try {
            $fechaNueva = substr((string)$request->fecha_limite, 0, 10);
        } catch (\Throwable $e) {
            $fechaNueva = (string)$request->fecha_limite;
        }

        $update = [];

        if (Schema::hasColumn('recursos', 'tipo_documento')) {
            $update['tipo_documento'] = $request->tipo_documento;
        } elseif (Schema::hasColumn('recursos', 'tipo_recurso')) {
            $update['tipo_recurso'] = $request->tipo_documento;
        }

        if (Schema::hasColumn('recursos', 'fecha_vencimiento')) {
            $update['fecha_vencimiento'] = $request->fecha_limite;
        } elseif (Schema::hasColumn('recursos', 'fecha_limite')) {
            $update['fecha_limite'] = $request->fecha_limite;
        }

        if (Schema::hasColumn('recursos', 'descripcion')) {
            $update['descripcion'] = $request->descripcion;
        }

        // Si se cambió la fecha, reabrir para que el líder pueda responder de nuevo
        // (limpia evidencia anterior y vuelve a pendiente)
        if ($fechaAntes !== '' && $fechaNueva !== '' && $fechaAntes !== $fechaNueva) {
            // Eliminar archivos viejos (si existen) para evitar basura en storage
            try {
                if (Schema::hasColumn('recursos', 'archivo_respuesta') && !empty($row->archivo_respuesta)) {
                    $old = (string)$row->archivo_respuesta;
                    if ($old !== '' && Storage::disk('public')->exists($old)) {
                        Storage::disk('public')->delete($old);
                    }
                }

                // Si la evidencia quedó en `archivo` (fallback), solo borrarla si parece una respuesta
                // (ruta típica: recursos/respuestas/...).
                if (Schema::hasColumn('recursos', 'archivo') && !empty($row->archivo)) {
                    $oldA = (string)$row->archivo;
                    if ($oldA !== '' && stripos($oldA, 'recursos/respuestas') !== false) {
                        if (Storage::disk('public')->exists($oldA)) {
                            Storage::disk('public')->delete($oldA);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // continuar
            }

            if (Schema::hasColumn('recursos', 'archivo_respuesta')) {
                $update['archivo_respuesta'] = null;
            }
            if (Schema::hasColumn('recursos', 'enlace_respuesta')) {
                $update['enlace_respuesta'] = null;
            }
            if (Schema::hasColumn('recursos', 'respuesta')) {
                $update['respuesta'] = null;
            }
            if (Schema::hasColumn('recursos', 'respondido_en')) {
                $update['respondido_en'] = null;
            }

            // Limpiar fallbacks de evidencia en comentarios, conservando observaciones si existen
            if (Schema::hasColumn('recursos', 'comentarios')) {
                try {
                    $c = (string)($row->comentarios ?? '');
                    if ($c !== '') {
                        $c = preg_replace('/\r?\n?Enlace respuesta:\s*https?:\/\/[^\s]+/i', '', $c);
                        $c = preg_replace('/\r?\n?Archivo respuesta:\s*[^\r\n]+/i', '', $c);
                        $c = trim((string)$c);
                        $update['comentarios'] = ($c === '') ? null : $c;
                    }
                } catch (\Throwable $e) {
                    // continuar
                }
            }

            // Si no existe archivo_respuesta, puede que la respuesta esté en `archivo`.
            // Solo lo limpiamos si parece evidencia de respuesta.
            if (!Schema::hasColumn('recursos', 'archivo_respuesta') && Schema::hasColumn('recursos', 'archivo')) {
                $oldA = (string)($row->archivo ?? '');
                if ($oldA !== '' && stripos($oldA, 'recursos/respuestas') !== false) {
                    $update['archivo'] = null;
                }
            }

            if (Schema::hasColumn('recursos', 'estado')) {
                $update['estado'] = 'pendiente';
            }
        }

        if (empty($update)) {
            return response()->json(['success' => false, 'message' => 'No hay columnas editables'], 400);
        }

        DB::table('recursos')->where($idCol, $recurso)->update($update);

        return response()->json(['success' => true]);
    }

    // ======================================================
    // SUBIR MULTIMEDIA
    // ======================================================
    public function storeMultimedia(Request $request)
    {
        $request->validate([
            'titulo'     => 'required|string|max:255',
            'categoria'  => 'required|in:plantillas,manuales,otros',
            'archivo'    => 'required|file|max:20480',
            'destino'    => 'required|in:todos,semillero',
            'descripcion'=> 'nullable|string',
            'semillero_id' => 'required_if:destino,semillero|nullable|exists:semilleros,id_semillero'
        ]);

        $path = $request->file('archivo')->store('multimedia', 'public');

        DB::table('recursos')->insert([
            'nombre_archivo' => $request->titulo,
            'archivo'        => $path,
            'categoria'      => $request->categoria,
            'dirigido_a'     => $request->destino,
            'semillero_id'   => $request->destino === 'semillero' ? $request->semillero_id : null,
            'descripcion'    => $request->descripcion,
            'user_id'        => auth()->id(),
            'estado'         => 'pendiente',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return response()->json(['success' => true]);
    }

    // ======================================================
    // VISTA MULTIMEDIA
    // ======================================================
    public function multimedia()
    {
        $semilleros = DB::table('semilleros')
            ->select('id_semillero','nombre')
            ->get();

        // ★★ IMPORTANTE: vista correcta según tu estructura ★★
        return view('admin.recursos.multimedia.multimedia', compact('semilleros'));
    }

    // ======================================================
    // LISTAR MULTIMEDIA (AJAX)
    // ======================================================
    public function obtenerMultimedia()
    {
        $recursos = DB::table('recursos')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($recursos);
    }

    // ======================================================
    // ELIMINAR MULTIMEDIA
    // ======================================================
    public function deleteMultimedia($id)
    {
        $recurso = DB::table('recursos')->where('id', $id)->first();

        if (!$recurso) {
            return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
        }

        if ($recurso->archivo && Storage::disk('public')->exists($recurso->archivo)) {
            Storage::disk('public')->delete($recurso->archivo);
        }

        DB::table('recursos')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }

    // ======================================================
    // OBTENER LÍDER
    // ======================================================
    public function liderDeSemillero($id)
    {
        $lider = DB::table('semilleros as s')
            ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 's.id_lider_semi')
            ->leftJoin('users as u', 'u.id', '=', 'ls.id_usuario')
            ->where('s.id_semillero', $id)
            ->select(
                'u.id',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellidos,''))) as nombre_completo")
            )
            ->first();

        return response()->json([
            'success' => true,
            'lider' => $lider ? [
                'id' => $lider->id,
                'nombre_completo' => $lider->nombre_completo,
            ] : null,
        ]);
    }
}
