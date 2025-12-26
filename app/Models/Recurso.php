<?php

// app/Models/Recurso.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
{
    protected $table = 'recursos';
    protected $primaryKey = 'id_recurso';

    protected $fillable = [
        'nombre_archivo',
        'archivo',
        'archivo_respuesta',
        'categoria',
        'tipo_documento',
        'dirigido_a',
        'estado',
        'fecha_vencimiento',
        'descripcion',
        'respuesta',
        'enlace_respuesta',
        'respondido_en',
        'comentarios',
        'user_id',
        'semillero_id',
        'proyecto_id',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'respondido_en' => 'datetime',
    ];

    // Relación con semillero
    public function semillero()
    {
        return $this->belongsTo(Semillero::class, 'semillero_id', 'id_semillero');
    }

    // Scope para actividades dirigidas a líderes
    public function scopeParaLideres($query)
    {
        return $query->where('dirigido_a', 'lideres');
    }

    // Estado calculado (incluye "vencido")
    public function getEstadoCalculadoAttribute()
    {
        $estado = $this->estado;

        if (
            $estado === 'pendiente' &&
            $this->fecha_vencimiento &&
            now()->gt($this->fecha_vencimiento)
        ) {
            return 'vencido';
        }

        return $estado;
    }
}
