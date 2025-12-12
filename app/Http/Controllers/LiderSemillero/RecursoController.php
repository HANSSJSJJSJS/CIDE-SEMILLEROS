<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\Recurso;
use App\Models\Semillero;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecursoController extends Controller
{
    /**
     * Vista principal del líder semillero
     */
    public function index()
    {
        $user = Auth::user();

        $semillero = Semillero::where('id_lider_semi', $user->id)->first();

        $proyectos = $semillero 
            ? Proyecto::where('id_semillero', $semillero->id_semillero)->get()
            : collect();

        $recursos = Recurso::whereIn('dirigido_a', ['lideres', 'todos'])
                ->orderBy('created_at', 'desc')
                ->get();

        return view('lider_semi.recursos.index', compact('proyectos', 'recursos'));
    }


    /**
     * Obtener proyectos del semillero (JSON)
     */
    public function proyectos($id_semillero)
    {
        $proyectos = Proyecto::where('id_semillero', $id_semillero)->get();

        return response()->json($proyectos);
    }


    /**
     * Obtener información del líder de un semillero
     */
    public function lider($id_semillero)
    {
        $sem = Semillero::find($id_semillero);

        if (!$sem) {
            return response()->json(['lider' => null]);
        }

        $lider = DB::table('users')
            ->where('id', $sem->id_lider_semi)
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name,' ',users.apellidos) as nombre_completo")
            )
            ->first();

        return response()->json(['lider' => $lider]);
    }


    /**
     * Guardar recurso nuevo (líder general o admin lo asignan)
     */
    public function store(Request $request)
    {
        $request->validate([
            'semillero_id'  => 'required|exists:semilleros,id_semillero',
            'proyecto_id'   => 'required|exists:proyectos,id_proyecto',
            'lider_id'      => 'required|exists:users,id',
            'titulo'        => 'required|string',
            'descripcion'   => 'required|string',
            'fecha_limite'  => 'required|date',
            'archivo'       => 'nullable|file|max:20480'
        ]);

        $archivo = null;

        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo')->store('recursos', 'public');
        }

        Recurso::create([
            'id_semillero'  => $request->semillero_id,
            'id_proyecto'   => $request->proyecto_id,
            'id_lider_semi' => $request->lider_id,
            'titulo'        => $request->titulo,
            'descripcion'   => $request->descripcion,
            'fecha_limite'  => $request->fecha_limite,
            'archivo'       => $archivo,
            'estado'        => 'pendiente',
            'dirigido_a'    => 'lideres'
        ]);

        return response()->json(['success' => true]);
    }


    /**
     * Responder recurso (líder semillero)
     */
    public function responder(Request $request)
    {
        $request->validate([
            'id_recurso' => 'required|exists:recursos,id',
            'respuesta'  => 'required|string',
        ]);

        $recurso = Recurso::find($request->id_recurso);

        $recurso->respuesta = $request->respuesta;
        $recurso->estado = 'respondido';
        $recurso->save();

        // Notificar a admins y líderes generales
        $lideres = DB::table('users')
            ->whereIn('role', ['admin', 'lider_general'])
            ->pluck('id');

        foreach ($lideres as $lid) {
            DB::table('notificaciones')->insert([
                'id_usuario' => $lid,
                'titulo'     => 'Nuevo recurso respondido',
                'mensaje'    => 'El líder de semillero respondió el recurso: ' . $recurso->titulo,
                'tipo'       => 'recurso',
                'leida'      => 0,
                'created_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
