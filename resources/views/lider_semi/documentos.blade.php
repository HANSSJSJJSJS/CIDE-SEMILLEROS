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
            <i class="fas fa-plus me-2"></i>Crear Evidencia
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
                        <div class="mt-3">
                            <button class="btn btn-ver-entregas w-100" onclick="abrirModalEntregas({{ $proyecto->id_proyecto }}, '{{ $proyecto->nombre }}')">
                                <i class="fas fa-folder-open me-2"></i>Ver Entregas
                            </button>
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
                        <div class="mt-3">
                            <button class="btn btn-ver-entregas w-100" onclick="abrirModalEntregas({{ $proyecto->id_proyecto }}, '{{ $proyecto->nombre }}')">
                                <i class="fas fa-folder-open me-2"></i>Ver Entregas
                            </button>
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
        <h2 id="tituloModalEvidencia">Registrar Evidencia de Avance</h2>
        
        <form id="formEvidencia">
            @csrf
            
            <!-- Nombre del Proyecto -->
            <div class="mb-3">
                <label class="form-label-evidencia">Nombre del Proyecto</label>
                <select class="form-select-evidencia" id="proyecto_id" name="proyecto_id" required>
                    <option value="">Selecciona el proyecto...</option>
                </select>
            </div>

            <!-- Aprendiz Asignado -->
            <div class="mb-3">
                <label class="form-label-evidencia">Aprendiz Asignado</label>
                <select class="form-select-evidencia" id="aprendiz_id" name="aprendiz_id">
                    <option value="">Sin asignar (selecciona primero un proyecto)</option>
                </select>
                <small class="text-muted">Selecciona un proyecto primero para ver los aprendices disponibles</small>
            </div>
            
            <!-- Lista de Evidencias Existentes (solo visible en modo edición) -->
            <div id="evidencias-existentes" style="display: none;">
                <h4 style="color: #1e4620; font-weight: 700; margin-bottom: 1rem;">Evidencias Subidas</h4>
                <div id="lista-evidencias" class="mb-4"></div>
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

<!-- Modal Editar Evidencia Individual -->
<div class="modal-overlay" id="modalEditarEvidencia">
    <div class="modal-evidencia">
        <button class="btn-cerrar-modal" onclick="cerrarModalEditarEvidencia()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 2rem; color: #666; cursor: pointer;">×</button>
        <h2>Editar Evidencias - <span id="nombreProyectoEditar"></span></h2>
        
        <form id="formEditarEvidencia">
            @csrf
            <input type="hidden" id="edit_documento_id" name="documento_id">
            
            <!-- Nombre del Aprendiz (Solo lectura) -->
            <div class="mb-3">
                <label class="form-label-evidencia">Aprendiz</label>
                <input type="text" class="form-control-evidencia" id="edit_nombre_aprendiz" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>

            <!-- Archivo (Solo lectura) -->
            <div class="mb-3">
                <label class="form-label-evidencia">Archivo</label>
                <input type="text" class="form-control-evidencia" id="edit_archivo_nombre" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>

            <!-- Fecha de Subida (Solo lectura) -->
            <div class="mb-3">
                <label class="form-label-evidencia">Fecha de Subida</label>
                <input type="text" class="form-control-evidencia" id="edit_fecha_subida" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>

            <!-- Estado (Solo lectura) -->
            <div class="mb-3">
                <label class="form-label-evidencia">Estado</label>
                <input type="text" class="form-control-evidencia" id="edit_estado_texto" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>

            <hr style="margin: 24px 0; border-color: #e0e0e0;">
            <h4 style="color: #1e4620; font-weight: 700; margin-bottom: 1rem;">Campos Editables</h4>

            <!-- Tipo de Documento (Editable) -->
            <div class="mb-3">
                <label class="form-label-evidencia">Tipo de Documento</label>
                <select class="form-select-evidencia" id="edit_tipo_documento" name="tipo_documento" required>
                    <option value="">Selecciona el tipo...</option>
                    <option value="pdf">Documento PDF</option>
                    <option value="word">Documento Word</option>
                    <option value="excel">Hoja de Cálculo</option>
                    <option value="presentacion">Presentación</option>
                    <option value="imagen">Imagen</option>
                    <option value="video">Video</option>
                    <option value="enlace">Enlace</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <!-- Fecha Límite de Entrega (Editable) -->
            <div class="mb-3">
                <label class="form-label-evidencia">Fecha Límite de Entrega</label>
                <input type="date" class="form-control-evidencia" id="edit_fecha_limite" name="fecha_limite" required>
            </div>

            <!-- Descripción (Editable) -->
            <div class="mb-3">
                <label class="form-label-evidencia">Descripción</label>
                <textarea class="form-control-evidencia form-textarea-evidencia" id="edit_descripcion" name="descripcion" placeholder="Agrega una descripción..." rows="4"></textarea>
            </div>

            <!-- Botones -->
            <div class="modal-botones">
                <button type="button" class="btn-cancelar-modal" onclick="cerrarModalEditarEvidencia()">Cancelar</button>
                <button type="submit" class="btn-guardar-modal">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
