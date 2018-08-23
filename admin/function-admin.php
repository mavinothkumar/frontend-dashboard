<?php

/**
 * Common functions
 */

/**
 * Verify Nonce
 *
 * @param array $request
 * @param null | string | array $permission
 */
function fed_verify_nonce( $request, $permission = null ) {
	if ( ! isset( $request['fed_nonce'] ) ) {
		wp_send_json_error( array( 'message' => 'Invalid Request' ) );
	}

	if ( ! wp_verify_nonce( $request['fed_nonce'], 'fed_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid Request' ) );
	}

	if ( null !== $permission ) {
		$user_role = fed_get_current_user_role_key();
		if ( is_string( $permission ) && $user_role !== $permission ) {
			wp_send_json_error( array( 'message' => 'Invalid Request' ) );
		}
		if ( is_array( $permission ) && ! in_array( $user_role, $permission, true ) ) {
			wp_send_json_error( array( 'message' => 'Invalid Request' ) );
		}
	}
}

/**
 * Check for Nonce
 *
 * @param $request
 * @param null | array | string $permission
 *
 * @internal param string $nonce Nonce
 * @internal param string $key Key
 */
function fed_nonce_check( $request, $permission = null ) {
	fed_verify_nonce( $request, $permission );
}

/**
 * Show form fields on admin dashboard
 *
 * @param string $selected Selected.
 *
 * @return string | array
 */
function fed_admin_user_profile_select( $selected = '' ) {
	$attr = array(
		'class'   => 'fed_admin_input_items',
		'options' => apply_filters( 'fed_admin_input_item_options', array(
			'single_line' => array(
				'name'  => __( 'Single Line', 'frontend-dashboard' ),
				'image' => plugins_url( 'admin/assets/images/inputs/single_line.png', BC_FED_PLUGIN )
			),
			'multi_line'  => array(
				'name'  => __( 'Multi-Line', 'frontend-dashboard' ),
				'image' => plugins_url( 'admin/assets/images/inputs/multi_line.png', BC_FED_PLUGIN )
			),
			'number'      => array(
				'name'  => __( 'Number', 'frontend-dashboard' ),
				'image' => plugins_url( 'admin/assets/images/inputs/number.png', BC_FED_PLUGIN )
			),
			'email'       => array(
				'name'  => __( 'Email', 'frontend-dashboard' ),
				'image' => plugins_url( 'admin/assets/images/inputs/email.png', BC_FED_PLUGIN )
			),
			'checkbox'    => array(
				'name'  => __( 'Checkbox', 'frontend-dashboard' ),
				'image' => plugins_url( 'admin/assets/images/inputs/checkbox.png', BC_FED_PLUGIN )
			),
			'select'      => array(
				'name'  => __( 'Select', 'frontend-dashboard' ),
				'image' => plugins_url( 'admin/assets/images/inputs/select.png', BC_FED_PLUGIN )
			),
			'radio'       => array(
				'name'  => __( 'Radio', 'frontend-dashboard' ),
				'image' => plugins_url( 'admin/assets/images/inputs/radio.png', BC_FED_PLUGIN )
			),
			'password'    => array(
				'name'  => __( 'Password', 'frontend-dashboard' ),
				'image' => plugins_url( 'admin/assets/images/inputs/password.png', BC_FED_PLUGIN )
			),
			'url'         => array(
				'name'  => __( 'URL', 'frontend-dashboard' ),
				'image' => plugins_url( 'admin/assets/images/inputs/url.png', BC_FED_PLUGIN )
			),
		) ),
		'value'   => $selected,
	);

	return apply_filters( 'fed_admin_input_items', $attr );
}

/**
 * Enable or Disable.
 *
 * @param string|bool $condition Condition.
 *
 * @return string
 */
function fed_enable_disable( $condition = '' ) {
	if ( $condition === true || $condition === 'true' || $condition === 'enable' || $condition === 'Enable' ) {
		return '<div class="fed_enable"></div>';
	}

	return '<div class="fed_disable"></div>';
}

/**
 * @param string $condition
 *
 * @return string
 */
function fed_is_required( $condition = '' ) {
	if ( $condition === true || $condition === 'true' || $condition === 'enable' || $condition === 'Enable' ) {
		return '<span class="bg-red-font">*</span>';
	}

	return '';
}

/**
 * @param string $condition
 *
 * @return bool
 */
function fed_is_true_false( $condition = '' ) {
	if ( $condition === true || $condition === 'true' || $condition === 'enable' || $condition === 'Enable' ) {
		return true;
	}

	return false;
}

/**
 * @param string $condition
 * @param string $type
 *
 * @return array
 */
function fed_profile_enable_disable( $condition = '', $type = '' ) {
	$content = ' this field';
	if ( $type === 'register' ) {
		$content = 'Show Register Form';
	}
	if ( $type === 'dashboard' ) {
		$content = 'Show User Dashboard';
	}
	if ( $type === 'user_profile' ) {
		$content = 'Show User Profile Page';
	}

	if ( $condition === true || $condition === 'true' || $condition === 'enable' || $condition === 'Enable' ) {
		return array(
			'button'  => 'btn-primary',
			'title'   => 'Enabled',
			'content' => 'This option is enabled in ' . $content
		);
	}

	return array(
		'button'  => 'btn-danger',
		'title'   => 'Disabled',
		'content' => 'This option is disabled in ' . $content
	);
}

/**
 * Get Input Details.
 *
 * @param array $attr Attributes
 *
 * @return string
 */
function fed_get_input_details( $attr ) {
	$values                  = array();
	$values['placeholder']   = isset( $attr['placeholder'] ) && $attr['placeholder'] != '' ? esc_attr( $attr['placeholder'] ) : '';
	$values['input_type']    = isset( $attr['input_type'] ) && $attr['input_type'] != '' ? esc_attr( $attr['input_type'] ) : 'single_line';
	$values['class']         = isset( $attr['class_name'] ) && $attr['class_name'] != '' ? 'form-control ' . esc_attr( $attr['class_name'] ) : 'form-control';
	$values['name']          = isset( $attr['input_meta'] ) && $attr['input_meta'] != '' ? esc_attr( $attr['input_meta'] ) : 'BUG';
	$values['value']         = isset( $attr['user_value'] ) && $attr['user_value'] != '' ? $attr['user_value'] : '';
	$values['required']      = isset( $attr['is_required'] ) && $attr['is_required'] == 'true' ? 'required="required"' : '';
	$values['id']            = isset( $attr['id_name'] ) && $attr['id_name'] != '' ? 'id="' . $attr['id_name'] . '"' : '';
	$values['default_value'] = isset( $attr['default_value'] ) && $attr['default_value'] != '' ? $attr['default_value'] : 'yes';
	$input                   = '';
	$values['readonly']      = isset( $attr['readonly'] ) && $attr['readonly'] === true ? 'readonly=readonly' : '';
	$values['disabled']      = isset( $attr['disabled'] ) && $attr['disabled'] === true ? 'disabled=disabled' : '';

	$label              = isset( $attr['label'] ) ? $attr['label'] : '';
	$values['extended'] = isset( $attr['extended'] ) ? ( is_string( $attr['extended'] ) ? unserialize( $attr['extended'] ) : $attr['extended'] ) : array();

	$values['extra'] = isset( $attr['extra'] ) ? $attr['extra'] : '';

	switch ( $values['input_type'] ) {
		case 'single_line':
			$input .= '<input ' . $values['disabled'] . ' ' . $values['extra'] . ' ' . $values['id'] . ' ' . $values['readonly'] . ' ' . $values['required'] . ' type="text" name="' . $values['name'] . '"  value="' . esc_attr( $values['value'] ) . '" class="' . $values['class'] . '" placeholder="' . $values['placeholder'] . '" >';
			break;

		case 'hidden':
			$input .= '<input ' . $values['disabled'] . ' ' . $values['extra'] . ' ' . $values['id'] . ' ' . $values['readonly'] . ' ' . $values['required'] . ' type="hidden" name="' . $values['name'] . '"  value="' . esc_attr( $values['value'] ) . '" class="' . $values['class'] . '" placeholder="' . $values['placeholder'] . '">';
			break;

		case 'email':
			$input .= '<input ' . $values['disabled'] . ' ' . $values['extra'] . ' ' . $values['id'] . ' ' . $values['readonly'] . ' ' . $values['required'] . ' type="email" name="' . $values['name'] . '"   value="' . esc_attr( $values['value'] ) . '" class="' . $values['class'] . '" placeholder="' . $values['placeholder'] . '">';
			break;

		case 'password':
			$input .= '<input ' . $values['disabled'] . ' ' . $values['extra'] . ' ' . $values['id'] . ' ' . $values['required'] . ' type="password"
			name="' . $values['name'] . '"    class="' . $values['class'] . '" placeholder="' . $values['placeholder'] . '">';
			break;


		case 'url':
			$input .= '<input ' . $values['disabled'] . ' ' . $values['extra'] . ' ' . $values['id'] . ' ' . $values['required'] . ' type="url"  placeholder="' . $values['placeholder'] . '"  name="' . $values['name'] . '"    class="' . $values['class'] . '" value="' . esc_attr( $values['value'] ) . '" >';
			break;

		case 'multi_line':
			$rows = isset( $attr['rows'] ) ? absint( $attr['rows'] ) : 4;

			$input .= '<textarea ' . $values['disabled'] . ' ' . $values['id'] . ' ' . $values['extra'] . ' name="' . $values['name'] . '"   rows="' . $rows . '"
			          class="' . $values['class'] . '" placeholder="' . $values['placeholder'] . '">' . esc_textarea( $values['value'] ) . '</textarea>';
			break;

		case 'checkbox':
			$values['class'] = $values['class'] == 'form-control' ? '' : $values['class'];
			$input           .= '<label class="' . $values['class'] . '">
			<input ' . $values['disabled'] . ' ' . $values['extra'] . ' ' . $values['id'] . ' ' . $values['required'] . '  name="' . $values['name'] . '"  value="' . $values['default_value'] . '" type="checkbox" ' . checked( $values['value'], $values['default_value'], false ) . '> ' . $label . '</label>';

			break;

		case 'select':
			$options = fed_get_select_option_value( $attr['input_value'] );
			$input   .= '<select ' . $values['disabled'] . ' ' . $values['id'] . ' ' . $values['extra'] . ' name="' . $values['name'] . '"  class="' . $values['class'] . '">';
			foreach ( $options as $key => $label ) {
				$input .= '<option
						value="' . esc_attr( $key ) . '" ' . selected( $values['value'], $key, false ) . '>' . $label . '</option>';
			}
			$input .= '</select>';
			break;

		case 'number':
			$min  = isset( $attr['input_min'] ) ? $attr['input_min'] : 0;
			$max  = isset( $attr['input_max'] ) ? $attr['input_max'] : 99999999999999999999999999999999999999999999999999;
			$step = isset( $attr['input_step'] ) ? $attr['input_step'] : 'any';

			$input .= '<input ' . $values['disabled'] . ' ' . $values['id'] . ' ' . $values['readonly'] . ' ' . $values['extra'] . ' ' . $values['required'] . ' type="number" name="' . $values['name'] . '" 
			                                value="' . esc_attr( $values['value'] ) . '" class="' . $values['class'] . '"
			                                placeholder="' . $values['placeholder'] . '"
			                                min="' . esc_attr( $min ) . '"
			                                max="' . esc_attr( $max ) . '"
			                                step="' . esc_attr( $step ) . '">';
			break;

		case 'radio':
			$values['class'] = $values['class'] === 'form-control' ? '' : $values['class'];
			$options         = fed_get_select_option_value( $attr['input_value'] );
			foreach ( $options as $key => $label ) {
				$input .= '<label class="' . $values['class'] . '" for="' . $key . '">
					<input ' . $values['disabled'] . ' ' . $values['extra'] . ' ' . $values['readonly'] . ' name="' . $values['name'] . '"  value="' . $key . '" 
					       type="radio" ' . checked( $values['value'], $key, false ) . ' ' . $values['required'] . ' >
					' . $label . '
				</label>';
			}
			break;

	}

	return apply_filters( 'fed_custom_input_fields', $input, $values, $attr );
}

/**
 * @param $attr
 *
 * @return string
 */
function fed_get_input_group( $attr ) {
	if ( ! isset( $attr[0] ) || ! isset( $attr[1] ) ) {
		return 'INVALID FORMAT';
	}

	return '<div class="input-group fed_input_group">
                      ' . $attr[0] . '
                      <div class="input-group-btn">
                      ' . $attr[1] . '
                      </div>
                      </div>';
}

/**
 * @param $input_value
 *
 * @return array
 */
function fed_get_select_option_value( $input_value ) {
	if ( is_string( $input_value ) ) {
		return strlen( $input_value ) > 0 ? fed_convert_comma_separated_key_value( $input_value ) : array();
	}
	if ( is_array( $input_value ) ) {
		return count( $input_value ) > 0 ? $input_value : array();
	}

}

/**
 * Get all user roles
 *
 * @return array
 */
function fed_get_user_roles() {
	require_once( ABSPATH . '/wp-admin/includes/user.php' );
	$user_roles = get_editable_roles();
	$roles      = array();
	foreach ( $user_roles as $key => $user_role ) {
		$roles[ $key ] = $user_role['name'];
	}

	return $roles;
}

/**
 * Get all Extra user roles
 */
function fed_get_extra_user_roles() {
	$user_roles = fed_get_user_roles();

	return array_diff( $user_roles, fed_default_user_roles() );
}

/**
 * @return array
 */
function fed_get_user_roles_without_admin() {
	$user_roles = fed_get_user_roles();
	unset( $user_roles['administrator'] );

	return $user_roles;
}

/**
 * Default user roles
 */
function fed_default_user_roles() {
	return array(
		'administrator' => 'Administrator',
		'editor'        => 'Editor',
		'author'        => 'Author',
		'contributor'   => 'Contributor',
		'subscriber'    => 'Subscriber',
	);
}

/**
 * Get Default value for User Profile
 *
 * @param string $action Action.
 *
 * @return array
 */
function fed_get_empty_value_for_user_profile( $action ) {
	$default = array(
		'label_name'     => '',
		'input_order'    => '',
		'is_required'    => '',
		'input_type'     => '',
		'input_meta'     => '',
		'placeholder'    => '',
		'class_name'     => '',
		'id_name'        => '',
		'input_size'     => '',
		'input_value'    => '',
		'input_location' => '',
		'input_min'      => '',
		'input_max'      => '',
		'input_step'     => '',
		'input_row'      => '',
		'post_type'      => 'post',
//		'extra'             => 'yes',
		'user_role'      => array_keys( fed_get_user_roles() )
	);

	$default['extended'] = fed_default_extended_fields();

	$user_profile = array(
		'show_register'     => '',
		'show_dashboard'    => '',
		'menu'              => 'profile',
		'show_user_profile' => 'Enable',
	);

	if ( $action == 'profile' ) {
		$default_value = array_merge( $default, $user_profile );
	} else {
		$default_value = $default;
	}

	apply_filters( 'fed_empty_value_for_user_profile', $default_value );

	return $default_value;
}

