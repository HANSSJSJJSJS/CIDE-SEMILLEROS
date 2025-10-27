<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Renderiza una vista que EXTIENDE el layout nuevo
        return view('Admin.dashboard');
    }
}
