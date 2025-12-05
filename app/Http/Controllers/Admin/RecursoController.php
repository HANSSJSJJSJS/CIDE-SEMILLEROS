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
    //              CENTRO DE RECURSOS + NUEVO MÓDULO
    // ======================================================

    /**
     * Vista principal del módulo de recursos.
     * Muestra los semilleros con contadores de recursos para líderes.
     */
    public function index()
    {
        // Traemos semilleros + info básica del líder
        $semilleros = DB::table('semilleros as s')
            ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 's.id_lider_semi')
            ->leftJoin('users as u', 'u.id', '=', 'ls.id_lider_semi')
            ->select(
                's.id_semillero',
                's.nombre',

                // columnas "virtuales" para la tarjeta
                DB::raw("'Sin descripción' as descripcion"),
                DB::raw("'ACTIVO' as estado"),

                's.id_lider_semi',
                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(u.nombre, ''), ' ',
                            COALESCE(u.apellidos, '')
                        )
                    ) as lider_nombre
                ")
            )
            ->orderBy('s.nombre')
            ->get();

        // Contadores de recursos por semillero (solo los dirigidos a líderes)
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
    // DESCARGAR RECURSO
    // ======================================================
    public function download(Recurso $recurso)
    {
        if (! $recurso->archivo || ! Storage::disk('public')->exists($recurso->archivo)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('public')->download($recurso->archivo, $recurso->nombre_archivo);
    }

    // ======================================================
    // ELIMINAR
    // ======================================================
    public function destroy(Recurso $recurso)
    {
        if ($recurso->archivo && Storage::disk('public')->exists($recurso->archivo)) {
            Storage::disk('public')->delete($recurso->archivo);
        }

        $recurso->delete();

        return response()->json(['success' => true]);
    }

    // ======================================================
    //          RECURSOS PARA LÍDERES DE SEMILLERO
    // ======================================================

    /**
     * Obtener recursos de un semillero para el modal "Ver Recursos"
     * GET admin/semilleros/{semillero}/recursos
     */
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
                DB::raw('DATE(r.fecha_vencimiento) as fecha_limite'),
                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(u.nombre, ''), ' ',
                            COALESCE(u.apellidos, '')
                        )
                    ) as lider_nombre
                ")
            )
            ->orderBy('r.fecha_vencimiento', 'desc')
            ->get()
            ->map(function ($r) {
                // Detectar recursos vencidos (comparando strings Y-m-d)
                $estado = $r->estado;
                $hoy    = now()->toDateString(); // 'YYYY-mm-dd'

                if (
                    $estado === 'pendiente' &&
                    $r->fecha_limite &&
                    $r->fecha_limite < $hoy
                ) {
                    $estado = 'vencido';
                }

                $r->estado = $estado;
                return $r;
            });

        return response()->json([
            'actividades' => $recursos, // el JS usa data.actividades
        ]);
    }

    // ======================================================
    // CREAR RECURSO PARA LÍDER DEL SEMILLERO
    // ======================================================
    public function storeActividad(Request $request)
    {
        $request->validate([
            'semillero_id' => 'required|exists:semilleros,id_semillero',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'fecha_limite' => 'required|date|after_or_equal:today',
        ]);

        Recurso::create([
            'nombre_archivo'    => $request->titulo,
            'descripcion'       => $request->descripcion,
            'fecha_vencimiento' => $request->fecha_limite,
            'categoria'         => 'otros',
            'dirigido_a'        => 'lideres',
            'estado'            => 'pendiente',
            'semillero_id'      => $request->semillero_id,
            'user_id'           => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }

    // ======================================================
    // ACTUALIZAR ESTADO DEL RECURSO / ACTIVIDAD
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
}