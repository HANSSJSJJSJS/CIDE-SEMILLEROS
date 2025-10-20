
<form action="{{ route('aprendiz.archivos.upload.post') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-4">
        <label class="form-label">Selecciona un Proyecto</label>
        <select class="form-select" name="proyecto_id" required>
            <option value="">Selecciona un Proyecto</option>
            <option value="1">Proyecto de Seguridad Informática</option>
            <option value="2">Proyecto de Desarrollo Web</option>
            <option value="3">Proyecto de Análisis de Datos</option>
            <option value="4">Proyecto de Redes y Telecomunicaciones</option>
            <option value="5">Proyecto de Inteligencia Artificial</option>
        </select>
    </div>
    ...
</form>
