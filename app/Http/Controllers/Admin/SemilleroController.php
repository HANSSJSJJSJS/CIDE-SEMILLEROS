<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Semillero;
use App\Models\Aprendiz;

class SemilleroController extends Controller
{
    /**
     * INDEX – Listado de semilleros con búsqueda + paginación
     */
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));

        // Resolver columnas reales según esquema
        $tblS = 'semilleros';
        $tblL = 'lideres_semillero';

        $sId   = Schema::hasColumn($tblS,'id_semillero') ? 'id_semillero' : 'id';
        $sNom  = Schema::hasColumn($tblS,'nombre')
                    ? 'nombre'
                    : (Schema::hasColumn($tblS,'nombre_semillero') ? 'nombre_semillero' : 'nombre');

        $sLinea= Schema::hasColumn($tblS,'linea_investigacion')
                    ? 'linea_investigacion'
                    : 'linea_investigacion';

        $sLider= Schema::hasColumn($tblS,'id_lider_semi')
                    ? 'id_lider_semi'
                    : (Schema::hasColumn($tblS,'id_lider_usuario') ? 'id_lider_usuario' : null);

        $lPk   = Schema::hasColumn($tblL,'id_lider_semi')
                    ? 'id_lider_semi'
                    : (Schema::hasColumn($tblL,'id_usuario') ? 'id_usuario' : null);

        // Columnas variables en lideres_semillero
        $lNom   = Schema::hasColumn($tblL,'nombres') ? 'nombres' : (Schema::hasColumn($tblL,'nombre') ? 'nombre' : null);
        $lApe   = Schema::hasColumn($tblL,'apellidos') ? 'apellidos' : (Schema::hasColumn($tblL,'apellido') ? 'apellido' : null);
        $lEmail = Schema::hasColumn($tblL,'correo_institucional') ? 'correo_institucional' : (Schema::hasColumn($tblL,'email') ? 'email' : null);

        $leaderNameExpr = $lNom && $lApe
            ? "TRIM(CONCAT(COALESCE(l.`$lNom`,''),' ',COALESCE(l.`$lApe`,'')))"
            : ($lNom ? "l.`$lNom`" : ($lApe ? "l.`$lApe`" : "NULL"));

        // Base de consulta
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
            DB::raw("$leaderNameExpr as lider_nombre"),
            DB::raw($lEmail ? "l.`$lEmail` as lider_correo" : "NULL as lider_correo"),
        ];

        $semilleros = $query
            ->select($selects)
            ->when($q !== '', function ($w) use ($q, $sNom, $sLinea, $lNom, $lApe, $lEmail) {
                $w->where(function ($s) use ($q, $sNom, $sLinea, $lNom, $lApe, $lEmail) {
                    $s->where(DB::raw("s.`{$sNom}`"), 'like', "%{$q}%")
                      ->orWhere(DB::raw("s.`{$sLinea}`"), 'like', "%{$q}%");
                    if ($lNom)   $s->orWhere(DB::raw("l.`{$lNom}`"), 'like', "%{$q}%");
                    if ($lApe)   $s->orWhere(DB::raw("l.`{$lApe}`"), 'like', "%{$q}%");
                    if ($lEmail) $s->orWhere(DB::raw("l.`{$lEmail}`"), 'like', "%{$q}%");
                });
            })
            ->orderBy(DB::raw("s.`{$sNom}`"))
            ->paginate(12)
            ->withQueryString();

        return view('Admin.semilleros.index', compact('semilleros','q'));
    }

    /**
     * STORE – Crear semillero
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'              => ['required','string','max:255'],
            'linea_investigacion' => ['required','string','max:255'],
            'id_lider_semi'       => ['nullable','integer'],
        ]);

        $tblS = 'semilleros';
        $tblL = 'lideres_semillero';

        $sNom  = Schema::hasColumn($tblS,'nombre')
                    ? 'nombre'
                    : (Schema::hasColumn($tblS,'nombre_semillero') ? 'nombre_semillero' : 'nombre');

        $sLinea= Schema::hasColumn($tblS,'linea_investigacion')
                    ? 'linea_investigacion'
                    : 'linea_investigacion';

        $sLider= Schema::hasColumn($tblS,'id_lider_semi')
                    ? 'id_lider_semi'
                    : (Schema::hasColumn($tblS,'id_lider_usuario') ? 'id_lider_usuario' : null);

        // Validar líder
        if (!empty($data['id_lider_semi']) && $sLider) {
            $lPk = Schema::hasColumn($tblL,'id_lider_semi')
                        ? 'id_lider_semi'
                        : (Schema::hasColumn($tblL,'id_usuario') ? 'id_usuario' : null);

            if ($lPk) {
                $exists = DB::table($tblL)->where($lPk, $data['id_lider_semi'])->exists();
                if (!$exists) {
                    return back()->withErrors([
                        'id_lider_semi' => 'El líder seleccionado no es válido.'
                    ])->withInput();
                }
            }
        }

        // Validar nombre único
        if (DB::table($tblS)->where($sNom, $data['nombre'])->exists()) {
            return back()
                ->withErrors(['nombre' => 'Ya existe un semillero con ese nombre.'])
                ->withInput();
        }

        // Evitar líder duplicado
        if (!empty($data['id_lider_semi']) && $sLider) {
            $ocupado = DB::table($tblS)->where($sLider, $data['id_lider_semi'])->exists();
            if ($ocupado) {
                return back()
                    ->withErrors(['id_lider_semi' => 'Ese líder ya tiene un semillero asignado.'])
                    ->withInput();
            }
        }

        // Insertar registro
        $insert = [
            $sNom   => $data['nombre'],
            $sLinea => $data['linea_investigacion'],
        ];

        if ($sLider) $insert[$sLider] = $data['id_lider_semi'] ?? null;

        if (Schema::hasColumn($tblS,'created_at'))      $insert['created_at'] = now();
        if (Schema::hasColumn($tblS,'updated_at'))      $insert['updated_at'] = now();

        DB::table($tblS)->insert($insert);

        return redirect()
            ->route('admin.semilleros.index')
            ->with('success', 'Se ha creado el semillero '.$data['nombre'].' correctamente.');
    }

    /**
     * EDIT – Datos para modal
     */
    public function edit($id)
    {
        $tblL = 'lideres_semillero';
        $lNom   = Schema::hasColumn($tblL,'nombres') ? 'nombres' : (Schema::hasColumn($tblL,'nombre') ? 'nombre' : null);
        $lApe   = Schema::hasColumn($tblL,'apellidos') ? 'apellidos' : (Schema::hasColumn($tblL,'apellido') ? 'apellido' : null);
        $lEmail = Schema::hasColumn($tblL,'correo_institucional') ? 'correo_institucional' : (Schema::hasColumn($tblL,'email') ? 'email' : null);

        $leaderNameExpr = $lNom && $lApe
            ? "TRIM(CONCAT(COALESCE(l.`$lNom`,''),' ',COALESCE(l.`$lApe`,'')))"
            : ($lNom ? "l.`$lNom`" : ($lApe ? "l.`$lApe`" : "NULL"));

        $row = DB::table('semilleros as s')
            ->leftJoin('lideres_semillero as l', 'l.id_lider_semi', '=', 's.id_lider_semi')
            ->where('s.id_semillero', $id)
            ->select(
                's.id_semillero',
                's.nombre',
                's.linea_investigacion',
                's.id_lider_semi',
                DB::raw("$leaderNameExpr as lider_nombre"),
                DB::raw($lEmail ? "l.`$lEmail` as lider_correo" : "NULL as lider_correo")
            )
            ->first();

        if (!$row) {
            return response()->json(['error' => 'Semillero no encontrado'], 404);
        }

        return response()->json($row);
    }

    /**
     * LISTA de líderes disponibles
     */
    public function lideresDisponibles(Request $request)
    {
        $q = trim($request->get('q',''));
        $includeCurrent = $request->integer('include_current');

        $tblL = 'lideres_semillero';
        $lNom   = Schema::hasColumn($tblL,'nombres') ? 'nombres' : (Schema::hasColumn($tblL,'nombre') ? 'nombre' : null);
        $lApe   = Schema::hasColumn($tblL,'apellidos') ? 'apellidos' : (Schema::hasColumn($tblL,'apellido') ? 'apellido' : null);
        $lEmail = Schema::hasColumn($tblL,'correo_institucional') ? 'correo_institucional' : (Schema::hasColumn($tblL,'email') ? 'email' : null);
        $orderCol = $lNom ?: ($lApe ?: ($lEmail ?: 'id_lider_semi'));

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
            ->when($q !== '', function ($w) use ($q, $lNom, $lApe, $lEmail) {
                $w->where(function($s) use ($q, $lNom, $lApe, $lEmail){
                    if ($lNom)   $s->where("l.$lNom", 'like', "%{$q}%");
                    if ($lApe)   $s->orWhere("l.$lApe", 'like', "%{$q}%");
                    if ($lEmail) $s->orWhere("l.$lEmail", 'like', "%{$q}%");
                });
            })
            ->select(
                'l.id_lider_semi',
                DB::raw((($lNom && $lApe)
                        ? "TRIM(CONCAT(COALESCE(l.`$lNom`,''),' ',COALESCE(l.`$lApe`,'')))"
                        : ($lNom ? "l.`$lNom`" : ($lApe ? "l.`$lApe`" : "'Líder'"))) . " as nombre"),
                DB::raw($lEmail ? "l.`$lEmail` as correo" : "NULL as correo")
            )
            ->orderBy("l.$orderCol")
            ->limit(20)
            ->get();

        return response()->json($query);
    }

    /**
     * UPDATE – Actualizar semillero
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre'              => ['required','string','max:150'],
            'linea_investigacion' => ['required','string','max:255'],
            'id_lider_semi'       => ['nullable','integer','exists:lideres_semillero,id_lider_semi'],
        ]);

        $tbl = 'semilleros';

        $idCol     = Schema::hasColumn($tbl,'id_semillero') ? 'id_semillero' : 'id';

        $colNombre = Schema::hasColumn($tbl,'nombre')
                        ? 'nombre'
                        : (Schema::hasColumn($tbl,'nombre_semillero') ? 'nombre_semillero' : null);

        $colLinea  = Schema::hasColumn($tbl,'linea_investigacion')
                        ? 'linea_investigacion'
                        : 'linea_investigacion';

        $colLider  = Schema::hasColumn($tbl,'id_lider_semi')
                        ? 'id_lider_semi'
                        : (Schema::hasColumn($tbl,'id_lider_usuario') ? 'id_lider_usuario' : null);

        // Verificar líder duplicado
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

        // Construir update
        $update = [];
        if ($colNombre) $update[$colNombre] = $data['nombre'];
        if ($colLinea)  $update[$colLinea]  = $data['linea_investigacion'];
        if ($colLider)  $update[$colLider]  = $data['id_lider_semi'] ?? null;

        if (Schema::hasColumn($tbl,'updated_at')) $update['updated_at'] = now();

        DB::table($tbl)->where($idCol, $id)->update($update);

        return back()
            ->with('success', 'Se ha actualizado el semillero '.$data['nombre'].' correctamente.');
    }

    /**
     * SHOW + asignarAprendiz + quitarAprendiz (sin cambios)
     */
    public function show($id)
    {
        try {
            $semillero = \App\Models\Semillero::with(['aprendices', 'lider'])
                ->findOrFail($id);

            $aprendices = \App\Models\Aprendiz
                ::whereNull('semillero_id')
                ->orWhere('semillero_id','')
                ->where('estado','Activo')
                ->get();

            return view('admin.semilleros.show', compact('semillero','aprendices'));

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.semilleros.index')
                ->with('error', 'Error al cargar el semillero: '.$e->getMessage());
        }
    }

    public function asignarAprendiz(Request $request, $idSemillero)
    {
        $request->validate([
            'aprendiz_id' => 'required|exists:aprendices,id_usuario'
        ]);

        try {
            DB::beginTransaction();

            $aprendiz = \App\Models\Aprendiz::findOrFail($request->aprendiz_id);
            $aprendiz->update(['semillero_id'=>$idSemillero]);

            DB::commit();
            return response()->json(['success'=>true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success'=>false],500);
        }
    }

    public function quitarAprendiz($idSemillero, $idAprendiz)
    {
        try {
            DB::beginTransaction();

            $aprendiz = \App\Models\Aprendiz
                ::where('id_usuario',$idAprendiz)
                ->where('semillero_id',$idSemillero)
                ->firstOrFail();

            $aprendiz->update(['semillero_id'=>null]);

            DB::commit();
            return response()->json(['success'=>true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success'=>false],500);
        }
    }

  // ============================================================
// ELIMINAR SEMILLERO (con restricción de proyectos asociados)
// ============================================================
public function destroy($id)
{
    $tbl = 'semilleros';

    // Detectar columna ID
    $idCol = Schema::hasColumn($tbl, 'id_semillero') ? 'id_semillero' : 'id';

    // Detectar columna nombre
    $colNombre = Schema::hasColumn($tbl, 'nombre')
        ? 'nombre'
        : (Schema::hasColumn($tbl, 'nombre_semillero')
            ? 'nombre_semillero'
            : $idCol);

    // Buscar el semillero
    $row = DB::table($tbl)->where($idCol, $id)->first();

    if (!$row) {
        return redirect()
            ->route('admin.semilleros.index')
            ->with('error', 'El semillero no existe.');
    }

    $nombre = $row->{$colNombre} ?? 'semillero';

    // ===============================================
    // 1) RESTRICCIÓN: ¿TIENE PROYECTOS ASOCIADOS?
    // ===============================================
    if (Schema::hasTable('proyectos')) {

        $tblP = 'proyectos';

        // Intentar detectar el nombre de la FK al semillero
        $fkCol = null;
        foreach (['semillero_id', 'id_semillero', 'id_semillero_fk'] as $c) {
            if (Schema::hasColumn($tblP, $c)) {
                $fkCol = $c;
                break;
            }
        }

        if ($fkCol) {
            $tieneProyectos = DB::table($tblP)
                ->where($fkCol, $id)
                ->exists();

            if ($tieneProyectos) {
                return redirect()
                    ->route('admin.semilleros.index')
                    ->with(
                        'error',
                        'No se puede eliminar el semillero "'.$nombre.
                        '" porque tiene proyectos asociados.'
                    );
            }
        }
    }

    // ===============================================
    // 2) Si no tiene proyectos, se puede eliminar
    // ===============================================
    DB::table($tbl)->where($idCol, $id)->delete();

    return redirect()
        ->route('admin.semilleros.index')
        ->with('success', 'Se ha eliminado el semillero "'.$nombre.'" correctamente.');
}

}
