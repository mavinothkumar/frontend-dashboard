<?php
/**
 * Register Tab.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Register Settings Tab.
 *
 * @param  array $fed_login_register  Login Register.
 */
function fed_admin_register_settings_tab( $fed_login_register ) {
	$user_role   = isset( $fed_login_register['register']['role'] ) ? array_keys( $fed_login_register['register']['role'] ) : array();
	$name        = isset( $fed_login_register['register']['name'] ) ? $fed_login_register['register']['name'] : 'User Role';
	$position    = isset( $fed_login_register['register']['position'] ) ? $fed_login_register['register']['position'] : 999;
	$auto_login  = isset( $fed_login_register['register']['auto_login'] ) ? $fed_login_register['register']['auto_login'] : '';
	$user_roles  = fed_get_user_roles();
	$email_notif = fed_get_data( 'register.register_email_notification', $fed_login_register );
	?>
	<form method="post"
		  class="fed_admin_menu fed_ajax space-y-6"
		  action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ); ?>">

		<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
		<?php echo fed_loader(); ?>

		<input type="hidden" name="fed_admin_unique" value="fed_login_details"/>
		<input type="hidden" name="fed_admin_unique_login" value="fed_register_settings"/>

		<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
			<div class="space-y-1.5">
				<label class="block text-xs font-bold text-slate-700 m-0 mb-1.5">
					<?php esc_html_e( 'Menu Name', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
				</label>
				<input type="text" name="fed_admin_login[name]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( '(eg) User Role', 'frontend-dashboard' ); ?>" required class="w-full" />
			</div>

			<div class="space-y-1.5">
				<label class="block text-xs font-bold text-slate-700 m-0 mb-1.5">
					<?php esc_html_e( 'Menu Order Position', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
				</label>
				<input type="number" name="fed_admin_login[position]" value="<?php echo esc_attr( $position ); ?>" placeholder="40" required class="w-full" />
			</div>

			<div class="space-y-1.5">
				<label class="block text-xs font-bold text-slate-700 m-0 mb-1.5">
					<?php esc_html_e( 'Auto Login after Register?', 'frontend-dashboard' ); ?>
				</label>
				<select name="fed_admin_login[auto_login]" class="w-full">
					<option value=""><?php esc_html_e( 'Please Select', 'frontend-dashboard' ); ?></option>
					<option value="yes" <?php selected( $auto_login, 'yes' ); ?>><?php esc_html_e( 'Yes', 'frontend-dashboard' ); ?></option>
					<option value="no" <?php selected( $auto_login, 'no' ); ?>><?php esc_html_e( 'No', 'frontend-dashboard' ); ?></option>
				</select>
			</div>

			<div class="space-y-1.5">
				<label class="block text-xs font-bold text-slate-700 m-0 mb-1.5">
					<?php esc_html_e( 'Email Notification after Register', 'frontend-dashboard' ); ?>
				</label>
				<select name="fed_admin_login[register_email_notification]" class="w-full">
					<option value=""><?php esc_html_e( 'Please Select', 'frontend-dashboard' ); ?></option>
					<option value="user" <?php selected( $email_notif, 'user' ); ?>><?php esc_html_e( 'Only User', 'frontend-dashboard' ); ?></option>
					<option value="admin" <?php selected( $email_notif, 'admin' ); ?>><?php esc_html_e( 'Only Admin', 'frontend-dashboard' ); ?></option>
					<option value="both" <?php selected( $email_notif, 'both' ); ?>><?php esc_html_e( 'Both User and Admin', 'frontend-dashboard' ); ?></option>
				</select>
			</div>
		</div>

		<!-- User Roles Selector Widget -->
		<div class="pt-4 border-t border-slate-100">
			<?php
			fed_render_user_roles_selector(
				array(
					'name_prefix' => 'fed_admin_login[role]',
					'selected'    => $user_role,
					'all_roles'   => $user_roles,
					'title'       => __( 'Show User Role(s) in Register Form', 'frontend-dashboard' ),
					'description' => __( 'Select which user roles will be selectable by users during registration.', 'frontend-dashboard' ),
				)
			);
			?>
		</div>

		<div class="pt-4 border-t border-slate-100 flex items-center justify-end">
			<button type="submit" class="fed-btn-primary h-11 inline-flex items-center justify-center gap-2 px-6 rounded-xl font-semibold text-xs tracking-wide shadow-sm transition-all active:scale-95 cursor-pointer">
				<i class="fas fa-save text-xs" style="color: #ffffff !important;"></i>
				<span style="color: #ffffff !important;"><?php esc_html_e( 'Save Changes', 'frontend-dashboard' ); ?></span>
			</button>
		</div>
	</form>
	<?php
}