/**
 * Process User Profile.
 *
 * @param array $row User Profiles
 * @param string $action Action
 * @param string $update Status
 *
 * @return array
 */
function fed_process_user_profile( $row, $action, $update = 'no' ) {

	$default = array(
		'label_name'     => isset( $row['label_name'] ) ? sanitize_text_field( $row['label_name'] ) : '',
		'input_order'    => isset( $row['input_order'] ) ? sanitize_text_field( $row['input_order'] ) : '',
		'is_required'    => isset( $row['is_required'] ) ? sanitize_text_field( $row['is_required'] ) : 'false',
		'placeholder'    => isset( $row['placeholder'] ) ? sanitize_text_field( $row['placeholder'] ) : '',
		'class_name'     => isset( $row['class_name'] ) ? sanitize_text_field( $row['class_name'] ) : '',
		'id_name'        => isset( $row['id_name'] ) ? sanitize_text_field( $row['id_name'] ) : '',
		'input_value'    => isset( $row['input_value'] ) ? sanitize_text_field( $row['input_value'] ) : '',
		'input_location' => isset( $row['location'] ) ? sanitize_text_field( $row['location'] ) : '',
		'input_min'      => isset( $row['input_min'] ) ? sanitize_text_field( $row['input_min'] ) : '',
		'input_max'      => isset( $row['input_max'] ) ? sanitize_text_field( $row['input_max'] ) : '',
		'input_step'     => isset( $row['input_step'] ) ? sanitize_text_field( $row['input_step'] ) : '',
		'input_row'      => isset( $row['input_row'] ) ? sanitize_text_field( $row['input_row'] ) : '',
		'input_type'     => isset( $row['input_type'] ) ? sanitize_text_field( $row['input_type'] ) : '',
		'input_meta'     => isset( $row['input_meta'] ) ? sanitize_text_field( $row['input_meta'] ) : '',

//		'extra'          => isset( $row['extra'] ) ? esc_attr( $row['extra'] ) : '',

		'user_role' => ( isset( $row['user_role'] ) && ! empty( $row['user_role'] ) ) ? ( is_string( $row['user_role'] ) ) ? unserialize( $row['user_role'] ) : serialize( array_keys( $row['user_role'] ) ) : array(),
	);

	if ( $action === 'post' ) {
		$default['post_type'] = ( isset( $row['post_type'] ) && fed_check_post_type( $row['post_type'] ) ) ? esc_attr( $row['post_type'] ) : 'post';
	}


	if ( $row['input_type'] === 'date' ) {
		if ( isset( $row['extended'] ) ) {
			if ( $update === 'yes' ) {
				$extended            = array(
					'date_format' => isset( $row['extended']['date_format'] ) ? esc_attr( $row['extended']['date_format'] ) : 'd-m-Y',
					'enable_time' => isset( $row['extended']['enable_time'] ) ? esc_attr( $row['extended']['enable_time'] ) : 'no',
					'date_mode'   => isset( $row['extended']['date_mode'] ) ? esc_attr( $row['extended']['date_mode'] ) : 'single',
					'time_24hr'   => isset( $row['extended']['time_24hr'] ) ? esc_attr( $row['extended']['time_24hr'] ) : '24_hours',
				);
				$default['extended'] = serialize( $extended );
			} else {
				if ( is_string( $row['extended'] ) ) {
					$default['extended'] = unserialize( $row['extended'] );
				}
				if ( is_array( $row['extended'] ) ) {
					$default['extended'] = $row['extended'];
				}
			}
		} else {
			$default['extended'] = fed_default_extended_fields();
		}
	}

	if ( $action === 'profile' ) {
		$user_profile  = array(
			'show_register'     => fed_filter_show_register( $row ),
			'show_dashboard'    => isset( $row['show_dashboard'] ) ? esc_attr( $row['show_dashboard'] ) : 'Disable',
			'menu'              => isset( $row['menu'] ) ? esc_attr( $row['menu'] ) : 'profile',
			'show_user_profile' => isset( $row['show_user_profile'] ) ? esc_attr( $row['show_user_profile'] ) : 'Enable',

		);
		$default_value = array_merge( $default, $user_profile );
	} else {
		$default_value = $default;
	}

//	var_dump( $default_value );

	return $default_value;
}

/**
 * Process Menu
 *
 * @param array $row Menu Items.
 *
 * @return array
 */
function fed_process_menu( $row ) {
	$default_value = array(
		'menu_slug'         => isset( $row['fed_menu_slug'] ) ? esc_attr( $row['fed_menu_slug'] ) : 'ERROR',
		'menu'              => isset( $row['fed_menu_name'] ) ? esc_attr( $row['fed_menu_name'] ) : 'ERROR',
		'menu_image_id'     => isset( $row['menu_image_id'] ) ? esc_attr( $row['menu_image_id'] ) : 'ERROR',
		'show_user_profile' => isset( $row['show_user_profile'] ) ? esc_attr( $row['show_user_profile'] ) : 'Enable',
		'menu_order'        => isset( $row['fed_menu_order'] ) ? esc_attr( $row['fed_menu_order'] ) : '9',
		'user_role'         => ( isset( $row['user_role'] ) && ! empty( $row['user_role'] ) ) ? ( is_string( $row['user_role'] ) ) ? unserialize( $row['user_role'] ) : serialize( array_keys( $row['user_role'] ) ) : array(),
		'extended'          => isset( $row['extended'] ) ? esc_attr( $row['extended'] ) : '',

	);

	return apply_filters( 'fed_process_menu', $default_value, $row );
}

/**
 * Convert comma separated and new line into key value pair
 *
 * @param string $text String.
 *
 * @return array
 */
function fed_convert_comma_separated_key_value( $text ) {
	$n = explode( '|', $text );
	$s = array();
	foreach ( $n as $m ) {
		$mm          = explode( ',', $m );
		$s[ $mm[0] ] = $mm[1];
	}

	return $s;
}

/**
 * Check is the field is belongs to extra profile
 *
 * @param string $meta_key Meta Key
 *
 * @return bool
 */
function fed_check_field_is_belongs_to_extra( $meta_key = '' ) {

	if ( $meta_key == '' ) {
		return false;
	}
	$user_data = fed_get_user_profile_default_meta_values();

	$key = array_reduce( $user_data, function ( $result, $item ) {
		$result[] = $item['input_meta'];

		return $result;
	}, array() );


	if ( in_array( $meta_key, $key, false ) ) {
		return true;
	}

	return false;
}

/**
 * Validation to No Update Fields
 *
 * @return array
 */
function fed_no_update_fields() {
	$fields = array(
		'user_login'
	);

	return apply_filters( 'fed_no_update_fields', $fields );
}

/**
 * Changing Archive page author
 *
 * @param string $single_template Template.
 *
 * @return string
 */
function get_custom_post_type_archive_template( $single_template ) {
	if ( isset( $_GET['fed_user_profile'] ) || is_author() ) {
		$single_template = apply_filters( 'fed_change_author_frontend_page', BC_FED_PLUGIN_DIR ) . '/templates/author.php';

	}

	return $single_template;
}

add_filter( 'archive_template', 'get_custom_post_type_archive_template' );

/**
 * Font Awesome List
 */
