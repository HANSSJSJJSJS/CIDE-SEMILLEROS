<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Evidencia;
use App\Models\Documento;
use App\Models\User;
use App\Models\Aprendiz;

class Proyecto extends Model
{
    protected $table = 'proyectos';
    protected $primaryKey = 'id_proyecto';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // usas creado_en / actualizado_en

    protected $fillable = [
        'id_semillero',
        'nombre_proyecto',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_fin',
    ];

    // Casts para que las fechas se traten como instancias de fecha/datetime
    protected $casts = [
        'fecha_inicio'   => 'date',
        'fecha_fin'      => 'date',
        'creado_en'      => 'datetime',
        'actualizado_en' => 'datetime',
    ];

    /** ---------------------------------------------------------
     *  RELACIONES
     *  --------------------------------------------------------- */

    // 🔹 Un proyecto pertenece a un semillero
    public function semillero()
    {
        return $this->belongsTo(Semillero::class, 'id_semillero', 'id_semillero');
    }

    // 🔹 Un proyecto tiene muchos documentos
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'id_proyecto', 'id_proyecto');
    }

    // 🔹 Un proyecto tiene muchos aprendices (N-N) vía pivote aprendiz_proyecto
    public function aprendices()
    {
        return $this->belongsToMany(
            Aprendiz::class,      // modelo relacionado
            'aprendiz_proyecto',  // tabla pivote
            'id_proyecto',        // FK del proyecto en el pivote
            'id_aprendiz'         // FK del aprendiz en el pivote
        );
    }

    // 🔹 Un proyecto tiene muchas evidencias
    public function evidencias()
    {
        return $this->hasMany(Evidencia::class, 'proyecto_id', 'id_proyecto');
    }

    // 🔹 (OPCIONAL) método para agregar una observación al historial en el mismo campo
    public function agregarObservacionTexto(string $texto, ?User $usuario = null): void
    {
        $usuario = $usuario ?: auth()->user();

        $linea = now()->format('Y-m-d H:i')
               . ' - ' . ($usuario?->nombre ?? 'Sistema')
               . ': ' . $texto;

        $actual = $this->observaciones ? rtrim($this->observaciones) . "\n" : '';

        $this->observaciones = $actual . $linea;
        $this->save();
    }
}
