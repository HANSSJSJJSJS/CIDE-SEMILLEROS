@extends('layouts.aprendiz')

@section('title','Mi Perfil')
@section('module-title','Mi Perfil')
@section('module-subtitle','Información personal y académica')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/aprendiz/style.css') }}">
@endpush

@section('content')
    <div class="container py-2">
        <div class="row">
            <!-- Contenido Principal -->
            <div class="col-md-9">
                <div class="profile-section">
                    <h2 class="mb-3">Mi Perfil</h2>
                    <p class="text-muted mb-4">Información personal y académica</p>

                    <form>
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Programa de Formación</label>
                            <input type="text" class="form-control" value="Semillero SENA" disabled>
                        </div>

                        @if(isset($aprendiz->estado))
                            @if($aprendiz->estado === 'Activo')
                                <div class="alert alert-success text-center fw-bold">
                 Estado: HABILITADO EN EL SÍSTEMA
            </div>
        @else
            <div class="alert alert-danger text-center fw-bold">
                Estado: INHABILITADO EN EL SÍSTEMA
            </div>
        @endif
    @else
        <div class="alert alert-secondary text-center fw-bold">
            Estado NO DISPONIBLE EN EL SÍSTEMA
        </div>
    @endif

                    </form>

                    <button type="button" class="btn btn-primary edit-profile-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        Editar Perfil
                    </button>

                    <!-- Modal de edición de perfil -->
                    <div class="modal fade modal-sena" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editProfileForm" method="POST" action="{{ route('aprendiz.perfil.update') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Nombre Completo</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Correo Electrónico</label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Programa de Formación</label>
                                            <input type="text" class="form-control" name="programa_formacion" value="Semillero SENA" disabled>
                                        </div>

                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-sena">Actualizar Perfil</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const hasErrors = {!! json_encode($errors->any() ?? false) !!};
    if (hasErrors) {
        const modalEl = document.getElementById('editProfileModal');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    }
});
</script>
@endpush
