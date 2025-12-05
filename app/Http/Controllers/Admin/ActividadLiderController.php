<?php 
// app/Http/Controllers/Admin/ActividadLiderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recurso;
use App\Models\Semillero;
use Illuminate\Http\Request;

class ActividadLiderController extends Controller
{
    // LISTA PRINCIPAL (vista index.blade.php que ya armamos)
    public function index()
    {
        $semilleros = Semillero::with(['lider', 'actividadesLider'])->get();

        // Agregamos contadores para que la vista los use
        $semilleros->each(function ($semillero) {
            $total = $semillero->actividadesLider->count();
            $pendientes = $semillero->actividadesLider
                ->filter(fn ($r) => $r->estado_calculado === 'pendiente')
                ->count();
            $aprobadas = $semillero->actividadesLider
                ->filter(fn ($r) => $r->estado_calculado === 'aprobado')
                ->count();

            $semillero->actividades_total = $total;
            $semillero->actividades_pendientes = $pendientes;
            $semillero->actividades_completadas = $aprobadas;
            $semillero->lider_nombre = optional($semillero->lider)->nombre_completo ?? null;
        });

        return view('admin.recursos.index', compact('semilleros'));
    }

    // JSON: actividades por semillero (modal)
    public function actividadesPorSemillero(Semillero $semillero)
    {
        $actividades = $semillero->actividadesLider()
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Recurso $r) use ($semillero) {
                return [
                    'id'            => $r->id,
                    'titulo'        => $r->nombre_archivo,
                    'descripcion'   => $r->descripcion,
                    'estado'        => $r->estado_calculado, // pendiente / aprobado / rechazado / vencido
                    'fecha_limite'  => optional($r->fecha_vencimiento)?->format('Y-m-d'),
                    'lider_nombre'  => optional($semillero->lider)->nombre_completo,
                ];
            });

        return response()->json([
            'actividades' => $actividades,
        ]);
    }

    // JSON: líder del semillero (para llenar el modal de creación)
    public function liderDeSemillero(Semillero $semillero)
    {
        $lider = $semillero->lider;

        if (!$lider) {
            return response()->json(['lider' => null]);
        }

        return response()->json([
            'lider' => [
                'id'             => $lider->id,
                'nombre_completo'=> $lider->nombre_completo,
            ],
        ]);
    }

    // CREA UNA NUEVA ACTIVIDAD (guarda en recursos)
    public function store(Request $request)
    {
        $data = $request->validate([
            'semillero_id'   => ['required', 'exists:semilleros,id_semillero'],
            'lider_id'       => ['required', 'exists:users,id'],
            'titulo'         => ['required', 'string', 'max:255'],
            'descripcion'    => ['required', 'string'],
            'tipo_actividad' => ['required', 'string', 'max:50'],
            'fecha_limite'   => ['required', 'date', 'after_or_equal:today'],
        ]);

        $recurso = new Recurso();
        $recurso->nombre_archivo    = $data['titulo'];        // lo usamos como título
        $recurso->archivo           = null;                   // si luego adjuntas algo, aquí va la ruta
        $recurso->categoria         = 'otros';                // o lo que quieras
        $recurso->dirigido_a        = 'lideres';
        $recurso->estado            = 'pendiente';
        $recurso->fecha_vencimiento = $data['fecha_limite'];
        $recurso->descripcion       = $data['descripcion'];
        $recurso->comentarios       = null;
        $recurso->semillero_id      = $data['semillero_id'];
        $recurso->user_id           = auth()->id();           // admin que crea

        $recurso->save();

        return response()->json([
            'success'    => true,
            'message'    => 'Actividad creada correctamente.',
            'recurso_id' => $recurso->id,
        ]);
    }
}
