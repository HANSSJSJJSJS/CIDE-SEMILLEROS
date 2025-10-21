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
        'nombre_completo',
        'ficha',
        'programa',
        'id_tipo_documento', // nullable en tu BD
        'tipo_documento',
        'documento',
        'celular',
        'correo_institucional',
        'correo_personal',
        'contacto_nombre',     // 👈 como pediste
        'contacto_celular',    // 👈 como pediste
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
}
