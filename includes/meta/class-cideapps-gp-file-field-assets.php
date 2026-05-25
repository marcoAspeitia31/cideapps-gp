<?php

/**
 * Enqueues shared assets for native file/image fields.
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/includes/meta
 * @since      1.3.0
 */

/**
 * Admin assets for the WordPress media library file picker.
 *
 * @since 1.3.0
 */
class Cideapps_Gp_File_Field_Assets {

	/**
	 * Enqueue media scripts and the file field handler.
	 *
	 * @since 1.3.0
	 */
	public static function enqueue() {
		wp_enqueue_media();

		$plugin_file = dirname( dirname( dirname( __FILE__ ) ) ) . '/cideapps-gp.php';
		$script_url  = plugins_url( 'admin/js/cagp-metabox-file.js', $plugin_file );
		$version     = defined( 'CIDEAPPS_GP_VERSION' ) ? CIDEAPPS_GP_VERSION : '1.0.0';

		wp_enqueue_script(
			'cagp-metabox-file',
			$script_url,
			array( 'jquery', 'media-upload' ),
			$version,
			true
		);

		wp_localize_script(
			'cagp-metabox-file',
			'cagpMetaboxFile',
			array(
				'frameTitle'  => esc_html__( 'Select or upload image', 'cideapps-gp' ),
				'frameButton' => esc_html__( 'Use this image', 'cideapps-gp' ),
			)
		);
	}
}
