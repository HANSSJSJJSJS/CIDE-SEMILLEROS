
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Detalles del Semillero: {{ $semillero->nombre_semillero }}</h3>
                    <a href="{{ route('admin.semilleros.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Información del semillero -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Información del Semillero</h5>
                            <p><strong>Nombre:</strong> {{ $semillero->nombre_semillero }}</p>
                            <p><strong>Línea de investigación:</strong> {{ $semillero->linea_investigacion }}</p>
                            <p><strong>Líder:</strong>
                                @if($semillero->lider)
                                    {{ $semillero->lider->nombres }} {{ $semillero->lider->apellidos }}
                                @else
                                    <span class="text-muted">Sin líder asignado</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Estadísticas</h5>
                            <p><strong>Total de aprendices:</strong> {{ $semillero->aprendices->count() }}</p>
                            <p><strong>Proyectos activos:</strong> {{ $semillero->proyectos->count() }}</p>
                        </div>
                    </div>

                    <!-- Aprendices asignados -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Aprendices Asignados ({{ $semillero->aprendices->count() }})</h6>
                        </div>
                        <div class="card-body p-0">
                            @if($semillero->aprendices->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Documento</th>
                                                <th>Nombres</th>
                                                <th>Apellidos</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($semillero->aprendices as $aprendiz)
                                                <tr>
                                                    <td>{{ $aprendiz->documento }}</td>
                                                    <td>{{ $aprendiz->nombres }}</td>
                                                    <td>{{ $aprendiz->apellidos }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $aprendiz->estado == 'Activo' ? 'success' : 'secondary' }}">
                                                            {{ $aprendiz->estado }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('admin.semilleros.quitar-aprendiz', ['semillero' => $semillero->id_semillero, 'aprendiz' => $aprendiz->id_aprendiz]) }}"
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('¿Está seguro de retirar a este aprendiz del semillero?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-user-minus"></i> Retirar
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-3 text-center text-muted">
                                    <i class="fas fa-users-slash fa-2x mb-2"></i>
                                    <p class="mb-0">No hay aprendices asignados a este semillero.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Formulario para agregar aprendices -->
                    @if($aprendices->count() > 0)
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Agregar Aprendiz al Semillero</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.semilleros.asignar-aprendiz', $semillero->id_semillero) }}" method="POST">
                                    @csrf
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-8">
                                            <label for="aprendiz_id" class="form-label">Seleccionar Aprendiz</label>
                                            <select name="aprendiz_id" id="aprendiz_id" class="form-select" required>
                                                <option value="">-- Seleccione un aprendiz --</option>
                                                @foreach($aprendices as $aprendiz)
                                                    <option value="{{ $aprendiz->id_aprendiz }}">
                                                        {{ $aprendiz->nombres }} {{ $aprendiz->apellidos }}
                                                        ({{ $aprendiz->documento }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-user-plus"></i> Asignar Aprendiz
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.semilleros.edit', $semillero->id_semillero) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Semillero
                        </a>

                        <form action="{{ route('admin.semilleros.destroy', $semillero->id_semillero) }}" method="POST"
                              onsubmit="return confirm('¿Está seguro de eliminar este semillero? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Eliminar Semillero
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
