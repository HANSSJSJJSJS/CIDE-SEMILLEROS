<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Notifications\ResetPasswordNotification;
use App\Models\UserModulePermission;
use App\Models\Proyecto;
use App\Models\Aprendiz;
use App\Models\Administrador;
use App\Models\Documento;
use App\Models\Evidencia;
use App\Models\LiderSemillero;
use App\Models\LiderInvestigacion;
use Illuminate\Support\Facades\Mail;
use App\Mail\UsuarioCreadoMail;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Campos que se pueden llenar por mass assignment
    protected $fillable = [
            'nombre',
            'apellidos',
            'email',
            'password',
            'role',
            'tipo_documento',
            'documento',
            'celular',
            'genero',
            'tipo_rh',
            'must_change_password',
            ];


    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ============================================================
    // RELACIONES
    // ============================================================

    public function proyectos(): BelongsToMany
    {
        return $this->belongsToMany(
            Proyecto::class,
            'proyecto_user',
            'user_id',
            'id_proyecto'
        );
    }

    public function liderSemillero()
    {
        // Tabla lideres_semillero: id_usuario -> users.id
        return $this->hasOne(LiderSemillero::class, 'id_usuario', 'id');
    }

    public function aprendiz()
    {
        // En la BD la FK es user_id → users.id
        return $this->hasOne(Aprendiz::class, 'user_id', 'id');
    }

    public function administrador()
    {
        // Tabla administradores: id_usuario → users.id
        return $this->hasOne(Administrador::class, 'id_usuario', 'id');
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class, 'id_usuario', 'id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'id_usuario', 'id');
    }

    public function liderInvestigacion()
    {
        return $this->hasOne(LiderInvestigacion::class, 'user_id', 'id');
    }

    public function isLiderInvestigacion(): bool
    {
        return $this->role === 'LIDER_INVESTIGACION';
    }

    // ============================================================
    // PERMISOS POR MÓDULO
    // ============================================================

    public function modulePermissions()
    {
        return $this->hasMany(UserModulePermission::class, 'user_id');
    }

    public function canManageModule(string $module, string $action): bool
    {
        // 1) ADMIN siempre puede todo
        if ($this->role === 'ADMIN') {
            return true;
        }

        // 2) LÍDER DE INVESTIGACIÓN → depende de tiene_permisos
        if ($this->role === 'LIDER_INVESTIGACION') {
            return (bool) $this->li_tiene_permisos;
        }

        // 3) Otros roles → módulos personalizados
        $perm = $this->modulePermissions()
            ->where('module', $module)
            ->first();

        if (! $perm) {
            return false;
        }

        if ($action === 'view') {
            return (bool) ($perm->can_create || $perm->can_update || $perm->can_delete);
        }

        return match ($action) {
            'create' => (bool) $perm->can_create,
            'update' => (bool) $perm->can_update,
            'delete' => (bool) $perm->can_delete,
            default  => false,
        };
    }

    // ============================================================
    // ACCESSOR DEFINITIVO
    // ============================================================

    public function getLiTienePermisosAttribute()
    {
        if ($this->role !== 'LIDER_INVESTIGACION') {
            return false;
        }

        // Si viene desde el SELECT del index
        if (array_key_exists('li_tiene_permisos', $this->attributes)) {
            return (bool) $this->attributes['li_tiene_permisos'];
        }

        // Si es el usuario logueado
        return $this->liderInvestigacion
            ? (bool) $this->liderInvestigacion->tiene_permisos
            : false;
    }
    public function sendPasswordResetNotification($token)
{
    $this->notify(new ResetPasswordNotification($token));
}
}
