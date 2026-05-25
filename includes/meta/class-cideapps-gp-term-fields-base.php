<?php

/**
 * Base class for native taxonomy term fields.
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/includes/meta
 * @since      1.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers add/edit term UI and save handlers.
 *
 * @since 1.3.0
 */
abstract class Cideapps_Gp_Term_Fields_Base {

	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	protected $taxonomy = '';

	/**
	 * Register term field hooks.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		add_action( "{$this->taxonomy}_add_form_fields", array( $this, 'render_add_form_fields' ) );
		add_action( "{$this->taxonomy}_edit_form_fields", array( $this, 'render_edit_form_fields' ), 10, 2 );
		add_action( "created_{$this->taxonomy}", array( $this, 'save' ) );
		add_action( "edited_{$this->taxonomy}", array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 20 );
	}

	/**
	 * Field definitions for this taxonomy.
	 *
	 * @since 1.3.0
	 * @return array<int, array<string, mixed>>
	 */
	abstract protected function get_fields();

	/**
	 * Render fields on the "Add term" screen.
	 *
	 * @since 1.3.0
	 */
	public function render_add_form_fields() {
		wp_nonce_field( $this->get_nonce_action(), $this->get_nonce_name() );

		foreach ( $this->get_fields() as $field ) {
			$this->render_add_field( $field );
		}
	}

	/**
	 * Render fields on the "Edit term" screen.
	 *
	 * @since 1.3.0
	 * @param WP_Term $term     Current term.
	 * @param string  $taxonomy Taxonomy slug.
	 */
	public function render_edit_form_fields( $term, $taxonomy ) {
		unset( $taxonomy );

		wp_nonce_field( $this->get_nonce_action(), $this->get_nonce_name() );

		foreach ( $this->get_fields() as $field ) {
			$this->render_edit_field( $field, (int) $term->term_id );
		}
	}

	/**
	 * Save term field values.
	 *
	 * @since 1.3.0
	 * @param int $term_id Term ID.
	 */
	public function save( $term_id ) {
		if ( ! $this->can_save( $term_id ) ) {
			return;
		}

		foreach ( $this->get_fields() as $field ) {
			$this->save_field( $term_id, $field );
		}
	}

	/**
	 * Enqueue file field assets on taxonomy admin screens.
	 *
	 * @since 1.3.0
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || $this->taxonomy !== $screen->taxonomy ) {
			return;
		}

		if ( ! $this->has_field_type( 'file' ) ) {
			return;
		}

		Cideapps_Gp_File_Field_Assets::enqueue();
	}

	/**
	 * Render a field row on the add term form.
	 *
	 * @since 1.3.0
	 * @param array<string, mixed> $field Field configuration.
	 */
	protected function render_add_field( array $field ) {
		$field_label = isset( $field['label'] ) ? $field['label'] : '';
		$field_desc  = isset( $field['description'] ) ? $field['description'] : '';

		echo '<div class="form-field cagp-term-field cagp-term-field--add">';

		if ( ! empty( $field_label ) ) {
			printf( '<label for="%s">%s</label>', esc_attr( $field['id'] ), esc_html( $field_label ) );
		}

		$this->render_field_input( $field, 0 );

		if ( ! empty( $field_desc ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field_desc ) );
		}

