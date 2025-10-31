<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LiderSemillero;

class ReunionesLideresController extends Controller
{
    public function index()
    {
        $lideres = LiderSemillero::all();
        return view('Admin.Reuniones.calendario_scml', compact('lideres'));
    }
}
