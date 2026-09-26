<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::withCount(['estudiantes', 'actividadesVinculacion'])
            ->orderByDesc('activo')
            ->orderBy('nombre')
            ->get()
            ->each(fn (Carrera $carrera) => $carrera->actividades_count = $carrera->actividades()->count());

        // Actividades distintas que tienen al menos un estudiante con carrera
        $totalActividades = \App\Models\Actividad::where(function ($query) {
            $query->whereHas('inscripciones.estudiante', fn ($q) => $q->whereNotNull('carrera_id'))
                ->orWhereHas('inscripcion.estudiante', fn ($q) => $q->whereNotNull('carrera_id'));
        })->count();

        return view('admin.carreras.index', compact('carreras', 'totalActividades'));
    }

    public function create()
    {
        return view('admin.carreras.create', ['carrera' => new Carrera(['activo' => true, 'horas_requeridas' => 90])]);
    }

    public function store(Request $request)
    {
        $validated = $this->validar($request);

        $validated['activo'] = $request->boolean('activo');
        $validated['imagen'] = $this->guardarImagen($request);

        Carrera::create($validated);

        return redirect()->route('admin.carreras.index')
            ->with('success', 'La carrera "' . $validated['nombre'] . '" se creó correctamente.');
    }

    public function edit(Carrera $carrera)
    {
        return view('admin.carreras.edit', compact('carrera'));
    }

    public function update(Request $request, Carrera $carrera)
    {
        $validated = $this->validar($request, $carrera);

        $validated['activo'] = $request->boolean('activo');

        if ($request->hasFile('imagen')) {
            $this->borrarImagen($carrera);
            $validated['imagen'] = $this->guardarImagen($request);
        } elseif ($request->boolean('quitar_imagen')) {
            $this->borrarImagen($carrera);
            $validated['imagen'] = null;
        } else {
            unset($validated['imagen']);
        }

        $carrera->update($validated);

        return redirect()->route('admin.carreras.index')
            ->with('success', 'La carrera "' . $carrera->nombre . '" se actualizó correctamente.');
    }

    public function destroy(Carrera $carrera)
    {
        $estudiantes = $carrera->estudiantes()->count();
        $actividades = $carrera->actividadesVinculacion()->count();

        // No se elimina si ya tiene datos asociados: se sugiere desactivarla
        if ($estudiantes > 0 || $actividades > 0) {
            return redirect()->route('admin.carreras.index')->with('warning',
                'No se puede eliminar "' . $carrera->nombre . '" porque tiene ' . $estudiantes . ' estudiante(s) y '
                . $actividades . ' actividad(es) asociadas. Puedes desactivarla desde "Editar" para ocultarla.');
        }

        $nombre = $carrera->nombre;
        $this->borrarImagen($carrera);
        $carrera->delete();

        return redirect()->route('admin.carreras.index')
            ->with('success', 'La carrera "' . $nombre . '" se eliminó correctamente.');
    }

    private function validar(Request $request, ?Carrera $carrera = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:120', Rule::unique('carreras', 'nombre')->ignore($carrera?->id)],
            'horas_requeridas' => ['required', 'integer', 'min:1', 'max:2000'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'activo' => ['nullable', 'boolean'],
        ], [
            'nombre.required' => 'Escribe el nombre de la carrera.',
            'nombre.unique' => 'Ya existe una carrera con ese nombre.',
            'horas_requeridas.required' => 'Indica las horas de vinculación requeridas.',
            'horas_requeridas.integer' => 'Las horas deben ser un número entero.',
            'horas_requeridas.min' => 'Las horas deben ser al menos 1.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser JPG, PNG o WEBP.',
            'imagen.max' => 'La imagen no puede pesar más de 3 MB.',
        ]);
    }

    // Guarda la imagen en storage/app/public/carreras y devuelve la ruta usable con asset()
    private function guardarImagen(Request $request): ?string
    {
        if (! $request->hasFile('imagen')) {
            return null;
        }

        return 'storage/' . $request->file('imagen')->store('carreras', 'public');
    }

    // Solo borra imágenes subidas (no las imágenes por defecto de public/images)
    private function borrarImagen(Carrera $carrera): void
    {
        if ($carrera->imagen && str_starts_with($carrera->imagen, 'storage/')) {
            Storage::disk('public')->delete(substr($carrera->imagen, strlen('storage/')));
        }
    }
}
