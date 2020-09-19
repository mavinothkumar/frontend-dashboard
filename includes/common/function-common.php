<?php
/**
 * Function.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_input_box' ) ) {
	/**
	 * Input Fields.
	 *
	 * @param  string $meta_key  Meta Key.
	 * @param  array  $attr  Input Attributes.
	 * @param  string $type  Input Format.
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
		$values['placeholder'] = isset( $attr['placeholder'] ) && ! empty( $attr['placeholder'] ) ? esc_attr(
			$attr['placeholder']
		) : '';
		$values['label']       = isset( $attr['label'] ) && ! empty( $attr['label'] ) ? strip_tags(
			$attr['label'],
			'<i><b>'
		) : '';
		$values['class_name']  = isset( $attr['class'] ) && ! empty( $attr['class'] ) ? esc_attr( $attr['class'] ) : '';

		$values['user_value'] = isset( $attr['value'] ) && ! empty( $attr['value'] ) ? esc_attr( $attr['value'] ) : '';
		$values['input_min']  = isset( $attr['min'] ) && ! empty( $attr['min'] ) ? esc_attr( $attr['min'] ) : 0;
		$values['input_max']  = isset( $attr['max'] ) && ! empty( $attr['max'] ) ? esc_attr(
			$attr['max']
		) : 99999999999999999999999999999999999999999999999999;
		$values['input_step'] = isset( $attr['step'] ) && ! empty( $attr['step'] ) ? esc_attr( $attr['step'] ) : 'any';

		$values['is_required']   = isset( $attr['is_required'] ) && ( 'true' == $attr['required'] ) ? 'required="required"' : '';
		$values['id_name']       = isset( $attr['id'] ) && ! empty( $attr['id'] ) ? esc_attr( $attr['id'] ) : '';
		$values['readonly']      = isset( $attr['readonly'] ) && ( true === $attr['readonly'] ) ? true : '';
		$values['user_value']    = isset( $attr['value'] ) && ! empty( $attr['value'] ) ? esc_attr(
			$attr['value']
		) : '';
		$values['input_value']   = isset( $attr['options'] ) && ! empty( $attr['options'] ) ? $attr['options'] : '';
		$values['disabled']      = isset( $attr['disabled'] ) && ( true === $attr['disabled'] ) ? true : '';
		$values['default_value'] = isset( $attr['default_value'] ) && ! empty( $attr['default_value'] ) ? esc_attr(
			$attr['default_value']
		) : 'yes';
		$values['extra']         = isset( $attr['extra'] ) ? $attr['extra'] : '';
		$values['extended']      = isset( $attr['extended'] ) && ! empty( $attr['extended'] ) ? esc_attr(
			$attr['extended']
		) : array();
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
	 * @param  string $hide  Hide.
	 *
	 * @param  null   $message  Message.
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
	 * Sort given array by order key.
	 *
	 * @param  array $a  First Value.
	 * @param  array $b  Second Value.
	 *
	 * @return int | string.
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
	 * Sort by Desc.
	 *
	 * @param  array $a  First Element.
	 * @param  array $b  Second Element.
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
	 * WP Nonce Field.
	 *
	 * @param  int | string $action  Action.
	 * @param  string       $name  Name.
	 * @param  bool         $referer  Referer.
	 * @param  bool         $echo  Echo.
	 *
	 * @return string
	 */
	function fed_wp_nonce_field( $action = 'fed_nonce', $name = 'fed_nonce', $referer = true, $echo = true ) {
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
	 * Generate Random String.
	 *
	 * @param  int $length  Length.
	 *
	 * @return string
	 */
	function fed_get_random_string( $length = 10 ) {
		$characters        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$characters_length = strlen( $characters );
		$random_string     = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$random_string .= $characters[ rand( 0, $characters_length - 1 ) ];
		}

		return $random_string;
	}
}


