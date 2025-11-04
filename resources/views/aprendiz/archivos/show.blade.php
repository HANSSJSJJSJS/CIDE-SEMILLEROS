<div class="file-content">
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/aprendiz/style.css') }}">
    <title>Detalles del Archivo</title>
</head>
<body>
    <div class="container">
        <h1>Detalles del Archivo</h1>

        <div class="file-details">
            <h2>{{ $archivo->nombre }}</h2>
            <p><strong>Descripción:</strong> {{ $archivo->descripcion }}</p>
            <p><strong>Subido por:</strong> {{ $archivo->subido_por }}</p>
            <p><strong>Fecha de subida:</strong> {{ $archivo->created_at->format('d/m/Y') }}</p>
        </div>

        <div class="file-actions">
            <a href="{{ route('archivos.descargar', $archivo->id) }}" class="btn btn-download">Descargar Archivo</a>
        </div>

        <div class="back-link">
            <a href="{{ route('archivos.index') }}">Volver a la lista de archivos</a>
        </div>
    </div>
</body>
</html>
</div>
