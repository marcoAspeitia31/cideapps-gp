<?php

/**
 * Base class for native WordPress metaboxes.
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/includes/meta
 * @since      1.1.0
 */

defined( 'ABSPATH' ) || exit;

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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 20 );
	}

	/**
	 * Field definitions for this metabox.
	 *
	 * @since 1.1.0
	 * @return array<int, array<string, mixed>>
	 */
	abstract protected function get_fields();

	/**
	 * Metabox title.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_metabox_title() {
		return $this->metabox_title;
	}

	/**
	 * Register the metabox with WordPress.
	 *
	 * @since 1.1.0
	 */
	public function add_meta_box() {
		add_meta_box(
			$this->metabox_id,
			$this->get_metabox_title(),
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

		if ( 'file' === $field_type ) {
			echo '<div class="cagp-metabox-field cagp-metabox-field--' . esc_attr( $field_type ) . '">';
		} else {
			echo '<p class="cagp-metabox-field cagp-metabox-field--' . esc_attr( $field_type ) . '">';
		}

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

			case 'file':
				$this->render_file_field( $field, $post_id );
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

		if ( 'file' === $field_type ) {
			echo '</div>';
		} else {
			echo '</p>';
		}
	}

	/**
	 * Render a file/image field compatible with CMB2 storage (URL + _id).
	 *
	 * @since 1.2.0
	 * @param array<string, mixed> $field   Field configuration.
	 * @param int                  $post_id Post ID.
	 */
	protected function render_file_field( array $field, $post_id ) {
		$field_id      = $field['id'];
		$id_key        = $field_id . '_id';
		$url           = get_post_meta( $post_id, $field_id, true );
		$attachment_id = (int) get_post_meta( $post_id, $id_key, true );
		$preview_size  = isset( $field['preview_size'] ) ? $field['preview_size'] : array( 150, 150 );
		$has_preview   = $attachment_id || ! empty( $url );
		$field_label   = isset( $field['label'] ) ? $field['label'] : '';

		printf(
			'<div class="cagp-metabox-file" data-field-id="%1$s" data-frame-title="%2$s">',
			esc_attr( $field_id ),
			esc_attr( $field_label )
		);

		echo '<div class="' . esc_attr( $this->get_file_preview_class( $has_preview ) ) . '"';
		$this->render_file_preview_a11y_attrs( $has_preview, $field_label );
		echo '>';
		if ( $attachment_id ) {
			echo wp_get_attachment_image( $attachment_id, $preview_size );
		} elseif ( ! empty( $url ) ) {
			printf( '<img src="%s" alt="" />', esc_url( $url ) );
		}
		echo '</div>';

		printf(
			'<input type="hidden" class="cagp-metabox-file-url" id="%1$s" name="%1$s" value="%2$s" />',
			esc_attr( $field_id ),
			esc_url( $url )
		);
		printf(
			'<input type="hidden" class="cagp-metabox-file-id" id="%1$s" name="%1$s" value="%2$s" />',
			esc_attr( $id_key ),
			esc_attr( $attachment_id ? (string) $attachment_id : '' )
		);

		printf(
			'<p class="cagp-metabox-file-actions"><button type="button" class="button cagp-metabox-file-upload">%s</button> ',
			esc_html__( 'Select image', 'cideapps-gp' )
		);
		printf(
			'<button type="button" class="button cagp-metabox-file-remove%s">%s</button></p>',
			( $attachment_id || $url ) ? '' : ' hidden',
			esc_html__( 'Remove image', 'cideapps-gp' )
		);

		echo '</div>';
	}

	/**
	 * CSS class for the file field preview wrapper.
	 *
	 * @since 1.5.2
	 * @param bool $has_preview Whether a preview image is shown.
	 * @return string
	 */
	protected function get_file_preview_class( $has_preview ) {
		$classes = array( 'cagp-metabox-file-preview' );

		if ( $has_preview ) {
			$classes[] = 'is-clickable';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Accessibility attributes for a clickable file preview.
	 *
	 * @since 1.5.2
	 * @param bool   $has_preview Whether preview is interactive.
	 * @param string $field_label Field label for aria-label.
	 */
	protected function render_file_preview_a11y_attrs( $has_preview, $field_label ) {
		if ( ! $has_preview ) {
			return;
		}

		echo ' role="button" tabindex="0"';

		if ( $field_label ) {
			printf(
				' aria-label="%s"',
				esc_attr(
					sprintf(
						/* translators: %s: field label */
						__( 'Edit %s', 'cideapps-gp' ),
						$field_label
					)
				)
			);
		}
	}

	/**
	 * Enqueue admin assets when the metabox includes file fields.
	 *
	 * @since 1.2.0
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || $this->post_type !== $screen->post_type ) {
			return;
		}

		if ( ! $this->has_field_type( 'file' ) ) {
			return;
		}

		Cideapps_Gp_File_Field_Assets::enqueue();
	}

	/**
	 * Check whether this metabox defines a given field type.
	 *
	 * @since 1.2.0
	 * @param string $type Field type slug.
	 * @return bool
	 */
	protected function has_field_type( $type ) {
		foreach ( $this->get_fields() as $field ) {
			if ( isset( $field['type'] ) && $type === $field['type'] ) {
				return true;
			}
		}

		return false;
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
			$this->save_field( $post_id, $field );
		}
	}

	/**
	 * Save a single metabox field.
	 *
	 * @since 1.2.0
	 * @param int                  $post_id Post ID.
	 * @param array<string, mixed> $field   Field configuration.
	 */
	protected function save_field( $post_id, array $field ) {
		$field_id   = $field['id'];
		$field_type = isset( $field['type'] ) ? $field['type'] : 'text';

		if ( 'file' === $field_type ) {
			$this->save_file_field( $post_id, $field );
			return;
		}

		if ( ! $this->is_set_in_post( $field_id ) ) {
			return;
		}

		$raw_value = $this->get_unslashed_from_post( $field_id );
		if ( null === $raw_value ) {
			return;
		}

		$value = $this->sanitize_field_value( $field, $raw_value );

		update_post_meta( $post_id, $field_id, $value );
	}

	/**
	 * Save file field meta (URL + attachment ID), matching CMB2 format.
	 *
	 * @since 1.2.0
	 * @param int                  $post_id Post ID.
	 * @param array<string, mixed> $field   Field configuration.
	 */
	protected function save_file_field( $post_id, array $field ) {
		$field_id = $field['id'];
		$id_key   = $field_id . '_id';

		if ( ! $this->is_set_in_post( $field_id ) && ! $this->is_set_in_post( $id_key ) ) {
			return;
		}

		$url_raw = $this->get_unslashed_from_post( $field_id );
		$url     = null !== $url_raw ? esc_url_raw( $url_raw ) : '';

		$id_raw = $this->get_unslashed_from_post( $id_key );
		$attachment_id = null !== $id_raw ? absint( $id_raw ) : 0;

		if ( $attachment_id ) {
			$attachment_url = wp_get_attachment_url( $attachment_id );

			if ( $attachment_url ) {
				$url = $attachment_url;
			}
		}

		if ( $attachment_id || $url ) {
			update_post_meta( $post_id, $field_id, $url );
			update_post_meta( $post_id, $id_key, $attachment_id );
			return;
		}

		delete_post_meta( $post_id, $field_id );
		delete_post_meta( $post_id, $id_key );
	}

	/**
	 * Whether a POST key is present (nonce verified in can_save()).
	 *
	 * @since 1.2.0
	 * @param string $key POST key.
	 * @return bool
	 */
	protected function is_set_in_post( $key ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in can_save().
		return isset( $_POST[ $key ] );
	}

	/**
	 * Read an unslashed POST value (nonce verified in can_save()).
	 *
	 * @since 1.2.0
	 * @param string $key POST key.
	 * @return mixed|null Null when the key is not set.
	 */
	protected function get_unslashed_from_post( $key ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in can_save().
		if ( ! isset( $_POST[ $key ] ) ) {
			return null;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified in can_save(); sanitized by callers.
		return wp_unslash( $_POST[ $key ] );
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
