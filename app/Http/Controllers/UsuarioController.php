<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        // Obtenemos los usuarios
        $usuarios = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderByDesc('created_at')
            ->get();

        // Si es AJAX, devuelve solo la tabla
        if ($request->ajax()) {
            return view('admin.sections.gestion_usuario', compact('usuarios'));
        }

        // Si entra por navegador normal, devuelve la vista completa
        return view('admin.sections.gestion_usuario', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios._form');
    }

    public function store(Request $request)
    {
        // Aquí puedes implementar la lógica de crear usuarios
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);

        if ($request->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Usuario creado correctamente.']);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Usuario creado correctamente.');
    }
}
