<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Listado de usuarios</h3>
                    <a href="{{ route('admin.usuarios.index') }}"
                       class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-sm font-medium text-gray-700
                              ring-1 ring-inset ring-gray-300 hover:bg-gray-50
                              dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-gray-700">
                        Refrescar
                    </a>
                </div>

                <div class="px-4 pb-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Correo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registrado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">#{{ $user->id }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ optional($user->created_at)->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            <a href="#"
                                               class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-gray-300 text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-gray-700">
                                                Ver
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-300">
                                        No hay usuarios registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
