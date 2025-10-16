<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        // Mostrar lista de usuarios
    }

    public function create()
    {
        // Mostrar formulario de creación
    }

    public function store(Request $request)
    {
        // Guardar nuevo usuario
    }

    public function show($id)
    {
        // Mostrar un usuario específico
    }

    public function edit($id)
    {
        // Mostrar formulario de edición
    }

    public function update(Request $request, $id)
    {
        // Actualizar usuario
    }

    public function destroy($id)
    {
        // Eliminar usuario
    }
}
