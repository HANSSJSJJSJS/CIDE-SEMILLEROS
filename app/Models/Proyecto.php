<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function semillero()
    {
        return $this->belongsTo(Semillero::class, 'id_semillero', 'id_semillero');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'id_proyecto', 'id_proyecto');
    }

    public function aprendices()
    {
        return $this->belongsToMany(Aprendiz::class, 'aprendiz_proyecto', 'id_proyecto', 'id_aprendiz', 'id_proyecto', 'id_aprendiz');
    }
}
