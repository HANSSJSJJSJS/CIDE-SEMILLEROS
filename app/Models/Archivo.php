<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;

    protected $table = 'archivos';

    protected $fillable = [
        'nombre',
        'ruta',
        'descripcion',
        'usuario_id',
        'proyecto_id',
    ];

    public function usuario()
    {
        return $this->belongsTo('App\Models\Aprendiz', 'usuario_id');
    }

    public function proyecto()
    {
        return $this->belongsTo('App\Models\Proyecto', 'proyecto_id');
    }
}
