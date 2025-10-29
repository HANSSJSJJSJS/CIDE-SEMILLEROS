<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Proyecto;
use App\Models\Aprendiz;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';
    protected $primaryKey = 'id_documento';
    public $timestamps = false;

    protected $fillable = [
        'id_proyecto',
        'id_aprendiz',
        'documento',
        'ruta_archivo',
        'tipo_archivo',
        'tamanio',
        'fecha_subida',
        'estado'
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
        'tamanio' => 'integer'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id_proyecto');
    }

    public function aprendiz()
    {
        return $this->belongsTo(Aprendiz::class, 'id_aprendiz');
    }
}
