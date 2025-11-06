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
        'linea_investigacion',
        'descripcion',
        'estado',
        'progreso',
        'id_lider_semi',
    ];

    // Relación con proyectos (ajusta el modelo/llaves si tus nombres difieren)
    public function proyectos()
    {
        return $this->hasMany(\App\Models\Proyecto::class, 'id_semillero', 'id_semillero');
    }

    // Relación con aprendices vía tabla pivote
    public function aprendices()
    {
        return $this->belongsToMany(
            \App\Models\Aprendiz::class,
            'aprendiz_semillero',
            'id_semillero',
            'id_aprendiz'
        );
    }

    // Relación con líder (si tienes el modelo LiderSemillero)
    public function lider()
    {
        return $this->belongsTo(\App\Models\LiderSemillero::class, 'id_lider_semi', 'id_lider_semi');
    }
}
