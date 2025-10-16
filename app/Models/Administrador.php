<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administradores';   // nombre de tu tabla
    protected $primaryKey = 'id_usuario';   // PK es id_usuario
    public $incrementing = false;           // si no es AUTO_INCREMENT
    public $timestamps = true;              // mapear tus columnas de fecha
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'id_usuario',
        'nombre',
        'apellidos',
        'correo',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario');
    }
}
