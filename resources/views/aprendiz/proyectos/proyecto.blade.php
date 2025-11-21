@extends('layouts.aprendiz')

@section('title','Mis Proyectos')
@section('module-title','Mis Proyectos')
@section('module-subtitle','Proyectos asignados en el semillero SENA')

@push('styles')
@endpush

@section('content')
<div class="container-xxl py-4">
  <div class="row gx-4">
    <!-- Main content -->
    <main class="col-12">
      <!-- Tarjetas resumen -->
      <div class="stat-cards row g-3 mb-2">
        <div class="col-12 col-md-4">
          <div class="stat-card stat-assigned d-flex flex-column justify-content-between p-3 h-100">
            <div class="stat-title">Proyectos Asignados</div>
            <div class="d-flex align-items-end justify-content-between">
              <div class="stat-value">{{ $countProyectos ?? 0 }}</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="stat-card stat-pending d-flex flex-column justify-content-between p-3 h-100">
            <div class="stat-title">Pendientes</div>
            <div class="d-flex align-items-end justify-content-between">
              <div class="stat-value">{{ $countPendientes ?? 0 }}</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="stat-card stat-complete d-flex flex-column justify-content-between p-3 h-100">
            <div class="stat-title">Completos</div>
            <div class="d-flex align-items-end justify-content-between">
              <div class="stat-value">{{ $countCompletos ?? 0 }}</div>
            </div>
          </div>
        </div>
      </div>
      

      <div class="mt-4">
        <div class="row g-4 g-xxl-5">
          @forelse ($proyectos as $proyecto)
            <div class="col-12 col-md-6 col-xl-4 d-flex">
              <div class="project-card shadow-sm w-100">
                <div class="project-topband">
                  <div>
                    <h5 class="m-0 fw-bolder">{{ $proyecto->nombre_proyecto }}</h5>
                    <small class="opacity-75">{{ $proyecto->estado ?? 'En progreso' }}</small>
                  </div>
                </div>
                <div class="p-3 p-lg-4">
                  <p class="text-muted mb-3">{{ $proyecto->descripcion ?? '—' }}</p>
                  <div class="fw-bold mb-1">Progreso</div>
                  <div class="progress progress-rounded my-2">
                    @php
                      $st = $stats[$proyecto->id_proyecto] ?? ['progreso_pct'=>0,'completos'=>0,'subidos'=>0];
                      $pct = (int)($st['progreso_pct'] ?? 0);
                      $comp = (int)($st['completos'] ?? 0);
                      $sub  = (int)($st['subidos'] ?? 0);
                    @endphp
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <div class="fw-bold mb-3">Documentos: <span class="opacity-75">{{ $comp }}/{{ $sub }}</span></div>
                  <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('aprendiz.proyectos.show', $proyecto->id_proyecto) }}" class="btn btn-pill px-4 fw-semibold" style="background-color:#39A900;border-color:#39A900;color:#ffffff;">Ver Detalles</a>
                    <a href="{{ route('aprendiz.documentos.index', ['proyecto' => $proyecto->id_proyecto]) }}" class="btn btn-outline-success btn-pill px-4 fw-semibold">Subir Docs</a>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12 text-center text-muted py-5">
              <i class="bi bi-kanban fs-1 d-block mb-2"></i>
              No tienes proyectos asignados aún.
            </div>
          @endforelse
        </div>
      </div>
    </main>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function(){
    const nameI = document.getElementById('filterName');
    const startI = document.getElementById('filterStart');
    const endI = document.getElementById('filterEnd');
    const clearB = document.getElementById('filterClear');
    const items = Array.from(document.querySelectorAll('.project-col'));

    function apply(){
      const q = (nameI?.value || '').trim().toLowerCase();
      const ds = startI?.value || '';
      const de = endI?.value || '';
      items.forEach(el => {
        const n = el.getAttribute('data-name') || '';
        const s = el.getAttribute('data-start') || '';
        const e = el.getAttribute('data-end') || '';
        let ok = true;
        if(q) ok = ok && n.includes(q);
        if(ds) ok = ok && (s >= ds);
        if(de) ok = ok && (e <= de);
        el.classList.toggle('d-none', !ok);
      });
    }
    nameI?.addEventListener('input', apply);
    startI?.addEventListener('change', apply);
    endI?.addEventListener('change', apply);
    clearB?.addEventListener('click', (e)=>{ e.preventDefault(); if(nameI) nameI.value=''; if(startI) startI.value=''; if(endI) endI.value=''; apply(); });
  })();
</script>
@endpush
