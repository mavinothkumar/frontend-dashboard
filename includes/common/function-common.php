<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_input_box' ) ) {
	/**
	 * Input Fields.
	 *
	 * @param  string  $meta_key  Meta Key
	 * @param  array  $attr  Input Attributes
	 * @param  string  $type  Input Format.
	 *
	 * @return string
	 */
	function fed_input_box( $meta_key, $attr = array(), $type = 'text' ) {
		/**
		 * Break it, if the input meta doesn't exist
		 */
		if ( empty( $meta_key ) ) {
			wp_die( 'Please add input meta key' );
		}

		$values                = array();
		$values['placeholder'] = isset( $attr['placeholder'] ) && ! empty( $attr['placeholder'] ) ? esc_attr( $attr['placeholder'] ) : '';
		$values['label']       = isset( $attr['label'] ) && ! empty( $attr['label'] ) ? strip_tags( $attr['label'],
			'<i><b>' ) : '';
		$values['class_name']  = isset( $attr['class'] ) && ! empty( $attr['class'] ) ? esc_attr( $attr['class'] ) : '';

		$values['user_value'] = isset( $attr['value'] ) && ! empty( $attr['value'] ) ? esc_attr( $attr['value'] ) : '';
		$values['input_min']  = isset( $attr['min'] ) && ! empty( $attr['min'] ) ? esc_attr( $attr['min'] ) : 0;
		$values['input_max']  = isset( $attr['max'] ) && ! empty( $attr['max'] ) ? esc_attr( $attr['max'] ) : 99999999999999999999999999999999999999999999999999;
		$values['input_step'] = isset( $attr['step'] ) && ! empty( $attr['step'] ) ? esc_attr( $attr['step'] ) : 'any';

		$values['is_required']   = isset( $attr['is_required'] ) && $attr['required'] == 'true' ? 'required="required"' : '';
		$values['id_name']       = isset( $attr['id'] ) && ! empty( $attr['id'] ) ? esc_attr( $attr['id'] ) : '';
		$values['readonly']      = isset( $attr['readonly'] ) && $attr['readonly'] === true ? true : '';
		$values['user_value']    = isset( $attr['value'] ) && ! empty( $attr['value'] ) ? esc_attr( $attr['value'] ) : '';
		$values['input_value']   = isset( $attr['options'] ) && ! empty( $attr['options'] ) ? $attr['options'] : '';
		$values['disabled']      = isset( $attr['disabled'] ) && $attr['disabled'] === true ? true : '';
		$values['default_value'] = isset( $attr['default_value'] ) && ! empty( $attr['default_value'] ) ? esc_attr( $attr['default_value'] ) : 'yes';
		$values['extra']         = isset( $attr['extra'] ) ? $attr['extra'] : '';
		$values['extended']      = isset( $attr['extended'] ) && ! empty( $attr['extended'] ) ? esc_attr( $attr['extended'] ) : array();
		$values['input_type']    = $type;
		$values['input_meta']    = isset( $attr['name'] ) ? $attr['name'] : $meta_key;
		$values['content']       = isset( $attr['content'] ) ? $attr['content'] : '';

		return fed_get_input_details( $values );
	}
}
if ( ! function_exists( 'fed_loader' ) ) {
	/**
	 * Loader.
	 *
	 * @param  string  $hide
	 *
	 * @return string
	 */
	function fed_loader( $hide = 'hide', $message = null ) {
		$html = '<div class="preview-area ' . $hide . '">
        <div class="spinner_circle">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>';

		if ( $message ) {
			$html .= '<div class="fed_loader_message hide">' . $message . '</div>';
		}

		$html .= '</div>';

		return $html;
	}
}
if ( ! function_exists( 'fed_sort_by_order' ) ) {
	/**
	 * Sort given array by order key
	 */
	function fed_sort_by_order( $a, $b ) {
		if ( isset( $a['order'], $b['order'] ) ) {
			return $a['order'] - $b['order'];
		}
		if ( isset( $a['input_order'], $b['input_order'] ) ) {
			return $a['input_order'] - $b['input_order'];
		}
		if ( isset( $a['menu_order'], $b['menu_order'] ) ) {
			return $a['menu_order'] - $b['menu_order'];
		}

		return 199;
	}
}

if ( ! function_exists( 'fed_sort_by_desc' ) ) {
	/**
	 * Sort by Desc
	 *
	 * @param  array  $a  First Element.
	 * @param  array  $b  Second Element.
	 *
	 * @param  string  $key
	 *
	 * @return int
	 */
	function fed_sort_by_desc( $a, $b ) {
		if ( isset( $a['id'], $b['id'] ) ) {
			return (int) $b['id'] - (int) $a['id'];
		}

		return 199;
	}
}

