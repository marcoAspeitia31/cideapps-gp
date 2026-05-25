<?php

/**
 * Settings options submenu (native Settings API).
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 * @since      1.4.0
 */

/**
 * Plugin settings stored in cagp_settings_options.
 *
 * @since 1.4.0
 */
class Cideapps_Gp_Settings_Options_Page extends Cideapps_Gp_Options_Page_Base {

	/**
	 * wp_options key.
	 *
	 * @var string
	 */
	protected $option_name = 'cagp_settings_options';

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	protected $menu_slug = 'cagp_settings_options';

	/**
	 * Parent menu slug.
	 *
	 * @var string
	 */
	protected $parent_slug = 'cagp_theme_options';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->page_title = esc_html__( 'Settings', 'cideapps-gp' );
		$this->menu_title = esc_html__( 'Settings', 'cideapps-gp' );
	}

	/**
	 * Field definitions.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	protected function get_fields() {
		return array(
			array(
				'id'          => 'settings_title',
				'label'       => esc_html__( 'Addons', 'cideapps-gp' ),
				'description' => esc_html__( 'This section is the responsible to storage some plugins functionality', 'cideapps-gp' ),
				'type'        => 'title',
			),
			array(
				'id'          => 'google_api_key',
				'label'       => esc_html__( 'Google Maps API Key', 'cideapps-gp' ),
				'description' => esc_html__( 'Add a your Google Maps API key', 'cideapps-gp' ),
				'type'        => 'password',
			),
		);
	}
}
