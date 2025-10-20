<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Semillero;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SemilleroController extends Controller
{
    // Muestra los semilleros asociados al líder y la vista correspondiente
    public function semilleros()
    {
        $userId = auth()->id();

        // Si existe la tabla de proyectos, mostrar proyectos como "Mis Proyectos"
        if (Schema::hasTable('proyectos')) {
            $pcols = [];
            // nombre
            if (Schema::hasColumn('proyectos', 'nombre_proyecto')) {
                $pcols[] = DB::raw('nombre_proyecto as nombre');
            } else {
                $pcols[] = DB::raw("'' as nombre");
            }
            // descripcion
            if (Schema::hasColumn('proyectos', 'descripcion')) {
                $pcols[] = 'descripcion';
            } else {
                $pcols[] = DB::raw("'' as descripcion");
            }
            // estado
            if (Schema::hasColumn('proyectos', 'estado')) {
                $pcols[] = 'estado';
            } else {
                $pcols[] = DB::raw("'EN_EJECUCION' as estado");
            }
            // progreso (placeholder)
            $pcols[] = DB::raw('0 as progreso');
            // aprendices (placeholder)
            $pcols[] = DB::raw('0 as aprendices');

            $proyectos = DB::table('proyectos')->select($pcols)->get();

            // Reusar la misma vista, mapeando proyectos a la colección esperada
            $semilleros = $proyectos;
            return view('lider_semi.semilleros', compact('semilleros'));
        }

        $query = Semillero::query();

        // Selecciones seguras con alias para columnas que la vista usa (sin asumir PK 'id')
        $cols = [];
        if (Schema::hasColumn('semilleros', 'nombre')) {
            $cols[] = 'nombre';
        } else {
            $cols[] = DB::raw("'' as nombre");
        }
        if (Schema::hasColumn('semilleros', 'estado')) {
            $cols[] = 'estado';
        } else {
            // si no existe, alias como 'Activo'
            $cols[] = DB::raw("'Activo' as estado");
        }
        if (Schema::hasColumn('semilleros', 'descripcion')) {
            $cols[] = 'descripcion';
        } else {
            $cols[] = DB::raw("'' as descripcion");
        }
        if (Schema::hasColumn('semilleros', 'progreso')) {
            $cols[] = 'progreso';
        } else {
            $cols[] = DB::raw('0 as progreso');
        }
        if (Schema::hasColumn('semilleros', 'aprendices')) {
            $cols[] = 'aprendices';
        } else {
            $cols[] = DB::raw('0 as aprendices');
        }
        $query->select($cols);

        if (Schema::hasColumn('semilleros', 'id_lider_semi')) {
            $query->where('id_lider_semi', $userId);
        }

        if (Schema::hasColumn('semilleros', 'estado')) {
            $query->whereIn('estado', ['Activo', 'ACTIVO']);
        }

        $semilleros = $query->get();

        return view('lider_semi.semilleros', compact('semilleros'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        // validar y guardar
    }

    public function show($id)
    {
        // mostrar un semillero
    }

    public function edit($id)
    {
        // formulario de edición
    }

    public function update(Request $request, $id)
    {
        // actualizar semillero
    }

    public function destroy($id)
    {
        // eliminar semillero
    }
}
