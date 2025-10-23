@extends('layouts.lider_semi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/calendario.css') }}">
<link rel="stylesheet" href="{{ asset('css/calendario-views.css') }}">
@endpush

@section('content')
<div class="container-fluid mt-4 px-4">
    <header class="calendario-header-custom">
        <h1>Calendario de Actividades</h1>
        <p class="subtitle">Programa y gestiona reuniones y eventos con tus semilleros</p>
    </header>

    <div class="dashboard-calendario">
        <div class="calendar-container">
            <div class="calendar-header">
                <div class="calendar-nav">
                    <button id="prev-period"><i class="fas fa-chevron-left"></i></button>
                    <button id="next-period"><i class="fas fa-chevron-right"></i></button>
                    <button id="today-btn" class="btn-today"><i class="fas fa-calendar-day"></i> Hoy</button>
                </div>
                <div class="current-period" id="current-period">Octubre 2025</div>
                <div class="view-switcher">
                    <button class="view-btn" data-view="year" title="Vista Año">
                        <i class="fas fa-calendar"></i> Año
                    </button>
                    <button class="view-btn active" data-view="month" title="Vista Mes">
                        <i class="fas fa-calendar-alt"></i> Mes
                    </button>
                    <button class="view-btn" data-view="week" title="Vista Semana">
                        <i class="fas fa-calendar-week"></i> Semana
                    </button>
                    <button class="view-btn" data-view="day" title="Vista Día">
                        <i class="fas fa-calendar-day"></i> Día
                    </button>
                </div>
            </div>

            <!-- Vista de Año -->
            <div class="calendar-view" id="year-view" style="display: none;">
                <div class="year-grid" id="year-grid">
                    <!-- Los meses se generarán con JavaScript -->
                </div>
            </div>

            <!-- Vista de Mes -->
            <div class="calendar-view active" id="month-view">
                <div class="calendar-grid" id="calendar-grid">
                    <!-- Los días se generarán con JavaScript -->
                </div>
            </div>

            <!-- Vista de Semana -->
            <div class="calendar-view" id="week-view" style="display: none;">
                <div class="week-container" id="week-container">
                    <!-- La semana se generará con JavaScript -->
                </div>
            </div>

            <!-- Vista de Día -->
            <div class="calendar-view" id="day-view" style="display: none;">
                <div class="day-container" id="day-container">
                    <!-- El día se generará con JavaScript -->
                </div>
            </div>
        </div>

        <div class="events-sidebar">
            <h2 class="section-title">Próximos Eventos</h2>
            <ul class="events-list" id="events-list">
                <!-- Los eventos se cargarán con JavaScript -->
            </ul>

            <div class="empty-state" id="empty-events" style="display: none;">
                <i class="far fa-calendar-plus"></i>
                <p>No hay eventos programados</p>
                <p class="text-muted small">Haz click en un día del calendario para agendar</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de selección de horario -->
<div class="modal-calendario" id="time-slot-modal">
    <div class="modal-content-calendario time-slot-modal-content">
        <div class="modal-header-calendario">
            <div class="modal-header-icon">
                <div class="form-icon-modal">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h3 class="modal-title-calendario" id="time-slot-title">Selecciona un Horario</h3>
                    <p class="modal-subtitle" id="time-slot-date">Fecha seleccionada</p>
                </div>
            </div>
            <button class="close-modal-calendario" id="close-time-slot-modal">&times;</button>
        </div>
        
        <div class="time-slots-container" id="time-slots-container">
            <!-- Los horarios se generarán con JavaScript -->
        </div>
    </div>
</div>

