<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';
    protected $primaryKey = 'id_evento';
    
    protected $fillable = [
        'id_lider',
        'id_usuario',
        'id_proyecto',
        'titulo',
        'tipo',
        'linea_investigacion',
        'descripcion',
        'fecha_hora',
        'duracion',
        'ubicacion',
        'link_virtual',
        'codigo_reunion',
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
        return $this->belongsToMany(
            Aprendiz::class,
            'evento_participantes',
            'id_evento',
            'id_aprendiz'
        )->withTimestamps();
    }
}
