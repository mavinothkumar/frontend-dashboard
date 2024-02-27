<?php
/**
 * Add / Edit Profile.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Admin User Profile Role Based.
 *
 * @param array $row User Profile Details.
 * @param string $action Action type.
 * @param array $menu_options Menu options.
 */
function fed_get_admin_up_role_based( $row, $action, $menu_options ) {
	$all_roles = fed_get_user_roles();
	$options   = fed_get_key_value_array( $menu_options, 'menu_slug', 'menu' );
	?>
    <div class="row fed_admin_up_role_based">
        <div class="col-md-12">
            <label>
				<?php esc_attr_e( 'Select user role to show this input field', 'frontend-dashboard' ); ?>
            </label>
        </div>
		<?php

		foreach ( $all_roles as $key => $role ) {
			$c_value = in_array( $key, $row['user_role'], false ) ? 'Enable' : 'Disable';
			?>
            <div class="col-md-3">
				<?php
				echo fed_input_box(
					'user_role',
					array(
						'default_value' => 'Enable',
						'name'          => 'user_role[' . $key . ']',
						'label'         => $role,
						'value'         => $c_value,
					), 'checkbox'
				);
				?>
            </div>
		<?php } ?>
    </div>
	<?php if ( 'profile' == $action ) { ?>
        <div class="row padd_top_10">
            <div class="form-group col-md-4">
                <label><?php esc_attr_e( 'Menu Location', 'frontend-dashboard' ); ?></label>
				<?php
				echo fed_input_box(
					'menu',
					array(
						'default_value' => 'Enable',
						'label'         => __( 'Menu Location', 'frontend-dashboard' ),
						'value'         => $row['menu'],
						'options'       => $options,
					), 'select'
				);
				?>
            </div>

			<?php if ( ( 'user_pass' != $row['input_meta'] ) && ( 'confirmation_password' != $row['input_meta'] ) ) { ?>
                <div class="form-group col-md-4 fed_show_user_profile">
					<?php
					echo fed_input_box(
						'show_user_profile',
						array(
							'default_value' => 'Disable',
							'label'         => __( 'Disable in User Profile?', 'frontend-dashboard' ),
							'value'         => $row['show_user_profile'],
						), 'checkbox'
					);
					?>
                </div>
			<?php } ?>
            <div class="form-group col-md-4 fed_show_user_profile">
				<?php
				echo fed_input_box(
					'extended[disable_user_access]',
					array(
						'default_value' => 'Disable',
						'label'         => __( 'Disable User to Edit?', 'frontend-dashboard' ),
						'value'         => fed_get_data( 'extended.disable_user_access', $row, 'Enable' ),
					), 'checkbox'
				);
				echo fed_show_help_message( array(
					'content' => __( 'Only Admin Can Update this Field (For Users, it will be readonly)', 'frontend-dashboard' ),
				) );
				?>
            </div>
        </div>
		<?php
	}
}

/**
 * Get Admin User Profile Display Permission.
 *
 * @param array $row User Profile Details.
 * @param string $action Action Type.
 * @param string $type Process Type.
 */
function fed_get_admin_up_display_permission( $row, $action, $type = '' ) {
	?>
    <div class="row fed_admin_up_display_permission">
		<?php
		if ( 'profile' === $action ) {
			if ( 'file' === $type ) {
				$value        = 'Disable';
				$others       = true;
				$notification = '<i class="fa fa-info bg-info-font" data-toggle="popover" data-trigger="hover" title=" Status" data-content="' . __( 'Only registered user can upload the files.',
						'frontend-dashboard' ) . '"></i>';
			} else {
				$value        = $row['show_register'];
				$others       = '';
				$notification = '';
			}
			?>
            <div class="form-group col-md-4">
				<?php
				echo fed_input_box(
					'show_register',
					array(
						'default_value' => 'Enable',
						'label'         => __( 'Show in Register Form', 'frontend-dashboard' ) . ' ' . $notification,
						'value'         => $value,
						'disabled'      => $others,
					), 'checkbox'
				);
				?>
            </div>

            <div class="form-group col-md-4">
				<?php
				echo fed_input_box(
					'show_dashboard',
					array(
						'default_value' => 'Enable',
						'label'         => __( 'Show in User Dashboard ', 'frontend-dashboard' ),
						'value'         => $row['show_dashboard'],
					), 'checkbox'
				);
				?>
            </div>
		<?php } ?>

		<?php
		if ( 'post' == $action ) {
			?>
            <div class="form-group col-md-4">
                <label><?php esc_attr_e( 'Post Type', 'frontend-dashboard' ); ?></label>
				<?php
				echo fed_input_box(
					'post_type',
					array(
						'default_value' => 'Post',
						'value'         => $row['post_type'],
						'options'       => fed_get_public_post_types(),
					), 'select'
				);
				?>
            </div>
		<?php } ?>
        <div class="form-group col-md-4">
			<?php
			echo fed_input_box(
				'is_required',
				array(
					'default_value' => 'true',
					'label'         => __( 'Is this required Field', 'frontend-dashboard' ),
					'value'         => $row['is_required'],
				), 'checkbox'
			);
			?>
        </div>
    </div>
	<?php
}

