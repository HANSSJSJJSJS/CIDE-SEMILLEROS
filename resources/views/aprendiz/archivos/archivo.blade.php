@extends('layouts.aprendiz')

@section('title','Mis Documentos')
@section('module-title','Mis Documentos')
@section('module-subtitle','Consulta y filtra tus archivos subidos')

@section('content')
<div class="container-fluid py-3">
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-6 col-xl-4">
          <label class="form-label mb-1">Proyecto</label>
          <select name="proyecto" class="form-select">
            <option value="">Todos</option>
            @foreach(($proyectos ?? collect()) as $p)
              <option value="{{ $p->id_proyecto }}" {{ (string)$p->id_proyecto === (string)($proyecto ?? '') ? 'selected' : '' }}>
                {{ $p->nombre_proyecto ?? ('Proyecto #'.$p->id_proyecto) }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
          <label class="form-label mb-1">Fecha</label>
          <input type="date" name="fecha" value="{{ $fecha }}" class="form-control">
        </div>
        <div class="col-6 col-md-3 col-xl-2">
          <button class="btn btn-success w-100">Filtrar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="m-0">Resultados</h5>
      <span class="badge bg-light text-primary">{{ method_exists($archivos,'total') ? $archivos->total() : ($archivos->count() ?? 0) }} registro(s)</span>
    </div>
    <div class="card-body">
      @if(method_exists($archivos,'count') ? $archivos->count() === 0 : ($archivos->isEmpty() ?? true))
        <div class="text-center text-muted py-5">No se encontraron documentos</div>
      @else
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Proyecto</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @foreach($archivos as $a)
                <tr>
                  <td>{{ optional($a->proyecto)->nombre_proyecto ?? '—' }}</td>
                  <td>{{ $a->nombre_original ?? '—' }}</td>
                  <td><span class="badge text-bg-secondary">{{ $a->mime_type ?? '—' }}</span></td>
                  <td>
                    <span class="badge {{ ($a->estado ?? '') === 'aprobado' ? 'text-bg-success' : (($a->estado ?? '') === 'rechazado' ? 'text-bg-danger' : 'text-bg-warning') }}">
                      {{ strtoupper($a->estado ?? 'PENDIENTE') }}
                    </span>
                  </td>
                  <td class="text-muted small">{{ optional($a->subido_en)->format('d/m/Y H:i') ?? (\Carbon\Carbon::parse($a->subido_en)->format('d/m/Y H:i') ?? '—') }}</td>
                  <td>
                    @php $url = $a->ruta ? \Illuminate\Support\Facades\Storage::disk('public')->url($a->ruta) : null; @endphp
                    @if($url)
                      <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary">Ver</a>
                    @else
                      <button class="btn btn-sm btn-outline-secondary" disabled>Sin archivo</button>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if(method_exists($archivos,'links'))
          <div class="mt-3">{{ $archivos->links() }}</div>
        @endif
      @endif
    </div>
  </div>
</div>
@endsection
