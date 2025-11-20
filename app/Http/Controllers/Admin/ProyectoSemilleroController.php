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
        $semillero->load(['lider:id_lider_semi,nombres,apellidos,correo_institucional']);

        $proyectos = $semillero->proyectos()
            ->orderByDesc('id_proyecto')
            ->get();

        return view('Admin.semilleros.proyectos', compact('semillero', 'proyectos'));
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

        // La tabla proyectos no tiene AUTO_INCREMENT en id_proyecto, calculamos el siguiente id manualmente
        $nextId = (int) (\App\Models\Proyecto::max('id_proyecto') ?? 0) + 1;

        Proyecto::create([
            'id_proyecto'     => $nextId,
            'id_semillero'    => $semillero->id_semillero,
            'nombre_proyecto' => $request->nombre_proyecto,
            'descripcion'     => $request->descripcion,
            'estado'          => $request->estado,
            'fecha_inicio'    => $request->fecha_inicio,
            'fecha_fin'       => $request->fecha_fin,
        ]);

        return back()->with('ok','Proyecto registrado correctamente.');
    }

    public function update(Request $request, Semillero $semillero, Proyecto $proyecto)
    {
        $data = $request->validate([
            'nombre_proyecto' => ['required','string','max:255'],
            'descripcion'     => ['nullable','string'],
            'estado'          => ['required','in:EN_FORMULACION,EN_EJECUCION,FINALIZADO,ARCHIVADO'],
            'fecha_inicio'    => ['nullable','date'],
            'fecha_fin'       => ['nullable','date','after_or_equal:fecha_inicio'],
        ]);

        $proyecto->update($data);

        return back()->with('ok','Proyecto actualizado correctamente.');
    }

    public function destroy(Semillero $semillero, Proyecto $proyecto)
    {
        $proyecto->delete();
        return back()->with('ok','Proyecto eliminado correctamente.');
    }

    // (Mejor moverlo a SemilleroController)
    public function show($id)
    {
        $exists = DB::table('semilleros')->where('id_semillero', $id)->exists();
        abort_unless($exists, 404, 'Semillero no encontrado.');

        return redirect()->route('admin.semilleros.proyectos.index', $id);
    }

    // GET /admin/semilleros/{semillero}/proyectos/{proyecto}/detalle
    public function detalle(Semillero $semillero, Proyecto $proyecto)
    {
<<<<<<< HEAD
        // Validar pertenencia
        if ($proyecto->id_semillero !== $semillero->id_semillero) {
            abort(404, 'El proyecto no pertenece a este semillero');
        }

        // Cargar aprendices (solo columnas útiles)
        $proyecto->load([
            'aprendices:id_aprendiz,nombres,apellidos,correo_institucional,correo_personal,celular'
        ]);

        $integrantes   = $proyecto->aprendices;

        // Documentación aprobada del proyecto actual
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

        $observaciones = '';

        return view('Admin.semilleros.detalle_proyecto', compact(
            'semillero','proyecto','integrantes','documentacion','observaciones'
        ));
    }

    /**
     * AJAX: datos JSON de un proyecto para el modal de edición
     * GET /admin/semilleros/{semillero}/proyectos/{proyecto}/json
     */
    public function editAjax(Semillero $semillero, Proyecto $proyecto)
    {
        // Aseguramos que el proyecto pertenece al semillero
        if ((int) $proyecto->id_semillero !== (int) $semillero->id_semillero) {
            abort(404, 'El proyecto no pertenece a este semillero');
=======
        // Cargar relaciones reales: aprendices vinculados y documentos del proyecto
        $proyecto->load([
            'aprendices' => function ($q) {
                $q->select(
                    'aprendices.id_aprendiz',
                    'aprendices.nombres',
                    'aprendices.apellidos',
                    'aprendices.correo_institucional',
                    'aprendices.correo_personal',
                    'aprendices.celular'
                );
            },
            'documentos' => function ($q) {
                $q->orderByDesc('fecha_subida');
            },
        ]);

        $integrantes   = $proyecto->aprendices;
        $documentacion = $proyecto->documentos;
        $observaciones = null; // pendiente definir de dónde se obtienen

        return view('Admin.semilleros.detalle_proyecto',
            compact('semillero','proyecto','integrantes','documentacion','observaciones')
        );
    }

    // AJAX: datos para editar proyecto en modal
    public function editAjax(Semillero $semillero, Proyecto $proyecto)
    {
        // seguridad: que el proyecto pertenezca al semillero
        if ($proyecto->id_semillero !== $semillero->id_semillero) {
            abort(404);
>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
        }

        return response()->json([
            'id_proyecto'     => $proyecto->id_proyecto,
            'nombre_proyecto' => $proyecto->nombre_proyecto,
<<<<<<< HEAD
            'estado'          => $proyecto->estado,
            'descripcion'     => $proyecto->descripcion,
=======
            'descripcion'     => $proyecto->descripcion,
            'estado'          => $proyecto->estado,
>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
            'fecha_inicio'    => optional($proyecto->fecha_inicio)->format('Y-m-d'),
            'fecha_fin'       => optional($proyecto->fecha_fin)->format('Y-m-d'),
        ]);
    }
}
