<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TablaGestionUsuario extends Model
{
    protected $table = 'users'; // <-- TU TABLA
    public $timestamps = false;         // o true si tienes updated_at

    protected $fillable = ['type','title','description','user_id','created_at'];

    protected $casts = [
        'created_at' => 'datetime',     // para usar diffForHumans()
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