function fed_font_awesome_list() {
	return array(
		'fas fa-address-book' => 'f2b9',
		'fas fa-address-card' => 'f2bb',
		'fas fa-adjust' => 'f042',
		'fas fa-align-center' => 'f037',
		'fas fa-align-justify' => 'f039',
		'fas fa-align-left' => 'f036',
		'fas fa-align-right' => 'f038',
		'fas fa-allergies' => 'f461',
		'fas fa-ambulance' => 'f0f9',
		'fas fa-american-sign-language-interpreting' => 'f2a3',
		'fas fa-anchor' => 'f13d',
		'fas fa-angle-double-down' => 'f103',
		'fas fa-angle-double-left' => 'f100',
		'fas fa-angle-double-right' => 'f101',
		'fas fa-angle-double-up' => 'f102',
		'fas fa-angle-down' => 'f107',
		'fas fa-angle-left' => 'f104',
		'fas fa-angle-right' => 'f105',
		'fas fa-angle-up' => 'f106',
		'fas fa-archive' => 'f187',
		'fas fa-arrow-alt-circle-down' => 'f358',
		'fas fa-arrow-alt-circle-left' => 'f359',
		'fas fa-arrow-alt-circle-right' => 'f35a',
		'fas fa-arrow-alt-circle-up' => 'f35b',
		'fas fa-arrow-circle-down' => 'f0ab',
		'fas fa-arrow-circle-left' => 'f0a8',
		'fas fa-arrow-circle-right' => 'f0a9',
		'fas fa-arrow-circle-up' => 'f0aa',
		'fas fa-arrow-down' => 'f063',
		'fas fa-arrow-left' => 'f060',
		'fas fa-arrow-right' => 'f061',
		'fas fa-arrow-up' => 'f062',
		'fas fa-arrows-alt' => 'f0b2',
		'fas fa-arrows-alt-h' => 'f337',
		'fas fa-arrows-alt-v' => 'f338',
		'fas fa-assistive-listening-systems' => 'f2a2',
		'fas fa-asterisk' => 'f069',
		'fas fa-at' => 'f1fa',
		'fas fa-audio-description' => 'f29e',
		'fas fa-backward' => 'f04a',
		'fas fa-balance-scale' => 'f24e',
		'fas fa-ban' => 'f05e',
		'fas fa-band-aid' => 'f462',
		'fas fa-barcode' => 'f02a',
		'fas fa-bars' => 'f0c9',
		'fas fa-baseball-ball' => 'f433',
		'fas fa-basketball-ball' => 'f434',
		'fas fa-bath' => 'f2cd',
		'fas fa-battery-empty' => 'f244',
		'fas fa-battery-full' => 'f240',
		'fas fa-battery-half' => 'f242',
		'fas fa-battery-quarter' => 'f243',
		'fas fa-battery-three-quarters' => 'f241',
		'fas fa-bed' => 'f236',
		'fas fa-beer' => 'f0fc',
		'fas fa-bell' => 'f0f3',
		'fas fa-bell-slash' => 'f1f6',
		'fas fa-bicycle' => 'f206',
		'fas fa-binoculars' => 'f1e5',
		'fas fa-birthday-cake' => 'f1fd',
		'fas fa-blender' => 'f517',
		'fas fa-blind' => 'f29d',
		'fas fa-bold' => 'f032',
		'fas fa-bolt' => 'f0e7',
		'fas fa-bomb' => 'f1e2',
		'fas fa-book' => 'f02d',
		'fas fa-book-open' => 'f518',
		'fas fa-bookmark' => 'f02e',
		'fas fa-bowling-ball' => 'f436',
		'fas fa-box' => 'f466',
		'fas fa-box-open' => 'f49e',
		'fas fa-boxes' => 'f468',
		'fas fa-braille' => 'f2a1',
		'fas fa-briefcase' => 'f0b1',
		'fas fa-briefcase-medical' => 'f469',
		'fas fa-broadcast-tower' => 'f519',
		'fas fa-broom' => 'f51a',
		'fas fa-bug' => 'f188',
		'fas fa-building' => 'f1ad',
		'fas fa-bullhorn' => 'f0a1',
		'fas fa-bullseye' => 'f140',
		'fas fa-burn' => 'f46a',
		'fas fa-bus' => 'f207',
		'fas fa-calculator' => 'f1ec',
		'fas fa-calendar' => 'f133',
		'fas fa-calendar-alt' => 'f073',
		'fas fa-calendar-check' => 'f274',
		'fas fa-calendar-minus' => 'f272',
		'fas fa-calendar-plus' => 'f271',
		'fas fa-calendar-times' => 'f273',
		'fas fa-camera' => 'f030',
		'fas fa-camera-retro' => 'f083',
		'fas fa-capsules' => 'f46b',
		'fas fa-car' => 'f1b9',
		'fas fa-caret-down' => 'f0d7',
		'fas fa-caret-left' => 'f0d9',
		'fas fa-caret-right' => 'f0da',
		'fas fa-caret-square-down' => 'f150',
		'fas fa-caret-square-left' => 'f191',
		'fas fa-caret-square-right' => 'f152',
		'fas fa-caret-square-up' => 'f151',
		'fas fa-caret-up' => 'f0d8',
		'fas fa-cart-arrow-down' => 'f218',
		'fas fa-cart-plus' => 'f217',
		'fas fa-certificate' => 'f0a3',
		'fas fa-chalkboard' => 'f51b',
		'fas fa-chalkboard-teacher' => 'f51c',
		'fas fa-chart-area' => 'f1fe',
		'fas fa-chart-bar' => 'f080',
		'fas fa-chart-line' => 'f201',
		'fas fa-chart-pie' => 'f200',
		'fas fa-check' => 'f00c',
		'fas fa-check-circle' => 'f058',
		'fas fa-check-square' => 'f14a',
		'fas fa-chess' => 'f439',
		'fas fa-chess-bishop' => 'f43a',
		'fas fa-chess-board' => 'f43c',
		'fas fa-chess-king' => 'f43f',
		'fas fa-chess-knight' => 'f441',
		'fas fa-chess-pawn' => 'f443',
		'fas fa-chess-queen' => 'f445',
		'fas fa-chess-rook' => 'f447',
		'fas fa-chevron-circle-down' => 'f13a',
		'fas fa-chevron-circle-left' => 'f137',
		'fas fa-chevron-circle-right' => 'f138',
		'fas fa-chevron-circle-up' => 'f139',
		'fas fa-chevron-down' => 'f078',
		'fas fa-chevron-left' => 'f053',
		'fas fa-chevron-right' => 'f054',
		'fas fa-chevron-up' => 'f077',
		'fas fa-child' => 'f1ae',
		'fas fa-church' => 'f51d',
		'fas fa-circle' => 'f111',
		'fas fa-circle-notch' => 'f1ce',
		'fas fa-clipboard' => 'f328',
		'fas fa-clipboard-check' => 'f46c',
		'fas fa-clipboard-list' => 'f46d',
		'fas fa-clock' => 'f017',
		'fas fa-clone' => 'f24d',
		'fas fa-closed-captioning' => 'f20a',
		'fas fa-cloud' => 'f0c2',
		'fas fa-cloud-download-alt' => 'f381',
		'fas fa-cloud-upload-alt' => 'f382',
		'fas fa-code' => 'f121',
		'fas fa-code-branch' => 'f126',
		'fas fa-coffee' => 'f0f4',
		'fas fa-cog' => 'f013',
		'fas fa-cogs' => 'f085',
		'fas fa-coins' => 'f51e',
		'fas fa-columns' => 'f0db',
		'fas fa-comment' => 'f075',
		'fas fa-comment-alt' => 'f27a',
		'fas fa-comment-dots' => 'f4ad',
		'fas fa-comment-slash' => 'f4b3',
		'fas fa-comments' => 'f086',
		'fas fa-compact-disc' => 'f51f',
		'fas fa-compass' => 'f14e',
		'fas fa-compress' => 'f066',
		'fas fa-copy' => 'f0c5',
		'fas fa-copyright' => 'f1f9',
		'fas fa-couch' => 'f4b8',
		'fas fa-credit-card' => 'f09d',
		'fas fa-crop' => 'f125',
		'fas fa-crosshairs' => 'f05b',
		'fas fa-crow' => 'f520',
		'fas fa-crown' => 'f521',
		'fas fa-cube' => 'f1b2',
		'fas fa-cubes' => 'f1b3',
		'fas fa-cut' => 'f0c4',
		'fas fa-database' => 'f1c0',
		'fas fa-deaf' => 'f2a4',
		'fas fa-desktop' => 'f108',
		'fas fa-diagnoses' => 'f470',
		'fas fa-dice' => 'f522',
		'fas fa-dice-five' => 'f523',
		'fas fa-dice-four' => 'f524',
		'fas fa-dice-one' => 'f525',
		'fas fa-dice-six' => 'f526',
		'fas fa-dice-three' => 'f527',
		'fas fa-dice-two' => 'f528',
		'fas fa-divide' => 'f529',
		'fas fa-dna' => 'f471',
		'fas fa-dollar-sign' => 'f155',
		'fas fa-dolly' => 'f472',
		'fas fa-dolly-flatbed' => 'f474',
		'fas fa-donate' => 'f4b9',
		'fas fa-door-closed' => 'f52a',
		'fas fa-door-open' => 'f52b',
		'fas fa-dot-circle' => 'f192',
		'fas fa-dove' => 'f4ba',
		'fas fa-download' => 'f019',
		'fas fa-dumbbell' => 'f44b',
		'fas fa-edit' => 'f044',
		'fas fa-eject' => 'f052',
		'fas fa-ellipsis-h' => 'f141',
		'fas fa-ellipsis-v' => 'f142',
		'fas fa-envelope' => 'f0e0',
		'fas fa-envelope-open' => 'f2b6',
		'fas fa-envelope-square' => 'f199',
		'fas fa-equals' => 'f52c',
		'fas fa-eraser' => 'f12d',
		'fas fa-euro-sign' => 'f153',
		'fas fa-exchange-alt' => 'f362',
		'fas fa-exclamation' => 'f12a',
		'fas fa-exclamation-circle' => 'f06a',
		'fas fa-exclamation-triangle' => 'f071',
		'fas fa-expand' => 'f065',
		'fas fa-expand-arrows-alt' => 'f31e',
		'fas fa-external-link-alt' => 'f35d',
		'fas fa-external-link-square-alt' => 'f360',
		'fas fa-eye' => 'f06e',
		'fas fa-eye-dropper' => 'f1fb',
		'fas fa-eye-slash' => 'f070',
		'fas fa-fast-backward' => 'f049',
		'fas fa-fast-forward' => 'f050',
		'fas fa-fax' => 'f1ac',
		'fas fa-feather' => 'f52d',
		'fas fa-female' => 'f182',
		'fas fa-fighter-jet' => 'f0fb',
		'fas fa-file' => 'f15b',
		'fas fa-file-alt' => 'f15c',
		'fas fa-file-archive' => 'f1c6',
		'fas fa-file-audio' => 'f1c7',
		'fas fa-file-code' => 'f1c9',
		'fas fa-file-excel' => 'f1c3',
		'fas fa-file-image' => 'f1c5',
		'fas fa-file-medical' => 'f477',
		'fas fa-file-medical-alt' => 'f478',
		'fas fa-file-pdf' => 'f1c1',
		'fas fa-file-powerpoint' => 'f1c4',
		'fas fa-file-video' => 'f1c8',
		'fas fa-file-word' => 'f1c2',
		'fas fa-film' => 'f008',
		'fas fa-filter' => 'f0b0',
		'fas fa-fire' => 'f06d',
		'fas fa-fire-extinguisher' => 'f134',
		'fas fa-first-aid' => 'f479',
		'fas fa-flag' => 'f024',
		'fas fa-flag-checkered' => 'f11e',
		'fas fa-flask' => 'f0c3',
		'fas fa-folder' => 'f07b',
		'fas fa-folder-open' => 'f07c',
		'fas fa-font' => 'f031',
		'fas fa-football-ball' => 'f44e',
		'fas fa-forward' => 'f04e',
		'fas fa-frog' => 'f52e',
		'fas fa-frown' => 'f119',
		'fas fa-futbol' => 'f1e3',
		'fas fa-gamepad' => 'f11b',
		'fas fa-gas-pump' => 'f52f',
		'fas fa-gavel' => 'f0e3',
		'fas fa-gem' => 'f3a5',
		'fas fa-genderless' => 'f22d',
		'fas fa-gift' => 'f06b',
		'fas fa-glass-martini' => 'f000',
		'fas fa-glasses' => 'f530',
		'fas fa-globe' => 'f0ac',
		'fas fa-golf-ball' => 'f450',
		'fas fa-graduation-cap' => 'f19d',
		'fas fa-greater-than' => 'f531',
		'fas fa-greater-than-equal' => 'f532',
		'fas fa-h-square' => 'f0fd',
		'fas fa-hand-holding' => 'f4bd',
		'fas fa-hand-holding-heart' => 'f4be',
		'fas fa-hand-holding-usd' => 'f4c0',
		'fas fa-hand-lizard' => 'f258',
		'fas fa-hand-paper' => 'f256',
		'fas fa-hand-peace' => 'f25b',
		'fas fa-hand-point-down' => 'f0a7',
		'fas fa-hand-point-left' => 'f0a5',
		'fas fa-hand-point-right' => 'f0a4',
		'fas fa-hand-point-up' => 'f0a6',
		'fas fa-hand-pointer' => 'f25a',
		'fas fa-hand-rock' => 'f255',
		'fas fa-hand-scissors' => 'f257',
		'fas fa-hand-spock' => 'f259',
		'fas fa-hands' => 'f4c2',
		'fas fa-hands-helping' => 'f4c4',
		'fas fa-handshake' => 'f2b5',
		'fas fa-hashtag' => 'f292',
		'fas fa-hdd' => 'f0a0',
		'fas fa-heading' => 'f1dc',
		'fas fa-headphones' => 'f025',
		'fas fa-heart' => 'f004',
		'fas fa-heartbeat' => 'f21e',
		'fas fa-helicopter' => 'f533',
		'fas fa-history' => 'f1da',
		'fas fa-hockey-puck' => 'f453',
		'fas fa-home' => 'f015',
		'fas fa-hospital' => 'f0f8',
		'fas fa-hospital-alt' => 'f47d',
		'fas fa-hospital-symbol' => 'f47e',
		'fas fa-hourglass' => 'f254',
		'fas fa-hourglass-end' => 'f253',
		'fas fa-hourglass-half' => 'f252',
		'fas fa-hourglass-start' => 'f251',
		'fas fa-i-cursor' => 'f246',
		'fas fa-id-badge' => 'f2c1',
		'fas fa-id-card' => 'f2c2',
		'fas fa-id-card-alt' => 'f47f',
		'fas fa-image' => 'f03e',
		'fas fa-images' => 'f302',
		'fas fa-inbox' => 'f01c',
		'fas fa-indent' => 'f03c',
		'fas fa-industry' => 'f275',
		'fas fa-infinity' => 'f534',
		'fas fa-info' => 'f129',
		'fas fa-info-circle' => 'f05a',
		'fas fa-italic' => 'f033',
		'fas fa-key' => 'f084',
		'fas fa-keyboard' => 'f11c',
		'fas fa-kiwi-bird' => 'f535',
		'fas fa-language' => 'f1ab',
		'fas fa-laptop' => 'f109',
		'fas fa-leaf' => 'f06c',
		'fas fa-lemon' => 'f094',
		'fas fa-less-than' => 'f536',
		'fas fa-less-than-equal' => 'f537',
		'fas fa-level-down-alt' => 'f3be',
		'fas fa-level-up-alt' => 'f3bf',
		'fas fa-life-ring' => 'f1cd',
		'fas fa-lightbulb' => 'f0eb',
		'fas fa-link' => 'f0c1',
		'fas fa-lira-sign' => 'f195',
		'fas fa-list' => 'f03a',
		'fas fa-list-alt' => 'f022',
		'fas fa-list-ol' => 'f0cb',
		'fas fa-list-ul' => 'f0ca',
		'fas fa-location-arrow' => 'f124',
		'fas fa-lock' => 'f023',
		'fas fa-lock-open' => 'f3c1',
		'fas fa-long-arrow-alt-down' => 'f309',
		'fas fa-long-arrow-alt-left' => 'f30a',
		'fas fa-long-arrow-alt-right' => 'f30b',
		'fas fa-long-arrow-alt-up' => 'f30c',
		'fas fa-low-vision' => 'f2a8',
		'fas fa-magic' => 'f0d0',
		'fas fa-magnet' => 'f076',
		'fas fa-male' => 'f183',
		'fas fa-map' => 'f279',
		'fas fa-map-marker' => 'f041',
		'fas fa-map-marker-alt' => 'f3c5',
		'fas fa-map-pin' => 'f276',
		'fas fa-map-signs' => 'f277',
		'fas fa-mars' => 'f222',
		'fas fa-mars-double' => 'f227',
		'fas fa-mars-stroke' => 'f229',
		'fas fa-mars-stroke-h' => 'f22b',
		'fas fa-mars-stroke-v' => 'f22a',
		'fas fa-medkit' => 'f0fa',
		'fas fa-meh' => 'f11a',
		'fas fa-memory' => 'f538',
		'fas fa-mercury' => 'f223',
		'fas fa-microchip' => 'f2db',
		'fas fa-microphone' => 'f130',
		'fas fa-microphone-alt' => 'f3c9',
		'fas fa-microphone-alt-slash' => 'f539',
		'fas fa-microphone-slash' => 'f131',
		'fas fa-minus' => 'f068',
		'fas fa-minus-circle' => 'f056',
		'fas fa-minus-square' => 'f146',
		'fas fa-mobile' => 'f10b',
		'fas fa-mobile-alt' => 'f3cd',
		'fas fa-money-bill' => 'f0d6',
		'fas fa-money-bill-alt' => 'f3d1',
		'fas fa-money-bill-wave' => 'f53a',
		'fas fa-money-bill-wave-alt' => 'f53b',
		'fas fa-money-check' => 'f53c',
		'fas fa-money-check-alt' => 'f53d',
		'fas fa-moon' => 'f186',
		'fas fa-motorcycle' => 'f21c',
		'fas fa-mouse-pointer' => 'f245',
		'fas fa-music' => 'f001',
		'fas fa-neuter' => 'f22c',
		'fas fa-newspaper' => 'f1ea',
		'fas fa-not-equal' => 'f53e',
		'fas fa-notes-medical' => 'f481',
		'fas fa-object-group' => 'f247',
		'fas fa-object-ungroup' => 'f248',
		'fas fa-outdent' => 'f03b',
		'fas fa-paint-brush' => 'f1fc',
		'fas fa-palette' => 'f53f',
		'fas fa-pallet' => 'f482',
		'fas fa-paper-plane' => 'f1d8',
		'fas fa-paperclip' => 'f0c6',
		'fas fa-parachute-box' => 'f4cd',
		'fas fa-paragraph' => 'f1dd',
		'fas fa-parking' => 'f540',
		'fas fa-paste' => 'f0ea',
		'fas fa-pause' => 'f04c',
		'fas fa-pause-circle' => 'f28b',
		'fas fa-paw' => 'f1b0',
		'fas fa-pen-square' => 'f14b',
		'fas fa-pencil-alt' => 'f303',
		'fas fa-people-carry' => 'f4ce',
		'fas fa-percent' => 'f295',
		'fas fa-percentage' => 'f541',
		'fas fa-phone' => 'f095',
		'fas fa-phone-slash' => 'f3dd',
		'fas fa-phone-square' => 'f098',
		'fas fa-phone-volume' => 'f2a0',
		'fas fa-piggy-bank' => 'f4d3',
		'fas fa-pills' => 'f484',
		'fas fa-plane' => 'f072',
		'fas fa-play' => 'f04b',
		'fas fa-play-circle' => 'f144',
		'fas fa-plug' => 'f1e6',
		'fas fa-plus' => 'f067',
		'fas fa-plus-circle' => 'f055',
		'fas fa-plus-square' => 'f0fe',
		'fas fa-podcast' => 'f2ce',
		'fas fa-poo' => 'f2fe',
		'fas fa-portrait' => 'f3e0',
		'fas fa-pound-sign' => 'f154',
		'fas fa-power-off' => 'f011',
		'fas fa-prescription-bottle' => 'f485',
		'fas fa-prescription-bottle-alt' => 'f486',
		'fas fa-print' => 'f02f',
		'fas fa-procedures' => 'f487',
		'fas fa-project-diagram' => 'f542',
		'fas fa-puzzle-piece' => 'f12e',
		'fas fa-qrcode' => 'f029',
		'fas fa-question' => 'f128',
		'fas fa-question-circle' => 'f059',
		'fas fa-quidditch' => 'f458',
		'fas fa-quote-left' => 'f10d',
		'fas fa-quote-right' => 'f10e',
		'fas fa-random' => 'f074',
		'fas fa-receipt' => 'f543',
		'fas fa-recycle' => 'f1b8',
		'fas fa-redo' => 'f01e',
		'fas fa-redo-alt' => 'f2f9',
		'fas fa-registered' => 'f25d',
		'fas fa-reply' => 'f3e5',
		'fas fa-reply-all' => 'f122',
		'fas fa-retweet' => 'f079',
		'fas fa-ribbon' => 'f4d6',
		'fas fa-road' => 'f018',
		'fas fa-robot' => 'f544',
		'fas fa-rocket' => 'f135',
		'fas fa-rss' => 'f09e',
		'fas fa-rss-square' => 'f143',
		'fas fa-ruble-sign' => 'f158',
		'fas fa-ruler' => 'f545',
		'fas fa-ruler-combined' => 'f546',
		'fas fa-ruler-horizontal' => 'f547',
		'fas fa-ruler-vertical' => 'f548',
		'fas fa-rupee-sign' => 'f156',
		'fas fa-save' => 'f0c7',
		'fas fa-school' => 'f549',
		'fas fa-screwdriver' => 'f54a',
		'fas fa-search' => 'f002',
		'fas fa-search-minus' => 'f010',
		'fas fa-search-plus' => 'f00e',
		'fas fa-seedling' => 'f4d8',
		'fas fa-server' => 'f233',
		'fas fa-share' => 'f064',
		'fas fa-share-alt' => 'f1e0',
		'fas fa-share-alt-square' => 'f1e1',
		'fas fa-share-square' => 'f14d',
		'fas fa-shekel-sign' => 'f20b',
		'fas fa-shield-alt' => 'f3ed',
		'fas fa-ship' => 'f21a',
		'fas fa-shipping-fast' => 'f48b',
		'fas fa-shoe-prints' => 'f54b',
		'fas fa-shopping-bag' => 'f290',
		'fas fa-shopping-basket' => 'f291',
		'fas fa-shopping-cart' => 'f07a',
		'fas fa-shower' => 'f2cc',
		'fas fa-sign' => 'f4d9',
		'fas fa-sign-in-alt' => 'f2f6',
		'fas fa-sign-language' => 'f2a7',
		'fas fa-sign-out-alt' => 'f2f5',
		'fas fa-signal' => 'f012',
		'fas fa-sitemap' => 'f0e8',
		'fas fa-skull' => 'f54c',
		'fas fa-sliders-h' => 'f1de',
		'fas fa-smile' => 'f118',
		'fas fa-smoking' => 'f48d',
		'fas fa-smoking-ban' => 'f54d',
		'fas fa-snowflake' => 'f2dc',
		'fas fa-sort' => 'f0dc',
		'fas fa-sort-alpha-down' => 'f15d',
		'fas fa-sort-alpha-up' => 'f15e',
		'fas fa-sort-amount-down' => 'f160',
		'fas fa-sort-amount-up' => 'f161',
		'fas fa-sort-down' => 'f0dd',
		'fas fa-sort-numeric-down' => 'f162',
		'fas fa-sort-numeric-up' => 'f163',
		'fas fa-sort-up' => 'f0de',
		'fas fa-space-shuttle' => 'f197',
		'fas fa-spinner' => 'f110',
		'fas fa-square' => 'f0c8',
		'fas fa-square-full' => 'f45c',
		'fas fa-star' => 'f005',
		'fas fa-star-half' => 'f089',
		'fas fa-step-backward' => 'f048',
		'fas fa-step-forward' => 'f051',
		'fas fa-stethoscope' => 'f0f1',
		'fas fa-sticky-note' => 'f249',
		'fas fa-stop' => 'f04d',
		'fas fa-stop-circle' => 'f28d',
		'fas fa-stopwatch' => 'f2f2',
		'fas fa-store' => 'f54e',
		'fas fa-store-alt' => 'f54f',
		'fas fa-stream' => 'f550',
		'fas fa-street-view' => 'f21d',
		'fas fa-strikethrough' => 'f0cc',
		'fas fa-stroopwafel' => 'f551',
		'fas fa-subscript' => 'f12c',
		'fas fa-subway' => 'f239',
		'fas fa-suitcase' => 'f0f2',
		'fas fa-sun' => 'f185',
		'fas fa-superscript' => 'f12b',
		'fas fa-sync' => 'f021',
		'fas fa-sync-alt' => 'f2f1',
		'fas fa-syringe' => 'f48e',
		'fas fa-table' => 'f0ce',
		'fas fa-table-tennis' => 'f45d',
		'fas fa-tablet' => 'f10a',
		'fas fa-tablet-alt' => 'f3fa',
		'fas fa-tablets' => 'f490',
		'fas fa-tachometer-alt' => 'f3fd',
		'fas fa-tag' => 'f02b',
		'fas fa-tags' => 'f02c',
		'fas fa-tape' => 'f4db',
		'fas fa-tasks' => 'f0ae',
		'fas fa-taxi' => 'f1ba',
		'fas fa-terminal' => 'f120',
		'fas fa-text-height' => 'f034',
		'fas fa-text-width' => 'f035',
		'fas fa-th' => 'f00a',
		'fas fa-th-large' => 'f009',
		'fas fa-th-list' => 'f00b',
		'fas fa-thermometer' => 'f491',
		'fas fa-thermometer-empty' => 'f2cb',
		'fas fa-thermometer-full' => 'f2c7',
		'fas fa-thermometer-half' => 'f2c9',
		'fas fa-thermometer-quarter' => 'f2ca',
		'fas fa-thermometer-three-quarters' => 'f2c8',
		'fas fa-thumbs-down' => 'f165',
		'fas fa-thumbs-up' => 'f164',
		'fas fa-thumbtack' => 'f08d',
		'fas fa-ticket-alt' => 'f3ff',
		'fas fa-times' => 'f00d',
		'fas fa-times-circle' => 'f057',
		'fas fa-tint' => 'f043',
		'fas fa-toggle-off' => 'f204',
		'fas fa-toggle-on' => 'f205',
		'fas fa-toolbox' => 'f552',
		'fas fa-trademark' => 'f25c',
		'fas fa-train' => 'f238',
		'fas fa-transgender' => 'f224',
		'fas fa-transgender-alt' => 'f225',
		'fas fa-trash' => 'f1f8',
		'fas fa-trash-alt' => 'f2ed',
		'fas fa-tree' => 'f1bb',
		'fas fa-trophy' => 'f091',
		'fas fa-truck' => 'f0d1',
		'fas fa-truck-loading' => 'f4de',
		'fas fa-truck-moving' => 'f4df',
		'fas fa-tshirt' => 'f553',
		'fas fa-tty' => 'f1e4',
		'fas fa-tv' => 'f26c',
		'fas fa-umbrella' => 'f0e9',
		'fas fa-underline' => 'f0cd',
		'fas fa-undo' => 'f0e2',
		'fas fa-undo-alt' => 'f2ea',
		'fas fa-universal-access' => 'f29a',
		'fas fa-university' => 'f19c',
		'fas fa-unlink' => 'f127',
		'fas fa-unlock' => 'f09c',
		'fas fa-unlock-alt' => 'f13e',
		'fas fa-upload' => 'f093',
		'fas fa-user' => 'f007',
		'fas fa-user-alt' => 'f406',
		'fas fa-user-alt-slash' => 'f4fa',
		'fas fa-user-astronaut' => 'f4fb',
		'fas fa-user-check' => 'f4fc',
		'fas fa-user-circle' => 'f2bd',
		'fas fa-user-clock' => 'f4fd',
		'fas fa-user-cog' => 'f4fe',
		'fas fa-user-edit' => 'f4ff',
		'fas fa-user-friends' => 'f500',
		'fas fa-user-graduate' => 'f501',
		'fas fa-user-lock' => 'f502',
		'fas fa-user-md' => 'f0f0',
		'fas fa-user-minus' => 'f503',
		'fas fa-user-ninja' => 'f504',
		'fas fa-user-plus' => 'f234',
		'fas fa-user-secret' => 'f21b',
		'fas fa-user-shield' => 'f505',
		'fas fa-user-slash' => 'f506',
		'fas fa-user-tag' => 'f507',
		'fas fa-user-tie' => 'f508',
		'fas fa-user-times' => 'f235',
		'fas fa-users' => 'f0c0',
		'fas fa-users-cog' => 'f509',
		'fas fa-utensil-spoon' => 'f2e5',
		'fas fa-utensils' => 'f2e7',
		'fas fa-venus' => 'f221',
		'fas fa-venus-double' => 'f226',
		'fas fa-venus-mars' => 'f228',
		'fas fa-vial' => 'f492',
		'fas fa-vials' => 'f493',
		'fas fa-video' => 'f03d',
		'fas fa-video-slash' => 'f4e2',
		'fas fa-volleyball-ball' => 'f45f',
		'fas fa-volume-down' => 'f027',
		'fas fa-volume-off' => 'f026',
		'fas fa-volume-up' => 'f028',
		'fas fa-walking' => 'f554',
		'fas fa-wallet' => 'f555',
		'fas fa-warehouse' => 'f494',
		'fas fa-weight' => 'f496',
		'fas fa-wheelchair' => 'f193',
		'fas fa-wifi' => 'f1eb',
		'fas fa-window-close' => 'f410',
		'fas fa-window-maximize' => 'f2d0',
		'fas fa-window-minimize' => 'f2d1',
		'fas fa-window-restore' => 'f2d2',
		'fas fa-wine-glass' => 'f4e3',
		'fas fa-won-sign' => 'f159',
		'fas fa-wrench' => 'f0ad',
		'fas fa-x-ray' => 'f497',
		'fas fa-yen-sign' => 'f157',
		'far fa-address-book' => 'f2b9',
		'far fa-address-card' => 'f2bb',
		'far fa-arrow-alt-circle-down' => 'f358',
		'far fa-arrow-alt-circle-left' => 'f359',
		'far fa-arrow-alt-circle-right' => 'f35a',
		'far fa-arrow-alt-circle-up' => 'f35b',
		'far fa-bell' => 'f0f3',
		'far fa-bell-slash' => 'f1f6',
		'far fa-bookmark' => 'f02e',
		'far fa-building' => 'f1ad',
		'far fa-calendar' => 'f133',
		'far fa-calendar-alt' => 'f073',
		'far fa-calendar-check' => 'f274',
		'far fa-calendar-minus' => 'f272',
		'far fa-calendar-plus' => 'f271',
		'far fa-calendar-times' => 'f273',
		'far fa-caret-square-down' => 'f150',
		'far fa-caret-square-left' => 'f191',
		'far fa-caret-square-right' => 'f152',
		'far fa-caret-square-up' => 'f151',
		'far fa-chart-bar' => 'f080',
		'far fa-check-circle' => 'f058',
		'far fa-check-square' => 'f14a',
		'far fa-circle' => 'f111',
		'far fa-clipboard' => 'f328',
		'far fa-clock' => 'f017',
		'far fa-clone' => 'f24d',
		'far fa-closed-captioning' => 'f20a',
		'far fa-comment' => 'f075',
		'far fa-comment-alt' => 'f27a',
		'far fa-comment-dots' => 'f4ad',
		'far fa-comments' => 'f086',
		'far fa-compass' => 'f14e',
		'far fa-copy' => 'f0c5',
		'far fa-copyright' => 'f1f9',
		'far fa-credit-card' => 'f09d',
		'far fa-dot-circle' => 'f192',
		'far fa-edit' => 'f044',
		'far fa-envelope' => 'f0e0',
		'far fa-envelope-open' => 'f2b6',
		'far fa-eye' => 'f06e',
		'far fa-eye-slash' => 'f070',
		'far fa-file' => 'f15b',
		'far fa-file-alt' => 'f15c',
		'far fa-file-archive' => 'f1c6',
		'far fa-file-audio' => 'f1c7',
		'far fa-file-code' => 'f1c9',
		'far fa-file-excel' => 'f1c3',
		'far fa-file-image' => 'f1c5',
		'far fa-file-pdf' => 'f1c1',
		'far fa-file-powerpoint' => 'f1c4',
		'far fa-file-video' => 'f1c8',
		'far fa-file-word' => 'f1c2',
		'far fa-flag' => 'f024',
		'far fa-folder' => 'f07b',
		'far fa-folder-open' => 'f07c',
		'far fa-frown' => 'f119',
		'far fa-futbol' => 'f1e3',
		'far fa-gem' => 'f3a5',
		'far fa-hand-lizard' => 'f258',
		'far fa-hand-paper' => 'f256',
		'far fa-hand-peace' => 'f25b',
		'far fa-hand-point-down' => 'f0a7',
		'far fa-hand-point-left' => 'f0a5',
		'far fa-hand-point-right' => 'f0a4',
		'far fa-hand-point-up' => 'f0a6',
		'far fa-hand-pointer' => 'f25a',
		'far fa-hand-rock' => 'f255',
		'far fa-hand-scissors' => 'f257',
		'far fa-hand-spock' => 'f259',
		'far fa-handshake' => 'f2b5',
		'far fa-hdd' => 'f0a0',
		'far fa-heart' => 'f004',
		'far fa-hospital' => 'f0f8',
		'far fa-hourglass' => 'f254',
		'far fa-id-badge' => 'f2c1',
		'far fa-id-card' => 'f2c2',
		'far fa-image' => 'f03e',
		'far fa-images' => 'f302',
		'far fa-keyboard' => 'f11c',
		'far fa-lemon' => 'f094',
		'far fa-life-ring' => 'f1cd',
		'far fa-lightbulb' => 'f0eb',
		'far fa-list-alt' => 'f022',
		'far fa-map' => 'f279',
		'far fa-meh' => 'f11a',
		'far fa-minus-square' => 'f146',
		'far fa-money-bill-alt' => 'f3d1',
		'far fa-moon' => 'f186',
		'far fa-newspaper' => 'f1ea',
		'far fa-object-group' => 'f247',
		'far fa-object-ungroup' => 'f248',
		'far fa-paper-plane' => 'f1d8',
		'far fa-pause-circle' => 'f28b',
		'far fa-play-circle' => 'f144',
		'far fa-plus-square' => 'f0fe',
		'far fa-question-circle' => 'f059',
		'far fa-registered' => 'f25d',
		'far fa-save' => 'f0c7',
		'far fa-share-square' => 'f14d',
		'far fa-smile' => 'f118',
		'far fa-snowflake' => 'f2dc',
		'far fa-square' => 'f0c8',
		'far fa-star' => 'f005',
		'far fa-star-half' => 'f089',
		'far fa-sticky-note' => 'f249',
		'far fa-stop-circle' => 'f28d',
		'far fa-sun' => 'f185',
		'far fa-thumbs-down' => 'f165',
		'far fa-thumbs-up' => 'f164',
		'far fa-times-circle' => 'f057',
		'far fa-trash-alt' => 'f2ed',
		'far fa-user' => 'f007',
		'far fa-user-circle' => 'f2bd',
		'far fa-window-close' => 'f410',
		'far fa-window-maximize' => 'f2d0',
		'far fa-window-minimize' => 'f2d1',
		'far fa-window-restore' => 'f2d2',
		'fab fa-500px' => 'f26e',
		'fab fa-accessible-icon' => 'f368',
		'fab fa-accusoft' => 'f369',
		'fab fa-adn' => 'f170',
		'fab fa-adversal' => 'f36a',
		'fab fa-affiliatetheme' => 'f36b',
		'fab fa-algolia' => 'f36c',
		'fab fa-amazon' => 'f270',
		'fab fa-amazon-pay' => 'f42c',
		'fab fa-amilia' => 'f36d',
		'fab fa-android' => 'f17b',
		'fab fa-angellist' => 'f209',
		'fab fa-angrycreative' => 'f36e',
		'fab fa-angular' => 'f420',
		'fab fa-app-store' => 'f36f',
		'fab fa-app-store-ios' => 'f370',
		'fab fa-apper' => 'f371',
		'fab fa-apple' => 'f179',
		'fab fa-apple-pay' => 'f415',
		'fab fa-asymmetrik' => 'f372',
		'fab fa-audible' => 'f373',
		'fab fa-autoprefixer' => 'f41c',
		'fab fa-avianex' => 'f374',
		'fab fa-aviato' => 'f421',
		'fab fa-aws' => 'f375',
		'fab fa-bandcamp' => 'f2d5',
		'fab fa-behance' => 'f1b4',
		'fab fa-behance-square' => 'f1b5',
		'fab fa-bimobject' => 'f378',
		'fab fa-bitbucket' => 'f171',
		'fab fa-bitcoin' => 'f379',
		'fab fa-bity' => 'f37a',
		'fab fa-black-tie' => 'f27e',
		'fab fa-blackberry' => 'f37b',
		'fab fa-blogger' => 'f37c',
		'fab fa-blogger-b' => 'f37d',
		'fab fa-bluetooth' => 'f293',
		'fab fa-bluetooth-b' => 'f294',
		'fab fa-btc' => 'f15a',
		'fab fa-buromobelexperte' => 'f37f',
		'fab fa-buysellads' => 'f20d',
		'fab fa-cc-amazon-pay' => 'f42d',
		'fab fa-cc-amex' => 'f1f3',
		'fab fa-cc-apple-pay' => 'f416',
		'fab fa-cc-diners-club' => 'f24c',
		'fab fa-cc-discover' => 'f1f2',
		'fab fa-cc-jcb' => 'f24b',
		'fab fa-cc-mastercard' => 'f1f1',
		'fab fa-cc-paypal' => 'f1f4',
		'fab fa-cc-stripe' => 'f1f5',
		'fab fa-cc-visa' => 'f1f0',
		'fab fa-centercode' => 'f380',
		'fab fa-chrome' => 'f268',
		'fab fa-cloudscale' => 'f383',
		'fab fa-cloudsmith' => 'f384',
		'fab fa-cloudversify' => 'f385',
		'fab fa-codepen' => 'f1cb',
		'fab fa-codiepie' => 'f284',
		'fab fa-connectdevelop' => 'f20e',
		'fab fa-contao' => 'f26d',
		'fab fa-cpanel' => 'f388',
		'fab fa-creative-commons' => 'f25e',
		'fab fa-creative-commons-by' => 'f4e7',
		'fab fa-creative-commons-nc' => 'f4e8',
		'fab fa-creative-commons-nc-eu' => 'f4e9',
		'fab fa-creative-commons-nc-jp' => 'f4ea',
		'fab fa-creative-commons-nd' => 'f4eb',
		'fab fa-creative-commons-pd' => 'f4ec',
		'fab fa-creative-commons-pd-alt' => 'f4ed',
		'fab fa-creative-commons-remix' => 'f4ee',
		'fab fa-creative-commons-sa' => 'f4ef',
		'fab fa-creative-commons-sampling' => 'f4f0',
		'fab fa-creative-commons-sampling-plus' => 'f4f1',
		'fab fa-creative-commons-share' => 'f4f2',
		'fab fa-css3' => 'f13c',
		'fab fa-css3-alt' => 'f38b',
		'fab fa-cuttlefish' => 'f38c',
		'fab fa-d-and-d' => 'f38d',
		'fab fa-dashcube' => 'f210',
		'fab fa-delicious' => 'f1a5',
		'fab fa-deploydog' => 'f38e',
		'fab fa-deskpro' => 'f38f',
		'fab fa-deviantart' => 'f1bd',
		'fab fa-digg' => 'f1a6',
		'fab fa-digital-ocean' => 'f391',
		'fab fa-discord' => 'f392',
		'fab fa-discourse' => 'f393',
		'fab fa-dochub' => 'f394',
		'fab fa-docker' => 'f395',
		'fab fa-draft2digital' => 'f396',
		'fab fa-dribbble' => 'f17d',
		'fab fa-dribbble-square' => 'f397',
		'fab fa-dropbox' => 'f16b',
		'fab fa-drupal' => 'f1a9',
		'fab fa-dyalog' => 'f399',
		'fab fa-earlybirds' => 'f39a',
		'fab fa-ebay' => 'f4f4',
		'fab fa-edge' => 'f282',
		'fab fa-elementor' => 'f430',
		'fab fa-ember' => 'f423',
		'fab fa-empire' => 'f1d1',
		'fab fa-envira' => 'f299',
		'fab fa-erlang' => 'f39d',
		'fab fa-ethereum' => 'f42e',
		'fab fa-etsy' => 'f2d7',
		'fab fa-expeditedssl' => 'f23e',
		'fab fa-facebook' => 'f09a',
		'fab fa-facebook-f' => 'f39e',
		'fab fa-facebook-messenger' => 'f39f',
		'fab fa-facebook-square' => 'f082',
		'fab fa-firefox' => 'f269',
		'fab fa-first-order' => 'f2b0',
		'fab fa-first-order-alt' => 'f50a',
		'fab fa-firstdraft' => 'f3a1',
		'fab fa-flickr' => 'f16e',
		'fab fa-flipboard' => 'f44d',
		'fab fa-fly' => 'f417',
		'fab fa-font-awesome' => 'f2b4',
		'fab fa-font-awesome-alt' => 'f35c',
		'fab fa-font-awesome-flag' => 'f425',
		'fab fa-fonticons' => 'f280',
		'fab fa-fonticons-fi' => 'f3a2',
		'fab fa-fort-awesome' => 'f286',
		'fab fa-fort-awesome-alt' => 'f3a3',
		'fab fa-forumbee' => 'f211',
		'fab fa-foursquare' => 'f180',
		'fab fa-free-code-camp' => 'f2c5',
		'fab fa-freebsd' => 'f3a4',
		'fab fa-fulcrum' => 'f50b',
		'fab fa-galactic-republic' => 'f50c',
		'fab fa-galactic-senate' => 'f50d',
		'fab fa-get-pocket' => 'f265',
		'fab fa-gg' => 'f260',
		'fab fa-gg-circle' => 'f261',
		'fab fa-git' => 'f1d3',
		'fab fa-git-square' => 'f1d2',
		'fab fa-github' => 'f09b',
		'fab fa-github-alt' => 'f113',
		'fab fa-github-square' => 'f092',
		'fab fa-gitkraken' => 'f3a6',
		'fab fa-gitlab' => 'f296',
		'fab fa-gitter' => 'f426',
		'fab fa-glide' => 'f2a5',
		'fab fa-glide-g' => 'f2a6',
		'fab fa-gofore' => 'f3a7',
		'fab fa-goodreads' => 'f3a8',
		'fab fa-goodreads-g' => 'f3a9',
		'fab fa-google' => 'f1a0',
		'fab fa-google-drive' => 'f3aa',
		'fab fa-google-play' => 'f3ab',
		'fab fa-google-plus' => 'f2b3',
		'fab fa-google-plus-g' => 'f0d5',
		'fab fa-google-plus-square' => 'f0d4',
		'fab fa-google-wallet' => 'f1ee',
		'fab fa-gratipay' => 'f184',
		'fab fa-grav' => 'f2d6',
		'fab fa-gripfire' => 'f3ac',
		'fab fa-grunt' => 'f3ad',
		'fab fa-gulp' => 'f3ae',
		'fab fa-hacker-news' => 'f1d4',
		'fab fa-hacker-news-square' => 'f3af',
		'fab fa-hips' => 'f452',
		'fab fa-hire-a-helper' => 'f3b0',
		'fab fa-hooli' => 'f427',
		'fab fa-hotjar' => 'f3b1',
		'fab fa-houzz' => 'f27c',
		'fab fa-html5' => 'f13b',
		'fab fa-hubspot' => 'f3b2',
		'fab fa-imdb' => 'f2d8',
		'fab fa-instagram' => 'f16d',
		'fab fa-internet-explorer' => 'f26b',
		'fab fa-ioxhost' => 'f208',
		'fab fa-itunes' => 'f3b4',
		'fab fa-itunes-note' => 'f3b5',
		'fab fa-java' => 'f4e4',
		'fab fa-jedi-order' => 'f50e',
		'fab fa-jenkins' => 'f3b6',
		'fab fa-joget' => 'f3b7',
		'fab fa-joomla' => 'f1aa',
		'fab fa-js' => 'f3b8',
		'fab fa-js-square' => 'f3b9',
		'fab fa-jsfiddle' => 'f1cc',
		'fab fa-keybase' => 'f4f5',
		'fab fa-keycdn' => 'f3ba',
		'fab fa-kickstarter' => 'f3bb',
		'fab fa-kickstarter-k' => 'f3bc',
		'fab fa-korvue' => 'f42f',
		'fab fa-laravel' => 'f3bd',
		'fab fa-lastfm' => 'f202',
		'fab fa-lastfm-square' => 'f203',
		'fab fa-leanpub' => 'f212',
		'fab fa-less' => 'f41d',
		'fab fa-line' => 'f3c0',
		'fab fa-linkedin' => 'f08c',
		'fab fa-linkedin-in' => 'f0e1',
		'fab fa-linode' => 'f2b8',
		'fab fa-linux' => 'f17c',
		'fab fa-lyft' => 'f3c3',
		'fab fa-magento' => 'f3c4',
		'fab fa-mandalorian' => 'f50f',
		'fab fa-mastodon' => 'f4f6',
		'fab fa-maxcdn' => 'f136',
		'fab fa-medapps' => 'f3c6',
		'fab fa-medium' => 'f23a',
		'fab fa-medium-m' => 'f3c7',
		'fab fa-medrt' => 'f3c8',
		'fab fa-meetup' => 'f2e0',
		'fab fa-microsoft' => 'f3ca',
		'fab fa-mix' => 'f3cb',
		'fab fa-mixcloud' => 'f289',
		'fab fa-mizuni' => 'f3cc',
		'fab fa-modx' => 'f285',
		'fab fa-monero' => 'f3d0',
		'fab fa-napster' => 'f3d2',
		'fab fa-nintendo-switch' => 'f418',
		'fab fa-node' => 'f419',
		'fab fa-node-js' => 'f3d3',
		'fab fa-npm' => 'f3d4',
		'fab fa-ns8' => 'f3d5',
		'fab fa-nutritionix' => 'f3d6',
		'fab fa-odnoklassniki' => 'f263',
		'fab fa-odnoklassniki-square' => 'f264',
		'fab fa-old-republic' => 'f510',
		'fab fa-opencart' => 'f23d',
		'fab fa-openid' => 'f19b',
		'fab fa-opera' => 'f26a',
		'fab fa-optin-monster' => 'f23c',
		'fab fa-osi' => 'f41a',
		'fab fa-page4' => 'f3d7',
		'fab fa-pagelines' => 'f18c',
		'fab fa-palfed' => 'f3d8',
		'fab fa-patreon' => 'f3d9',
		'fab fa-paypal' => 'f1ed',
		'fab fa-periscope' => 'f3da',
		'fab fa-phabricator' => 'f3db',
		'fab fa-phoenix-framework' => 'f3dc',
		'fab fa-phoenix-squadron' => 'f511',
		'fab fa-php' => 'f457',
		'fab fa-pied-piper' => 'f2ae',
		'fab fa-pied-piper-alt' => 'f1a8',
		'fab fa-pied-piper-hat' => 'f4e5',
		'fab fa-pied-piper-pp' => 'f1a7',
		'fab fa-pinterest' => 'f0d2',
		'fab fa-pinterest-p' => 'f231',
		'fab fa-pinterest-square' => 'f0d3',
		'fab fa-playstation' => 'f3df',
		'fab fa-product-hunt' => 'f288',
		'fab fa-pushed' => 'f3e1',
		'fab fa-python' => 'f3e2',
		'fab fa-qq' => 'f1d6',
		'fab fa-quinscape' => 'f459',
		'fab fa-quora' => 'f2c4',
		'fab fa-r-project' => 'f4f7',
		'fab fa-ravelry' => 'f2d9',
		'fab fa-react' => 'f41b',
		'fab fa-readme' => 'f4d5',
		'fab fa-rebel' => 'f1d0',
		'fab fa-red-river' => 'f3e3',
		'fab fa-reddit' => 'f1a1',
		'fab fa-reddit-alien' => 'f281',
		'fab fa-reddit-square' => 'f1a2',
		'fab fa-rendact' => 'f3e4',
		'fab fa-renren' => 'f18b',
		'fab fa-replyd' => 'f3e6',
		'fab fa-researchgate' => 'f4f8',
		'fab fa-resolving' => 'f3e7',
		'fab fa-rocketchat' => 'f3e8',
		'fab fa-rockrms' => 'f3e9',
		'fab fa-safari' => 'f267',
		'fab fa-sass' => 'f41e',
		'fab fa-schlix' => 'f3ea',
		'fab fa-scribd' => 'f28a',
		'fab fa-searchengin' => 'f3eb',
		'fab fa-sellcast' => 'f2da',
		'fab fa-sellsy' => 'f213',
		'fab fa-servicestack' => 'f3ec',
		'fab fa-shirtsinbulk' => 'f214',
		'fab fa-simplybuilt' => 'f215',
		'fab fa-sistrix' => 'f3ee',
		'fab fa-sith' => 'f512',
		'fab fa-skyatlas' => 'f216',
		'fab fa-skype' => 'f17e',
		'fab fa-slack' => 'f198',
		'fab fa-slack-hash' => 'f3ef',
		'fab fa-slideshare' => 'f1e7',
		'fab fa-snapchat' => 'f2ab',
		'fab fa-snapchat-ghost' => 'f2ac',
		'fab fa-snapchat-square' => 'f2ad',
		'fab fa-soundcloud' => 'f1be',
		'fab fa-speakap' => 'f3f3',
		'fab fa-spotify' => 'f1bc',
		'fab fa-stack-exchange' => 'f18d',
		'fab fa-stack-overflow' => 'f16c',
		'fab fa-staylinked' => 'f3f5',
		'fab fa-steam' => 'f1b6',
		'fab fa-steam-square' => 'f1b7',
		'fab fa-steam-symbol' => 'f3f6',
		'fab fa-sticker-mule' => 'f3f7',
		'fab fa-strava' => 'f428',
		'fab fa-stripe' => 'f429',
		'fab fa-stripe-s' => 'f42a',
		'fab fa-studiovinari' => 'f3f8',
		'fab fa-stumbleupon' => 'f1a4',
		'fab fa-stumbleupon-circle' => 'f1a3',
		'fab fa-superpowers' => 'f2dd',
		'fab fa-supple' => 'f3f9',
		'fab fa-teamspeak' => 'f4f9',
		'fab fa-telegram' => 'f2c6',
		'fab fa-telegram-plane' => 'f3fe',
		'fab fa-tencent-weibo' => 'f1d5',
		'fab fa-themeisle' => 'f2b2',
		'fab fa-trade-federation' => 'f513',
		'fab fa-trello' => 'f181',
		'fab fa-tripadvisor' => 'f262',
		'fab fa-tumblr' => 'f173',
		'fab fa-tumblr-square' => 'f174',
		'fab fa-twitch' => 'f1e8',
		'fab fa-twitter' => 'f099',
		'fab fa-twitter-square' => 'f081',
		'fab fa-typo3' => 'f42b',
		'fab fa-uber' => 'f402',
		'fab fa-uikit' => 'f403',
		'fab fa-uniregistry' => 'f404',
		'fab fa-untappd' => 'f405',
		'fab fa-usb' => 'f287',
		'fab fa-ussunnah' => 'f407',
		'fab fa-vaadin' => 'f408',
		'fab fa-viacoin' => 'f237',
		'fab fa-viadeo' => 'f2a9',
		'fab fa-viadeo-square' => 'f2aa',
		'fab fa-viber' => 'f409',
		'fab fa-vimeo' => 'f40a',
		'fab fa-vimeo-square' => 'f194',
		'fab fa-vimeo-v' => 'f27d',
		'fab fa-vine' => 'f1ca',
		'fab fa-vk' => 'f189',
		'fab fa-vnv' => 'f40b',
		'fab fa-vuejs' => 'f41f',
		'fab fa-weibo' => 'f18a',
		'fab fa-weixin' => 'f1d7',
		'fab fa-whatsapp' => 'f232',
		'fab fa-whatsapp-square' => 'f40c',
		'fab fa-whmcs' => 'f40d',
		'fab fa-wikipedia-w' => 'f266',
		'fab fa-windows' => 'f17a',
		'fab fa-wolf-pack-battalion' => 'f514',
		'fab fa-wordpress' => 'f19a',
		'fab fa-wordpress-simple' => 'f411',
		'fab fa-wpbeginner' => 'f297',
		'fab fa-wpexplorer' => 'f2de',
		'fab fa-wpforms' => 'f298',
		'fab fa-xbox' => 'f412',
		'fab fa-xing' => 'f168',
		'fab fa-xing-square' => 'f169',
		'fab fa-y-combinator' => 'f23b',
		'fab fa-yahoo' => 'f19e',
		'fab fa-yandex' => 'f413',
		'fab fa-yandex-international' => 'f414',
		'fab fa-yelp' => 'f1e9',
		'fab fa-yoast' => 'f2b1',
		'fab fa-youtube' => 'f167',
		'fab fa-youtube-square' => 'f431'
	);
}

