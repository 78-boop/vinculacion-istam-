<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('dev_reset_url'))
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm">
            <p class="text-blue-800 font-semibold mb-1">🔧 Modo local: aún no hay correo configurado</p>
            <p class="text-blue-700 mb-2">Usa este link para continuar con la recuperación de tu contraseña:</p>
            <a href="{{ session('dev_reset_url') }}" class="underline text-blue-700 break-all">{{ session('dev_reset_url') }}</a>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
