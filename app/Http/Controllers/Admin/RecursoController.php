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
    // INDEX – RECURSOS POR SEMILLERO (VISTA PRINCIPAL)
    // ======================================================
public function index()
{
    $semilleros = DB::table('semilleros as s')
        ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 's.id_lider_semi')
        ->leftJoin('users as u', 'u.id', '=', 'ls.id_usuario')
        ->select(
            's.id_semillero',
            's.nombre',
            DB::raw("'Sin descripción' as descripcion"), // 👈 FIX
            DB::raw("'ACTIVO' as estado"),               // 👈 FIX
            's.id_lider_semi',
            DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellidos,''))) as lider_nombre")
        )
        ->orderBy('s.nombre')
        ->get();

    foreach ($semilleros as $s) {
        $query = DB::table('recursos')
            ->where('dirigido_a', 'lideres')
            ->where('semillero_id', $s->id_semillero);

        $s->actividades_total      = (clone $query)->count();
        $s->actividades_pendientes = (clone $query)->where('estado', 'pendiente')->count();
        $s->actividades_aprobadas  = (clone $query)->where('estado', 'aprobado')->count();
        $s->actividades_rechazadas = (clone $query)->where('estado', 'rechazado')->count();
    }

    return view('admin.recursos.index', compact('semilleros'));
}



    // ======================================================
    // PROYECTOS POR SEMILLERO
    // ======================================================
    public function getProyectos($id)
    {
        return DB::table('proyectos')
            ->where('id_semillero', $id)
            ->select('id_proyecto', 'nombre_proyecto')
            ->get();
    }

    // ======================================================
    // CREAR RECURSO PARA LÍDER
    // ======================================================
    public function storeActividad(Request $request)
    {
        $request->validate([
            'semillero_id' => 'required|exists:semilleros,id_semillero',
            'proyecto_id'  => 'required|exists:proyectos,id_proyecto',
            'categoria'    => 'required|in:plantillas,manuales,otros',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'fecha_limite' => 'required|date'
        ]);

        Recurso::create([
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
        ]);

        return response()->json(['success' => true]);
    }

    // ======================================================
    // ELIMINAR RECURSO
    // ======================================================
    public function destroy($id)
    {
        Recurso::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // ======================================================
    // ACTUALIZAR ESTADO
    // ======================================================
    public function actualizarEstadoActividad(Request $request, $id)
    {
        Recurso::where('id', $id)->update([
            'estado' => $request->estado,
            'comentarios' => $request->comentarios
        ]);

        return response()->json(['success' => true]);
    }

    // ======================================================
    // EDITAR RECURSO
    // ======================================================
    public function actualizarRecurso(Request $request, $id)
    {
        Recurso::where('id', $id)->update([
            'descripcion' => $request->descripcion,
            'fecha_vencimiento' => $request->fecha_limite
        ]);

        return response()->json(['success' => true]);
    }

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
    // LISTAR MULTIMEDIA (AJAX)
    // ======================================================
    public function obtenerMultimedia()
    {
        return DB::table('recursos')
            ->whereNotNull('archivo')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    }

    // ======================================================
    // SUBIR MULTIMEDIA
    // ======================================================
    public function storeMultimedia(Request $request)
    {
        $path = $request->file('archivo')->store('multimedia', 'public');

        DB::table('recursos')->insert([
            'nombre_archivo' => $request->titulo,
            'archivo'        => $path,
            'categoria'      => $request->categoria,
            'dirigido_a'     => $request->destino,
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
        $r = DB::table('recursos')->where('id', $id)->first();

        if ($r && Storage::disk('public')->exists($r->archivo)) {
            Storage::disk('public')->delete($r->archivo);
        }

        DB::table('recursos')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }
// ======================================================
// OBTENER LÍDER DEL SEMILLERO (AJAX)
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
        'lider' => $lider
    ]);
}













}
