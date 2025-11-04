@extends('layouts.lider_semi')

@section('content')
<div class="container-fluid mt-4 px-4">
    <div class="mb-4">
        <h3 class="fw-bold" style="color:#2d572c;">Aprendices del Grupo</h3>
        <p class="text-muted">Supervisa el progreso y desempeño de tus aprendices</p>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de Documento</label>
                    <select id="filtro-tipo-doc" class="form-select form-underline">
                        <option value="">Todos</option>
                        <option value="CC">CC</option>
                        <option value="TI">TI</option>
                        <option value="CE">CE</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Número de Documento</label>
                    <input type="text" id="filtro-documento" class="form-control form-underline" placeholder="Ej: 1023456789">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" id="filtro-nombre" class="form-control form-underline" placeholder="Buscar por nombre">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button id="btn-limpiar-filtros" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="py-3 px-4">APRENDIZ</th>
                            <th class="py-3">PROYECTO</th>
                            <th class="py-3">DOCUMENTO</th>
                            <th class="py-3">ESTADO</th>
                            <th class="py-3 text-end pe-4">VER</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aprendices as $aprendiz)
                        <tr>
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex justify-content-center align-items-center" 
                                         style="width:42px;height:42px;background-color:#5aa72e;color:#fff;font-weight:700;font-size:14px;">
                                        {{ strtoupper(substr($aprendiz->nombre_completo ?? 'A', 0, 1)) }}{{ strtoupper(substr(explode(' ', $aprendiz->nombre_completo ?? 'M')[1] ?? 'M', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $aprendiz->nombre_completo }}</div>
                                        <small class="text-muted">{{ $aprendiz->correo_institucional ?? 'Sin correo' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge" style="background-color:#e8f5e9;color:#2d572c;border:1px solid #5aa72e;padding:6px 12px;border-radius:20px;">
                                    {{ $aprendiz->proyecto_nombre }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="fw-semibold">{{ $aprendiz->tipo_documento ?? 'CC' }}</div>
                                <small class="text-muted">{{ $aprendiz->documento ?? 'Sin documento' }}</small>
                            </td>
                            <td class="py-3">
                                @if($aprendiz->estado === 'Activo')
                                    <span class="badge bg-success" style="padding:6px 12px;border-radius:20px;">Activo</span>
                                @else
                                    <span class="badge bg-danger" style="padding:6px 12px;border-radius:20px;">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3 text-end pe-4">
                                <button class="btn btn-sm btn-outline-success btn-ver-detalle" 
                                        style="border-radius:20px;padding:4px 12px;"
                                        data-id="{{ $aprendiz->id_aprendiz }}"
                                        data-nombre="{{ $aprendiz->nombre_completo }}"
                                        data-tipo-doc="{{ $aprendiz->tipo_documento ?? 'CC' }}"
                                        data-documento="{{ $aprendiz->documento ?? '' }}"
                                        data-celular="{{ $aprendiz->celular ?? '' }}"
                                        data-correo-inst="{{ $aprendiz->correo_institucional ?? '' }}"
                                        data-correo-pers="{{ $aprendiz->correo_personal ?? '' }}"
                                        data-semillero="{{ $aprendiz->semillero_nombre }}"
                                        data-ficha="{{ $aprendiz->ficha ?? '' }}"
                                        data-programa="{{ $aprendiz->programa ?? '' }}"
                                        data-contacto-nombre="{{ $aprendiz->contacto_nombre ?? '' }}"
                                        data-contacto-celular="{{ $aprendiz->contacto_celular ?? '' }}">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                <p>No hay aprendices registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lateral -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="detalleAprendiz" style="width:500px;">
    <div class="offcanvas-header" style="background-color:#2d572c;color:#fff;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex justify-content-center align-items-center" 
                 style="width:50px;height:50px;background-color:#5aa72e;color:#fff;font-weight:700;font-size:18px;"
                 id="modal-iniciales">MG</div>
            <h5 class="mb-0 fw-bold" id="modal-nombre">María González</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-4">
        <!-- Información Personal -->
        <div class="mb-4">
            <h6 class="fw-bold mb-3" style="color:#2d572c;border-bottom:3px solid #5aa72e;padding-bottom:8px;">Información Personal</h6>
            <div class="row g-3">
                <div class="col-6">
                    <div class="p-3" style="background-color:#f8f9fa;border-left:4px solid #5aa72e;border-radius:4px;">
                        <small class="text-muted d-block mb-1">TIPO DE DOCUMENTO</small>
                        <div class="fw-semibold" id="modal-tipo-doc">CC</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3" style="background-color:#f8f9fa;border-left:4px solid #5aa72e;border-radius:4px;">
                        <small class="text-muted d-block mb-1">NUMERO DE DOCUMENTO</small>
                        <div class="fw-semibold" id="modal-documento">1087654321</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3" style="background-color:#f8f9fa;border-left:4px solid #5aa72e;border-radius:4px;">
                        <small class="text-muted d-block mb-1">CELULAR</small>
                        <div class="fw-semibold" id="modal-celular">3105551234</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3" style="background-color:#f8f9fa;border-left:4px solid #5aa72e;border-radius:4px;">
                        <small class="text-muted d-block mb-1">CORREO INSTITUCIONAL</small>
                        <div class="fw-semibold" style="font-size:12px;word-break:break-all;" id="modal-correo-inst">maria.gonzalez@sena.edu.co</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3" style="background-color:#f8f9fa;border-left:4px solid #5aa72e;border-radius:4px;">
                        <small class="text-muted d-block mb-1">CORREO PERSONAL</small>
                        <div class="fw-semibold" style="font-size:13px;word-break:break-all;" id="modal-correo-pers">maria.personal@gmail.com</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Académica -->
        <div class="mb-4">
            <h6 class="fw-bold mb-3" style="color:#2d572c;border-bottom:3px solid #5aa72e;padding-bottom:8px;">Información Académica</h6>
            <div class="row g-3">
                <div class="col-6">
                    <div class="p-3" style="background-color:#f8f9fa;border-left:4px solid #5aa72e;border-radius:4px;">
                        <small class="text-muted d-block mb-1">SEMILLERO</small>
                        <div class="fw-semibold" id="modal-semillero">IA</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3" style="background-color:#f8f9fa;border-left:4px solid #5aa72e;border-radius:4px;">
                        <small class="text-muted d-block mb-1">FICHA</small>
                        <div class="fw-semibold" id="modal-ficha">FICHA001</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3" style="background-color:#f8f9fa;border-left:4px solid #5aa72e;border-radius:4px;">
                        <small class="text-muted d-block mb-1">PROGRAMA</small>
                        <div class="fw-semibold" id="modal-programa">Programa IA</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contacto de Emergencia -->
        <div class="mb-4">
            <h6 class="fw-bold mb-3" style="color:#2d572c;border-bottom:3px solid #5aa72e;padding-bottom:8px;">Contacto de Emergencia</h6>
            <div class="p-3" style="background-color:#f8f9fa;border-radius:8px;">
                <div class="mb-2">
                    <small class="text-muted d-block">Nombre:</small>
                    <div class="fw-semibold" id="modal-contacto-nombre">Juan González</div>
                </div>
                <div>
                    <small class="text-muted d-block">Celular:</small>
                    <div class="fw-semibold" id="modal-contacto-celular">3105559876</div>
                </div>
            </div>
        </div>

        <button type="button" class="btn w-100" style="background-color:#5aa72e;color:#fff;" data-bs-dismiss="offcanvas">
            Cerrar
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script cargado correctamente');
    
    const modalElement = document.getElementById('detalleAprendiz');
    if (!modalElement) {
        console.error('No se encontró el elemento modal');
        return;
    }
    
    const modal = new bootstrap.Offcanvas(modalElement);
    
    // Elementos de filtro
    const filtroTipoDoc = document.getElementById('filtro-tipo-doc');
    const filtroDocumento = document.getElementById('filtro-documento');
    const filtroNombre = document.getElementById('filtro-nombre');
    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    const tbody = document.querySelector('tbody');
    const todasLasFilas = Array.from(tbody.querySelectorAll('tr:not(.no-results)'));
    
    // Función de filtrado
    function aplicarFiltros() {
        const tipoSeleccionado = filtroTipoDoc.value.toLowerCase();
        const documentoBuscado = filtroDocumento.value.toLowerCase().trim();
        const nombreBuscado = filtroNombre.value.toLowerCase().trim();
        
        let filasVisibles = 0;
        
        todasLasFilas.forEach(fila => {
            if (fila.querySelector('.no-results')) return;
            
            const tipoDoc = fila.querySelector('td:nth-child(3) .fw-semibold')?.textContent.toLowerCase() || '';
            const numDoc = fila.querySelector('td:nth-child(3) small')?.textContent.toLowerCase() || '';
            const nombre = fila.querySelector('td:nth-child(1) .fw-semibold')?.textContent.toLowerCase() || '';
            
            const cumpleTipo = !tipoSeleccionado || tipoDoc.includes(tipoSeleccionado);
            const cumpleDocumento = !documentoBuscado || numDoc.includes(documentoBuscado);
            const cumpleNombre = !nombreBuscado || nombre.includes(nombreBuscado);
            
            if (cumpleTipo && cumpleDocumento && cumpleNombre) {
                fila.style.display = '';
                filasVisibles++;
            } else {
                fila.style.display = 'none';
            }
        });
        
        // Mostrar mensaje si no hay resultados
        let mensajeNoResultados = tbody.querySelector('.no-results');
        if (filasVisibles === 0) {
            if (!mensajeNoResultados) {
                mensajeNoResultados = document.createElement('tr');
                mensajeNoResultados.className = 'no-results';
                mensajeNoResultados.innerHTML = '<td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-search fa-3x mb-3 opacity-25"></i><p>No se encontraron aprendices con los filtros aplicados</p></td>';
                tbody.appendChild(mensajeNoResultados);
            }
            mensajeNoResultados.style.display = '';
        } else if (mensajeNoResultados) {
            mensajeNoResultados.style.display = 'none';
        }
    }
    
    // Event listeners para filtros
    filtroTipoDoc.addEventListener('change', aplicarFiltros);
    filtroDocumento.addEventListener('input', aplicarFiltros);
    filtroNombre.addEventListener('input', aplicarFiltros);
    
    // Limpiar filtros
    btnLimpiar.addEventListener('click', function() {
        filtroTipoDoc.value = '';
        filtroDocumento.value = '';
        filtroNombre.value = '';
        aplicarFiltros();
    });
    
    // Modal - Botones Ver
    const botones = document.querySelectorAll('.btn-ver-detalle');
    console.log('Botones encontrados:', botones.length);
    
    botones.forEach((btn, index) => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Click en botón Ver', this.dataset);
            
            const nombre = this.dataset.nombre || '';
            const tipoDoc = this.dataset.tipoDoc || 'CC';
            const documento = this.dataset.documento || 'Sin documento';
            const celular = this.dataset.celular || 'Sin celular';
            const correoInst = this.dataset.correoInst || 'Sin correo';
            const correoPers = this.dataset.correoPers || 'Sin correo';
            const semillero = this.dataset.semillero || 'Sin asignar';
            const ficha = this.dataset.ficha || 'Sin ficha';
            const programa = this.dataset.programa || 'Sin programa';
            const contactoNombre = this.dataset.contactoNombre || 'Sin contacto';
            const contactoCelular = this.dataset.contactoCelular || 'Sin celular';
            
            console.log('Datos del aprendiz:', { nombre, tipoDoc, documento });
            
            // Generar iniciales
            const palabras = nombre.trim().split(' ');
            const ini1 = (palabras[0] || 'A').charAt(0).toUpperCase();
            const ini2 = (palabras[1] || 'M').charAt(0).toUpperCase();
            
            // Actualizar modal
            document.getElementById('modal-iniciales').textContent = ini1 + ini2;
            document.getElementById('modal-nombre').textContent = nombre;
            document.getElementById('modal-tipo-doc').textContent = tipoDoc;
            document.getElementById('modal-documento').textContent = documento;
            document.getElementById('modal-celular').textContent = celular;
            document.getElementById('modal-correo-inst').textContent = correoInst;
            document.getElementById('modal-correo-pers').textContent = correoPers;
            document.getElementById('modal-semillero').textContent = semillero;
            document.getElementById('modal-ficha').textContent = ficha;
            document.getElementById('modal-programa').textContent = programa;
            document.getElementById('modal-contacto-nombre').textContent = contactoNombre;
            document.getElementById('modal-contacto-celular').textContent = contactoCelular;
            
            console.log('Mostrando modal');
            modal.show();
        });
    });
});
</script>
@endsection