// Variable global para guardar el nombre del proyecto actual
let proyectoNombreActual = '';

// Función para abrir modal de entregas
function abrirModalEntregas(proyectoId, proyectoNombre) {
    proyectoNombreActual = proyectoNombre;
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
            console.log('Datos recibidos:', data); // Debug
            if (data.entregas && data.entregas.length > 0) {
                let html = '';
                data.entregas.forEach(entrega => {
                    console.log('Entrega:', entrega); // Debug
                    const estadoBadge = entrega.estado === 'pendiente' ? 'badge-pendiente' : 
                                       entrega.estado === 'aprobado' ? 'badge-aprobado' : 'badge-rechazado';
                    const estadoTexto = entrega.estado === 'pendiente' ? 'PENDIENTE' : 
                                       entrega.estado === 'aprobado' ? 'APROBADO' : 'RECHAZADO';
                    
                    html += `
                        <div class="entrega-card" style="position: relative;">
                            <!-- Botón de editar solo si NO está aprobado -->
                            ${entrega.estado !== 'aprobado' ? `
                                <button class="btn-editar-entrega" onclick="abrirModalEditarEvidencia(${entrega.id}, '${entrega.nombre_aprendiz}', '${entrega.archivo_nombre}', '${entrega.fecha}', '${entrega.estado}', '${(entrega.descripcion || '').replace(/'/g, "\\'")}', '${proyectoNombreActual}')" title="Editar evidencia">
                                    <i class="fas fa-edit"></i>
                                </button>
                            ` : `
                                <div class="badge-aprobado-lock" title="Evidencia aprobada - No editable">
                                    <i class="fas fa-lock"></i>
                                </div>
                            `}
                            
                            <div class="entrega-header">
                                <h3 class="entrega-nombre">${entrega.nombre_aprendiz}</h3>
                                <span class="${estadoBadge}">${estadoTexto}</span>
                            </div>
                            <div class="entrega-info">
                                ${entrega.archivo_nombre || 'Sin archivo'} · ${entrega.fecha}
                            </div>
                            <p class="entrega-descripcion">${entrega.descripcion || 'Sin descripción'}</p>
                            <div class="entrega-acciones">
                                ${(entrega.archivo_url && entrega.archivo_url !== '' && entrega.archivo_url !== 'null') ? `
                                    <button class="btn-ver-documento" onclick="verDocumento('${entrega.archivo_url}')">
                                        <i class="fas fa-file-alt"></i> Ver Documento
                                    </button>
                                ` : `
                                    <button class="btn-ver-documento" disabled style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="fas fa-file-alt"></i> Sin Archivo
                                    </button>
                                `}
                                ${entrega.estado === 'pendiente' ? `
                                    <button class="btn-aprobar" onclick="cambiarEstadoEntrega(${entrega.id}, 'aprobado')">
                                        Aprobar
                                    </button>
                                    <button class="btn-rechazar" onclick="cambiarEstadoEntrega(${entrega.id}, 'rechazado')">
                                        Rechazar
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
        
        // Restaurar estado original del modal
        document.getElementById('tituloModalEvidencia').textContent = 'Registrar Evidencia de Avance';
        const selectProyecto = document.getElementById('proyecto_id');
        selectProyecto.disabled = false;
        selectProyecto.style.backgroundColor = '';
        selectProyecto.style.cursor = '';
        document.getElementById('evidencias-existentes').style.display = 'none';
        document.getElementById('lista-evidencias').innerHTML = '';
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

    // Cargar aprendices cuando se selecciona un proyecto
    selectProyecto.addEventListener('change', function() {
        const proyectoId = this.value;
        const selectAprendiz = document.getElementById('aprendiz_id');
        
        if (!proyectoId) {
            selectAprendiz.innerHTML = '<option value="">Sin asignar (selecciona primero un proyecto)</option>';
            return;
        }
        
        // Cargar aprendices del proyecto
        fetch(`/lider_semi/proyectos/${proyectoId}/aprendices-list`)
            .then(response => response.json())
            .then(data => {
                selectAprendiz.innerHTML = '<option value="">Sin asignar</option>';
                if (data.aprendices && data.aprendices.length > 0) {
                    data.aprendices.forEach(aprendiz => {
                        const option = document.createElement('option');
                        option.value = aprendiz.id_aprendiz;
                        option.textContent = aprendiz.nombre_completo;
                        selectAprendiz.appendChild(option);
                    });
                } else {
                    selectAprendiz.innerHTML = '<option value="">No hay aprendices asignados a este proyecto</option>';
                }
            })
            .catch(error => {
                console.error('Error al cargar aprendices:', error);
                selectAprendiz.innerHTML = '<option value="">Error al cargar aprendices</option>';
            });
    });

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
                // Forzar recarga completa sin caché
                window.location.href = window.location.href.split('?')[0] + '?t=' + new Date().getTime();
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

// Función editarProyecto eliminada - ya no se usa

// Función para abrir modal de editar evidencia individual
function abrirModalEditarEvidencia(documentoId, nombreAprendiz, archivoNombre, fecha, estado, descripcion, proyectoNombre) {
    // Llenar campos de solo lectura
    document.getElementById('edit_documento_id').value = documentoId;
    document.getElementById('edit_nombre_aprendiz').value = nombreAprendiz;
    document.getElementById('edit_archivo_nombre').value = archivoNombre || 'Sin archivo';
    document.getElementById('edit_fecha_subida').value = fecha;
    
    // Mostrar estado en texto legible
    const estadoTexto = estado === 'pendiente' ? 'Pendiente' : 
                       estado === 'aprobado' ? 'Aprobado' : 'Rechazado';
    document.getElementById('edit_estado_texto').value = estadoTexto;
    
    // Llenar campos editables (vacíos por defecto para que el líder los complete)
    document.getElementById('edit_tipo_documento').value = '';
    document.getElementById('edit_fecha_limite').value = '';
    document.getElementById('edit_descripcion').value = descripcion || '';
    
    document.getElementById('nombreProyectoEditar').textContent = proyectoNombreActual || proyectoNombre;
    
    // Abrir modal
    document.getElementById('modalEditarEvidencia').classList.add('active');
}

// Función para cerrar modal de editar evidencia
function cerrarModalEditarEvidencia() {
    document.getElementById('modalEditarEvidencia').classList.remove('active');
    document.getElementById('formEditarEvidencia').reset();
}

// Manejar el envío del formulario de editar evidencia
document.addEventListener('DOMContentLoaded', function() {
    const formEditarEvidencia = document.getElementById('formEditarEvidencia');
    
    if (formEditarEvidencia) {
        formEditarEvidencia.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const documentoId = document.getElementById('edit_documento_id').value;
            const tipoDocumento = document.getElementById('edit_tipo_documento').value;
            const fechaLimite = document.getElementById('edit_fecha_limite').value;
            const descripcion = document.getElementById('edit_descripcion').value;
            
            const btnGuardar = formEditarEvidencia.querySelector('.btn-guardar-modal');
            btnGuardar.disabled = true;
            btnGuardar.textContent = 'Guardando...';
            
            // Actualizar el documento
            fetch(`/lider_semi/documentos/${documentoId}/actualizar`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    tipo_documento: tipoDocumento,
                    fecha_limite: fechaLimite,
                    descripcion: descripcion
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Evidencia actualizada exitosamente');
                    cerrarModalEditarEvidencia();
                    // Recargar la página para ver los cambios
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo actualizar la evidencia'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar la evidencia');
            })
            .finally(() => {
                btnGuardar.disabled = false;
                btnGuardar.textContent = 'Guardar Cambios';
            });
        });
    }
});
</script>
@endsection

