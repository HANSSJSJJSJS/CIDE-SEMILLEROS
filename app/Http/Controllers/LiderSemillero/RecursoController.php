<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\Recurso;
use App\Models\Semillero;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
            'id_recurso' => 'required',
            'respuesta'  => 'nullable|string',
        ]);

        $recurso = Recurso::find($request->id_recurso);
        if (!$recurso) {
            return response()->json(['success' => false, 'message' => 'Recurso no encontrado.'], 404);
        }

        $tipo = strtolower(trim((string) ($recurso->tipo_documento ?? '')));
        $esLink = in_array($tipo, ['enlace', 'link', 'url'], true);

        $rules = [];
        if ($esLink) {
            $rules['enlace_respuesta'] = 'required|url|max:2048';
        } else {
            $fileRule = 'required|file|max:20480';
            if (in_array($tipo, ['pdf'], true)) {
                $fileRule .= '|mimes:pdf';
            } elseif (in_array($tipo, ['documento', 'doc', 'docx', 'word'], true)) {
                $fileRule .= '|mimes:doc,docx';
            } elseif (in_array($tipo, ['presentacion', 'presentación', 'ppt', 'pptx'], true)) {
                $fileRule .= '|mimes:ppt,pptx';
            } elseif (in_array($tipo, ['imagen', 'img', 'image'], true)) {
                $fileRule .= '|mimes:jpg,jpeg,png,gif,webp';
            } elseif (in_array($tipo, ['video', 'mp4', 'avi', 'mov', 'mkv', 'webm'], true)) {
                $fileRule .= '|mimes:mp4,avi,mov,mkv,webm';
            }
            $rules['archivo_respuesta'] = $fileRule;
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        try {
            if (Schema::hasTable('recursos') && !Schema::hasColumn('recursos', 'respuesta')) {
                Schema::table('recursos', function ($table) {
                    $table->text('respuesta')->nullable();
                });
            }
            if (Schema::hasTable('recursos') && !Schema::hasColumn('recursos', 'archivo_respuesta')) {
                Schema::table('recursos', function ($table) {
                    $table->string('archivo_respuesta')->nullable();
                });
            }
            if (Schema::hasTable('recursos') && !Schema::hasColumn('recursos', 'enlace_respuesta')) {
                Schema::table('recursos', function ($table) {
                    $table->string('enlace_respuesta')->nullable();
                });
            }
            if (Schema::hasTable('recursos') && !Schema::hasColumn('recursos', 'respondido_en')) {
                Schema::table('recursos', function ($table) {
                    $table->dateTime('respondido_en')->nullable();
                });
            }
        } catch (\Throwable $e) {
            // continuar sin alterar esquema
        }

        $cols = [];
        try {
            if (Schema::hasTable('recursos')) {
                $cols = Schema::getColumnListing('recursos');
            }
        } catch (\Throwable $e) {
            $cols = [];
        }
        $hasCol = function (string $col) use ($cols): bool {
            return in_array($col, $cols, true);
        };

        $archivoPath = null;
        $enlace = null;
        if ($esLink) {
            $enlace = $request->input('enlace_respuesta');
        } elseif ($request->hasFile('archivo_respuesta')) {
            // Si ya existía una respuesta previa, borrar el archivo anterior para evitar duplicados
            try {
                if (in_array('archivo_respuesta', $cols, true)) {
                    $old = (string)($recurso->archivo_respuesta ?? '');
                    if ($old !== '' && $old !== 'sin_archivo' && Storage::disk('public')->exists($old)) {
                        Storage::disk('public')->delete($old);
                    }
                } elseif (in_array('archivo', $cols, true)) {
                    $oldA = (string)($recurso->archivo ?? '');
                    if ($oldA !== '' && $oldA !== 'sin_archivo' && stripos($oldA, 'recursos/respuestas') !== false) {
                        if (Storage::disk('public')->exists($oldA)) {
                            Storage::disk('public')->delete($oldA);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // continuar
            }
            $archivoPath = $request->file('archivo_respuesta')->store('recursos/respuestas', 'public');
        }

        $update = [];

        $respuestaTxt = trim((string)$request->input('respuesta', ''));

        // Guardar texto de respuesta en la columna más adecuada
        if ($respuestaTxt !== '') {
            if ($hasCol('respuesta')) {
                $update['respuesta'] = $respuestaTxt;
            } elseif ($hasCol('comentarios')) {
                $update['comentarios'] = $respuestaTxt;
            }
        }

        if ($hasCol('respondido_en')) {
            $update['respondido_en'] = now();
        }

        if ($hasCol('estado')) {
            // La columna estado suele ser ENUM (p.ej. pendiente/aprobado/rechazado).
            // Evitar asignar valores no permitidos como 'respondido' que generan error 500.
            $estadoActual = strtolower(trim((string)($recurso->estado ?? '')));
            // Si estaba rechazado/vencido, reabrir para revisión
            if (in_array($estadoActual, ['rechazado', 'vencido'], true)) {
                $update['estado'] = 'pendiente';

                // Limpiar observación de rechazo si existe y no estamos usando comentarios para guardar respuesta
                if ($hasCol('comentarios') && !array_key_exists('comentarios', $update)) {
                    $update['comentarios'] = null;
                }
            } elseif ($estadoActual === '') {
                $update['estado'] = 'pendiente';
            }
        }

        // Guardar archivo/enlace de respuesta
        if (!empty($archivoPath)) {
            // Si se sube archivo, limpiar enlace previo
            if ($hasCol('enlace_respuesta')) {
                $update['enlace_respuesta'] = null;
            }
            if ($hasCol('archivo_respuesta')) {
                $update['archivo_respuesta'] = $archivoPath;
            } elseif ($hasCol('archivo')) {
                $archivoActual = (string)($recurso->archivo ?? '');
                // Evitar sobreescribir un archivo original real
                if ($archivoActual === '' || $archivoActual === 'sin_archivo') {
                    $update['archivo'] = $archivoPath;
                } elseif ($hasCol('comentarios')) {
                    $base = array_key_exists('comentarios', $update)
                        ? trim((string)$update['comentarios'])
                        : trim((string)($recurso->comentarios ?? ''));
                    $update['comentarios'] = trim($base . "\nArchivo respuesta: " . $archivoPath);
                }
            }
        }

        if (!empty($enlace)) {
            // Si se envía enlace, limpiar archivo previo
            if ($hasCol('archivo_respuesta')) {
                $update['archivo_respuesta'] = null;
            }
            if ($hasCol('enlace_respuesta')) {
                $update['enlace_respuesta'] = $enlace;
            } elseif ($hasCol('comentarios')) {
                $base = array_key_exists('comentarios', $update)
                    ? trim((string)$update['comentarios'])
                    : trim((string)($recurso->comentarios ?? ''));
                $update['comentarios'] = trim($base . "\nEnlace respuesta: " . $enlace);
            }
        }

        if (!empty($update)) {
            DB::table('recursos')->where('id_recurso', $recurso->id_recurso)->update($update);
        }

        // Notificar a admins y líderes generales
        try {
            if (Schema::hasTable('notificaciones')) {
                $lideres = DB::table('users')
                    ->whereIn('role', ['admin', 'lider_general'])
                    ->pluck('id');

                $tituloRecurso = $recurso->titulo ?? $recurso->nombre_archivo ?? 'Recurso';

                foreach ($lideres as $lid) {
                    DB::table('notificaciones')->insert([
                        'id_usuario' => $lid,
                        'titulo'     => 'Nuevo recurso respondido',
                        'mensaje'    => 'El líder de semillero respondió el recurso: ' . $tituloRecurso,
                        'tipo'       => 'recurso',
                        'leida'      => 0,
                        'created_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // continuar sin notificar
        }

        $viewUrl = null;
        if (!empty($enlace)) {
            $viewUrl = $enlace;
        } elseif (!empty($archivoPath)) {
            $viewUrl = Storage::disk('public')->url($archivoPath);
        }

        return response()->json([
            'success' => true,
            'message' => 'Respuesta enviada correctamente.',
            'view_url' => $viewUrl,
        ]);
    }
}
