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
        'codigo_reunion',
        'tipo',
        'linea_investigacion',
        'recordatorio',
        'estado',
        'id_proyecto',
        'id_lider',
        'id_lider_semi',
        'id_admin',
        'id_lider_usuario',
        'id_usuario',
        'creado_por'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'duracion' => 'integer'
    ];

    public function lider()
    {
        return $this->belongsTo(User::class, 'id_lider');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function participantes()
    {
        return $this->belongsToMany(
            Aprendiz::class,
            'evento_participantes',
            'id_evento',
            'id_aprendiz',
            'id_evento',
            'id_aprendiz'
        )->withTimestamps();
    }
}

