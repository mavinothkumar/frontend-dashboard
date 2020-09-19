<?php
/**
 * Function.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Get Avatar.
 *
 * @param  string $id_or_email  ID or Email.
 * @param  string $alt  Alt.
 * @param  string $class  Class.
 * @param  string $extra  Extra.
 * @param  string $size  Size.
 * @param  string $attr  Attr.
 *
 * @return string
 */
function fed_get_avatar( $id_or_email, $alt = '', $class = '', $extra = '', $size = '', $attr = '' ) {
	$fed_upl   = get_option( 'fed_admin_settings_upl' );
	$user_data = $id_or_email;

	if ( $url = get_user_meta( $id_or_email, 'fed_user_profile_image', true ) ) {
		// send the default image.
		return sprintf(
			"<img alt='%s' src='%s' class='%s' %s />",
			esc_attr( $alt ),
			esc_url( $url ),
			esc_attr( $class ),
			$extra
		);
	}

	if ( filter_var( $user_data, FILTER_VALIDATE_EMAIL ) ) {
		$user_id   = get_user_by( 'email', $id_or_email );
		$user_data = $user_id->ID;
	}

	if (
		isset( $fed_upl['settings']['fed_upl_change_profile_pic'] ) &&
		$fed_upl['settings']['fed_upl_change_profile_pic'] &&
		! empty( $fed_upl['settings']['fed_upl_change_profile_pic'] )
	) {
		$user_obj = get_userdata( $user_data );

		$gavatar_id = $user_obj->has_prop( $fed_upl['settings']['fed_upl_change_profile_pic'] ) ?
			$user_obj->get( $fed_upl['settings']['fed_upl_change_profile_pic'] ) :
			'';

		if ( ! empty( $gavatar_id ) && $gavatar_id ) {
			return wp_get_attachment_image( (int) $gavatar_id, $size, false, $attr );
		}

		// send the default image.
		return sprintf(
			"<img alt='%s' src='%s' class='%s' %s />",
			esc_attr( $alt ),
			esc_url( 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&f=y&s=600' ),
			esc_attr( $class ),
			$extra
		);
	}

	$get_avatar = get_avatar_data( $id_or_email, array( 'size' => 600 ) );

	return sprintf(
		"<img alt='%s' src='%s' class='%s' %s />",
		esc_attr( $alt ),
		esc_url( $get_avatar['url'] ),
		esc_attr( $class ),
		$extra
	);

}

/**
 * Get Current Page URL.
 *
 * @return string
 */
function fed_get_current_page_url() {
	global $wp;

	return home_url( add_query_arg( array(), $wp->request ) );
}

/**
 * Current Page URL.
 *
 * @return string
 */
function fed_current_page_url() {

	global $wp;

	$query_string = isset( $_SERVER['QUERY_STRING'] ) ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : array();

	return add_query_arg( $query_string, '', home_url( $wp->request ) );
}

/**
 * Get Registration content.
 *
 * @return array
 */
function fed_get_registration_content_fields() {
	$details         = fed_fetch_user_profile_by_registration();
	$fed_admin_login = get_option( 'fed_admin_login' );
	$role            = fed_role_with_pricing_flat( $fed_admin_login );

	if ( $details instanceof WP_Error ) {
		wp_die( esc_attr__( 'Default Tables not installed, please contact support', 'frontend-dashboard' ) );
	}

	$registration = array();
	foreach ( $details as $detail ) {
		$registration[ $detail['input_meta'] ] = array(
			'name'        => $detail['label_name'],
			'input'       => fed_get_input_details(
				array(
					'input_meta'  => $detail['input_meta'],
					'placeholder' => $detail['placeholder'],
					'class'       => $detail['class_name'],
					'id'          => $detail['id_name'],
					'is_required' => $detail['is_required'],
					'step'        => $detail['input_step'],
					'input_min'   => $detail['input_min'],
					'input_max'   => $detail['input_max'],
					'input_rows'  => $detail['input_row'],
					'user_value'  => $detail['input_value'],
					'input_type'  => $detail['input_type'],
					'input_value' => $detail['input_value'],
					'extended'    => $detail['extended'],
				)
			),
			'input_order' => $detail['input_order'],
			'extended'    => $detail['extended'],
			'input_type'  => $detail['input_type'],
			'input_meta'  => $detail['input_meta'],
		);
	}

	if ( $role ) {
		$registration['role'] = array(
			'name'        => $fed_admin_login['register']['name'],
			'input'       => fed_input_box( 'role', array( 'options' => $role ), 'select' ),
			'input_order' => $fed_admin_login['register']['position'],
		);
	}

	/**
	 * Hidden text to make sure its a registration form
	 */
	$registration['fed_registration_form'] = array(
		'name'        => '',
		'input'       => fed_get_input_details(
			array(
				'input_meta'  => 'fed_registration_form',
				'placeholder' => '',
				'class'       => '',
				'id'          => '',
				'is_required' => 'true',
				'input_step'  => '',
				'input_min'   => '',
				'input_max'   => '',
				'rows'        => '',
				'user_value'  => 'frf',
				'input_type'  => 'hidden',
			)
		),
		'input_order' => 9999,
	);

	return $registration;

}

/**
 * Process required field to match the requirement.
 */
function fed_process_user_profile_required_field() {
	$fields = fed_fetch_table_by_is_required( BC_FED_TABLE_USER_PROFILE );

	return array_reduce(
		$fields, function ( $result, $item ) {
		$result[ $item['input_meta'] ] = 'Please enter ' . $item['label_name'];

		return $result;
	}, array()
	);
}

/**
 * Process User Profile Required by Menu.
 *
 * @param  string $menu  Menu.
 *
 * @return mixed
 */
function fed_process_user_profile_required_by_menu( $menu ) {
	$fields = fed_fetch_user_profile_required_by_menu( $menu );

	$values = array_reduce(
		$fields, function ( $result, $item ) {
		$result[ $item['input_meta'] ] = 'Please enter ' . $item['label_name'];

		return $result;
	}, array()
	);

	return $values;
}


/**
 * Process Author Input Record.
 *
 * @param  object $user  User Object.
 * @param  array  $single_item  Single Item.
 *
 * @return string
 */
function fed_process_author_details( $user, array $single_item ) {
	/**
	 * 'text'     => 'Text Box',
	 * 'number'   => 'Number Box',
	 * 'textarea' => 'Textarea',
	 * 'email'    => 'Email',
	 * 'checkbox' => 'Checkbox',
	 * 'select'   => 'Select',
	 * 'radio'    => 'Radio',
	 * 'password' => 'Password',
	 * 'url'      => 'URL',
	 * 'date'     => 'Date',
	 * 'file'     => 'File',
	 * 'color'    => 'Color'
	 */
	if ( 'file' === $single_item['input_type'] ) {
		return wp_get_attachment_image( (int) $user->get( $single_item['input_meta'] ), 'thumbnail' );
	}

	if ( 'url' === $single_item['input_type'] ) {
		return make_clickable( $user->get( $single_item['input_meta'] ) );
	}

	if ( 'color' === $single_item['input_type'] ) {
		return fed_input_box(
			$single_item['input_meta'],
			array(
				'value'    => $user->get( $single_item['input_meta'] ),
				'disabled' => true,
			), 'color'
		);
	}

	if ( 'checkbox' === $single_item['input_type'] ) {
		return fed_input_box(
			$single_item['input_meta'], array(
			'value'    => $user->get( $single_item['input_meta'] ),
			'disabled' => true,
		), 'checkbox'
		);
	}

	if ( 'radio' === $single_item['input_type'] ) {
		$input_value = fed_convert_comma_separated_key_value( $single_item['input_value'] );

		return isset( $input_value[ $user->get( $single_item['input_meta'] ) ] ) ? $input_value[ $user->get(
			$single_item['input_meta']
		) ] : '';
	}

	if ( 'date' === $single_item['input_type'] ) {
		$user_date = $user->get( $single_item['input_meta'] );
		$extended  = is_string( $single_item['extended'] ) ? unserialize(
			$single_item['extended']
		) : $single_item['extended'];
		$format    = ( 'true' == $extended['enable_time'] ) ? '%e %B %Y -  %I:%M %p' : '%e %B %Y';

		if ( false !== strpos( $user_date, 'to' ) ) {
			$range = explode( 'to', $user_date );

			return ucfirst( strftime( $format, strtotime( $range[0] ) ) ) . ' to ' . ucfirst(
					strftime(
						$format,
						strtotime( $range[1] )
					)
				);

		}

		if ( strpos( $user_date, ';' ) !== false ) {
			$multiple      = explode( ';', $user_date );
			$multiple_item = '';
			foreach ( $multiple as $item ) {
				$multiple_item .= ucfirst( strftime( $format, strtotime( $item ) ) ) . '<br>';
			}

			return $multiple_item;
		}

		return ucfirst( strftime( $format, strtotime( $user_date ) ) );
	}

	$value = $user->get( $single_item['input_meta'] );

	return apply_filters( 'fed_process_author_custom_details', $value, $user, $single_item );
}

/**
 * User Input Mandatory Required Fields
 */
function fed_input_mandatory_required_fields() {
	return apply_filters(
		'fed_input_mandatory_required_fields', array(
			'user_login',
			'user_pass',
			'confirmation_password',
			'user_email',
		)
	);
}

/**
 * Get WP Editor.
 *
 * @param  string $content  Content.
 * @param  string $id  ID.
 * @param  array  $options  Options.
 *
 * @return string
 */
function fed_get_wp_editor( $content = '', $id, array $options = array() ) {
	ob_start();

	wp_editor( $content, $id, $options );

	$temp = ob_get_clean();

	return $temp;
}

/**
 * Get Dashboard Display Categories.
 *
 * @param  string $post  Post.
 * @param  string $cpt  CPT.
 *
 * @return string
 */
function fed_get_dashboard_display_categories( $post = '', $cpt = '' ) {
	$categories         = array();
	$fed_get_categories = get_terms(
		array(
			'taxonomy'   => $cpt->name,
			'hide_empty' => false,
		)
	);

	if ( isset( $post->ID ) ) {
		$categories = wp_get_post_terms( $post->ID, $cpt->name, array( 'fields' => 'ids' ) );
	}

	return fed_convert_array_to_id_name( $fed_get_categories, 'term_id', $cpt->name, $categories );
}

/**
 * Get Dashboard Display Tags.
 *
 * @param  string $post  Post.
 * @param  string $cpt  CPT.
 *
 * @return string
 */
function fed_get_dashboard_display_tags( $post = '', $cpt = '' ) {
	$tags         = array();
	$fed_get_tags = get_terms(
		array(
			'taxonomy'   => $cpt->name,
			'hide_empty' => false,
		)
	);
	if ( isset( $post->ID ) ) {
		$tags = wp_get_post_terms( $post->ID, $cpt->name, array( 'fields' => 'slugs' ) );
	}

	return fed_convert_array_to_id_name( $fed_get_tags, 'slug', $cpt->name, $tags );
}

/**
 * Convert Array to ID Name.
 *
 * @param  array  $array  Array.
 * @param  string $key  Key.
 * @param  string $type  Type.
 * @param  array  $compare  Compare.
 *
 * @return string
 */
function fed_convert_array_to_id_name( array $array, $key = 'term_id', $type = '', $compare = array() ) {
	$new_category = array();
	$html         = '';

	if ( $array ) {
		foreach ( $array as $value ) {
			$new_category[ $value->$key ] = $value->name;
		}

		$html .= fed_get_input_details(
			array(
				'input_value' => $new_category,
				'input_meta'  => 'tax_input[' . $type . ']',
				'input_type'  => 'select',
				'user_value'  => $compare,
				'id_name'     => $type,
				'extended'    => array( 'multiple' => 'Enable' ),
			)
		);

		return $html;
	}

	return fed_get_input_details(
		array(
			'input_value' => array(),
			'input_meta'  => $type,
			'input_type'  => 'select',
			'user_value'  => '',
			'id_name'     => $type,
			'extended'    => array( 'multiple' => 'Enable' ),
		)
	);
}

/**
 * Get post format.
 */
function fed_dashboard_get_post_format() {
	if ( current_theme_supports( 'post-formats' ) ) {
		$post_formats = get_theme_support( 'post-formats' );
		if ( is_array( $post_formats[0] ) ) {
			return $post_formats[0];
		}

		return false;
	}

	return false;
}

/**
 * Get post meta 0th element.
 *
 * @param  int $id  post ID.
 *
 * @return array.
 */
function fed_get_post_meta( $id ) {
	$post_meta = get_post_meta( $id );
	$temp      = array();
	foreach ( $post_meta as $index => $items ) {
		foreach ( $items as $item ) {
			$temp[ $index ] = $item;
		}
	}

	return $temp;
}

/**
 * Get Categories ID By Post ID.
 *
 * @param  int $post_id  Post ID.
 *
 * @return array
 */
function fed_get_categories_id_by_post_id( $post_id ) {
	$categories = get_the_category( $post_id );

	return fed_convert_array_object_to_key_value( $categories );
}

/**
 * Get Post Status Symbol.
 *
 * @param  string $status  Status.
 *
 * @return string
 */
function fed_get_post_status_symbol( $status ) {
	if ( 'publish' == $status ) {
		return ' <i class="fa fa-check bg-primary-font fed_popover" data-toggle="popover" data-trigger="hover" title="' . esc_attr( $status ) . '" 
 data-content="' . __( 'Awesome! This post as been published', 'frontend-dashboard' ) . '"></i>';
	}
	if ( 'pending' == $status ) {
		return ' <i class="fa fa-pause bg-info-font fed_popover" data-toggle="popover" data-trigger="hover" title="' . esc_attr( $status ) . '" 
 data-content="' . __( 'Please wait, your post is in pending status, editor or admin will approve your post.',
				'frontend-dashboard' ) . '"></i>';
	}

	return '<i class="fa fa-exclamation bg-danger-font fed_popover" data-toggle="popover" data-trigger="hover" title="' . esc_attr( $status ) . '" 
 data-content="' . esc_attr( $status ) . '"></i>';

}

