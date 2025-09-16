(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(document).on('ready', () => {
	
		$(":input").inputmask();
		$("#business_main_phone").inputmask( { "mask": "999 999 9999", "show-mask-on-hover": false } );
		$("#whatsapp_phone").inputmask( { "mask": "+99 99 9999 9999" } );
		
  	});

	$(function () {
		const maskOpts = { mask: '999 999 9999', showMaskOnHover: false };

		// Aplica al enfocar (sirve para inputs nuevos también)
		$(document).on('focusin', 'input[id^="business_secondary_phones_"]', function () {
			const $el = $(this);
			if (!$el.data('mask-init')) {
				$el.inputmask(maskOpts);
				$el.data('mask-init', true); // evita re-inicializar
			}
		});

		// Inicializa los que ya existen en el DOM
		$('input[id^="business_secondary_phones_"]').each(function () {
			const $el = $(this);
			if (!$el.data('mask-init')) {
				$el.inputmask(maskOpts);
				$el.data('mask-init', true);
			}
		});
  });


})( jQuery );