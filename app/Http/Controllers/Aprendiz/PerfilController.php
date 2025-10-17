<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Mostrar datos personales del aprendiz
        return view('aprendiz.perfil.show', compact('user'));
    }
}
