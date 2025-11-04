<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semillero extends Model
{
    protected $table = 'semilleros';
    protected $primaryKey = 'id_semillero';
<<<<<<< HEAD
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'progreso',
        'lider_id',
    ];

    // Relación con proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_semillero', 'id_semillero');
    }
=======

    protected $fillable = [
        'id_semillero', 'nombre', 'descripcion', 'estado', 'progreso', 'aprendices', 'lider_id',
    ];

    // Relación futura si aplica (ajustar claves cuando exista la tabla/proyectos)
    // public function proyectos()
    // {
    //     return $this->hasMany(\App\Models\Proyecto::class, 'semillero_id');
    // }
>>>>>>> Fusionmain

    public function aprendices()
    {
        return $this->belongsToMany(Aprendiz::class, 'aprendiz_semillero', 'id_semillero', 'id_aprendiz', 'id_semillero', 'id_aprendiz');
    }
}
<<<<<<< HEAD

=======
>>>>>>> Fusionmain
