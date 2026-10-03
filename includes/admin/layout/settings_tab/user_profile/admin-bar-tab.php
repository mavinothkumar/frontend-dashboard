<?php
/**
 * Role-Based WordPress Admin Bar Visibility Tab.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Admin Bar Visibility Tab
 *
 * @param array $fed_admin_options
 */
function fed_admin_user_profile_hide_bar_tab( $fed_admin_options = array() ) {
	$saved_options = get_option( 'fed_admin_settings_upl_hide_admin_bar', array() );
	$hidden_roles  = isset( $saved_options['hide_admin_menu_bar']['role'] ) && is_array( $saved_options['hide_admin_menu_bar']['role'] )
		? array_keys( $saved_options['hide_admin_menu_bar']['role'] )
		: array();

	$all_roles                         = fed_get_user_roles();
	$all_roles['fed_disable_all_user'] = __( 'Unregistered / Logged-out Users', 'frontend-dashboard' );
	?>
	<form method="post"
			class="fed_admin_menu fed_ajax space-y-6"
			action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ); ?>">

		<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo fed_loader();
		?>

		<input type="hidden" name="fed_admin_unique" value="fed_admin_setting_upl_hide_bar"/>

		<!-- User Roles Selector Widget -->
		<div>
			<?php
			fed_render_user_roles_selector(
				array(
					'name_prefix' => 'hide_menu_bar[role]',
					'selected'    => $hidden_roles,
					'all_roles'   => $all_roles,
					'title'       => __( 'Hide WordPress Admin Top Bar by User Role', 'frontend-dashboard' ),
					'description' => __( 'Select user roles and visitor states for which the WordPress top admin toolbar should be hidden.', 'frontend-dashboard' ),
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