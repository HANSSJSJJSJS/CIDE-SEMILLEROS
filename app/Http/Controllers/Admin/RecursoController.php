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



public function download(Recurso $recurso)
{
    // 🔴 Caso sin archivo
    if (
        !$recurso->archivo ||
        $recurso->archivo === 'sin_archivo'
    ) {
        return back()->with('error', 'Este recurso no tiene archivo para descargar');
    }

    // 🔴 El archivo no existe físicamente
    if (!Storage::exists($recurso->archivo)) {
        return back()->with('error', 'El archivo no existe en el servidor');
    }

    // ✅ Descargar correctamente
    return Storage::download(
        $recurso->archivo,
        $recurso->nombre_archivo
    );
}


    // ======================================================
    // ELIMINAR RECURSO
    // ======================================================
    public function destroy($id)
    {
        Recurso::where('id_recurso', $id)->delete();
        return response()->json(['success' => true]);
    }
    // ======================================================
    // ACTUALIZAR ESTADO
    // ======================================================
                public function actualizarEstadoActividad(Request $request, $id)
            {
                Recurso::where('id_recurso', $id)->update([
                    'estado' => $request->estado,
                    'comentarios' => $request->comentarios,
                    'respondido_en' => now()
                ]);

                return response()->json(['success' => true]);
            }


    // ======================================================
    // EDITAR RECURSO
    // ======================================================
 public function actualizarRecurso(Request $request, $id)
{
    Recurso::where('id_recurso', $id)->update([
        'descripcion' => $request->descripcion,
        'fecha_vencimiento' => $request->fecha_limite,
        'tipo_documento' => $request->tipo_documento,
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
    if (!is_numeric($id)) {
        return response()->json([
            'success' => false,
            'message' => 'ID inválido'
        ], 400);
    }

    // 🔑 USAR id_recurso (NO id)
    $recurso = DB::table('recursos')
        ->where('id_recurso', $id)
        ->first();

    if (!$recurso) {
        return response()->json([
            'success' => false,
            'message' => 'Recurso no encontrado'
        ], 404);
    }

    // 🗑️ Eliminar archivo físico
    if ($recurso->archivo && $recurso->archivo !== 'sin_archivo') {
        if (Storage::disk('public')->exists($recurso->archivo)) {
            Storage::disk('public')->delete($recurso->archivo);
        }
    }

    // 🗑️ Eliminar registro BD
    DB::table('recursos')
        ->where('id_recurso', $id)
        ->delete();

    return response()->json([
        'success' => true,
        'message' => 'Archivo eliminado correctamente'
    ]);
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


// ======================================================
// LISTAR RECURSOS POR SEMILLERO (ADMIN)
// ======================================================
public function porSemillero($semilleroId)
{
    $recursos = DB::table('recursos as r')
        ->leftJoin('users as u', 'u.id', '=', 'r.user_id')
        ->leftJoin('proyectos as p', 'p.id_proyecto', '=', 'r.proyecto_id')
        ->where('r.dirigido_a', 'lideres')
        ->where('r.semillero_id', $semilleroId)
            ->select(
            'r.id_recurso as id',
            'r.nombre_archivo as titulo',
            'r.descripcion',
            'r.estado',
            'r.fecha_vencimiento as fecha_limite',
            'r.fecha_asignacion',
            'r.archivo',
            'r.archivo_respuesta',
            'r.enlace_respuesta',
            'r.tipo_documento as tipo_recurso',
            'r.comentarios',
            'r.respondido_en',
            DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellidos,''))) as lider_nombre"),
            'p.nombre_proyecto'
        )

        ->orderBy('r.created_at', 'desc')
        ->get();

    return response()->json($recursos);
}
} 