<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SemilleroController extends Controller
{
    /**
     * Listado con líder actual (solo lectura).
     */
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));

        $semilleros = DB::table('semilleros as s')
            ->leftJoin('lideres_semillero as l', 'l.id_lider_semi', '=', 's.id_lider_semi')
            ->select(
                's.id_semillero',
                's.nombre',
                's.linea_investigacion',
                's.id_lider_semi',
                DB::raw("TRIM(CONCAT(COALESCE(l.nombres,''),' ',COALESCE(l.apellidos,''))) as lider_nombre"),
                'l.correo_institucional as lider_correo'
            )
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('s.nombre', 'like', "%{$q}%")
                      ->orWhere('s.linea_investigacion', 'like', "%{$q}%")
                      ->orWhere(DB::raw("CONCAT(COALESCE(l.nombres,''),' ',COALESCE(l.apellidos,''))"), 'like', "%{$q}%");
                });
            })
            ->orderBy('s.nombre')
            ->paginate(10)
            ->withQueryString();

        return view('Admin.semilleros.index', compact('semilleros','q'));
    }
    public function store(Request $request)
{
    $data = $request->validate([
        'nombre'              => ['required','string','max:255','unique:semilleros,nombre'],
        'linea_investigacion' => ['required','string','max:255'],
        'id_lider_semi'       => ['nullable','integer','exists:lideres_semillero,id_lider_semi'],
    ], [
        'required' => 'El campo :attribute es obligatorio.',
        'unique'   => 'Ya existe un semillero con ese nombre.',
        'exists'   => 'El líder seleccionado no es válido.',
    ], [
        'nombre'              => 'nombre del semillero',
        'linea_investigacion' => 'línea de investigación',
        'id_lider_semi'       => 'líder de semillero',
    ]);

    // Si viene líder, validar que no esté asignado a otro semillero
    if (!empty($data['id_lider_semi'])) {
        $ocupado = DB::table('semilleros')
            ->where('id_lider_semi', $data['id_lider_semi'])
            ->exists();

        if ($ocupado) {
            return back()->withErrors([
                'id_lider_semi' => 'Ese líder ya tiene un semillero asignado.'
            ])->withInput();
        }
    }

    DB::table('semilleros')->insert([
        'nombre'              => $data['nombre'],
        'linea_investigacion' => $data['linea_investigacion'],
        'id_lider_semi'       => $data['id_lider_semi'] ?? null,
        // Usa los nombres de columnas de tu esquema:
        // si tu tabla maneja "creado_en/actualizado_en" usa esas:
        'creado_en'           => now(),
        'actualizado_en'      => now(),
        // si en tu esquema usas timestamps de laravel, usa en cambio:
        // 'created_at'       => now(),
        // 'updated_at'       => now(),
    ]);

    return redirect()
        ->route('admin.semillero.index')
        ->with('success', 'Semillero creado correctamente.');
}


    /**
     * Devuelve datos del semillero + líder actual para llenar el modal (AJAX).
     */
    public function edit($id)
    {
        $row = DB::table('semilleros as s')
            ->leftJoin('lideres_semillero as l', 'l.id_lider_semi', '=', 's.id_lider_semi')
            ->where('s.id_semillero', $id)
            ->select(
                's.id_semillero',
                's.nombre',
                's.linea_investigacion',
                's.id_lider_semi',
                DB::raw("TRIM(CONCAT(COALESCE(l.nombres,''),' ',COALESCE(l.apellidos,''))) as lider_nombre"),
                'l.correo_institucional as lider_correo'
            )
            ->first();

        if (!$row) return response()->json(['error' => 'Semillero no encontrado'], 404);
        return response()->json($row);
    }

    /**
     * Busca líderes que NO tengan semillero asignado (para el buscador).
     * Si se pasa include_current, se permite mostrar también al líder actual.
     */
    public function lideresDisponibles(Request $request)
    {
        $q = trim($request->get('q',''));
        $includeCurrent = $request->integer('include_current'); // id_lider_semi actual (opcional)

        $query = DB::table('lideres_semillero as l')
            ->leftJoin('semilleros as s', 's.id_lider_semi', '=', 'l.id_lider_semi')
            ->when($includeCurrent, function($w) use ($includeCurrent) {
                $w->where(function($x) use ($includeCurrent) {
                    $x->whereNull('s.id_lider_semi')
                      ->orWhere('l.id_lider_semi', $includeCurrent);
                });
            }, function($w) {
                $w->whereNull('s.id_lider_semi');
            })
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function($s) use ($q){
                    $s->where('l.nombres', 'like', "%{$q}%")
                      ->orWhere('l.apellidos', 'like', "%{$q}%")
                      ->orWhere('l.correo_institucional', 'like', "%{$q}%");
                });
            })
            ->select(
                'l.id_lider_semi',
                DB::raw("TRIM(CONCAT(COALESCE(l.nombres,''),' ',COALESCE(l.apellidos,''))) as nombre"),
                'l.correo_institucional as correo'
            )
            ->orderBy('l.nombres')
            ->limit(20)
            ->get();

        return response()->json($query);
    }

    /**
     * Actualiza SOLO el semillero (nombre, línea) y la relación con el líder.
     * No modifica la tabla users ni datos del líder.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre'              => ['required','string','max:255', Rule::unique('semilleros','nombre')->ignore($id,'id_semillero')],
            'linea_investigacion' => ['required','string','max:255'],
            'id_lider_semi'       => ['nullable','integer','exists:lideres_semillero,id_lider_semi'],
        ]);

        // Si viene un nuevo líder, verificar que no esté ya asignado a otro semillero
        if (!empty($data['id_lider_semi'])) {
            $ocupado = DB::table('semilleros')
                ->where('id_lider_semi', $data['id_lider_semi'])
                ->where('id_semillero', '<>', $id)
                ->exists();

            if ($ocupado) {
                return back()->withErrors(['id_lider_semi' => 'Ese líder ya tiene un semillero asignado.'])
                             ->withInput();
            }
        }

        DB::table('semilleros')
            ->where('id_semillero', $id)
            ->update([
                'nombre'              => $data['nombre'],
                'linea_investigacion' => $data['linea_investigacion'],
                'id_lider_semi'       => $data['id_lider_semi'] ?? null,
                'updated_at'          => now(),
            ]);

        return back()->with('success', 'Semillero actualizado.');
    }

    /**
     * Eliminar semillero.
     */
    public function destroy($id)
    {
        DB::table('semilleros')->where('id_semillero', $id)->delete();
        return back()->with('success', 'Semillero eliminado.');
    }

public function show($id)
{
    // Datos del semillero (mock mientras tanto)
    $semillero = DB::table('semilleros')
        ->leftJoin('lideres_semillero', 'lideres_semillero.id_lider_semi', '=', 'semilleros.id_lider_semi')
        ->select(
            'semilleros.nombre',
            'semilleros.linea_investigacion as linea',
            DB::raw("CONCAT(lideres_semillero.nombres,' ',lideres_semillero.apellidos) as lider")
        )
        ->where('semilleros.id_semillero', $id)
        ->first();

    // Por ahora, sin proyectos reales
    return view('Admin.semilleros.show', compact('semillero'));
}

    

    
}
