<?php

/**
 * Base Settings API options page.
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/includes/meta
 * @since      1.4.0
 */

/**
 * Registers and renders a plugin options page stored as a single array option.
 *
 * @since 1.4.0
 */
abstract class Cideapps_Gp_Options_Page_Base {

	/**
	 * wp_options key.
	 *
	 * @var string
	 */
	protected $option_name = '';

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	protected $menu_slug = '';

	/**
	 * Page title.
	 *
	 * @var string
	 */
	protected $page_title = '';

	/**
	 * Menu title.
	 *
	 * @var string
	 */
	protected $menu_title = '';

	/**
	 * Parent menu slug for submenu pages.
	 *
	 * @var string
	 */
	protected $parent_slug = '';

	/**
	 * Menu icon URL.
	 *
	 * @var string
	 */
	protected $menu_icon = '';

	/**
	 * Menu position.
	 *
	 * @var int
	 */
	protected $menu_position = null;

	/**
	 * Required capability.
	 *
	 * @var string
	 */
	protected $capability = 'manage_options';

	/**
	 * Register hooks.
	 *
	 * @since 1.4.0
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Field definitions.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	abstract protected function get_fields();

	/**
	 * Page title (translate only when rendering admin UI).
	 *
	 * @since 1.4.1
	 * @return string
	 */
	protected function get_page_title() {
		return $this->page_title;
	}

	/**
	 * Menu title (translate only when rendering admin UI).
	 *
	 * @since 1.4.1
	 * @return string
	 */
	protected function get_menu_title() {
		return $this->menu_title;
	}

	/**
	 * Menu icon URL.
	 *
	 * @since 1.4.1
	 * @return string
	 */
	protected function get_menu_icon() {
		return $this->menu_icon;
	}

	/**
	 * Menu position.
	 *
	 * @since 1.4.1
	 * @return int|null
	 */
	protected function get_menu_position() {
		return $this->menu_position;
	}

	/**
	 * Register admin menu entry.
	 *
	 * @since 1.4.0
	 */
	public function register_menu() {
		if ( $this->parent_slug ) {
			add_submenu_page(
				$this->parent_slug,
				$this->get_page_title(),
				$this->get_menu_title(),
				$this->capability,
				$this->menu_slug,
				array( $this, 'render_page' )
			);
			return;
		}

		add_menu_page(
			$this->get_page_title(),
			$this->get_menu_title(),
			$this->capability,
			$this->menu_slug,
			array( $this, 'render_page' ),
			$this->get_menu_icon(),
			$this->get_menu_position()
		);
	}

