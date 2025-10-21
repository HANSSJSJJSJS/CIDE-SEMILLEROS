<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiderGeneral extends Model
{
    protected $table = 'lider_general'; // tu tabla es singular

    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    protected $keyType = 'int';

    // en esta tabla "creado_en" puede ser NULL según tu dump
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    public $timestamps = true;

   protected $fillable = ['id_lidergen','nombres','apellido','Correo_institucional'];

}