if ( ! function_exists( 'fed_is_shortcode_in_content' ) ) {
	/**
	 * Is Shortcode in Content.
	 *
	 * @param  mixed $shortcodes  Shortcodes.
	 *
	 * @return bool
	 */
	function fed_is_shortcode_in_content( $shortcodes = null ) {
		global $post;
		if ( null === $shortcodes ) {
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
	 * Shortcode Lists.
	 *
	 * @return mixed|void
	 */
	function fed_shortcode_lists() {
		return apply_filters(
			'fed_shortcode_lists', array(
				'fed_login',
				'fed_login_only',
				'fed_register_only',
				'fed_forgot_password_only',
				'fed_dashboard',
				'fed_user',
				'fed_transactions',
			)
		);
	}
}


if ( ! function_exists( 'fed_js_translation' ) ) {
	/**
	 * JS Translation.
	 *
	 * @return array
	 */
	function fed_js_translation() {
		return array(
			'plugin_url'          => plugins_url( BC_FED_PLUGIN_NAME ),
			'payment_info'        => 'no',
			'fed_admin_form_post' => admin_url(
				'admin-ajax.php?action=fed_admin_form_post&fed_nonce=' . wp_create_nonce( 'fed_nonce' )
			),
			'fed_login_form_post' => admin_url(
				'admin-ajax.php?action=fed_login_form_post&fed_nonce=' . wp_create_nonce( 'fed_nonce' )
			),
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
				'plugin_installed_successfully' => __(
					'Plugin Installed and Activated Successfully',
					'frontend-dashboard'
				),
			),
			'common'              => array(
				'hide_add_new_menu'    => __( 'Hide Add New Menu', 'frontend-dashboard' ),
				'add_new_menu'         => __( 'Add New Menu', 'frontend-dashboard' ),
				'successfully_updated' => __( 'Successfully Updated', 'frontend-dashboard' ),
				'successfully_added'   => __( 'Successfully Added', 'frontend-dashboard' ),
			),
			'password_meter'      => array(
				'empty'    => __( 'Strength indicator', 'frontend-dashboard' ),
				'short'    => __( 'Very weak', 'frontend-dashboard' ),
				'bad'      => __( 'Weak', 'frontend-dashboard' ),
				'good'     => _x( 'Medium', 'password strength', 'frontend-dashboard' ),
				'strong'   => __( 'Strong', 'frontend-dashboard' ),
				'mismatch' => __( 'Mismatch', 'frontend-dashboard' ),
			),
		);
	}
}

if ( ! function_exists( 'fed_get_ajax_form_action' ) ) {
	/**
	 * Fed Get AJAX Form Action.
	 *
	 * @param  null $action  Action.
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
	 * Get Form Action.
	 *
	 * @param  null $action  Action.
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
	 * Is Current User Role.
	 *
	 * @param  array $allowed_roles  Allowed Roles.
	 * @param  bool  $is_key  Is Key.
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
	 * Is Admin.
	 *
	 * @return bool
	 */
	function fed_is_admin() {
		$user = wp_get_current_user();

		return in_array( 'administrator', $user->roles ) ? true : false;

	}
}

if ( ! function_exists( 'fed_is_user_role' ) ) {
	/**
	 * Check is User Role.
	 *
	 * @param  string $user_role  user role.
	 *
	 * @return bool
	 */
	function fed_is_user_role( $user_role ) {
		$user = wp_get_current_user();

		return in_array( $user_role, $user->roles ) ? true : false;

	}
}

if ( ! function_exists( 'fed_get_default_menu_type' ) ) {
	/**
	 * Get Default Menu Type.
	 *
	 * @return mixed|void
	 */
	function fed_get_default_menu_type() {
		return apply_filters(
			'fed_get_default_menu_type', array(
				'post',
				'user',
				'logout',
				'collapse',
				'custom',
			)
		);
	}
}

if ( ! function_exists( 'fed_menu_split' ) ) {
	/**
	 * Menu Split.
	 *
	 * @param  string $string  String.
	 *
	 * @return array
	 */
	function fed_menu_split( $string ) {
		return explode( '_', $string, 2 );
	}
}

if ( ! function_exists( 'fed_get_menu_mobile_attributes' ) ) {
	/**
	 * Get Menu Mobile Attributes.
	 *
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
	/** Dump.
	 *
	 * @param  mixed $var  Variable.
	 * @param  bool  $exit  Exit.
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
	 * Create New Instance.
	 *
	 * @param  mixed $class_name  Class Name.
	 * @param  array $arguments  Argument.
	 *
	 * @return bool|mixed
	 */
	function fed_create_new_instance( $class_name, array $arguments = array() ) {
		if ( class_exists( $class_name ) ) {
			try {
				return call_user_func_array(
					array(
						new ReflectionClass( $class_name ),
						'newInstance',
					),
					$arguments
				);
			} catch ( ReflectionException $e ) {
				wp_die( 'Class Name ' . esc_attr( $class_name ) . ' Doesnt exist' );
			}
		}

		wp_die( 'Class Name ' . esc_attr( $class_name ) . ' Doesnt exist' );
	}
}

if ( ! function_exists( 'fed_menu_page_url' ) ) {
	/**
	 * Menu Page URL
	 *
	 * @param  string $page_slug  Page Slug.
	 *
	 * @param  mixed  $parameters  Parameters.
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
	 * Encrypt.
	 *
	 * @param  string $string  String.
	 *
	 * @return bool|string
	 */
	function fed_encrypt( $string ) {
		$secret_key = wp_create_nonce();
		$secret_iv  = fed_generate_secret();

		$encrypt_method = 'AES-128-CBC';
		$key            = hash( 'sha256', $secret_key );
		$iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		return base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );

	}
}

