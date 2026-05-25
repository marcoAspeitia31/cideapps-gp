<?php

/**
 * Renders native Settings API option fields.
 *
 * @package    Cideapps_Gp
 * @subpackage Cideapps_Gp/includes/meta
 * @since      1.4.0
 */

/**
 * HTML output for theme/settings option fields.
 *
 * @since 1.4.0
 */
class Cideapps_Gp_Options_Field_Renderer {

	/**
	 * Option name for field name attributes.
	 *
	 * @var string
	 */
	private $option_name;

	/**
	 * Constructor.
	 *
	 * @param string $option_name Option key in wp_options.
	 */
	public function __construct( $option_name ) {
		$this->option_name = $option_name;
	}

	/**
	 * Render multiple fields inside a form table.
	 *
	 * @since 1.5.0
	 * @param array<int, array<string, mixed>> $fields Field definitions.
	 * @param array<string, mixed>             $values Option values.
	 */
	public function render_fields_table( array $fields, array $values ) {
		echo '<table class="form-table" role="presentation">';

		foreach ( $fields as $field ) {
			$this->render_field( $field, $values );
		}

		echo '</table>';
	}

	/**
	 * Render a field row.
	 *
	 * @param array<string, mixed> $field  Field definition.
	 * @param array<string, mixed> $values All option values.
	 */
	public function render_field( array $field, array $values ) {
		$type = isset( $field['type'] ) ? $field['type'] : 'text';

		if ( 'title' === $type ) {
			$this->render_title( $field );
			return;
		}

		echo '<tr class="cagp-options-field cagp-options-field--' . esc_attr( $type ) . '">';
		echo '<th scope="row">';
		if ( ! empty( $field['label'] ) ) {
			printf( '<label for="%s">%s</label>', esc_attr( $field['id'] ), esc_html( $field['label'] ) );
		}
		echo '</th><td>';

		$field_id = $field['id'];
		$value    = isset( $values[ $field_id ] ) ? $values[ $field_id ] : '';

		switch ( $type ) {
			case 'textarea':
				$this->render_textarea( $field_id, $value );
				break;
			case 'wysiwyg':
				$this->render_wysiwyg( $field_id, $value );
				break;
			case 'file':
				$this->render_file( $field, $values );
				break;
			case 'repeatable':
				$this->render_repeatable( $field_id, $value );
				break;
			case 'map':
				$this->render_map( $field_id, is_array( $value ) ? $value : array() );
				break;
			case 'password':
				$this->render_password( $field_id, $value );
				break;
			case 'text_email':
				printf(
					'<input type="email" class="regular-text" id="%1$s" name="%2$s" value="%3$s" />',
					esc_attr( $field_id ),
					esc_attr( $this->field_name( $field_id ) ),
					esc_attr( $value )
				);
				break;
			case 'text_url':
				printf(
					'<input type="url" class="regular-text" id="%1$s" name="%2$s" value="%3$s" />',
					esc_attr( $field_id ),
					esc_attr( $this->field_name( $field_id ) ),
					esc_url( $value )
				);
				break;
			default:
				printf(
					'<input type="text" class="regular-text" id="%1$s" name="%2$s" value="%3$s" />',
					esc_attr( $field_id ),
					esc_attr( $this->field_name( $field_id ) ),
					esc_attr( $value )
				);
				break;
		}

		if ( ! empty( $field['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['description'] ) );
		}

		echo '</td></tr>';
	}

	/**
	 * Render section title row.
	 *
	 * @param array<string, mixed> $field Field definition.
	 */
	private function render_title( array $field ) {
		echo '<tr class="cagp-options-section"><td colspan="2">';
		if ( ! empty( $field['label'] ) ) {
			echo '<h2>' . esc_html( $field['label'] ) . '</h2>';
		}
		if ( ! empty( $field['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['description'] ) );
		}
		echo '</td></tr>';
	}

	/**
	 * Render textarea field.
	 *
	 * @param string $field_id Field ID.
	 * @param mixed  $value    Current value.
	 */
	private function render_textarea( $field_id, $value ) {
		printf(
			'<textarea class="large-text" rows="4" id="%1$s" name="%2$s">%3$s</textarea>',
			esc_attr( $field_id ),
			esc_attr( $this->field_name( $field_id ) ),
			esc_textarea( $value )
		);
	}

	/**
	 * Render WYSIWYG field.
	 *
	 * @param string $field_id Field ID.
	 * @param mixed  $value    Current value.
	 */
	private function render_wysiwyg( $field_id, $value ) {
		wp_editor(
			$value,
			$field_id,
			array(
				'textarea_name' => $this->field_name( $field_id ),
				'textarea_rows' => (int) get_option( 'default_post_edit_rows', 10 ),
				'media_buttons' => true,
				'teeny'         => false,
				'quicktags'     => true,
			)
		);
	}

	/**
	 * Render password field.
	 *
	 * @param string $field_id Field ID.
	 * @param mixed  $value    Current value.
	 */
	private function render_password( $field_id, $value ) {
		printf(
			'<input type="password" class="regular-text" id="%1$s" name="%2$s" value="%3$s" autocomplete="off" />',
			esc_attr( $field_id ),
			esc_attr( $this->field_name( $field_id ) ),
			esc_attr( $value )
		);
	}

	/**
	 * Render file field (CMB2-compatible URL + _id).
	 *
	 * @param array<string, mixed> $field  Field definition.
	 * @param array<string, mixed> $values All option values.
	 */
	private function render_file( array $field, array $values ) {
		$field_id      = $field['id'];
		$id_key        = $field_id . '_id';
		$url           = isset( $values[ $field_id ] ) ? $values[ $field_id ] : '';
		$attachment_id = isset( $values[ $id_key ] ) ? (int) $values[ $id_key ] : 0;

		echo '<div class="cagp-metabox-file" data-field-id="' . esc_attr( $field_id ) . '">';
		echo '<div class="cagp-metabox-file-preview">';
		if ( $attachment_id ) {
			$preview_size = isset( $field['preview_size'] ) ? $field['preview_size'] : 'thumbnail';
			echo wp_get_attachment_image( $attachment_id, $preview_size );
		} elseif ( ! empty( $url ) ) {
			printf( '<img src="%s" alt="" />', esc_url( $url ) );
		}
		echo '</div>';

		printf(
			'<input type="hidden" class="cagp-metabox-file-url" id="%1$s" name="%2$s" value="%3$s" />',
			esc_attr( $field_id ),
			esc_attr( $this->field_name( $field_id ) ),
			esc_url( $url )
		);
		printf(
			'<input type="hidden" class="cagp-metabox-file-id" id="%1$s" name="%2$s" value="%3$s" />',
			esc_attr( $id_key ),
			esc_attr( $this->field_name( $id_key ) ),
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
	 * Render repeatable text fields.
	 *
	 * @param string $field_id Field ID.
	 * @param mixed  $value    Current values.
	 */
	private function render_repeatable( $field_id, $value ) {
		$rows = is_array( $value ) ? $value : array( '' );

		if ( empty( $rows ) ) {
			$rows = array( '' );
		}

		echo '<div class="cagp-repeatable" data-field-id="' . esc_attr( $field_id ) . '">';
		echo '<div class="cagp-repeatable-rows">';

		foreach ( $rows as $row_value ) {
			echo '<div class="cagp-repeatable-row">';
			printf(
				'<input type="text" class="regular-text" name="%s" value="%s" />',
				esc_attr( $this->field_name( $field_id ) . '[]' ),
				esc_attr( $row_value )
			);
			printf(
				' <button type="button" class="button cagp-repeatable-remove">%s</button>',
				esc_html__( 'Remove', 'cideapps-gp' )
			);
			echo '</div>';
		}

		echo '</div>';
		printf(
			'<p><button type="button" class="button cagp-repeatable-add" data-name="%s">%s</button></p>',
			esc_attr( $this->field_name( $field_id ) . '[]' ),
			esc_html__( 'Add', 'cideapps-gp' )
		);
		echo '</div>';
	}

	/**
	 * Render Google Maps location field.
	 *
	 * @param string               $field_id Field ID.
	 * @param array<string, mixed> $location Location data.
	 */
	private function render_map( $field_id, array $location ) {
		$latitude  = isset( $location['latitude'] ) ? $location['latitude'] : '';
		$longitude = isset( $location['longitude'] ) ? $location['longitude'] : '';

		echo '<div class="cagp-map-field" data-field-id="' . esc_attr( $field_id ) . '">';
		printf(
			'<input type="text" class="large-text cagp-map-search" placeholder="%s" />',
			esc_attr__( 'Search for an address', 'cideapps-gp' )
		);
		echo '<div class="cagp-map-canvas"></div>';
		printf(
			'<input type="hidden" class="cagp-map-latitude" name="%s" value="%s" />',
			esc_attr( $this->field_name( $field_id ) . '[latitude]' ),
			esc_attr( $latitude )
		);
		printf(
			'<input type="hidden" class="cagp-map-longitude" name="%s" value="%s" />',
			esc_attr( $this->field_name( $field_id ) . '[longitude]' ),
			esc_attr( $longitude )
		);
		echo '</div>';
	}

	/**
	 * Build input name for option array.
	 *
	 * @param string $field_id Field ID.
	 * @return string
	 */
	private function field_name( $field_id ) {
		return $this->option_name . '[' . $field_id . ']';
	}
}
