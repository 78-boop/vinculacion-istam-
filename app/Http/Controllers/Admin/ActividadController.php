<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index()
    {
        $actividades = Actividad::with(['inscripcion.estudiante', 'inscripcion.proyecto'])
            ->latest('fecha')
            ->get();

        return view('admin.actividades.index', compact('actividades'));
    }

    public function create()
    {
        $inscripciones = Inscripcion::with(['estudiante', 'proyecto'])->get();

        return view('admin.actividades.create', compact('inscripciones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscripcion_id'      => ['required', 'exists:inscripcions,id'],
            'fecha'               => ['required', 'date'],
            'lugar'               => ['nullable', 'string', 'max:255'],
            'descripcion'         => ['required', 'string'],
            'horas'               => ['required', 'numeric', 'min:0.1', 'max:24'],
            'estado'              => ['required', 'in:pendiente,aprobada,rechazada'],
            'comentario_docente'  => ['nullable', 'string'],
        ]);

        Actividad::create($validated);

        return redirect()->route('admin.actividades.index')
            ->with('success', 'Actividad registrada correctamente.');
    }

    public function edit(Actividad $actividad)
    {
        $inscripciones = Inscripcion::with(['estudiante', 'proyecto'])->get();

        return view('admin.actividades.edit', compact('actividad', 'inscripciones'));
    }

    public function update(Request $request, Actividad $actividad)
    {
        $validated = $request->validate([
            'inscripcion_id'      => ['required', 'exists:inscripcions,id'],
            'fecha'               => ['required', 'date'],
            'lugar'               => ['nullable', 'string', 'max:255'],
            'descripcion'         => ['required', 'string'],
            'horas'               => ['required', 'numeric', 'min:0.1', 'max:24'],
            'estado'              => ['required', 'in:pendiente,aprobada,rechazada'],
            'comentario_docente'  => ['nullable', 'string'],
        ]);

        $actividad->update($validated);

        return redirect()->route('admin.actividades.index')
            ->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return redirect()->route('admin.actividades.index')
            ->with('success', 'Actividad eliminada correctamente.');
    }
}