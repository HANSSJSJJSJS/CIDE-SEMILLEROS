@extends('layouts.admin')
@section('title', 'Panel de Administración')
@includeWhen(isset($usuarios), 'admin.sections.gestion_usuario', ['usuarios' => $usuarios])
{{-- <- así revienta $usuarios --}}