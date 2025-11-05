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

        $query = Recurso::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nombre_archivo', 'like', "%{$q}%")
                  ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        if ($cat !== '') {
            $query->where('categoria', $cat);
        }

        $items = $query->latest('id')->get();

        // Para ayudar al autocompletado del IDE
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $data = $items->map(function (Recurso $r) use ($disk) {
            $exists = $disk->exists($r->archivo);
            $mime   = $exists ? $disk->mimeType($r->archivo) : null;
            $size   = $exists ? (int) $disk->size($r->archivo) : null;
            $url    = $exists ? $disk->url($r->archivo) : '#';

            return [
                'id'          => $r->id,
                // la vista usa 'titulo' para mostrar
                'titulo'      => $r->nombre_archivo,
                'descripcion' => $r->descripcion,
                'categoria'   => $r->categoria,      // 'plantillas'|'manuales'|'otros'
                'mime'        => $mime,
                'size'        => $size,
                'url'         => $url,
                'download'    => route('admin.recursos.download', ['recurso' => $r->id], false),
                'created_at'  => optional($r->created_at)->toDateTimeString(),
                'updated_at'  => optional($r->updated_at)->toDateTimeString(),
            ];
        });

        return response()->json(['data' => $data]);
    }

    // POST /admin/recursos  -> subir archivo
    public function store(Request $request)
    {
        // Debe coincidir con los names del form
        $request->validate([
            'nombre_archivo' => ['required','string','max:255'],
            'categoria'      => ['required','in:plantillas,manuales,otros'],
            'descripcion'    => ['nullable','string'],
            'archivo'        => ['required','file','max:20480'], // 20 MB
        ]);

        $file = $request->file('archivo');

        // Guarda en storage/app/public/recursos y devuelve "recursos/xxxxx.ext"
        $path = $file->store('recursos', 'public');

        $recurso = Recurso::create([
            'nombre_archivo' => $request->nombre_archivo,
            'categoria'      => $request->categoria,
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

    // nombre de descarga amigable con extensión
    $downloadName = $recurso->nombre_archivo;
    $ext = pathinfo($recurso->archivo, PATHINFO_EXTENSION);
    if ($ext && !Str::endsWith(Str::lower($downloadName), '.'.Str::lower($ext))) {
        $downloadName .= '.'.$ext;
    }

    // ruta física absoluta y respuesta de descarga (sin warnings del IDE)
    $absolutePath = $disk->path($recurso->archivo);
    return response()->download($absolutePath, $downloadName);
}

}
