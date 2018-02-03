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

function fed_is_true_false( $condition = '' ) {
	if ( $condition === true || $condition === 'true' || $condition === 'enable' || $condition === 'Enable' ) {
		return true;
	}

	return false;
}

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
	if ( isset($_GET['fed_user_profile']) || is_author() ) {
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
		'fa fa-500px'                  => '500px',
		'fa fa-adjust'                 => 'Adjust',
		'fa fa-adn'                    => 'Adn',
		'fa fa-align-center'           => 'Align center',
		'fa fa-align-justify'          => 'Align justify',
		'fa fa-align-left'             => 'Align left',
		'fa fa-align-right'            => 'Align right',
		'fa fa-amazon'                 => 'Amazon',
		'fa fa-ambulance'              => 'Ambulance',
		'fa fa-anchor'                 => 'Anchor',
		'fa fa-android'                => 'Android',
		'fa fa-angellist'              => 'Angellist',
		'fa fa-angle-double-down'      => 'Angle double down',
		'fa fa-angle-double-left'      => 'Angle double left',
		'fa fa-angle-double-right'     => 'Angle double right',
		'fa fa-angle-double-up'        => 'Angle double up',
		'fa fa-angle-down'             => 'Angle down',
		'fa fa-angle-left'             => 'Angle left',
		'fa fa-angle-right'            => 'Angle right',
		'fa fa-angle-up'               => 'Angle up',
		'fa fa-apple'                  => 'Apple',
		'fa fa-archive'                => 'Archive',
		'fa fa-area-chart'             => 'Area chart',
		'fa fa-arrow-circle-down'      => 'Arrow circle down',
		'fa fa-arrow-circle-left'      => 'Arrow circle left',
		'fa fa-arrow-circle-o-down'    => 'Arrow circle o down',
		'fa fa-arrow-circle-o-left'    => 'Arrow circle o left',
		'fa fa-arrow-circle-o-right'   => 'Arrow circle o right',
		'fa fa-arrow-circle-o-up'      => 'Arrow circle o up',
		'fa fa-arrow-circle-right'     => 'Arrow circle right',
		'fa fa-arrow-circle-up'        => 'Arrow circle up',
		'fa fa-arrow-down'             => 'Arrow down',
		'fa fa-arrow-left'             => 'Arrow left',
		'fa fa-arrow-right'            => 'Arrow right',
		'fa fa-arrow-up'               => 'Arrow up',
		'fa fa-arrows'                 => 'Arrows',
		'fa fa-arrows-alt'             => 'Arrows alt',
		'fa fa-arrows-h'               => 'Arrows h',
		'fa fa-arrows-v'               => 'Arrows v',
		'fa fa-asterisk'               => 'Asterisk',
		'fa fa-at'                     => 'At',
		'fa fa-backward'               => 'Backward',
		'fa fa-balance-scale'          => 'Balance scale',
		'fa fa-ban'                    => 'Ban',
		'fa fa-bar-chart'              => 'Bar chart',
		'fa fa-barcode'                => 'Barcode',
		'fa fa-bars'                   => 'Bars',
		'fa fa-battery-empty'          => 'Battery empty',
		'fa fa-battery-full'           => 'Battery full',
		'fa fa-battery-half'           => 'Battery half',
		'fa fa-battery-quarter'        => 'Battery quarter',
		'fa fa-battery-three-quarters' => 'Battery three quarters',
		'fa fa-bed'                    => 'Bed',
		'fa fa-beer'                   => 'Beer',
		'fa fa-behance'                => 'Behance',
		'fa fa-behance-square'         => 'Behance square',
		'fa fa-bell'                   => 'Bell',
		'fa fa-bell-o'                 => 'Bell o',
		'fa fa-bell-slash'             => 'Bell slash',
		'fa fa-bell-slash-o'           => 'Bell slash o',
		'fa fa-bicycle'                => 'Bicycle',
		'fa fa-binoculars'             => 'Binoculars',
		'fa fa-birthday-cake'          => 'Birthday cake',
		'fa fa-bitbucket'              => 'Bitbucket',
		'fa fa-bitbucket-square'       => 'Bitbucket square',
		'fa fa-black-tie'              => 'Black tie',
		'fa fa-bold'                   => 'Bold',
		'fa fa-bolt'                   => 'Bolt',
		'fa fa-bomb'                   => 'Bomb',
		'fa fa-book'                   => 'Book',
		'fa fa-bookmark'               => 'Bookmark',
		'fa fa-bookmark-o'             => 'Bookmark o',
		'fa fa-briefcase'              => 'Briefcase',
		'fa fa-btc'                    => 'Btc',
		'fa fa-bug'                    => 'Bug',
		'fa fa-building'               => 'Building',
		'fa fa-building-o'             => 'Building o',
		'fa fa-bullhorn'               => 'Bullhorn',
		'fa fa-bullseye'               => 'Bullseye',
		'fa fa-bus'                    => 'Bus',
		'fa fa-buysellads'             => 'Buysellads',
		'fa fa-calculator'             => 'Calculator',
		'fa fa-calendar'               => 'Calendar',
		'fa fa-calendar-check-o'       => 'Calendar check o',
		'fa fa-calendar-minus-o'       => 'Calendar minus o',
		'fa fa-calendar-o'             => 'Calendar o',
		'fa fa-calendar-plus-o'        => 'Calendar plus o',
		'fa fa-calendar-times-o'       => 'Calendar times o',
		'fa fa-camera'                 => 'Camera',
		'fa fa-camera-retro'           => 'Camera retro',
		'fa fa-car'                    => 'Car',
		'fa fa-caret-down'             => 'Caret down',
		'fa fa-caret-left'             => 'Caret left',
		'fa fa-caret-right'            => 'Caret right',
		'fa fa-caret-square-o-down'    => 'Caret square o down',
		'fa fa-caret-square-o-left'    => 'Caret square o left',
		'fa fa-caret-square-o-right'   => 'Caret square o right',
		'fa fa-caret-square-o-up'      => 'Caret square o up',
		'fa fa-caret-up'               => 'Caret up',
		'fa fa-cart-arrow-down'        => 'Cart arrow down',
		'fa fa-cart-plus'              => 'Cart plus',
		'fa fa-cc'                     => 'Cc',
		'fa fa-cc-amex'                => 'Cc amex',
		'fa fa-cc-diners-club'         => 'Cc diners club',
		'fa fa-cc-discover'            => 'Cc discover',
		'fa fa-cc-jcb'                 => 'Cc jcb',
		'fa fa-cc-mastercard'          => 'Cc mastercard',
		'fa fa-cc-paypal'              => 'Cc paypal',
		'fa fa-cc-stripe'              => 'Cc stripe',
		'fa fa-cc-visa'                => 'Cc visa',
		'fa fa-certificate'            => 'Certificate',
		'fa fa-chain-broken'           => 'Chain broken',
		'fa fa-check'                  => 'Check',
		'fa fa-check-circle'           => 'Check circle',
		'fa fa-check-circle-o'         => 'Check circle o',
		'fa fa-check-square'           => 'Check square',
		'fa fa-check-square-o'         => 'Check square o',
		'fa fa-chevron-circle-down'    => 'Chevron circle down',
		'fa fa-chevron-circle-left'    => 'Chevron circle left',
		'fa fa-chevron-circle-right'   => 'Chevron circle right',
		'fa fa-chevron-circle-up'      => 'Chevron circle up',
		'fa fa-chevron-down'           => 'Chevron down',
		'fa fa-chevron-left'           => 'Chevron left',
		'fa fa-chevron-right'          => 'Chevron right',
		'fa fa-chevron-up'             => 'Chevron up',
		'fa fa-child'                  => 'Child',
		'fa fa-chrome'                 => 'Chrome',
		'fa fa-circle'                 => 'Circle',
		'fa fa-circle-o'               => 'Circle o',
		'fa fa-circle-o-notch'         => 'Circle o notch',
		'fa fa-circle-thin'            => 'Circle thin',
		'fa fa-clipboard'              => 'Clipboard',
		'fa fa-clock-o'                => 'Clock o',
		'fa fa-clone'                  => 'Clone',
		'fa fa-cloud'                  => 'Cloud',
		'fa fa-cloud-download'         => 'Cloud download',
		'fa fa-cloud-upload'           => 'Cloud upload',
		'fa fa-code'                   => 'Code',
		'fa fa-code-fork'              => 'Code fork',
		'fa fa-codepen'                => 'Codepen',
		'fa fa-coffee'                 => 'Coffee',
		'fa fa-cog'                    => 'Cog',
		'fa fa-cogs'                   => 'Cogs',
		'fa fa-columns'                => 'Columns',
		'fa fa-comment'                => 'Comment',
		'fa fa-comment-o'              => 'Comment o',
		'fa fa-commenting'             => 'Commenting',
		'fa fa-commenting-o'           => 'Commenting o',
		'fa fa-comments'               => 'Comments',
		'fa fa-comments-o'             => 'Comments o',
		'fa fa-compass'                => 'Compass',
		'fa fa-compress'               => 'Compress',
		'fa fa-connectdevelop'         => 'Connectdevelop',
		'fa fa-contao'                 => 'Contao',
		'fa fa-copyright'              => 'Copyright',
		'fa fa-creative-commons'       => 'Creative commons',
		'fa fa-credit-card'            => 'Credit card',
		'fa fa-crop'                   => 'Crop',
		'fa fa-crosshairs'             => 'Crosshairs',
		'fa fa-css3'                   => 'Css3',
		'fa fa-cube'                   => 'Cube',
		'fa fa-cubes'                  => 'Cubes',
		'fa fa-cutlery'                => 'Cutlery',
		'fa fa-dashcube'               => 'Dashcube',
		'fa fa-database'               => 'Database',
		'fa fa-delicious'              => 'Delicious',
		'fa fa-desktop'                => 'Desktop',
		'fa fa-deviantart'             => 'Deviantart',
		'fa fa-diamond'                => 'Diamond',
		'fa fa-digg'                   => 'Digg',
		'fa fa-dot-circle-o'           => 'Dot circle o',
		'fa fa-download'               => 'Download',
		'fa fa-dribbble'               => 'Dribbble',
		'fa fa-dropbox'                => 'Dropbox',
		'fa fa-drupal'                 => 'Drupal',
		'fa fa-eject'                  => 'Eject',
		'fa fa-ellipsis-h'             => 'Ellipsis h',
		'fa fa-ellipsis-v'             => 'Ellipsis v',
		'fa fa-empire'                 => 'Empire',
		'fa fa-envelope'               => 'Envelope',
		'fa fa-envelope-o'             => 'Envelope o',
		'fa fa-envelope-square'        => 'Envelope square',
		'fa fa-eraser'                 => 'Eraser',
		'fa fa-eur'                    => 'Eur',
		'fa fa-exchange'               => 'Exchange',
		'fa fa-exclamation'            => 'Exclamation',
		'fa fa-exclamation-circle'     => 'Exclamation circle',
		'fa fa-exclamation-triangle'   => 'Exclamation triangle',
		'fa fa-expand'                 => 'Expand',
		'fa fa-expeditedssl'           => 'Expeditedssl',
		'fa fa-external-link'          => 'External link',
		'fa fa-external-link-square'   => 'External link square',
		'fa fa-eye'                    => 'Eye',
		'fa fa-eye-slash'              => 'Eye slash',
		'fa fa-eyedropper'             => 'Eyedropper',
		'fa fa-facebook'               => 'Facebook',
		'fa fa-facebook-official'      => 'Facebook official',
		'fa fa-facebook-square'        => 'Facebook square',
		'fa fa-fast-backward'          => 'Fast backward',
		'fa fa-fast-forward'           => 'Fast forward',
		'fa fa-fax'                    => 'Fax',
		'fa fa-female'                 => 'Female',
		'fa fa-fighter-jet'            => 'Fighter jet',
		'fa fa-file'                   => 'File',
		'fa fa-file-archive-o'         => 'File archive o',
		'fa fa-file-audio-o'           => 'File audio o',
		'fa fa-file-code-o'            => 'File code o',
		'fa fa-file-excel-o'           => 'File excel o',
		'fa fa-file-image-o'           => 'File image o',
		'fa fa-file-o'                 => 'File o',
		'fa fa-file-pdf-o'             => 'File pdf o',
		'fa fa-file-powerpoint-o'      => 'File powerpoint o',
		'fa fa-file-text'              => 'File text',
		'fa fa-file-text-o'            => 'File text o',
		'fa fa-file-video-o'           => 'File video o',
		'fa fa-file-word-o'            => 'File word o',
		'fa fa-files-o'                => 'Files o',
		'fa fa-film'                   => 'Film',
		'fa fa-filter'                 => 'Filter',
		'fa fa-fire'                   => 'Fire',
		'fa fa-fire-extinguisher'      => 'Fire extinguisher',
		'fa fa-firefox'                => 'Firefox',
		'fa fa-flag'                   => 'Flag',
		'fa fa-flag-checkered'         => 'Flag checkered',
		'fa fa-flag-o'                 => 'Flag o',
		'fa fa-flask'                  => 'Flask',
		'fa fa-flickr'                 => 'Flickr',
		'fa fa-floppy-o'               => 'Floppy o',
		'fa fa-folder'                 => 'Folder',
		'fa fa-folder-o'               => 'Folder o',
		'fa fa-folder-open'            => 'Folder open',
		'fa fa-folder-open-o'          => 'Folder open o',
		'fa fa-font'                   => 'Font',
		'fa fa-fonticons'              => 'Fonticons',
		'fa fa-forumbee'               => 'Forumbee',
		'fa fa-forward'                => 'Forward',
		'fa fa-foursquare'             => 'Foursquare',
		'fa fa-frown-o'                => 'Frown o',
		'fa fa-futbol-o'               => 'Futbol o',
		'fa fa-gamepad'                => 'Gamepad',
		'fa fa-gavel'                  => 'Gavel',
		'fa fa-gbp'                    => 'Gbp',
		'fa fa-genderless'             => 'Genderless',
		'fa fa-get-pocket'             => 'Get pocket',
		'fa fa-gg'                     => 'Gg',
		'fa fa-gg-circle'              => 'Gg circle',
		'fa fa-gift'                   => 'Gift',
		'fa fa-git'                    => 'Git',
		'fa fa-git-square'             => 'Git square',
		'fa fa-github'                 => 'Github',
		'fa fa-github-alt'             => 'Github alt',
		'fa fa-github-square'          => 'Github square',
		'fa fa-glass'                  => 'Glass',
		'fa fa-globe'                  => 'Globe',
		'fa fa-google'                 => 'Google',
		'fa fa-google-plus'            => 'Google plus',
		'fa fa-google-plus-square'     => 'Google plus square',
		'fa fa-google-wallet'          => 'Google wallet',
		'fa fa-graduation-cap'         => 'Graduation cap',
		'fa fa-gratipay'               => 'Gratipay',
		'fa fa-h-square'               => 'H square',
		'fa fa-hacker-news'            => 'Hacker news',
		'fa fa-hand-lizard-o'          => 'Hand lizard o',
		'fa fa-hand-o-down'            => 'Hand o down',
		'fa fa-hand-o-left'            => 'Hand o left',
		'fa fa-hand-o-right'           => 'Hand o right',
		'fa fa-hand-o-up'              => 'Hand o up',
		'fa fa-hand-paper-o'           => 'Hand paper o',
		'fa fa-hand-peace-o'           => 'Hand peace o',
		'fa fa-hand-pointer-o'         => 'Hand pointer o',
		'fa fa-hand-rock-o'            => 'Hand rock o',
		'fa fa-hand-scissors-o'        => 'Hand scissors o',
		'fa fa-hand-spock-o'           => 'Hand spock o',
		'fa fa-hdd-o'                  => 'Hdd o',
		'fa fa-header'                 => 'Header',
		'fa fa-headphones'             => 'Headphones',
		'fa fa-heart'                  => 'Heart',
		'fa fa-heart-o'                => 'Heart o',
		'fa fa-heartbeat'              => 'Heartbeat',
		'fa fa-history'                => 'History',
		'fa fa-home'                   => 'Home',
		'fa fa-hospital-o'             => 'Hospital o',
		'fa fa-hourglass'              => 'Hourglass',
		'fa fa-hourglass-end'          => 'Hourglass end',
		'fa fa-hourglass-half'         => 'Hourglass half',
		'fa fa-hourglass-o'            => 'Hourglass o',
		'fa fa-hourglass-start'        => 'Hourglass start',
		'fa fa-houzz'                  => 'Houzz',
		'fa fa-html5'                  => 'Html5',
		'fa fa-i-cursor'               => 'I cursor',
		'fa fa-ils'                    => 'Ils',
		'fa fa-inbox'                  => 'Inbox',
		'fa fa-indent'                 => 'Indent',
		'fa fa-industry'               => 'Industry',
		'fa fa-info'                   => 'Info',
		'fa fa-info-circle'            => 'Info circle',
		'fa fa-inr'                    => 'Inr',
		'fa fa-instagram'              => 'Instagram',
		'fa fa-internet-explorer'      => 'Internet explorer',
		'fa fa-ioxhost'                => 'Ioxhost',
		'fa fa-italic'                 => 'Italic',
		'fa fa-joomla'                 => 'Joomla',
		'fa fa-jpy'                    => 'Jpy',
		'fa fa-jsfiddle'               => 'Jsfiddle',
		'fa fa-key'                    => 'Key',
		'fa fa-keyboard-o'             => 'Keyboard o',
		'fa fa-krw'                    => 'Krw',
		'fa fa-language'               => 'Language',
		'fa fa-laptop'                 => 'Laptop',
		'fa fa-lastfm'                 => 'Lastfm',
		'fa fa-lastfm-square'          => 'Lastfm square',
		'fa fa-leaf'                   => 'Leaf',
		'fa fa-leanpub'                => 'Leanpub',
		'fa fa-lemon-o'                => 'Lemon o',
		'fa fa-level-down'             => 'Level down',
		'fa fa-level-up'               => 'Level up',
		'fa fa-life-ring'              => 'Life ring',
		'fa fa-lightbulb-o'            => 'Lightbulb o',
		'fa fa-line-chart'             => 'Line chart',
		'fa fa-link'                   => 'Link',
		'fa fa-linkedin'               => 'Linkedin',
		'fa fa-linkedin-square'        => 'Linkedin square',
		'fa fa-linux'                  => 'Linux',
		'fa fa-list'                   => 'List',
		'fa fa-list-alt'               => 'List alt',
		'fa fa-list-ol'                => 'List ol',
		'fa fa-list-ul'                => 'List ul',
		'fa fa-location-arrow'         => 'Location arrow',
		'fa fa-lock'                   => 'Lock',
		'fa fa-long-arrow-down'        => 'Long arrow down',
		'fa fa-long-arrow-left'        => 'Long arrow left',
		'fa fa-long-arrow-right'       => 'Long arrow right',
		'fa fa-long-arrow-up'          => 'Long arrow up',
		'fa fa-magic'                  => 'Magic',
		'fa fa-magnet'                 => 'Magnet',
		'fa fa-male'                   => 'Male',
		'fa fa-map'                    => 'Map',
		'fa fa-map-marker'             => 'Map marker',
		'fa fa-map-o'                  => 'Map o',
		'fa fa-map-pin'                => 'Map pin',
		'fa fa-map-signs'              => 'Map signs',
		'fa fa-mars'                   => 'Mars',
		'fa fa-mars-double'            => 'Mars double',
		'fa fa-mars-stroke'            => 'Mars stroke',
		'fa fa-mars-stroke-h'          => 'Mars stroke h',
		'fa fa-mars-stroke-v'          => 'Mars stroke v',
		'fa fa-maxcdn'                 => 'Maxcdn',
		'fa fa-meanpath'               => 'Meanpath',
		'fa fa-medium'                 => 'Medium',
		'fa fa-medkit'                 => 'Medkit',
		'fa fa-meh-o'                  => 'Meh o',
		'fa fa-mercury'                => 'Mercury',
		'fa fa-microphone'             => 'Microphone',
		'fa fa-microphone-slash'       => 'Microphone slash',
		'fa fa-minus'                  => 'Minus',
		'fa fa-minus-circle'           => 'Minus circle',
		'fa fa-minus-square'           => 'Minus square',
		'fa fa-minus-square-o'         => 'Minus square o',
		'fa fa-mobile'                 => 'Mobile',
		'fa fa-money'                  => 'Money',
		'fa fa-moon-o'                 => 'Moon o',
		'fa fa-motorcycle'             => 'Motorcycle',
		'fa fa-mouse-pointer'          => 'Mouse pointer',
		'fa fa-music'                  => 'Music',
		'fa fa-neuter'                 => 'Neuter',
		'fa fa-newspaper-o'            => 'Newspaper o',
		'fa fa-object-group'           => 'Object group',
		'fa fa-object-ungroup'         => 'Object ungroup',
		'fa fa-odnoklassniki'          => 'Odnoklassniki',
		'fa fa-odnoklassniki-square'   => 'Odnoklassniki square',
		'fa fa-opencart'               => 'Opencart',
		'fa fa-openid'                 => 'Openid',
		'fa fa-opera'                  => 'Opera',
		'fa fa-optin-monster'          => 'Optin monster',
		'fa fa-outdent'                => 'Outdent',
		'fa fa-pagelines'              => 'Pagelines',
		'fa fa-paint-brush'            => 'Paint brush',
		'fa fa-paper-plane'            => 'Paper plane',
		'fa fa-paper-plane-o'          => 'Paper plane o',
		'fa fa-paperclip'              => 'Paperclip',
		'fa fa-paragraph'              => 'Paragraph',
		'fa fa-pause'                  => 'Pause',
		'fa fa-paw'                    => 'Paw',
		'fa fa-paypal'                 => 'Paypal',
		'fa fa-pencil'                 => 'Pencil',
		'fa fa-pencil-square'          => 'Pencil square',
		'fa fa-pencil-square-o'        => 'Pencil square o',
		'fa fa-phone'                  => 'Phone',
		'fa fa-phone-square'           => 'Phone square',
		'fa fa-picture-o'              => 'Picture o',
		'fa fa-pie-chart'              => 'Pie chart',
		'fa fa-pied-piper'             => 'Pied piper',
		'fa fa-pied-piper-alt'         => 'Pied piper alt',
		'fa fa-pinterest'              => 'Pinterest',
		'fa fa-pinterest-p'            => 'Pinterest p',
		'fa fa-pinterest-square'       => 'Pinterest square',
		'fa fa-plane'                  => 'Plane',
		'fa fa-play'                   => 'Play',
		'fa fa-play-circle'            => 'Play circle',
		'fa fa-play-circle-o'          => 'Play circle o',
		'fa fa-plug'                   => 'Plug',
		'fa fa-plus'                   => 'Plus',
		'fa fa-plus-circle'            => 'Plus circle',
		'fa fa-plus-square'            => 'Plus square',
		'fa fa-plus-square-o'          => 'Plus square o',
		'fa fa-power-off'              => 'Power off',
		'fa fa-print'                  => 'Print',
		'fa fa-puzzle-piece'           => 'Puzzle piece',
		'fa fa-qq'                     => 'Qq',
		'fa fa-qrcode'                 => 'Qrcode',
		'fa fa-question'               => 'Question',
		'fa fa-question-circle'        => 'Question circle',
		'fa fa-quote-left'             => 'Quote left',
		'fa fa-quote-right'            => 'Quote right',
		'fa fa-random'                 => 'Random',
		'fa fa-rebel'                  => 'Rebel',
		'fa fa-recycle'                => 'Recycle',
		'fa fa-reddit'                 => 'Reddit',
		'fa fa-reddit-square'          => 'Reddit square',
		'fa fa-refresh'                => 'Refresh',
		'fa fa-registered'             => 'Registered',
		'fa fa-renren'                 => 'Renren',
		'fa fa-repeat'                 => 'Repeat',
		'fa fa-reply'                  => 'Reply',
		'fa fa-reply-all'              => 'Reply all',
		'fa fa-retweet'                => 'Retweet',
		'fa fa-road'                   => 'Road',
		'fa fa-rocket'                 => 'Rocket',
		'fa fa-rss'                    => 'Rss',
		'fa fa-rss-square'             => 'Rss square',
		'fa fa-rub'                    => 'Rub',
		'fa fa-safari'                 => 'Safari',
		'fa fa-scissors'               => 'Scissors',
		'fa fa-search'                 => 'Search',
		'fa fa-search-minus'           => 'Search minus',
		'fa fa-search-plus'            => 'Search plus',
		'fa fa-sellsy'                 => 'Sellsy',
		'fa fa-server'                 => 'Server',
		'fa fa-share'                  => 'Share',
		'fa fa-share-alt'              => 'Share alt',
		'fa fa-share-alt-square'       => 'Share alt square',
		'fa fa-share-square'           => 'Share square',
		'fa fa-share-square-o'         => 'Share square o',
		'fa fa-shield'                 => 'Shield',
		'fa fa-ship'                   => 'Ship',
		'fa fa-shirtsinbulk'           => 'Shirtsinbulk',
		'fa fa-shopping-cart'          => 'Shopping cart',
		'fa fa-sign-in'                => 'Sign in',
		'fa fa-sign-out'               => 'Sign out',
		'fa fa-signal'                 => 'Signal',
		'fa fa-simplybuilt'            => 'Simplybuilt',
		'fa fa-sitemap'                => 'Sitemap',
		'fa fa-skyatlas'               => 'Skyatlas',
		'fa fa-skype'                  => 'Skype',
		'fa fa-slack'                  => 'Slack',
		'fa fa-sliders'                => 'Sliders',
		'fa fa-slideshare'             => 'Slideshare',
		'fa fa-smile-o'                => 'Smile o',
		'fa fa-sort'                   => 'Sort',
		'fa fa-sort-alpha-asc'         => 'Sort alpha asc',
		'fa fa-sort-alpha-desc'        => 'Sort alpha desc',
		'fa fa-sort-amount-asc'        => 'Sort amount asc',
		'fa fa-sort-amount-desc'       => 'Sort amount desc',
		'fa fa-sort-asc'               => 'Sort asc',
		'fa fa-sort-desc'              => 'Sort desc',
		'fa fa-sort-numeric-asc'       => 'Sort numeric asc',
		'fa fa-sort-numeric-desc'      => 'Sort numeric desc',
		'fa fa-soundcloud'             => 'Soundcloud',
		'fa fa-space-shuttle'          => 'Space shuttle',
		'fa fa-spinner'                => 'Spinner',
		'fa fa-spoon'                  => 'Spoon',
		'fa fa-spotify'                => 'Spotify',
		'fa fa-square'                 => 'Square',
		'fa fa-square-o'               => 'Square o',
		'fa fa-stack-exchange'         => 'Stack exchange',
		'fa fa-stack-overflow'         => 'Stack overflow',
		'fa fa-star'                   => 'Star',
		'fa fa-star-half'              => 'Star half',
		'fa fa-star-half-o'            => 'Star half o',
		'fa fa-star-o'                 => 'Star o',
		'fa fa-steam'                  => 'Steam',
		'fa fa-steam-square'           => 'Steam square',
		'fa fa-step-backward'          => 'Step backward',
		'fa fa-step-forward'           => 'Step forward',
		'fa fa-stethoscope'            => 'Stethoscope',
		'fa fa-sticky-note'            => 'Sticky note',
		'fa fa-sticky-note-o'          => 'Sticky note o',
		'fa fa-stop'                   => 'Stop',
		'fa fa-street-view'            => 'Street view',
		'fa fa-strikethrough'          => 'Strikethrough',
		'fa fa-stumbleupon'            => 'Stumbleupon',
		'fa fa-stumbleupon-circle'     => 'Stumbleupon circle',
		'fa fa-subscript'              => 'Subscript',
		'fa fa-subway'                 => 'Subway',
		'fa fa-suitcase'               => 'Suitcase',
		'fa fa-sun-o'                  => 'Sun o',
		'fa fa-superscript'            => 'Superscript',
		'fa fa-table'                  => 'Table',
		'fa fa-tablet'                 => 'Tablet',
		'fa fa-tachometer'             => 'Tachometer',
		'fa fa-tag'                    => 'Tag',
		'fa fa-tags'                   => 'Tags',
		'fa fa-tasks'                  => 'Tasks',
		'fa fa-taxi'                   => 'Taxi',
		'fa fa-television'             => 'Television',
		'fa fa-tencent-weibo'          => 'Tencent weibo',
		'fa fa-terminal'               => 'Terminal',
		'fa fa-text-height'            => 'Text height',
		'fa fa-text-width'             => 'Text width',
		'fa fa-th'                     => 'Th',
		'fa fa-th-large'               => 'Th large',
		'fa fa-th-list'                => 'Th list',
		'fa fa-thumb-tack'             => 'Thumb tack',
		'fa fa-thumbs-down'            => 'Thumbs down',
		'fa fa-thumbs-o-down'          => 'Thumbs o down',
		'fa fa-thumbs-o-up'            => 'Thumbs o up',
		'fa fa-thumbs-up'              => 'Thumbs up',
		'fa fa-ticket'                 => 'Ticket',
		'fa fa-times'                  => 'Times',
		'fa fa-times-circle'           => 'Times circle',
		'fa fa-times-circle-o'         => 'Times circle o',
		'fa fa-tint'                   => 'Tint',
		'fa fa-toggle-off'             => 'Toggle off',
		'fa fa-toggle-on'              => 'Toggle on',
		'fa fa-trademark'              => 'Trademark',
		'fa fa-train'                  => 'Train',
		'fa fa-transgender'            => 'Transgender',
		'fa fa-transgender-alt'        => 'Transgender alt',
		'fa fa-trash'                  => 'Trash',
		'fa fa-trash-o'                => 'Trash o',
		'fa fa-tree'                   => 'Tree',
		'fa fa-trello'                 => 'Trello',
		'fa fa-tripadvisor'            => 'Tripadvisor',
		'fa fa-trophy'                 => 'Trophy',
		'fa fa-truck'                  => 'Truck',
		'fa fa-try'                    => 'Try',
		'fa fa-tty'                    => 'Tty',
		'fa fa-tumblr'                 => 'Tumblr',
		'fa fa-tumblr-square'          => 'Tumblr square',
		'fa fa-twitch'                 => 'Twitch',
		'fa fa-twitter'                => 'Twitter',
		'fa fa-twitter-square'         => 'Twitter square',
		'fa fa-umbrella'               => 'Umbrella',
		'fa fa-underline'              => 'Underline',
		'fa fa-undo'                   => 'Undo',
		'fa fa-university'             => 'University',
		'fa fa-unlock'                 => 'Unlock',
		'fa fa-unlock-alt'             => 'Unlock alt',
		'fa fa-upload'                 => 'Upload',
		'fa fa-usd'                    => 'Usd',
		'fa fa-user'                   => 'User',
		'fa fa-user-md'                => 'User md',
		'fa fa-user-plus'              => 'User plus',
		'fa fa-user-secret'            => 'User secret',
		'fa fa-user-times'             => 'User times',
		'fa fa-users'                  => 'Users',
		'fa fa-venus'                  => 'Venus',
		'fa fa-venus-double'           => 'Venus double',
		'fa fa-venus-mars'             => 'Venus mars',
		'fa fa-viacoin'                => 'Viacoin',
		'fa fa-video-camera'           => 'Video camera',
		'fa fa-vimeo'                  => 'Vimeo',
		'fa fa-vimeo-square'           => 'Vimeo square',
		'fa fa-vine'                   => 'Vine',
		'fa fa-vk'                     => 'Vk',
		'fa fa-volume-down'            => 'Volume down',
		'fa fa-volume-off'             => 'Volume off',
		'fa fa-volume-up'              => 'Volume up',
		'fa fa-weibo'                  => 'Weibo',
		'fa fa-weixin'                 => 'Weixin',
		'fa fa-whatsapp'               => 'Whatsapp',
		'fa fa-wheelchair'             => 'Wheelchair',
		'fa fa-wifi'                   => 'Wifi',
		'fa fa-wikipedia-w'            => 'Wikipedia w',
		'fa fa-windows'                => 'Windows',
		'fa fa-wordpress'              => 'Wordpress',
		'fa fa-wrench'                 => 'Wrench',
		'fa fa-xing'                   => 'Xing',
		'fa fa-xing-square'            => 'Xing square',
		'fa fa-y-combinator'           => 'Y combinator',
		'fa fa-yahoo'                  => 'Yahoo',
		'fa fa-yelp'                   => 'Yelp',
		'fa fa-youtube'                => 'Youtube',
		'fa fa-youtube-play'           => 'Youtube play',
		'fa fa-youtube-square'         => 'Youtube square',
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

add_action( 'template_redirect', 'fed_paypal' );
/**
 * PayPal Request Process.
 * TODO: PayPal
 */
function fed_paypal() {
	if ( isset( $_REQUEST['fed_paypal'], $_REQUEST['fed_display_user_not_paid'] ) ) {
		if ( ! wp_verify_nonce( $_REQUEST['fed_display_user_not_paid'], 'fed_display_user_not_paid' ) ) {
			wp_die( 'Something Went Wrong, Please go back and refresh the page.' );
		}
		$paypal = new paypal_payment();
		$paypal->process_paypal();
	}
	if ( isset( $_REQUEST['paymentId'], $_REQUEST['PayerID'] ) ) {
		$paypal = new paypal_payment();
		$paypal->paypal_success( $_REQUEST );
	}
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

	return '
	<span class="' . $icon . '" data-toggle="popover" data-trigger="hover" title="' . $title . '" data-content="' . $content . '" data-original-title="' . $title . '"></span>
	';
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

function fed_get_public_post_types() {
	$post_type = get_post_types( array( 'public' => true ), 'objects' );
	$new_type  = array();
	foreach ( $post_type as $index => $type ) {
		$new_type[ $index ] = $type->label;
	}

	return $new_type;
}

function fed_check_post_type( $post_type ) {
	$post_types = get_post_types( array( 'public' => true ) );

	return isset( $post_types[ $post_type ] ) ? true : false;
}

function fed_get_current_screen_id() {
	$screen = get_current_screen();

	return $screen->id;
}

function fed_plugin_versions() {
	$versions = apply_filters( 'fed_plugin_versions', array( 'core' => 'Core' ) );

	return implode( ' | ', $versions );
}

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

function fed_request_empty( $request ) {
	if ( '' == trim( $request ) ) {
		return true;
	}

	return false;
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

if (!function_exists('array_column')) {
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
	 * @return array
	 */
	function array_column($input = null, $columnKey = null, $indexKey = null)
	{
		// Using func_get_args() in order to check for proper number of
		// parameters and trigger errors exactly as the built-in array_column()
		// does in PHP 5.5.
		$argc = func_num_args();
		$params = func_get_args();
		if ($argc < 2) {
			trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
			return null;
		}
		if (!is_array($params[0])) {
			trigger_error(
				'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
				E_USER_WARNING
			);
			return null;
		}
		if (!is_int($params[1])
		    && !is_float($params[1])
		    && !is_string($params[1])
		    && $params[1] !== null
		    && !(is_object($params[1]) && method_exists($params[1], '__toString'))
		) {
			trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
			return false;
		}
		if (isset($params[2])
		    && !is_int($params[2])
		    && !is_float($params[2])
		    && !is_string($params[2])
		    && !(is_object($params[2]) && method_exists($params[2], '__toString'))
		) {
			trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
			return false;
		}
		$paramsInput = $params[0];
		$paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
		$paramsIndexKey = null;
		if (isset($params[2])) {
			if (is_float($params[2]) || is_int($params[2])) {
				$paramsIndexKey = (int) $params[2];
			} else {
				$paramsIndexKey = (string) $params[2];
			}
		}
		$resultArray = array();
		foreach ($paramsInput as $row) {
			$key = $value = null;
			$keySet = $valueSet = false;
			if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
				$keySet = true;
				$key = (string) $row[$paramsIndexKey];
			}
			if ($paramsColumnKey === null) {
				$valueSet = true;
				$value = $row;
			} elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
				$valueSet = true;
				$value = $row[$paramsColumnKey];
			}
			if ($valueSet) {
				if ($keySet) {
					$resultArray[$key] = $value;
				} else {
					$resultArray[] = $value;
				}
			}
		}
		return $resultArray;
	}
}