/**
 * Get Tags ID by Post ID.
 *
 * @param  int $post_id  Post ID.
 *
 * @return array
 */
function fed_get_tags_id_by_post_id( $post_id ) {
	$tags = get_the_tags( $post_id );

	return fed_convert_array_object_to_key_value( $tags );
}

/**
 * Convert Array Object to Key Value.
 *
 * @param  array  $array  Array.
 * @param  string $key  Key.
 * @param  string $value  Value.
 *
 * @return array
 */
function fed_convert_array_object_to_key_value( $array, $key = 'slug', $value = 'term_id' ) {
	$ids = array();
	foreach ( $array as $object ) {
		$ids[ $object->$key ] = $object->$value;
	}

	return $ids;
}

/**
 * Get Payment Notificcation.
 */
function fed_get_payment_notification() {
	if ( isset( $_REQUEST['success'] ) && 'no' == $_REQUEST['success'] ) {
		?>
		<div class="alert alert-danger">
			<button type="button"
					class="close"
					data-dismiss="alert"
					aria-hidden="true">&times;
			</button>
			<strong>Cancelled!</strong>
			Sorry your transaction has been cancelled
		</div>
		<?php
	}
	if ( isset( $_REQUEST['success'] ) && 'yes' == $_REQUEST['success'] && isset( $_REQUEST['tid'] ) ) {
		?>
		<div class="alert alert-success">
			<button type="button"
					class="close"
					data-dismiss="alert"
					aria-hidden="true">&times;
			</button>
			<strong>Payment Success!</strong>
			Thanks for your payment - You transaction ID : <?php echo esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['tid'] ) ) ); ?>
		</div>
		<?php
	}
}

