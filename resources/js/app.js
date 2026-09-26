import './bootstrap';
import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es.js';
import 'flatpickr/dist/flatpickr.min.css';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;

/* =========================================================
   AVISOS DEL SISTEMA (SweetAlert2)
   Todos los mensajes (guardar, editar, eliminar, errores...)
   pasan por aquí: nunca se usan los cuadros del navegador.
   ========================================================= */

// Estilo común de todos los modales del sistema
const Modal = Swal.mixin({
	buttonsStyling: false,
	reverseButtons: true,
	customClass: {
		popup: 'sa-popup',
		title: 'sa-title',
		htmlContainer: 'sa-text',
		confirmButton: 'sa-btn sa-btn-ok',
		cancelButton: 'sa-btn sa-btn-cancel',
		denyButton: 'sa-btn sa-btn-danger',
		input: 'sa-input',
		validationMessage: 'sa-validation',
		actions: 'sa-actions',
	},
	showClass: { popup: 'sa-in' },
	hideClass: { popup: 'sa-out' },
});

// Notificación pequeña en la esquina (para operaciones exitosas)
const Toast = Swal.mixin({
	toast: true,
	position: 'top-end',
	showConfirmButton: false,
	showCloseButton: true,
	timer: 3800,
	timerProgressBar: true,
	customClass: { popup: 'sa-toast', title: 'sa-toast-title', htmlContainer: 'sa-toast-text', closeButton: 'sa-toast-close' },
	didOpen: (toast) => {
		toast.addEventListener('mouseenter', Swal.stopTimer);
		toast.addEventListener('mouseleave', Swal.resumeTimer);
	},
});

window.Swal = Modal;
window.SwalToast = Toast;

const CLAVE_PENDIENTE = 'sistema-aviso-pendiente';

// Guarda un aviso para mostrarlo en la página siguiente (tras reload/redirect)
function guardarPendiente(aviso) {
	try { sessionStorage.setItem(CLAVE_PENDIENTE, JSON.stringify(aviso)); } catch (e) { /* sin storage */ }
}

function tomarPendiente() {
	try {
		const valor = sessionStorage.getItem(CLAVE_PENDIENTE);
		sessionStorage.removeItem(CLAVE_PENDIENTE);
		return valor ? JSON.parse(valor) : null;
	} catch (e) {
		return null;
	}
}

// Muestra un aviso: éxito = toast, error/advertencia = modal
window.sistemaAlerta = function (icon, title, text = '') {
	if (icon === 'success') {
		return Toast.fire({ icon, title, text });
	}
	return Modal.fire({ icon, title, text, confirmButtonText: 'Entendido' });
};

// Aviso que sobrevive a una recarga: úsalo antes de window.location.reload()
window.sistemaAviso = function (icon, title, text = '') {
	guardarPendiente({ icon, title, text });
};

// Confirmación antes de enviar un formulario
window.sistemaConfirmar = function (form, title, text, opciones = {}) {
	const peligro = opciones.peligro ?? esFormularioEliminar(form);

	Modal.fire({
		icon: peligro ? 'warning' : 'question',
		title,
		text,
		showCancelButton: true,
		confirmButtonText: opciones.boton || (peligro ? 'Sí, eliminar' : 'Sí, continuar'),
		cancelButtonText: 'Cancelar',
		customClass: {
			popup: 'sa-popup',
			title: 'sa-title',
			htmlContainer: 'sa-text',
			confirmButton: 'sa-btn ' + (peligro ? 'sa-btn-danger' : 'sa-btn-ok'),
			cancelButton: 'sa-btn sa-btn-cancel',
			actions: 'sa-actions',
		},
	}).then((result) => {
		if (!result.isConfirmed) return;
		form.dataset.confirmado = '1';
		mostrarCargando();
		form.submit();
	});

	return false;
};

// Promesa de confirmación para usar en scripts: if (await sistemaPreguntar(...))
window.sistemaPreguntar = async function (title, text = '', opciones = {}) {
	const r = await Modal.fire({
		icon: opciones.icon || 'question',
		title,
		text,
		showCancelButton: true,
		confirmButtonText: opciones.boton || 'Sí, continuar',
		cancelButtonText: 'Cancelar',
	});
	return r.isConfirmed;
};

