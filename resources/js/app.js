import './bootstrap';
import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es.js';
import 'flatpickr/dist/flatpickr.min.css';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;
window.Swal = Swal;

window.sistemaAlerta = function (icon, title, text = '') {
	return Swal.fire({
		icon,
		title,
		text,
		confirmButtonText: 'Aceptar',
		confirmButtonColor: '#006B47',
		buttonsStyling: true,
	});
};

window.sistemaConfirmar = function (form, title, text) {
	Swal.fire({
		icon: 'warning',
		title,
		text,
		showCancelButton: true,
		confirmButtonText: 'Sí, continuar',
		cancelButtonText: 'Cancelar',
		confirmButtonColor: '#006B47',
		cancelButtonColor: '#6B7280',
		reverseButtons: true,
	}).then((result) => {
		if (result.isConfirmed) form.submit();
	});

	return false;
};

window.alert = function (mensaje) {
	const texto = String(mensaje || '');
	const esError = texto.includes('❌') || texto.toLowerCase().includes('error');
	return window.sistemaAlerta(esError ? 'error' : 'success', esError ? 'No se pudo completar' : 'Operación realizada', texto.replace(/^[✅❌⚠️]\s*/, ''));
};

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
	const flash = document.querySelector('meta[name="sistema-flash"]');
	if (flash?.dataset.icon && flash.dataset.title) {
		window.sistemaAlerta(flash.dataset.icon, flash.dataset.title, flash.dataset.text || '');
	}

	document.querySelectorAll('form[data-confirm-title]').forEach((form) => {
		form.addEventListener('submit', (event) => {
			event.preventDefault();
			window.sistemaConfirmar(form, form.dataset.confirmTitle, form.dataset.confirmText || '');
		});
	});

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
