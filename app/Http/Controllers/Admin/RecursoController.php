<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recurso;
use App\Models\Semillero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecursoController extends Controller
{
    // ======================================================
    //              CENTRO DE RECURSOS + NUEVO MÓDULO
    // ======================================================

    /**
     * Vista principal del módulo de recursos.
     * Ahora también muestra los semilleros con sus actividades.
     */
    public function index()
    {
        $semilleros = Semillero::with('lider')->get();

        foreach ($semilleros as $s) {
            $q = Recurso::where('dirigido_a', 'lideres')
                ->where('semillero_id', $s->id_semillero);

            $s->actividades_total      = (clone $q)->count();
            $s->actividades_pendientes = (clone $q)->where('estado', 'pendiente')->count();
            $s->actividades_aprobadas  = (clone $q)->where('estado', 'aprobado')->count();
            $s->actividades_rechazadas = (clone $q)->where('estado', 'rechazado')->count();

            $s->lider_nombre = optional($s->lider)->nombre_completo;
        }

        return view('admin.recursos.index', compact('semilleros'));
    }

    /**
     * Lista de recursos generales via AJAX
     */
    public function listar(Request $request)
    {
        $search = $request->get('search');
        $categoria = $request->get('categoria');

        $query = Recurso::query()->whereNull('semillero_id'); // solo recursos generales

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_archivo', 'like', "%$search%");
                $q->orWhere('descripcion', 'like', "%$search%");
            });
        }

        if ($categoria) {
            $query->where('categoria', $categoria);
        }

        return response()->json([
            'data' => $query->orderByDesc('created_at')->get()
        ]);
    }

    /**
     * Guardar un recurso general (con archivo)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_archivo' => 'required|string|max:255',
            'categoria'      => 'required|in:plantillas,manuales,otros',
            'dirigido_a'     => 'required|in:todos,aprendices,lideres',
            'archivo'        => 'required|file|max:15000',
        ]);

        $file = $request->file('archivo');
        $fileName = time().'_'.$file->getClientOriginalName();
        $filePath = $file->storeAs('recursos', $fileName, 'public');

        $recurso = new Recurso();
        $recurso->nombre_archivo = $request->nombre_archivo;
        $recurso->archivo = $filePath;
        $recurso->categoria = $request->categoria;
        $recurso->dirigido_a = $request->dirigido_a;
        $recurso->descripcion = $request->descripcion;
        $recurso->estado = 'pendiente';
        $recurso->user_id = Auth::id();
        $recurso->save();

        return response()->json(['success' => true]);
    }

    /**
     * Descargar archivo
     */
    public function download(Recurso $recurso)
    {
        if (!$recurso->archivo || !Storage::disk('public')->exists($recurso->archivo)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('public')->download($recurso->archivo, $recurso->nombre_archivo);
    }

    /**
     * Eliminar recurso o actividad
     */
    public function destroy(Recurso $recurso)
    {
        if ($recurso->archivo && Storage::disk('public')->exists($recurso->archivo)) {
            Storage::disk('public')->delete($recurso->archivo);
        }

        $recurso->delete();

        return response()->json(['success' => true]);
    }

    // ======================================================
    //          ACTIVIDADES → (NOMBRE CONSERVADO)
    // ======================================================

    /**
     * Obtener actividades de un semillero
     * GET admin/recursos/actividades/semillero/{semillero}
     */
    public function actividadesPorSemillero(Semillero $semillero)
    {
        $actividades = Recurso::where('dirigido_a', 'lideres')
            ->where('semillero_id', $semillero->id_semillero)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($a) use ($semillero) {

                // Estado virtual "vencido"
                $estadoVista = $a->estado;
                if (
                    $a->estado === 'pendiente' &&
                    $a->fecha_vencimiento &&
                    now()->gt($a->fecha_vencimiento)
                ) {
                    $estadoVista = 'vencido';
                }

                return [
                    'id' => $a->id,
                    'titulo' => $a->nombre_archivo,
                    'descripcion' => $a->descripcion,
                    'estado' => $estadoVista,
                    'fecha_vencimiento' => optional($a->fecha_vencimiento)?->format('Y-m-d'),
                    'fecha_creacion' => optional($a->created_at)?->format('Y-m-d'),
                    'comentarios' => $a->comentarios,
                    'lider_nombre' => optional($semillero->lider)->nombre_completo,
                ];
            });

        return response()->json(['actividades' => $actividades]);
    }

    /**
     * Crear actividad para líder de semillero
     * POST admin/recursos/actividades
     */
    public function storeActividad(Request $request)
    {
        $request->validate([
            'semillero_id' => 'required|exists:semilleros,id_semillero',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'fecha_limite' => 'required|date|after_or_equal:today'
        ]);

        $actividad = new Recurso();
        $actividad->nombre_archivo = $request->titulo;
        $actividad->descripcion = $request->descripcion;
        $actividad->fecha_vencimiento = $request->fecha_limite;
        $actividad->categoria = 'otros';
        $actividad->dirigido_a = 'lideres';
        $actividad->estado = 'pendiente';
        $actividad->semillero_id = $request->semillero_id;
        $actividad->user_id = Auth::id();
        $actividad->save();

        return response()->json(['success' => true]);
    }

    /**
     * Actualizar estado de una actividad
     * PUT admin/recursos/actividades/{recurso}/estado
     */
    public function actualizarEstadoActividad(Request $request, Recurso $recurso)
    {
        $request->validate([
            'estado'      => 'required|in:pendiente,aprobado,rechazado',
            'comentarios' => 'nullable|string|max:500'
        ]);

        if ($recurso->dirigido_a !== 'lideres') {
            return response()->json(['success' => false, 'message' => 'No es una actividad'], 400);
        }

        $recurso->estado = $request->estado;
        $recurso->comentarios = $request->comentarios;
        $recurso->save();

        return response()->json(['success' => true]);
    }
}