// Pide un texto (reemplaza a prompt())
window.sistemaPedirTexto = async function (title, opciones = {}) {
	const minimo = opciones.minimo ?? 0;
	const r = await Modal.fire({
		title,
		text: opciones.texto || '',
		input: 'textarea',
		inputPlaceholder: opciones.placeholder || 'Escribe aquí...',
		inputAttributes: { 'aria-label': title },
		showCancelButton: true,
		confirmButtonText: opciones.boton || 'Enviar',
		cancelButtonText: 'Cancelar',
		inputValidator: (valor) => {
			if (!valor || valor.trim().length < minimo) {
				return minimo > 0 ? `Escribe al menos ${minimo} caracteres.` : 'Este campo es obligatorio.';
			}
			return undefined;
		},
	});
	return r.isConfirmed ? r.value.trim() : null;
};

function mostrarCargando() {
	Modal.fire({
		title: 'Procesando...',
		text: 'Un momento, por favor.',
		allowOutsideClick: false,
		allowEscapeKey: false,
		showConfirmButton: false,
		didOpen: () => Swal.showLoading(),
	});
}

function esFormularioEliminar(form) {
	const metodo = form.querySelector('input[name="_method"]');
	return !!metodo && metodo.value.toUpperCase() === 'DELETE';
}

// alert() del navegador -> aviso del sistema
window.alert = function (mensaje) {
	const texto = String(mensaje ?? '');
	const limpio = texto.replace(/^\s*(✅|❌|⚠️|⚠)\s*/u, '');
	let icon = 'info';
	let title = 'Aviso';

	if (texto.includes('❌') || /error|no se pudo/i.test(texto)) {
		icon = 'error';
		title = 'No se pudo completar';
	} else if (texto.includes('⚠')) {
		icon = 'warning';
		title = 'Atención';
	} else if (texto.includes('✅')) {
		icon = 'success';
		title = 'Operación realizada';
	}

	return window.sistemaAlerta(icon, title, limpio);
};

// Si la página se recarga mientras un aviso está abierto, se muestra en la siguiente
window.addEventListener('beforeunload', () => {
	if (!Swal.isVisible()) return;
	const popup = Swal.getPopup();
	if (!popup) return;
	const icono = ['success', 'error', 'warning', 'info'].find((i) => popup.classList.contains('swal2-icon-' + i));
	const titulo = Swal.getTitle()?.textContent || '';
	if (icono && titulo && titulo !== 'Procesando...') {
		guardarPendiente({ icon: icono, title: titulo, text: Swal.getHtmlContainer()?.textContent || '' });
	}
});

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
	// 1) Avisos enviados por Laravel (session flash / errores de validación)
	const flash = document.querySelector('meta[name="sistema-flash"]');
	const pendiente = tomarPendiente();

	if (flash?.dataset.icon && flash.dataset.title) {
		if (flash.dataset.icon === 'error' && flash.dataset.lista) {
			const errores = JSON.parse(flash.dataset.lista);
			Modal.fire({
				icon: 'error',
				title: flash.dataset.title,
				html: errores.length > 1
					? '<ul class="sa-lista">' + errores.map((e) => `<li>${escapar(e)}</li>`).join('') + '</ul>'
					: escapar(errores[0] || flash.dataset.text || ''),
				confirmButtonText: 'Entendido',
			});
		} else {
			window.sistemaAlerta(flash.dataset.icon, flash.dataset.title, flash.dataset.text || '');
		}
	} else if (pendiente) {
		window.sistemaAlerta(pendiente.icon, pendiente.title, pendiente.text);
	}

	// 2) Confirmación en formularios: los que tienen data-confirm-title
	//    y TODOS los formularios de eliminar (aunque no lo tengan)
	document.addEventListener('submit', (event) => {
		const form = event.target;
		if (!(form instanceof HTMLFormElement) || form.dataset.confirmado === '1') return;

		const titulo = form.dataset.confirmTitle || (esFormularioEliminar(form) ? '¿Eliminar este registro?' : null);
		if (!titulo) return;

		event.preventDefault();
		window.sistemaConfirmar(form, titulo, form.dataset.confirmText || 'Esta acción no se puede deshacer.', {
			boton: form.dataset.confirmButton,
		});
	}, true);

	flatpickr('.actividad-datepicker', {
		locale: Spanish,
		altInput: true,
		altInputClass: 'actividad-datepicker mt-1 block w-full border-gray-300 rounded-md shadow-sm text-base',
		altFormat: 'd/m/Y',
		dateFormat: 'Y-m-d',
		disableMobile: true,
		allowInput: true,
		monthSelectorType: 'static',
	});
});

function escapar(texto) {
	const div = document.createElement('div');
	div.textContent = String(texto ?? '');
	return div.innerHTML;
}
