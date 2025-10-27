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
        // Relación mediante proyecto_user: user_id (pivot) ↔ aprendices.id_usuario (PK)
        return $this->belongsToMany(
            Aprendiz::class,
            'proyecto_user', // tabla pivote
            'id_proyecto',   // fk en pivot hacia proyectos (según esquema)
            'user_id',       // fk en pivot hacia aprendices (coincide con id_usuario)
            'id_proyecto',   // clave local en proyectos
            'id_usuario'     // clave en aprendices (PK)
        )->distinct();
    }

    // Relación directa con usuarios a través de proyecto_user
    public function usuarios()
    {
        return $this->belongsToMany(
            User::class,
            'proyecto_user', // tabla pivote
            'id_proyecto',   // fk en pivot hacia proyectos (según esquema)
            'user_id',       // fk en pivot hacia users
            'id_proyecto',   // clave local en proyectos
            'id'             // clave en users
        )->distinct();
    }
    public function evidencias()
{
    return $this->hasMany(Evidencia::class, 'id_proyecto', 'id_proyecto');
}
};

