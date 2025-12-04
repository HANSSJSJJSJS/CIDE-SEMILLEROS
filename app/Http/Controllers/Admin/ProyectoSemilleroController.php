<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Semillero;
use App\Models\Proyecto;

class ProyectoSemilleroController extends Controller
{
    public function index(Semillero $semillero)
    {
        // Cargar líder con datos del usuario
        $semillero->load(['lider' => function($query) {
            $query->select('id_lider_semi', 'id_usuario', 'correo_institucional')
                  ->with(['user:id,nombre,apellidos']);
        }]);

        $proyectos = $semillero->proyectos()
            ->orderByDesc('id_proyecto')
            ->paginate(8);

        return view('admin.semilleros.proyectos.index', compact('semillero','proyectos'));
    }

    public function store(Request $request, Semillero $semillero)
    {
        $v = Validator::make($request->all(), [
            'nombre_proyecto' => ['required','string','max:255'],
            'descripcion'     => ['nullable','string'],
            'estado'          => ['required','in:EN_FORMULACION,EN_EJECUCION,FINALIZADO,ARCHIVADO'],
            'fecha_inicio'    => ['nullable','date'],
            'fecha_fin'       => ['nullable','date','after_or_equal:fecha_inicio'],
        ]);

        if ($v->fails()) {
            return back()
                ->withErrors($v, 'crearProyecto')
                ->withInput()
                ->with('openModal', 'modalCrearProyecto');
        }

        Proyecto::create([
            'id_semillero'    => $semillero->id_semillero,
            'nombre_proyecto' => $request->nombre_proyecto,
            'descripcion'     => $request->descripcion,
            'estado'          => $request->estado,
            'fecha_inicio'    => $request->fecha_inicio,
            'fecha_fin'       => $request->fecha_fin,
        ]);

        return back()->with('ok','Proyecto registrado correctamente.');
    }

    // ===============================================
    // 🔹 UPDATE (sin modificaciones para observaciones)
    // ===============================================
    public function update(Request $request, Semillero $semillero, Proyecto $proyecto)
    {
        if ((int) $proyecto->id_semillero !== (int) $semillero->id_semillero) {
            abort(404, 'El proyecto no pertenece a este semillero');
        }

        $data = $request->validate([
            'nombre_proyecto' => 'required|string|max:255',
            'estado'          => 'required|string|max:50',
            'descripcion'     => 'nullable|string',
            'fecha_inicio'    => 'nullable|date',
            'fecha_fin'       => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $proyecto->update($data);

        return redirect()
            ->route('admin.semilleros.proyectos.index', $semillero->id_semillero)
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    // ===============================================
    // 🔹 ELIMINAR
    // ===============================================
    public function destroy(Semillero $semillero, Proyecto $proyecto)
    {
        if ((int) $proyecto->id_semillero !== (int) $semillero->id_semillero) {
            abort(404, 'El proyecto no pertenece a este semillero');
        }

        $tieneAprendices = DB::table('aprendiz_proyecto')
            ->where('id_proyecto', $proyecto->id_proyecto)
            ->exists();

        if ($tieneAprendices) {
            return back()->with('error',
                'No se puede eliminar el proyecto '.$proyecto->nombre_proyecto.' porque tiene aprendices asignados.'
            );
        }

        $nombre = $proyecto->nombre_proyecto;
        $proyecto->delete();

        return back()->with('success', 'Proyecto eliminado correctamente: '.$nombre);
    }

    // ===============================================
    // 🔹 SHOW (redirige a index)
    // ===============================================
    public function show($id)
    {
        $exists = DB::table('semilleros')->where('id_semillero', $id)->exists();
        abort_unless($exists, 404, 'Semillero no encontrado.');

        return redirect()->route('admin.semilleros.proyectos.index', $id);
    }

    // ===============================================
    // 🔹 DETALLE DEL PROYECTO + HISTORIAL DE OBSERVACIONES
    // ===============================================
    public function detalle(Semillero $semillero, Proyecto $proyecto)
    {
        if ((int)$proyecto->id_semillero !== (int)$semillero->id_semillero) {
            abort(404, 'El proyecto no pertenece a este semillero');
        }

        $proyecto->load(['aprendices.user']);
        $integrantes = $proyecto->aprendices;

        $documentacion = $proyecto->documentos()
            ->where('estado', 'APROBADO')
            ->orderByDesc('fecha_subida')
            ->get([
                'id_documento',
                DB::raw("documento as nombre"),
                DB::raw("fecha_subida as fecha"),
                'ruta_archivo',
                'tipo_archivo',
                'tamanio'
            ]);

        // ================================
        // 🔹 HISTORIAL DE OBSERVACIONES
        // ================================
        $observacionesHistorial = [];
        $raw = $proyecto->observaciones;

        if ($raw) {
            $lineas = preg_split("/\r\n|\n|\r/", $raw);

            foreach ($lineas as $linea) {
                $linea = trim($linea);
                if ($linea === '') continue;

                // Formato esperado:
                // [YYYY-MM-DD HH:MM] Nombre Apellido: contenido
                if (preg_match('/^\[(.+?)\]\s+(.*?):\s*(.*)$/', $linea, $m)) {
                    $observacionesHistorial[] = [
                        'fecha' => $m[1],
                        'autor' => $m[2],
                        'texto' => $m[3],
                    ];
                } else {
                    // Línea sin formato
                    $observacionesHistorial[] = [
                        'fecha' => null,
                        'autor' => null,
                        'texto' => $linea,
                    ];
                }
            }
        }

        return view('admin.semilleros.proyectos.detalle', compact(
            'semillero',
            'proyecto',
            'integrantes',
            'documentacion',
            'observacionesHistorial'
        ));
    }

    // ===============================================
    // 🔹 GUARDAR *NUEVA* OBSERVACIÓN (ACUMULA)
    // ===============================================
    public function guardarObservaciones(Request $request, Semillero $semillero, Proyecto $proyecto)
    {
        if ((int) $proyecto->id_semillero !== (int) $semillero->id_semillero) {
            abort(404, 'El proyecto no pertenece a este semillero');
        }

        $data = $request->validate([
            'nueva_observacion' => 'required|string',
        ]);

        $user = $request->user();

        // Ejemplo:
        // [2025-12-04 13:45] Carol Orca: Texto...
        $linea = '[' . now()->format('Y-m-d H:i') . '] '
               . trim(($user->nombre ?? '') . ' ' . ($user->apellidos ?? ''))
               . ': ' . $data['nueva_observacion'];

        // Agregar al final del campo TEXT
        $actual = $proyecto->observaciones ? rtrim($proyecto->observaciones) . "\n" : '';

        $proyecto->observaciones = $actual . $linea;
        $proyecto->save();

        return back()->with('success', 'Observaciones guardadas correctamente.');
    }

    // ===============================================
    // 🔹 AJAX (para editar modal)
    // ===============================================
    public function editAjax(Semillero $semillero, Proyecto $proyecto)
    {
        if ((int) $proyecto->id_semillero !== (int) $semillero->id_semillero) {
            abort(404, 'El proyecto no pertenece a este semillero');
        }

        return response()->json([
            'id_proyecto'     => $proyecto->id_proyecto,
            'nombre_proyecto' => $proyecto->nombre_proyecto,
            'estado'          => $proyecto->estado,
            'descripcion'     => $proyecto->descripcion,
            'observaciones'   => $proyecto->observaciones,
            'fecha_inicio'    => optional($proyecto->fecha_inicio)->format('Y-m-d'),
            'fecha_fin'       => optional($proyecto->fecha_fin)->format('Y-m-d'),
        ]);
    }
}
