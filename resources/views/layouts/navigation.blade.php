@php
    $usuario = Auth::user();
    $iniciales = collect(preg_split('/\s+/', trim($usuario->name)))
        ->filter()
        ->take(2)
        ->map(fn ($parte) => mb_substr($parte, 0, 1))
        ->implode('');
    $iniciales = mb_strtoupper($iniciales);
@endphp

<nav x-data="{ open: false }" class="bg-istam-light border-b-2 border-[#4a3520]">
    <!-- Primary Navigation Menu -->
    <div class="px-3 sm:px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 no-underline">
                        <div class="bg-white rounded-full p-1.5 flex items-center justify-center shrink-0">
                            <img src="{{ asset('images/logo-istam.png') }}" alt="ISTAM" class="h-8 w-8 object-contain">
                        </div>
                        <span class="font-extrabold text-black text-xs sm:text-sm leading-tight uppercase">
                            Entorno Virtual<br>de Aprendizaje
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-3 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Área Personal
                    </x-nav-link>

                    @if (Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.periodos.index')" :active="request()->routeIs('admin.periodos.*')">
                            Períodos
                        </x-nav-link>
                        <x-nav-link :href="route('admin.proyectos.index')" :active="request()->routeIs('admin.proyectos.*')">
                            Proyectos
                        </x-nav-link>
                        <x-nav-link :href="route('admin.inscripciones.index')" :active="request()->routeIs('admin.inscripciones.*')">
                            Inscripciones
                        </x-nav-link>
                        <x-nav-link :href="route('admin.actividades.index')" :active="request()->routeIs('admin.actividades.*')">
                            Actividades
                        </x-nav-link>
                        <x-nav-link :href="route('admin.usuarios.index')" :active="request()->routeIs('admin.usuarios.*')">
                            Usuarios
                        </x-nav-link>
                        <x-nav-link :href="route('admin.certificados.index')" :active="request()->routeIs('admin.certificados.*')">
                            Certificados
                        </x-nav-link>
                        <x-nav-link :href="route('admin.tipos-certificado.index')" :active="request()->routeIs('admin.tipos-certificado.*')">
                            Documentos requeridos
                        </x-nav-link>
                    @elseif (Auth::user()->role === 'docente')
                        <x-nav-link :href="route('docente.proyectos.create')" :active="request()->routeIs('docente.proyectos.*')">
                            Proponer Proyecto
                        </x-nav-link>
                        <x-nav-link :href="route('docente.certificados.index')" :active="request()->routeIs('docente.certificados.*')">
                            Certificados
                        </x-nav-link>
                        <x-nav-link :href="route('docente.actividades-vinculacion.index')" :active="request()->routeIs('docente.actividades-vinculacion.*')">
                            Actividades Vinculación
                        </x-nav-link>
                    @elseif (Auth::user()->role === 'estudiante')
                        <x-nav-link :href="route('certificados-estudiante.index')" :active="request()->routeIs('certificados-estudiante.*')">
                            Certificados
                        </x-nav-link>
                        <x-nav-link :href="route('actividades-vinculacion.index')" :active="request()->routeIs('actividades-vinculacion.*')">
                            Actividades Vinculación
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-800 bg-white hover:text-indigo-700 focus:outline-none transition ease-in-out duration-150">
                            @if($usuario->profile_photo_path)
                                <img src="{{ asset('storage/' . $usuario->profile_photo_path) }}" alt="Foto de {{ $usuario->name }}" style="width: 32px; height: 32px; border-radius: 9999px; object-fit: cover; border: 2px solid #006B47;">
                            @else
                                <span style="display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 9999px; background-color: #006B47; color: #FFFFFF; font-size: 12px; font-weight: 700;">
                                    {{ $iniciales }}
                                </span>
                            @endif

                            <span class="max-w-40 truncate">{{ $usuario->name }}</span>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-green-200 focus:outline-none focus:bg-green-200 focus:text-gray-900 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Área Personal
            </x-responsive-nav-link>

            @if (Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.periodos.index')" :active="request()->routeIs('admin.periodos.*')">
                    Períodos
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.proyectos.index')" :active="request()->routeIs('admin.proyectos.*')">
                    Proyectos
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.inscripciones.index')" :active="request()->routeIs('admin.inscripciones.*')">
                    Inscripciones
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.actividades.index')" :active="request()->routeIs('admin.actividades.*')">
                    Actividades
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.usuarios.index')" :active="request()->routeIs('admin.usuarios.*')">
                    Usuarios
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.certificados.index')" :active="request()->routeIs('admin.certificados.*')">
                    Certificados
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.tipos-certificado.index')" :active="request()->routeIs('admin.tipos-certificado.*')">
                    Documentos requeridos
                </x-responsive-nav-link>
            @elseif (Auth::user()->role === 'docente')
                <x-responsive-nav-link :href="route('docente.proyectos.create')" :active="request()->routeIs('docente.proyectos.*')">
                    Proponer Proyecto
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('docente.certificados.index')" :active="request()->routeIs('docente.certificados.*')">
                    Certificados
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('docente.actividades-vinculacion.index')" :active="request()->routeIs('docente.actividades-vinculacion.*')">
                    Actividades Vinculación
                </x-responsive-nav-link>
            @elseif (Auth::user()->role === 'estudiante')
                <x-responsive-nav-link :href="route('certificados-estudiante.index')" :active="request()->routeIs('certificados-estudiante.*')">
                    Certificados
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('actividades-vinculacion.index')" :active="request()->routeIs('actividades-vinculacion.*')">
                    Actividades Vinculación
                </x-responsive-nav-link>
            @endif

        
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-green-700">
            <div class="flex items-center gap-3 px-4">
                @if($usuario->profile_photo_path)
                    <img src="{{ asset('storage/' . $usuario->profile_photo_path) }}" alt="Foto de {{ $usuario->name }}" style="width: 40px; height: 40px; border-radius: 9999px; object-fit: cover; border: 2px solid #006B47;">
                @else
                    <span style="display: inline-flex; width: 40px; height: 40px; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 9999px; background-color: #006B47; color: #FFFFFF; font-size: 14px; font-weight: 700;">
                        {{ $iniciales }}
                    </span>
                @endif
                <div>
                    <div class="font-medium text-base text-gray-900">{{ $usuario->name }}</div>
                    <div class="font-medium text-sm text-gray-700">{{ $usuario->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>