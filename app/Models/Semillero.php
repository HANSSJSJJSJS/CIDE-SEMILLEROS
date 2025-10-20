<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semillero extends Model
{
    protected $table = 'semilleros';

    protected $fillable = [
        'nombre', 'descripcion', 'estado', 'progreso', 'aprendices', 'lider_id',
    ];

    // Relación futura si aplica (ajustar claves cuando exista la tabla/proyectos)
    // public function proyectos()
    // {
    //     return $this->hasMany(\App\Models\Proyecto::class, 'semillero_id');
    // }
}