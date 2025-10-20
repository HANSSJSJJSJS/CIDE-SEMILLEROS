<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semillero extends Model
{
    protected $table = 'semilleros';
    protected $primaryKey = 'id_semillero';

    protected $fillable = [
        'nombre_semillero',
        'descripcion',
        'fecha_creacion',
        'estado'
    ];

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_semillero');
    }
}
