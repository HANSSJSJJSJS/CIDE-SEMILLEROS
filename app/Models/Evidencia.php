<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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
        // La FK local en evidencias es 'id_usuario'. El owner key en aprendices puede ser 'id_usuario' o 'user_id'.
        $ownerKey = 'id_usuario';
        try {
            if (Schema::hasTable('aprendices')) {
                $ownerKey = Schema::hasColumn('aprendices','id_usuario')
                    ? 'id_usuario'
                    : (Schema::hasColumn('aprendices','user_id') ? 'user_id' : 'id_usuario');
            }
        } catch (\Throwable $e) {}
        return $this->belongsTo(Aprendiz::class, 'id_usuario', $ownerKey);
    }
}
