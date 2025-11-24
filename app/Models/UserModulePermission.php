<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModulePermission extends Model
{
    protected $table = 'user_module_permissions';

    protected $fillable = [
        'user_id',
        'module',
        'can_create',
        'can_update',
        'can_delete',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
