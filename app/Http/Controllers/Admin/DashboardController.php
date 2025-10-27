<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::select('id','name','email', 'role','created_at')
            ->latest('id')
            ->paginate(10);

        return view('Admin.dashboard-admin', compact('users'));
        // Ajusta el path de la vista si tu carpeta es "Admin" con mayúscula.
    }
}
