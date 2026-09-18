<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProyectoVinculacion;
use App\Models\PeriodoAcademico;
use App\Models\User;
use Illuminate\Http\Request;

class ProyectoVinculacionController extends Controller
{
    public function index()
    {
        $proyectos = ProyectoVinculacion::with(['docente', 'periodoAcademico'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.proyectos.index', compact('proyectos'));
    }

    public function create()
    {
        $docentes = User::where('role', 'docente')->get();
        $periodos = PeriodoAcademico::orderByDesc('fecha_inicio')->get();

        return view('admin.proyectos.create', compact('docentes', 'periodos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'docente_id' => 'required|exists:users,id',
            'periodo_academico_id' => 'required|exists:periodo_academicos,id',
        ]);

        // Un proyecto creado directamente por el admin no necesita aprobación.
        $validated['estado'] = 'aprobado';

        ProyectoVinculacion::create($validated);

        return redirect()->route('admin.proyectos.index')->with('success', 'Proyecto de vinculación creado correctamente.');
    }

    // Docente: formulario para proponer un nuevo proyecto
    public function crearPropuesta()
    {
        $periodos = PeriodoAcademico::orderByDesc('fecha_inicio')->get();

        return view('docente.proyectos.crear', compact('periodos'));
    }

    // Docente: envía la propuesta (queda pendiente de aprobación)
    public function storePropuesta(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'required|string',
            'periodo_academico_id' => 'required|exists:periodo_academicos,id',
        ]);

        $validated['docente_id'] = auth()->id();
        $validated['estado'] = 'pendiente';

        ProyectoVinculacion::create($validated);

        return redirect()->route('dashboard')->with('success', 'Propuesta enviada. Un administrador debe aprobarla antes de que quede disponible.');
    }

    // Admin: aprueba una propuesta de proyecto
    public function aprobar(ProyectoVinculacion $proyecto)
    {
        $proyecto->update(['estado' => 'aprobado']);

        return redirect()->route('admin.proyectos.index')->with('success', 'Proyecto aprobado.');
    }

    // Admin: rechaza una propuesta de proyecto
    public function rechazar(ProyectoVinculacion $proyecto)
    {
        $proyecto->update(['estado' => 'rechazado']);

        return redirect()->route('admin.proyectos.index')->with('success', 'Proyecto rechazado.');
    }

    public function edit(ProyectoVinculacion $proyecto)
    {
        $docentes = User::where('role', 'docente')->get();
        $periodos = PeriodoAcademico::orderByDesc('fecha_inicio')->get();

        return view('admin.proyectos.edit', compact('proyecto', 'docentes', 'periodos'));
    }

    public function update(Request $request, ProyectoVinculacion $proyecto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'docente_id' => 'required|exists:users,id',
            'periodo_academico_id' => 'required|exists:periodo_academicos,id',
        ]);

        $proyecto->update($validated);

        return redirect()->route('admin.proyectos.index')->with('success', 'Proyecto de vinculación actualizado.');
    }

    public function destroy(ProyectoVinculacion $proyecto)
    {
        $proyecto->delete();
        return redirect()->route('admin.proyectos.index')->with('success', 'Proyecto de vinculación eliminado.');
    }
}