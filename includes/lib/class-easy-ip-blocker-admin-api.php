<?php
/**
 * Admin API file.
 *
 * @package Easy_IP_Blocker/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin API class.
 */
class Easy_IP_Blocker_Admin_API {

	/**
	 * Allowed HTML elements and attributes for wp_kses output.
	 *
	 * @var array
	 */
	public $allowed_htmls = array(
		'a'        => array(
			'href'   => array(),
			'id'     => array(),
			'title'  => array(),
			'class'  => array(),
			'target' => array(),
			'rel'    => array(),
		),
		'svg'      => array(
			'class'   => array(),
			'viewBox' => array(),
			'viewbox' => array(),
			'width'   => array(),
			'height'  => array(),
			'fill'    => array(),
			'xmlns'   => array(),
		),
		'path'     => array(
			'd'              => array(),
			'stroke'         => array(),
			'stroke-width'   => array(),
			'stroke-linecap' => array(),
			'fill'           => array(),
		),
		'circle'   => array(
			'cx'           => array(),
			'cy'           => array(),
			'r'            => array(),
			'stroke'       => array(),
			'stroke-width' => array(),
			'fill'         => array(),
		),
		'line'     => array(
			'x1'             => array(),
			'y1'             => array(),
			'x2'             => array(),
			'y2'             => array(),
			'stroke'         => array(),
			'stroke-width'   => array(),
			'stroke-linecap' => array(),
		),
		'h1'       => array(
			'class' => array(),
		),
		'h2'       => array(
			'class' => array(),
		),
		'h3'       => array(
			'class' => array(),
		),
		'h4'       => array(
			'class' => array(),
		),
		'input'    => array(
			'id'                  => array(),
			'type'                => array(),
			'name'                => array(),
			'placeholder'         => array(),
			'value'               => array(),
			'class'               => array(),
			'checked'             => array(),
			'style'               => array(),
			'data-uploader_title' => array(),
			'data-uploader_text'  => array(),
			'tabindex'            => array(),
		),
		'select'   => array(
			'id'          => array(),
			'type'        => array(),
			'name'        => array(),
			'placeholder' => array(),
			'value'       => array(),
			'multiple'    => array(),
			'style'       => array(),
		),
		'option'   => array(
			'id'       => array(),
			'value'    => array(),
			'selected' => array(),
		),
		'label'    => array(
			'for'   => array(),
			'title' => array(),
			'class' => array(),
		),
		'span'     => array(
			'class' => array(),
			'title' => array(),
		),
		'table'    => array(
			'class' => array(),
			'role'  => array(),
		),
		'tbody'    => array(
			'class' => array(),
		),
		'th'       => array(
			'scope' => array(),
		),
		'form'     => array(
			'method'  => array(),
			'action'  => array(),
			'enctype' => array(),
		),
		'div'      => array(
			'class' => array(),
			'id'    => array(),
		),
		'img'      => array(
			'class' => array(),
			'id'    => array(),
			'src'   => array(),
		),
		'textarea' => array(
			'class'       => array(),
			'id'          => array(),
			'rows'        => array(),
			'cols'        => array(),
			'name'        => array(),
			'placeholder' => array(),
			'spellcheck'  => array(),
		),
		'tr'       => array(),
		'td'       => array(),
		'p'        => array(
			'class' => array(),
			'style' => array(),
		),
		'br'       => array(),
		'em'       => array(),
		'strong'   => array(),
		'code'     => array(
			'class' => array(),
		),
		'dl'       => array(
			'class' => array(),
		),
		'dt'       => array(),
		'dd'       => array(),
	);

