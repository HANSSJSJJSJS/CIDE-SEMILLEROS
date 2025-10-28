<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administradores';

    // PK es id_usuario, no autoincremental
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    protected $keyType = 'int';

    // columnas de timestamps personalizadas
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    public $timestamps = true;

    protected $fillable = [
        'id_usuario',
        'nombre',
        'apellidos',
    ];
}
