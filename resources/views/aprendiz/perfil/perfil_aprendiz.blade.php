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
                            <input type="text" name="name" class="form-control pill-input @error('name') is-invalid @enderror" value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control pill-input @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Programa de Formación</label>
                            <input type="text" class="form-control pill-input" value="{{ $aprendiz->programa ?? 'Semillero SENA' }}" disabled>
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
