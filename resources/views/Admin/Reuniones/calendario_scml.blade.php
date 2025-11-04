@extends('layouts.admin')

@push('styles')
@php($v = time())
<link rel="stylesheet" href="{{ asset('css/calendario.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/calendario-views.css') }}?v={{ $v }}">
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
  <div class="header">
    <div class="header-top">
      <h1>📅 Calendario SCML</h1>
      <div class="view-switcher">
        <button class="view-btn active" data-view="month">Mes</button>
        <button class="view-btn" data-view="week">Semana</button>
        <button class="view-btn" data-view="day">Día</button>
      </div>
    </div>
    <div class="header-controls">
      <div class="nav-controls">
        <button class="nav-btn" id="prevBtn">← Anterior</button>
        <button class="today-btn" id="todayBtn">Hoy</button>
        <button class="nav-btn" id="nextBtn">Siguiente →</button>
      </div>
      <div class="current-period" id="currentPeriod"></div>
    </div>
  </div>

  <!-- Vista de Mes -->
  <div class="calendar-month" id="monthView">
    <div class="weekdays">
      <div class="weekday">Dom</div>
      <div class="weekday">Lun</div>
      <div class="weekday">Mar</div>
      <div class="weekday">Mié</div>
      <div class="weekday">Jue</div>
      <div class="weekday">Vie</div>
      <div class="weekday">Sáb</div>
    </div>
    <div class="days-grid" id="daysGrid"></div>
  </div>

  <!-- Vista de Semana -->
  <div class="calendar-week" id="weekView">
    <div class="week-header" id="weekHeader"></div>
    <div class="week-grid" id="weekGrid"></div>
  </div>

  <!-- Vista de Día -->
  <div class="calendar-day" id="dayView">
    <div class="day-header" id="dayHeader"></div>
    <div class="day-grid" id="dayGrid"></div>
  </div>
</div>

<!-- Modal para agregar/editar eventos -->
<div class="modal-calendario" id="event-modal">
  <div class="modal-content-calendario">
    <div class="modal-header-calendario">
      <h3 class="modal-title-calendario" id="modal-title">Nueva Reunión</h3>
      <button class="close-modal-calendario" id="close-modal">&times;</button>
    </div>

    <form id="event-form">
      @csrf
      <input type="hidden" id="event-id">

      <div class="form-group-calendario">
        <label for="event-title">Título de la reunión *</label>
        <input type="text" id="event-title" class="form-control-calendario" placeholder="Ej: Reunión Semillero IA" required>
      </div>

      <div class="form-group-calendario">
        <label for="event-proyecto">Seleccionar semillero</label>
        <select id="event-proyecto" class="form-control-calendario">
          <option value="">Sin semillero seleccionado</option>
        </select>
      </div>

      <div class="form-group-calendario">
        <label for="event-date">Fecha *</label>
        <input type="date" id="event-date" class="form-control-calendario" required>
      </div>

      <div class="form-group-calendario">
        <label for="event-time">Hora *</label>
        <input type="time" id="event-time" class="form-control-calendario" required>
      </div>

      <div class="form-group-calendario">
        <label for="event-duration">Duración (minutos) *</label>
        <input type="number" id="event-duration" class="form-control-calendario" value="60" min="15" step="15" required>
      </div>

      <div class="form-group-calendario">
        <label for="event-description">Descripción</label>
        <textarea id="event-description" class="form-control-calendario" rows="3" placeholder="Detalles de la reunión..."></textarea>
      </div>

      <div class="form-group-calendario">
        <label for="event-participants">Participantes (líderes del semillero)</label>
        <select id="event-participants" class="form-control-calendario" multiple size="5"></select>
        <small class="text-muted">Mantén presionada la tecla Ctrl para seleccionar múltiples participantes</small>
      </div>

      <div class="form-actions-calendario">
        <button type="button" class="btn-calendario btn-secondary-calendario" id="cancel-event">Cancelar</button>
        <button type="submit" class="btn-calendario btn-primary-calendario">
          <i class="fas fa-save"></i> Guardar Reunión
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Drawer lateral (detalle de reunión) -->
<div id="event-detail-overlay" class="drawer-overlay" style="display:none;"></div>
<aside id="event-detail-drawer" class="drawer" aria-hidden="true" style="display:none;">
  <div class="drawer-header">
    <div class="drawer-avatar" id="detail-avatar">RM</div>
    <h3 class="drawer-title" id="detail-title">Detalle de la reunión</h3>
    <button type="button" class="drawer-close" id="drawer-close-btn">&times;</button>
  </div>

  <div class="drawer-body">
    <section class="drawer-section">
      <h4 class="drawer-section-title">Información General</h4>
      <div class="drawer-grid">
        <div class="drawer-field">
          <div class="drawer-label">Título</div>
          <div class="drawer-value" id="detail-titulo">--</div>
        </div>
        <div class="drawer-field">
          <div class="drawer-label">Tipo</div>
          <div class="drawer-value" id="detail-tipo">--</div>
        </div>
        <div class="drawer-field">
          <div class="drawer-label">Fecha</div>
          <div class="drawer-value" id="detail-fecha">--</div>
        </div>
        <div class="drawer-field">
          <div class="drawer-label">Hora</div>
          <div class="drawer-value" id="detail-hora">--</div>
        </div>
        <div class="drawer-field">
          <div class="drawer-label">Duración</div>
          <div class="drawer-value" id="detail-duracion">--</div>
        </div>
        <div class="drawer-field">
          <div class="drawer-label">Ubicación</div>
          <div class="drawer-value" id="detail-ubicacion">--</div>
        </div>
        <div class="drawer-field" id="detail-link-field" style="display:none;">
          <div class="drawer-label">Enlace</div>
          <a href="#" id="detail-link" class="drawer-link" target="_blank" rel="noopener" style="display:none;">Abrir reunión</a>
        </div>
      </div>
    </section>

    <section class="drawer-section">
      <h4 class="drawer-section-title">Participantes</h4>
      <select id="detail-participants" class="form-control-calendario" multiple size="5" disabled>
        <option value="">(Se muestra solo como referencia)</option>
      </select>
    </section>

    <section class="drawer-section">
      <h4 class="drawer-section-title">Descripción</h4>
      <div id="detail-descripcion" class="drawer-description">--</div>
    </section>

    <div class="drawer-actions">
      <button type="button" class="btn-calendario btn-secondary-calendario" id="drawer-close-cta">Cerrar</button>
    </div>
  </div>
</aside>

<!-- JSON config para JS -->
<script type="application/json" id="calendar-config">
{!! json_encode([
  'csrf'    => csrf_token(),
  'routes'  => [
    'obtener'     => route('admin.reuniones-lideres.obtener', [], false),
    'baseEventos' => route('admin.reuniones-lideres.store',   [], false),
    'semilleros'  => route('admin.reuniones-lideres.semilleros', [], false),
    'lideres'     => route('admin.reuniones-lideres.lideres',    [], false),
  ],
  'feriados' => config('app.feriados', []),
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>

@push('scripts')
  {{-- Archivos comunes del calendario --}}
  <script src="{{ asset('calendario-state.js') }}?v={{ $v }}"></script>
  <script src="{{ asset('calendario-views.js') }}?v={{ $v }}"></script>
  <script src="{{ asset('calendario-drag.js') }}?v={{ $v }}"></script>
  <script src="{{ asset('calendario-wizard.js') }}?v={{ $v }}"></script>

  {{-- Script del módulo Admin --}}
  <script src="{{ asset('js/Admin/reuniones.js') }}?v={{ $v }}" defer></script>
@endpush

@endsection
