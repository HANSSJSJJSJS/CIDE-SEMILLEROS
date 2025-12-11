<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function edit()
    {
        return view('admin.perfil.index'); // tu vista
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'email' => ['required','email','max:255','unique:users,email,'.$user->id],
        ]);
        $user->update(['email' => $request->email]);
        return back()->with('status', 'Información actualizada correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required','current_password'],
            'password'         => ['required','string','min:8','confirmed'],
        ]);
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);
        return back()->with('status', 'Contraseña actualizada correctamente.');
    }
}
