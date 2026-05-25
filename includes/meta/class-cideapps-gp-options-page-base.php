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
		add_action( 'admin_init', array( $this, 'register_save_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'wp_redirect', array( $this, 'redirect_with_active_tab' ), 10, 2 );
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
	 * Whether fields grouped by `title` should render as horizontal tabs.
	 *
	 * @since 1.5.0
	 * @return bool
	 */
	protected function uses_tabbed_layout() {
		return false;
	}

	/**
	 * Submit button label.
	 *
	 * @since 1.5.0
	 * @return string
	 */
	protected function get_submit_button_label() {
		return esc_html__( 'Save settings', 'cideapps-gp' );
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

		$this->render_admin_notices();

		echo '<form method="post" action="options.php" class="cagp-options-form">';
		settings_fields( $this->option_name );

		if ( $this->uses_tabbed_layout() ) {
			$this->render_tabbed_fields( $renderer, $values );
		} else {
			$renderer->render_fields_table( $this->get_fields(), $values );
		}

		submit_button( $this->get_submit_button_label() );
		echo '</form></div>';
	}

	/**
	 * Output Settings API notices (success / error).
	 *
	 * @since 1.5.0
	 */
	protected function render_admin_notices() {
		if ( function_exists( 'settings_errors' ) ) {
			settings_errors( $this->option_name );
		}
	}

	/**
	 * Build tab sections from `title` field definitions.
	 *
	 * @since 1.5.0
	 * @return array<string, array<string, mixed>>
	 */
	protected function get_tab_sections() {
		$tabs    = array();
		$current = null;

		foreach ( $this->get_fields() as $field ) {
			$type = isset( $field['type'] ) ? $field['type'] : 'text';

			if ( 'title' === $type ) {
				$tab_id           = $field['id'];
				$current          = $tab_id;
				$tabs[ $tab_id ] = array(
					'id'          => $tab_id,
					'label'       => isset( $field['label'] ) ? $field['label'] : '',
					'description' => isset( $field['description'] ) ? $field['description'] : '',
					'fields'      => array(),
				);
				continue;
			}

			if ( $current ) {
				$tabs[ $current ]['fields'][] = $field;
			}
		}

		return $tabs;
	}

	/**
	 * Resolve active tab id for the options screen.
	 *
	 * @since 1.5.0
	 * @param array<string, array<string, mixed>> $tabs Tab sections.
	 * @return string
	 */
	protected function get_active_tab_id( array $tabs ) {
		$tab_ids = array_keys( $tabs );

		if ( empty( $tab_ids ) ) {
			return '';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- tab is presentational only.
		if ( ! empty( $_GET['tab'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$requested = sanitize_key( wp_unslash( $_GET['tab'] ) );

			if ( in_array( $requested, $tab_ids, true ) ) {
				return $requested;
			}
		}

		$key = $this->get_active_tab_transient_key();
		$tab = get_transient( $key );

		if ( is_string( $tab ) && in_array( $tab, $tab_ids, true ) ) {
			delete_transient( $key );
			return $tab;
		}

		return $tab_ids[0];
	}

	/**
	 * Register admin notice after a successful Settings API save.
	 *
	 * @since 1.5.0
	 */
	public function register_save_notice() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only query arg from core redirect.
		if ( empty( $_GET['settings-updated'] ) || empty( $_GET['page'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( sanitize_key( wp_unslash( $_GET['page'] ) ) !== $this->menu_slug ) {
			return;
		}

		add_settings_error(
			$this->option_name,
			'settings_saved',
			esc_html__( 'Settings saved successfully.', 'cideapps-gp' ),
			'updated'
		);
	}

	/**
	 * Render tab navigation and tab panels.
	 *
	 * @since 1.5.0
	 * @param Cideapps_Gp_Options_Field_Renderer $renderer Field renderer.
	 * @param array<string, mixed>               $values   Option values.
	 */
	protected function render_tabbed_fields( Cideapps_Gp_Options_Field_Renderer $renderer, array $values ) {
		$tabs       = $this->get_tab_sections();
		$active_tab = $this->get_active_tab_id( $tabs );

		if ( empty( $tabs ) ) {
			$renderer->render_fields_table( $this->get_fields(), $values );
			return;
		}

		printf(
			'<input type="hidden" name="cagp_options_active_tab" id="cagp-options-active-tab" value="%s" />',
			esc_attr( $active_tab )
		);

		echo '<nav class="nav-tab-wrapper cagp-options-tabs" aria-label="' . esc_attr__( 'Settings sections', 'cideapps-gp' ) . '">';

		foreach ( $tabs as $tab_id => $tab ) {
			$classes = array( 'nav-tab' );

			if ( $tab_id === $active_tab ) {
				$classes[] = 'nav-tab-active';
			}

			printf(
				'<a href="#%1$s" class="%2$s" data-tab="%1$s" role="tab" aria-selected="%3$s">%4$s</a>',
				esc_attr( $tab_id ),
				esc_attr( implode( ' ', $classes ) ),
				$tab_id === $active_tab ? 'true' : 'false',
				esc_html( $tab['label'] )
			);
		}

		echo '</nav>';

		echo '<div class="cagp-options-tab-panels">';

		foreach ( $tabs as $tab_id => $tab ) {
			$panel_classes = array( 'cagp-options-tab-panel' );

			if ( $tab_id !== $active_tab ) {
				$panel_classes[] = 'cagp-options-tab-panel--hidden';
			}

			printf(
				'<div id="%1$s" class="%2$s" data-tab="%1$s" role="tabpanel">',
				esc_attr( $tab_id ),
				esc_attr( implode( ' ', $panel_classes ) )
			);

			if ( ! empty( $tab['label'] ) ) {
				echo '<h2 class="cagp-options-tab-panel__title">' . esc_html( $tab['label'] ) . '</h2>';
			}

			if ( ! empty( $tab['description'] ) ) {
				printf( '<p class="description cagp-options-tab-panel__desc">%s</p>', esc_html( $tab['description'] ) );
			}

			$renderer->render_fields_table( $tab['fields'], $values );
			echo '</div>';
		}

		echo '</div>';
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

		$this->store_active_tab_from_request();

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
			plugins_url( 'admin/css/cagp-options-admin.css', $plugin_file ),
			array(),
			$version
		);

		if ( $this->uses_tabbed_layout() ) {
			$this->enqueue_tabs_script();
		}

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
	 * Persist active tab on redirect after Settings API save.
	 *
	 * @since 1.5.0
	 * @param string $location Redirect URL.
	 * @param int    $status   HTTP status.
	 * @return string
	 */
	public function redirect_with_active_tab( $location, $status ) {
		unset( $status );

		if ( ! $this->uses_tabbed_layout() || false === strpos( $location, 'page=' . $this->menu_slug ) ) {
			return $location;
		}

		$tab = $this->get_stored_active_tab();

		if ( $tab ) {
			$location = add_query_arg( 'tab', $tab, $location );
		}

		return $location;
	}

	/**
	 * Store active tab from the options form POST.
	 *
	 * @since 1.5.0
	 */
	protected function store_active_tab_from_request() {
		if ( ! $this->uses_tabbed_layout() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Settings API form; capability checked upstream.
		if ( empty( $_POST['cagp_options_active_tab'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$tab = sanitize_key( wp_unslash( $_POST['cagp_options_active_tab'] ) );

		$tabs = $this->get_tab_sections();

		if ( ! isset( $tabs[ $tab ] ) ) {
			return;
		}

		set_transient(
			$this->get_active_tab_transient_key(),
			$tab,
			MINUTE_IN_SECONDS
		);
	}

	/**
	 * Transient key for the active tab after save.
	 *
	 * @since 1.5.0
	 * @return string
	 */
	protected function get_active_tab_transient_key() {
		return 'cagp_options_tab_' . $this->option_name . '_' . get_current_user_id();
	}

	/**
	 * Read and clear stored active tab.
	 *
	 * @since 1.5.0
	 * @return string
	 */
	protected function get_stored_active_tab() {
		$key = $this->get_active_tab_transient_key();
		$tab = get_transient( $key );

		if ( $tab ) {
			delete_transient( $key );
		}

		return is_string( $tab ) ? $tab : '';
	}

	/**
	 * Enqueue tab switching script.
	 *
	 * @since 1.5.0
	 */
	protected function enqueue_tabs_script() {
		$plugin_file = dirname( dirname( dirname( __FILE__ ) ) ) . '/cideapps-gp.php';
		$version     = defined( 'CIDEAPPS_GP_VERSION' ) ? CIDEAPPS_GP_VERSION : '1.0.0';

		wp_enqueue_script(
			'cagp-options-tabs',
			plugins_url( 'admin/js/cagp-options-tabs.js', $plugin_file ),
			array( 'jquery' ),
			$version,
			true
		);
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

		wp_localize_script(
			'cagp-options-map',
			'cagpMapGeocode',
			array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'cagp_reverse_geocode' ),
				'debounceMs' => 500,
				'i18n'       => array(
					'loading' => esc_html__( 'Looking up address…', 'cideapps-gp' ),
					'updated' => esc_html__( 'Address updated from map location. You can edit it before saving.', 'cideapps-gp' ),
					'error'   => esc_html__( 'Could not look up the address.', 'cideapps-gp' ),
				),
			)
		);
	}
}