if ( ! function_exists( 'fed_decrypt' ) ) {
	/**
	 * Decrypt.
	 *
	 * @param  string $string  String.
	 *
	 * @return string
	 */
	function fed_decrypt( $string ) {
		$secret_key = wp_create_nonce();
		$secret_iv  = fed_generate_secret();

		$encrypt_method = 'AES-128-CBC';
		$key            = hash( 'sha256', $secret_key );
		$iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		return openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}
}

if ( ! function_exists( 'fed_generate_secret' ) ) {
	/**
	 * Generate Secret.
	 *
	 * @return mixed|string
	 */
	function fed_generate_secret() {
		$session = fed_sanitize_text_field( $_SESSION );
		if ( isset( $session['fed_secret'] ) && ! empty( $session['fed_secret'] ) ) {
			return $session['fed_secret'];
		}
		$session['fed_secret'] = fed_get_random_string( 5 );

		return $session['fed_secret'];
	}
}

if ( ! function_exists( 'fed_add_admin_notifications' ) ) {
	/**
	 * Add Admin Notifications.
	 *
	 * @param  array $array  Array.
	 */
	function fed_add_admin_notifications( array $array ) {
		$_SESSION['fed_admin_errors'] = fed_sanitize_text_field( $array );
	}
}

add_action( 'init', 'fed_check_admin_notifications' );
/**
 * Check Admin Notifications.
 */
function fed_check_admin_notifications() {
	if ( isset( $_SESSION['fed_admin_errors'] ) && count( $_SESSION['fed_admin_errors'] ) > 0 ) {
		add_action( 'admin_notices', 'fed_show_notifications_message' );
	}
}

/**
 * Show Notifications Message.
 */
function fed_show_notifications_message() {
	?>
	<div class="error notice">
		<p>
			<?php echo fed_convert_array_value_to_string( $_SESSION['fed_admin_errors'], ',' ); ?>
		</p>
	</div>
	<?php
	fed_clear_admin_notification();
}

/**
 * Clear Admin Notification.
 */
function fed_clear_admin_notification() {
	if ( isset( $_SESSION['fed_admin_errors'] ) ) {
		$_SESSION['fed_admin_errors'] = array();
	}
}


/**
 * Recursively implodes an array with optional key inclusion.
 *
 * Example of $include_keys output: key, value, key, value, key, value
 *
 * @access  public
 *
 * @param  array  $array  multi-dimensional array to recursively implode.
 * @param  string $glue  value that glues elements together.
 * @param  bool   $include_keys  include keys before their values.
 * @param  bool   $trim_all  trim ALL whitespace from string.
 *
 * @return  string  imploded array.
 */
function fed_convert_array_value_to_string( array $array, $glue = ',', $include_keys = false, $trim_all = false ) {
	$glued_string = '';
	// Recursively iterates array and adds key/value to glued string.
	array_walk_recursive(
		$array, function ( $value, $key ) use ( $glue, $include_keys, &$glued_string ) {
		$include_keys and $glued_string .= $key . $glue;
		$glued_string .= $value . $glue;
	}
	);
	// Removes last $glue from string.
	strlen( $glue ) > 0 and $glued_string = substr( $glued_string, 0, - strlen( $glue ) );
	// Trim ALL whitespace.
	$trim_all and $glued_string = preg_replace( "/(\s)/ixsm", '', $glued_string );

	return (string) $glued_string;
}

