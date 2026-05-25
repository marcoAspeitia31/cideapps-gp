( function ( $ ) {
	'use strict';

	$( document ).on( 'click', '.cagp-metabox-file-upload', function ( event ) {
		event.preventDefault();

		if ( typeof wp === 'undefined' || ! wp.media ) {
			// eslint-disable-next-line no-alert
			window.alert( 'WordPress media library is not available.' );
			return;
		}

		const fileL10n = window.cagpMetaboxFile || {};
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
}( jQuery ) );
