<?php

/**
 * Service custom fields.
 *
 * @link       https://github.com/marcoAspeitia31/
 * @since      1.0.0
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */

/**
 * Native metabox for service pricing and slogan fields.
 *
 * @since 1.0.0
 * @package Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */
class Cideapps_Gp_Service_Fields extends Cideapps_Gp_Metabox_Base {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	protected $post_type = 'services';

	/**
	 * Metabox ID.
	 *
	 * @var string
	 */
	protected $metabox_id = 'service_metabox';

	/**
	 * Metabox title.
	 *
	 * @var string
	 */
	protected $metabox_title = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->metabox_title = esc_html__( 'Services pricing and generic details', 'cideapps-gp' );
	}

	/**
	 * Field definitions.
	 *
	 * @since 1.1.0
	 * @return array<int, array<string, mixed>>
	 */
	protected function get_fields() {
		return array(
			array(
				'id'          => 'service_pricing',
				'label'       => esc_html__( 'Pricing', 'cideapps-gp' ),
				'description' => esc_html__( 'Write the service pricing (e.g. 200)', 'cideapps-gp' ),
				'type'        => 'text_money',
			),
			array(
				'id'          => 'service_slogan',
				'label'       => esc_html__( 'Slogan', 'cideapps-gp' ),
				'description' => esc_html__( 'Write the service slogan (e.g. The best slogan for a service)', 'cideapps-gp' ),
				'type'        => 'text',
			),
		);
	}
}
