<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $table = 'evidencias';
    protected $primaryKey = 'id_evidencia';
    public $timestamps = true;

    protected $fillable = [
        'proyecto_id',
        'id_usuario',
        'nombre',
        'estado',
    ];

    // Relación inversa con Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id', 'id_proyecto');
    }

    // Autor (Aprendiz) que subió la evidencia
    public function autor()
    {
        return $this->belongsTo(Aprendiz::class, 'id_usuario', 'id_usuario');
    }
}
