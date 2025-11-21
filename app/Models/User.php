<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\UserModulePermission; 
use App\Models\Proyecto;
use App\Models\Aprendiz;
use App\Models\Administrador;
use App\Models\Documento;
use App\Models\Evidencia;
use App\Models\LiderSemillero;
use App\Models\LiderInvestigacion;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','apellidos','email','password','role','telefono'];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================
    //   RELACIONES Y ROLES
    // =========================

    public function proyectos(): BelongsToMany
    {
        return $this->belongsToMany(Proyecto::class, 'proyecto_user', 'user_id', 'id_proyecto');
    }

    public function redirectPath()
    {
        return match ($this->role) {
            'ADMIN'               => '/admin/dashboard',
            'LIDER_SEMILLERO'     => '/lider_semi/dashboard',
            'APRENDIZ'            => '/aprendiz/dashboard',
            'LIDER GENERAL'       => '/lider/dashboard',
            'LIDER_INVESTIGACION' => '/lider_investigacion/dashboard',
            default               => '/',
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

    // ==============================
    //   PERMISOS POR MÓDULO
    // ==============================

    public function modulePermissions()
    {
        return $this->hasMany(UserModulePermission::class, 'user_id');
    }

    public function canManageModule(string $module, string $action): bool
    {
        // Admin siempre puede todo
        if ($this->role === 'ADMIN') {
            return true;
        }

        $perm = $this->modulePermissions()->where('module', $module)->first();

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
            default => false,
        };
    }
} 