	/**
	 * Generate HTML for displaying fields.
	 *
	 * @param  array   $data Data array.
	 * @param  object  $post Post object.
	 * @param  boolean $echo Whether to echo the field HTML or return it.
	 * @return string|void
	 */
	public function display_field( $data = array(), $post = null, $echo = true ) {

		if ( isset( $data['field'] ) ) {
			$field = $data['field'];
		} else {
			$field = $data;
		}

		$option_name = '';
		if ( isset( $data['prefix'] ) ) {
			$option_name = $data['prefix'];
		}

		$data = '';
		if ( $post ) {
			$option_name .= $field['id'];
			$option       = get_post_meta( $post->ID, $field['id'], true );

			if ( isset( $option ) ) {
				$data = $option;
			}
		} else {
			$option_name .= $field['id'];
			$option       = get_option( $option_name );

			if ( isset( $option ) ) {
				$data = $option;
			}
		}

		if ( false === $data && isset( $field['default'] ) ) {
			$data = $field['default'];
		} elseif ( false === $data ) {
			$data = '';
		}

		$html = '';

		switch ( $field['type'] ) {

			case 'text':
			case 'url':
			case 'email':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '" />' . "\n";
				break;

			case 'password':
			case 'number':
			case 'hidden':
				$min = '';
				if ( isset( $field['min'] ) ) {
					$min = ' min="' . esc_attr( $field['min'] ) . '"';
				}

				$max = '';
				if ( isset( $field['max'] ) ) {
					$max = ' max="' . esc_attr( $field['max'] ) . '"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '"' . $min . $max . '/>' . "\n";
				break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="" />' . "\n";
				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . esc_textarea( $data ) . '</textarea><br/>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' === $data ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
				break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( in_array( $k, (array) $data, true ) ) {
						$checked = true;
					}
					$html .= '<p><label for="' . esc_attr( $field['id'] . '_' . $k ) . '" class="checkbox_multi"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . esc_html( $v ) . '</label></p> ';
				}
				break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k === $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . esc_html( $v ) . '</label> ';
				}
				break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k === $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . esc_html( $v ) . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( in_array( $k, (array) $data, true ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . esc_html( $v ) . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'image':
				$image_thumb = '';
				if ( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . esc_attr( $option_name ) . '_preview" class="image_preview" src="' . esc_url( $image_thumb ) . '" /><br/>' . "\n";
				$html .= '<input id="' . esc_attr( $option_name ) . '_button" type="button" data-uploader_title="' . esc_attr__( 'Upload an image', 'easy-ip-blocker' ) . '" data-uploader_text="' . esc_attr__( 'Use image', 'easy-ip-blocker' ) . '" class="image_upload_button button" value="' . esc_attr__( 'Upload new image', 'easy-ip-blocker' ) . '" />' . "\n";
				$html .= '<input id="' . esc_attr( $option_name ) . '_delete" type="button" class="image_delete_button button" value="' . esc_attr__( 'Remove image', 'easy-ip-blocker' ) . '" />' . "\n";
				$html .= '<input id="' . esc_attr( $option_name ) . '" class="image_data_field" type="hidden" name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $data ) . '"/><br/>' . "\n";
				break;

			case 'editor':
				wp_editor(
					$data,
					$option_name,
					array(
						'textarea_name' => $option_name,
					)
				);
				break;
		}

		switch ( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . esc_html( $field['description'] ) . '</span>';
				break;

			default:
				if ( ! $post ) {
					$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
				}

				$html .= '<span class="description">' . esc_html( $field['description'] ) . '</span>' . "\n";

				if ( ! $post ) {
					$html .= '</label>' . "\n";
				}
				break;
		}

		if ( ! $echo ) {
			return $html;
		}

		echo wp_kses( $html, $this->allowed_htmls );
	}

	/**
	 * Validate form field.
	 *
	 * @param  string $data Submitted value.
	 * @param  string $type Type of field to validate.
	 * @return string Validated value.
	 */
	public function validate_field( string $data = '', string $type = 'text' ): string {

		switch ( $type ) {
			case 'text':
				$data = esc_attr( $data );
				break;
			case 'url':
				$data = esc_url( $data );
				break;
			case 'email':
				$data = is_email( $data );
				break;
		}

		return $data;
	}
}
