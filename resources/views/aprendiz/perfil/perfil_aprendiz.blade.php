@extends('layouts.aprendiz')

@section('title','Mi Perfil')
@section('module-title','Mi Perfil')
@section('module-subtitle','Información personal y académica')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/aprendiz/style.css') }}">
@endpush

@section('content')
    <div class="container-xxl py-4 perfil-page">
        <div class="row justify-content-center">
            <!-- Contenido Principal -->
            <div class="col-12 col-lg-10 col-xl-9">
                <div class="glass-box p-4 p-md-5">

                    @if(session('success'))
                        <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger fw-semibold">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('aprendiz.perfil.update') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nombre completo</label>
                            @php
                                $nombreCompleto = trim(($user->nombre ?? '').' '.($user->apellidos ?? ''));
                                if ($nombreCompleto === '') {
                                    $nombreCompleto = $user->name ?? '';
                                }
                            @endphp
                            <input type="text" name="name" class="form-control pill-input @error('name') is-invalid @enderror" value="{{ old('name', $nombreCompleto) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control pill-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Programa de Formación</label>
                            <input type="text" class="form-control pill-input" value="{{ $aprendiz->programa ?? 'Semillero SENA' }}" disabled>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tipo de Documento</label>
                                <input type="text" name="tipo_documento" class="form-control pill-input" value="{{ old('tipo_documento', $user->tipo_documento ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Número de Documento</label>
                                <input type="text" name="documento" class="form-control pill-input" value="{{ old('documento', $user->documento ?? '') }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Celular</label>
                                <input type="text" name="celular" class="form-control pill-input" value="{{ old('celular', $user->celular ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sexo</label>
                                @php $sexoVal = old('sexo', $user->sexo ?? ($user->genero ?? '')); @endphp
                                <select name="sexo" class="form-select pill-input">
                                    <option value="">Seleccione</option>
                                    <option value="HOMBRE" {{ strtoupper($sexoVal)==='HOMBRE' ? 'selected' : '' }}>HOMBRE</option>
                                    <option value="MUJER" {{ strtoupper($sexoVal)==='MUJER' ? 'selected' : '' }}>MUJER</option>
                                    <option value="NO DEFINIDO" {{ strtoupper($sexoVal)==='NO DEFINIDO' ? 'selected' : '' }}>NO DEFINIDO</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">RH</label>
                                @php $rhVal = old('rh', $user->rh ?? ($user->tipo_rh ?? '')); @endphp
                                <select name="rh" class="form-select pill-input">
                                    <option value="">Seleccione</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $opt)
                                        <option value="{{ $opt }}" {{ $rhVal===$opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Correo Institucional</label>
                                @php $correoInst = old('correo_institucional', $aprendiz->correo_institucional ?? ($user->email ?? '')); @endphp
                                <input type="email" name="correo_institucional" class="form-control pill-input" value="{{ $correoInst }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Nivel Académico</label>
                            @php
                                $nivel = old('nivel_academico', $user->nivel_academico ?? ($user->nivelAcademico ?? ($user->nivel ?? ($user->nivel_educativo ?? ($aprendiz->nivel_academico ?? ($aprendiz->nivel_educativo ?? ''))))));
                            @endphp
                            <select name="nivel_academico" class="form-select pill-input">
                                <option value="">Seleccione</option>
                                <option value="ARTICULACION_MEDIA_10_11" {{ $nivel==='ARTICULACION_MEDIA_10_11' ? 'selected' : '' }}>ARTICULACION_MEDIA_10_11</option>
                                <option value="TECNOACADEMIA_7_9" {{ $nivel==='TECNOACADEMIA_7_9' ? 'selected' : '' }}>TECNOACADEMIA_7_9</option>
                                <option value="TECNICO" {{ $nivel==='TECNICO' ? 'selected' : '' }}>TECNICO</option>
                                <option value="TECNOLOGO" {{ $nivel==='TECNOLOGO' ? 'selected' : '' }}>TECNOLOGO</option>
                                <option value="PROFESIONAL" {{ $nivel==='PROFESIONAL' ? 'selected' : '' }}>PROFESIONAL</option>
                            </select>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-success btn-pill px-4 fw-semibold" type="submit">Guardar cambios</button>
                        </div>
                    </form>



                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
