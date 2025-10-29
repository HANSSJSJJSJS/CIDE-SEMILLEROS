<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semillero extends Model
{
    protected $table = 'semilleros';
    protected $primaryKey = 'id_semillero';
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

    public function aprendices()
    {
        return $this->belongsToMany(Aprendiz::class, 'aprendiz_semillero', 'id_semillero', 'id_aprendiz', 'id_semillero', 'id_aprendiz');
    }
}

