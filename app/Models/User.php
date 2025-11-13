<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Importar BelongsToMany
use App\Models\Proyecto;
use App\Models\Aprendiz;
use App\Models\Administrador;
use App\Models\Documento;
use App\Models\Evidencia;
use App\Models\LiderSemillero;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name','apellidos','email','password','role','telefono'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- AQUÍ AÑADIMOS LA RELACIÓN DE MUCHOS A MUCHOS ---

    /**
     * El usuario pertenece a muchos proyectos.
     */
    public function proyectos(): BelongsToMany
    {
        // Esto define la relación:
        // 1. Con el modelo Proyecto.
        // 2. Usando la tabla pivote 'proyecto_user'.
        // 3. Asume que las claves son 'user_id' y 'proyecto_id' o 'id_proyecto'.
        // Si tu clave foránea en la tabla pivote es 'id_proyecto' para la columna del proyecto,
        // ajusta la definición de la relación aquí:
        return $this->belongsToMany(Proyecto::class, 'proyecto_user', 'user_id', 'id_proyecto');
    }

    // ----------------------------------------------------

    public function redirectPath()
{
    return match ($this->role) {
        'ADMIN' => '/admin/dashboard',
        'LIDER_SEMILLERO' => '/lider_semi/dashboard',
        'APRENDIZ' => '/aprendiz/dashboard',
        'LIDER GENERAL' => '/lider/dashboard',
        default => '/',
    };
}

public function liderSemillero()
{
    return $this->hasOne(LiderSemillero::class, 'id_lider_semi', 'id');
}

public function aprendiz()
{
    return $this->hasOne(Aprendiz::class, 'id_usuario');
}

public function administrador()
{
    return $this->hasOne(Administrador::class, 'id_usuario');
}

public function evidencias()
{
    // Evidencias subidas por este usuario (FK en evidencias: id_usuario -> users.id)
    return $this->hasMany(Evidencia::class, 'id_usuario', 'id');
}
 public function documentos()
{
    return $this->hasMany(Documento::class, 'id_usuario', 'id');
}

}
