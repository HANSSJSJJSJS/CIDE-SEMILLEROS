<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Administrador;
use App\Models\LiderGeneral;
use App\Models\LiderSemillero;
use App\Models\Aprendiz;
use Illuminate\Support\Facades\Hash;

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


    // Nuevo método para crear usuario vía AJAX
        /**
     * Crear usuario por AJAX (sin recargar la página)
     */
    public function storeAjax(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,APRENDIZ',
        ]);

        // Crear el usuario principal
        $user = User::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // Crear registro adicional según el rol
        switch ($request->role) {
            case 'ADMIN':
                Administrador::create(['id_usuario' => $user->id]);
                break;
            case 'LIDER_GENERAL':
                LiderGeneral::create(['id_usuario' => $user->id]);
                break;
            case 'LIDER_SEMILLERO':
                LiderSemillero::create(['id_usuario' => $user->id]);
                break;
            case 'APRENDIZ':
                Aprendiz::create(['id_usuario' => $user->id]);
                break;
        }

        // Responder JSON
        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente.',
            'user'    => $user
        ]);
    }















}


