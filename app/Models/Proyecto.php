<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Evidencia;


class Proyecto extends Model
{
    protected $table = 'proyectos';
    protected $primaryKey = 'id_proyecto';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'id_semillero',
        'id_tipo_proyecto',
        'nombre_proyecto',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_fin',
        'creado_en',
        'actualizado_en'
    ];

    // Relación con semillero
    public function semillero()
    {
        return $this->belongsTo(Semillero::class, 'id_semillero', 'id_semillero');
    }

    // Relación con documentos
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'id_proyecto', 'id_proyecto');
    }

    public function aprendices()
    {
        // La relación se hace a través de la tabla documentos
        return $this->belongsToMany(
            Aprendiz::class,
            'documentos',
            'id_proyecto',
            'id_aprendiz'
        )->distinct();
    }
    public function evidencias()
{
    return $this->hasMany(Evidencia::class, 'id_proyecto', 'id_proyecto');
}
};
