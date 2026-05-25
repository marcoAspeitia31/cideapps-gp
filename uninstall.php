<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * By default this plugin does NOT delete posts, post meta, term meta, or options.
 * That keeps client sites safe if the plugin is removed temporarily or by mistake.
 *
 * To delete plugin options on uninstall (development/staging only), add to the theme
 * or a mu-plugin:
 *
 *   add_filter( 'cagp_uninstall_delete_data', '__return_true' );
 *
 * @package Cideapps_Gp
 * @link    https://github.com/marcoAspeitia31/cideapps-gp
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Whether to remove plugin options from wp_options on uninstall.
 *
 * @since 1.1.0
 * @param bool $delete Whether to delete data. Default false.
 */
$cagp_delete_data = (bool) apply_filters( 'cagp_uninstall_delete_data', false );

if ( ! $cagp_delete_data ) {
	return;
}

delete_option( 'cagp_theme_options' );
delete_option( 'cagp_settings_options' );
