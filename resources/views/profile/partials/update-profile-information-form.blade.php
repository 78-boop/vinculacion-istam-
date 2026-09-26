<section class="ui-panel">
    <div class="ui-panel-head">
        <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/></svg></span> Información personal</h3>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="ui-panel-body"
          data-confirm-title="¿Guardar los cambios de tu perfil?" data-confirm-button="Sí, guardar"
          x-data="{ foto: @js($user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null) }">
        @csrf
        @method('patch')

        <div class="ui-form-grid">
            <div class="ui-campo completo">
                <label for="name">Nombre completo</label>
                <input id="name" name="name" type="text" class="ui-input @error('name') is-invalido @enderror" value="{{ old('name', $user->name) }}" required autocomplete="name">
                @error('name') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="ui-campo completo">
                <label for="email">Correo electrónico</label>
                <input id="email" name="email" type="email" class="ui-input @error('email') is-invalido @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
                @error('email') <span class="error">{{ $message }}</span> @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <span class="ayuda">
                        Tu correo aún no está verificado.
                        <button form="send-verification" type="submit" style="border:0;background:none;padding:0;color:var(--ui-verde);font-weight:700;cursor:pointer;text-decoration:underline">Reenviar el correo de verificación</button>
                    </span>
                @endif
            </div>

            <div class="ui-campo completo">
                <label for="profile_photo">Foto de perfil</label>
                <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
                    <template x-if="foto"><img :src="foto" alt="" data-sin-zoom style="width:64px;height:64px;border-radius:18px;object-fit:cover;border:1px solid var(--ui-borde)"></template>
                    <label class="ui-btn ui-btn-suave ui-btn-sm" style="position:relative;overflow:hidden;cursor:pointer">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.6-4.6a2 2 0 0 1 2.8 0L16 16m-2-2 1.6-1.6a2 2 0 0 1 2.8 0L20 14M14 8h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                        <span x-text="foto ? 'Cambiar foto' : 'Elegir foto'"></span>
                        <input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp" style="position:absolute;inset:0;opacity:0;cursor:pointer"
                               @change="const f = $event.target.files[0]; if (f) foto = URL.createObjectURL(f)">
                    </label>
                </div>
                <span class="ayuda">JPG, PNG o WEBP. Máximo 2 MB.</span>
                @error('profile_photo') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="pf-botones">
            <button type="submit" class="ui-btn ui-btn-primario">
                <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                Guardar cambios
            </button>
        </div>
    </form>
</section>