if ( ! function_exists( 'fed_wp_nonce_field' ) ) {
	/**
	 * @param  int  $action
	 * @param  string  $name
	 * @param  bool  $referer
	 * @param  bool  $echo
	 *
	 * @return string
	 */
	function fed_wp_nonce_field( $action = 'fed_nonce', $name = "fed_nonce", $referer = true, $echo = true ) {
		$name        = esc_attr( $name );
		$nonce_field = '<input type="hidden" name="' . $name . '" value="' . wp_create_nonce( $action ) . '" />';

		if ( $referer ) {
			$nonce_field .= wp_referer_field( false );
		}

		if ( $echo ) {
			echo $nonce_field;
		}

		return $nonce_field;
	}
}

if ( ! function_exists( 'fed_get_random_string' ) ) {
	/**
	 * Generate Random String
	 *
	 * @param  int  $length
	 *
	 * @return string
	 */
	function fed_get_random_string( $length = 10 ) {
		$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString     = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
		}

		return $randomString;
	}
}


if ( ! function_exists( 'fed_is_shortcode_in_content' ) ) {
	/**
	 * @param  null  $shortcodes
	 *
	 * @return bool
	 */
	function fed_is_shortcode_in_content( $shortcodes = null ) {
		global $post;
		if ( $shortcodes === null ) {
			$shortcodes = fed_shortcode_lists();
		}

		if ( is_array( $shortcodes ) && is_a( $post, 'WP_Post' ) ) {
			foreach ( $shortcodes as $shortcode ) {
				if ( has_shortcode( $post->post_content, $shortcode ) ) {
					return true;
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'fed_shortcode_lists' ) ) {
	/**
	 * @return mixed|void
	 */
	function fed_shortcode_lists() {
		return apply_filters( 'fed_shortcode_lists', array(
			'fed_login',
			'fed_login_only',
			'fed_register_only',
			'fed_forgot_password_only',
			'fed_dashboard',
			'fed_user',
			'fed_transactions',
		) );
	}
}


if ( ! function_exists( 'fed_js_translation' ) ) {
	/**
	 * @return array
	 */
	function fed_js_translation() {
		return array(
			'plugin_url'          => plugins_url( BC_FED_PLUGIN_NAME ),
			'payment_info'        => 'no',
			'fed_admin_form_post' => admin_url( 'admin-ajax.php?action=fed_admin_form_post&nonce=' . wp_create_nonce( "fed_admin_form_post" ) ),
			'fed_login_form_post' => admin_url( 'admin-ajax.php?action=fed_login_form_post&nonce=' . wp_create_nonce( "fed_login_form_post" ) ),
			'alert'               => array(
				'confirmation'                  => array(
					'title'   => __( 'Are you sure?', 'frontend-dashboard' ),
					'text'    => __( 'You want to do this action?', 'frontend-dashboard' ),
					'confirm' => __( 'Yes, Please Proceed', 'frontend-dashboard' ),
					'cancel'  => __( 'No, Cancel it', 'frontend-dashboard' ),
				),
				'redirecting'                   => __( 'Please wait, you are redirecting..', 'frontend-dashboard' ),
				'title_cancelled'               => __( 'Cancelled', 'frontend-dashboard' ),
				'something_went_wrong'          => __( 'Something Went Wrong', 'frontend-dashboard' ),
				'invalid_form_submission'       => __( 'Invalid form submission', 'frontend-dashboard' ),
				'please_try_again'              => __( 'Please try again', 'frontend-dashboard' ),
				'plugin_installed_successfully' => __( 'Plugin Installed and Activated Successfully',
					'frontend-dashboard' ),
			),
			'common'              => array(
				'hide_add_new_menu' => __( 'Hide Add New Menu', 'frontend-dashboard' ),
				'add_new_menu'      => __( 'Add New Menu', 'frontend-dashboard' ),
			),
		);
	}
}

if ( ! function_exists( 'fed_get_ajax_form_action' ) ) {
	/**
	 * @param  null  $action
	 *
	 * @return string|void
	 */
	function fed_get_ajax_form_action( $action = null ) {
		if ( $action ) {
			return admin_url( 'admin-ajax.php?action=' . $action );
		}

		return '#';
	}
}

if ( ! function_exists( 'fed_get_form_action' ) ) {
	/**
	 * @param  null  $action
	 *
	 * @return string|void
	 */
	function fed_get_form_action( $action = null ) {
		if ( $action ) {
			return get_admin_url() . 'admin-post.php?action=' . $action;
		}

		return '#';
	}
}


if ( ! function_exists( 'fed_is_current_user_role' ) ) {

	/**
	 * @param  array  $allowed_roles
	 * @param  bool  $is_key
	 *
	 * @return bool
	 */
	function fed_is_current_user_role( array $allowed_roles, $is_key = true ) {
		if ( count( $allowed_roles ) ) {
			$user          = fed_get_current_user_role();
			$allowed_roles = $is_key ? array_keys( $allowed_roles ) : $allowed_roles;
			if ( array_intersect( $allowed_roles, $user ) ) {
				return true;
			}
		}

		return false;
	}
}


if ( ! function_exists( 'fed_is_admin' ) ) {
	/**
	 * @return bool
	 */
	function fed_is_admin() {
		$user = wp_get_current_user();

		return in_array( 'administrator', $user->roles ) ? true : false;

	}
}

if ( ! function_exists( 'fed_get_default_menu_type' ) ) {
	/**
	 * @return mixed|void
	 */
	function fed_get_default_menu_type() {
		return apply_filters( 'fed_get_default_menu_type', array(
			'post',
			'user',
			'logout',
			'collapse',
			'custom',
		) );
	}
}

if ( ! function_exists( 'fed_menu_split' ) ) {
	/**
	 * @param $string
	 *
	 * @return array
	 */
	function fed_menu_split( $string ) {
		return explode( '_', $string, 2 );
	}
}

if ( ! function_exists( 'fed_get_menu_mobile_attributes' ) ) {
	/**
	 * @return array
	 */
	function fed_get_menu_mobile_attributes() {
		$collapse = array();
		if ( wp_is_mobile() ) {
			$collapse['in']     = '';
			$collapse['d']      = ' collapsed';
			$collapse['expand'] = 'false';
		} else {
			$collapse['in']     = 'in';
			$collapse['d']      = ' ';
			$collapse['expand'] = 'true';
		}

		return $collapse;
	}
}


if ( ! function_exists( 'bcdump' ) ) {
	/**
	 * @param $var
	 * @param  bool  $exit
	 */
	function bcdump( $var, $exit = false ) {
		echo '<pre style="font-size:11px;">';

		if ( is_array( $var ) || is_object( $var ) ) {
			echo htmlentities( print_r( $var, true ) );
		} elseif ( is_string( $var ) ) {
			echo "string(" . strlen( $var ) . ") \"" . htmlentities( $var ) . "\"\n";
		} else {
			var_dump( $var );
		}

		echo "\n</pre>";

		if ( $exit ) {
			exit;
		}
	}
}

if ( ! function_exists( 'fed_create_new_instance' ) ) {
	/**
	 * @param $className
	 * @param  array  $arguments
	 *
	 * @return bool|mixed
	 */
	function fed_create_new_instance( $className, array $arguments = array() ) {
		if ( class_exists( $className ) ) {
			try {
				return call_user_func_array( array(
					new ReflectionClass( $className ),
					'newInstance',
				),
					$arguments );
			} catch ( ReflectionException $e ) {
				wp_die( 'Class Name ' . $className . ' Doesnt exist' );
			}
		}

		wp_die( 'Class Name ' . $className . ' Doesnt exist' );
	}
}

if ( ! function_exists( 'fed_menu_page_url' ) ) {
	/**
	 * @param $page_slug
	 *
	 * @param  string  $parameters
	 *
	 * @return string|void
	 */
	function fed_menu_page_url( $page_slug, $parameters = null ) {
		if ( is_array( $parameters ) && count( $parameters ) ) {
			return admin_url( '/admin.php?page=' . $page_slug ) . '&' . http_build_query( $parameters );
		}

		return admin_url( '/admin.php?page=' . $page_slug );
	}
}

if ( ! function_exists( 'fed_encrypt' ) ) {
	/**
	 * @param $string
	 *
	 * @return bool|string
	 */
	function fed_encrypt( $string ) {
		$secret_key = wp_create_nonce();
		$secret_iv  = fed_generate_secret();

		$encrypt_method = "AES-128-CBC";
		$key            = hash( 'sha256', $secret_key );
		$iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		return base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );

	}
}

if ( ! function_exists( 'fed_decrypt' ) ) {
	/**
	 * @param $string
	 *
	 * @return string
	 */
	function fed_decrypt( $string ) {
		$secret_key = wp_create_nonce();
		$secret_iv  = fed_generate_secret();

		$encrypt_method = "AES-128-CBC";
		$key            = hash( 'sha256', $secret_key );
		$iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		return openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}
}

if ( ! function_exists( 'fed_generate_secret' ) ) {
	/**
	 * @return mixed|string
	 */
	function fed_generate_secret() {
		if ( isset( $_SESSION['fed_secret'] ) && ! empty( $_SESSION['fed_secret'] ) ) {
			return $_SESSION['fed_secret'];
		}
		$_SESSION['fed_secret'] = fed_get_random_string( 5 );

		return $_SESSION['fed_secret'];
	}
}

if ( ! function_exists( 'fed_add_admin_notifications' ) ) {
	/**
	 * @param  array  $array
	 */
	function fed_add_admin_notifications( array $array ) {
		$_SESSION['fed_admin_errors'] = $array;
	}
}

add_action( 'init', 'fed_check_admin_notifications' );
function fed_check_admin_notifications() {
	if ( isset( $_SESSION['fed_admin_errors'] ) && count( $_SESSION['fed_admin_errors'] ) > 0 ) {
		add_action( 'admin_notices', 'fed_show_notifications_message' );
	}
}

/**
 * @return string
 */
function fed_show_notifications_message() {
	?>
    <div class="error notice">
        <p><?php echo fed_convert_array_value_to_string( $_SESSION['fed_admin_errors'], ',' ); ?></p>
    </div>
	<?php
	fed_clear_admin_notification();
}

function fed_clear_admin_notification() {
	if ( isset( $_SESSION['fed_admin_errors'] ) ) {
		$_SESSION['fed_admin_errors'] = array();
	}
}


/**
 * Recursively implodes an array with optional key inclusion
 *
 * Example of $include_keys output: key, value, key, value, key, value
 *
 * @access  public
 *
 * @param  array  $array  multi-dimensional array to recursively implode
 * @param  string  $glue  value that glues elements together
 * @param  bool  $include_keys  include keys before their values
 * @param  bool  $trim_all  trim ALL whitespace from string
 *
 * @return  string  imploded array
 */
function fed_convert_array_value_to_string( array $array, $glue = ',', $include_keys = false, $trim_all = false ) {
	$glued_string = '';
	// Recursively iterates array and adds key/value to glued string
	array_walk_recursive( $array, function ( $value, $key ) use ( $glue, $include_keys, &$glued_string ) {
		$include_keys and $glued_string .= $key . $glue;
		$glued_string .= $value . $glue;
	} );
	// Removes last $glue from string
	strlen( $glue ) > 0 and $glued_string = substr( $glued_string, 0, - strlen( $glue ) );
	// Trim ALL whitespace
	$trim_all and $glued_string = preg_replace( "/(\s)/ixsm", '', $glued_string );

	return (string) $glued_string;
}

/**
 * @param $message
 * @param  string  $type
 */
function fed_show_alert_message( $message, $type = 'danger' ) {
	?>
    <div class="alert alert-<?php echo $type; ?>">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong><?php echo $message; ?></strong>
    </div>
	<?php
}


/**
 * @return array
 */
function fed_illegal_usernames() {
	$login = get_option( 'fed_admin_login' );
	FED_Log::writeLog( [ '$login' => $login ] );
	$illegal = isset( $login['restrict_username'] ) && ! empty( $login['restrict_username'] ) ? explode( ',',
		$login['restrict_username'] ) : array();

	return apply_filters( 'fed_illegal_user_names', $illegal );
}

/**
 *
 * @param $username
 *
 * @return bool
 */
function fed_validate_username( $username ) {
	$result = preg_grep( '#' . $username . '#i', fed_illegal_usernames() );

	if ( $result && count( $result ) ) {
		return true;
	}

	return false;
}


if ( ! function_exists( 'fed_is_shortcode_in_page' ) ) {
	/**
	 * @param $shorcode
	 *
	 * @return bool
	 */
	function fed_is_shortcode_in_page( $shorcode ) {
		global $post;
		if ( has_shortcode( $post->post_content, $shorcode ) ) {
			return true;
		}

		return false;
	}
}

/**
 * @param  string  $key
 *
 * @return bool|string
 */
function fed_get_current_user( $key = 'id' ) {
	$current_user = wp_get_current_user();
	if ( ! ( $current_user instanceof WP_User ) ) {
		return false;
	}

	switch ( $key ) {
		case 'email':
			return esc_html( $current_user->user_email );
			break;
		case 'username':
			return esc_html( $current_user->user_login );
			break;
		case 'firstname':
			return esc_html( $current_user->user_firstname );
			break;
		case 'lastname':
			return esc_html( $current_user->user_lastname );
			break;
		case 'display_name':
			return esc_html( $current_user->display_name );
			break;
		case 'id':
			return esc_html( $current_user->ID );
			break;
	}

	return false;
}