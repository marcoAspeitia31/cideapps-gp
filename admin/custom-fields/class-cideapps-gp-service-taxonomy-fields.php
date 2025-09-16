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
class Cideapps_Gp_Service_Taxonomy_Fields {

    function service_taxonomy_metabox() {

        $prefix = 'service_taxonomy_';

        $service_taxonomy_metabox = new_cmb2_box( array(
            'id'            => $prefix . 'metabox',
            'title'         => esc_html__( 'Service Category Metabox', 'cideapps-gp' ),
            'object_types'  => array( 'term' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
            'show_in_rest'  => WP_REST_Server::ALLMETHODS,
            'taxonomies'    => array( 'service_category' ),
        ) );

        $service_taxonomy_metabox->add_field( array(
            'name' => esc_html__( 'Main image', 'cideapps-gp' ),
            'desc' => esc_html__( 'Suggested size 512 x 512 pixels.', 'cideapps-gp' ),
            'id'   => $prefix . 'image',
            'preview_size' => array( 256, 256 ),
            'type' => 'file',
            'query_args' => array(
                'type' => array(
                    'image/jpg',
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                )
            )
        ) );
        
    }

}