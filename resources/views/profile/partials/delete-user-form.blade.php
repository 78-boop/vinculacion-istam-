<section class="ui-panel" style="border-color:#FECACA">
    <div class="ui-panel-head">
        <h3><span class="ui-chip-ico" style="background:#FEE2E2;color:#B91C1C"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg></span> Eliminar cuenta</h3>
    </div>
    <div class="ui-panel-body">
        <p style="margin:0 0 16px;font-size:13.5px;color:var(--ui-texto-2)">
            Al eliminar tu cuenta se borrarán de forma permanente todos tus datos. Esta acción no se puede deshacer.
        </p>

        <form id="form-eliminar-cuenta" method="post" action="{{ route('profile.destroy') }}" data-confirmado="1">
            @csrf
            @method('delete')
            <input type="hidden" name="password" id="eliminar-cuenta-password">
        </form>

        @foreach ($errors->userDeletion->get('password') as $mensaje)
            <p style="margin:0 0 12px;color:#B91C1C;font-size:13px;font-weight:600">{{ $mensaje }}</p>
        @endforeach

        <button type="button" class="ui-btn ui-btn-peligro" id="btn-eliminar-cuenta">Eliminar mi cuenta</button>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('btn-eliminar-cuenta')?.addEventListener('click', async function () {
        const r = await Swal.fire({
            icon: 'warning',
            title: '¿Eliminar tu cuenta?',
            text: 'Se borrarán todos tus datos de forma permanente. Escribe tu contraseña para confirmar.',
            input: 'password',
            inputPlaceholder: 'Tu contraseña',
            inputAttributes: { 'aria-label': 'Tu contraseña', autocomplete: 'current-password' },
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar mi cuenta',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'sa-popup', title: 'sa-title', htmlContainer: 'sa-text', input: 'sa-input', confirmButton: 'sa-btn sa-btn-danger', cancelButton: 'sa-btn sa-btn-cancel', actions: 'sa-actions', validationMessage: 'sa-validation' },
            inputValidator: (v) => !v ? 'Escribe tu contraseña.' : undefined,
        });
        if (!r.isConfirmed) return;
        document.getElementById('eliminar-cuenta-password').value = r.value;
        document.getElementById('form-eliminar-cuenta').submit();
    });
});
</script>
