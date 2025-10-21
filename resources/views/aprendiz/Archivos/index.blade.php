<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/aprendiz/style.css') }}">
    <title>Archivos del Aprendiz</title>
</head>
<body>
    <div class="container">
        <h1>Archivos Disponibles</h1>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Archivo</th>
                    <th>Fecha de Subida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($archivos as $archivo)
                    <tr>
                        <td>{{ $archivo->nombre }}</td>
                        <td>{{ $archivo->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('archivos.show', $archivo->id) }}" class="btn btn-view">Ver</a>
                            <a href="{{ asset('storage/' . $archivo->ruta) }}" class="btn btn-download" download>Descargar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
