<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Proyecto;

class Archivo extends Model
{
    use HasFactory;

    protected $table = 'archivos';

    protected $fillable = [
        'nombre_original',
        'nombre_almacenado',
        'ruta',
        'proyecto_id',
        'user_id',
        'estado',
        'mime_type',
        'subido_en',
    ];

    // 🔹 Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔹 Scopes útiles para estadísticas
    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDelProyecto($query, $proyectoId)
    {
        return $query->where('proyecto_id', $proyectoId);
    }

    public function scopeCompletados($query)
    {
        // Estado aprobado por el líder
        return $query->where('estado', 'aprobado');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
}
