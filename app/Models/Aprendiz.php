<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Semillero;
use App\Models\Proyecto;
use App\Models\Documento;
// use App\Models\EventoParticipante; // si tienes este modelo

class Aprendiz extends Model
{
    use HasFactory;

    protected $table = 'aprendices';
    protected $primaryKey = 'id_aprendiz';
    public $timestamps = false; // porque usas creado_en / actualizado_en

    protected $fillable = [
        'user_id',
        'nombres',
        'apellidos',
        'ficha',
        'programa',
        'nivel_educativo',
        'vinculado_sena',
        'institucion',
        'correo_institucional',
        'correo_personal',
        'contacto_nombre',
        'contacto_celular',
        'semillero_id',
        'estado',
    ];

    protected $casts = [
        'vinculado_sena' => 'boolean',
    ];

    // ============================================================
    // RELACIONES
    // ============================================================

    /**
     * Usuario base (tabla users)
     */
    public function user()
    {
        // FK en aprendices = user_id → users.id
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Semillero al que pertenece
     */
    public function semillero()
    {
        return $this->belongsTo(Semillero::class, 'semillero_id', 'id_semillero');
    }

    /**
     * Proyectos en los que participa el aprendiz
     * (tabla pivote: aprendiz_proyecto)
     */
    public function proyectos()
    {
        return $this->belongsToMany(
            Proyecto::class,
            'aprendiz_proyecto',
            'id_aprendiz',   // FK a aprendices
            'id_proyecto'    // FK a proyectos
        );
    }

    /**
     * Documentos (evidencias/entregas) asociados al aprendiz
     */
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'id_aprendiz', 'id_aprendiz');
    }

    /*
    // Si tienes el modelo EventoParticipante:
    public function eventoParticipantes()
    {
        return $this->hasMany(EventoParticipante::class, 'id_aprendiz', 'id_aprendiz');
    }
    */
}
