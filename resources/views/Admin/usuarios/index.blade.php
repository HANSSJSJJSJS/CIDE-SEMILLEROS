<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                {{ __('Usuarios') }}
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Total: {{ $users->total() }}
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl ring-1 ring-gray-200/70 dark:ring-gray-700/50">
                {{-- Toolbar --}}
                <div class="p-4 sm:p-5 border-b border-gray-200/70 dark:border-gray-700/50">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Listado de usuarios
                        </h3>

                        <div class="flex flex-col sm:flex-row gap-2">
                            {{-- Búsqueda --}}
                            <form action="{{ route('admin.usuarios.index') }}" method="GET" class="flex">
                                <label class="sr-only" for="q">Buscar</label>
                                <input id="q" name="q" value="{{ request('q') }}"
                                       placeholder="Buscar por nombre o correo…"
                                       class="w-full sm:w-64 rounded-l-md border border-gray-300 dark:border-gray-600
                                              bg-white dark:bg-gray-900/30 px-3 py-2 text-sm
                                              text-gray-900 dark:text-gray-100 placeholder:text-gray-400
                                              focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <button class="rounded-r-md px-3 py-2 text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700">
                                    Buscar
                                </button>
                            </form>

                            {{-- Acciones rápidas --}}
                            <a href="{{ route('admin.usuarios.index') }}"
                               class="inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium
                                      bg-white dark:bg-gray-900/30 text-gray-700 dark:text-gray-200
                                      ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M13 2a1 1 0 10-2 0v2a1 1 0 102 0V2zM4.222 5.636a1 1 0 011.414-1.414l1.415 1.414A1 1 0 015.636 7.05L4.222 5.636zM2 11a1 1 0 100 2h2a1 1 0 100-2H2zm16.364-4.95l1.414-1.414a1 1 0 011.414 1.414L19.192 7.05a1 1 0 11-1.414-1zM20 11a1 1 0 100 2h2a1 1 0 100-2h-2zM6.05 18.364L4.636 19.78a1 1 0 01-1.414-1.415l1.414-1.414a1 1 0 011.414 1.414zM11 20a1 1 0 102 0v2a1 1 0 10-2 0v-2zm8.778-1.636a1 1 0 10-1.414-1.414l-1.415 1.414a1 1 0 101.415 1.414l1.414-1.414z"/>
                                </svg>
                                Refrescar
                            </a>

                            @can('create', App\Models\User::class)
                            <a href="{{ route('admin.usuarios.create') }}"
                               class="inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-semibold
                                      bg-indigo-600 text-white hover:bg-indigo-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 5a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h5V6a1 1 0 011-1z"/>
                                </svg>
                                Nuevo
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- Tabla --}}
                <div class="px-4 pb-4 sm:px-5 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/80 dark:bg-gray-900/30 backdrop-blur supports-[backdrop-filter]:bg-white/60 sticky top-0 z-10">
                            <tr class="text-xs uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Usuario</th>
                                <th class="px-4 py-3 text-left">Correo</th>
                                <th class="px-4 py-3 text-left">Registrado</th>
                                <th class="px-4 py-3 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">#{{ $user->id }}</td>

                                    {{-- Avatar + nombre --}}
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 shrink-0 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 text-white grid place-content-center text-sm font-semibold">
                                                {{ mb_substr($user->name,0,1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $user->name }}
                                                </div>
                                                @if(method_exists($user,'roles') && $user->roles->count())
                                                    <div class="mt-0.5 flex flex-wrap gap-1">
                                                        @foreach($user->roles as $role)
                                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-300 dark:bg-indigo-900/30 dark:text-indigo-200 dark:ring-indigo-700/60">
                                                                {{ $role->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                        <a href="mailto:{{ $user->email }}" class="hover:underline">{{ $user->email }}</a>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-700/60 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-200">
                                            {{ optional($user->created_at)->format('Y-m-d H:i') }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.usuarios.show',$user) }}"
                                               class="inline-flex items-center rounded-md px-2.5 py-1.5 text-xs font-medium
                                                      bg-white dark:bg-gray-900/30 text-gray-700 dark:text-gray-200
                                                      ring-1 ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/60">
                                                Ver
                                            </a>
                                            @can('update',$user)
                                            <a href="{{ route('admin.usuarios.edit',$user) }}"
                                               class="inline-flex items-center rounded-md px-2.5 py-1.5 text-xs font-medium
                                                      bg-indigo-600 text-white hover:bg-indigo-700">
                                                Editar
                                            </a>
                                            @endcan
                                            @can('delete',$user)
                                            <form action="{{ route('admin.usuarios.destroy',$user) }}" method="POST" onsubmit="return confirm('¿Eliminar este usuario?')">
                                                @csrf @method('DELETE')
                                                <button
                                                    class="inline-flex items-center rounded-md px-2.5 py-1.5 text-xs font-medium
                                                           bg-rose-600 text-white hover:bg-rose-700">
                                                    Eliminar
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-16 text-center">
                                        <div class="mx-auto max-w-sm">
                                            <div class="mx-auto h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700/60 grid place-content-center mb-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z"/></svg>
                                            </div>
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">No hay usuarios</h4>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cuando registres usuarios, aparecerán aquí.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Paginación --}}
                    <div class="mt-4">
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
