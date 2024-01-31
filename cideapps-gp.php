<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/marcoAspeitia31/
 * @since             1.0.0
 * @package           Cideapps_Gp
 *
 * @wordpress-plugin
 * Plugin Name:       Cideapps GP
 * Plugin URI:        https://github.com/marcoAspeitia31/cideapps-gp/
 * Description:       Plugin genérico para iniciar el desarrollo de temas personalizados, incluye CPT de testimoniales, servicios, tambien metaboxes con CMB2 
 * Version:           1.0.0
 * Author:            Marco Aspeitia
 * Author URI:        https://github.com/marcoAspeitia31//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cideapps-gp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CIDEAPPS_GP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cideapps-gp-activator.php
 */
function activate_cideapps_gp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cideapps-gp-activator.php';
	Cideapps_Gp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cideapps-gp-deactivator.php
 */
function deactivate_cideapps_gp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cideapps-gp-deactivator.php';
	Cideapps_Gp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cideapps_gp' );
register_deactivation_hook( __FILE__, 'deactivate_cideapps_gp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cideapps-gp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cideapps_gp() {

	$plugin = new Cideapps_Gp();
	$plugin->run();

}
run_cideapps_gp();
