<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/marcoAspeitia31/
 * @since      1.0.0
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin
 * @author     Marco Aspeitia <maspeitiap@devitm.com>
 */
class Cideapps_Gp_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cideapps_Gp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cideapps_Gp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cideapps-gp-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cideapps_Gp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cideapps_Gp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		 wp_enqueue_script( 'jquery-inputmask', plugin_dir_url( __FILE__ ) . 'js/jquery.inputmask.bundle.min.js', array( 'jquery' ), $this->version, false );
		 wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cideapps-gp-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * This function excecutes something after a custom taxonomy is registered
	 * 
	 * @link https://developer.wordpress.org/reference/hooks/registered_taxonomy_taxonomy/
	 * 
	 * @param string $taxonomy Taxonomy key.
	 * @param array|string $object_type Name of the object type for the taxonomy object.
	 * @param array|string $args Optional args used in taxonomy registration. 
	 */
	public function create_uncategorized_term_in_custom_taxonomy( $taxonomy, $object_type, $args ) {

		// check if category(term) exists
        $uncategorized = term_exists('uncategorized', $taxonomy);

        if ( ! $uncategorized ) {
            // if term is not exist, insert it
            $create_uncategorized = wp_insert_term(
                'Uncategorized',
                $taxonomy,
                array(
					'description'	=>  __('This is your default term of your custom category', 'cideapps-gp'),
                    'slug'          =>  'uncategorized',
                )
            );
            
        }
		
	}

	/**
	 * This function excecutes something after a service category taxonomy is registered
	 * 
	 * @link https://developer.wordpress.org/reference/hooks/registered_taxonomy_taxonomy/
	 * 
	 * @param string $taxonomy Taxonomy key.
	 * @param array|string $object_type Name of the object type for the taxonomy object.
	 * @param array|string $args Optional args used in taxonomy registration. 
	 */
	public function create_service_category_default_terms( $taxonomy, $object_type, $args ) {

		$main_services_term = term_exists( 'main-services', $taxonomy );

		if ( ! $main_services_term ) {

			$create_main_services_term = wp_insert_term(
				'Main services',
				$taxonomy,
				array(
					'slug' 		  => 'main-services',
					'description' => __('This term needs to relate the main services that will appear in the website frontend', 'cideapps-gp'),
				)
			);

		}

		$featured_services_term = term_exists( 'featured-services', $taxonomy );

		if ( ! $featured_services_term ) {

			$create_featured_services_term = wp_insert_term( 
				'Featured services',
				$taxonomy,
				array(
					'slug' => 'featured-services',
					'description' => __('This term needs to relate the featured services that will appear in the website frontend', 'cideapps-gp'),
				),
			);

		}

	}

}
