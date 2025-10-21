<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProyecto extends Model
{
    protected $table = 'tipos_proyecto';
    protected $primaryKey = 'id_tipo_proyecto';

    protected $fillable = [
        'nombre_tipo',
        'descripcion'
    ];

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_tipo_proyecto');
    }
}
