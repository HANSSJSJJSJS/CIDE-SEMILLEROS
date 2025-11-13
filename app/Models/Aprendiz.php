<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aprendiz extends Model
{
    protected $table = 'aprendices';
    protected $primaryKey = 'id_aprendiz';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id_aprendiz',
        'user_id',
        'nombres',
        'apellidos',
        'ficha',
        'programa',
        'tipo_documento',
        'documento',
        'celular',
        'correo_institucional',
        'correo_personal',
        'contacto_nombre',
        'contacto_celular',
        'semillero_id',      // FK al semillero (nuevo)
        'vinculado_sena',    // bool: 1=SENA, 0=otra institución
        'institucion',       // nullable cuando no está en SENA
    ];

    /** Relaciones */
    public function user()
    {


        return $this->belongsTo(User::class, 'user_id', 'id');

        // clave foránea real en la tabla aprendices: user_id -> users.id
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // (Conservamos la relación de main por compatibilidad, si no la usas, puedes quitarla)
    public function grupos()
    {
        // OJO: esta pivot referencia id_usuario; verifícala con tu esquema real
        return $this->belongsToMany(Grupo::class, 'grupo_aprendices', 'id_usuario', 'id_grupo');

    }

    public function semillero()
    {
        return $this->belongsTo(Semillero::class, 'semillero_id', 'id_semillero');
    }

    public function proyectos()
    {
        return $this->belongsToMany(
            Proyecto::class,
            'aprendiz_proyecto',
            'id_aprendiz',
            'id_proyecto'
        );
    }

    /** Atributos virtuales */
    protected $appends = ['nombre_completo', 'linea_investigacion'];

    public function getNombreCompletoAttribute(): string
    {
        $n = trim((string)($this->attributes['nombres'] ?? ''));
        $a = trim((string)($this->attributes['apellidos'] ?? ''));
        return trim($n . ' ' . $a);
    }

    // Se toma desde la relación con semillero; no se persiste en aprendices
    public function getLineaInvestigacionAttribute(): ?string
    {
        return optional($this->semillero)->linea_investigacion;
    }
}
