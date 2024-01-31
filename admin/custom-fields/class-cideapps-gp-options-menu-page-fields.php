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
         * Branding.
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
            'attributes' => array(
                'type' => 'number',
            ),
        ) );

        // E-mail.
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Contact e-mail', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your corporate email (optional)', 'cideapps-gp' ),
            'id'   => 'contact_email',
            'type' => 'text_email',
        ) );

        /**
         * Social Media
         */
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Social Media', 'cideapps-gp' ),
            'desc' => esc_html__( 'Section to add the URL links of your social media profiles', 'cideapps-gp' ),
            'id'   => 'social_media_title',
            'type' => 'title',
        ) );

        // Facebook
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Facebook', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your Facebook profile URL', 'cideapps-gp' ),
            'id'   => 'facebook_url',
            'type' => 'text_url',
            'protocols' => array( 'https' ),
        ) );

        // Instagram
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Instagram', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your Instagram profile URL', 'cideapps-gp' ),
            'id'   => 'instagram_url',
            'type' => 'text_url',
            'protocols' => array( 'https' ),
        ) );

        // Youtube
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Youtube', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your Youtube profile URL', 'cideapps-gp' ),
            'id'   => 'youtube_url',
            'type' => 'text_url',
            'protocols' => array( 'https' ),
        ) );

        // Tiktok
        $cagp_metabox->add_field( array(
            'name' => esc_html__( 'Tiktok', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add your tiktok profile URL', 'cideapps-gp' ),
            'id'   => 'tiktok_url',
            'type' => 'text_url',
            'protocols' => array( 'https' ),
        ) );

        /**
         * Registers secondary options page, and set main item as parent.
         */
        $cagp_settings_metabox = new_cmb2_box( array(
            'id'           => 'cagp_settings_metabox',
            'title'        => esc_html__( 'Settings', 'cideapps-gp' ),
            'object_types' => array( 'options-page' ),
            'option_key'   => 'cagp_settings_options',
            'parent_slug'  => 'cagp_theme_options',
        ) );

        //Google API key
        $cagp_settings_metabox->add_field( array(
            'name' => esc_html__( 'Google API Key', 'cideapps-gp' ),
            'desc' => esc_html__( 'Add a your Google Maps API key', 'cideapps-gp' ),
            'id'   => 'google_api_key',
            'type' => 'text',
            'attributes' => array(
                'type' => 'password',
            ),
        ) );

    }

}