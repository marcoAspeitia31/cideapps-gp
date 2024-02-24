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
class Cideapps_Gp_Options_Menu_Page_Fields {

    function menu_page_metabox() {

        /**
         * Defining metabox
         */
        $cagp_metabox = new_cmb2_box(
            array(
                'id' => 'cagp_metabox',
                'title' => esc_html__( 'Theme options', 'cideapps-gp' ),
                'object_types' => array( 'options-page' ),
                'option_key' => 'cagp_theme_options',
                'icon_url' => plugin_dir_url( __DIR__ ) . 'img/logo-cideapps.png',
                'menu_title' => esc_html__( 'Theme options', 'cideapps-gp' ),
                'capability' => 'manage_options',
                'position' => 2,
                'priority' => 10,
                'save_button' => esc_html__( 'Save', 'cideapps-gp' ),
            )
        );

        /**
         * Branding Title.
         */
        $cagp_metabox->add_field(
            array(
                'name' => esc_html__( 'Branding', 'cideapps-gp' ),
                'desc' => esc_html__( 'This is the logo branding section', 'cideapps-gp' ),
                'id' => 'branding_title',
                'type' => 'title',
            )
        );

        // Menu logo.
        $cagp_metabox->add_field(
            array(
                'name' => esc_html__( 'Menu logo', 'cideapps-gp' ),
                'desc' => esc_html__( 'Upload an image or enter a URL.', 'cideapps-gp' ),
                'id'   => 'menu_logo',
                'type' => 'file',
                'query_args' => array(
                    'type' => array(
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    )
                ),
            )
        );

        // Footer logo.
        $cagp_metabox->add_field(
            array(
                'name' => esc_html__( 'Footer logo', 'cideapps-gp' ),
                'desc' => esc_html__( 'Upload an image or enter a URL.', 'cideapps-gp' ),
                'id'   => 'footer_logo',
                'type' => 'file',
                'query_args' => array(
                    'type' => array(
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    )
                ),
            )
        );

        /**
         * Contact information.
         */
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Contact information', 'cideapps-gp' ),
            'desc' => esc_html__( 'Section to configure the main contact information', 'cideapps-gp' ),
            'id'   => 'contact_title',
            'type' => 'title',
        ) );

        // Phone.
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Business phone', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your phone number (optional)', 'cideapps-gp' ),
            'id'   => 'business_phone',
            'type' => 'text',
           /*  'attributes' => array(
                'type' => 'number',
            ), */
        ) );

        // Whatsapp.
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Whatsapp', 'cideapps-gp' ),
            'desc' => esc_html__( 'The Whatsapp number must have the country prefix code (e.g. +52 55 4506 2592)', 'cideapps-gp' ),
            'id'   => 'whatsapp_phone',
            'type' => 'text',
        ) );

        // E-mail.
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Contact e-mail', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your corporate email (optional)', 'cideapps-gp' ),
            'id'   => 'contact_email',
            'type' => 'text_email',
        ) );

        // Map
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Location', 'cideapps-gp' ),
            'desc' => esc_html__( 'Drag the marker to set the exact location', 'cideapps-gp' ),
            'id' => 'location',
            'type' => 'pw_map',
            // 'split_values' => true, // Save latitude and longitude as two separate fields
        ) );

        // Business address
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Business address', 'cideapps-gp' ),
            'desc' => esc_html__( 'This address will be autocompleted after modifying and saving this page', 'cideapps-gp' ),
            'id'   => 'business_address',
            'type' => 'textarea_small',
        ) );

        // Contact form shortcode
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Main contact form shortcode', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add the main contact form 7 shortcode (e.g. [contact-form-7 id="c7de0b7" title="Contact form 1"] )', 'cideapps-gp' ),
            'id'   => 'main_contact_form_shortcode',
            'type' => 'text',
        ) );

        /**
         * Social Media Title.
         */
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Social Media', 'cideapps-gp' ),
            'desc' => esc_html__( 'Section to add the URL links of your social media profiles', 'cideapps-gp' ),
            'id'   => 'social_media_title',
            'type' => 'title',
        ) );

        // Facebook.
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Facebook', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your Facebook profile URL', 'cideapps-gp' ),
            'id'   => 'facebook_url',
            'type' => 'text_url',
            'protocols' => array( 'https' ),
        ) );

        // Instagram.
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Instagram', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your Instagram profile URL', 'cideapps-gp' ),
            'id'   => 'instagram_url',
            'type' => 'text_url',
            'protocols' => array( 'https' ),
        ) );

        // Youtube.
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Youtube', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your Youtube profile URL', 'cideapps-gp' ),
            'id'   => 'youtube_url',
            'type' => 'text_url',
            'protocols' => array( 'https' ),
        ) );

        // Twitter/X.
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Twitter/X', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your Twitter/X profile URL', 'cideapps-gp' ),
            'id'   => 'twitter_url',
            'type' => 'text_url',
            'protocols' => array( 'https' ),
        ) );

        // Tiktok.
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Tiktok', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your tiktok profile URL', 'cideapps-gp' ),
            'id'   => 'tiktok_url',
            'type' => 'text_url',
            'protocols' => array( 'https' ),
        ) );

        /**
         * Registers settings submenu options page.
         */
        $cagp_settings_metabox = new_cmb2_box( array(
            'id'           => 'cagp_settings_metabox',
            'title'        => esc_html__( 'Settings', 'cideapps-gp' ),
            'object_types' => array( 'options-page' ),
            'option_key'   => 'cagp_settings_options',
            'parent_slug'  => 'cagp_theme_options',
        ) );

        // Title.
        $cagp_settings_metabox->add_field( array(
            'name' => esc_html__( 'Addons', 'cideapps-gp' ),
            'desc' => esc_html__( 'This section is the responsible to storage some plugins functionality', 'cideapps-gp' ),
            'id'   => 'settings_title',
            'type' => 'title',
        ) );

        //Google API key.
        $cagp_settings_metabox->add_field( array(
            'name' => esc_html__( 'Google Maps API Key', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add a your Google Maps API key', 'cideapps-gp' ),
            'id'   => 'google_api_key',
            'type' => 'text',
            'attributes' => array(
                'type' => 'password',
            ),
        ) );

    }

    function geocode_business_address( $option, $old_value, $value ) {

        if( $option == 'cagp_theme_options' ) {

            $cagp_settings_options = get_option( 'cagp_settings_options' );
            $cagp_theme_options = get_option( $option );

            if( ! empty( $cagp_settings_options['google_api_key'] ) ) {

                $google_api_key = esc_html( $cagp_settings_options['google_api_key'] );

            }

            if( $cagp_theme_options['location'] && isset( $google_api_key ) ) {

                $location = $cagp_theme_options['location'];

                $url_maps = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$location['latitude'].",".$location['longitude']."&sensor=false&key=".$google_api_key;
                $json_data = json_decode( @file_get_contents( $url_maps ) );

                $business_address = array(
                    'business_address' => $json_data->results[0]->formatted_address
                );

                $new_value = array_merge( $cagp_theme_options, $business_address );

                update_option( $option, $new_value );
            }

        }

    }

}