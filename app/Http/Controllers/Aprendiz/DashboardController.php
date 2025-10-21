<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Aquí puedes preparar datos para la vista del dashboard
        return view('aprendiz.dashboard-aprendiz', compact('user'));
    }
}
