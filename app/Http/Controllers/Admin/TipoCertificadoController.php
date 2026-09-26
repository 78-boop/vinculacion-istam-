<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoCertificado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipoCertificadoController extends Controller
{
    public function index(): View
    {
        $tipos = TipoCertificado::orderBy('orden')->orderBy('id')->get();

        return view('admin.tipos-certificado.index', compact('tipos'));
    }

    public function create(): View
    {
        // Se propone la siguiente posición libre en la lista
        $siguienteOrden = (int) TipoCertificado::max('orden') + 1;

        return view('admin.tipos-certificado.create', compact('siguienteOrden'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:tipos_certificado,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'orden' => ['required', 'integer', 'min:0', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $validated['codigo'] = strtoupper(trim($validated['codigo']));
        $validated['activo'] = $request->boolean('activo');
        TipoCertificado::create($validated);

        return redirect()->route('admin.tipos-certificado.index')
            ->with('success', 'Documento requerido creado correctamente.');
    }

    public function edit(TipoCertificado $tiposCertificado): View
    {
        return view('admin.tipos-certificado.edit', ['tipo' => $tiposCertificado]);
    }

    public function update(Request $request, TipoCertificado $tiposCertificado): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:tipos_certificado,codigo,' . $tiposCertificado->id],
            'nombre' => ['required', 'string', 'max:255'],
            'orden' => ['required', 'integer', 'min:0', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $validated['codigo'] = strtoupper(trim($validated['codigo']));
        $validated['activo'] = $request->boolean('activo');
        $tiposCertificado->update($validated);

        return redirect()->route('admin.tipos-certificado.index')
            ->with('success', 'Documento requerido actualizado correctamente.');
    }

    public function destroy(TipoCertificado $tiposCertificado): RedirectResponse
    {
        if ($tiposCertificado->certificadosEstudiante()->exists()) {
            return redirect()->route('admin.tipos-certificado.index')
                ->with('error', 'No se puede eliminar porque ya existen archivos asociados. Desactívalo para ocultarlo a los estudiantes.');
        }

        $tiposCertificado->delete();

        return redirect()->route('admin.tipos-certificado.index')
            ->with('success', 'Documento requerido eliminado correctamente.');
    }
}
