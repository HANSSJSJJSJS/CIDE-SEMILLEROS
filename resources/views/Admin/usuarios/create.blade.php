@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Crear nuevo usuario</h1>


   


    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            
        </div>
    @endif

    <form method="POST" action="{{ route('admin.usuarios.store') }}" class="card p-4 shadow-sm">
        @csrf

        {{-- Selector de Rol --}}
        <div class="mb-3">
            <label for="role" class="form-label fw-bold">Rol</label>
            <select id="role" name="role" class="form-select" required>
            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Selecciona un rol</option>
            <option value="ADMIN"            @selected(old('role')==='ADMIN')>Administrador</option>
            <option value="LIDER GENERAL"    @selected(old('role')==='LIDER GENERAL')>Líder General</option>
            <option value="LIDER SEMILLERO"  @selected(old('role')==='LIDER SEMILLERO')>Líder Semillero</option>
            <option value="APRENDIZ"         @selected(old('role')==='APRENDIZ')>Aprendiz</option>
            </select>

        </div>

        {{-- Contenedor de campos (oculto al inicio) --}}
        <div id="fieldsContainer" style="display:none;">
            <hr>

            {{-- Campos generales --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" data-common required disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" class="form-control" name="apellido" value="{{ old('apellido') }}" data-common required disabled>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" data-common required disabled>
            </div>

            {{-- Campos específicos por rol --}}
            {{-- LÍDER SEMILLERO --}}
            <div class="role-section" data-role="lider_semillero" style="display:none;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de documento</label>
                        <select name="tipo_documento_lider" class="form-select" disabled>
                            <option value="" disabled selected>Selecciona</option>
                            <option value="CC" @selected(old('tipo_documento_lider')==='CC')>Cédula de ciudadanía (CC)</option>
                            <option value="CE" @selected(old('tipo_documento_lider')==='CE')>Cédula de extranjería (CE)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Número de documento</label>
                        <input type="text" name="numero_documento_lider" class="form-control" value="{{ old('numero_documento_lider') }}" disabled>
                    </div>
                </div>
            </div>

            {{-- APRENDIZ --}}
            <div class="role-section" data-role="aprendiz" style="display:none;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de documento</label>
                        <select name="tipo_documento_aprendiz" class="form-select" disabled>
                            <option value="" disabled selected>Selecciona</option>
                            <option value="CC" @selected(old('tipo_documento_aprendiz')==='CC')>Cédula de ciudadanía (CC)</option>
                            <option value="TI" @selected(old('tipo_documento_aprendiz')==='TI')>Tarjeta de identidad (TI)</option>
                            <option value="CE" @selected(old('tipo_documento_aprendiz')==='CE')>Cédula de extranjería (CE)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Número de documento</label>
                        <input type="text" name="numero_documento_aprendiz" class="form-control" value="{{ old('numero_documento_aprendiz') }}" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ficha</label>
                        <input type="text" name="ficha" class="form-control" value="{{ old('ficha') }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Programa de formación</label>
                        <input type="text" name="programa_formacion" class="form-control" value="{{ old('programa_formacion') }}" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Celular</label>
                        <input type="text" name="celular" class="form-control" value="{{ old('celular') }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo institucional</label>
                        <input type="email" name="correo_institucional" class="form-control" value="{{ old('correo_institucional') }}" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contacto de emergencia</label>
                        <input type="text" name="contacto_emergencia" class="form-control" value="{{ old('contacto_emergencia') }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Número de contrato</label>
                        <input type="text" name="numero_contrato" class="form-control" value="{{ old('numero_contrato') }}" disabled>
                    </div>
                </div>
            </div>

            {{-- Contraseñas (comunes a todos los roles) --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" data-common required disabled>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control" data-common required disabled>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar usuario</button>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancelar</a>   
            <a href="{{ route('dashboard-admin') }}" class="btn btn-outline-secondary mb-3">   ← Regresar al Dashboard  </a>

        
            </div>
        </div>
    </form>
</div>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const container = document.getElementById('fieldsContainer');
    const roleSections = document.querySelectorAll('.role-section');

    // mostrar / ocultar campos según rol
    function toggleFields() {
        const role = roleSelect.value;
        const hasRole = !!role;

        // mostrar contenedor si hay rol
        container.style.display = hasRole ? '' : 'none';

        // mostrar sólo la sección del rol seleccionado
        roleSections.forEach(section => {
            section.style.display = (section.dataset.role === role) ? '' : 'none';
            section.querySelectorAll('input, select').forEach(el => {
                el.disabled = section.dataset.role !== role;
                el.required = section.dataset.role === role;
            });
        });

        // habilitar campos comunes
        document.querySelectorAll('[data-common]').forEach(el => {
            el.disabled = !hasRole;
        });
    }

    roleSelect.addEventListener('change', toggleFields);
    toggleFields(); // estado inicial (por si hay old input)
});
</script>
@endsection
