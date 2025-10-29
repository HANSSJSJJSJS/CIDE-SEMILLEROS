<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Evidencia;
use App\Models\Documento;
use App\Models\User;


class Proyecto extends Model
{
    protected $table = 'proyectos';
    protected $primaryKey = 'id_proyecto';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // columnas personalizadas en migración

    protected $fillable = [
        'id_semillero',
        'id_tipo_proyecto',
        'nombre_proyecto',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_fin'
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

    // Relación con usuarios (aprendices)
    public function aprendices()
    {
        return $this->belongsToMany(User::class, 'proyecto_user', 'id_proyecto', 'user_id');
    }

    // Relación con documentos
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'id_proyecto', 'id_proyecto');
    }
    public function evidencias()
{
    return $this->hasMany(Evidencia::class, 'id_proyecto', 'id_proyecto');
}
}

