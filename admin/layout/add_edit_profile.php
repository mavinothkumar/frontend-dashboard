<?php

/**
 * Add New User Input
 */
function fed_add_new_user_input() {
	$id              = '';
	$add_edit_action = 'Add New ';
	$selected        = '';
	$action          = isset( $_GET['fed_action'] ) ? esc_attr( $_GET['fed_action'] ) : '';
	if ( isset( $_GET['fed_input_id'] ) ) {
		$id              = (int) $_GET['fed_input_id'];
		$add_edit_action = 'Edit ';
	}

	if ( $action !== 'profile' && $action !== 'post' ) {
		?>
		<div class="bc_fed container fed_add_page_profile_container text-center">
			<a class="btn btn-primary"
			   href="<?php echo menu_page_url( 'fed_add_user_profile', false ) . '&fed_action=post' ?>">
				<i class="fa fa-envelope"></i>
				<?php _e( 'Add Extra Post Field', 'fed' ) ?>
			</a>
			<a class="btn btn-primary"
			   href="<?php echo menu_page_url( 'fed_add_user_profile', false ) . '&fed_action=profile' ?>">
				<i class="fa fa-user-plus"></i>
				<?php _e( 'Add Extra User Profile Field', 'fed' ) ?>
			</a>
		</div>
		<?php
		exit();
	}

	if ( $action === 'profile' ) {
		$page = 'Profile';
		$url  = menu_page_url( 'fed_user_profile', false );
		if ( is_int( $id ) ) {
			$rows = fed_fetch_table_row_by_id( BC_FED_USER_PROFILE_DB, $id );
			if ( $rows instanceof WP_Error ) {
				?>
				<div class="container fed_UP_container">
					<div class="row">
						<div class="col-md-12">
							<div class="alert alert-danger">
								<button type="button"
										class="close"
										data-dismiss="alert"
										aria-hidden="true">&times;
								</button>
								<strong><?php echo $rows->get_error_message() ?></strong>
							</div>
						</div>
					</div>
				</div>
				<?php
				exit();
			}
			$row      = fed_process_user_profile( $rows, $action );
			$selected = $row['input_type'];
		} else {
			$row = fed_get_empty_value_for_user_profile( $action );
		}
	}
	if ( $action === 'post' ) {
		$page = 'Post';
		$url  = menu_page_url( 'fed_post_fields', false );
		if ( is_int( $id ) ) {
			$rows = fed_fetch_table_row_by_id( BC_FED_POST_DB, $id );
			if ( $rows instanceof WP_Error ) {
				?>
				<div class="container fed_UP_container">
					<div class="row">
						<div class="col-md-12">
							<div class="alert alert-danger">
								<button type="button"
										class="close"
										data-dismiss="alert"
										aria-hidden="true">&times;
								</button>
								<strong><?php echo $rows->get_error_message() ?></strong>
							</div>
						</div>
					</div>
				</div>
				<?php
				exit();
			}
			$row      = fed_process_user_profile( $rows, $action );
			$selected = $row['input_type'];
		}
		else {
			$row = fed_get_empty_value_for_user_profile( $action );
		}
	}

	$buttons = fed_admin_user_profile_select( $selected );
	?>
	<div class="bc_fed container fed_add_edit_input_container">
		<div class="row fed_admin_up_select">
			<div class="col-md-3">
				<a class="btn btn-primary"
				   href="<?php echo $url; ?>">
					<i class="fa fa-mail-reply"></i>
					<?php _e( 'Back to', 'fed' ) ?> <?php echo $page ?>
				</a>
			</div>
			<div class="col-md-3 col-lg-offset-1 text-center">
				<h4 class="fed_header_font_color text-uppercase">
					<?php echo $add_edit_action; ?> <?php echo $page ?> field
				</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="fed_buttons_container <?php echo $buttons['class']; ?>">
					<?php
					foreach ( $buttons['options'] as $index => $button ) {
						$active = $buttons['value'] === $index ? 'active' : '';
						?>
						<div class="fed_button <?php echo $active; ?>" data-button="<?php echo $index; ?>">
							<div class="fed_button_image">
								<img src="<?php echo $button['image'] ?>"/>
							</div>
							<div class="fed_button_text"><?php echo $button['name'] ?></div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<div class="fed_all_input_fields_container">

			<?php
			//			Input Type
			fed_admin_input_fields_single_line( $row, $action );
			//            Email Type
			fed_admin_input_fields_mail( $row, $action );
			//            Number Type
			fed_admin_input_fields_number( $row, $action );
			//            Password
			fed_admin_input_fields_password( $row, $action );
			//            TextArea
			fed_admin_input_fields_multi_line( $row, $action );
			//            Checkbox
			fed_admin_input_fields_checkbox( $row, $action );
			//            Radio
			fed_admin_input_fields_radio( $row, $action );
			//            Select / Dropdown
			fed_admin_input_fields_select( $row, $action );
			// URL
			fed_admin_input_fields_url( $row, $action );

			do_action( 'fed_admin_input_fields_container_extra', $row, $action );
			?>

		</div>
	</div>
	<?php
}

/**
 * Get Admin User Profile Role Based.
 *
 * @param array $row User Profile Details
 * @param string $action Action type
 */
function fed_get_admin_up_role_based( $row, $action ) {
	$all_roles = fed_get_user_roles();
	$options   = fed_fetch_menu_with_key_value();
	?>
	<div class="row fed_admin_up_role_based">
		<div class="col-md-12">
			<label><?php _e( 'Select user role to show this input field', 'fed' ) ?></label>
		</div>
		<?php foreach ( $all_roles as $key => $role ) {
			$c_value = in_array( $key, $row['user_role'] ) ? 'Enable' : 'Disable';
			?>
			<div class="col-md-2">
				<?php echo fed_input_box( 'user_role', array(
					'default_value' => 'Enable',
					'name'          => 'user_role[' . $key . ']',
					'label'         => $role,
					'value'         => $c_value,
				), 'checkbox' ); ?>
			</div>
		<?php } ?>
	</div>
	<?php if ( $action == 'profile' ) { ?>
		<div class="row padd_top_10">
			<div class="form-group col-md-4">
				<label><?php _e( 'Menu Location', 'fed' ) ?></label>
				<?php echo fed_input_box( 'menu', array(
					'default_value' => 'Enable',
					'label'         => 'Menu Location',
					'value'         => $row['menu'],
					'options'       => $options
				), 'select' ); ?>
			</div>

			<?php if ( $row['input_meta'] != 'user_pass' && $row['input_meta'] != 'confirmation_password' ) { ?>
				<div class="form-group col-md-4 fed_show_user_profile">
					<?php echo fed_input_box( 'show_user_profile', array(
						'default_value' => 'Disable',
						'label'         => 'Disable in User Profile?',
						'value'         => $row['show_user_profile'],
					), 'checkbox' ); ?>
				</div>
			<?php } ?>
		</div>

		<?php
	}
}

/**
 * Get Admin User Profile Display Permission
 *
 * @param array $row User Profile Details
 * @param string $action Action Type
 * @param string $type Process Type
 */
function fed_get_admin_up_display_permission( $row, $action, $type = '' ) {
	?>
	<div class="row fed_admin_up_display_permission">
		<?php
		if ( $action == 'profile' ) {
			if ( $type == 'file' ) {
				$value        = 'Disable';
				$others       = 'disabled';
				$notification = '<i class="fa fa-info bg-info-font" data-toggle="popover" data-trigger="hover" title=" Status" data-content="Only registered user can upload the files."></i>';
			} else {
				$value        = $row['show_register'];
				$others       = '';
				$notification = '';
			} ?>
			<div class="form-group col-md-4">
				<?php echo fed_input_box( 'show_register', array(
						'default_value' => 'Enable',
						'label'         => 'Show this field on Register Form'.$notification,
						'value'         => $value,
						'disabled'      => $others
					), 'checkbox' );

				?>

			</div>

			<div class="form-group col-md-4">
				<?php echo fed_input_box( 'show_dashboard', array(
					'default_value' => 'Enable',
					'label'         => 'Show this field on User Dashboard',
					'value'         => $row['show_dashboard']
				), 'checkbox' ); ?>
			</div>
		<?php } ?>

		<?php if ( $action == 'post' ) {
			?>
			<div class="form-group col-md-4">
				<label><?php _e( 'Post Type', 'fed' ) ?></label>
				<?php echo fed_input_box( 'post_type', array(
					'default_value' => 'Post',
					'value'         => $row['post_type'],
					'options'       => fed_get_public_post_types(),
				), 'select' ); ?>
			</div>
		<?php } ?>
		<div class="form-group col-md-4">
			<?php echo fed_input_box( 'is_required', array(
				'default_value' => 'true',
				'label'         => 'Is this required Field',
				'value'         => $row['is_required'],
			), 'checkbox' ); ?>
		</div>

	</div>
	<?php
}

/**
 * Get Admin User Profile Label Input Order
 *
 * @param array $row User Profile Details
 */
function fed_get_admin_up_label_input_order( $row ) {
	$change = '';
	if ( ! fed_check_field_is_belongs_to_extra( $row['input_meta'] ) ) {
		$change = 'fed_input_label_for_onchange';
	}
	?>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="">Label Name *</label>
			<?php echo fed_input_box( 'label_name', array(
				'class' => 'form-control ' . $change . ' ',
				'value' => $row['label_name']
			), 'text' ); ?>
		</div>

		<div class="form-group col-md-6">
			<label for=""><?php _e( 'Input Order', 'fed' ) ?> *</label>
			<?php
			echo fed_input_box( 'input_order', array( 'value' => $row['input_order'] ), 'number' ); ?>
		</div>
	</div>
	<?php
}

/**
 * Get Input Type and Submit Button
 *
 * @param string $input_type Submit Input Type.
 * @param string $action Action Type.
 */
function fed_get_input_type_and_submit_btn( $input_type, $action ) {

	$input_id = ( isset( $_GET['fed_input_id'] ) ) ? $_GET['fed_input_id'] : '';

	?>
	<div class="form-group">
		<?php echo fed_input_box( 'input_type', array( 'value' => $input_type ), 'hidden' ); ?>
		<?php echo fed_input_box( 'input_id', array( 'value' => $input_id ), 'hidden' ); ?>
		<?php echo fed_input_box( 'fed_action', array( 'value' => $action ), 'hidden' ); ?>
	</div>

	<button type="submit"
			class="btn btn-primary"><?php _e( 'Submit', 'fed' ) ?>
	</button>
	<?php
}

/**
 * Get Admin User Profile Input Meta
 *
 * @param array $row User Profile Data.
 */
function fed_get_admin_up_input_meta( $row ) {
	?>
	<div class="form-group col-md-6">
		<label for=""><?php _e( 'Input Meta * [Alpha Numeric and underscore only]', 'fed' ) ?></label>
		<?php
		if ( fed_check_field_is_belongs_to_extra( $row['input_meta'] ) ) {
			echo fed_input_box( 'input_meta', array(
				'class' => 'fed_admin_input_meta form-control',
				'value' => $row['input_meta'],
			), 'readonly' );

			echo fed_input_box( 'fed_extra', array(
				'value' => $row['input_meta'],
			), 'hidden' );
		} else {
			echo fed_input_box( 'input_meta', array(
				'class' => 'fed_admin_input_meta form-control',
				'value' => $row['input_meta']
			), 'text' );
		}
		?>
	</div>
	<?php
}