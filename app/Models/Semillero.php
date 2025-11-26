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

    /** 1:N con Proyectos (semilleros.id_semillero -> proyectos.id_semillero) */
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_semillero', 'id_semillero');
    }

    /** 1:N con Aprendices (semilleros.id_semillero -> aprendices.semillero_id) */
    public function aprendices()
{
    return $this->hasMany(\App\Models\Aprendiz::class, 'semillero_id', 'id_semillero');
    }

    /** Líder del semillero */
    public function lider()
    {
        return $this->belongsTo(LiderSemillero::class, 'id_lider_semi', 'id_lider_semi');
    }
}