/**
 * Yes Or No
 *
 * @param string $sort Yes or No
 *
 * @return array
 */
function fed_yes_no( $sort = 'DESC' ) {
	$value = array(
		'yes' => 'Yes',
		'no'  => 'No'
	);

	if ( 'ASC' === $sort ) {
		asort( $value );
	}

	return $value;

}

/**
 * Show Register Filter
 *
 * @param array $row Row.
 *
 * @return bool | string
 */
function fed_filter_show_register( $row ) {
	if ( isset( $row['show_register'], $row['input_type'] ) ) {
		/**
		 * un-register user should not upload the file
		 */
		if ( 'file' === $row['input_type'] ) {
			$required = 'Disable';
		} else {
			$required = esc_attr( $row['show_register'] );
		}
	} else {
		$required = 'Disable';
	}

	return $required;
}

/**
 * Allow user role to upload files
 */
function fed_enable_file_uploads_by_role() {
	global $current_user;
	$user = get_userdata( $current_user->ID );
	$role = isset( $user->roles[0] ) ? $user->roles[0] : null;
	if ( $role ) {
		$fed_admin_options = get_option( 'fed_admin_settings_user', array() );
		if ( isset( $fed_admin_options['user']['upload_permission'] ) ) {
			$fed_upload_permission = array_keys( $fed_admin_options['user']['upload_permission'] );
			if ( in_array( $role, $fed_upload_permission, false ) ) {
				$contributor = get_role( $role );
				$contributor->add_cap( 'upload_files' );
			}
		}
	}
}

