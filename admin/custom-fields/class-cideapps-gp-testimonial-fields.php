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
 * Native metabox for testimonial text fields and CMB2 metabox for the image field (Phase 1 migration).
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

	/**
	 * Register CMB2 metabox for the image field until Phase 2 migration.
	 *
	 * @since 1.0.0
	 */
	public function testimonial_image_metabox() {
		$prefix = 'testimonials_';

		$testimonial_metabox = new_cmb2_box(
			array(
				'id'           => $prefix . 'image_metabox',
				'title'        => esc_html__( 'Testimonial image', 'cideapps-gp' ),
				'object_types' => array( 'testimonials' ),
				'context'      => 'normal',
				'priority'     => 'default',
				'show_names'   => true,
				'show_in_rest' => WP_REST_Server::ALLMETHODS,
			)
		);

		$testimonial_metabox->add_field(
			array(
				'name'         => esc_html__( 'Main image', 'cideapps-gp' ),
				'desc'         => esc_html__( 'Suggested size 256 x 256 pixels.', 'cideapps-gp' ),
				'id'           => $prefix . 'image',
				'preview_size' => array( 150, 150 ),
				'type'         => 'file',
				'query_args'   => array(
					'type' => array(
						'image/jpg',
						'image/jpeg',
						'image/png',
						'image/webp',
					),
				),
			)
		);
	}
}
