<section class="ui-panel">
    <div class="ui-panel-head">
        <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-8a1 1 0 0 1 1-1Z"/></svg></span> Cambiar contraseña</h3>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="ui-panel-body"
          data-confirm-title="¿Cambiar tu contraseña?" data-confirm-text="La usarás la próxima vez que inicies sesión." data-confirm-button="Sí, cambiar">
        @csrf
        @method('put')

        <div style="display:flex;flex-direction:column;gap:16px">
            <div class="ui-campo">
                <label for="update_password_current_password">Contraseña actual</label>
                <input id="update_password_current_password" name="current_password" type="password" class="ui-input" autocomplete="current-password">
                @foreach ($errors->updatePassword->get('current_password') as $mensaje) <span class="error">{{ $mensaje }}</span> @endforeach
            </div>
            <div class="ui-campo">
                <label for="update_password_password">Nueva contraseña</label>
                <input id="update_password_password" name="password" type="password" class="ui-input" autocomplete="new-password">
                @foreach ($errors->updatePassword->get('password') as $mensaje) <span class="error">{{ $mensaje }}</span> @endforeach
            </div>
            <div class="ui-campo">
                <label for="update_password_password_confirmation">Repite la nueva contraseña</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="ui-input" autocomplete="new-password">
                @foreach ($errors->updatePassword->get('password_confirmation') as $mensaje) <span class="error">{{ $mensaje }}</span> @endforeach
            </div>
        </div>

        <div class="pf-botones">
            <button type="submit" class="ui-btn ui-btn-primario">Actualizar contraseña</button>
        </div>
    </form>
</section>
