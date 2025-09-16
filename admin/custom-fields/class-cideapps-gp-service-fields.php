<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/marcoAspeitia31/
 * @since      1.0.0
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 * @author     Marco Aspeitia <maspeitiap@devitm.com>
 */
class Cideapps_Gp_Service_Fields {

    function service_metabox() {

        $prefix = 'service_';

        $service_metabox = new_cmb2_box( array(
            'id'            => $prefix . 'metabox',
            'title'         => esc_html__( 'Services pricing and generic details', 'cideapps-gp' ),
            'object_types'  => array( 'services' ),
            'context'    => 'normal',
            'priority'   => 'high',
            'show_names' => true,
            'show_in_rest' => WP_REST_Server::ALLMETHODS,
        ) );

        $service_metabox->add_field( array(
            'name' => esc_html__( 'Pricing', 'cideapps-gp' ),
            'desc' => esc_html__( 'Write the service pricing (e.g. 200)', 'cideapps-gp' ),
            'id'   => $prefix . 'pricing',
            'type' => 'text_money',
            // 'before_field' => '£', // override '$' symbol if needed
        ) );

        $service_metabox->add_field( array(
            'name' => esc_html__( 'Slogan', 'cideapps-gp' ),
            'desc' => esc_html__( 'Write the service slogan (e.g. The best slogan for a service)', 'cideapps-gp' ),
            'id'   => $prefix . 'slogan',
            'type' => 'text',
        ) );
        
    }

}