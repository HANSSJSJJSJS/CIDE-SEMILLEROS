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
        // Cargar líder con datos de users (nombre y apellidos están en users)
        $semillero->load(['lider' => function($query) {
            $query->select('id_lider_semi', 'id_usuario', 'correo_institucional')
                  ->with(['user:id,nombre,apellidos']);
        }]);

        // AHORA CON PAGINACIÓN
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
        // Aseguramos que pertenece a ese semillero
        if ((int) $proyecto->id_semillero !== (int) $semillero->id_semillero) {
            abort(404, 'El proyecto no pertenece a este semillero');
        }

        // 1) Verificar si tiene aprendices asociados en la tabla pivote
        //    (según tu error: tabla "aprendiz_proyecto", FK "id_proyecto")
        $tieneAprendices = DB::table('aprendiz_proyecto')
            ->where('id_proyecto', $proyecto->id_proyecto)
            ->exists();

        if ($tieneAprendices) {
            return back()->with(
                'error',
                'No se puede eliminar el proyecto '.$proyecto->nombre_proyecto.' porque tiene aprendices asignados.'
            );
        }

        // 2) Si no tiene aprendices, se puede eliminar
        $nombre = $proyecto->nombre_proyecto;
        $proyecto->delete();

        return back()->with('ok','Proyecto eliminado correctamente: '.$nombre);
    }

    // (Mejor moverlo a SemilleroController, pero lo dejamos igual que tú)
    public function show($id)
    {
        $exists = DB::table('semilleros')->where('id_semillero', $id)->exists();
        abort_unless($exists, 404, 'Semillero no encontrado.');

        return redirect()->route('admin.semilleros.proyectos.index', $id);
    }

    // GET /admin/semilleros/{semillero}/proyectos/{proyecto}/detalle
    public function detalle(Semillero $semillero, Proyecto $proyecto)
{
    if ((int)$proyecto->id_semillero !== (int)$semillero->id_semillero) {
        abort(404, 'El proyecto no pertenece a este semillero');
    }

    // Cargar aprendices + su usuario (tabla users)
    $proyecto->load([
        'aprendices.user',
    ]);

    $integrantes   = $proyecto->aprendices;

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

    return view('admin.semilleros.proyectos.detalle', compact(
        'semillero','proyecto','integrantes','documentacion','observaciones'
    ));
}


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
            'fecha_inicio'    => optional($proyecto->fecha_inicio)->format('Y-m-d'),
            'fecha_fin'       => optional($proyecto->fecha_fin)->format('Y-m-d'),
        ]);
    }
}
