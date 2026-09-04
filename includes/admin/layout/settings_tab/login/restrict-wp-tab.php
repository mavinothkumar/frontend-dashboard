<?php
/**
 * Restrict WP Admin.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Restrict WP Admin Tab.
 *
 * @param  array $fed_login_register  Login Register.
 */
function fed_admin_restrict_wp_admin_tab( $fed_login_register ) {
	$user_role  = isset( $fed_login_register['restrict_wp']['role'] ) ? array_keys( $fed_login_register['restrict_wp']['role'] ) : array();
	$user_roles = fed_get_user_roles_without_admin();
	?>
	<form method="post"
		  class="fed_admin_menu fed_ajax space-y-6"
		  action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ); ?>">

		<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
		<?php echo fed_loader(); ?>

		<input type="hidden" name="fed_admin_unique" value="fed_login_details"/>
		<input type="hidden" name="fed_admin_unique_login" value="fed_wp_restrict_settings"/>

		<!-- User Roles Selector Widget -->
		<div>
			<?php
			fed_render_user_roles_selector(
				array(
					'name_prefix' => 'fed_admin_login[role]',
					'selected'    => $user_role,
					'all_roles'   => $user_roles,
					'title'       => __( 'Restrict User Role(s) to access the WP admin area', 'frontend-dashboard' ),
					'description' => __( 'Select user roles that will be blocked from accessing the WordPress wp-admin dashboard.', 'frontend-dashboard' ),
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
