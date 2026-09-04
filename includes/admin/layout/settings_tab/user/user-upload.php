<?php
/**
 * User Upload.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User Upload Permission Tab.
 *
 * @param  array $fed_admin_options  Admin Options.
 */
function fed_admin_user_upload_permission_tab( $fed_admin_options ) {
	$all_roles             = fed_get_user_roles();
	$fed_upload_permission = isset( $fed_admin_options['user']['upload_permission'] ) ? array_keys( $fed_admin_options['user']['upload_permission'] ) : array();
	?>

	<form method="post"
			class="fed_admin_menu fed_ajax space-y-6"
			action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ); ?>">

		<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
		<?php echo fed_loader(); ?>

		<input type="hidden" name="fed_admin_unique" value="fed_admin_setting_user"/>
		<input type="hidden" name="fed_admin_unique_user" value="fed_admin_user_upload"/>

		<!-- User Roles Selector Widget -->
		<div>
			<?php
			fed_render_user_roles_selector(
				array(
					'name_prefix' => 'user[upload_permission]',
					'selected'    => $fed_upload_permission,
					'all_roles'   => $all_roles,
					'title'       => __( 'Allowed User Roles for Media Uploads', 'frontend-dashboard' ),
					'description' => __( 'Select the user roles permitted to upload attachments, featured images, and documents in the frontend.', 'frontend-dashboard' ),
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
