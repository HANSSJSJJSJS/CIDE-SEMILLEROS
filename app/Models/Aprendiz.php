class Aprendiz {
    protected $table = 'aprendices';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'rol',
        'estado',
    ];

    public function proyectos() {
        return $this->belongsToMany(Proyecto::class);
    }

    public function archivos() {
        return $this->hasMany(Archivo::class);
    }
}
