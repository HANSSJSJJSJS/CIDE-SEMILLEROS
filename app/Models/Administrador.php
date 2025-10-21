<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administradores';

    // Si tu PK no es "id", ajusta aquí; si es "id", puedes omitirlo:
    // protected $primaryKey = 'id_administrador';

    public $timestamps = true;
    // Tus columnas de timestamp SON en español (lo vemos en el error):
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    // ✅ MUY IMPORTANTE: permitir asignar id_usuario
    protected $fillable = ['id_usuario','nombre'];

    // Alternativa permisiva (para descartar problemas de mass assignment):
    // protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
