<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class SemilleroController extends Controller
{
    /**
     * Listado con líder actual (solo lectura).
     */
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));

        // Resolver columnas reales segun esquema
        $tblS = 'semilleros';
        $tblL = 'lideres_semillero';
        $sId   = Schema::hasColumn($tblS,'id_semillero') ? 'id_semillero' : 'id';
        $sNom  = Schema::hasColumn($tblS,'nombre') ? 'nombre' : (Schema::hasColumn($tblS,'nombre_semillero') ? 'nombre_semillero' : 'nombre');
        $sLinea= Schema::hasColumn($tblS,'linea_investigacion') ? 'linea_investigacion' : 'linea_investigacion';
        $sLider= Schema::hasColumn($tblS,'id_lider_semi') ? 'id_lider_semi' : (Schema::hasColumn($tblS,'id_lider_usuario') ? 'id_lider_usuario' : null);

        $lPk   = Schema::hasColumn($tblL,'id_lider_semi') ? 'id_lider_semi' : (Schema::hasColumn($tblL,'id_usuario') ? 'id_usuario' : null);

        $query = DB::table($tblS.' as s');
        if ($sLider && $lPk) {
            $query->leftJoin($tblL.' as l', 'l.'.$lPk, '=', 's.'.$sLider);
        } else {
            $query->leftJoin($tblL.' as l', DB::raw('1'), DB::raw('1'));
        }

        $selects = [
            's.'.$sId.' as id_semillero',
            DB::raw("s.`{$sNom}` as nombre"),
            's.'.$sLinea.' as linea_investigacion',
            $sLider ? DB::raw('s.`'.$sLider.'` as id_lider_semi') : DB::raw('NULL as id_lider_semi'),
            DB::raw("TRIM(CONCAT(COALESCE(l.nombres,''),' ',COALESCE(l.apellidos,''))) as lider_nombre"),
            DB::raw("l.correo_institucional as lider_correo"),
        ];

        $semilleros = $query
            ->select($selects)
            ->when($q !== '', function ($w) use ($q, $sNom, $sLinea) {
                $w->where(function ($s) use ($q, $sNom, $sLinea) {
                    $s->where(DB::raw("s.`{$sNom}`"), 'like', "%{$q}%")
                      ->orWhere(DB::raw("s.`{$sLinea}`"), 'like', "%{$q}%")
                      ->orWhere(DB::raw("CONCAT(COALESCE(l.nombres,''),' ',COALESCE(l.apellidos,''))"), 'like', "%{$q}%");
                });
            })
            ->orderBy(DB::raw("s.`{$sNom}`"))
            ->paginate(10)
            ->withQueryString();

        return view('Admin.semilleros.index', compact('semilleros','q'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'              => ['required','string','max:255'],
            'linea_investigacion' => ['required','string','max:255'],
            'id_lider_semi'       => ['nullable','integer'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
        ], [
            'nombre'              => 'nombre del semillero',
            'linea_investigacion' => 'línea de investigación',
            'id_lider_semi'       => 'líder de semillero',
        ]);

        $tblS = 'semilleros';
        $tblL = 'lideres_semillero';
        $sNom  = Schema::hasColumn($tblS,'nombre') ? 'nombre' : (Schema::hasColumn($tblS,'nombre_semillero') ? 'nombre_semillero' : 'nombre');
        $sLinea= Schema::hasColumn($tblS,'linea_investigacion') ? 'linea_investigacion' : 'linea_investigacion';
        $sLider= Schema::hasColumn($tblS,'id_lider_semi') ? 'id_lider_semi' : (Schema::hasColumn($tblS,'id_lider_usuario') ? 'id_lider_usuario' : null);

        // Validar 'exists' del líder contra la PK real
        if (!empty($data['id_lider_semi']) && $sLider) {
            $lPk = Schema::hasColumn($tblL,'id_lider_semi') ? 'id_lider_semi' : (Schema::hasColumn($tblL,'id_usuario') ? 'id_usuario' : null);
            if ($lPk) {
                $exists = DB::table($tblL)->where($lPk, $data['id_lider_semi'])->exists();
                if (!$exists) {
                    return back()->withErrors(['id_lider_semi' => 'El líder seleccionado no es válido.'])->withInput();
                }
            }
        }

<<<<<<< HEAD
        // Unicidad manual por nombre
        $dup = DB::table($tblS)->where($sNom, $data['nombre'])->exists();
        if ($dup) {
            return back()->withErrors(['nombre' => 'Ya existe un semillero con ese nombre.'])->withInput();
        }

        // Evitar líder duplicado en otro semillero
        if (!empty($data['id_lider_semi']) && $sLider) {
            $ocupado = DB::table($tblS)->where($sLider, $data['id_lider_semi'])->exists();
            if ($ocupado) {
                return back()->withErrors(['id_lider_semi' => 'Ese líder ya tiene un semillero asignado.'])->withInput();
            }
        }

        $insert = [
            $sNom   => $data['nombre'],
            $sLinea => $data['linea_investigacion'],
        ];
        if ($sLider) { $insert[$sLider] = $data['id_lider_semi'] ?? null; }
        if (Schema::hasColumn($tblS,'created_at')) { $insert['created_at'] = now(); }
        if (Schema::hasColumn($tblS,'updated_at')) { $insert['updated_at'] = now(); }
        if (Schema::hasColumn($tblS,'creado_en'))   { $insert['creado_en']   = now(); }
        if (Schema::hasColumn($tblS,'actualizado_en')) { $insert['actualizado_en'] = now(); }

        DB::table($tblS)->insert($insert);
=======
        $insert = [
            'nombre'              => $data['nombre'],
            'linea_investigacion' => $data['linea_investigacion'],
            'id_lider_semi'       => $data['id_lider_semi'] ?? null,
        ];

        // Si la tabla usa PK 'id_semillero' sin autoincrement, calcular siguiente id
        $tbl = 'semilleros';
        if (Schema::hasColumn($tbl, 'id_semillero') && !Schema::hasColumn($tbl, 'id')) {
            // Intenta detectar si requiere valor manual (no AI) asumiendo NOT NULL sin default
            // Estrategia simple: setear id = max + 1
            $next = (int) (DB::table($tbl)->max('id_semillero') ?? 0) + 1;
            $insert['id_semillero'] = $next;
        }

        DB::table('semilleros')->insert($insert);
>>>>>>> PreFu

        return redirect()->route('admin.semilleros.index')
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
    // 1) Validar lo que realmente envía el form: id_lider_semi (opcional)
    $data = $request->validate([
        'nombre'              => ['required','string','max:150'],
        'linea_investigacion' => ['required','string','max:255'],
        'id_lider_semi'       => ['nullable','integer','exists:lideres_semillero,id_lider_semi'],
    ], [], [
        'nombre'              => 'nombre',
        'linea_investigacion' => 'línea de investigación',
        'id_lider_semi'       => 'líder de semillero',
    ]);

    $tbl = 'semilleros';

    // 2) Resolver nombres reales de columnas según tu esquema
    $idCol     = Schema::hasColumn($tbl,'id_semillero') ? 'id_semillero' : 'id';
    $colNombre = Schema::hasColumn($tbl,'nombre') ? 'nombre'
                : (Schema::hasColumn($tbl,'nombre_semillero') ? 'nombre_semillero' : null);
    $colLinea  = Schema::hasColumn($tbl,'linea_investigacion') ? 'linea_investigacion'
                : (Schema::hasColumn($tbl,'línea_investigación') ? 'línea_investigación' : null);
    $colLider  = Schema::hasColumn($tbl,'id_lider_semi') ? 'id_lider_semi'
                : (Schema::hasColumn($tbl,'id_lider_usuario') ? 'id_lider_usuario' : null);

    // 3) (Opcional) Evitar que el mismo líder quede en dos semilleros
    if (!empty($data['id_lider_semi']) && $colLider) {
        $ocupado = DB::table($tbl)
            ->where($colLider, $data['id_lider_semi'])
            ->where($idCol, '<>', $id)
            ->exists();

        if ($ocupado) {
            return back()
                ->withErrors(['id_lider_semi' => 'Ese líder ya tiene un semillero asignado.'])
                ->withInput();
        }
    }

    // 4) Armar update
    $update = [];
    if ($colNombre) $update[$colNombre] = $data['nombre'];
    if ($colLinea)  $update[$colLinea]  = $data['linea_investigacion'];
    if ($colLider)  $update[$colLider]  = $data['id_lider_semi'] ?? null;

    if (Schema::hasColumn($tbl,'updated_at'))     $update['updated_at']     = now();
    if (Schema::hasColumn($tbl,'actualizado_en')) $update['actualizado_en'] = now();

    DB::table($tbl)->where($idCol, $id)->update($update);

    return back()->with('success', 'Semillero actualizado correctamente.');
}

    

    
}
