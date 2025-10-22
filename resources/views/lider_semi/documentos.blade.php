@extends('layouts.lider_semi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/documentos.css') }}">
@endpush

@section('content')
<div class="container-fluid mt-4 px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 documentos-header">
        <div>
            <h2>Gestión de Documentación</h2>
            <p>Revisa y gestiona los documentos subidos por tus aprendices</p>
        </div>
        <button class="btn btn-light btn-crear-proyecto" id="btnAbrirModal">
            <i class="fas fa-plus me-2"></i>Crear Proyecto
        </button>
    </div>

    <!-- Alerta de Pendientes -->
    @php
        $totalPendientes = isset($proyectosActivos) ? $proyectosActivos->sum('pendientes') : 0;
    @endphp
    @if($totalPendientes > 0)
    <div class="alert alerta-pendientes" role="alert">
        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
        <strong>Tienes {{ $totalPendientes }} documento(s) pendiente(s) de revisión</strong>
    </div>
    @endif

    <!-- Proyectos Activos -->
    <div class="mb-5">
        <h4 class="seccion-titulo">Proyectos Activos</h4>
        <div class="row g-4">
            @forelse($proyectosActivos ?? [] as $proyecto)
            <div class="col-md-4">
                <div class="card proyecto-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="proyecto-titulo">{{ $proyecto->nombre }}</h5>
                            <span class="badge badge-activo">ACTIVO</span>
                        </div>
                        <p class="proyecto-descripcion">{{ $proyecto->descripcion ?: 'Sin descripción' }}</p>
                        
                        <!-- Estadísticas -->
                        <div class="row text-center proyecto-estadisticas">
                            <div class="col-4">
                                <div class="estadistica-numero">{{ $proyecto->entregas }}</div>
                                <small class="estadistica-label">Entregas</small>
                            </div>
                            <div class="col-4">
                                <div class="estadistica-numero">{{ $proyecto->pendientes }}</div>
                                <small class="estadistica-label">Pendientes</small>
                            </div>
                            <div class="col-4">
                                <div class="estadistica-numero">{{ $proyecto->aprobadas }}</div>
                                <small class="estadistica-label">Aprobadas</small>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn btn-ver-entregas w-100" onclick="abrirModalEntregas({{ $proyecto->id_proyecto }}, '{{ $proyecto->nombre }}')">
                                    Ver Entregas
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-editar-proyecto w-100">
                                    Editar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="estado-vacio">
                    <i class="fas fa-folder-open"></i>
                    <p>No hay proyectos activos</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Proyectos Completados -->
    <div class="mb-5">
        <h4 class="seccion-titulo">Proyectos Completados</h4>
        <div class="row g-4">
            @forelse($proyectosCompletados ?? [] as $proyecto)
            <div class="col-md-4">
                <div class="card proyecto-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="proyecto-titulo">{{ $proyecto->nombre }}</h5>
                            <span class="badge badge-completado">COMPLETADO</span>
                        </div>
                        <p class="proyecto-descripcion">{{ $proyecto->descripcion ?: 'Sin descripción' }}</p>
                        
                        <!-- Estadísticas -->
                        <div class="row text-center proyecto-estadisticas">
                            <div class="col-4">
                                <div class="estadistica-numero">{{ $proyecto->entregas }}</div>
                                <small class="estadistica-label">Entregas</small>
                            </div>
                            <div class="col-4">
                                <div class="estadistica-numero">{{ $proyecto->pendientes }}</div>
                                <small class="estadistica-label">Pendientes</small>
                            </div>
                            <div class="col-4">
                                <div class="estadistica-numero">{{ $proyecto->aprobadas }}</div>
                                <small class="estadistica-label">Aprobadas</small>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn btn-ver-entregas w-100" onclick="abrirModalEntregas({{ $proyecto->id_proyecto }}, '{{ $proyecto->nombre }}')">
                                    Ver Entregas
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-editar-proyecto w-100">
                                    Editar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="estado-vacio">
                    <i class="fas fa-check-circle"></i>
                    <p>No hay proyectos completados</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Registrar Evidencia -->