/**
 * Show Alert Message.
 *
 * @param  string $message  Message.
 * @param  string $type  Type.
 */
function fed_show_alert_message( $message, $type = 'danger' ) {
	?>
	<div class="alert alert-<?php echo esc_attr( $type ); ?>">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<strong><?php echo wp_kses_post( $message ); ?></strong>
	</div>
	<?php
}


/**
 * Illegal User Names.
 *
 * @return array
 */
function fed_illegal_usernames() {
	$login   = get_option( 'fed_admin_login' );
	$illegal = isset( $login['restrict_username'] ) && ! empty( $login['restrict_username'] ) ? explode(
		',',
		$login['restrict_username']
	) : array();

	return apply_filters( 'fed_illegal_user_names', $illegal );
}

/**
 * Validate User Name.
 *
 * @param  string $username  user Name.
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
	 * Is Shortcode in Page.
	 *
	 * @param  string $shorcode  Shortcode.
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
 * Get Current User.
 *
 * @param  string $key  User Column Key.
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

/**
 * Convert Timestamp to Date Format.
 *
 * @param  string $timestamp  Timestamp.
 *
 * @return false|string
 */
function fed_timestamp_to_date_format( $timestamp ) {
	return $timestamp && ! empty( $timestamp ) ? date( get_option( 'date_format' ), $timestamp ) : 'ERROR';
}

/**
 * Get Formatted Date,
 *
 * @param  string $date  Date.
 *
 * @return false|string
 */
function fed_get_formatted_date( $date ) {
	return $date && ! empty( $date ) ? date( get_option( 'date_format' ), strtotime( $date ) ) : 'ERROR';
}


/**
 * Get Menu URL by Slug
 *
 * @param  string $menu_slug  Menu Slug.
 * @param  string $menu_type  Menu Type.
 *
 * @return array|array[]|bool|mixed|string|\WP_Error
 */
function fed_get_menu_url_by_slug( $menu_slug, $menu_type ) {
	if ( $menu_slug && ! empty( $menu_slug ) && is_user_logged_in() ) {
		$menu          = wp_cache_get( 'fed_dashboard_menu_' . get_current_user_id(), 'frontend-dashboard' );
		$dashboard_url = fed_get_dashboard_url();
		if ( $menu && isset( $menu['menu_items'] ) && is_array( $menu ) && count( $menu ) ) {
			foreach ( $menu['menu_items'] as $key => $item ) {
				if ( $item['menu_slug'] === $menu_slug ) {
					return add_query_arg(
						array(
							'menu_type' => $menu_type,
							'menu_slug' => $menu_slug,
							'menu_id'   => fed_get_data( 'id', $item, 0 ),
							'parent_id' => $key,
							'fed_nonce' => wp_create_nonce(
								'fed_nonce'
							),
						), $dashboard_url
					);
				}
			}

			return false;
		} else {
			$dashboard_container = new FED_Routes( $_REQUEST );

			$menu = $dashboard_container->setDashboardMenuQuery();

			foreach ( $menu['menu_items'] as $key => $item ) {
				if ( $item['menu_slug'] === $menu_slug ) {
					return add_query_arg(
						array(
							'menu_type' => $menu_type,
							'menu_slug' => $menu_slug,
							'menu_id'   => fed_get_data( 'id', $item, 0 ),
							'parent_id' => $key,
							'fed_nonce' => wp_create_nonce(
								'fed_nonce'
							),
						), $dashboard_url
					);
				}
			}

			return false;
		}
	}

	return false;
}


/**
 * WordPress VIP Functions.
 */


/**
 * Get User Meta
 *
 * @param  int    $user_id  User ID
 * @param  string $key  User Meta Key
 * @param  bool   $single  Single or array
 *
 * @return mixed
 */
function fed_get_user_meta( $user_id, $key = '', $single = false ) {
	if ( defined( 'WPCOM_IS_VIP_ENV' ) && WPCOM_IS_VIP_ENV ) {
		return get_user_attribute( $user_id, $key );
	}

	return get_user_meta( $user_id, $key, $single );
}

/**
 * Show Password Meter
 */

add_action( 'fed_register_below_form_field', function ( $input_meta, $content ) {
	if ( $content && ( 'user_pass' === $input_meta || 'confirmation_password' === $input_meta ) ) {
		?>
		<span class="fed_password_strength"></span>
		<?php
	}
}, 10, 2 );
