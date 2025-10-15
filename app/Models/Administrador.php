<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administradores';
    protected $primaryKey = 'id_usuario'; // PK que también es FK a users.id
    public $incrementing = false;         // no autoincrementa
    public $timestamps   = false;         // pon true si tu tabla tiene created_at/updated_at

    protected $fillable = ['id_usuario', 'nombre'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
