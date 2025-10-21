<div class="file-content">
    <h1>Detalles del Proyecto</h1>

    <h2>{{ $proyecto->nombre }}</h2>
    <p><strong>Descripción:</strong> {{ $proyecto->descripcion }}</p>
    <p><strong>Fecha de Inicio:</strong> {{ $proyecto->fecha_inicio }}</p>
    <p><strong>Fecha de Fin:</strong> {{ $proyecto->fecha_fin }}</p>

    <h3>Archivos Asociados</h3>
    <ul>
        @foreach($proyecto->archivos as $archivo)
            <li>
                <a href="{{ route('archivos.show', $archivo->id) }}">{{ $archivo->nombre }}</a>
                <span> - <a href="{{ route('archivos.download', $archivo->id) }}">Descargar</a></span>
            </li>
        @endforeach
    </ul>

    <a href="{{ route('proyectos.index') }}">Volver a la lista de proyectos</a>
</div>
