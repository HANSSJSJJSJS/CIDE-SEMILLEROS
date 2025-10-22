@extends('layouts.admin')

@section('content')
  @include('admin.sesiones.dashboard-summary')
  @include('admin.sesiones.users-section')
  {{-- ya NO incluyas aquí users-modal --}}
  @include('admin.sesiones.semilleros-section')
  @include('admin.sesiones.reports-section')
  @include('admin.sesiones.activity-section')
  @include('admin.sesiones.settings-section')
@endsection

@push('modals')
  @include('admin.sesiones.users-modal')
@endpush