add_action( 'admin_init', 'fed_enable_file_uploads_by_role' );

/**
 * @param $tabs
 *
 * @return mixed
 */
function remove_medialibrary_tab( $tabs ) {
//	if ( ! current_user_can( 'administrator' ) ) {
//		unset( $tabs['library'] );
//	}
//	var_dump($tabs);
	unset( $tabs['library'] );

	return $tabs;
}

add_filter( 'media_upload_tabs', 'remove_medialibrary_tab' );

/**
 * Restricting users to view their own media files
 */
add_action( 'pre_get_posts', 'fed_restrict_user_profile_picture' );
/**
 * Restrict User Profile Picture
 *
 * @param WP_User_Query $wp_query_obj user query
 */
function fed_restrict_user_profile_picture( $wp_query_obj ) {

	global $current_user, $pagenow;

	if ( ! is_a( $current_user, 'WP_User' ) ) {
		return;
	}

	if ( 'admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments' ) {
		return;
	}

	if ( ! current_user_can( 'manage_media_library' ) ) {
		$wp_query_obj->set( 'author', $current_user->ID );
	}

}


/**
 * Script loading Pages
 *
 * @return  array
 */
function fed_get_script_loading_pages() {
	return apply_filters( 'fed_admin_script_loading_pages', array(
		'fed_settings_menu',
		'fed_user_profile',
		'fed_add_user_profile',
		'fed_user_profile_layout',
		'fed_post_fields',
		'fed_dashboard_menu',
		'fed_orders',
		'fed_help',
		'fed_status',
		'fed_plugin_pages'
	) );
}

