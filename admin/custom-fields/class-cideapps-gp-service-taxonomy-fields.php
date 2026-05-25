<?php

/**
 * Service category taxonomy custom fields.
 *
 * @link       https://github.com/marcoAspeitia31/
 * @since      1.0.0
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */

/**
 * Native term fields for service_category (Phase 3 migration).
 *
 * @since 1.0.0
 * @package Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */
class Cideapps_Gp_Service_Taxonomy_Fields extends Cideapps_Gp_Term_Fields_Base {

	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	protected $taxonomy = 'service_category';

	/**
	 * Field definitions.
	 *
	 * @since 1.3.0
	 * @return array<int, array<string, mixed>>
	 */
	protected function get_fields() {
		return array(
			array(
				'id'           => 'service_taxonomy_image',
				'label'        => esc_html__( 'Main image', 'cideapps-gp' ),
				'description'  => esc_html__( 'Suggested size 512 x 512 pixels.', 'cideapps-gp' ),
				'type'         => 'file',
				'preview_size' => array( 256, 256 ),
			),
		);
	}
}
