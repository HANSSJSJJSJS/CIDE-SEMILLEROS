<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use App\Http\Requests\LiderSemillero\UpdateContactoRequest;
use App\Http\Requests\LiderSemillero\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class PerfilController extends Controller
{
    public function updateContacto(UpdateContactoRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $email = $request->validated()['email'];
        $telefono = $request->validated()['telefono'] ?? null;

        if ($user->email !== $email) {
            $user->email = $email;
            $user->email_verified_at = null;
        }
        $user->save();

        $lider = $user->liderSemillero;
        if ($lider && $telefono !== null) {
            if (Schema::hasColumn($lider->getTable(), 'telefono')) {
                $lider->telefono = $telefono;
                $lider->save();
            }
        }

        return Redirect::back()->with('status', 'contacto-actualizado');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->password = $request->validated()['password'];
        $user->save();

        return Redirect::back()->with('status', 'password-actualizado');
    }
}
