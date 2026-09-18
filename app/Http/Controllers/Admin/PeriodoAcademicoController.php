<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;

class PeriodoAcademicoController extends Controller
{
    public function index()
    {
        $periodos = PeriodoAcademico::orderByDesc('fecha_inicio')->get();
        return view('admin.periodos.index', compact('periodos'));
    }

    public function create()
    {
        return view('admin.periodos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->has('activo');

        PeriodoAcademico::create($validated);

        return redirect()->route('admin.periodos.index')->with('success', 'Periodo académico creado correctamente.');
    }

    public function edit(PeriodoAcademico $periodo)
    {
        return view('admin.periodos.edit', compact('periodo'));
    }

    public function update(Request $request, PeriodoAcademico $periodo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->has('activo');

        $periodo->update($validated);

        return redirect()->route('admin.periodos.index')->with('success', 'Periodo académico actualizado.');
    }

    public function destroy(PeriodoAcademico $periodo)
    {
        $periodo->delete();
        return redirect()->route('admin.periodos.index')->with('success', 'Periodo académico eliminado.');
    }
}