<div class="modal-overlay" id="modalEvidencia">
    <div class="modal-evidencia">
        <h2>Registrar Evidencia de Avance</h2>
        
        <form id="formEvidencia">
            @csrf
            
            <!-- Nombre del Proyecto -->
            <div class="mb-3">
                <label class="form-label-evidencia">Nombre del Proyecto</label>
                <select class="form-select-evidencia" id="proyecto_id" name="proyecto_id" required>
                    <option value="">Selecciona el proyecto...</option>
                </select>
            </div>

            <!-- Título del Avance -->
            <div class="mb-3">
                <label class="form-label-evidencia">Título del Avance</label>
                <input type="text" class="form-control-evidencia" id="titulo" name="titulo" 
                       placeholder="Ej: Informe de pruebas funcionales" required>
            </div>

            <!-- Descripción del Avance -->
            <div class="mb-3">
                <label class="form-label-evidencia">Descripción del Avance</label>
                <textarea class="form-control-evidencia form-textarea-evidencia" id="descripcion" name="descripcion" 
                          placeholder="Describe brevemente el avance realizado..." required></textarea>
            </div>

            <!-- Tipo de Evidencia -->
            <div class="mb-3">
                <label class="form-label-evidencia">Tipo de Evidencia</label>
                <select class="form-select-evidencia" id="tipo_evidencia" name="tipo_evidencia" required>
                    <option value="">Selecciona el tipo de evidencia...</option>
                    <option value="pdf">Documento PDF</option>
                    <option value="enlace">Enlace</option>
                    <option value="documento">Documento Word</option>
                    <option value="presentacion">Presentación</option>
                    <option value="video">Video</option>
                    <option value="imagen">Imagen</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <!-- Campo Dinámico según Tipo de Evidencia -->
            <div id="campo-evidencia-container"></div>

            <!-- Fecha del Avance -->
            <div class="mb-3">
                <label class="form-label-evidencia">Fecha del Avance</label>
                <input type="date" class="form-control-evidencia" id="fecha" name="fecha" required>
            </div>

            <!-- Botones -->
            <div class="modal-botones">
                <button type="button" class="btn-cancelar-modal" id="btnCancelar">Cancelar</button>
                <button type="submit" class="btn-guardar-modal">Guardar Evidencia</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ver Entregas -->
<div class="modal-overlay" id="modalEntregas">
    <div class="modal-entregas">
        <button class="btn-cerrar-modal" onclick="cerrarModalEntregas()">×</button>
        <h2 id="tituloModalEntregas">Entregas - Proyecto</h2>
        
        <div id="contenedorEntregas">
            <!-- Las entregas se cargarán aquí dinámicamente -->
        </div>
    </div>
</div>

<script>
// Función para abrir modal de entregas
function abrirModalEntregas(proyectoId, proyectoNombre) {
    document.getElementById('tituloModalEntregas').textContent = `Entregas - ${proyectoNombre}`;
    document.getElementById('modalEntregas').classList.add('active');
    cargarEntregas(proyectoId);
}

// Función para cerrar modal de entregas
function cerrarModalEntregas() {
    document.getElementById('modalEntregas').classList.remove('active');
}

// Cargar entregas del proyecto
function cargarEntregas(proyectoId) {
    const contenedor = document.getElementById('contenedorEntregas');
    contenedor.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>';

    fetch(`/lider_semi/proyectos/${proyectoId}/entregas`)
        .then(response => response.json())
        .then(data => {
            if (data.entregas && data.entregas.length > 0) {
                let html = '';
                data.entregas.forEach(entrega => {
                    const estadoBadge = entrega.estado === 'pendiente' ? 'badge-pendiente' : 
                                       entrega.estado === 'aprobado' ? 'badge-aprobado' : 'badge-rechazado';
                    const estadoTexto = entrega.estado === 'pendiente' ? 'PENDIENTE' : 
                                       entrega.estado === 'aprobado' ? 'APROBADO' : 'RECHAZADO';
                    
                    html += `
                        <div class="entrega-card">
                            <div class="entrega-header">
                                <h3 class="entrega-nombre">${entrega.nombre_aprendiz}</h3>
                                <span class="${estadoBadge}">${estadoTexto}</span>
                            </div>
                            <div class="entrega-info">
                                ${entrega.archivo_nombre || 'Sin archivo'} · ${entrega.fecha}
                            </div>
                            <p class="entrega-descripcion">${entrega.descripcion}</p>
                            <div class="entrega-acciones">
                                ${entrega.estado === 'pendiente' ? `
                                    <button class="btn-aprobar" onclick="cambiarEstadoEntrega(${entrega.id}, 'aprobado')">
                                        Aprobar
                                    </button>
                                    <button class="btn-rechazar" onclick="cambiarEstadoEntrega(${entrega.id}, 'rechazado')">
                                        Rechazar
                                    </button>
                                ` : ''}
                                ${entrega.archivo_url ? `
                                    <button class="btn-ver-documento" onclick="verDocumento('${entrega.archivo_url}')">
                                        <i class="fas fa-file-alt"></i> Ver Documento
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                });
                contenedor.innerHTML = html;
            } else {
                contenedor.innerHTML = `
                    <div class="entregas-vacio">
                        <i class="fas fa-inbox"></i>
                        <p>No hay entregas para este proyecto</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contenedor.innerHTML = `
                <div class="entregas-vacio">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Error al cargar las entregas</p>
                </div>
            `;
        });
}

// Cambiar estado de entrega
function cambiarEstadoEntrega(entregaId, nuevoEstado) {
    if (!confirm(`¿Estás seguro de ${nuevoEstado === 'aprobado' ? 'aprobar' : 'rechazar'} esta entrega?`)) {
        return;
    }

    fetch(`/lider_semi/entregas/${entregaId}/estado`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ estado: nuevoEstado })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Entrega ${nuevoEstado === 'aprobado' ? 'aprobada' : 'rechazada'} exitosamente`);
            // Recargar entregas
            const proyectoId = data.proyecto_id;
            cargarEntregas(proyectoId);
            // Recargar página para actualizar estadísticas
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error: ' + (data.message || 'No se pudo actualizar el estado'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el estado de la entrega');
    });
}

