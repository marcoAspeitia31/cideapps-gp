<?php

/**
 * Registers post and term meta with the WordPress API.
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/includes/meta
 * @since      1.1.0
 */

/**
 * Central registry for custom meta keys exposed to WordPress.
 *
 * @since 1.1.0
 */
class Cideapps_Gp_Meta_Registry {

	/**
	 * Register all meta keys handled by the plugin.
	 *
	 * @since 1.1.0
	 */
	public function register() {
		$this->register_testimonial_meta();
		$this->register_service_meta();
	}

	/**
	 * Register testimonial post meta.
	 *
	 * @since 1.1.0
	 */
	private function register_testimonial_meta() {
		$fields = array(
			'testimonials_job'      => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'testimonials_opinion'  => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_textarea_field',
			),
			'testimonials_image'    => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
			),
			'testimonials_image_id' => array(
				'type'     => 'integer',
				'sanitize' => 'absint',
			),
		);

		foreach ( $fields as $meta_key => $args ) {
			register_post_meta(
				'testimonials',
				$meta_key,
				$this->get_post_meta_args( $args['type'], $args['sanitize'] )
			);
		}
	}

	/**
	 * Register service post meta.
	 *
	 * @since 1.1.0
	 */
	private function register_service_meta() {
		$fields = array(
			'service_pricing' => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'service_slogan'  => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
		);

		foreach ( $fields as $meta_key => $args ) {
			register_post_meta(
				'services',
				$meta_key,
				$this->get_post_meta_args( $args['type'], $args['sanitize'] )
			);
		}
	}

	/**
	 * Build register_post_meta arguments.
	 *
	 * @since 1.1.0
	 * @param string $type Meta value type.
	 * @param string $sanitize Sanitization callback.
	 * @return array<string, mixed>
	 */
	private function get_post_meta_args( $type, $sanitize ) {
		return array(
			'type'              => $type,
			'single'            => true,
			'show_in_rest'      => true,
			'auth_callback'     => array( $this, 'auth_edit_posts' ),
			'sanitize_callback' => $sanitize,
		);
	}

	/**
	 * Auth callback for post meta REST access.
	 *
	 * @since 1.1.0
	 * @param bool   $allowed   Whether the user can add the meta.
	 * @param string $meta_key  Meta key.
	 * @param int    $object_id Post ID.
	 * @return bool
	 */
	public function auth_edit_posts( $allowed, $meta_key, $object_id ) {
		unset( $allowed, $meta_key );

		return current_user_can( 'edit_post', $object_id );
	}
}
