
@php
    // Si no llegó $usuarios, usa una colección vacía para evitar el error
    $usuarios = $usuarios ?? collect();
@endphp


<h2 class="section-title">Gestión de Usuarios</h2>
<p class="section-subtitle">Listado de todos los usuarios registrados</p>

<div class="table-container">
  <table id="datosusuarios" class="table-activity">
    <thead>
      <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Creado</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($usuarios as $u)
        <tr>
          <td>{{ $u->id }}</td>
          <td>{{ $u->name }}</td>
          <td>{{ $u->email }}</td>
          <td>
            @php
              $rol = strtolower($u->role ?? '');
              $style = match($rol) {
                'administrador' => 'background:#DCFCE7;color:#16A34A;',
                'líder', 'lider' => 'background:#DBEAFE;color:#1E40AF;',
                'aprendiz' => 'background:#FEF3C7;color:#92400E;',
                default => 'background:#E0E7FF;color:#4338CA;',
              };
            @endphp
           
          </td>
          <td>{{ optional($u->created_at)->diffForHumans() ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center text-muted">No hay usuarios registrados.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
