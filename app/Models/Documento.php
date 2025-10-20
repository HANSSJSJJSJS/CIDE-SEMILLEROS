<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Proyecto;

class Documento extends Model
{
    protected $table = 'documentos'; // ajusta si tu tabla tiene otro nombre

    // Si usas asignación masiva, define $fillable
    // protected $fillable = ['id_proyecto', 'titulo', 'estado', ...];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }
}
