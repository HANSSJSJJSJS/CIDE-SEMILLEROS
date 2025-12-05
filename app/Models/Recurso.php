<?php

// app/Models/Recurso.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
{
    protected $table = 'recursos';

    protected $fillable = [
        'nombre_archivo',
        'archivo',
        'categoria',
        'dirigido_a',
        'estado',
        'fecha_vencimiento',
        'descripcion',
        'comentarios',
        'user_id',
        'semillero_id',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
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
