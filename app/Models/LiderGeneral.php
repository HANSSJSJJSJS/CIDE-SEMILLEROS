<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiderGeneral extends Model
{
    // Nombre EXACTO de la tabla
    protected $table = 'lider_general';

    // La PK de tu tabla (no es 'id')
    protected $primaryKey = 'id_usuario';
    public $incrementing = false; // si id_usuario no es AUTO_INCREMENT

    // Si quieres que Eloquent rellene creado_en/actualizado_en automáticamente:
    public $timestamps = true;
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    // MUY IMPORTANTE: columnas permitidas para asignación masiva
    protected $fillable = [
        'id_usuario',
        'nombre',
        'apellidos',
        'Correo_institucional', // usa el mismo casing que en la BD
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario');
    }
}
