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
