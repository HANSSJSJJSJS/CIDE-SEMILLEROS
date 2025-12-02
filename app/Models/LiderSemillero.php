<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Semillero;

class LiderSemillero extends Model
{
    use HasFactory;

    protected $table = 'lideres_semillero';
    protected $primaryKey = 'id_lider_semi';

    public $incrementing = false;   // porque usamos el id de users
    protected $keyType   = 'int';

    public $timestamps = false;     // porque usamos creado_en / actualizado_en

    protected $fillable = [
        'id_lider_semi',
        'correo_institucional',
        'id_semillero',
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_lider_semi', 'id');
    }

    // Relación con Semillero
    public function semillero()
    {
        return $this->belongsTo(Semillero::class, 'id_semillero', 'id_semillero');
    }
}
