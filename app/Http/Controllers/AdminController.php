<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){ return view('usuarios.index'); }
    public function create(){ return view('usuarios.create'); }
    public function store(Request $r){ /* ... */ }
    public function show($id){ /* ... */ }
    public function edit($id){ /* ... */ }
    public function update(Request $r,$id){ /* ... */ }
    public function destroy($id){ /* ... */ }
}
