<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Panel del Líder de Semillero')</title>
      <link rel="icon" type="image/png" href="{{ asset('images/logo_pestaña.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    {{-- Estilos base comunes --}}
    <link href="{{ asset('css/common/base.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('css/common/layout.css') }}?v={{ time() }}" rel="stylesheet">

    {{-- Layout administrativo heredado y vistas --}}
    <link href="{{ asset('css/admin/admin-layout.css') }}?v={{ time() }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/admin/admin-views.css') }}?v={{ time() }}" rel="stylesheet"> --}}

    {{-- Estilos específicos del panel de Líder de Semillero --}}
    <link href="{{ asset('css/lider_semi/lider.css') }}?v={{ time() }}" rel="stylesheet">
    {{-- Loader CSS global --}}
    <link href="{{ asset('css/common/loader.css') }}?v={{ time() }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
    @yield('styles')
</head>

<body class="adm-body">
  {{-- Loader Overlay --}}
  <div id="pageLoader" class="page-loader" aria-hidden="true" role="status">
    <div class="loader">
      <div class="orbe" style="--index:0"></div>
      <div class="orbe" style="--index:1"></div>
      <div class="orbe" style="--index:2"></div>
      <div class="orbe" style="--index:3"></div>
      <div class="orbe" style="--index:4"></div>
    </div>
    <div class="loader-text">Cargando...</div>
  </div>
  <div class="adm-shell">
    <aside id="admSidebar" class="adm-sidebar">
      <div class="adm-brand brand-large animate__animated animate__flipInX">
        <img src="{{ asset('images/logo-sena.png') }}" alt="Logo SENA" class="brand-logo-lg">
        <div class="brand-system-lg">Sistema de Gestión Semillero</div>
      </div>

      <div class="menu-card">
        <nav class="adm-nav">
          <a href="{{ route('lider_semi.dashboard') }}" class="nav-link {{ request()->routeIs('lider_semi.dashboard*') ? 'active' : '' }}">
            <i class="bi bi-house-fill me-2"></i>
            <span>Inicio</span>
          </a>
          <a href="{{ route('lider_semi.semilleros') }}" class="nav-link {{ request()->routeIs('lider_semi.semilleros*') ? 'active' : '' }}">
            <i class="bi bi-hdd-stack me-2"></i> <span>Mis Proyectos</span>
          </a>
          <a href="{{ route('lider_semi.aprendices') }}" class="nav-link {{ request()->routeIs('lider_semi.aprendices*') ? 'active' : '' }}">
            <i class="bi bi-person-video2 me-2"></i> <span>Aprendices</span>
          </a>
          <a href="{{ route('lider_semi.documentos') }}" class="nav-link {{ request()->routeIs('lider_semi.documentos*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text me-2"></i> <span>Documentación</span>
          </a>
         <a href="{{ route('lider_semi.recursos.index') }}" class="nav-link {{ request()->routeIs('lider_semi.recursos.*') ? 'active' : '' }}">
          <i class="bi bi-archive me-2"></i> <span>Recursos</span> </a>
          <a href="{{ route('lider_semi.calendario') }}" class="nav-link {{ request()->routeIs('lider_semi.calendario*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event me-2"></i> <span>Calendario</span>
          </a>
          <a href="{{ route('lider_semi.perfil') }}" class="nav-link {{ request()->routeIs('lider_semi.perfil*') ? 'active' : '' }}">
            <i class="bi bi-person-circle me-2"></i> <span>Mi Perfil</span>
          </a>
        </nav>
      </div>
    </aside>

    <div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>

    <div class="adm-content">
      <header class="adm-topbar">
        <div class="d-flex align-items-center gap-2">
          <button id="sidebarToggle" class="btn btn-outline-light d-lg-none">
            <i class="bi bi-list"></i>
          </button>
          <h1 class="h5 m-0 title-green">Líder de Semillero</h1>
        </div>

        <div class="profile-info" style="position:relative;">
          <button id="notifBtn" class="btn btn-link text-white position-relative me-2" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="notifPanel" aria-label="Notificaciones">
            <i class="bi bi-bell fs-5"></i>
            <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
          </button>
          <!-- Panel de notificaciones -->
          <div id="notifPanel" class="card shadow-sm" role="region" aria-label="Notificaciones" aria-hidden="true" style="display:none; position:absolute; right:80px; top:48px; width:320px; max-height:60vh; overflow:auto; z-index:1080;">
            <div class="card-header px-3 py-2 d-flex align-items-center justify-content-between">
              <span class="fw-semibold">Notificaciones</span>
              <button id="notifClose" class="btn btn-sm btn-outline-secondary">Cerrar</button>
            </div>
            <div id="notifList" class="list-group list-group-flush"></div>
            <div id="notifEmpty" class="p-3 text-center text-muted" style="display:none;">Sin notificaciones</div>
          </div>
          @php
            $lsUser = Auth::user();
            $lsDisplayName = trim($lsUser->name ?? '');
            if (class_exists('Illuminate\\Support\\Facades\\Schema') && class_exists('Illuminate\\Support\\Facades\\DB')) {
                if (\Illuminate\Support\Facades\Schema::hasTable('lideres_semillero')) {
                    $lsProfile = \Illuminate\Support\Facades\DB::table('lideres_semillero')->where('id_usuario', $lsUser->id)->first();
                    if ($lsProfile) {
                        $lsDisplayName = trim(($lsProfile->nombres ?? '').' '.($lsProfile->apellidos ?? '')) ?: $lsDisplayName;
                    }
                }
            }
            if ($lsDisplayName === '' && !empty($lsUser->email)) {
                $lsDisplayName = $lsUser->email;
            }
            $lsParts = preg_split('/\s+/', $lsDisplayName) ?: [];
            $lsInitials = '';
            foreach ($lsParts as $part) {
                if ($part === '') continue;
                $lsInitials .= mb_substr($part, 0, 1);
                if (mb_strlen($lsInitials) >= 2) break;
            }
            if ($lsInitials === '' && $lsDisplayName !== '') {
                $lsInitials = mb_substr($lsDisplayName, 0, 1);
            }
          @endphp

          <div class="avatar">{{ strtoupper($lsInitials ?: 'LS') }}</div>
          <div class="me-2 text-end d-none d-sm-block">
            <div class="fw-semibold">{{ $lsDisplayName }}</div>
            <small class="opacity-75">Líder de Semillero</small>
          </div>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-light btn-sm">
              <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
            </button>
          </form>
        </div>
      </header>

      <main class="adm-page">
        <div class="adm-page-head">
          <h2 class="page-title">@yield('module-title','')</h2>
          <p class="page-subtitle">@yield('module-subtitle','')</p>
        </div>

        <div class="adm-page-body">
          @yield('content')
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  (function () {
    const sidebar   = document.getElementById('admSidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const body      = document.body;
    const MQ_LG     = 992;

    const isMobile = () => window.innerWidth < MQ_LG;

    function openSidebar() {
      if (!isMobile()) return;
      sidebar.classList.add('show');
      overlay.classList.add('show');
      toggleBtn?.setAttribute('aria-expanded', 'true');
      body.classList.add('noscroll');
    }
    function closeSidebar() {
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
      toggleBtn?.setAttribute('aria-expanded', 'false');
      body.classList.remove('noscroll');
    }
    function toggleSidebar() {
      if (!isMobile()) return;
      sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
    }

    toggleBtn?.addEventListener('click', toggleSidebar);
    overlay?.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebar(); });
    window.addEventListener('resize', () => { if (!isMobile()) closeSidebar(); });
    sidebar?.addEventListener('click', (e) => {
      if (e.target.closest('a.nav-link') && isMobile()) closeSidebar();
    });
  })();

  // ===== Notificaciones Líder Semillero =====
  (function(){
    const btn = document.getElementById('notifBtn');
    const panel = document.getElementById('notifPanel');
    const list = document.getElementById('notifList');
    const empty = document.getElementById('notifEmpty');
    const badge = document.getElementById('notifBadge');
    const closeBtn = document.getElementById('notifClose');
    if(!btn || !panel) return;

    const ROUTES = {
      obtener: "{{ route('lider_semi.eventos.obtener', [], false) }}",
      calendario: "{{ route('lider_semi.calendario', [], false) }}"
    };

    function togglePanel(show){
      const willShow = typeof show==='boolean'? show : (panel.style.display==='none');
      panel.style.display = willShow ? 'block':'none';
      panel.setAttribute('aria-hidden', willShow ? 'false':'true');
      btn.setAttribute('aria-expanded', willShow ? 'true':'false');
      if (willShow) { panel.focus?.(); }
    }

    function render(items){
      list.innerHTML = '';
      if (!Array.isArray(items) || items.length===0){
        empty.style.display='block';
        badge?.classList.add('d-none');
        return;
      }
      empty.style.display='none';
      // Limitar a 10
      const top = items.slice(0,10);
      top.forEach(n=>{
        const a = document.createElement('a');
        a.href = ROUTES.calendario;
        a.className = 'list-group-item list-group-item-action';
        a.innerHTML = `
          <div class="d-flex w-100 justify-content-between">
            <h6 class="mb-1">${n.title}</h6>
            <small class="text-${n.urgent?'danger':'muted'}">${n.timeLabel}</small>
          </div>
          <small class="text-muted">${n.meta}</small>
        `;
        list.appendChild(a);
      });
      // Badge
      const count = items.length;
      if (badge){ badge.textContent = String(count); badge.classList.toggle('d-none', count===0); }
    }

    async function fetchNotifications(){
      try{
        const now = new Date();
        const mes = now.getMonth()+1; const anio = now.getFullYear();
        const url = new URL(ROUTES.obtener, window.location.origin);
        url.searchParams.set('mes', mes); url.searchParams.set('anio', anio);
        const res = await fetch(url.toString(), { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
        if (!res.ok) throw new Error('HTTP '+res.status);
        const data = await res.json();
        const eventos = Array.isArray(data?.eventos) ? data.eventos : [];
        const items = [];
        const nowMs = Date.now();
        eventos.forEach(ev=>{
          try{
            const raw = String(ev.fecha_hora||'');
            const start = raw ? new Date(raw.replace(' ', 'T')) : null;
            if (!start || !isFinite(start.getTime())) return;
            const diffMin = Math.round((start.getTime() - nowMs)/60000);
            const isToday = (new Date().toDateString() === start.toDateString());
            const isVirtual = String(ev.ubicacion||'').toLowerCase()==='virtual';
            const hasLink = !!(ev.link_virtual);
            // Reglas de notificación
            if (diffMin >= 0 && diffMin <= 120) {
              items.push({
                urgent: diffMin <= 10,
                title: ev.titulo || 'Reunión próxima',
                timeLabel: diffMin <= 0 ? 'Ahora' : `${diffMin} min`,
                meta: `${isVirtual? (hasLink? 'Virtual':'Virtual (sin enlace)') : 'Presencial'} • ${start.toLocaleString('es-CO',{ dateStyle:'short', timeStyle:'short' })}`
              });
            } else if (isToday && isVirtual && !hasLink) {
              items.push({
                urgent: false,
                title: 'Falta enlace de reunión',
                timeLabel: start.toLocaleTimeString('es-CO',{ hour:'2-digit', minute:'2-digit' }),
                meta: ev.titulo || 'Reunión virtual'
              });
            }
          }catch(_){/* noop */}
        });
        // Orden: urgentes primero, luego por hora
        items.sort((a,b)=> (b.urgent - a.urgent));
        render(items);
      }catch(err){
        // En error, ocultar badge para no confundir
        badge?.classList.add('d-none');
      }
    }

    // Eventos UI
    btn.addEventListener('click', ()=> togglePanel());
    closeBtn?.addEventListener('click', ()=> togglePanel(false));
    document.addEventListener('click', (e)=>{
      if (!panel.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
        if (panel.style.display==='block') togglePanel(false);
      }
    });
    document.addEventListener('keydown', (e)=>{ if(e.key==='Escape' && panel.style.display==='block') togglePanel(false); });

    // Carga inicial y refresco periódico
    fetchNotifications();
    setInterval(fetchNotifications, 60000);
  })();
  </script>

  {{-- Loader JS: ocultar al terminar carga --}}
  <script>
    (function(){
      const hide = () => {
        const el = document.getElementById('pageLoader');
        if (!el) return;
        el.classList.add('hidden');
        setTimeout(()=>{ try{ el.remove(); }catch(e){} }, 400);
      };
      if (document.readyState === 'complete') hide();
      window.addEventListener('load', hide);
    })();
  </script>
@stack('scripts')

</body>

</html>
