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
class Cideapps_Gp_Testimonial_Fields {

    function testimonial_metabox() {

        $prefix = 'testimonials_';

        $testimonial_metabox = new_cmb2_box( array(
            'id'            => $prefix . 'metabox',
            'title'         => esc_html__( 'Testimonial details', 'cideapps-gp' ),
            'object_types'  => array( 'testimonials' ),
            'context'    => 'normal',
            'priority'   => 'high',
            'show_names' => true,
            'show_in_rest' => WP_REST_Server::ALLMETHODS,
        ) );

        $testimonial_metabox->add_field( array(
            'name' => esc_html__( 'Main image', 'cideapps-gp' ),
            'desc' => esc_html__( 'Suggested size 256 x 256 pixels.', 'cideapps-gp' ),
            'id'   => $prefix . 'image',
            'preview_size' => array( 150, 150 ),
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

        $testimonial_metabox->add_field( array(
            'name' => esc_html__( 'Job', 'cideapps-gp' ),
            'desc' => esc_html__( 'Write the testimonial job (e.g. Sales manager, Marketing manager)', 'cideapps-gp' ),
            'id'   => $prefix . 'job',
            'type' => 'text',
        ) );

        $testimonial_metabox->add_field( array(
            'name' => esc_html__( 'Opinion', 'cideapps-gp' ),
            'desc' => esc_html__( 'Write the testimonial opinion', 'cideapps-gp' ),
            'id'   => $prefix . 'opinion',
            'type' => 'textarea_small',
        ) );
        
    }

}