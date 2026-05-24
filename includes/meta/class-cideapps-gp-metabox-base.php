<?php

/**
 * Base class for native WordPress metaboxes.
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/includes/meta
 * @since      1.1.0
 */

/**
 * Provides registration, rendering, and saving for post metaboxes.
 *
 * @since 1.1.0
 */
abstract class Cideapps_Gp_Metabox_Base {

	/**
	 * Post type slug.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Metabox ID.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $metabox_id = '';

	/**
	 * Metabox title.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $metabox_title = '';

	/**
	 * Metabox context.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $context = 'normal';

	/**
	 * Metabox priority.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $priority = 'high';

	/**
	 * Register metabox hooks.
	 *
	 * @since 1.1.0
	 */
	public function register() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_' . $this->post_type, array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Field definitions for this metabox.
	 *
	 * @since 1.1.0
	 * @return array<int, array<string, mixed>>
	 */
	abstract protected function get_fields();

	/**
	 * Register the metabox with WordPress.
	 *
	 * @since 1.1.0
	 */
	public function add_meta_box() {
		add_meta_box(
			$this->metabox_id,
			$this->metabox_title,
			array( $this, 'render' ),
			$this->post_type,
			$this->context,
			$this->priority,
			array(
				'__block_editor_compatible_meta_box' => true,
			)
		);
	}

	/**
	 * Render metabox fields.
	 *
	 * @since 1.1.0
	 * @param WP_Post $post Current post object.
	 */
	public function render( $post ) {
		wp_nonce_field( $this->metabox_id . '_save', $this->metabox_id . '_nonce' );

		echo '<div class="cagp-metabox">';

		foreach ( $this->get_fields() as $field ) {
			$this->render_field( $field, $post->ID );
		}

		echo '</div>';
	}

	/**
	 * Render a single field row.
	 *
	 * @since 1.1.0
	 * @param array<string, mixed> $field   Field configuration.
	 * @param int                  $post_id Post ID.
	 */
	protected function render_field( array $field, $post_id ) {
		$field_id    = $field['id'];
		$field_type  = isset( $field['type'] ) ? $field['type'] : 'text';
		$field_label = isset( $field['label'] ) ? $field['label'] : '';
		$field_desc  = isset( $field['description'] ) ? $field['description'] : '';
		$value       = get_post_meta( $post_id, $field_id, true );

		echo '<p class="cagp-metabox-field cagp-metabox-field--' . esc_attr( $field_type ) . '">';
		echo '<label for="' . esc_attr( $field_id ) . '"><strong>' . esc_html( $field_label ) . '</strong></label><br />';

		switch ( $field_type ) {
			case 'textarea':
				printf(
					'<textarea id="%1$s" name="%1$s" rows="4" class="large-text">%2$s</textarea>',
					esc_attr( $field_id ),
					esc_textarea( $value )
				);
				break;

			case 'text_money':
				echo '<span class="cagp-metabox-currency">$</span> ';
				printf(
					'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text cagp-metabox-money" />',
					esc_attr( $field_id ),
					esc_attr( $value )
				);
				break;

			default:
				printf(
					'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" />',
					esc_attr( $field_id ),
					esc_attr( $value )
				);
				break;
		}

		if ( ! empty( $field_desc ) ) {
			echo '<span class="description">' . esc_html( $field_desc ) . '</span>';
		}

		echo '</p>';
	}

	/**
	 * Save metabox field values.
	 *
	 * @since 1.1.0
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save( $post_id, $post ) {
		unset( $post );

		if ( ! $this->can_save( $post_id ) ) {
			return;
		}

		foreach ( $this->get_fields() as $field ) {
			$field_id = $field['id'];

			if ( ! isset( $_POST[ $field_id ] ) ) {
				continue;
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below per field type.
			$raw_value = wp_unslash( $_POST[ $field_id ] );
			$value     = $this->sanitize_field_value( $field, $raw_value );

			update_post_meta( $post_id, $field_id, $value );
		}
	}

	/**
	 * Determine whether the metabox can be saved.
	 *
	 * @since 1.1.0
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	protected function can_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		if ( ! isset( $_POST[ $this->metabox_id . '_nonce' ] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- verified via wp_verify_nonce.
		if ( ! wp_verify_nonce( wp_unslash( $_POST[ $this->metabox_id . '_nonce' ] ), $this->metabox_id . '_save' ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Sanitize a field value according to its type.
	 *
	 * @since 1.1.0
	 * @param array<string, mixed> $field     Field configuration.
	 * @param mixed                $raw_value Raw submitted value.
	 * @return mixed Sanitized value.
	 */
	protected function sanitize_field_value( array $field, $raw_value ) {
		$field_type = isset( $field['type'] ) ? $field['type'] : 'text';

		switch ( $field_type ) {
			case 'textarea':
				return sanitize_textarea_field( $raw_value );

			case 'text_money':
				return sanitize_text_field( $raw_value );

			default:
				return sanitize_text_field( $raw_value );
		}
	}
}
