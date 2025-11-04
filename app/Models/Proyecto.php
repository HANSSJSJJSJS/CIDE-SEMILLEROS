<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Evidencia;
use App\Models\Documento;
use App\Models\User;
use App\Models\Aprendiz;


class Proyecto extends Model
{
    protected $table = 'proyectos';
    protected $primaryKey = 'id_proyecto';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

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

    // Relación con aprendices (por id_usuario en pivot)
    public function aprendices()
    {
        return $this->belongsToMany(
            Aprendiz::class,
            'proyecto_user',
            'id_proyecto',
            'user_id',
            'id_proyecto',
            'id_usuario'
        )->distinct();
    }

    // Relación directa con usuarios a través de la misma pivote
    public function usuarios()
    {
        return $this->belongsToMany(
            User::class,
            'proyecto_user',
            'id_proyecto',
            'user_id',
            'id_proyecto',
            'id'
        )->distinct();
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class, 'id_proyecto', 'id_proyecto');
    }
}
