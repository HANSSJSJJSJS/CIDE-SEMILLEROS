<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aprendiz extends Model
{
    protected $table = 'aprendices';

    // PK real de la tabla (AUTO_INCREMENT)
    protected $primaryKey = 'id_aprendiz';
    public $incrementing = true;
    protected $keyType = 'int';

    // Timestamps personalizados de tu BD
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    public $timestamps = true;

    // No incluimos id_aprendiz en fillable si es AUTO_INCREMENT
    protected $fillable = [
        'id_usuario',
        'nombres',
        'apellido',
        'ficha',
        'programa',
        'tipo_documento',
        'documento',
        'celular',
        'correo_institucional',
        'correo_personal',
        'contacto_nombre',
        'contacto_celular',
    ];

    /* Relaciones útiles (ajústalas si tus pivots usan otros nombres) */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    public function grupos()
    {
        // Si tu pivot es 'grupo_aprendices' y clave local es id_usuario
        return $this->belongsToMany(Grupo::class, 'grupo_aprendices', 'id_usuario', 'id_grupo');
    }

    public function semilleros()
    {
        // Si tu pivot es 'aprendiz_semillero' con claves como indicas en main
        return $this->belongsToMany(Semillero::class, 'aprendiz_semillero', 'id_aprendiz', 'id_semillero', 'id_aprendiz', 'id_semillero');
    }
}
