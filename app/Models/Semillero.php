<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Proyecto;
use App\Models\Aprendiz;
use App\Models\LiderSemillero;

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

    // 1:N proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_semillero', 'id_semillero');
    }

    // N:M aprendices (tabla pivote aprendiz_semillero)
    public function aprendices()
    {
        return $this->belongsToMany(
            Aprendiz::class,
            'aprendiz_semillero', // tabla pivote
            'id_semillero',       // FK en pivote hacia semilleros
            'id_aprendiz',        // FK en pivote hacia aprendices
            'id_semillero',       // PK local en este modelo
            'id_aprendiz'         // PK en modelo Aprendiz
        );
        // ->withTimestamps(); // descomenta si tu pivote tiene timestamps
    }

    // Líder del semillero
    public function lider()
    {
        return $this->belongsTo(LiderSemillero::class, 'id_lider_semi', 'id_lider_semi');
    }
}
