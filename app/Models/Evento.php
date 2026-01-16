<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';
    protected $primaryKey = 'id_evento';
    public $incrementing = true;
    protected $keyType = 'int';

protected $fillable = [
    'titulo',
    'descripcion',
    'fecha_hora',
    'duracion',
    'ubicacion',
    'link_virtual',
    'id_proyecto',   // (semillero)
    'tipo',
    'recordatorio'
];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'duracion' => 'integer'
    ];

    // Relación con el líder (usuario)
    public function lider()
    {
        return $this->belongsTo(User::class, 'id_lider');
    }

    // Relación con el proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    // Relación muchos a muchos con aprendices (participantes)
    public function participantes()
    {
        // Pivot: evento_participantes.id_evento (FK a eventos.id_evento)
        //        evento_participantes.id_aprendiz (FK a aprendices.id_aprendiz)
        return $this->belongsToMany(
            Aprendiz::class,
            'evento_participantes',   // tabla pivote
            'id_evento',              // FK en pivote hacia eventos
            'id_aprendiz',            // FK en pivote hacia aprendices
            'id_evento',              // PK local en eventos
            'id_aprendiz'             // clave en aprendices
        )->withTimestamps();
    }
}
