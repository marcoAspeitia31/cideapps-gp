<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/marcoAspeitia31/
 * @since      1.0.0
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/includes
 * @author     Marco Aspeitia <maspeitiap@devitm.com>
 */
class Cideapps_Gp {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cideapps_Gp_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CIDEAPPS_GP_VERSION' ) ) {
			$this->version = CIDEAPPS_GP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'cideapps-gp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_testimonial_hooks();
		$this->define_services_hooks();
		$this->define_projects_hooks();
		$this->define_teams_hooks();
		$this->define_options_menu_page_fields_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cideapps_Gp_Loader. Orchestrates the hooks of the plugin.
	 * - Cideapps_Gp_i18n. Defines internationalization functionality.
	 * - Cideapps_Gp_Admin. Defines all hooks for the admin area.
	 * - Cideapps_Gp_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cideapps-gp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cideapps-gp-i18n.php';

		/**
		 * The class responsible for defining metaboxes with cmb2 framework functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cmb2-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cmb_field_map/cmb-field-map.php';

		/**
		 * Register custom taxonomies
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/custom-taxonomies/class-cideapps-gp-taxonomies.php';

		/**
		 * The classes responsible for defining all custom post types
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/custom-post-types/class-cideapps-gp-testimonial-post-type.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/custom-post-types/class-cideapps-gp-services-post-type.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/custom-post-types/class-cideapps-gp-projects-post-type.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/custom-post-types/class-cideapps-gp-teams-post-type.php';

		/**
		 * The classes responsibles for defining custom metaboxes.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/custom-fields/class-cideapps-gp-options-menu-page-fields.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/custom-fields/class-cideapps-gp-testimonial-fields.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cideapps-gp-admin.php';


		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cideapps-gp-public.php';

		$this->loader = new Cideapps_Gp_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cideapps_Gp_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cideapps_Gp_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cideapps_Gp_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'registered_taxonomy_service_category', $plugin_admin, 'create_uncategorized_term_in_custom_taxonomy', 10, 3 );
		$this->loader->add_action( 'registered_taxonomy_service_category', $plugin_admin, 'create_service_category_default_terms', 10, 3 );

		// Custom Taxonomies.
		$plugin_custom_taxonomies = new Cideapps_Gp_Taxonomies();
		$this->loader->add_action( 'init', $plugin_custom_taxonomies, 'service_category', 0 );

	}

	private function define_testimonial_hooks() {

		$plugin_testimonial_post_type = new Cideapps_Gp_Testimonial_Post_Type();

		$this->loader->add_action( 'init', $plugin_testimonial_post_type, 'testimonial_post_type', 0 );

		$plugin_testimonial_fields = new Cideapps_Gp_Testimonial_Fields();

		$this->loader->add_action( 'cmb2_init', $plugin_testimonial_fields, 'testimonial_metabox', 0 );

	}

	private function define_services_hooks() {

		$plugin_services_post_type = new Cideapps_Gp_Services_Post_Type();

		$this->loader->add_action( 'init', $plugin_services_post_type, 'services_post_type', 0 );

	}

	private function define_projects_hooks() {

		$plugin_projects_post_type = new Cideapps_Gp_Projects_Post_Type();

		$this->loader->add_action( 'init', $plugin_projects_post_type, 'projects_post_type', 0 );

	}

	private function define_teams_hooks() {

		$plugin_teams_post_type = new Cideapps_Gp_Teams_Post_Type();

		$this->loader->add_action( 'init', $plugin_teams_post_type, 'teams_post_type', 0 );

	}

	private function define_options_menu_page_fields_hooks() {

		$plugin_options_menu_page_fields = new Cideapps_Gp_Options_Menu_Page_Fields();

		$this->loader->add_action( 'cmb2_admin_init', $plugin_options_menu_page_fields, 'menu_page_metabox', 10, 0 );
		$this->loader->add_action( 'updated_option', $plugin_options_menu_page_fields, 'geocode_business_address', 10, 3 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cideapps_Gp_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cideapps_Gp_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
