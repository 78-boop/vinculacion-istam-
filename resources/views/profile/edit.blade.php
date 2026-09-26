@php
    $rolTexto = ['admin' => 'Administrador', 'docente' => 'Docente', 'estudiante' => 'Estudiante'][$user->role] ?? ucfirst($user->role);
    $ini = mb_strtoupper(collect(preg_split('/\s+/', trim($user->name)))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
@endphp

<x-app-layout>
    <style>
        .pf-hero { display: flex; align-items: center; gap: 20px; }
        .pf-foto { width: 92px; height: 92px; border-radius: 28px; flex-shrink: 0; object-fit: cover; border: 4px solid rgba(255,255,255,.35); box-shadow: 0 14px 30px -12px rgba(0,0,0,.5); background: linear-gradient(135deg, var(--ui-lima), #5FB36A); color: var(--ui-verde-noche); display: grid; place-items: center; font-size: 32px; font-weight: 800; }
        .pf-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
        .pf-chips span { font-size: 13px; font-weight: 700; padding: 6px 12px; border-radius: 99px; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.2); }
        .pf-grid { display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr); gap: 20px; align-items: start; }
        .pf-col { display: flex; flex-direction: column; gap: 20px; }
        .pf-botones { display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 18px; border-top: 1px solid var(--ui-borde); }
        @media (max-width: 960px) { .pf-grid { grid-template-columns: 1fr; } }
        @media (max-width: 560px) { .pf-hero { flex-direction: column; align-items: flex-start; } .pf-foto { width: 76px; height: 76px; font-size: 26px; } }
    </style>

    <div class="ui-wrap" style="max-width: 1100px;">
        <section class="ui-hero" style="margin-bottom: 22px;">
            <div class="pf-hero">
                @if ($user->profile_photo_path)
                    <img class="pf-foto" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Foto de {{ $user->name }}">
                @else
                    <span class="pf-foto">{{ $ini }}</span>
                @endif
                <div>
                    <span class="ui-hero-eyebrow">Mi perfil</span>
                    <h1>{{ $user->name }}</h1>
                    <div class="pf-chips">
                        <span>{{ $rolTexto }}</span>
                        <span>{{ $user->email }}</span>
                        @if ($user->cedula) <span>C.I. {{ $user->cedula }}</span> @endif
                        @if ($user->carrera) <span>{{ $user->carrera->nombre }}</span> @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="pf-grid">
            <div class="pf-col">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div class="pf-col">
                @include('profile.partials.update-password-form')
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
