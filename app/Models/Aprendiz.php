<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aprendiz extends Model
{
    protected $table = 'aprendices';
    protected $primaryKey = 'id_aprendiz';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [

        'id_aprendiz',
        'user_id',
        'nombres',
        'apellidos',
        'ficha',
        'programa',
        'tipo_documento',
        'documento',
        'celular',
        'correo_institucional',
        'correo_personal',
        'contacto_nombre',
        'contacto_celular',
        'semillero_id', // <<--- NUEVO: FK al semillero
    ];

    // Relaciones
    public function user()
    {

        return $this->belongsTo(User::class, 'user_id', 'id');

    }

    public function semillero()
    {
        return $this->belongsTo(Semillero::class, 'semillero_id', 'id_semillero');
    }

    public function proyectos()
    {
        return $this->belongsToMany(
            Proyecto::class,
            'aprendiz_proyecto',
            'id_aprendiz',
            'id_proyecto'
        );
    }

    // Atributos virtuales
    protected $appends = ['nombre_completo', 'linea_investigacion'];

    public function getNombreCompletoAttribute(): string
    {
        $n = trim((string)($this->attributes['nombres'] ?? ''));
        $a = trim((string)($this->attributes['apellidos'] ?? ''));
        return trim($n . ' ' . $a);
    }

    // NO se persiste en la tabla aprendices: se lee desde el semillero
    public function getLineaInvestigacionAttribute(): ?string
    {
        return optional($this->semillero)->linea_investigacion;
    }
}