		echo '</div>';
	}

	/**
	 * Render a field row on the edit term form.
	 *
	 * @since 1.3.0
	 * @param array<string, mixed> $field   Field configuration.
	 * @param int                  $term_id Term ID.
	 */
	protected function render_edit_field( array $field, $term_id ) {
		$field_label = isset( $field['label'] ) ? $field['label'] : '';
		$field_desc  = isset( $field['description'] ) ? $field['description'] : '';

		echo '<tr class="form-field cagp-term-field cagp-term-field--edit">';

		if ( ! empty( $field_label ) ) {
			printf(
				'<th scope="row"><label for="%1$s">%2$s</label></th>',
				esc_attr( $field['id'] ),
				esc_html( $field_label )
			);
		} else {
			echo '<th scope="row"></th>';
		}

		echo '<td>';
		$this->render_field_input( $field, $term_id );

		if ( ! empty( $field_desc ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field_desc ) );
		}

		echo '</td></tr>';
	}

	/**
	 * Render field input markup.
	 *
	 * @since 1.3.0
	 * @param array<string, mixed> $field   Field configuration.
	 * @param int                  $term_id Term ID.
	 */
	protected function render_field_input( array $field, $term_id ) {
		$field_type = isset( $field['type'] ) ? $field['type'] : 'text';

		if ( 'file' === $field_type ) {
			$this->render_file_field( $field, $term_id );
			return;
		}

		$field_id = $field['id'];
		$value    = $term_id ? get_term_meta( $term_id, $field_id, true ) : '';

		printf(
			'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" />',
			esc_attr( $field_id ),
			esc_attr( $value )
		);
	}

	/**
	 * Render a file/image field (CMB2-compatible URL + _id).
	 *
	 * @since 1.3.0
	 * @param array<string, mixed> $field   Field configuration.
	 * @param int                  $term_id Term ID.
	 */
	protected function render_file_field( array $field, $term_id ) {
		$field_id      = $field['id'];
		$id_key        = $field_id . '_id';
		$url           = $term_id ? get_term_meta( $term_id, $field_id, true ) : '';
		$attachment_id = $term_id ? (int) get_term_meta( $term_id, $id_key, true ) : 0;
		$preview_size  = isset( $field['preview_size'] ) ? $field['preview_size'] : array( 150, 150 );
		$has_preview   = $attachment_id || ! empty( $url );
		$field_label   = isset( $field['label'] ) ? $field['label'] : '';
		$preview_class = 'cagp-metabox-file-preview' . ( $has_preview ? ' is-clickable' : '' );

		printf(
			'<div class="cagp-metabox-file" data-field-id="%1$s" data-frame-title="%2$s">',
			esc_attr( $field_id ),
			esc_attr( $field_label )
		);

		echo '<div class="' . esc_attr( $preview_class ) . '"';

		if ( $has_preview ) {
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
	 * Save a single term field.
	 *
	 * @since 1.3.0
	 * @param int                  $term_id Term ID.
	 * @param array<string, mixed> $field   Field configuration.
	 */
	protected function save_field( $term_id, array $field ) {
		// Nonce and capability verified in can_save() before this runs.
		// phpcs:disable WordPress.Security.NonceVerification.Missing

		$field_type = isset( $field['type'] ) ? $field['type'] : 'text';

		if ( 'file' === $field_type ) {
			$this->save_file_field( $term_id, $field );
			// phpcs:enable WordPress.Security.NonceVerification.Missing
			return;
		}

		$field_id = $field['id'];

		if ( ! isset( $_POST[ $field_id ] ) ) {
			// phpcs:enable WordPress.Security.NonceVerification.Missing
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
		$value = sanitize_text_field( wp_unslash( $_POST[ $field_id ] ) );

		update_term_meta( $term_id, $field_id, $value );

		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Save file field term meta (URL + attachment ID).
	 *
	 * @since 1.3.0
	 * @param int                  $term_id Term ID.
	 * @param array<string, mixed> $field   Field configuration.
	 */
	protected function save_file_field( $term_id, array $field ) {
		// Nonce and capability verified in can_save() before this runs.
		// phpcs:disable WordPress.Security.NonceVerification.Missing

		$field_id = $field['id'];
		$id_key   = $field_id . '_id';

		if ( ! isset( $_POST[ $field_id ] ) && ! isset( $_POST[ $id_key ] ) ) {
			// phpcs:enable WordPress.Security.NonceVerification.Missing
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
		$url = isset( $_POST[ $field_id ] ) ? esc_url_raw( wp_unslash( $_POST[ $field_id ] ) ) : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- cast to int below.
		$attachment_id = isset( $_POST[ $id_key ] ) ? absint( wp_unslash( $_POST[ $id_key ] ) ) : 0;

		if ( $attachment_id ) {
			$attachment_url = wp_get_attachment_url( $attachment_id );

			if ( $attachment_url ) {
				$url = $attachment_url;
			}
		}

		if ( $attachment_id || $url ) {
			update_term_meta( $term_id, $field_id, $url );
			update_term_meta( $term_id, $id_key, $attachment_id );
			// phpcs:enable WordPress.Security.NonceVerification.Missing
			return;
		}

		delete_term_meta( $term_id, $field_id );
		delete_term_meta( $term_id, $id_key );

		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Determine whether term fields can be saved.
	 *
	 * @since 1.3.0
	 * @param int $term_id Term ID.
	 * @return bool
	 */
	protected function can_save( $term_id ) {
		unset( $term_id );

		if ( ! isset( $_POST[ $this->get_nonce_name() ] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- verified via wp_verify_nonce.
		if ( ! wp_verify_nonce( wp_unslash( $_POST[ $this->get_nonce_name() ] ), $this->get_nonce_action() ) ) {
			return false;
		}

		$taxonomy_object = get_taxonomy( $this->taxonomy );

		if ( ! $taxonomy_object || ! current_user_can( $taxonomy_object->cap->edit_terms ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check whether this taxonomy defines a given field type.
	 *
	 * @since 1.3.0
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
	 * Nonce action name.
	 *
	 * @since 1.3.0
	 * @return string
	 */
	protected function get_nonce_action() {
		return 'cagp_term_fields_' . $this->taxonomy . '_save';
	}

	/**
	 * Nonce field name.
	 *
	 * @since 1.3.0
	 * @return string
	 */
	protected function get_nonce_name() {
		return 'cagp_term_fields_' . $this->taxonomy . '_nonce';
	}
}
