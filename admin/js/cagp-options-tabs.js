/**
 * Tabbed layout for Cideapps GP options pages.
 */
( function ( $ ) {
	'use strict';

	function activateTab( tabId ) {
		var $tabs = $( '.cagp-options-tabs .nav-tab' );
		var $panels = $( '.cagp-options-tab-panel' );
		var $hidden = $( '#cagp-options-active-tab' );

		if ( ! tabId || ! $panels.filter( '[data-tab="' + tabId + '"]' ).length ) {
			tabId = $tabs.first().data( 'tab' );
		}

		$tabs.removeClass( 'nav-tab-active' ).attr( 'aria-selected', 'false' );
		$tabs.filter( '[data-tab="' + tabId + '"]' ).addClass( 'nav-tab-active' ).attr( 'aria-selected', 'true' );

		$panels.addClass( 'cagp-options-tab-panel--hidden' );
		$panels.filter( '[data-tab="' + tabId + '"]' ).removeClass( 'cagp-options-tab-panel--hidden' );

		if ( $hidden.length ) {
			$hidden.val( tabId );
		}

		if ( window.history && window.history.replaceState ) {
			var url = new URL( window.location.href );
			url.searchParams.set( 'tab', tabId );
			window.history.replaceState( {}, '', url.toString() );
		}
	}

	$( function () {
		var $wrap = $( '.cagp-options-wrap' );

		if ( ! $wrap.length ) {
			return;
		}

		$wrap.on( 'click', '.cagp-options-tabs .nav-tab', function ( event ) {
			event.preventDefault();
			var tabId = $( this ).data( 'tab' );

			activateTab( tabId );

			// Google Maps must recalculate size when its tab becomes visible.
			if ( tabId === 'contact_title' ) {
				window.setTimeout( function () {
					window.dispatchEvent( new Event( 'resize' ) );
				}, 150 );
			}
		} );
	} );
}( jQuery ) );
