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

		if ( typeof wp !== 'undefined' && wp.media && $( '.cagp-metabox-file' ).length ) {
			const fileL10n = window.cagpMetaboxFile || {};

			$( document ).on( 'click', '.cagp-metabox-file-upload', function ( event ) {
				event.preventDefault();

				const $container = $( this ).closest( '.cagp-metabox-file' );
				const $urlInput = $container.find( '.cagp-metabox-file-url' );
				const $idInput = $container.find( '.cagp-metabox-file-id' );
				const $preview = $container.find( '.cagp-metabox-file-preview' );
				const $remove = $container.find( '.cagp-metabox-file-remove' );

				const frame = wp.media( {
					title: fileL10n.frameTitle || 'Select or upload image',
					button: { text: fileL10n.frameButton || 'Use this image' },
					library: { type: 'image' },
					multiple: false,
				} );

				frame.on( 'select', function () {
					const attachment = frame.state().get( 'selection' ).first().toJSON();
					$urlInput.val( attachment.url );
					$idInput.val( attachment.id );

					const thumb = attachment.sizes && attachment.sizes.thumbnail
						? attachment.sizes.thumbnail.url
						: attachment.url;

					$preview.html( '<img src="' + thumb + '" alt="" />' );
					$remove.removeClass( 'hidden' );
				} );

				frame.open();
			} );

			$( document ).on( 'click', '.cagp-metabox-file-remove', function ( event ) {
				event.preventDefault();

				const $container = $( this ).closest( '.cagp-metabox-file' );

				$container.find( '.cagp-metabox-file-url' ).val( '' );
				$container.find( '.cagp-metabox-file-id' ).val( '' );
				$container.find( '.cagp-metabox-file-preview' ).empty();
				$( this ).addClass( 'hidden' );
			} );
		}
  });


})( jQuery );