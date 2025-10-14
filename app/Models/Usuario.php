<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Importante para usar Auth
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    // Indica el nombre de tu tabla
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false; // Ya tienes timestamps personalizados

    protected $fillable = [
        'correo',
        'password_hash',
        'rol',
        'estado',
    ];

    // Laravel espera una columna 'password', así que le decimos que use 'password_hash'
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Opcional: si deseas usar los timestamps personalizados
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
}