	/**
	 * Register settings with Settings API.
	 *
	 * @since 1.4.0
	 */
	public function register_settings() {
		register_setting(
			$this->option_name,
			$this->option_name,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_options' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Render options page.
	 *
	 * @since 1.4.0
	 */
	public function render_page() {
		if ( ! current_user_can( $this->capability ) ) {
			return;
		}

		$values   = get_option( $this->option_name, array() );
		$renderer = new Cideapps_Gp_Options_Field_Renderer( $this->option_name );

		if ( ! is_array( $values ) ) {
			$values = array();
		}

		echo '<div class="wrap cagp-options-wrap">';
		echo '<h1>' . esc_html( $this->get_page_title() ) . '</h1>';
		echo '<form method="post" action="options.php" class="cagp-options-form">';
		settings_fields( $this->option_name );
		echo '<table class="form-table" role="presentation">';

		foreach ( $this->get_fields() as $field ) {
			$renderer->render_field( $field, $values );
		}

		echo '</table>';
		submit_button( esc_html__( 'Save', 'cideapps-gp' ) );
		echo '</form></div>';
	}

	/**
	 * Sanitize submitted option values.
	 *
	 * @since 1.4.0
	 * @param mixed $input Submitted values.
	 * @return array<string, mixed>
	 */
	public function sanitize_options( $input ) {
		if ( ! is_array( $input ) ) {
			return get_option( $this->option_name, array() );
		}

		$output = get_option( $this->option_name, array() );

		if ( ! is_array( $output ) ) {
			$output = array();
		}

		foreach ( $this->get_fields() as $field ) {
			$field_id   = $field['id'];
			$field_type = isset( $field['type'] ) ? $field['type'] : 'text';

			if ( 'title' === $field_type ) {
				continue;
			}

			switch ( $field_type ) {
				case 'file':
					$output = $this->sanitize_file_field( $input, $output, $field_id );
					break;
				case 'repeatable':
					if ( isset( $input[ $field_id ] ) ) {
						$output[ $field_id ] = array_values(
							array_filter(
								array_map( 'sanitize_text_field', (array) $input[ $field_id ] ),
								static function ( $item ) {
									return '' !== $item;
								}
							)
						);
					}
					break;
				case 'map':
					if ( isset( $input[ $field_id ] ) && is_array( $input[ $field_id ] ) ) {
						$output[ $field_id ] = array(
							'latitude'  => isset( $input[ $field_id ]['latitude'] ) ? sanitize_text_field( $input[ $field_id ]['latitude'] ) : '',
							'longitude' => isset( $input[ $field_id ]['longitude'] ) ? sanitize_text_field( $input[ $field_id ]['longitude'] ) : '',
						);
					}
					break;
				case 'wysiwyg':
					if ( isset( $input[ $field_id ] ) ) {
						$output[ $field_id ] = wp_kses_post( $input[ $field_id ] );
					}
					break;
				case 'textarea':
					if ( isset( $input[ $field_id ] ) ) {
						$output[ $field_id ] = sanitize_textarea_field( $input[ $field_id ] );
					}
					break;
				case 'text_email':
					if ( isset( $input[ $field_id ] ) ) {
						$output[ $field_id ] = sanitize_email( $input[ $field_id ] );
					}
					break;
				case 'text_url':
					if ( isset( $input[ $field_id ] ) ) {
						$output[ $field_id ] = esc_url_raw( $input[ $field_id ] );
					}
					break;
				case 'password':
					if ( isset( $input[ $field_id ] ) ) {
						$output[ $field_id ] = sanitize_text_field( $input[ $field_id ] );
					}
					break;
				default:
					if ( isset( $input[ $field_id ] ) ) {
						$output[ $field_id ] = sanitize_text_field( $input[ $field_id ] );
					}
					break;
			}
		}

		return $output;
	}

	/**
	 * Sanitize file field values in options array.
	 *
	 * @param array<string, mixed> $input    Submitted input.
	 * @param array<string, mixed> $output   Output array.
	 * @param string               $field_id Field ID.
	 * @return array<string, mixed>
	 */
	protected function sanitize_file_field( $input, $output, $field_id ) {
		$id_key = $field_id . '_id';

		$url           = isset( $input[ $field_id ] ) ? esc_url_raw( $input[ $field_id ] ) : '';
		$attachment_id = isset( $input[ $id_key ] ) ? absint( $input[ $id_key ] ) : 0;

		if ( $attachment_id ) {
			$attachment_url = wp_get_attachment_url( $attachment_id );

			if ( $attachment_url ) {
				$url = $attachment_url;
			}
		}

		if ( $attachment_id || $url ) {
			$output[ $field_id ] = $url;
			$output[ $id_key ]   = $attachment_id;
		} else {
			unset( $output[ $field_id ], $output[ $id_key ] );
		}

		return $output;
	}

	/**
	 * Enqueue admin assets for this options page.
	 *
	 * @since 1.4.0
	 * @param string $hook Current admin hook.
	 */
	public function enqueue_assets( $hook ) {
		if ( ! $this->is_options_screen( $hook ) ) {
			return;
		}

		$plugin_file = dirname( dirname( dirname( __FILE__ ) ) ) . '/cideapps-gp.php';
		$version     = defined( 'CIDEAPPS_GP_VERSION' ) ? CIDEAPPS_GP_VERSION : '1.0.0';

		wp_enqueue_style(
			'cagp-options-admin',
			plugins_url( 'admin/css/cagp-options-map.css', $plugin_file ),
			array(),
			$version
		);

		if ( $this->has_field_type( 'file' ) ) {
			Cideapps_Gp_File_Field_Assets::enqueue();
		}

		if ( $this->has_field_type( 'repeatable' ) ) {
			$this->enqueue_repeatable_script();
		}

		if ( $this->has_field_type( 'map' ) ) {
			$this->enqueue_map_assets();
		}
	}

	/**
	 * Whether current hook is this options screen.
	 *
	 * @param string $hook Admin hook suffix.
	 * @return bool
	 */
	protected function is_options_screen( $hook ) {
		$screens = array(
			'toplevel_page_' . $this->menu_slug,
			$this->parent_slug . '_page_' . $this->menu_slug,
		);

		return in_array( $hook, $screens, true );
	}

	/**
	 * Check if page defines a field type.
	 *
	 * @param string $type Field type.
	 * @return bool
	 */
	protected function has_field_type( $type ) {
		foreach ( $this->get_fields() as $field ) {
			if ( isset( $field['type'] ) && $type === $field['type'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Enqueue repeatable field script.
	 *
	 * @since 1.4.0
	 */
	protected function enqueue_repeatable_script() {
		$plugin_file = dirname( dirname( dirname( __FILE__ ) ) ) . '/cideapps-gp.php';
		$script_url  = plugins_url( 'admin/js/cagp-options-repeatable.js', $plugin_file );
		$version     = defined( 'CIDEAPPS_GP_VERSION' ) ? CIDEAPPS_GP_VERSION : '1.0.0';

		wp_enqueue_script(
			'cagp-options-repeatable',
			$script_url,
			array( 'jquery' ),
			$version,
			true
		);
	}

	/**
	 * Enqueue map field assets.
	 *
	 * @since 1.4.0
	 */
	protected function enqueue_map_assets() {
		$settings      = get_option( 'cagp_settings_options', array() );
		$google_api_key = is_array( $settings ) && ! empty( $settings['google_api_key'] )
			? sanitize_text_field( $settings['google_api_key'] )
			: '';

		$plugin_file = dirname( dirname( dirname( __FILE__ ) ) ) . '/cideapps-gp.php';
		$version     = defined( 'CIDEAPPS_GP_VERSION' ) ? CIDEAPPS_GP_VERSION : '1.0.0';
		$map_deps    = array( 'jquery' );

		if ( $google_api_key ) {
			wp_enqueue_script(
				'cagp-google-maps-api',
				add_query_arg(
					array(
						'key'       => $google_api_key,
						'libraries' => 'places',
					),
					'https://maps.googleapis.com/maps/api/js'
				),
				array(),
				null,
				true
			);
			$map_deps[] = 'cagp-google-maps-api';
		}

		wp_enqueue_style(
			'cagp-options-map',
			plugins_url( 'admin/css/cagp-options-map.css', $plugin_file ),
			array(),
			$version
		);

		wp_enqueue_script(
			'cagp-options-map',
			plugins_url( 'admin/js/cagp-options-map.js', $plugin_file ),
			$map_deps,
			$version,
			true
		);
	}
}
