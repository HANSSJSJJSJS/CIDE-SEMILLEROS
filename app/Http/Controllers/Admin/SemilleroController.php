<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SemilleroController extends Controller
{
    /**
     * Muestra la lista de semilleros con filtros y unión opcional a líderes.
     */
    public function index(Request $request)
    {
        $q     = trim($request->get('q', ''));
        $linea = trim($request->get('linea', ''));
        $lider = trim($request->get('lider', ''));

        // 🔍 Detectar columnas reales de la tabla semilleros
        $idCol = Schema::hasColumn('semilleros', 'id')
            ? 'id'
            : (Schema::hasColumn('semilleros', 'id_semillero') ? 'id_semillero' : null);

        $fkCol = collect(['id_lider_usuario', 'id_lider_semi', 'id_lider', 'lider_id', 'id_lidergen'])
            ->first(fn($c) => Schema::hasColumn('semilleros', $c));

        // Resolver columnas de nombre y línea según existan en el esquema
        $colNombre = Schema::hasColumn('semilleros', 'nombre')
            ? 'nombre'
            : (Schema::hasColumn('semilleros', 'nombre_semillero') ? 'nombre_semillero' : null);
        $colLinea = Schema::hasColumn('semilleros', 'linea_investigacion')
            ? 'linea_investigacion'
            : (Schema::hasColumn('semilleros', 'línea_investigación') ? 'línea_investigación' : null);

        $hasNombre  = $colNombre !== null;
        $hasLinea   = $colLinea !== null;
        $hasCreated = Schema::hasColumn('semilleros', 'created_at');

        // 🧱 Query base
        $query = DB::table('semilleros as s');

        // JOIN solo si existe la columna de líder
        if ($fkCol) {
            $query->leftJoin('lideres_semillero as l', 'l.id_usuario', '=', DB::raw("s.$fkCol"));
        }
            // columnas opcionales en lideres_semillero
            $hasLiderCel = Schema::hasColumn('lideres_semillero', 'celular');
            $hasLiderCorreo = Schema::hasColumn('lideres_semillero', 'correo_institucional');
            // SELECT seguro, adaptable al esquema
            $selectSemillero = $hasNombre ? "COALESCE(s.$colNombre,'')" : "''";
            $selectLinea     = $hasLinea  ? "COALESCE(s.$colLinea,'')" : "''";
            $selectCreated   = $hasCreated ? "s.created_at" : "NULL";

            // Si hay FK hacemos join y podemos usar columnas del líder;
            // si no hay FK, devolvemos strings vacíos para esas columnas.
            $selectLider    = $fkCol ? 'TRIM(CONCAT(COALESCE(l.nombres,"")," ",COALESCE(l.apellidos,"")))' : "''";
            $selectCorreo   = $fkCol && $hasLiderCorreo ? 'COALESCE(l.correo_institucional,"")' : "''";
            $selectTelefono = $fkCol && $hasLiderCel    ? 'COALESCE(l.celular,"")' : "''";

            $query->select([
                DB::raw($idCol ? "s.$idCol as id" : "0 as id"),
                DB::raw("$selectSemillero as semillero"),
                DB::raw("$selectLinea as linea_investigacion"),
                DB::raw("$selectLider as lider"),
                DB::raw($fkCol ? 'l.id_usuario as lider_id' : 'NULL as lider_id'),
                DB::raw("$selectCorreo as correo"),
                DB::raw("$selectTelefono as telefono"),
                DB::raw("$selectCreated as created_at"),
            ]);

        // 🔎 Filtros de búsqueda
        if ($q !== '') {
            $query->where(function ($w) use ($q, $hasNombre, $hasLinea, $fkCol, $colNombre, $colLinea) {
                if ($hasNombre) $w->orWhere("s.$colNombre", 'like', "%{$q}%");
                if ($hasLinea)  $w->orWhere("s.$colLinea", 'like', "%{$q}%");
                if ($fkCol)     $w->orWhere(DB::raw('CONCAT(COALESCE(l.nombres,"")," ",COALESCE(l.apellidos,""))'), 'like', "%{$q}%");
            });
        }

        if ($linea !== '' && $hasLinea) {
            $query->where("s.$colLinea", 'like', "%{$linea}%");
        }

        if ($lider !== '' && $fkCol) {
            $query->where(DB::raw('CONCAT(COALESCE(l.nombres,"")," ",COALESCE(l.apellidos,""))'), 'like', "%{$lider}%");
        }

        // 📅 Orden adaptable
        if ($hasCreated) {
            $query->orderByDesc('s.created_at');
        } elseif ($idCol) {
            $query->orderByDesc("s.$idCol");
        } elseif ($hasNombre) {
            $query->orderBy("s.$colNombre");
        }

        $semilleros = $query->paginate(12)->withQueryString();

        // 📋 Obtener líderes para el modal (si la tabla existe)
        $lideres = Schema::hasTable('lideres_semillero')
            ? (function(){
                $hasCel = Schema::hasColumn('lideres_semillero','celular');
                $hasCorreo = Schema::hasColumn('lideres_semillero','correo_institucional');
                $sel = [
                    'id_usuario',
                    DB::raw('TRIM(CONCAT(COALESCE(nombres,"")," ",COALESCE(apellidos,""))) as nombre'),
                ];
                $sel[] = $hasCorreo ? 'correo_institucional' : DB::raw("'' as correo_institucional");
                $sel[] = $hasCel ? 'celular' : DB::raw("'' as celular");
                return DB::table('lideres_semillero')->select($sel)->orderBy('nombres')->get();
            })()
            : collect([]);

        return view('Admin.semilleros.index', compact('semilleros', 'lideres'));
    }

    // ─────────── Métodos futuros (create, store, edit, update, destroy) ───────────

    public function create()
    {
        // vista para registrar nuevo semillero
        return view('Admin.semilleros.create');
    }

    public function store(Request $request)
    {
        // Validar entrada del formulario (desde la vista)
        $validated = $request->validate([
            'nombre'             => 'required|string|max:150',
            'linea_investigacion'=> 'required|string|max:255',
            'id_lider_usuario'   => 'nullable|integer|exists:lideres_semillero,id_usuario',
        ]);

        // Resolver columnas reales de la tabla 'semilleros'
        $tbl = 'semilleros';
        $colNombre = Schema::hasColumn($tbl, 'nombre') ? 'nombre'
                    : (Schema::hasColumn($tbl, 'nombre_semillero') ? 'nombre_semillero' : null);
        $colLinea  = Schema::hasColumn($tbl, 'linea_investigacion') ? 'linea_investigacion'
                    : (Schema::hasColumn($tbl, 'línea_investigación') ? 'línea_investigación' : null);
        $colLider  = Schema::hasColumn($tbl, 'id_lider_usuario') ? 'id_lider_usuario'
                    : (Schema::hasColumn($tbl, 'id_lider_semi') ? 'id_lider_semi' : null);
        $hasCreated = Schema::hasColumn($tbl, 'created_at');
        $hasUpdated = Schema::hasColumn($tbl, 'updated_at');
        $hasCreado  = Schema::hasColumn($tbl, 'creado_en');
        $hasActual  = Schema::hasColumn($tbl, 'actualizado_en');

        // Construir payload de inserción acorde al esquema
        $insert = [];
        if ($colNombre) $insert[$colNombre] = $validated['nombre'];
        if ($colLinea)  $insert[$colLinea]  = $validated['linea_investigacion'];
        if ($colLider && !empty($validated['id_lider_usuario'])) {
            $insert[$colLider] = $validated['id_lider_usuario'];
        }
        if ($hasCreated) $insert['created_at'] = now();
        if ($hasUpdated) $insert['updated_at'] = now();
        if ($hasCreado)  $insert['creado_en'] = now();
        if ($hasActual)  $insert['actualizado_en'] = now();

        $newId = DB::table($tbl)->insertGetId($insert);

        if ($request->wantsJson()) {
            // Preparar payload del item para la tabla
            $fkCol = Schema::hasColumn($tbl, 'id_lider_usuario') ? 'id_lider_usuario'
                    : (Schema::hasColumn($tbl, 'id_lider_semi') ? 'id_lider_semi' : null);

            $lider = null;
            if (!empty($insert[$fkCol ?? ''])) {
                $lider = DB::table('lideres_semillero as l')
                    ->select(
                        DB::raw('TRIM(CONCAT(COALESCE(l.nombres,"")," ",COALESCE(l.apellidos,""))) as nombre'),
                        Schema::hasColumn('lideres_semillero','correo_institucional') ? 'correo_institucional' : DB::raw("'' as correo_institucional"),
                        Schema::hasColumn('lideres_semillero','celular') ? 'celular' : DB::raw("'' as celular")
                    )
                    ->where('l.id_usuario', $insert[$fkCol])
                    ->first();
            }

            return response()->json([
                'ok' => true,
                'item' => [
                    'id' => $newId,
                    'semillero' => $colNombre ? $insert[$colNombre] : '',
                    'linea_investigacion' => $colLinea ? $insert[$colLinea] : '',
                    'lider' => $lider->nombre ?? '',
                    'correo' => $lider->correo_institucional ?? '',
                    'telefono' => $lider->celular ?? '',
                ],
            ]);
        }

        return back()->with('success', 'Semillero creado correctamente.');
    }

    public function edit($id)
    {
        $tbl = 'semilleros';
        $idCol = Schema::hasColumn($tbl, 'id') ? 'id' : (Schema::hasColumn($tbl, 'id_semillero') ? 'id_semillero' : null);
        if(!$idCol) abort(404);

        $colNombre = Schema::hasColumn($tbl, 'nombre') ? 'nombre'
                    : (Schema::hasColumn($tbl, 'nombre_semillero') ? 'nombre_semillero' : null);
        $colLinea  = Schema::hasColumn($tbl, 'linea_investigacion') ? 'linea_investigacion'
                    : (Schema::hasColumn($tbl, 'línea_investigación') ? 'línea_investigación' : null);
        $colLider  = Schema::hasColumn($tbl, 'id_lider_usuario') ? 'id_lider_usuario'
                    : (Schema::hasColumn($tbl, 'id_lider_semi') ? 'id_lider_semi' : null);

        $row = DB::table($tbl)->where($idCol, $id)->first();
        if(!$row) abort(404);

        $lideres = DB::table('lideres_semillero')
            ->select(
                'id_usuario',
                DB::raw('TRIM(CONCAT(COALESCE(nombres,"")," ",COALESCE(apellidos,""))) as nombre'),
                Schema::hasColumn('lideres_semillero','correo_institucional') ? 'correo_institucional' : DB::raw("'' as correo_institucional")
            )
            ->orderBy('nombres')
            ->get();

        return view('Admin.semilleros.edit', [
            'id' => $row->{$idCol},
            'nombre' => $colNombre ? ($row->{$colNombre} ?? '') : '',
            'linea' => $colLinea ? ($row->{$colLinea} ?? '') : '',
            'lider_id' => $colLider ? ($row->{$colLider} ?? '') : '',
            'lideres' => $lideres,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre'              => 'required|string|max:150',
            'linea_investigacion' => 'required|string|max:255',
            'id_lider_usuario'    => 'nullable|integer|exists:lideres_semillero,id_usuario',
        ]);

        $tbl = 'semilleros';
        $idCol = Schema::hasColumn($tbl, 'id') ? 'id' : (Schema::hasColumn($tbl, 'id_semillero') ? 'id_semillero' : null);
        if(!$idCol) return back()->withErrors('No se puede determinar la PK de semilleros');

        $colNombre = Schema::hasColumn($tbl, 'nombre') ? 'nombre'
                    : (Schema::hasColumn($tbl, 'nombre_semillero') ? 'nombre_semillero' : null);
        $colLinea  = Schema::hasColumn($tbl, 'linea_investigacion') ? 'linea_investigacion'
                    : (Schema::hasColumn($tbl, 'línea_investigación') ? 'línea_investigación' : null);
        $colLider  = Schema::hasColumn($tbl, 'id_lider_usuario') ? 'id_lider_usuario'
                    : (Schema::hasColumn($tbl, 'id_lider_semi') ? 'id_lider_semi' : null);
        $hasUpdated = Schema::hasColumn($tbl, 'updated_at');
        $hasActual  = Schema::hasColumn($tbl, 'actualizado_en');

        $update = [];
        if ($colNombre) $update[$colNombre] = $validated['nombre'];
        if ($colLinea)  $update[$colLinea]  = $validated['linea_investigacion'];
        if ($colLider)  $update[$colLider]  = $validated['id_lider_usuario'] ?? null;
        if ($hasUpdated) $update['updated_at'] = now();
        if ($hasActual)  $update['actualizado_en'] = now();

        DB::table($tbl)->where($idCol, $id)->update($update);

        if ($request->wantsJson()) return response()->json(['ok'=>true]);
        return back()->with('success', 'Semillero actualizado correctamente.');
    }

    public function destroy($id)
    {
        $tbl = 'semilleros';
        $idCol = Schema::hasColumn($tbl, 'id') ? 'id' : (Schema::hasColumn($tbl, 'id_semillero') ? 'id_semillero' : null);
        if($idCol){ DB::table($tbl)->where($idCol, $id)->delete(); }
        return back()->with('success', 'Semillero eliminado.');
    }
}
