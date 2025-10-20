<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Proyecto;

class Archivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_archivo',
        'ruta',
        'proyecto_id',
        'user_id',
        'estado'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
