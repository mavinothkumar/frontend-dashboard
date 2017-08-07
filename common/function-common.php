<?php

/**
 * Input Fields.
 *
 * @param string $meta_key Meta Key
 * @param array $attr Input Attributes
 * @param string $type Input Format.
 *
 * @return string
 */
function fed_input_box( $meta_key, $attr = array(), $type = 'text' ) {
	$placeholder = isset( $attr['placeholder'] ) ? esc_attr( $attr['placeholder'] ) : '';
	//$class         = isset( $attr['class'] ) ? esc_attr( $attr['class'] ) : '';
	$class         = isset( $attr['class'] ) && $attr['class'] != '' ? 'form-control ' . esc_attr( $attr['class'] ) : 'form-control';
	$name          = isset( $attr['name'] ) ? esc_attr( $attr['name'] ) : $meta_key;
	$value         = isset( $attr['value'] ) ? $attr['value'] : '';
	$required      = isset( $attr['required'] ) && $attr['required'] == 'true' ? 'required="required"' : '';
	$id            = isset( $attr['id'] ) ? $attr['id'] : '';
	$default_value = isset( $attr['default_value'] ) && $attr['default_value'] != '' ? $attr['default_value'] : 'yes';
	$input         = '';
	$disabled      = isset( $attr['disabled'] ) && $attr['disabled'] == 'disabled' ? 'disabled=disabled' : '';
	$readonly      = isset( $attr['readonly'] ) && $attr['readonly'] == 'readonly' ? 'readonly=readonly' : '';

	$extra     = isset( $attr['extra'] ) ? ( $attr['extra'] ) : '';
	$mode      = isset( $attr['extended']['date_mode'] ) && $attr['extended']['date_mode'] != '' ? esc_attr( $attr['extended']['date_mode'] ) : 'range';
	$image_wxh = isset( $attr['image_wxh'] ) && count( $attr['image_wxh'] ) == 2 ? $attr['image_wxh'] : 'thumbnail';
	$content   = isset( $attr['content'] ) ? $attr['content'] : '';

	switch ( $type ) {

		case 'text':
			$input .= '<input ' . $required . ' type="text" name="' . $name . '"  value="' . esc_attr( $value ) . '" class="' . $class . '" placeholder="' . $placeholder . '" id="' . $id . '" ' . $extra . '>';
			break;

		case 'hidden':
			$input .= '<input type="hidden" name="' . $name . '"  value="' . esc_attr( $value ) . '" class="' . $class . '" id="' . $id . '">';
			break;

		case 'readonly':
			$input .= '<input readonly type="text" name="' . $name . '"  value="' . esc_attr( $value ) . '" class="' . $class . '" id="' . $id . '">';
			break;

		case 'email':
			$input .= '<input ' . $required . ' type="email" name="' . $name . '"   value="' . esc_attr( $value ) . '" class="' . $class . '" placeholder="' . $placeholder . '" id="' . $id . '">';
			break;

		case 'password':
			$input .= '<input ' . $required . ' type="password" name="' . $name . '"    class="' . $class . '" placeholder="' . $placeholder . '" id="' . $id . '">';
			break;

		case 'url':
			$input .= '<input ' . $required . ' type="url"  placeholder="' . $placeholder . '"  name=" ' . $name . '"    class="' . $class . '"  id="' . $id . '" value="' . esc_attr( $value ) . '" >';
			break;

		case 'date':
			$input .= '<input ' . $required . ' type="text" name="' . $name . '"  data-date-format="F j, Y h:i K" data-alt-input="true" data-mode="' . $mode . '"    class="flatpickr ' . $class . '"  id="' . $id . '" value="' . esc_attr( $value ) . '">';
			break;

		case 'color':
			if ( $value == '' ) {
				$value = '#000000';
			}
			$input .= '<input ' . $required . ' type="text" name="' . $name . '"    class="jscolor {hash:true} ' . $class . '"  id="' . $id . '"  value="' . esc_attr( $value ) . '" >';
			break;

		case 'file':
			if ( is_array( $image_wxh ) ) {
				$width_height = 'style="width:' . $image_wxh[0] . 'px; height:' . $image_wxh[1] . 'px"';
			} else {
				$width_height = '';
			}
			if ( $value != '' ) {
				$value = (int) $value;
				$img   = wp_get_attachment_image( $value, $image_wxh );
				if ( $img == '' ) {
					$img = '<span class="fed_upload_icon fa-2x fa fa-upload" ></span>';
				}
			} else {
				$value = '';
				$img   = '<span class="fed_upload_icon fa-2x fa fa-upload"></span>';
			}

			$input .= '<div ' . $width_height . ' class="fed_upload_container text-center ' . $class . '" id="' . $id . '">
<div class="fed_upload_image_container">' . $img . '</div>
<input type="hidden" name=" ' . $name . '" class="fed_upload_input" value="' . (int) $value . '"  />
						</div>';
			break;

		case 'textarea':
			$rows = isset( $attr['rows'] ) ? absint( $attr['rows'] ) : 4;

			$input .= '<textarea name="' . $name . '"   rows="' . $rows . '"
			          class="' . $class . '" placeholder="' . $placeholder . '" id="' . $id . '">' . esc_textarea( $value ) . '</textarea>';
			break;

		case 'checkbox':
			$label = isset( $attr['label'] ) ? $attr['label'] : '';
			$class = $class == 'form-control' ? '' : $class;

			$input .= '<div class=""><label class="' . $class . '" for="' . $name . '"><input ' . $disabled . '  class="' . $class . '" name="' . $name . '"  value="' . $default_value . '" type="checkbox"  id="' . $id . '" ' . checked( $value, $default_value, false ) . '/> ' . $label . '</label></div>';

			break;

		case 'select':
			$options = is_array( $attr['options'] ) ? $attr['options'] : array();
			$input   .= '<select name="' . $name . '"  class="' . $class . '" id="' . $id . '">';
			foreach ( $options as $key => $label ) {
				$input .= '<option
						value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . $label . '</option>';
			}
			$input .= '</select>';
			break;

		case 'number':
			$min  = isset( $attr['min'] ) ? $attr['min'] : 0;
			$max  = isset( $attr['max'] ) ? $attr['max'] : '';
			$step = isset( $attr['step'] ) ? $attr['step'] : 'any';

			$input .= '<input ' . $required . ' type="number" name="' . $name . '" 
			                                value="' . esc_attr( $value ) . '" class="' . $class . '"
			                                placeholder="' . $placeholder . '"
			                                min="' . esc_attr( $min ) . '"
			                                max="' . esc_attr( $max ) . '"
			                                step="' . esc_attr( $step ) . '"
			                                id="' . $id . '">';
			break;

		case 'radio':
			$options = is_array( $attr['options'] ) ? $attr['options'] : array();
			$class   = $class == 'form-control' ? '' : $class;
			foreach ( $options as $key => $label ) {
				$input .= '<label class="' . $class . '" for="' . $key . '">
					<input name="' . $name . '"  value="' . $key . '"
					       type="radio"' . checked( $value, $key, false ) . '>
					' . $label . '
				</label>';
			}
			break;

		case 'content':
			$input .= $content;
	}

	return $input;
}

/**
 * Loader.
 *
 * @return string
 */
function fed_loader() {
	return '<div id="preview-area">
        <div class="spinner_circle hide">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
    </div>';
}

/**
 * Sort given array by order key
 */
function fed_sort_by_order( $a, $b ) {
	if ( isset( $a['input_order'], $b['input_order'] ) ) {
		return $a['input_order'] - $b['input_order'];
	} elseif ( isset( $a['menu_order'], $b['menu_order'] ) ) {
		return $a['menu_order'] - $b['menu_order'];
	} else {
		return 199;
	}
}

/**
 * Sort by Desc
 *
 * @param array $a First Element.
 * @param array $b Second Element.
 *
 * @return int
 */
function fed_sort_by_desc( $a, $b ) {
	if ( isset( $a['id'] ) && isset( $b['id'] ) ) {
		return (int) $b['id'] - (int) $a['id'];
	} else {
		return 199;
	}
}
