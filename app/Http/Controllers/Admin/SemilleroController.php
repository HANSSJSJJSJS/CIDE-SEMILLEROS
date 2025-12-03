<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class SemilleroController extends Controller
{
    // ============================================================
    // LISTADO
    // ============================================================
    public function index(Request $request)
    {
        $q    = $request->get('q');
        $sNom = $request->get('sNom');

        // columnas permitidas para ordenar
        $allowedOrder = ['nombre', 'linea_investigacion'];
        if (! in_array($sNom, $allowedOrder)) {
            $sNom = 'nombre';
        }

        $semillerosQuery = DB::table('semilleros as s')
            ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 's.id_lider_semi')
            ->leftJoin('users as u', 'u.id', '=', 'ls.id_lider_semi')
            ->select(
                's.id_semillero',
                's.nombre',
                's.linea_investigacion',
                's.id_lider_semi',
                'ls.correo_institucional as lider_correo',
                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(u.nombre, ''), ' ',
                            COALESCE(u.apellidos, '')
                        )
                    ) as lider_nombre
                ")
            );

        if (! empty($q)) {
            $semillerosQuery->where(function ($sub) use ($q) {
                $sub->where('s.nombre', 'like', "%{$q}%")
                    ->orWhere('s.linea_investigacion', 'like', "%{$q}%")
                    ->orWhere('ls.correo_institucional', 'like', "%{$q}%")
                    ->orWhere(
                        DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellidos,'')))"),
                        'like',
                        "%{$q}%"
                    );
            });
        }

        $semilleros = $semillerosQuery
            ->orderBy('s.' . $sNom, 'asc')
            ->paginate(12)
            ->withQueryString();

        // líderes disponibles para el modal "nuevo semillero"
        $lideresDisponibles = DB::table('lideres_semillero as ls')
            ->leftJoin('users as u', 'u.id', '=', 'ls.id_lider_semi')
            ->whereNull('ls.id_semillero')
            ->select(
                'ls.id_lider_semi',
                'ls.correo_institucional',
                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(u.nombre, ''), ' ',
                            COALESCE(u.apellidos, '')
                        )
                    ) as nombre_completo
                ")
            )
            ->orderBy('nombre_completo')
            ->get();

        return view('admin.semilleros.index', [
            'semilleros'         => $semilleros,
            'q'                  => $q,
            'sNom'               => $sNom,
            'lideresDisponibles' => $lideresDisponibles,
        ]);
    }

    // ============================================================
    // GUARDAR NUEVO
    // ============================================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'             => 'required|string|max:255|unique:semilleros,nombre',
            'linea_investigacion'=> 'required|string|max:255',
            'id_lider_semi'      => 'nullable|exists:lideres_semillero,id_lider_semi',
        ]);

        DB::transaction(function () use ($data) {

            $idSemillero = DB::table('semilleros')->insertGetId([
                'nombre'             => $data['nombre'],
                'linea_investigacion'=> $data['linea_investigacion'],
                'id_lider_semi'      => $data['id_lider_semi'] ?? null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // si asigna líder, lo marcamos como ocupado
            if (!empty($data['id_lider_semi'])) {
                DB::table('lideres_semillero')
                    ->where('id_lider_semi', $data['id_lider_semi'])
                    ->update([
                        'id_semillero'  => $idSemillero,
                        'actualizado_en'=> now(),
                    ]);
            }
        });

        return redirect()
            ->route('admin.semilleros.index')
            ->with('success', 'Semillero creado correctamente.');
    }

    // ============================================================
    // EDITAR AJAX (para el modal editar)
    // ============================================================
public function editAjax($id)
{
    try {
        $semillero = DB::table('semilleros as s')
            ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 's.id_lider_semi')
            ->leftJoin('users as u', 'u.id', '=', 'ls.id_lider_semi')
            ->where('s.id_semillero', $id)
            ->select(
                's.id_semillero',
                's.nombre',
                's.linea_investigacion',
                's.id_lider_semi',
                'ls.correo_institucional as lider_correo',
                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(u.nombre, ''), ' ',
                            COALESCE(u.apellidos, '')
                        )
                    ) as lider_nombre
                ")
            )
            ->first();

        if (! $semillero) {
            return response()->json([
                'message' => 'Semillero no encontrado',
            ], 404);
        }

        // ✅ devolvemos directamente el objeto
        return response()->json($semillero);

    } catch (\Throwable $e) {
        // Log para ver en storage/logs/laravel.log
        \Log::error('Error en SemilleroController@editAjax', [
            'id'        => $id,
            'mensaje'   => $e->getMessage(),
            'archivo'   => $e->getFile(),
            'linea'     => $e->getLine(),
        ]);

        return response()->json([
            'message' => $e->getMessage(), // aquí viene el SQLSTATE o lo que sea
        ], 500);
    }
}




    // ============================================================
    // ACTUALIZAR
    // ============================================================
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre'             => 'required|string|max:255|unique:semilleros,nombre,' . $id . ',id_semillero',
            'linea_investigacion'=> 'required|string|max:255',
            'id_lider_semi'      => 'nullable|exists:lideres_semillero,id_lider_semi',
        ]);

        DB::transaction(function () use ($data, $id) {

            // limpiar cualquier líder que tuviera este semillero
            DB::table('lideres_semillero')
                ->where('id_semillero', $id)
                ->update([
                    'id_semillero'  => null,
                    'actualizado_en'=> now(),
                ]);

            // actualizar semillero
            DB::table('semilleros')
                ->where('id_semillero', $id)
                ->update([
                    'nombre'             => $data['nombre'],
                    'linea_investigacion'=> $data['linea_investigacion'],
                    'id_lider_semi'      => $data['id_lider_semi'] ?? null,
                    'updated_at'         => now(),
                ]);

            // asignar nuevo líder si viene
            if (!empty($data['id_lider_semi'])) {
                DB::table('lideres_semillero')
                    ->where('id_lider_semi', $data['id_lider_semi'])
                    ->update([
                        'id_semillero'  => $id,
                        'actualizado_en'=> now(),
                    ]);
            }
        });

        return redirect()
            ->route('admin.semilleros.index')
            ->with('success', 'Semillero actualizado correctamente.');
    }

    // ============================================================
    // ELIMINAR
    // ============================================================
    public function destroy($id)
    {
        try {
            // 1) Bloquear si hay proyectos asociados
            $tieneProyectos = Schema::hasTable('proyectos')
                ? DB::table('proyectos')->where('id_semillero', $id)->exists()
                : false;

            if ($tieneProyectos) {
                return back()->with('error', 'No se puede eliminar el semillero porque tiene proyectos asociados. Elimina o reubica los proyectos primero.');
            }

            DB::transaction(function () use ($id) {
                // 2) Desasignar aprendices (1:N) si existe la columna
                if (Schema::hasTable('aprendices') && Schema::hasColumn('aprendices', 'semillero_id')) {
                    DB::table('aprendices')
                        ->where('semillero_id', $id)
                        ->update([
                            'semillero_id'  => null,
                            'actualizado_en'=> now(),
                        ]);
                }

                // 2b) Si existiera tabla pivote aprendiz_semillero, limpiar
                if (Schema::hasTable('aprendiz_semillero')) {
                    DB::table('aprendiz_semillero')
                        ->where('id_semillero', $id)
                        ->delete();
                }

                // 3) Desasignar líder de este semillero (si lo hay)
                if (Schema::hasTable('lideres_semillero')) {
                    DB::table('lideres_semillero')
                        ->where('id_semillero', $id)
                        ->update([
                            'id_semillero'  => null,
                            'actualizado_en'=> now(),
                        ]);
                }

                // 4) Eliminar semillero
                DB::table('semilleros')
                    ->where('id_semillero', $id)
                    ->delete();
            });

            return redirect()
                ->route('admin.semilleros.index')
                ->with('success', 'Semillero eliminado correctamente.');

        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo eliminar el semillero: ' . $e->getMessage());
        }
    }

    // ============================================================
    // LÍDERES DISPONIBLES (para ambos modales)
    // ============================================================
    public function lideresDisponibles(Request $request)
    {
        $q             = trim($request->get('q', ''));
        $includeActual = $request->integer('include_current'); // para el modal editar

        $query = DB::table('lideres_semillero as ls')
            ->leftJoin('users as u', 'u.id', '=', 'ls.id_lider_semi');

        // Líderes libres + opcionalmente el actual
        $query->where(function ($w) use ($includeActual) {
            $w->whereNull('ls.id_semillero');
            if ($includeActual) {
                $w->orWhere('ls.id_lider_semi', $includeActual);
            }
        });

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('u.nombre', 'like', "%{$q}%")
                  ->orWhere('u.apellidos', 'like', "%{$q}%")
                  ->orWhere('ls.correo_institucional', 'like', "%{$q}%")
                  ->orWhere('u.email', 'like', "%{$q}%");
            });
        }

        $rows = $query
            ->orderBy('u.nombre')
            ->limit(20)
            ->get();

        $items = $rows->map(function ($r) {
            $nombre = trim(($r->nombre ?? '') . ' ' . ($r->apellidos ?? ''));
            if ($nombre === '') {
                $nombre = '(Sin nombre)';
            }

            return [
                'id_lider_semi' => $r->id_lider_semi,
                'nombre'        => $nombre,
                'correo'        => $r->correo_institucional ?? $r->email,
            ];
        });

        return response()->json([
            'ok'    => true,
            'items' => $items,
        ]);
    }
    











}
