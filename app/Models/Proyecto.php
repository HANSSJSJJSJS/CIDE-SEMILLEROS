class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    public function aprendices()
    {
        return $this->belongsToMany(Aprendiz::class);
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }
}
