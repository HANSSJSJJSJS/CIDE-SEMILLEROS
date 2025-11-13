@php
    $activos = $activos ?? collect();
    $completados = $completados ?? collect();
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos</title>
    <style>
        body{font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; margin: 0; background:#f7f7f7;}
        .container{max-width:1100px;margin:24px auto;padding:0 16px}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
        h1{font-size:22px;margin:0}
        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px}
        .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px}
        .title{font-weight:600;font-size:16px;margin:0 0 4px}
        .desc{color:#6b7280;font-size:13px;min-height:34px}
        .meta{display:flex;gap:8px;margin-top:10px;font-size:12px;color:#374151}
        .badge{display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px}
        .badge.activo{background:#e0f2fe;color:#0369a1}
        .badge.completado{background:#ecfccb;color:#3f6212}
        .section-title{margin:20px 0 10px;font-size:14px;color:#374151;text-transform:uppercase;letter-spacing:.04em}
        .empty{color:#6b7280;font-size:14px;border:1px dashed #d1d5db;border-radius:12px;padding:18px;background:#fff}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Recursos de Proyectos</h1>
    </div>

    <div>
        <div class="section-title">Proyectos activos</div>
        @if($activos->isEmpty())
            <div class="empty">No hay proyectos activos.</div>
        @else
            <div class="grid">
                @foreach($activos as $p)
                    <div class="card">
                        <div class="badge activo">Activo</div>
                        <h2 class="title">{{ $p['nombre'] ?? 'Proyecto' }} (ID {{ $p['id'] }})</h2>
                        <div class="desc">{{ $p['descripcion'] ?? 'Sin descripción' }}</div>
                        <div class="meta">
                            <div>Entregas: <strong>{{ $p['entregas'] }}</strong></div>
                            <div>Pendientes: <strong>{{ $p['pendientes'] }}</strong></div>
                            <div>Aprobadas: <strong>{{ $p['aprobadas'] }}</strong></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div>
        <div class="section-title">Proyectos completados</div>
        @if($completados->isEmpty())
            <div class="empty">No hay proyectos completados.</div>
        @else
            <div class="grid">
                @foreach($completados as $p)
                    <div class="card">
                        <div class="badge completado">Completado</div>
                        <h2 class="title">{{ $p['nombre'] ?? 'Proyecto' }} (ID {{ $p['id'] }})</h2>
                        <div class="desc">{{ $p['descripcion'] ?? 'Sin descripción' }}</div>
                        <div class="meta">
                            <div>Entregas: <strong>{{ $p['entregas'] }}</strong></div>
                            <div>Pendientes: <strong>{{ $p['pendientes'] }}</strong></div>
                            <div>Aprobadas: <strong>{{ $p['aprobadas'] }}</strong></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
</body>
</html>