// Ver documento
function verDocumento(url) {
    window.open(url, '_blank');
}

document.addEventListener('DOMContentLoaded', function() {
    const modalOverlay = document.getElementById('modalEvidencia');
    const btnAbrirModal = document.getElementById('btnAbrirModal');
    const btnCancelar = document.getElementById('btnCancelar');
    const formEvidencia = document.getElementById('formEvidencia');
    const selectProyecto = document.getElementById('proyecto_id');
    const selectTipoEvidencia = document.getElementById('tipo_evidencia');
    const campoEvidenciaContainer = document.getElementById('campo-evidencia-container');
    const inputFecha = document.getElementById('fecha');

    // Establecer fecha actual por defecto
    const hoy = new Date().toISOString().split('T')[0];
    inputFecha.value = hoy;

    // Manejar cambio de tipo de evidencia
    selectTipoEvidencia.addEventListener('change', function() {
        const tipo = this.value;
        campoEvidenciaContainer.innerHTML = '';

        if (!tipo) return;

        let campoHTML = '';
        let descripcionTipo = '';

        switch(tipo) {
            case 'pdf':
                descripcionTipo = 'El aprendiz deberá subir un documento en formato PDF';
                break;
            
            case 'enlace':
                descripcionTipo = 'El aprendiz deberá proporcionar un enlace (Google Drive, OneDrive, etc.)';
                break;
            
            case 'documento':
                descripcionTipo = 'El aprendiz deberá subir un documento Word (.doc o .docx)';
                break;
            
            case 'presentacion':
                descripcionTipo = 'El aprendiz deberá subir una presentación (PowerPoint, PDF, etc.)';
                break;
            
            case 'video':
                descripcionTipo = 'El aprendiz deberá proporcionar un enlace de video (YouTube, Vimeo, etc.)';
                break;
            
            case 'imagen':
                descripcionTipo = 'El aprendiz deberá subir una imagen (JPG, PNG, etc.)';
                break;
            
            case 'otro':
                descripcionTipo = 'El aprendiz deberá subir un archivo del tipo especificado';
                break;
        }

        campoHTML = `
            <div class="mb-3">
                <div class="alert" style="background-color:#e8f5e9;border-left:4px solid #5aa72e;border-radius:8px;padding:12px 16px;">
                    <i class="fas fa-info-circle text-success me-2"></i>
                    <strong>${descripcionTipo}</strong>
                </div>
            </div>
        `;

        campoEvidenciaContainer.innerHTML = campoHTML;
    });

    // Cargar proyectos al abrir el modal
    btnAbrirModal.addEventListener('click', function() {
        cargarProyectos();
        modalOverlay.classList.add('active');
    });

    // Cerrar modal
    btnCancelar.addEventListener('click', function() {
        cerrarModal();
    });

    // Cerrar modal al hacer clic fuera
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            cerrarModal();
        }
    });

    // Función para cerrar modal
    function cerrarModal() {
        modalOverlay.classList.remove('active');
        formEvidencia.reset();
        inputFecha.value = hoy;
    }

    // Cargar proyectos desde la base de datos
    function cargarProyectos() {
        fetch('/lider_semi/proyectos/list')
            .then(response => response.json())
            .then(data => {
                selectProyecto.innerHTML = '<option value="">Selecciona el proyecto...</option>';
                data.proyectos.forEach(proyecto => {
                    const option = document.createElement('option');
                    option.value = proyecto.id_proyecto;
                    option.textContent = proyecto.nombre_proyecto;
                    selectProyecto.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al cargar proyectos:', error);
                alert('Error al cargar los proyectos');
            });
    }

    // Enviar formulario
    formEvidencia.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(formEvidencia);
        const btnGuardar = formEvidencia.querySelector('.btn-guardar-modal');
        
        btnGuardar.disabled = true;
        btnGuardar.textContent = 'Guardando...';

        fetch('/lider_semi/evidencias/store', {
            method: 'POST',
            body: formData
            // No incluir headers para que el navegador establezca automáticamente Content-Type con boundary
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Evidencia registrada exitosamente');
                cerrarModal();
                location.reload(); // Recargar para mostrar cambios
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar la evidencia'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar la evidencia');
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.textContent = 'Guardar Evidencia';
        });
    });
});
</script>
@endsection

