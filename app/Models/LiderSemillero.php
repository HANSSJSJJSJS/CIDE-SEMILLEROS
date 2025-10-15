<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiderSemillero extends Model
{
    protected $table = 'lideres_semillero';
    public $timestamps = false; // pon true si tu tabla tiene created_at/updated_at

    protected $fillable = [
        'id_usuario',
        'nombre_completo',
        'tipo_documento',       // VARCHAR(5): CC/CE
        'documento',            // cambia a 'documentos' si tu columna es plural
        'correo_institucional',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario');
    }
}
