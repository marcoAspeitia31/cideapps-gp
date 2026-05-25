<?php

/**
 * Theme options page (native Settings API).
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/admin/custom-fields
 * @since      1.4.0
 */

/**
 * Theme options stored in cagp_theme_options.
 *
 * @since 1.4.0
 */
class Cideapps_Gp_Theme_Options_Page extends Cideapps_Gp_Options_Page_Base {

	/**
	 * wp_options key.
	 *
	 * @var string
	 */
	protected $option_name = 'cagp_theme_options';

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	protected $menu_slug = 'cagp_theme_options';

	/**
	 * Page title.
	 *
	 * @return string
	 */
	protected function get_page_title() {
		return esc_html__( 'Theme options', 'cideapps-gp' );
	}

	/**
	 * Menu title.
	 *
	 * @return string
	 */
	protected function get_menu_title() {
		return esc_html__( 'Theme options', 'cideapps-gp' );
	}

	/**
	 * Menu icon URL.
	 *
	 * @return string
	 */
	protected function get_menu_icon() {
		return plugin_dir_url( dirname( __FILE__ ) ) . 'img/logo-cideapps.png';
	}

	/**
	 * Menu position.
	 *
	 * @return int
	 */
	protected function get_menu_position() {
		return 2;
	}

	/**
	 * Field definitions.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	protected function get_fields() {
		return array(
			array(
				'id'          => 'branding_title',
				'label'       => esc_html__( 'Branding', 'cideapps-gp' ),
				'description' => esc_html__( 'This is the branding section logo and description', 'cideapps-gp' ),
				'type'        => 'title',
			),
			array(
				'id'          => 'menu_logo',
				'label'       => esc_html__( 'Menu logo', 'cideapps-gp' ),
				'description' => esc_html__( 'Upload an image or enter a URL.', 'cideapps-gp' ),
				'type'        => 'file',
			),
			array(
				'id'          => 'footer_logo',
				'label'       => esc_html__( 'Footer logo', 'cideapps-gp' ),
				'description' => esc_html__( 'Upload an image or enter a URL.', 'cideapps-gp' ),
				'type'        => 'file',
			),
			array(
				'id'          => 'about_business',
				'label'       => esc_html__( 'About Business', 'cideapps-gp' ),
				'description' => esc_html__( 'Write something interesting about your business (e.g. Atention schedule, Description, etc.)', 'cideapps-gp' ),
				'type'        => 'wysiwyg',
			),
			array(
				'id'          => 'contact_title',
				'label'       => esc_html__( 'Contact information', 'cideapps-gp' ),
				'description' => esc_html__( 'Section to configure the main contact information', 'cideapps-gp' ),
				'type'        => 'title',
			),
			array(
				'id'          => 'business_main_phone',
				'label'       => esc_html__( 'Business main phone', 'cideapps-gp' ),
				'description' => esc_html__( 'Add your main business phone number (optional)', 'cideapps-gp' ),
				'type'        => 'text',
			),
			array(
				'id'          => 'business_secondary_phones',
				'label'       => esc_html__( 'Business secondary phones', 'cideapps-gp' ),
				'description' => esc_html__( 'Add your secondary business phone numbers (optional)', 'cideapps-gp' ),
				'type'        => 'repeatable',
			),
			array(
				'id'          => 'whatsapp_phone',
				'label'       => esc_html__( 'Whatsapp', 'cideapps-gp' ),
				'description' => esc_html__( 'The Whatsapp number must have the country prefix code (e.g. +52 55 4506 2592)', 'cideapps-gp' ),
				'type'        => 'text',
			),
			array(
				'id'          => 'contact_email',
				'label'       => esc_html__( 'Contact e-mail', 'cideapps-gp' ),
				'description' => esc_html__( 'Add your corporate email (optional)', 'cideapps-gp' ),
				'type'        => 'text_email',
			),
			array(
				'id'          => 'location',
				'label'       => esc_html__( 'Location', 'cideapps-gp' ),
				'description' => esc_html__( 'Drag the marker to set the exact location', 'cideapps-gp' ),
				'type'        => 'map',
			),
			array(
				'id'          => 'business_address',
				'label'       => esc_html__( 'Business address', 'cideapps-gp' ),
				'description' => esc_html__( 'This address will be autocompleted after modifying and saving this page', 'cideapps-gp' ),
				'type'        => 'textarea',
			),
			array(
				'id'          => 'business_working_hours',
				'label'       => esc_html__( 'Business working hours', 'cideapps-gp' ),
				'description' => esc_html__( 'Write your business working hours', 'cideapps-gp' ),
				'type'        => 'repeatable',
			),
			array(
				'id'          => 'main_contact_form_shortcode',
				'label'       => esc_html__( 'Main contact form shortcode', 'cideapps-gp' ),
				'description' => esc_html__( 'Add the main contact form 7 shortcode (e.g. [contact-form-7 id="c7de0b7" title="Contact form 1"] )', 'cideapps-gp' ),
				'type'        => 'text',
			),
			array(
				'id'          => 'main_cta_url',
				'label'       => esc_html__( 'Main CTA URL', 'cideapps-gp' ),
				'description' => esc_html__( 'Add the URL of your main CTA button', 'cideapps-gp' ),
				'type'        => 'text_url',
			),
			array(
				'id'          => 'social_media_title',
				'label'       => esc_html__( 'Social Media', 'cideapps-gp' ),
				'description' => esc_html__( 'Section to add the URL links of your social media profiles', 'cideapps-gp' ),
				'type'        => 'title',
			),
			array(
				'id'          => 'facebook_url',
				'label'       => esc_html__( 'Facebook', 'cideapps-gp' ),
				'description' => esc_html__( 'Add your Facebook profile URL', 'cideapps-gp' ),
				'type'        => 'text_url',
			),
			array(
				'id'          => 'instagram_url',
				'label'       => esc_html__( 'Instagram', 'cideapps-gp' ),
				'description' => esc_html__( 'Add your Instagram profile URL', 'cideapps-gp' ),
				'type'        => 'text_url',
			),
			array(
				'id'          => 'youtube_url',
				'label'       => esc_html__( 'Youtube', 'cideapps-gp' ),
				'description' => esc_html__( 'Add your Youtube profile URL', 'cideapps-gp' ),
				'type'        => 'text_url',
			),
			array(
				'id'          => 'twitter_url',
				'label'       => esc_html__( 'Twitter/X', 'cideapps-gp' ),
				'description' => esc_html__( 'Add your Twitter/X profile URL', 'cideapps-gp' ),
				'type'        => 'text_url',
			),
			array(
				'id'          => 'tiktok_url',
				'label'       => esc_html__( 'Tiktok', 'cideapps-gp' ),
				'description' => esc_html__( 'Add your tiktok profile URL', 'cideapps-gp' ),
				'type'        => 'text_url',
			),
		);
	}
}