/**
 * Get Admin User Profile Label Input Order.
 *
 * @param array $row User Profile Details.
 */
function fed_get_admin_up_label_input_order( $row ) {
	$change = '';
	if ( ! fed_check_field_is_belongs_to_extra( $row['input_meta'] ) ) {
		$change = 'fed_input_label_for_onchange';
	}
	?>
    <div class="row">
        <div class="form-group col-md-6">
            <label for=""><?php esc_attr_e( 'Label Name *', 'frontend-dashboard' ); ?></label>
			<?php
			echo fed_get_input_details(
				array(
					'input_type' => 'single_line',
					'input_meta' => 'label_name',
					'user_value' => $row['label_name'],
					'class_name' => $change,
				)
			)
			?>
        </div>

        <div class="form-group col-md-6">
            <label for="">
				<?php esc_attr_e( 'Input Order', 'frontend-dashboard' ); ?> *
            </label>
			<?php
			echo fed_get_input_details(
				array(
					'input_type' => 'number',
					'input_meta' => 'input_order',
					'user_value' => $row['input_order'],
				)
			);
			?>
        </div>
    </div>
	<?php
}

/**
 * Get Input Type and Submit Button.
 *
 * @param string $input_type Submit Input Type.
 * @param string $action Action Type.
 */
function fed_get_input_type_and_submit_btn( $input_type, $action ) {
	$get_payload = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
	$input_id    = ( isset( $get_payload['fed_input_id'] ) ) ? $get_payload['fed_input_id'] : '';

	?>
    <div class="form-group">
		<?php echo fed_input_box( 'input_type', array( 'value' => $input_type ), 'hidden' ); ?>
		<?php echo fed_input_box( 'input_id', array( 'value' => $input_id ), 'hidden' ); ?>
		<?php echo fed_input_box( 'fed_action', array( 'value' => $action ), 'hidden' ); ?>
    </div>

    <button type="submit" class="btn btn-primary">
		<?php esc_attr_e( 'Submit', 'frontend-dashboard' ); ?>
    </button>
	<?php
}

/**
 * Get Admin User Profile Input Meta.
 *
 * @param array $row User Profile Data.
 */
function fed_get_admin_up_input_meta( $row ) {
	?>
    <div class="form-group col-md-6">
        <label for="">
			<?php
			esc_attr_e(
				'Input Meta *',
				'frontend-dashboard'
			)
			?>
			<?php echo fed_show_help_message( array(
				'content' => __( 'Alpha Numeric and underscore only', 'frontend-dashboard' ),
			) ); ?>
        </label>
		<?php
		if ( fed_check_field_is_belongs_to_extra( $row['input_meta'] ) ) {
			echo fed_input_box(
				'input_meta',
				array(
					'class'    => 'fed_admin_input_meta form-control',
					'value'    => $row['input_meta'],
					'readonly' => true,
				), 'single_line'
			);

			echo fed_input_box(
				'fed_extra',
				array(
					'value' => $row['input_meta'],
				), 'hidden'
			);
		} else {
			echo fed_input_box(
				'input_meta',
				array(
					'class' => 'fed_admin_input_meta form-control',
					'value' => $row['input_meta'],
				), 'single_line'
			);
		}
		?>
    </div>
	<?php
}
