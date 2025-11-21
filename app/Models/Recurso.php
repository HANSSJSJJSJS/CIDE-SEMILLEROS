<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Recurso extends Model
{
    use HasFactory;

    protected $table = 'recursos';

    protected $fillable = [
        'nombre_archivo',
        'archivo',
        'descripcion',
        'categoria',
        'user_id',
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * Devuelve la URL pública al archivo.
     */
    public function getUrlAttribute(): ?string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if ($this->archivo && $disk->exists($this->archivo)) {
            return $disk->url($this->archivo);
        }
        return null;
    }

    /**
     * Devuelve el tamaño en bytes del archivo.
     */
    public function getSizeAttribute(): ?int
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if ($this->archivo && $disk->exists($this->archivo)) {
            return $disk->size($this->archivo);
        }
        return null;
    }

    /**
     * Devuelve el tipo MIME del archivo.
     */
    public function getMimeAttribute(): ?string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if ($this->archivo && $disk->exists($this->archivo)) {
            return $disk->mimeType($this->archivo);
        }
        return null;
    }

    /**
     * Relación: cada recurso pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
