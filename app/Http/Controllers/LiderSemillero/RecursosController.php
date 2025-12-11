<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecursosController extends Controller
{
    /**
     * Mostrar recursos del líder + info del semillero.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // ================================
        // OBTENER SEMILLERO ASIGNADO
        // ================================
        $semillero = DB::table('semilleros')
            ->where('id_lider_semi', $userId)
            ->first();

        // ================================
        // OBTENER RECURSOS SUBIDOS POR EL LÍDER
        // ================================
        $recursos = DB::table('recursos')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get();

        // Mostrar vista correcta
        return view('lider_semi.recursos.index', [
            'semillero' => $semillero,
            'recursos'  => $recursos,
        ]);
    }

    /**
     * Vista para subir un recurso.
     */
    public function create()
    {
        $userId = Auth::id();

        // Obtener el semillero
        $semillero = DB::table('semilleros')
            ->where('id_lider_semi', $userId)
            ->first();

        return view('lider_semi.recursos.create', compact('semillero'));
    }

    /**
     * Guardar un recurso en la base de datos.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'nombre_archivo' => 'nullable|string|max:255',
            'archivo'        => 'required|file|max:20480|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,png,jpg,jpeg',
            'categoria'      => 'required|in:plantillas,manuales,otros',
            'descripcion'    => 'nullable|string',
        ]);

        // Subida y renombre del archivo
        $file     = $request->file('archivo');
        $original = $file->getClientOriginalName();
        $filename = time() . '_' . $userId . '_' . $original;

        $path = $file->storeAs('recursos', $filename, 'public');

        DB::table('recursos')->insert([
            'nombre_archivo' => $request->nombre_archivo ?: $original,
            'archivo'        => $path,
            'categoria'      => $request->categoria,
            'descripcion'    => $request->descripcion,
            'user_id'        => $userId,
            'estado'         => 'pendiente',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()
            ->route('lider_semi.recursos.index')
            ->with('success', 'Recurso subido correctamente.');
    }

    /**
     * Descargar un recurso si pertenece al líder.
     */
    public function download($id)
    {
        $userId = Auth::id();

        $rec = DB::table('recursos')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$rec)
            return back()->with('error', 'Recurso no encontrado.');

        if (!Storage::disk('public')->exists($rec->archivo))
            return back()->with('error', 'Archivo no disponible.');

        return response()->download(storage_path('app/public/' . $rec->archivo));
    }

    /**
     * Eliminar un recurso solo si está pendiente.
     */
    public function destroy($id)
    {
        $userId = Auth::id();

        $rec = DB::table('recursos')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$rec)
            return back()->with('error', 'Recurso no encontrado.');

        if ($rec->estado !== 'pendiente')
            return back()->with('error', 'Solo se pueden eliminar recursos pendientes.');

        if (Storage::disk('public')->exists($rec->archivo)) {
            Storage::disk('public')->delete($rec->archivo);
        }

        DB::table('recursos')->where('id', $id)->delete();

        return back()->with('success', 'Recurso eliminado correctamente.');
    }

}