/**
 * Show Users By Role.
 *
 * @param  object $fed_user_attr  User Attribute.
 */
function fed_show_users_by_role( $fed_user_attr ) {
	$user_roles    = fed_get_user_roles();
	$get_user_role = $fed_user_attr->role;
	$current_url   = get_site_url() . '/' . $get_user_role . '/';
	?>
<div class="bc_fed fed_user_roles_container <?php echo esc_attr( $get_user_role ); ?>">

	<?php
	if ( ! array_key_exists( $get_user_role, $user_roles ) ) {
		?>
		<div class="alert alert-danger">
			<button type="button"
					class="close"
					data-dismiss="alert"
					aria-hidden="true">&times;
			</button>
			<strong>Sorry!</strong>
			The User Role " <?php echo esc_attr( $get_user_role ); ?>" is not available on your domain,
			please
			check the spelling assigned to the
			<strong>"role"</strong>
			short-code
		</div>
		<?php
	} else {
		$get_all_users = new WP_User_Query( array( 'role' => $get_user_role ) );
		if ( ! $get_all_users->get_total() ) {
			?>
			<div class="alert alert-info">
				<button type="button"
						class="close"
						data-dismiss="alert"
						aria-hidden="true">&times;
				</button>
				<strong>Sorry!</strong>
				There are no User assigned to this User Role " <?php echo esc_attr( $get_user_role ); ?>"
			</div>
			<?php
		} else {
			$chunks = array_chunk( $get_all_users->get_results(), 4 );
			foreach ( $chunks as $chunk ) {
				?>
				<div class="row fed_user_role_single">
					<?php
					foreach ( $chunk as $get_all_user ) {
						$name  = $get_all_user->get( 'display_name' );
						$email = $get_all_user->get( 'user_email' );
						?>
						<div class="col-md-3">
							<div class="panel panel-primary">
								<div class="panel-body">
									<?php
									// phpcs:ignore
									echo fed_get_avatar( $email, $name );
									?>
								</div>
								<div class="panel-footer bg-primary">
									<h3 class="panel-title">
										<a target="_blank"
												href="
												<?php
												echo esc_url(
													add_query_arg(
														array( 'fed_user_profile' => $get_all_user->ID ),
														get_permalink()
													)
												);
												?>
												">
											<?php echo esc_attr( $name ); ?>
										</a>
									</h3>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<?php
			}
		}
		?>
		</div>
		<?php
	}
}

/**
 * Show User by Role.
 *
 * @param  object $fed_user_attr  FED User Attr.
 * @param  int    $user_id  User ID.
 */
function fed_show_user_by_role( $fed_user_attr, $user_id ) {
	$user = new WP_User_Query(
		array(
			'include' => (int) $user_id,
			'role'    => $fed_user_attr->role,
		)
	);
	if ( $user->get_total() > 0 ) {
		$results = $user->get_results();
		fed_show_user_profile_page( $results[0] );
	} else {
		?>
		<div class="alert alert-info text-center">
			<button type="button"
					class="close"
					data-dismiss="alert"
					aria-hidden="true">&times;
			</button>
			<strong><?php esc_attr_e( 'Sorry!', 'frontend-dashboard' ); ?></strong>
			<?php esc_attr_e( 'No user found...', 'frontend-dashboard' ); ?>
		</div>
		<?php
	}
}

/**
 * Show user profile page by user ID.
 *
 * @param  object $user  User Data.
 */
function fed_show_user_profile_page( $user ) {
	/**
	 * Collect Menu, User Information and Menu Items.
	 */
	$profiles    = fed_array_group_by_key( fed_fetch_user_profile_by_dashboard(), 'menu' );
	$menus       = fed_fetch_table_rows_with_key( BC_FED_TABLE_MENU, 'menu_slug' );
	$upl_options = get_option( 'fed_admin_settings_upl' );

	/**
	 * Get author recent Posts
	 */
	$post_count   = isset( $upl_options['settings']['fed_upl_no_recent_post'] ) ? $upl_options['settings']['fed_upl_no_recent_post'] : 5;
	$author_query = array(
		'posts_per_page' => $post_count,
		'author'         => $user->ID,
		'order'          => 'DESC',
	);
	$author_posts = new WP_Query( $author_query );

	?>
	<div id="primary fed_user_profile"
			class="bc_fed fed-profile-area container">

		<div class="row fed_profile_container">
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-12 fed_profile_picture">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<div class="fed_profile_full_name text-center">
									<h3 class="panel-title">
										<a href="<?php echo esc_url( get_author_posts_url( $user->ID ) ); ?>">
											<?php echo esc_attr( $user->get( 'display_name' ) ); ?>
										</a>
									</h3>
								</div>
							</div>
							<div class="panel-body">
								<a href="<?php echo esc_url( get_author_posts_url( $user->ID ) ); ?>">
									<?php
									// phpcs:ignore
									echo fed_get_avatar( $user->ID, $user->display_name, 'img-responsive' );
									?>
								</a>
							</div>

							<div class="panel-footer">
								<?php
								do_action( 'fed_show_support_button_at_user_profile', $user );

								if ( 'no' === $upl_options['settings']['fed_upl_disable_desc'] ) {
									?>
									<div class="row">
										<div class="col-md-12 fed_profile_description">
											<?php echo esc_attr( $user->get( 'description' ) ); ?>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-9 fed_dashboard_container">
				<div class="row fed_dashboard_wrapper">
					<div class="col-md-12 fed_dashboard_menus fed_collapse">
						<ul class="nav nav-pills nav-justified list-group ">
							<?php
							$first = true;
							foreach ( $profiles as $index => $profile ) {
								if ( 'Enable' !== $menus[ $index ]['show_user_profile'] ) {
									continue;
								}
								if ( $first ) {
									$first  = false;
									$active = 'active';
								} else {
									$active = '';
								}
								?>
								<li class="fed_menu_slug <?php echo esc_attr( $active ); ?>"
										data-menu="<?php echo esc_attr( $menus[ $index ]['menu_slug'] ); ?>"
								>
									<a href="#<?php echo esc_attr( $menus[ $index ]['menu_slug'] ); ?>">
										<span class="
										<?php
										echo esc_attr(
											$menus[ $index ]['menu_image_id']
										);
										?>
										">
										</span>
										<?php
										echo esc_attr(
											ucwords( $menus[ $index ]['menu'] ), 'frontend-dashboard'
										);
										?>
									</a>
								</li>
								<?php
							}
							?>
						</ul>
					</div>
					<div class="col-md-12 fed_dashboard_items">
						<?php
						$first = true;
						foreach ( $profiles as $index => $item ) {
							if ( 'Enable' !== $menus[ $index ]['show_user_profile'] ) {
								continue;
							}
							if ( $first ) {
								$first  = false;
								$active = '';
							} else {
								$active = 'hide';
							}
							?>
							<div class="panel panel-primary fed_dashboard_item
							<?php
							echo esc_attr(
								$active . ' ' . $index
							);
							?>
							">
								<div class="panel-body">
									<?php
									foreach ( $item as $single_item ) {
										if ( 'Enable' !== $single_item['show_user_profile'] ) {
											continue;
										}

										if ( ( 'user_pass' === $single_item['input_meta'] ) || ( 'confirmation_password' === $single_item['input_meta'] ) ) {
											continue;
										}
										if ( in_array( $single_item['input_meta'], fed_no_update_fields(), false ) ) {
											$single_item['readonly'] = 'readonly';
										}
										if (
											count(
												array_intersect(
													$user->roles,
													unserialize( $single_item['user_role'] )
												)
											) <= 0
										) {
											continue;
										}

										?>
										<div class="row fed_dashboard_item_field">
											<div class="fed_dashboard_label_name fed_header_font_color col-md-4 text-right-md text-right-not-sm text-right-not-xs">
												<?php
												echo esc_attr(
													$single_item['label_name'], 'frontend-dashboard'
												);
												?>
											</div>
											<div class="col-md-8">
												<?php
												// phpcs:ignore
												echo fed_process_author_details( $user, $single_item );
												?>
											</div>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>

			</div>
		</div>
		<div class="row fed_user_posts_container">
			<div class="col-md-12">
				<h2>
					<?php esc_attr_e( 'Recent Post by', 'frontend-dashboard' ); ?>
					<?php echo esc_attr( $user->get( 'display_name' ) ); ?>
				</h2>
			</div>
			<div class="col-md-8 fed_posts">
				<?php
				while ( $author_posts->have_posts() ) :
					$author_posts->the_post();
					?>
					<div class="fed_post_title">
						<a href="<?php the_permalink(); ?>"
								title="<?php the_title_attribute(); ?>">
							<?php the_title(); ?>
						</a>
					</div>
					<div class="fed_post_excerpt">
						<?php the_excerpt(); ?>
					</div>
				<?php
				endwhile;
				?>
			</div>
			<div class="col-md-4 fed_sidebar">
				<?php get_sidebar(); ?>
			</div>
		</div>
	</div>
	<?php
	do_action( 'fed_user_profile_below' );

}

/**
 * Get 403 Error Page.
 */
function fed_get_403_error_page() {
	$url = explode( '?', esc_url_raw( add_query_arg( array() ) ) );
	?>
	<div class="panel panel-primary fed_dashboard_item active">
		<div class="panel-heading">
			<h3 class="panel-title">
				<span class="fa fa-exclamation-triangle"></span>
				<?php esc_attr_e( 'Error (403)', 'frontend-dashboard' ); ?>
			</h3>
		</div>
		<div class="panel-body">
			<h2><?php esc_attr_e( 'Unauthorised Access', 'frontend-dashboard' ); ?></h2>
			<a class="btn btn-primary" href="<?php echo esc_url( $url[0] ); ?>">
				<?php
				esc_attr_e(
					'Click here to visit Dashboard',
					'frontend-dashboard'
				)
				?>
			</a>
		</div>
	</div>
	<?php
}

/**
 * Set Alert.
 *
 * @param  string $key  Key.
 * @param  string $message  Message.
 */
function fed_set_alert( $key, $message ) {
	set_transient( $key, $message, MINUTE_IN_SECONDS );
}

/**
 * Show Alert.
 *
 * @param  string $key  Key.
 *
 * @return string
 */
function fed_show_alert( $key ) {
	$value = get_transient( $key );
	$html  = '';
	if ( $value ) {
		if ( is_array( $value ) ) {
			$value = $value[0];
		}
		$html .= '<div class="fkm_hide_alert alert alert-success m-y-10">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<strong>' . wp_kses_post( $value ) . '</strong>
						</div>';
		delete_transient( $key );
	}

	return $html;
}


/**
 * Show Form Label
 *
 * @param $content
 */
function fed_show_form_label( $content ) {
	$label = '';
	if (
		( ! isset( $content['input_type'] ) && ! empty( $content['name'] ) ) ||
		( isset( $content['input_type'] ) && 'label' !== $content['input_type'] && ! empty( $content['name'] ) )
	) {
		$label = '<label>' . esc_attr__( $content['name'], 'frontend-dashboard' ) . '</label>';
	}

	$label = apply_filters( 'fed_show_form_label', $label, $content );

	return $label;
}