/**
 * Get Post Status.
 *
 * @return array
 */
function fed_get_post_status() {
	return apply_filters( 'fed_update_post_status', array(
		'pending' => 'Pending',
		'publish' => 'Publish',
	) );
}

/**
 * Get all post meta.
 *
 * @param string $postid Post ID
 *
 * @return array | null
 *
 * TODO: DB
 */
function fed_get_all_post_meta( $postid ) {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value, meta_id, post_id
			FROM $wpdb->postmeta WHERE post_id = %d
			ORDER BY meta_key,meta_id", $postid ), ARRAY_A );
}

/**
 * Get all post meta key.
 *
 * @param string $postid Post ID.
 *
 * @return array
 */
function fed_get_all_post_meta_key( $postid ) {
	$post_meta          = fed_get_all_post_meta( $postid );
	$post_meta_with_key = array();
	foreach ( $post_meta as $item ) {
		$post_meta_with_key[ $item['meta_key'] ] = $item;
	}

	return $post_meta_with_key;
}

/**
 * Check Extension Loaded.
 *
 * @param string $extension PHP Extensions.
 *
 * @return bool
 */
function fed_check_extension_loaded( $extension ) {
	if ( ! extension_loaded( $extension ) ) {
		return false;
	}

	return true;
}

/**
 * Get PayPal Admin Options
 *
 * @param array $options Options.
 *
 * @return array
 *
 * TODO: PayPal
 */
function fed_get_paypal_admin_options( $options ) {
	if ( ! $options || ! isset( $options['paypal'] ) ) {
		return array( 'status' => false, 'message' => 'PayPal Options not set' );
	}

	if ( $options['paypal']['mode'] == 'live' ) {
		if ( '' == $options['paypal']['live_client_id'] || '' == $options['paypal']['live_secrete_id'] ) {
			return array( 'status' => false, 'message' => 'Live Client ID / Secrete ID not set' );
		}

		return array(
			'mode'       => 'live',
			'client_id'  => $options['paypal']['live_client_id'],
			'secrete_id' => $options['paypal']['live_secrete_id']
		);
	}

	if ( $options['paypal']['mode'] == 'sandbox' ) {
		if ( '' == $options['paypal']['sandbox_client_id'] || '' == $options['paypal']['sandbox_secrete_id'] ) {
			return array( 'status' => false, 'message' => 'Sandbox Client ID / Secrete ID not set' );
		}

		return array(
			'mode'       => 'sandbox',
			'client_id'  => $options['paypal']['sandbox_client_id'],
			'secrete_id' => $options['paypal']['sandbox_secrete_id']
		);
	}

	return array( 'status' => false, 'message' => 'Oops! Something went wrong' );
}

/**
 * Currency Type
 *
 * @return array
 */
