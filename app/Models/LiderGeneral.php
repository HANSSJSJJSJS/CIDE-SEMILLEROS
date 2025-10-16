<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiderGeneral extends Model
{
    protected $table = 'lider_general';   // o 'lideres_generales'
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    public $timestamps   = true;
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [               // 👈 incluye ambos
        'id_usuario',
        'nombres',
        'apellidos',
        'Correo_institucional',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
