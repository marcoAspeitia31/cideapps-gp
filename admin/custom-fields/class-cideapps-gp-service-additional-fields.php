<?php

/**
 * Additional service fields (migrated from theme CMB2).
 *
 * @link       https://github.com/marcoAspeitia31/
 * @since      1.4.0
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */

/**
 * Native metabox for service icon and custom excerpt.
 *
 * Preserves legacy meta keys: cwt_services_image, cwt_services_image_id, cwt_services_excerpt.
 *
 * @since 1.4.0
 * @package Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */
class Cideapps_Gp_Service_Additional_Fields extends Cideapps_Gp_Metabox_Base {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	protected $post_type = 'services';

	/**
	 * Metabox ID (matches former theme CMB2 box id suffix).
	 *
	 * @var string
	 */
	protected $metabox_id = 'cwt_services_metabox';

	/**
	 * Metabox title.
	 *
	 * @since 1.4.0
	 * @return string
	 */
	protected function get_metabox_title() {
		return esc_html__( 'Additional information', 'cideapps-gp' );
	}

	/**
	 * Field definitions.
	 *
	 * @since 1.4.0
	 * @return array<int, array<string, mixed>>
	 */
	protected function get_fields() {
		return array(
			array(
				'id'           => 'cwt_services_image',
				'label'        => esc_html__( 'Main icon', 'cideapps-gp' ),
				'description'  => esc_html__( 'Suggested size 100 x 100 pixels.', 'cideapps-gp' ),
				'type'         => 'file',
				'preview_size' => array( 100, 100 ),
			),
			array(
				'id'          => 'cwt_services_excerpt',
				'label'       => esc_html__( 'Custom excerpt', 'cideapps-gp' ),
				'description' => esc_html__( 'Optional short description for listings.', 'cideapps-gp' ),
				'type'        => 'textarea',
			),
		);
	}
}
