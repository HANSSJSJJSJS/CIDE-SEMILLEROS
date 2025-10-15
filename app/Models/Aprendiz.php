<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aprendiz extends Model
{
    protected $table = 'aprendices';
    public $timestamps = false; // pon true si tu tabla tiene timestamps

    protected $fillable = [
        'id_usuario',
        'nombres',
        'apellidos',
        'ficha',
        'programa',
        'tipo_documento',
        'documento',
        'celular',
        'correo_institucional',
        'contacto_nombre',
        'contacto_celular',
        'correo_personal',
    ];
}
