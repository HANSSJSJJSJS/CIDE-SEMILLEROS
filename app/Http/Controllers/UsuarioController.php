<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    // Dashboard admin: muestra widgets + tabla de usuarios
    public function dashboard()
    {
        $users = User::select(['id','name','email','created_at'])
            ->latest('id')
            ->paginate(10);

        return view('admin.dashboard-admin', compact('users'));
    }

    // Listado dedicado (opcional) en /admin/usuarios
    public function index()
    {
        $users = User::select(['id','name','email','created_at'])
            ->latest('id')
            ->paginate(10);

        return view('usuarios.index', compact('users'));
    }

    public function create(){ return view('usuarios.create'); }
    public function store(Request $r){ /* ... */ }
    public function show($id){ /* ... */ }
    public function edit($id){ /* ... */ }
    public function update(Request $r,$id){ /* ... */ }
    public function destroy($id){ /* ... */ }
}
