<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semillero extends Model
{
    protected $table = 'semilleros';
    protected $primaryKey = 'id_semillero'; // Clave primaria correcta según la BD
    public $timestamps = false; // La tabla no usa timestamps estándar de Laravel

    protected $fillable = [
        'nombre_semillero',
        'línea_investigación',
        'id_lider_usuario',
        'estado'
    ];

    protected $dates = [
        'creado_en'
    ];

    // Relación con proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_semillero');
    }

    // Relación con el líder
    public function lider()
    {
        return $this->belongsTo(User::class, 'id_lider_usuario');
    }
}
