<x-app-layout>

    <div>
        <div class="ui-wrap" style="max-width: 860px;">
            <x-ui.hero :volver="route('admin.usuarios.index')" volver-texto="Usuarios" titulo="Editar usuario" subtitulo="Actualiza los datos y el rol del usuario." />

            <div class="bg-white shadow rounded-lg p-6 sm:p-8">
                <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST" data-confirm-title="¿Guardar los cambios?" data-confirm-text="Se actualizará la información." data-confirm-button="Sí, guardar">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Nombre completo</label>
                        <input type="text" name="name" value="{{ old('name', $usuario->name) }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500" required>
                        @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Cédula de identidad</label>
                        <input type="text" name="cedula" value="{{ old('cedula', $usuario->cedula) }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Necesaria para generar el certificado de vinculación.</p>
                        @error('cedula')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Correo electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $usuario->email) }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500" required>
                        @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Rol</label>
                        <select name="role"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500" required>
                            <option value="estudiante" {{ old('role', $usuario->role) == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                            <option value="docente" {{ old('role', $usuario->role) == 'docente' ? 'selected' : '' }}>Docente</option>
                            <option value="admin" {{ old('role', $usuario->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                        @error('role')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block font-medium text-sm text-gray-700">Carrera</label>
                            <a href="{{ route('admin.carreras.create') }}" class="text-sm font-semibold" style="color:#006B47">+ Nueva carrera</a>
                        </div>
                        <select name="carrera_id"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500">
                            <option value="">-- Sin carrera (solo aplica a estudiantes) --</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}" {{ old('carrera_id', $usuario->carrera_id) == $carrera->id ? 'selected' : '' }}>
                                    {{ $carrera->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrera_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Nueva contraseña</label>
                        <input type="password" name="password"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Déjalo en blanco para mantener la contraseña actual.</p>
                        <p class="text-xs text-gray-500 mt-1">Si la cambias: mínimo 8 caracteres, con mayúscula, minúscula, número y símbolo.</p>
                        @error('password')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmation"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500">
                        @error('password_confirmation')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="ui-btn ui-btn-primario">
                            <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                            Guardar Cambios
                        </button>
                        <a href="{{ route('admin.usuarios.index') }}"
                           class="ui-btn ui-btn-suave">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>