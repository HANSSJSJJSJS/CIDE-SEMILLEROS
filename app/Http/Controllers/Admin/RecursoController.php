<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recurso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecursoController extends Controller
{
    // GET /admin/recursos  -> vista
    public function index()
    {
        return view('admin.recursos.index');
    }

    // GET /admin/recursos/listar  -> JSON que consume tu JS
    public function listar(Request $request)
    {
        $q   = trim((string) $request->get('q'));
        $cat = (string) $request->get('categoria');

        $user  = Auth::user();
        $query = Recurso::query();

        // ==========================
        // FILTRO POR ROL + dirigido_a
        // ==========================
        if ($user) {
            if ($user->rol === 'admin') {
                // admin ve todo
            } elseif ($user->rol === 'aprendiz') {
                $query->whereIn('dirigido_a', ['todos', 'aprendices']);
            } elseif ($user->rol === 'lider') {
                $query->whereIn('dirigido_a', ['todos', 'lideres']);
            }
        }

        // Filtro por texto
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nombre_archivo', 'like', "%{$q}%")
                  ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        // Filtro por categoría
        if ($cat !== '') {
            $query->where('categoria', $cat);
        }

        $items = $query->latest('id')->get();

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $esAdmin = $user && $user->rol === 'admin';

        $data = $items->map(function (Recurso $r) use ($disk, $esAdmin) {
            $exists = $disk->exists($r->archivo);
            $mime   = $exists ? $disk->mimeType($r->archivo) : null;
            $size   = $exists ? (int) $disk->size($r->archivo) : null;
            $url    = $exists ? $disk->url($r->archivo) : '#';

            return [
                'id'          => $r->id,
                'titulo'      => $r->nombre_archivo,
                'descripcion' => $r->descripcion,
                'categoria'   => $r->categoria,
                'dirigido_a'  => $r->dirigido_a,
                'mime'        => $mime,
                'size'        => $size,
                'url'         => $url,
                'download'    => route('admin.recursos.download', $r->id),
                'created_at'  => optional($r->created_at)->toDateTimeString(),
                'updated_at'  => optional($r->updated_at)->toDateTimeString(),
                'can_delete'  => $esAdmin,
            ];
        });

        return response()->json(['data' => $data]);
    }

    // POST /admin/recursos  -> subir archivo
    public function store(Request $request)
    {
        $request->validate([
            'nombre_archivo' => ['required','string','max:255'],
            'categoria'      => ['required','in:plantillas,manuales,otros'],
            'dirigido_a'     => ['required','in:todos,aprendices,lideres'],
            'descripcion'    => ['nullable','string'],
            'archivo'        => ['required','file','max:20480'],
        ]);

        $file = $request->file('archivo');
        $path = $file->store('recursos', 'public');

        $recurso = Recurso::create([
            'nombre_archivo' => $request->nombre_archivo,
            'categoria'      => $request->categoria,
            'dirigido_a'     => $request->dirigido_a,
            'descripcion'    => $request->descripcion,
            'archivo'        => $path,
            'user_id'        => Auth::id(),
        ]);

        return response()->json([
            'ok' => true,
            'id' => $recurso->id,
        ], 201);
    }

    // GET /admin/recursos/{recurso}/dl  -> descarga
    public function download(Recurso $recurso)
    {
        $disk = Storage::disk('public');

        if (!$disk->exists($recurso->archivo)) {
            abort(404, 'Archivo no encontrado');
        }

        $downloadName = $recurso->nombre_archivo;
        $ext = pathinfo($recurso->archivo, PATHINFO_EXTENSION);

        if ($ext && !Str::endsWith(Str::lower($downloadName), '.'.Str::lower($ext))) {
            $downloadName .= '.' . $ext;
        }

        $absolutePath = $disk->path($recurso->archivo);
        return response()->download($absolutePath, $downloadName);
    }

    // DELETE /admin/recursos/{id}
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user || $user->rol !== 'admin') {
            abort(403, 'No autorizado');
        }

        $recurso = Recurso::findOrFail($id);
        $disk = Storage::disk('public');

        if ($recurso->archivo && $disk->exists($recurso->archivo)) {
            $disk->delete($recurso->archivo);
        }

        $recurso->delete();

        return response()->json(['ok' => true]);
    }
}
