( function ( $ ) {
	'use strict';

	const fileL10n = window.cagpMetaboxFile || {};

	/**
	 * Apply selected attachment to the file field container.
	 *
	 * @param {jQuery} $container File field wrapper.
	 * @param {Object} attachment wp.media attachment JSON.
	 */
	function setAttachment( $container, attachment ) {
		const $urlInput = $container.find( '.cagp-metabox-file-url' );
		const $idInput = $container.find( '.cagp-metabox-file-id' );
		const $preview = $container.find( '.cagp-metabox-file-preview' );
		const $remove = $container.find( '.cagp-metabox-file-remove' );

		$urlInput.val( attachment.url );
		$idInput.val( attachment.id );

		const thumb =
			attachment.sizes && attachment.sizes.thumbnail
				? attachment.sizes.thumbnail.url
				: attachment.url;

		$preview
			.addClass( 'is-clickable' )
			.attr( 'role', 'button' )
			.attr( 'tabindex', '0' )
			.empty()
			.append( $( '<img />' ).attr( 'src', thumb ).attr( 'alt', '' ) );

		$remove.removeClass( 'hidden' );
	}

	/**
	 * Open wp.media with optional pre-selection of the current attachment.
	 *
	 * @param {jQuery} $container File field wrapper.
	 */
	function openFileFrame( $container ) {
		if ( typeof wp === 'undefined' || ! wp.media ) {
			// eslint-disable-next-line no-alert
			window.alert( fileL10n.mediaUnavailable || 'WordPress media library is not available.' );
			return;
		}

		const $idInput = $container.find( '.cagp-metabox-file-id' );
		const frameTitle =
			$container.data( 'frameTitle' ) || fileL10n.frameTitle || 'Select or upload image';
		const frameButton = fileL10n.frameButton || 'Use this image';

		const frame = wp.media( {
			title: frameTitle,
			button: { text: frameButton },
			library: { type: 'image' },
			multiple: false,
		} );

		frame.on( 'open', function () {
			const attachmentId = parseInt( $idInput.val(), 10 );

			if ( ! attachmentId ) {
				return;
			}

			const selection = frame.state().get( 'selection' );

			selection.reset();

			const attachment = wp.media.attachment( attachmentId );

			attachment.fetch().then( function () {
				selection.add( attachment );
			} );
		} );

		frame.on( 'select', function () {
			const attachment = frame.state().get( 'selection' ).first().toJSON();
			setAttachment( $container, attachment );
		} );

		frame.open();
	}

	$( document ).on( 'click', '.cagp-metabox-file-upload', function ( event ) {
		event.preventDefault();
		openFileFrame( $( this ).closest( '.cagp-metabox-file' ) );
	} );

	$( document ).on( 'click', '.cagp-metabox-file-preview.is-clickable', function ( event ) {
		event.preventDefault();
		openFileFrame( $( this ).closest( '.cagp-metabox-file' ) );
	} );

	$( document ).on( 'keydown', '.cagp-metabox-file-preview.is-clickable', function ( event ) {
		if ( event.key === 'Enter' || event.key === ' ' ) {
			event.preventDefault();
			openFileFrame( $( this ).closest( '.cagp-metabox-file' ) );
		}
	} );

	$( document ).on( 'click', '.cagp-metabox-file-remove', function ( event ) {
		event.preventDefault();

		const $container = $( this ).closest( '.cagp-metabox-file' );
		const $preview = $container.find( '.cagp-metabox-file-preview' );

		$container.find( '.cagp-metabox-file-url' ).val( '' );
		$container.find( '.cagp-metabox-file-id' ).val( '' );
		$preview
			.empty()
			.removeClass( 'is-clickable' )
			.removeAttr( 'role' )
			.removeAttr( 'tabindex' )
			.removeAttr( 'aria-label' );
		$( this ).addClass( 'hidden' );
	} );
}( jQuery ) );
