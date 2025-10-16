<?php

namespace App\Policies;

use App\Models\Aprendiz;
use App\Models\Archivo;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArchivoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the aprendiz can view the archivo.
     *
     * @param  \App\Models\Aprendiz  $aprendiz
     * @param  \App\Models\Archivo  $archivo
     * @return mixed
     */
    public function view(Aprendiz $aprendiz, Archivo $archivo)
    {
        // Permitir ver el archivo si el aprendiz está asignado al proyecto correspondiente
        return $aprendiz->proyectos()->where('id', $archivo->proyecto_id)->exists();
    }

    /**
     * Determine whether the aprendiz can download the archivo.
     *
     * @param  \App\Models\Aprendiz  $aprendiz
     * @param  \App\Models\Archivo  $archivo
     * @return mixed
     */
    public function download(Aprendiz $aprendiz, Archivo $archivo)
    {
        // Permitir descargar el archivo si el aprendiz está asignado al proyecto correspondiente
        return $this->view($aprendiz, $archivo);
    }

    /**
     * Determine whether the aprendiz can create archivos.
     *
     * @param  \App\Models\Aprendiz  $aprendiz
     * @return mixed
     */
    public function create(Aprendiz $aprendiz)
    {
        // Permitir crear archivos solo si el aprendiz tiene permisos
        return true; // Aquí puedes agregar lógica adicional si es necesario
    }

    /**
     * Determine whether the aprendiz can update the archivo.
     *
     * @param  \App\Models\Aprendiz  $aprendiz
     * @param  \App\Models\Archivo  $archivo
     * @return mixed
     */
    public function update(Aprendiz $aprendiz, Archivo $archivo)
    {
        // No permitir que el aprendiz actualice el archivo
        return false;
    }

    /**
     * Determine whether the aprendiz can delete the archivo.
     *
     * @param  \App\Models\Aprendiz  $aprendiz
     * @param  \App\Models\Archivo  $archivo
     * @return mixed
     */
    public function delete(Aprendiz $aprendiz, Archivo $archivo)
    {
        // No permitir que el aprendiz elimine el archivo
        return false;
    }
}
