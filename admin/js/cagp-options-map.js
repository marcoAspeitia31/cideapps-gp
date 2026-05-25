( function ( $ ) {
	'use strict';

	const maps = [];

	function initializeMap( $field ) {
		if ( typeof google === 'undefined' || ! google.maps ) {
			return;
		}

		const $search = $field.find( '.cagp-map-search' );
		const $canvas = $field.find( '.cagp-map-canvas' );
		const $latitude = $field.find( '.cagp-map-latitude' );
		const $longitude = $field.find( '.cagp-map-longitude' );

		let latLng = new google.maps.LatLng( 19.355822065893356, -99.28017934521715 );
		let zoom = 15;

		if ( $latitude.val().length > 0 && $longitude.val().length > 0 ) {
			latLng = new google.maps.LatLng( parseFloat( $latitude.val() ), parseFloat( $longitude.val() ) );
			zoom = 17;
		}

		const map = new google.maps.Map( $canvas[0], {
			center: latLng,
			zoom: zoom,
		} );

		const marker = new google.maps.Marker( {
			map: map,
			draggable: true,
			position: latLng,
		} );

		const autocomplete = new google.maps.places.Autocomplete( $search[0] );
		autocomplete.bindTo( 'bounds', map );

		google.maps.event.addListener( autocomplete, 'place_changed', function () {
			const place = autocomplete.getPlace();

			if ( ! place.geometry ) {
				return;
			}

			if ( place.geometry.viewport ) {
				map.fitBounds( place.geometry.viewport );
			} else {
				map.setCenter( place.geometry.location );
				map.setZoom( 17 );
			}

			marker.setPosition( place.geometry.location );
			$latitude.val( place.geometry.location.lat() );
			$longitude.val( place.geometry.location.lng() );
		} );

		$search.on( 'keypress', function ( event ) {
			if ( 13 === event.keyCode ) {
				event.preventDefault();
			}
		} );

		google.maps.event.addListener( marker, 'drag', function () {
			$latitude.val( marker.getPosition().lat() );
			$longitude.val( marker.getPosition().lng() );
		} );

		maps.push( map );
	}

	$( function () {
		$( '.cagp-map-field' ).each( function () {
			initializeMap( $( this ) );
		} );
	} );
}( jQuery ) );
