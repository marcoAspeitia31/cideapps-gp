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

/**
 * Coordinates theme options, settings submenu, and geocoding.
 *
 * @since 1.0.0
 * @package Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */
class Cideapps_Gp_Options_Menu_Page_Fields {

	/**
	 * Register options pages and geocoding filter.
	 *
	 * @since 1.4.0
	 */
	public function register() {
		$theme_options = new Cideapps_Gp_Theme_Options_Page();
		$theme_options->register();

		$settings_options = new Cideapps_Gp_Settings_Options_Page();
		$settings_options->register();

		add_filter( 'pre_update_option_cagp_theme_options', array( $this, 'geocode_business_address' ), 10, 2 );
	}

	/**
	 * Reverse geocode location into business_address when theme options are saved.
	 *
	 * @since 1.0.0
	 * @param mixed $value     New option value.
	 * @param mixed $old_value Previous option value.
	 * @return mixed
	 */
	public function geocode_business_address( $value, $old_value ) {
		unset( $old_value );

		if ( ! is_array( $value ) ) {
			return $value;
		}

		$settings = get_option( 'cagp_settings_options', array() );

		if ( ! is_array( $settings ) || empty( $settings['google_api_key'] ) ) {
			return $value;
		}

		if ( empty( $value['location']['latitude'] ) || empty( $value['location']['longitude'] ) ) {
			return $value;
		}

		$google_api_key = sanitize_text_field( $settings['google_api_key'] );
		$latitude       = sanitize_text_field( $value['location']['latitude'] );
		$longitude      = sanitize_text_field( $value['location']['longitude'] );

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
			$this->add_geocode_settings_error(
				sprintf(
					/* translators: %s: error message */
					__( 'Could not update the business address: %s', 'cideapps-gp' ),
					$response->get_error_message()
				)
			);
			return $value;
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

			$this->add_geocode_settings_error(
				sprintf(
					/* translators: 1: HTTP status code, 2: API message */
					__( 'Could not update the business address (HTTP %1$d). %2$s', 'cideapps-gp' ),
					(int) $code,
					$api_message ? $api_message : __( 'Unknown geocoding error.', 'cideapps-gp' )
				)
			);
			return $value;
		}

		if ( ! empty( $body['results'][0]['formatted_address'] ) ) {
			$value['business_address'] = sanitize_text_field( $body['results'][0]['formatted_address'] );
		} elseif ( is_array( $body ) && isset( $body['status'] ) && 'ZERO_RESULTS' !== $body['status'] ) {
			$this->add_geocode_settings_error(
				sprintf(
					/* translators: %s: Google Geocoding API status */
					__( 'Could not update the business address: %s', 'cideapps-gp' ),
					sanitize_text_field( $body['status'] )
				)
			);
		}

		return $value;
	}

	/**
	 * Queue a geocoding error for display on the Theme options screen.
	 *
	 * @since 1.5.0
	 * @param string $message Error message.
	 */
	private function add_geocode_settings_error( $message ) {
		add_settings_error(
			'cagp_theme_options',
			'geocode_error',
			wp_kses_post( $message ),
			'error'
		);
	}
}
