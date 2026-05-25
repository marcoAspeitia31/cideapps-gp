( function ( $ ) {
	'use strict';

	$( document ).on( 'click', '.cagp-repeatable-add', function ( event ) {
		event.preventDefault();

		const $repeatable = $( this ).closest( '.cagp-repeatable' );
		const fieldName = $( this ).data( 'name' );
		const removeLabel = $repeatable.data( 'remove-label' ) || 'Remove';

		const $row = $(
			'<div class="cagp-repeatable-row">' +
				'<input type="text" class="regular-text" name="' + fieldName + '" value="" />' +
				' <button type="button" class="button cagp-repeatable-remove">' + removeLabel + '</button>' +
			'</div>'
		);

		$repeatable.find( '.cagp-repeatable-rows' ).append( $row );
	} );

	$( document ).on( 'click', '.cagp-repeatable-remove', function ( event ) {
		event.preventDefault();

		const $rows = $( this ).closest( '.cagp-repeatable-rows' ).find( '.cagp-repeatable-row' );

		if ( $rows.length > 1 ) {
			$( this ).closest( '.cagp-repeatable-row' ).remove();
			return;
		}

		$( this ).closest( '.cagp-repeatable-row' ).find( 'input' ).val( '' );
	} );
}( jQuery ) );
