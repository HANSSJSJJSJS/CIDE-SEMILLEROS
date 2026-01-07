@extends('layouts.admin')

@push('styles')
@php($v = time())
<link rel="stylesheet" href="{{ asset('css/admin/calendario-admin.css') }}?v={{ $v }}">
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container cal-admin">
  <div class="header">
    <div class="header-top">
      <div class="header-title">
        <div class="title-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-week" viewBox="0 0 16 16">
            <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-5 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
          </svg>
        </div>
        <div class="title-text">
          <h1 class="title">Calendario SCML</h1>
          <div class="subtitle">Gestiona las reuniones con los líderes de semillero</div>
        </div>
      </div>
    </div>
    <div class="header-controls">
      <div class="view-switcher">
        <button class="view-btn active" data-view="month">Mes</button>
        <button class="view-btn" data-view="week">Semana</button>
        <button class="view-btn" data-view="day">Día</button>
      </div>
      <div class="nav-controls">
        <button class="nav-btn" id="prevBtn">&#x2039; Anterior</button>
        <button class="today-btn" id="todayBtn">Hoy</button>
        <button class="nav-btn" id="nextBtn">Siguiente &#x203A;</button>
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

        <div class="date-banner" id="date-banner">
          <div class="date-badge">
            <div class="day" id="date-banner-day">--</div>
            <div class="month" id="date-banner-month">---</div>
          </div>
          <div>
            <div class="date-text" id="date-banner-long">--</div>
            <div class="date-sub">Fecha seleccionada para la reunión</div>
          </div>
        </div>



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

        <div class="form-grid-2">
          <div class="form-group-calendario">
            <label for="event-duration">Duración <span class="required">*</span></label>
            <select id="event-duration" name="duracion" class="form-control-calendario" required>
              <option value="30">30 minutos</option>
              <option value="60" selected>1 hora</option>
              <option value="90">1 hora 30 minutos</option>
              <option value="120">2 horas</option>
              <option value="180">3 horas</option>
              <option value="240">4 horas</option>
            </select>
          </div>

          <div class="form-group-calendario">
            <label>Hora seleccionada</label>
            <div class="selected-time-display" id="selected-time-display">
              <i class="fas fa-clock"></i>
              <span id="display-time">--:-- --</span>
            </div>
          </div>
        </div>

        <div class="form-group-calendario">
          <label for="event-location">Ubicación <span class="required">*</span></label>
          <select id="event-location" name="ubicacion" class="form-control-calendario" required>
            <option value="">Seleccione ubicación</option>
            <option value="presencial">Presencial</option>
            <option value="virtual">Reunión Virtual</option>
            <option value="otra">Otra ubicación</option>
          </select>
        </div>

        <div class="form-group-calendario">
          <label for="event-description">Descripción</label>
          <textarea id="event-description" class="form-control-calendario" rows="3" placeholder="Detalles de la reunión..."></textarea>
        </div>

        <div class="form-group-calendario">
          <label for="event-participants">Participantes (líderes del semillero)</label>
          <div class="participants-box">
            <input type="text" id="participants-search" class="form-control-calendario participants-search" placeholder="Buscar líder...">
            <div class="participants-list" id="participants-list"></div>
            <select id="event-participants" class="form-control-calendario" multiple size="5" style="display:none;"></select>
            <div class="participants-chips" id="participants-chips"></div>
          </div>
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
            <div class="drawer-label">Agendada por</div>
            <div class="drawer-value" id="detail-creador">--</div>
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
            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
              <a href="#" id="detail-link" class="drawer-link" target="_blank" rel="noopener" style="display:none;">Abrir reunión</a>
              <button type="button" id="detail-delete-link" title="Eliminar enlace" style="display:none; background:none;border:none;cursor:pointer;font-size:18px;">🗑️</button>
            </div>
            <div id="detail-link-editor" style="display:none; margin-top:8px; width:100%;">
              <input type="url" id="detail-link-input" placeholder="Pega un enlace de reunión (https://...)" style="width:100%; padding:8px; border:1px solid #e1e4e8; border-radius:6px;" />
              <div style="display:flex; gap:8px; margin-top:8px;">
                <button type="button" id="detail-save-link" class="btn-calendario btn-primary-calendario btn-small">Guardar enlace</button>
                <button type="button" id="detail-cancel-edit" class="btn-calendario btn-secondary-calendario btn-small">Cancelar</button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="drawer-section" id="detail-asistencia-section" style="display:none;">
        <h4 class="drawer-section-title">Participantes</h4>
        <p class="text-muted" style="font-size:12px;">Marca si el participante asistió o no a la reunión virtual.</p>
        <div id="detail-asistencia-list" class="attendance-list"></div>
        <div id="detail-asistencia-summary" class="attendance-summary" style="margin-top:16px; display:none;"></div>
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
</div>


<!-- JSON config para JS -->
<script type="application/json" id="calendar-config">
{!! json_encode([
  'csrf'    => csrf_token(),
  'currentUserId' => Auth::id(),
  'currentUserRole' => optional(Auth::user())->role,
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
  {{-- Archivos comunes del calendario (ubicados en public/js) --}}
  <script src="{{ asset('js/calendario-state.js') }}?v={{ $v }}"></script>
  <script src="{{ asset('js/calendario-views.js') }}?v={{ $v }}"></script>
  <script src="{{ asset('js/calendario-drag.js') }}?v={{ $v }}"></script>
  <script src="{{ asset('js/calendario-wizard.js') }}?v={{ $v }}"></script>
  {{-- Si usas calendario.js también, inclúyelo así: --}}
  {{-- <script src="{{ asset('js/calendario.js') }}?v={{ $v }}"></script> --}}

  {{-- Script del módulo Admin --}}
  <script src="{{ asset('js/Admin/reuniones.js') }}?v={{ $v }}" defer></script>
@endpush




@endsection
