{{-- resources/views/auth/register.blade.php --}}
<x-guest-layout>
    {{-- AlpineJS para dinamismo --}}
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>

    <form method="POST" action="{{ route('register') }}"
          x-data="{ role: '{{ old('role') }}' }" x-cloak>
        @csrf

        <!-- Rol del usuario -->
        <div>
            <x-input-label for="role" :value="__('Rol del usuario')" />
            <select id="role" name="role"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                    x-model="role" required>
                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Seleccione un rol</option>
                <option value="ADMIN" @selected(old('role')==='ADMIN')>ADMIN</option>
                <option value="LIDER_INTERMEDIARIO" @selected(old('role')==='LIDER_INTERMEDIARIO')>LÍDER INTERMEDIARIO</option>
                <option value="LIDER_SEMILLERO" @selected(old('role')==='LIDER_SEMILLERO')>LÍDER SEMILLERO</option>
                <option value="APRENDIZ" @selected(old('role')==='APRENDIZ')>APRENDIZ</option>
                <option value="LIDER GENERAL" @selected(old('role')==='LIDER GENERAL')>LÍDER GENERAL</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Título según rol (aparece inmediatamente tras elegir el rol) -->
        <div class="mt-6" x-show="role === 'LIDER_SEMILLERO'">
            <h3 class="font-bold text-indigo-600 text-lg">Datos de Líder Semillero</h3>
        </div>
        <div class="mt-6" x-show="role === 'APRENDIZ'">
            <h3 class="font-bold text-indigo-600 text-lg">Datos de Aprendiz</h3>
        </div>
        <div class="mt-6" x-show="role === 'ADMIN'">
            <h3 class="font-bold text-indigo-600 text-lg">Datos de Administrador</h3>
        </div>
      <!-- ===== Datos de LÍDER GENERAL ===== -->
        <div class="mt-6" x-show="role === 'LIDER GENERAL'">
            <h3 class="font-bold text-indigo-600 text-lg mb-2">Datos de Líder General</h3></div>






        <!-- ORDEN NUEVO (comienza aquí) -->

        <!-- 1) Nombre completo (users.name) -->
       <!-- Nombre completo -->
            <div class="mt-4">
            <x-input-label for="nombres" :value="__('Nombres')" />
            <x-text-input id="nombres" class="block mt-1 w-full" type="text" name="nombres"
                            :value="old('nombres')" required autofocus />
            <x-input-error :messages="$errors->get('nombres')" class="mt-2" />
            </div>

            <!-- Apellidos -->
            <div class="mt-4">
            <x-input-label for="apellidos" :value="__('Apellidos')" />
            <x-text-input id="apellidos" class="block mt-1 w-full" type="text" name="apellidos"
                            :value="old('apellidos')" required />
            <x-input-error :messages="$errors->get('apellidos')" class="mt-2" />
            </div>

        <!-- 2) Tipo de documento -->
        {{-- LÍDER: usa lider_tipo_documento --}}
        <div class="mt-4" x-show="role === 'LIDER_SEMILLERO'">
            <x-input-label for="lider_tipo_documento" :value="__('Tipo de documento')" />
            <select id="lider_tipo_documento" name="lider_tipo_documento"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="" disabled {{ old('lider_tipo_documento') ? '' : 'selected' }}>Seleccione</option>
                <option value="CC" @selected(old('lider_tipo_documento')==='CC')>CC</option>
                <option value="CE" @selected(old('lider_tipo_documento')==='CE')>CE</option>
            </select>
            <x-input-error :messages="$errors->get('lider_tipo_documento')" class="mt-2" />
        </div>

        {{-- APRENDIZ: usa aprendiz_tipo_documento --}}
        <div class="mt-4" x-show="role === 'APRENDIZ'">
            <x-input-label for="aprendiz_tipo_documento" :value="__('Tipo de documento')" />
            <select id="aprendiz_tipo_documento" name="aprendiz_tipo_documento"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="" disabled {{ old('aprendiz_tipo_documento') ? '' : 'selected' }}>Seleccione</option>
                <option value="CC" @selected(old('aprendiz_tipo_documento')==='CC')>CC</option>
                <option value="CE" @selected(old('aprendiz_tipo_documento')==='CE')>CE</option>
                <option value="TI" @selected(old('aprendiz_tipo_documento')==='TI')>TI</option>
            </select>
            <x-input-error :messages="$errors->get('aprendiz_tipo_documento')" class="mt-2" />
        </div>

        <!-- 3) Documento -->
        {{-- LÍDER: lider_documento --}}
        <div class="mt-4" x-show="role === 'LIDER_SEMILLERO'">
            <x-input-label for="lider_documento" :value="__('Documento')" />
            <x-text-input id="lider_documento" name="lider_documento" type="text"
                          class="block mt-1 w-full" :value="old('lider_documento')" />
            <x-input-error :messages="$errors->get('lider_documento')" class="mt-2" />
        </div>

        {{-- APRENDIZ: aprendiz_documento --}}
        <div class="mt-4" x-show="role === 'APRENDIZ'">
            <x-input-label for="aprendiz_documento" :value="__('Documento')" />
            <x-text-input id="aprendiz_documento" name="aprendiz_documento" type="text"
                          class="block mt-1 w-full" :value="old('aprendiz_documento')" />
            <x-input-error :messages="$errors->get('aprendiz_documento')" class="mt-2" />
        </div>

        <!-- 4) Correo personal (users.email) -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo personal')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                          :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- 5) Celular -->
        {{-- Solo aplica/valida para APRENDIZ --}}
        <div class="mt-4" x-show="role === 'APRENDIZ'">
            <x-input-label for="aprendiz_celular" :value="__('Celular')" />
            <x-text-input id="aprendiz_celular" name="aprendiz_celular" type="tel"
                          class="block mt-1 w-full" :value="old('aprendiz_celular')" />
            <x-input-error :messages="$errors->get('aprendiz_celular')" class="mt-2" />
        </div>

        <!-- 6) Contacto de emergencia (nombre) -->
        {{-- Solo aplica/valida para APRENDIZ --}}
        <div class="mt-4" x-show="role === 'APRENDIZ'">
            <x-input-label for="aprendiz_contacto" :value="__('Contacto de emergencia (nombre)')" />
            <x-text-input id="aprendiz_contacto" name="aprendiz_contacto" type="text"
                          class="block mt-1 w-full" :value="old('aprendiz_contacto')" />
            <x-input-error :messages="$errors->get('aprendiz_contacto')" class="mt-2" />
        </div>

        <!-- 7) Número de contacto (del contacto de emergencia) -->
        {{-- Solo aplica/valida para APRENDIZ --}}
        <div class="mt-4" x-show="role === 'APRENDIZ'">
            <x-input-label for="aprendiz_cel_contacto" :value="__('Número de contacto (emergencia)')" />
            <x-text-input id="aprendiz_cel_contacto" name="aprendiz_cel_contacto" type="tel"
                          class="block mt-1 w-full" :value="old('aprendiz_cel_contacto')" />
            <x-input-error :messages="$errors->get('aprendiz_cel_contacto')" class="mt-2" />
        </div>

        <!-- Extras SOLO de APRENDIZ: Ficha, Programa, Correo institucional -->
        <div x-show="role === 'APRENDIZ'">
            <!-- Ficha -->
            <div class="mt-4">
                <x-input-label for="aprendiz_ficha" :value="__('Ficha')" />
                <x-text-input id="aprendiz_ficha" name="aprendiz_ficha" type="text"
                              class="block mt-1 w-full" :value="old('aprendiz_ficha')" />
                <x-input-error :messages="$errors->get('aprendiz_ficha')" class="mt-2" />
            </div>

            <!-- Programa -->
            <div class="mt-4">
                <x-input-label for="aprendiz_programa" :value="__('Programa')" />
                <x-text-input id="aprendiz_programa" name="aprendiz_programa" type="text"
                              class="block mt-1 w-full" :value="old('aprendiz_programa')" />
                <x-input-error :messages="$errors->get('aprendiz_programa')" class="mt-2" />
            </div>

            <!-- Correo institucional -->
            <div class="mt-4">
                <x-input-label for="aprendiz_correo_institucional" :value="__('Correo institucional')" />
                <x-text-input id="aprendiz_correo_institucional" name="aprendiz_correo_institucional" type="email"
                              class="block mt-1 w-full" placeholder="ej: nombre@misena.edu.co"
                              :value="old('aprendiz_correo_institucional')" />
                <x-input-error :messages="$errors->get('aprendiz_correo_institucional')" class="mt-2" />
            </div>
        </div>

        <!-- 8) Contraseña -->
        <div class="mt-6">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- 9) Confirmar contraseña -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Botones -->
        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100
                      rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                      dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('¿Ya tienes cuenta? Inicia sesión') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarme') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
