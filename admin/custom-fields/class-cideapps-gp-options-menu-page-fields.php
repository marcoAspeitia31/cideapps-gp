<?php

/**
 * Registers native theme/settings options pages.
 *
 * @link       https://github.com/marcoAspeitia31/
 * @since      1.0.0
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */

defined( 'ABSPATH' ) || exit;

/**
 * Coordinates theme options, settings submenu, and reverse geocoding AJAX.
 *
 * @since 1.0.0
 * @package Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */
class Cideapps_Gp_Options_Menu_Page_Fields {

	/**
	 * Register options pages and geocoding AJAX.
	 *
	 * @since 1.4.0
	 */
	public function register() {
		$theme_options = new Cideapps_Gp_Theme_Options_Page();
		$theme_options->register();

		$settings_options = new Cideapps_Gp_Settings_Options_Page();
		$settings_options->register();

		add_action( 'wp_ajax_cagp_reverse_geocode', array( $this, 'ajax_reverse_geocode' ) );
	}

	/**
	 * AJAX: reverse geocode coordinates into a formatted address.
	 *
	 * @since 1.5.1
	 */
	public function ajax_reverse_geocode() {
		check_ajax_referer( 'cagp_reverse_geocode', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You do not have permission to perform this action.', 'cideapps-gp' ),
				),
				403
			);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.
		$latitude  = isset( $_POST['latitude'] ) ? sanitize_text_field( wp_unslash( $_POST['latitude'] ) ) : '';
		$longitude = isset( $_POST['longitude'] ) ? sanitize_text_field( wp_unslash( $_POST['longitude'] ) ) : '';

		if ( '' === $latitude || '' === $longitude ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Latitude and longitude are required.', 'cideapps-gp' ),
				),
				400
			);
		}

		$result = $this->reverse_geocode_coordinates( $latitude, $longitude );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
				),
				400
			);
		}

		wp_send_json_success(
			array(
				'address' => $result,
			)
		);
	}

	/**
	 * Call Google Geocoding API (reverse) using the stored API key.
	 *
	 * @since 1.5.1
	 * @param string $latitude  Latitude.
	 * @param string $longitude Longitude.
	 * @return string|WP_Error Formatted address or error.
	 */
	private function reverse_geocode_coordinates( $latitude, $longitude ) {
		$settings = get_option( 'cagp_settings_options', array() );

		if ( ! is_array( $settings ) || empty( $settings['google_api_key'] ) ) {
			return new WP_Error(
				'cagp_missing_api_key',
				esc_html__( 'Google Maps API key is not configured in Settings.', 'cideapps-gp' )
			);
		}

		$google_api_key = sanitize_text_field( $settings['google_api_key'] );

		$response = wp_remote_get(
			add_query_arg(
				array(
					'latlng' => $latitude . ',' . $longitude,
					'key'    => $google_api_key,
				),
				'https://maps.googleapis.com/maps/api/geocode/json'
			),
			array(
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'cagp_geocode_request_failed',
				sprintf(
					/* translators: %s: error message */
					__( 'Could not look up the address: %s', 'cideapps-gp' ),
					$response->get_error_message()
				)
			);
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== (int) $code ) {
			$api_message = '';

			if ( is_array( $body ) && ! empty( $body['error_message'] ) ) {
				$api_message = sanitize_text_field( $body['error_message'] );
			} elseif ( is_array( $body ) && ! empty( $body['status'] ) ) {
				$api_message = sanitize_text_field( $body['status'] );
			}

			return new WP_Error(
				'cagp_geocode_http_error',
				sprintf(
					/* translators: 1: HTTP status code, 2: API message */
					__( 'Could not look up the address (HTTP %1$d). %2$s', 'cideapps-gp' ),
					(int) $code,
					$api_message ? $api_message : __( 'Unknown geocoding error.', 'cideapps-gp' )
				)
			);
		}

		if ( ! empty( $body['results'][0]['formatted_address'] ) ) {
			return sanitize_text_field( $body['results'][0]['formatted_address'] );
		}

		if ( is_array( $body ) && isset( $body['status'] ) ) {
			return new WP_Error(
				'cagp_geocode_no_results',
				sprintf(
					/* translators: %s: Google Geocoding API status */
					__( 'Could not look up the address: %s', 'cideapps-gp' ),
					sanitize_text_field( $body['status'] )
				)
			);
		}

		return new WP_Error(
			'cagp_geocode_empty',
			esc_html__( 'No address was found for this location.', 'cideapps-gp' )
		);
	}
}
