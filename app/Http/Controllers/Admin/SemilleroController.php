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

        $fkCol = collect(['id_lider_semi', 'id_lider', 'lider_id', 'id_lidergen'])
            ->first(fn($c) => Schema::hasColumn('semilleros', $c));

        $hasNombre  = Schema::hasColumn('semilleros', 'nombre');
        $hasLinea   = Schema::hasColumn('semilleros', 'linea_investigacion');
        $hasCreated = Schema::hasColumn('semilleros', 'created_at');

        // 🧱 Query base
        $query = DB::table('semilleros as s');

        // JOIN solo si existe la columna de líder
        if ($fkCol) {
            $query->leftJoin('lideres_semillero as l', 'l.id_lider_semi', '=', DB::raw("s.$fkCol"));
        }
            // SELECT seguro, adaptable al esquema
            $selectSemillero = $hasNombre ? "COALESCE(s.nombre,'')" : "''";
            $selectLinea     = $hasLinea  ? "COALESCE(s.linea_investigacion,'')" : "''";
            $selectCreated   = $hasCreated ? "s.created_at" : "NULL";

            // Si hay FK hacemos join y podemos usar columnas del líder;
            // si no hay FK, devolvemos strings vacíos para esas columnas.
            $selectLider    = $fkCol ? 'TRIM(CONCAT(COALESCE(l.nombres,"")," ",COALESCE(l.apellidos,"")))' : "''";
            $selectCorreo   = $fkCol ? 'COALESCE(l.correo_institucional,"")' : "''";
            $selectTelefono = $fkCol ? 'COALESCE(l.celular,"")' : "''";

            $query->select([
                DB::raw($idCol ? "s.$idCol as id" : "0 as id"),
                DB::raw("$selectSemillero as semillero"),
                DB::raw("$selectLinea as linea_investigacion"),
                DB::raw("$selectLider as lider"),
                DB::raw("$selectCorreo as correo"),
                DB::raw("$selectTelefono as telefono"),
                DB::raw("$selectCreated as created_at"),
            ]);

        // 🔎 Filtros de búsqueda
        if ($q !== '') {
            $query->where(function ($w) use ($q, $hasNombre, $hasLinea, $fkCol) {
                if ($hasNombre) $w->orWhere('s.nombre', 'like', "%{$q}%");
                if ($hasLinea)  $w->orWhere('s.linea_investigacion', 'like', "%{$q}%");
                if ($fkCol)     $w->orWhere(DB::raw('CONCAT(COALESCE(l.nombres,"")," ",COALESCE(l.apellidos,""))'), 'like', "%{$q}%");
            });
        }

        if ($linea !== '' && $hasLinea) {
            $query->where('s.linea_investigacion', 'like', "%{$linea}%");
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
            $query->orderBy('s.nombre');
        }

        $semilleros = $query->paginate(12)->withQueryString();

        // 📋 Obtener líderes para el modal (si la tabla existe)
        $lideres = Schema::hasTable('lideres_semillero')
            ? DB::table('lideres_semillero')
                ->select(
                    'id_lider_semi',
                    DB::raw('TRIM(CONCAT(COALESCE(nombres,"")," ",COALESCE(apellidos,""))) as nombre'),
                    'correo_institucional',
                    'celular'
                )
                ->orderBy('nombres')
                ->get()
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
        // guardar nuevo semillero
    }

    public function edit($id)
    {
        // mostrar formulario de edición
    }

    public function update(Request $request, $id)
    {
        // actualizar registro
    }

    public function destroy($id)
    {
        // eliminar registro
    }
}