<!-- Modal para agregar/editar eventos - Wizard paso a paso -->
<div class="modal-calendario" id="event-modal">
    <div class="modal-content-calendario wizard-modal">
        <div class="modal-header-calendario">
            <div class="modal-header-icon">
                <div class="form-icon-modal">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div>
                    <h3 class="modal-title-calendario" id="modal-title">Nueva Reunión de Semillero</h3>
                    <p class="modal-subtitle">Completa los siguientes pasos para programar tu reunión</p>
                </div>
            </div>
            <button class="close-modal-calendario" id="close-modal">&times;</button>
        </div>
        
        <!-- Indicador de pasos -->
        <div class="wizard-steps">
            <div class="wizard-step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-label">Información Básica</div>
            </div>
            <div class="wizard-step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-label">Configuración</div>
            </div>
            <div class="wizard-step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-label">Detalles</div>
            </div>
        </div>
        
        <form id="event-form">
            @csrf
            <input type="hidden" id="event-id">
            <input type="hidden" id="event-date">
            <input type="hidden" id="event-time">
            
            <!-- PASO 1: Información Básica -->
            <div class="wizard-content" id="step-1" style="display: block;">
                <div class="step-header">
                    <h4><i class="fas fa-info-circle"></i> Información Básica de la Reunión</h4>
                    <p class="text-muted">Completa los datos principales de tu reunión</p>
                </div>
                
                <div class="form-group-calendario">
                    <label for="event-title">Título de la reunión <span class="required">*</span></label>
                    <input type="text" id="event-title" class="form-control-calendario" placeholder="Ej: Reunión Semillero IA" required>
                </div>
                
                <div class="form-group-calendario">
                    <label for="event-type">Tipo de reunión <span class="required">*</span></label>
                    <select id="event-type" class="form-control-calendario" required>
                        <option value="">Seleccione el tipo</option>
                        <option value="planificacion">Planificación de proyecto</option>
                        <option value="seguimiento">Seguimiento técnico</option>
                        <option value="revision">Revisión de avances</option>
                        <option value="capacitacion">Capacitación técnica</option>
                        <option value="general">Reunión general</option>
                    </select>
                </div>
                
                <div class="form-grid-2">
                    <div class="form-group-calendario">
                        <label for="event-duration">Duración <span class="required">*</span></label>
                        <select id="event-duration" class="form-control-calendario" required>
                            <option value="30">30 minutos</option>
                            <option value="60" selected>1 hora</option>
                            <option value="90">1 hora 30 minutos</option>
                            <option value="120">2 horas</option>
                            <option value="180">3 horas</option>
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
                    <select id="event-location" class="form-control-calendario" required>
                        <option value="">Seleccione ubicación</option>
                        <option value="sala1">Sala de Reuniones 1 - Bloque A</option>
                        <option value="sala2">Sala de Reuniones 2 - Bloque B</option>
                        <option value="lab1">Laboratorio de Informática 1</option>
                        <option value="lab2">Laboratorio de Informática 2</option>
                        <option value="virtual">Reunión Virtual</option>
                        <option value="otra">Otra ubicación</option>
                    </select>
                </div>
                
                <div class="form-group-calendario" id="virtual-link-group" style="display: none;">
                    <label for="event-virtual-link">Link de la reunión virtual <span class="required">*</span></label>
                    <input type="url" id="event-virtual-link" class="form-control-calendario" placeholder="https://teams.microsoft.com/... o https://meet.google.com/...">
                    <small class="form-help">Ingresa el enlace de Teams, Meet, Zoom u otra plataforma</small>
                </div>
            </div>
            
            <!-- PASO 2: Configuración -->
            <div class="wizard-content" id="step-2" style="display: none;">
                <div class="step-header">
                    <h4><i class="fas fa-cog"></i> Configuración de la Reunión</h4>
                    <p class="text-muted">Selecciona el proyecto y los participantes</p>
                </div>
                
                <div class="form-group-calendario">
                    <label for="event-proyecto">Proyecto Asociado</label>
                    <select id="event-proyecto" class="form-control-calendario">
                        <option value="">Sin proyecto asignado</option>
                        @foreach($proyectos as $proyecto)
                            <option value="{{ $proyecto->id_proyecto }}" data-aprendices='@json($proyecto->aprendices->pluck("id_aprendiz"))'>
                                {{ $proyecto->nombre_proyecto }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help">Al seleccionar un proyecto, se mostrarán sus aprendices asignados</small>
                </div>
                
                <div class="form-section-calendario">
                    <div class="participants-header">
                        <h4 class="section-title-modal">Participantes <span class="required">*</span></h4>
                        <button type="button" class="btn-select-all" id="select-all-participants">
                            <i class="fas fa-users"></i> Seleccionar Todos
                        </button>
                    </div>
                    
                    <div class="participants-filter">
                        <input type="text" id="search-participants" class="form-control-calendario" placeholder="Buscar aprendiz...">
                    </div>
                    
                    <div class="participants-container-modal" id="participants-list">
                        @foreach($aprendices as $aprendiz)
                        <div class="participant-item-modal" data-aprendiz-id="{{ $aprendiz->id_aprendiz }}" data-aprendiz-name="{{ strtolower($aprendiz->nombre_completo) }}">
                            <input type="checkbox" id="participant-{{ $aprendiz->id_aprendiz }}" 
                                   class="participant-checkbox-modal" 
                                   value="{{ $aprendiz->id_aprendiz }}">
                            <label for="participant-{{ $aprendiz->id_aprendiz }}" class="participant-info-modal">
                                <div class="participant-name-modal">{{ $aprendiz->nombre_completo }}</div>
                                <div class="participant-role-modal">Aprendiz - Semillero</div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="participants-count">
                        <i class="fas fa-user-check"></i>
                        <span id="selected-count">0</span> participante(s) seleccionado(s)
                    </div>
                </div>
            </div>
            
            <!-- PASO 3: Detalles -->
            <div class="wizard-content" id="step-3" style="display: none;">
                <div class="step-header">
                    <h4><i class="fas fa-file-alt"></i> Detalles de la Reunión</h4>
                    <p class="text-muted">Agrega información adicional y recordatorios</p>
                </div>
                
                <div class="form-group-calendario">
                    <label for="event-description">Descripción de la reunión</label>
                    <textarea id="event-description" class="form-control-calendario" rows="5" placeholder="Describe los temas a tratar, objetivos de la reunión, materiales necesarios, etc."></textarea>
                    <small class="form-help">Esta información será visible para todos los participantes</small>
                </div>
                
                <div class="form-section-calendario">
                    <h4 class="section-title-modal">Recordatorios</h4>
                    <p class="text-muted small">Selecciona cuándo deseas recibir un recordatorio</p>
                    <div class="notification-options-modal">
                        <div class="notification-option-modal selected" data-value="none">
                            <input type="radio" name="reminder" id="reminder-none" value="none" checked>
                            <label for="reminder-none">
                                <i class="far fa-bell-slash"></i>
                                <span>Sin recordatorio</span>
                            </label>
                        </div>
                        <div class="notification-option-modal" data-value="15">
                            <input type="radio" name="reminder" id="reminder-15min" value="15">
                            <label for="reminder-15min">
                                <i class="far fa-clock"></i>
                                <span>15 minutos antes</span>
                            </label>
                        </div>
                        <div class="notification-option-modal" data-value="30">
                            <input type="radio" name="reminder" id="reminder-30min" value="30">
                            <label for="reminder-30min">
                                <i class="far fa-clock"></i>
                                <span>30 minutos antes</span>
                            </label>
                        </div>
                        <div class="notification-option-modal" data-value="60">
                            <input type="radio" name="reminder" id="reminder-1h" value="60">
                            <label for="reminder-1h">
                                <i class="far fa-clock"></i>
                                <span>1 hora antes</span>
                            </label>
                        </div>
                        <div class="notification-option-modal" data-value="1440">
                            <input type="radio" name="reminder" id="reminder-1d" value="1440">
                            <label for="reminder-1d">
                                <i class="far fa-calendar"></i>
                                <span>1 día antes</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Resumen de la reunión -->
                <div class="meeting-summary">
                    <h4><i class="fas fa-clipboard-check"></i> Resumen de la Reunión</h4>
                    <div class="summary-item">
                        <strong>Título:</strong> <span id="summary-title">--</span>
                    </div>
                    <div class="summary-item">
                        <strong>Tipo:</strong> <span id="summary-type">--</span>
                    </div>
                    <div class="summary-item">
                        <strong>Fecha y Hora:</strong> <span id="summary-datetime">--</span>
                    </div>
                    <div class="summary-item">
                        <strong>Duración:</strong> <span id="summary-duration">--</span>
                    </div>
                    <div class="summary-item">
                        <strong>Ubicación:</strong> <span id="summary-location">--</span>
                    </div>
                    <div class="summary-item">
                        <strong>Participantes:</strong> <span id="summary-participants">--</span>
                    </div>
                </div>
            </div>
            
            <!-- Botones de navegación del wizard -->
            <div class="wizard-actions">
                <button type="button" class="btn-calendario btn-secondary-calendario" id="btn-prev-step" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn-calendario btn-secondary-calendario" id="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn-calendario btn-primary-calendario" id="btn-next-step">
                    Siguiente <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn-calendario btn-success-calendario" id="btn-submit" style="display: none;">
                    <i class="fas fa-check"></i> Programar Reunión
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/calendario.js') }}"></script>
<script src="{{ asset('js/calendario-views.js') }}"></script>
<script src="{{ asset('js/calendario-wizard.js') }}"></script>
@endsection
