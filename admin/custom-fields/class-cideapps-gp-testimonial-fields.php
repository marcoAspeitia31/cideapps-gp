<?php

/**
 * Testimonial custom fields.
 *
 * @link       https://github.com/marcoAspeitia31/
 * @since      1.0.0
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */

/**
 * Native metabox for testimonial fields (Phase 2: image migrated from CMB2).
 *
 * @since 1.0.0
 * @package Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */
class Cideapps_Gp_Testimonial_Fields extends Cideapps_Gp_Metabox_Base {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	protected $post_type = 'testimonials';

	/**
	 * Metabox ID.
	 *
	 * @var string
	 */
	protected $metabox_id = 'testimonials_details_metabox';

	/**
	 * Metabox title.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_metabox_title() {
		return esc_html__( 'Testimonial details', 'cideapps-gp' );
	}

	/**
	 * Field definitions for native metabox.
	 *
	 * @since 1.1.0
	 * @return array<int, array<string, mixed>>
	 */
	protected function get_fields() {
		return array(
			array(
				'id'           => 'testimonials_image',
				'label'        => esc_html__( 'Main image', 'cideapps-gp' ),
				'description'  => esc_html__( 'Suggested size 256 x 256 pixels.', 'cideapps-gp' ),
				'type'         => 'file',
				'preview_size' => array( 150, 150 ),
			),
			array(
				'id'          => 'testimonials_job',
				'label'       => esc_html__( 'Job', 'cideapps-gp' ),
				'description' => esc_html__( 'Write the testimonial job (e.g. Sales manager, Marketing manager)', 'cideapps-gp' ),
				'type'        => 'text',
			),
			array(
				'id'          => 'testimonials_opinion',
				'label'       => esc_html__( 'Opinion', 'cideapps-gp' ),
				'description' => esc_html__( 'Write the testimonial opinion', 'cideapps-gp' ),
				'type'        => 'textarea',
			),
		);
	}
}
