<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Models\ProyectoVinculacion;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UsuarioController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $rolFiltro = $request->query('rol');
        $buscar = trim((string) $request->query('buscar', ''));

        // Paginado: el instituto puede tener miles de usuarios
        $usuarios = User::with('carrera')
            ->when($rolFiltro, function ($query) use ($rolFiltro) {
                $query->where('role', $rolFiltro);
            })
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(fn ($q) => $q->where('name', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%")
                    ->orWhere('cedula', 'like', "%{$buscar}%"));
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $conteos = [
            'todos' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'docente' => User::where('role', 'docente')->count(),
            'estudiante' => User::where('role', 'estudiante')->count(),
        ];

        return view('admin.usuarios.index', compact('usuarios', 'rolFiltro', 'conteos', 'buscar'));
    }

    public function create()
    {
        $carreras = \App\Models\Carrera::where('activo', true)->orderBy('nombre')->get();
        return view('admin.usuarios.create', compact('carreras'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cedula' => 'nullable|string|max:20|unique:users,cedula',
            'carrera_id' => 'nullable|exists:carreras,id',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            // 'coordinador' existe en la base de datos pero todavía no tiene
            // panel propio en DashboardController, así que no lo ofrecemos aquí.
            'role' => ['required', Rule::in(['admin', 'docente', 'estudiante'])],
            'password' => ['required', 'confirmed', $this->passwordRule()],
        ], $this->validationMessages());

        $usuario = User::create([
            'name' => $validated['name'],
            'cedula' => $validated['cedula'] ?? null,
            'carrera_id' => $validated['carrera_id'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        // 'email_verified_at' no está en $fillable de User, así que se marca aparte
        // (un usuario registrado por el admin se considera verificado de entrada).
        $usuario->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function edit(User $usuario)
    {
        $carreras = \App\Models\Carrera::where('activo', true)
            ->when($usuario->carrera_id, fn ($q) => $q->orWhere('id', $usuario->carrera_id))
            ->orderBy('nombre')->get();
        return view('admin.usuarios.edit', compact('usuario', 'carreras'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cedula' => ['nullable', 'string', 'max:20', Rule::unique('users', 'cedula')->ignore($usuario->id)],
            'carrera_id' => 'nullable|exists:carreras,id',
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'role' => ['required', Rule::in(['admin', 'docente', 'estudiante'])],
            'password' => ['nullable', 'confirmed', $this->passwordRule()],
        ], $this->validationMessages());

        if ($validated['role'] !== $usuario->role) {
            $motivo = $this->motivoCambioRol($usuario);
            if ($motivo) {
                return back()->withInput()->withErrors(['role' => $motivo]);
            }
        }

        $usuario->name = $validated['name'];
        $usuario->cedula = $validated['cedula'] ?? null;
        $usuario->carrera_id = $validated['carrera_id'] ?? null;
        $usuario->email = $validated['email'];
        $usuario->role = $validated['role'];

        if (!empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
        }

        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $dependencias = $this->dependencias($usuario);
        if ($dependencias) {
            return redirect()->route('admin.usuarios.index')->with('error',
                'No se puede eliminar a ' . $usuario->name . ' porque tiene ' . implode(' y ', $dependencias) . '. '
                . 'Reasígnalos o elimínalos primero.');
        }

        try {
            $usuario->delete();
        } catch (QueryException $e) {
            return redirect()->route('admin.usuarios.index')->with('error',
                'No se puede eliminar a ' . $usuario->name . ' porque tiene registros asociados en el sistema (por ejemplo, documentos que revisó).');
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado.');
    }

    // Registros que se borrarían en cascada si se elimina al usuario
    private function dependencias(User $usuario): array
    {
        $lista = [];
        $proyectos = ProyectoVinculacion::where('docente_id', $usuario->id)->count();
        if ($proyectos) {
            $lista[] = $proyectos . ($proyectos === 1 ? ' proyecto a su cargo' : ' proyectos a su cargo');
        }
        $inscripciones = Inscripcion::where('estudiante_id', $usuario->id)->count();
        if ($inscripciones) {
            $lista[] = $inscripciones . ($inscripciones === 1 ? ' inscripción registrada' : ' inscripciones registradas');
        }
        return $lista;
    }

    private function motivoCambioRol(User $usuario): ?string
    {
        if ($usuario->id === auth()->id()) {
            return 'No puedes cambiar tu propio rol de administrador.';
        }
        if ($usuario->role === 'docente' && ProyectoVinculacion::where('docente_id', $usuario->id)->exists()) {
            return 'Este docente tiene proyectos a su cargo; reasígnalos antes de cambiar su rol.';
        }
        if ($usuario->role === 'estudiante' && Inscripcion::where('estudiante_id', $usuario->id)->exists()) {
            return 'Este estudiante tiene inscripciones registradas; no se puede cambiar su rol.';
        }
        return null;
    }

    private function passwordRule(): Rules\Password
    {
        return Rules\Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols();
    }

    private function validationMessages(): array
    {
        return [
            'name.required' => 'El nombre completo es obligatorio.',
            'name.max' => 'El nombre no puede superar los :max caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'role.required' => 'Selecciona un rol para el usuario.',
            'role.in' => 'El rol seleccionado no es válido.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'cedula.unique' => 'Esta cédula ya está registrada en otro usuario.',
            'cedula.max' => 'La cédula no puede superar los :max caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.mixed' => 'La contraseña debe incluir mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
            'password.symbols' => 'La contraseña debe incluir al menos un símbolo, por ejemplo: ! @ # $ %.',
        ];
    }

    // Da o quita el permiso para que el estudiante se inscriba en una actividad más
    public function permitirActividad(User $usuario)
    {
        if ($usuario->role !== 'estudiante') {
            return back()->with('error', 'Este permiso solo aplica a estudiantes.');
        }

        $usuario->forceFill(['permitir_nueva_actividad' => ! $usuario->permitir_nueva_actividad])->save();

        return back()->with('success', $usuario->permitir_nueva_actividad
            ? $usuario->name . ' ya puede inscribirse en una actividad más.'
            : 'Se quitó el permiso a ' . $usuario->name . ' para inscribirse en otra actividad.');
    }
}
