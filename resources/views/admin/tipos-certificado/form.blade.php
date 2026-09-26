@php($tipo = $tipo ?? null)

<div>
    <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
    <input id="codigo" name="codigo" value="{{ old('codigo', $tipo?->codigo) }}" maxlength="20" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm uppercase">
    @error('codigo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del documento</label>
    <input id="nombre" name="nombre" value="{{ old('nombre', $tipo?->nombre) }}" maxlength="255" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    @error('nombre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label for="orden" class="block text-sm font-medium text-gray-700">Posición en la lista de documentos</label>
    <input id="orden" name="orden" type="number" min="0" max="255" value="{{ old('orden', $tipo?->orden ?? ($siguienteOrden ?? 1)) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    <p class="mt-1 text-xs text-gray-500">
        Indica en qué lugar aparecerá este documento en la lista que ven el estudiante y el docente.
        Por ejemplo: <strong>1</strong> = primero, <strong>2</strong> = segundo, y así sucesivamente.
    </p>
    @error('orden') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<label class="inline-flex items-center gap-2">
    <input type="hidden" name="activo" value="0">
    <input type="checkbox" name="activo" value="1" @checked(old('activo', $tipo?->activo ?? true)) class="rounded border-gray-300 text-green-800">
    <span class="text-sm text-gray-700">Visible para estudiantes y docentes</span>
</label>
