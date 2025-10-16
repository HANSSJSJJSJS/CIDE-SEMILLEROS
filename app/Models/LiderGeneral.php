<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiderGeneral extends Model
{
    protected $table = 'lideres_generales';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'nombre',
        'Correo_institucional',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario');
    }
}
