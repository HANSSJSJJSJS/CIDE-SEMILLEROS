<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiderSemillero extends Model
{
    protected $table = 'lideres_semillero';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
    'id_lider_semi', 'nombres', 'apellido',
    'tipo_documento', 'documento', 'correo_institucional'
];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function semilleros(): HasMany
    {
        return $this->hasMany(Semillero::class, 'id_lider_usuario', 'id_usuario');
    }
}