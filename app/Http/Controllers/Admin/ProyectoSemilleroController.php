<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;           
use App\Models\Semillero;
use App\Models\Proyecto;
use App\Models\DocumentoProyecto; 
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
    // === MOCK de datos para vista previa (reemplaza con tus relaciones reales luego) ===
    $integrantes = collect([
        (object)['nombre'=>'Ana Pérez','correo'=>'ana.perez@example.com','telefono'=>'3001234567'],
        (object)['nombre'=>'Carlos Gómez','correo'=>'carlos.gomez@example.com','telefono'=>'3107654321'],
        (object)['nombre'=>'Sofía Rodríguez','correo'=>'sofia.rod@example.com','telefono'=>'3159876543'],
    ]);

    $documentacion = collect([
        (object)['nombre'=>'Acta de inicio.pdf','fecha'=>'2025-10-03'],
        (object)['nombre'=>'Informe parcial.docx','fecha'=>'2025-11-01'],
    ]);

    $observaciones = "El proyecto avanza correctamente; fortalecer la documentación técnica.";

    return view('Admin.semilleros.detalle_proyecto',
        compact('semillero','proyecto','integrantes','documentacion','observaciones')
    );
}
public function editAjax(Semillero $semillero, Proyecto $proyecto)
{
    // seguridad: que el proyecto pertenezca al semillero
    if ($proyecto->id_semillero !== $semillero->id_semillero) {
        abort(404);
    }

    return response()->json([
        'id_proyecto'     => $proyecto->id_proyecto,
        'nombre_proyecto' => $proyecto->nombre_proyecto,
        'descripcion'     => $proyecto->descripcion,
        'estado'          => $proyecto->estado,
        'fecha_inicio'    => optional($proyecto->fecha_inicio)->format('Y-m-d'),
        'fecha_fin'       => optional($proyecto->fecha_fin)->format('Y-m-d'),
    ]);
}


}
