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
        'id_usuario',
        'nombres',
        'apellidos',
        'ficha',
        'programa',
        'id_tipo_documento',
        'tipo_documento',
        'documento',
        'celular',
        'correo_institucional',
        'correo_personal',
        'contacto_nombre',
        'contacto_celular',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_aprendices', 'id_usuario', 'id_grupo');
    }

    public function semilleros()
    {
        return $this->belongsToMany(Semillero::class, 'aprendiz_semillero', 'id_aprendiz', 'id_semillero', 'id_aprendiz', 'id_semillero');
    }

    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'aprendiz_proyecto', 'id_aprendiz', 'id_proyecto', 'id_aprendiz', 'id_proyecto');
    }

    // Exponer atributo virtual "nombre_completo" a partir de nombres y apellidos
    protected $appends = ['nombre_completo'];

    public function getNombreCompletoAttribute(): string
    {
        $n = trim((string)($this->attributes['nombres'] ?? ''));
        $a = trim((string)($this->attributes['apellidos'] ?? ''));
        return trim($n . ' ' . $a);
    }
}
