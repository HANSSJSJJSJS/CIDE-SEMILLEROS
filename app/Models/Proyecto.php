<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyectos';
    protected $primaryKey = 'id_proyecto'; // ajusta si es distinto
    public $incrementing = true;
    protected $keyType = 'int';

    public function semillero()
    {
        return $this->belongsTo(\App\Models\Semillero::class, 'id_semillero', 'id_semillero');
    }

    public function documentos()
    {
        return $this->hasMany(\App\Models\Documento::class, 'id_proyecto', 'id_proyecto');
    }
}
