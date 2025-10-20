<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';
    protected $primaryKey = 'id_proyecto';

    protected $fillable = [
        'id_semillero',
        'id_tipo_proyecto',
        'nombre_proyecto',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_fin'
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_fin',
        'creado_en',
        'actualizado_en'
    ];

    // Relación con aprendices (usuarios) si usas tabla pivote:
    public function aprendices()
    {
        return $this->belongsToMany(User::class, 'proyecto_user', 'id_proyecto', 'user_id');
    }

    public function semillero()
    {
        return $this->belongsTo(Semillero::class, 'id_semillero');
    }

    public function tipoProyecto()
    {
        return $this->belongsTo(TipoProyecto::class, 'id_tipo_proyecto');
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class, 'id_proyecto');
    }
}
