( function ( $ ) {
	'use strict';

	const maps = [];
	let geocodeTimer = null;

	function getGeocodeConfig() {
		return typeof window.cagpMapGeocode === 'object' ? window.cagpMapGeocode : null;
	}

	function getAddressField( $field ) {
		const fieldId = $field.data( 'address-field-id' ) || 'business_address';
		return $( '#' + fieldId );
	}

	function getNoticeEl( $field ) {
		return $field.find( '.cagp-map-geocode-notice' );
	}

	function showGeocodeNotice( $field, message, type ) {
		const $notice = getNoticeEl( $field );

		if ( ! message ) {
			$notice.empty().removeClass( 'notice notice-error notice-success is-dismissible' );
			return;
		}

		const noticeClass = 'error' === type ? 'notice-error' : 'notice-success';

		$notice
			.attr( 'class', 'cagp-map-geocode-notice notice ' + noticeClass + ' is-dismissible' )
			.empty()
			.append( $( '<p></p>' ).text( message ) );
	}

	function reverseGeocode( lat, lng, $field ) {
		const config = getGeocodeConfig();
		const $address = getAddressField( $field );

		if ( ! config || ! $address.length ) {
			return;
		}

		$address.prop( 'readonly', true );
		showGeocodeNotice( $field, config.i18n.loading, 'success' );

		$.post( config.ajaxUrl, {
			action: 'cagp_reverse_geocode',
			nonce: config.nonce,
			latitude: lat,
			longitude: lng,
		} )
			.done( function ( response ) {
				if ( response.success && response.data && response.data.address ) {
					$address.val( response.data.address );
					showGeocodeNotice( $field, config.i18n.updated, 'success' );
					return;
				}

				const message =
					response.data && response.data.message
						? response.data.message
						: config.i18n.error || 'Could not look up the address.';

				showGeocodeNotice( $field, message, 'error' );
			} )
			.fail( function ( xhr ) {
				let message = config.i18n.error || 'Could not look up the address.';

				if ( xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message ) {
					message = xhr.responseJSON.data.message;
				}

				showGeocodeNotice( $field, message, 'error' );
			} )
			.always( function () {
				$address.prop( 'readonly', false );
			} );
	}

	function scheduleReverseGeocode( lat, lng, $field ) {
		const config = getGeocodeConfig();
		const delay = config && config.debounceMs ? config.debounceMs : 500;

		window.clearTimeout( geocodeTimer );
		geocodeTimer = window.setTimeout( function () {
			reverseGeocode( lat, lng, $field );
		}, delay );
	}

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

			if ( place.formatted_address ) {
				getAddressField( $field ).val( place.formatted_address );
				showGeocodeNotice( $field, '', '' );
			} else {
				scheduleReverseGeocode(
					place.geometry.location.lat(),
					place.geometry.location.lng(),
					$field
				);
			}
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

		google.maps.event.addListener( marker, 'dragend', function () {
			const position = marker.getPosition();

			$latitude.val( position.lat() );
			$longitude.val( position.lng() );

			scheduleReverseGeocode( position.lat(), position.lng(), $field );
		} );

		maps.push( map );
	}

	$( function () {
		$( '.cagp-map-field' ).each( function () {
			initializeMap( $( this ) );
		} );
	} );
}( jQuery ) );