function fed_currency_type() {
	return array(
		'USD' => array( 'name' => 'US Dollar', 'symbol' => '$', 'hex' => '&#x24;' ),
		'AED' => array( 'name' => 'United Arab Emirates Dirham', 'symbol' => 'د.إ', 'hex' => '&#x62f;&#x2e;&#x625;' ),
		'ANG' => array( 'name' => 'NL Antillian Guilder', 'symbol' => 'ƒ', 'hex' => '&#x192;' ),
		'ARS' => array( 'name' => 'Argentine Peso', 'symbol' => '$', 'hex' => '&#x24;' ),
		'AUD' => array( 'name' => 'Australian Dollar', 'symbol' => 'A$', 'hex' => '&#x41;&#x24;' ),
		'BRL' => array( 'name' => 'Brazilian Real', 'symbol' => 'R$', 'hex' => '&#x52;&#x24;' ),
		'BSD' => array( 'name' => 'Bahamian Dollar', 'symbol' => 'B$', 'hex' => '&#x42;&#x24;' ),
		'CAD' => array( 'name' => 'Canadian Dollar', 'symbol' => '$', 'hex' => '&#x24;' ),
		'CHF' => array( 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'hex' => '&#x43;&#x48;&#x46;' ),
		'CLP' => array( 'name' => 'Chilean Peso', 'symbol' => '$', 'hex' => '&#x24;' ),
		'CNY' => array( 'name' => 'Chinese Yuan Renminbi', 'symbol' => '¥', 'hex' => '&#xa5;' ),
		'COP' => array( 'name' => 'Colombian Peso', 'symbol' => '$', 'hex' => '&#x24;' ),
		'CZK' => array( 'name' => 'Czech Koruna', 'symbol' => 'Kč', 'hex' => '&#x4b;&#x10d;' ),
		'DKK' => array( 'name' => 'Danish Krone', 'symbol' => 'kr', 'hex' => '&#x6b;&#x72;' ),
		'EUR' => array( 'name' => 'Euro', 'symbol' => '€', 'hex' => '&#x20ac;' ),
		'FJD' => array( 'name' => 'Fiji Dollar', 'symbol' => 'FJ$', 'hex' => '&#x46;&#x4a;&#x24;' ),
		'GBP' => array( 'name' => 'British Pound', 'symbol' => '£', 'hex' => '&#xa3;' ),
		'GHS' => array( 'name' => 'Ghanaian New Cedi', 'symbol' => 'GH₵', 'hex' => '&#x47;&#x48;&#x20b5;' ),
		'GTQ' => array( 'name' => 'Guatemalan Quetzal', 'symbol' => 'Q', 'hex' => '&#x51;' ),
		'HKD' => array( 'name' => 'Hong Kong Dollar', 'symbol' => '$', 'hex' => '&#x24;' ),
		'HNL' => array( 'name' => 'Honduran Lempira', 'symbol' => 'L', 'hex' => '&#x4c;' ),
		'HRK' => array( 'name' => 'Croatian Kuna', 'symbol' => 'kn', 'hex' => '&#x6b;&#x6e;' ),
		'HUF' => array( 'name' => 'Hungarian Forint', 'symbol' => 'Ft', 'hex' => '&#x46;&#x74;' ),
		'IDR' => array( 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'hex' => '&#x52;&#x70;' ),
		'ILS' => array( 'name' => 'Israeli New Shekel', 'symbol' => '₪', 'hex' => '&#x20aa;' ),
		'INR' => array( 'name' => 'Indian Rupee', 'symbol' => '₹', 'hex' => '&#x20b9;' ),
		'ISK' => array( 'name' => 'Iceland Krona', 'symbol' => 'kr', 'hex' => '&#x6b;&#x72;' ),
		'JMD' => array( 'name' => 'Jamaican Dollar', 'symbol' => 'J$', 'hex' => '&#x4a;&#x24;' ),
		'JPY' => array( 'name' => 'Japanese Yen', 'symbol' => '¥', 'hex' => '&#xa5;' ),
		'KRW' => array( 'name' => 'South-Korean Won', 'symbol' => '₩', 'hex' => '&#x20a9;' ),
		'LKR' => array( 'name' => 'Sri Lanka Rupee', 'symbol' => '₨', 'hex' => '&#x20a8;' ),
		'MAD' => array( 'name' => 'Moroccan Dirham', 'symbol' => '.د.م', 'hex' => '&#x2e;&#x62f;&#x2e;&#x645;' ),
		'MMK' => array( 'name' => 'Myanmar Kyat', 'symbol' => 'K', 'hex' => '&#x4b;' ),
		'MXN' => array( 'name' => 'Mexican Peso', 'symbol' => '$', 'hex' => '&#x24;' ),
		'MYR' => array( 'name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'hex' => '&#x52;&#x4d;' ),
		'NOK' => array( 'name' => 'Norwegian Kroner', 'symbol' => 'kr', 'hex' => '&#x6b;&#x72;' ),
		'NZD' => array( 'name' => 'New Zealand Dollar', 'symbol' => '$', 'hex' => '&#x24;' ),
		'PAB' => array( 'name' => 'Panamanian Balboa', 'symbol' => 'B/.', 'hex' => '&#x42;&#x2f;&#x2e;' ),
		'PEN' => array( 'name' => 'Peruvian Nuevo Sol', 'symbol' => 'S/.', 'hex' => '&#x53;&#x2f;&#x2e;' ),
		'PHP' => array( 'name' => 'Philippine Peso', 'symbol' => '₱', 'hex' => '&#x20b1;' ),
		'PKR' => array( 'name' => 'Pakistan Rupee', 'symbol' => '₨', 'hex' => '&#x20a8;' ),
		'PLN' => array( 'name' => 'Polish Zloty', 'symbol' => 'zł', 'hex' => '&#x7a;&#x142;' ),
		'RON' => array( 'name' => 'Romanian New Lei', 'symbol' => 'lei', 'hex' => '&#x6c;&#x65;&#x69;' ),
		'RSD' => array( 'name' => 'Serbian Dinar', 'symbol' => 'RSD', 'hex' => '&#x52;&#x53;&#x44;' ),
		'RUB' => array( 'name' => 'Russian Rouble', 'symbol' => 'руб', 'hex' => '&#x440;&#x443;&#x431;' ),
		'SEK' => array( 'name' => 'Swedish Krona', 'symbol' => 'kr', 'hex' => '&#x6b;&#x72;' ),
		'SGD' => array( 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'hex' => '&#x53;&#x24;' ),
		'THB' => array( 'name' => 'Thai Baht', 'symbol' => '฿', 'hex' => '&#xe3f;' ),
		'TND' => array( 'name' => 'Tunisian Dinar', 'symbol' => 'DT', 'hex' => '&#x44;&#x54;' ),
		'TRY' => array( 'name' => 'Turkish Lira', 'symbol' => 'TL', 'hex' => '&#x54;&#x4c;' ),
		'TTD' => array( 'name' => 'Trinidad/Tobago Dollar', 'symbol' => '$', 'hex' => '&#x24;' ),
		'TWD' => array( 'name' => 'Taiwan Dollar', 'symbol' => 'NT$', 'hex' => '&#x4e;&#x54;&#x24;' ),
		'VEF' => array( 'name' => 'Venezuelan Bolivar Fuerte', 'symbol' => 'Bs', 'hex' => '&#x42;&#x73;' ),
		'VND' => array( 'name' => 'Vietnamese Dong', 'symbol' => '₫', 'hex' => '&#x20ab;' ),
		'XAF' => array( 'name' => 'CFA Franc BEAC', 'symbol' => 'FCFA', 'hex' => '&#x46;&#x43;&#x46;&#x41;' ),
		'XCD' => array( 'name' => 'East Caribbean Dollar', 'symbol' => '$', 'hex' => '&#x24;' ),
		'XPF' => array( 'name' => 'CFP Franc', 'symbol' => 'F', 'hex' => '&#x46;' ),
		'ZAR' => array( 'name' => 'South African Rand', 'symbol' => 'R', 'hex' => '&#x52;' ),
	);
}

/**
 * @param $currency
 *
 * @return mixed
 */
function fed_get_currency_symbol($currency)
{
    $type = fed_currency_type();
    return isset($type[$currency]['symbol']) ? $type[$currency]['symbol'] : '$$';
}

/**
 * Country Code.
 *
 * @return array
 */
function fed_get_country_code() {
	return apply_filters( 'fed_extend_country_code', array(
		'empty' => 'Select Country',
		'AF'    => 'AFGHANISTAN',
		'AX'    => 'ÅLAND ISLANDS',
		'AL'    => 'ALBANIA',
		'DZ'    => 'ALGERIA',
		'AS'    => 'AMERICAN SAMOA',
		'AD'    => 'ANDORRA',
		'AO'    => 'ANGOLA',
		'AI'    => 'ANGUILLA',
		'AQ'    => 'ANTARCTICA',
		'AG'    => 'ANTIGUA AND BAR­BUDA',
		'AR'    => 'ARGENTINA',
		'AM'    => 'ARMENIA',
		'AW'    => 'ARUBA',
		'AU'    => 'AUSTRALIA',
		'AT'    => 'AUSTRIA',
		'AZ'    => 'AZERBAIJAN',
		'BS'    => 'BAHAMAS',
		'BH'    => 'BAHRAIN',
		'BD'    => 'BANGLADESH',
		'BB'    => 'BARBADOS',
		'BY'    => 'BELARUS',
		'BE'    => 'BELGIUM',
		'BZ'    => 'BELIZE',
		'BJ'    => 'BENIN',
		'BM'    => 'BERMUDA',
		'BT'    => 'BHUTAN',
		'BO'    => 'BOLIVIA',
		'BA'    => 'BOSNIA AND HERZE­GOVINA',
		'BW'    => 'BOTSWANA',
		'BV'    => 'BOUVET ISLAND',
		'BR'    => 'BRAZIL',
		'IO'    => 'BRITISH INDIAN OCEAN TERRITORY',
		'BN'    => 'BRUNEI DARUSSALAM',
		'BG'    => 'BULGARIA',
		'BF'    => 'BURKINA FASO',
		'BI'    => 'BURUNDI',
		'KH'    => 'CAMBODIA',
		'CM'    => 'CAMEROON',
		'CA'    => 'CANADA',
		'CV'    => 'CAPE VERDE',
		'KY'    => 'CAYMAN ISLANDS',
		'CF'    => 'CENTRAL AFRICAN REPUBLIC',
		'TD'    => 'CHAD',
		'CL'    => 'CHILE',
		'CN'    => 'CHINA',
		'CX'    => 'CHRISTMAS ISLAND',
		'CC'    => 'COCOS (KEELING) ISLANDS',
		'CO'    => 'COLOMBIA',
		'KM'    => 'COMOROS',
		'CG'    => 'CONGO',
		'CD'    => 'CONGO, THE DEMO­CRATIC REPUBLIC OF THE',
		'CK'    => 'COOK ISLANDS',
		'CR'    => 'COSTA RICA',
		'CI'    => 'COTE D IVOIRE',
		'HR'    => 'CROATIA',
		'CU'    => 'CUBA',
		'CY'    => 'CYPRUS',
		'CZ'    => 'CZECH REPUBLIC',
		'DK'    => 'DENMARK',
		'DJ'    => 'DJIBOUTI',
		'DM'    => 'DOMINICA',
		'DO'    => 'DOMINICAN REPUBLIC',
		'EC'    => 'ECUADOR',
		'EG'    => 'EGYPT',
		'SV'    => 'EL SALVADOR',
		'GQ'    => 'EQUATORIAL GUINEA',
		'ER'    => 'ERITREA',
		'EE'    => 'ESTONIA',
		'ET'    => 'ETHIOPIA',
		'FK'    => 'FALKLAND ISLANDS (MALVINAS)',
		'FO'    => 'FAROE ISLANDS',
		'FJ'    => 'FIJI',
		'FI'    => 'FINLAND',
		'FR'    => 'FRANCE',
		'GF'    => 'FRENCH GUIANA',
		'PF'    => 'FRENCH POLYNESIA',
		'TF'    => 'FRENCH SOUTHERN TERRITORIES',
		'GA'    => 'GABON',
		'GM'    => 'GAMBIA',
		'GE'    => 'GEORGIA',
		'DE'    => 'GERMANY',
		'GH'    => 'GHANA',
		'GI'    => 'GIBRALTAR',
		'GR'    => 'GREECE',
		'GL'    => 'GREENLAND',
		'GD'    => 'GRENADA',
		'GP'    => 'GUADELOUPE',
		'GU'    => 'GUAM',
		'GT'    => 'GUATEMALA',
		'GG'    => 'GUERNSEY',
		'GN'    => 'GUINEA',
		'GW'    => 'GUINEA-BISSAU',
		'GY'    => 'GUYANA',
		'HT'    => 'HAITI',
		'HM'    => 'HEARD ISLAND AND MCDONALD ISLANDS',
		'VA'    => 'HOLY SEE (VATICAN CITY STATE)',
		'HN'    => 'HONDURAS',
		'HK'    => 'HONG KONG',
		'HU'    => 'HUNGARY',
		'IS'    => 'ICELAND',
		'IN'    => 'INDIA',
		'ID'    => 'INDONESIA',
		'IR'    => 'IRAN, ISLAMIC REPUB­LIC OF',
		'IQ'    => 'IRAQ',
		'IE'    => 'IRELAND',
		'IM'    => 'ISLE OF MAN',
		'IL'    => 'ISRAEL',
		'IT'    => 'ITALY',
		'JM'    => 'JAMAICA',
		'JP'    => 'JAPAN',
		'JE'    => 'JERSEY',
		'JO'    => 'JORDAN',
		'KZ'    => 'KAZAKHSTAN',
		'KE'    => 'KENYA',
		'KI'    => 'KIRIBATI',
		'KP'    => 'KOREA, DEMOCRATIC PEOPLES REPUBLIC OF',
		'KR'    => 'KOREA, REPUBLIC OF',
		'KW'    => 'KUWAIT',
		'KG'    => 'KYRGYZSTAN',
		'LA'    => 'LAO PEOPLES DEMO­CRATIC REPUBLIC',
		'LV'    => 'LATVIA',
		'LB'    => 'LEBANON',
		'LS'    => 'LESOTHO',
		'LR'    => 'LIBERIA',
		'LY'    => 'LIBYAN ARAB JAMA­HIRIYA',
		'LI'    => 'LIECHTENSTEIN',
		'LT'    => 'LITHUANIA',
		'LU'    => 'LUXEMBOURG',
		'MO'    => 'MACAO',
		'MK'    => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
		'MG'    => 'MADAGASCAR',
		'MW'    => 'MALAWI',
		'MY'    => 'MALAYSIA',
		'MV'    => 'MALDIVES',
		'ML'    => 'MALI',
		'MT'    => 'MALTA',
		'MH'    => 'MARSHALL ISLANDS',
		'MQ'    => 'MARTINIQUE',
		'MR'    => 'MAURITANIA',
		'MU'    => 'MAURITIUS',
		'YT'    => 'MAYOTTE',
		'MX'    => 'MEXICO',
		'FM'    => 'MICRONESIA, FEDER­ATED STATES OF',
		'MD'    => 'MOLDOVA, REPUBLIC OF',
		'MC'    => 'MONACO',
		'MN'    => 'MONGOLIA',
		'MS'    => 'MONTSERRAT',
		'MA'    => 'MOROCCO',
		'MZ'    => 'MOZAMBIQUE',
		'MM'    => 'MYANMAR',
		'NA'    => 'NAMIBIA',
		'NR'    => 'NAURU',
		'NP'    => 'NEPAL',
		'NL'    => 'NETHERLANDS',
		'AN'    => 'NETHERLANDS ANTI­LLES',
		'NC'    => 'NEW CALEDONIA',
		'NZ'    => 'NEW ZEALAND',
		'NI'    => 'NICARAGUA',
		'NE'    => 'NIGER',
		'NG'    => 'NIGERIA',
		'NU'    => 'NIUE',
		'NF'    => 'NORFOLK ISLAND',
		'MP'    => 'NORTHERN MARIANA ISLANDS',
		'NO'    => 'NORWAY',
		'OM'    => 'OMAN',
		'PK'    => 'PAKISTAN',
		'PW'    => 'PALAU',
		'PS'    => 'PALESTINIAN TERRI­TORY, OCCUPIED',
		'PA'    => 'PANAMA',
		'PG'    => 'PAPUA NEW GUINEA',
		'PY'    => 'PARAGUAY',
		'PE'    => 'PERU',
		'PH'    => 'PHILIPPINES',
		'PN'    => 'PITCAIRN',
		'PL'    => 'POLAND',
		'PT'    => 'PORTUGAL',
		'PR'    => 'PUERTO RICO',
		'QA'    => 'QATAR',
		'RE'    => 'REUNION',
		'RO'    => 'ROMANIA',
		'RU'    => 'RUSSIAN FEDERATION',
		'RW'    => 'RWANDA',
		'SH'    => 'SAINT HELENA',
		'KN'    => 'SAINT KITTS AND NEVIS',
		'LC'    => 'SAINT LUCIA',
		'PM'    => 'SAINT PIERRE AND MIQUELON',
		'VC'    => 'SAINT VINCENT AND THE GRENADINES',
		'WS'    => 'SAMOA',
		'SM'    => 'SAN MARINO',
		'ST'    => 'SAO TOME AND PRINC­IPE',
		'SA'    => 'SAUDI ARABIA',
		'SN'    => 'SENEGAL',
		'CS'    => 'SERBIA AND MON­TENEGRO',
		'SC'    => 'SEYCHELLES',
		'SL'    => 'SIERRA LEONE',
		'SG'    => 'SINGAPORE',
		'SK'    => 'SLOVAKIA',
		'SI'    => 'SLOVENIA',
		'SB'    => 'SOLOMON ISLANDS',
		'SO'    => 'SOMALIA',
		'ZA'    => 'SOUTH AFRICA',
		'GS'    => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
		'ES'    => 'SPAIN',
		'LK'    => 'SRI LANKA',
		'SD'    => 'SUDAN',
		'SR'    => 'SURINAME',
		'SJ'    => 'SVALBARD AND JAN MAYEN',
		'SZ'    => 'SWAZILAND',
		'SE'    => 'SWEDEN',
		'CH'    => 'SWITZERLAND',
		'SY'    => 'SYRIAN ARAB REPUB­LIC',
		'TW'    => 'TAIWAN, PROVINCE OF CHINA',
		'TJ'    => 'TAJIKISTAN',
		'TZ'    => 'TANZANIA, UNITED REPUBLIC OF',
		'TH'    => 'THAILAND',
		'TL'    => 'TIMOR-LESTE',
		'TG'    => 'TOGO',
		'TK'    => 'TOKELAU',
		'TO'    => 'TONGA',
		'TT'    => 'TRINIDAD AND TOBAGO',
		'TN'    => 'TUNISIA',
		'TR'    => 'TURKEY',
		'TM'    => 'TURKMENISTAN',
		'TC'    => 'TURKS AND CAICOS ISLANDS',
		'TV'    => 'TUVALU',
		'UG'    => 'UGANDA',
		'UA'    => 'UKRAINE',
		'AE'    => 'UNITED ARAB EMIR­ATES',
		'GB'    => 'UNITED KINGDOM',
		'US'    => 'UNITED STATES',
		'UM'    => 'UNITED STATES MINOR OUTLYING ISLANDS',
		'UY'    => 'URUGUAY',
		'UZ'    => 'UZBEKISTAN',
		'VU'    => 'VANUATU',
		'VE'    => 'VENEZUELA',
		'VN'    => 'VIET NAM',
		'VG'    => 'VIRGIN ISLANDS, BRIT­ISH',
		'VI'    => 'VIRGIN ISLANDS, U.S.',
		'WF'    => 'WALLIS AND FUTUNA',
		'EH'    => 'WESTERN SAHARA',
		'YE'    => 'YEMEN',
		'ZM'    => 'ZAMBIA',
		'ZW'    => 'ZIMBABWE',
	) );
}

/**
 * Country Code by ID.
 *
 * @param string $id Country ID.
 *
 * @return mixed|string
 */
function fed_get_country_code_by_id( $id ) {
	$countries = fed_get_country_code();

	return isset( $countries[ $id ] ) ? $countries[ $id ] : '';
}

/**
 * Payment Source.
 *
 * @return mixed array
 */
function fed_get_payment_sources() {
	return apply_filters( 'fed_payment_sources', array(
		'PayPal',
		'Money Transfer',
		'Cheque',
	) );
}

/**
 * Currency Type Key Value.
 *
 * @return array
 */
function fed_get_currency_type_key_value() {
	$currency  = fed_currency_type();
	$key_value = array();
	foreach ( $currency as $index => $item ) {
		$key_value[ $index ] = $item['name'] . '-' . $item['hex'];
	}

	return $key_value;
}

/**
 * get current user role.
 *
 * @return array
 */
function fed_get_current_user_role() {
	$user = get_userdata( get_current_user_id() );

	return $user->roles;
}

/**
 * @return bool
 */
function fed_get_current_user_role_key() {
	$user = get_userdata( get_current_user_id() );

	return $user ? $user->roles[0] : false;
}

/**
 * Get Amount Based on User Role.
 *
 * @return float
 *
 * TODO:Payment
 */
function fed_get_amount_based_on_user_role() {
	$current_user_role = fed_get_current_user_role();
	$settings          = get_option( 'fed_admin_settings_payments' );
	$role_pricing      = $settings['role_pricing'];
	$amount            = '0.00';
	if ( isset( $settings['role_pricing'] ) ) {
		if ( count( array_intersect( $current_user_role, array_keys( $role_pricing ) ) ) > 0 ) {
			$amount = $settings['role_pricing'][ $current_user_role[0] ];
		}
	}


	return (float) $amount;
}



/**
 * PayPal Payment Success and Cancel URL
 *
 *  TODO: PayPal
 * @return array
 */
function fed_paypal_payment_success_cancel_url() {
	return array(
		'success' => fed_get_dashboard_url() ? fed_get_dashboard_url() . '?success=yes' : site_url(),
		'cancel'  => fed_get_dashboard_url() ? fed_get_dashboard_url() . '?success=no' : site_url(),
	);
}

/**
 * Default extended Fields.
 *
 * @return array
 */
function fed_default_extended_fields() {
	return apply_filters( 'fed_default_extended_fields', array(
		'date_format' => 'd-m-Y',
		'enable_time' => 'no',
		'date_mode'   => 'single',
		'time_24hr'   => '24_hours',
	) );
}

/**
 * Get Default Post Items
 *
 * @return array
 */
function fed_get_default_post_items() {
	return array(
		'ID',
		'post_author',
		'post_date',
		'post_date_gmt',
		'post_content',
		'post_title',
		'post_excerpt',
		'post_status',
		'comment_status',
		'ping_status',
		'post_password',
		'post_name',
		'to_ping',
		'pinged',
		'post_modified',
		'post_modified_gmt',
		'post_content_filtered',
		'post_parent',
		'guid',
		'menu_order',
		'post_type',
		'post_mime_type',
		'comment_count'
	);
}

/**
 * Get Default Profile Items.
 *
 * @return array
 */
function fed_get_default_profile_items() {
	return array(
		'ID',
		'user_login',
		'user_pass',
		'user_nicename',
		'user_email',
		'user_url',
		'user_registered',
		'user_activation_key',
		'user_status',
		'display_name',
		'nickname',
		'first_name',
		'last_name',
		'description',
		'rich_editing',
		'comment_shortcuts',
		'admin_color',
		'use_ssl',
		'show_admin_bar_front',
		'dismissed_wp_pointers',
	);
}

/**
 * Get Payment Cycles.
 *
 * @return array
 * TODO: Payment
 */
function fed_get_payment_cycles() {
	return array(
		'annually' => __( 'Annually', 'frontend-dashboard' ),
		'monthly'  => __( 'Monthly', 'frontend-dashboard' ),
		'custom'   => __( 'Custom Days', 'frontend-dashboard' ),
	);
}

/**
 * Compare Two Array.
 *
 * @param array $array1 Array 1
 * @param array $array2 Array 2
 *
 * @return bool
 */
function fed_compare_two_array( $array1, $array2 ) {
	return count( array_intersect( $array1, $array2 ) ) > 0;
}

/**
 * Convert Payment Cycles to days
 *
 * @param array $payment_cycle Payment Cycle.
 *
 * @return int|string
 *
 * TODO: Payment
 */
function fed_convert_payment_cycles_to_days( $payment_cycle ) {
	$days = 30;
	switch ( $payment_cycle['payment_cycle'] ) {
		case 'annually':
			$days = '365';
			break;
		case 'monthly' :
			$days = '30';
			break;
		case 'custom':
			$days = $payment_cycle['custom_payment_cycle'];
			break;
	}

	return $days;
}

/**
 * Get User Name by ID.
 *
 * @param string $id User ID.
 *
 * @return string
 */
function fed_get_user_name_by_id( $id ) {
	$user = get_userdata( $id );

	return $user->user_login;
}

/**
 * Display Name by ID.
 *
 * @param string $id User ID.
 *
 * @return string
 */
function fed_get_display_name_by_id( $id ) {
	$user = get_userdata( $id );

	return $user->display_name;
}

/**
 * Redirect to 404
 */
function fed_redirect_to_404() {
	header( 'Status: 404 Not Found' );
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	header( 'location:' . home_url() . '/404' ) or die();
}

/**
 * Show helper message
 *
 * @param array $message
 *
 * @return string
 */
function fed_show_help_message( array $message ) {
	$icon    = isset( $message['icon'] ) ? $message['icon'] : 'fa fa-info-circle';
	$title   = isset( $message['title'] ) ? $message['title'] : 'Note';
	$content = isset( $message['content'] ) ? $message['content'] : '';
	$class = isset( $message['class'] ) ? $message['class'] : '';

	return '
	<span class="'.$class.'" data-toggle="popover" data-trigger="focus" role="button"  tabindex="0"  title="' . $title . '" data-content="' . $content . '" data-original-title="' . $title . '" data-html="true"><i class="' . $icon . '"></i></span>';
}

/**
 * Convert to pricing
 * TODO: Payment
 *
 * @param string $cycle
 * @param string $custom
 *
 * @return string
 */
function fed_convert_to_price( $cycle, $custom ) {
	switch ( $cycle ) {
		case 'annually':
			return ' / Year';
		case 'monthly':
			return ' / Month';
		case 'custom':
			return ' / ' . $custom . ' Day(s)';
	}

	return ' / Year';
}

/**
 * @return array
 */
function fed_get_public_post_types() {
	$post_type = get_post_types( array( 'public' => true ), 'objects' );
	$new_type  = array();
	foreach ( $post_type as $index => $type ) {
		$new_type[ $index ] = $type->label;
	}

	return $new_type;
}

/**
 * @param $post_type
 *
 * @return bool
 */
function fed_check_post_type( $post_type ) {
	$post_types = get_post_types( array( 'public' => true ) );

	return isset( $post_types[ $post_type ] ) ? true : false;
}

/**
 * @return string
 */
function fed_get_current_screen_id() {
	$screen = get_current_screen();

	return $screen->id;
}

/**
 * @return string
 */
function fed_plugin_versions() {
	$versions = apply_filters( 'fed_plugin_versions', array( 'core' => 'Core' ) );

	return implode( ' | ', $versions );
}

/**
 * @param $source
 * @param $_this
 * @param $that
 *
 * @return mixed
 */
function fed_convert_this_to_that( $source, $_this, $that ) {
	return str_replace( $_this, $that, $source );
}

/**
 * Show Menu Icons Popup
 */
function fed_menu_icons_popup() {
	?>
    <div class="bc_fed">
        <div class="modal fade fed_show_fa_list"
             tabindex="-1"
             role="dialog"
        >
            <div class="modal-dialog modal-lg"
                 role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title"><?php _e( 'Please Select one Image', 'frontend-dashboard' ) ?></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden"
                               id="fed_menu_box_id"
                               name="fed_menu_box_id"
                               value=""/>
                        <div class="row fed_fa_container">
							<?php foreach ( fed_font_awesome_list() as $key => $list ) {
								echo '<div class="col-md-1 fed_single_fa" 
							data-dismiss="modal"
							data-id="' . $key . '"
							data-toggle="popover"
							title="' . $list . '"
							data-trigger="hover"
							data-viewport=""
							data-content="' . $list . '"
							>
							<span class="' . $key . '"  data-id="' . $key . '" id="' . $key . '"></span>
							</div>';
							} ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php
}

/**
 * Compare two array and get the second array value
 *
 * @param array $array1
 * @param array $array2
 *
 * @return array
 */
function fed_compare_two_arrays_get_second_value( array $array1, array $array2 ) {
	foreach ( $array2 as $index => $key ) {
		if ( isset( $array1[ $index ] ) ) {
			continue;
		}
		unset( $array2[ $index ] );
	}

	return $array2;
}

/**
 * @param array $array
 * @param       $key
 * @param null  $value
 *
 * @return array
 */
function fed_get_key_value_array( array $array, $key, $value = null ) {
	$new_array = array();
	foreach ( $array as $index => $item ) {
		if ( is_object( $item ) ) {
			if ( isset( $item->$key, $item->$value ) ) {
				$new_array[ $item->$key ] = $item->$value;
			}
			if ( null === $value && isset( $item->$key ) ) {
				$new_array[ $item->$key ] = $item;
			}
		}
		if ( is_array( $item ) ) {
			if ( isset( $item[ $key ], $item[ $value ] ) ) {
				$new_array[ $item[ $key ] ] = $item[ $value ];
			}
			if ( null === $value && isset( $item[ $key ] ) ) {
				$new_array[ $item[ $key ] ] = $item;
			}
		}

	}

	return $new_array;
}

/**
 * @param string $post_type
 *
 * @return array
 */
function fed_get_category_tag_post_format( $post_type = 'post' ) {
	$taxonomies = get_object_taxonomies( $post_type, 'object' );
	$new_array  = array();
	foreach ( $taxonomies as $index => $taxonomy ) {
		if ( $taxonomy->public && $taxonomy->show_ui ) {
			if ( $taxonomy->hierarchical === false ) {
				$new_array['tag'][ $index ] = $taxonomy;
			} else {
				$new_array['category'][ $index ] = $taxonomy;
			}
		}
		if ( $index === 'post_format' ) {
			$new_array['post_format'][ $index ] = $taxonomy;
			continue;
		}
	}

	return $new_array;
}

/**
 * @param     $array
 * @param     $on
 * @param int $order
 *
 * @return array
 */
function fed_array_sort( $array, $on, $order = SORT_ASC ) {

	$new_array      = array();
	$sortable_array = array();

	if ( count( $array ) > 0 ) {
		foreach ( $array as $k => $v ) {
			if ( is_array( $v ) ) {
				foreach ( $v as $k2 => $v2 ) {
					if ( $k2 == $on ) {
						$sortable_array[ $k ] = $v2;
					}
				}
			} else {
				$sortable_array[ $k ] = $v;
			}
		}

		switch ( $order ) {
			case SORT_ASC:
				asort( $sortable_array );
				break;
			case SORT_DESC:
				arsort( $sortable_array );
				break;
		}

		foreach ( $sortable_array as $k => $v ) {
			$new_array[ $k ] = $array[ $k ];
		}
	}

	return $new_array;
}

/**
 * @param $request
 *
 * @return bool
 */
function fed_request_empty( $request ) {
	if ( '' == trim( $request ) ) {
		return true;
	}

	return false;
}


if ( ! function_exists( 'array_column' ) ) {
	/**
	 * Returns the values from a single column of the input array, identified by
	 * the $columnKey.
	 *
	 * Optionally, you may provide an $indexKey to index the values in the returned
	 * array by the values from the $indexKey column in the input array.
	 *
	 * @param array $input A multi-dimensional array (record set) from which to pull
	 *                     a column of values.
	 * @param mixed $columnKey The column of values to return. This value may be the
	 *                         integer key of the column you wish to retrieve, or it
	 *                         may be the string key name for an associative array.
	 * @param mixed $indexKey (Optional.) The column to use as the index/keys for
	 *                        the returned array. This value may be the integer key
	 *                        of the column, or it may be the string key name.
	 *
	 * @return array
	 */
	function array_column( $input = null, $columnKey = null, $indexKey = null ) {
		// Using func_get_args() in order to check for proper number of
		// parameters and trigger errors exactly as the built-in array_column()
		// does in PHP 5.5.
		$argc   = func_num_args();
		$params = func_get_args();
		if ( $argc < 2 ) {
			trigger_error( "array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING );

			return null;
		}
		if ( ! is_array( $params[0] ) ) {
			trigger_error(
				'array_column() expects parameter 1 to be array, ' . gettype( $params[0] ) . ' given',
				E_USER_WARNING
			);

			return null;
		}
		if ( ! is_int( $params[1] )
		     && ! is_float( $params[1] )
		     && ! is_string( $params[1] )
		     && $params[1] !== null
		     && ! ( is_object( $params[1] ) && method_exists( $params[1], '__toString' ) )
		) {
			trigger_error( 'array_column(): The column key should be either a string or an integer', E_USER_WARNING );

			return false;
		}
		if ( isset( $params[2] )
		     && ! is_int( $params[2] )
		     && ! is_float( $params[2] )
		     && ! is_string( $params[2] )
		     && ! ( is_object( $params[2] ) && method_exists( $params[2], '__toString' ) )
		) {
			trigger_error( 'array_column(): The index key should be either a string or an integer', E_USER_WARNING );

			return false;
		}
		$paramsInput     = $params[0];
		$paramsColumnKey = ( $params[1] !== null ) ? (string) $params[1] : null;
		$paramsIndexKey  = null;
		if ( isset( $params[2] ) ) {
			if ( is_float( $params[2] ) || is_int( $params[2] ) ) {
				$paramsIndexKey = (int) $params[2];
			} else {
				$paramsIndexKey = (string) $params[2];
			}
		}
		$resultArray = array();
		foreach ( $paramsInput as $row ) {
			$key    = $value = null;
			$keySet = $valueSet = false;
			if ( $paramsIndexKey !== null && array_key_exists( $paramsIndexKey, $row ) ) {
				$keySet = true;
				$key    = (string) $row[ $paramsIndexKey ];
			}
			if ( $paramsColumnKey === null ) {
				$valueSet = true;
				$value    = $row;
			} elseif ( is_array( $row ) && array_key_exists( $paramsColumnKey, $row ) ) {
				$valueSet = true;
				$value    = $row[ $paramsColumnKey ];
			}
			if ( $valueSet ) {
				if ( $keySet ) {
					$resultArray[ $key ] = $value;
				} else {
					$resultArray[] = $value;
				}
			}
		}

		return $resultArray;
	}
}

/**
 * @param $item
 */
function fed_call_function_method( $item ) {
	if ( is_string( $item['callable'] ) && function_exists( $item['callable'] ) ) {
		$parameter = isset( $item['arguments'] ) ? $item['arguments'] : '';
		call_user_func( $item['callable'], $parameter );
	} elseif ( is_array( $item['callable'] ) && method_exists( $item['callable']['object'], $item['callable']['method'] ) ) {
		$parameter = isset( $item['arguments'] ) ? $item['arguments'] : '';
		call_user_func( array( $item['callable']['object'], $item['callable']['method'] ), $parameter );
	} else {
		?>
        <div class="container fed_add_page_profile_container text-center">
			<?php printf( __( 'OOPS! You have not add the callable function, please add %s() to show the
					body container', 'frontend-dashboard' ), is_array( $item['callable']) ? $item['callable']['method'] :  $item['callable']) ?>
        </div>
		<?php
	}
}

/**
 * @param      $value
 * @param null $default
 *
 * @return null
 */
function fed_isset( $value, $default = null ) {
	return isset( $value ) && ! empty( $value ) && $value ? $value : $default;
}

/**
 * @param      $value
 * @param null $default
 *
 * @return null|string
 */
function fed_isset_sanitize( $value, $default = null ) {
	return isset( $value ) && ! empty( $value ) && $value ? sanitize_text_field( $value ) : $default;
}

/**
 * @param $var
 *
 * @return array|string
 */
function fed_sanitize_text_field( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'fed_sanitize_text_field', $var );
	}

	return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
}

/**
 * @param      $request
 * @param      $key
 * @param null $default
 *
 * @return null|string
 */
function fed_isset_request( $request, $key, $default = null ) {

	return isset( $request[ $key ] ) && ! empty( $request[ $key ] ) && $request[ $key ] ? sanitize_text_field( $request[ $key ] ) : $default;
}

/**
 * @param array $parameters
 * @param $url
 */
function fed_generate_url( array $parameters, $url ) {
	$parameters = array_merge( $parameters, array( 'fed_nonce' => wp_create_nonce( 'fed_nonce' ) ) );

	return esc_url( add_query_arg( $parameters, $url ) );
}

/**
 * @param $a
 * @param $b
 *
 * @return int
 */
function fed_array_of_object_sort_key( $a, $b ) {
	return strcmp( $b->create_time, $a->create_time );
}

/**
 * Get Table Status for Status Page
 * @return mixed
 */
function fed_get_table_status() {
	global $wpdb;
	$table_status = array();
	/**
	 * Check all table exists
	 */
	$user_profile = $wpdb->prefix . BC_FED_USER_PROFILE_DB;
	$menu         = $wpdb->prefix . BC_FED_MENU_DB;
	$post         = $wpdb->prefix . BC_FED_POST_DB;

	$table_status['user_profile'] = array(
		'title'       => 'User Profile',
		'status'      => $wpdb->get_var( "SHOW TABLES LIKE '$user_profile'" ) != $user_profile ? fed_enable_disable( false ) : fed_enable_disable( true ),
		'plugin_name' => BC_FED_APP_NAME,
		'position'    => 0,
	);
	$table_status['menu']         = array(
		'title'       => 'Menu',
		'status'      => $wpdb->get_var( "SHOW TABLES LIKE '$menu'" ) != $menu ? fed_enable_disable( false ) : fed_enable_disable( true ),
		'plugin_name' => BC_FED_APP_NAME,
		'position'    => 0,
	);

	$table_status['post'] = array(
		'title'       => 'Post',
		'status'      => $wpdb->get_var( "SHOW TABLES LIKE '$post'" ) != $post ? fed_enable_disable( false ) : fed_enable_disable( true ),
		'plugin_name' => BC_FED_APP_NAME,
		'position'    => 0,
	);

	$tables = apply_filters( 'fed_status_get_table_status', $table_status );

	return $tables;
}

