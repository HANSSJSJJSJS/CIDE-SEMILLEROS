<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class RecursoController extends Controller
{
    // ======================================================
    // VISTA MULTIMEDIA
    // ======================================================
    public function multimedia()
    {
        $semilleros = DB::table('semilleros')
            ->select('id_semillero', 'nombre')
            ->get();

        return view('admin.recursos.multimedia.multimedia', compact('semilleros'));
    }

    // ======================================================
    // LISTAR MULTIMEDIA (AJAX) – SEGURO
    // ======================================================
    public function obtenerMultimedia()
    {
        if (!Schema::hasTable('recursos')) {
            return response()->json([]);
        }

        $cols = Schema::getColumnListing('recursos');

        // 🔎 Detectar ID real
        $idCol = null;
        foreach (['id', 'id_recurso', 'id_recursos', 'id_recurso_lider', 'id_recurso_semillero'] as $c) {
            if (in_array($c, $cols, true)) {
                $idCol = $c;
                break;
            }
        }

        if (!$idCol) {
            return response()->json([]);
        }

        $recursos = DB::table('recursos')
            ->select([
                DB::raw("$idCol as id"),
                'nombre_archivo',
                'archivo',
                'categoria',
                DB::raw("LOWER(SUBSTRING_INDEX(archivo, '.', -1)) as extension"),
                'created_at'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(100) // 🔒 evita error de memoria
            ->get();

        return response()->json($recursos);
    }

    // ======================================================
    // SUBIR MULTIMEDIA
    // ======================================================
    public function storeMultimedia(Request $request)
    {
        $request->validate([
            'titulo'       => 'required|string|max:255',
            'categoria'    => 'required|in:plantillas,manuales,otros',
            'archivo'      => 'required|file|max:20480',
            'destino'      => 'required|in:todos,semillero',
            'descripcion'  => 'nullable|string',
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
            'user_id'        => Auth::id(),
            'estado'         => 'pendiente',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return response()->json(['success' => true]);
    }

    // ======================================================
    // ELIMINAR MULTIMEDIA
    // ======================================================
    public function deleteMultimedia($id)
    {
        $cols = Schema::getColumnListing('recursos');

        $idCol = null;
        foreach (['id', 'id_recurso', 'id_recursos', 'id_recurso_lider', 'id_recurso_semillero'] as $c) {
            if (in_array($c, $cols, true)) {
                $idCol = $c;
                break;
            }
        }

        if (!$idCol) {
            return response()->json(['success' => false], 500);
        }

        $recurso = DB::table('recursos')->where($idCol, $id)->first();

        if (!$recurso) {
            return response()->json(['success' => false], 404);
        }

        if (!empty($recurso->archivo) && Storage::disk('public')->exists($recurso->archivo)) {
            Storage::disk('public')->delete($recurso->archivo);
        }

        DB::table('recursos')->where($idCol, $id)->delete();

        return response()->json(['success' => true]);
    }
}
