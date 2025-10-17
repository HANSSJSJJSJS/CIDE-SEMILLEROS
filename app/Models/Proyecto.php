<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    // Si tu clave primaria no es 'id' (es 'id_proyecto'), defínelo:
    protected $primaryKey = 'id_proyecto';

    // Si la clave primaria no es autoincrement o no es un entero, especifica también:
    // public $incrementing = false; // si no es autoincrement
    // protected $keyType = 'string'; // si es string

    // Si tu tabla no usa las columnas por defecto 'created_at' y 'updated_at'
    // define los nombres personalizados:
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    // Nombre de la tabla si no es 'proyectos'
    protected $table = 'proyectos';

    protected $fillable = [
        'id_semillero',
        'id_tipo_proyecto',
        'nombre_proyecto',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_fin',
    ];

    // Relación con aprendices (usuarios) si usas tabla pivote:
    public function aprendices()
    {
        return $this->belongsToMany(User::class, 'proyecto_user', 'id_proyecto', 'user_id');
    }
}
