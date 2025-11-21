<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiderInvestigacion extends Model
{
    protected $table = 'lideres_investigacion';

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
