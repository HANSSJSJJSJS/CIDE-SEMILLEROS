<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recurso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $recursos = DB::table('recursos as r')
            ->leftJoin('users as u', 'u.id', '=', 'r.user_id')
            ->where('r.semillero_id', $semilleroId)
            ->where('r.dirigido_a', 'lideres')
            ->select(
                'r.id',
                'r.nombre_archivo as titulo',
                'r.descripcion',
                'r.estado',
                'r.archivo',
                DB::raw('DATE(r.fecha_vencimiento) as fecha_limite'),
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellidos,''))) as lider_nombre")
            )
            ->orderBy('r.fecha_vencimiento', 'desc')
            ->get()
            ->map(function ($r) {

                $hoy = now()->toDateString();

                if ($r->estado === 'pendiente' && $r->fecha_limite < $hoy) {
                    $r->estado = 'vencido';
                }

                return $r;
            });

        return response()->json([
            'actividades' => $recursos
        ]);
    }

    // ======================================================
    // CREAR RECURSO (ADMIN / LÍDER GENERAL)
    // ======================================================
    public function storeActividad(Request $request)
    {
        $request->validate([
            'semillero_id' => 'required|exists:semilleros,id_semillero',
            'proyecto_id'  => 'required|exists:proyectos,id_proyecto',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'fecha_limite' => 'required|date|after_or_equal:today'
        ]);

        Recurso::create([
            'nombre_archivo'    => $request->titulo,
            'archivo'           => 'sin_archivo',
            'categoria'         => 'otros',
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
    // ACTUALIZAR ESTADO DEL RECURSO
    // ======================================================
    public function actualizarEstadoActividad(Request $request, Recurso $recurso)
    {
        $request->validate([
            'estado'      => 'required|in:pendiente,aprobado,rechazado',
            'comentarios' => 'nullable|string|max:500',
        ]);

        if ($recurso->dirigido_a !== 'lideres') {
            return response()->json([
                'success' => false,
                'message' => 'No es un recurso de líder',
            ], 400);
        }

        $recurso->estado      = $request->estado;
        $recurso->comentarios = $request->comentarios;
        $recurso->save();

        return response()->json(['success' => true]);
    }

    // ======================================================
    // OBTENER LÍDER DEL SEMILLERO
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
    // VISTA DE MULTIMEDIA
    // ======================================================
    public function multimedia()
    {
        $semilleros = DB::table('semilleros')
            ->select('id_semillero','nombre')
            ->get();

        return view('admin.recursos.multimedia', compact('semilleros'));
    }
    public function obtenerMultimedia()
{
    $recursos = DB::table('recursos')
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($recursos);
}
public function deleteMultimedia($id)
{
    $recurso = DB::table('recursos')->where('id', $id)->first();

    if (!$recurso) {
        return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
    }

    // BORRAR ARCHIVO FÍSICO
    if ($recurso->archivo && Storage::disk('public')->exists($recurso->archivo)) {
        Storage::disk('public')->delete($recurso->archivo);
    }

    // BORRAR REGISTRO
    DB::table('recursos')->where('id', $id)->delete();

    return response()->json(['success' => true]);
}
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



} // FIN DEL CONTROLADOR
