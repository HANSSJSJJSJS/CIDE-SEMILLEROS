<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aprendiz extends Model
{
    protected $table = 'aprendices';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'id_usuario',
        'nombre_completo',
        'ficha',
        'programa',
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
}