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
	$all_roles = fed_get_user_roles();
	$fed_upload_permission = isset( $fed_admin_options['user']['upload_permission'] ) ? array_keys( $fed_admin_options['user']['upload_permission'] ) : array();
	?>

	<form method="post"
			class="fed_admin_menu fed_ajax space-y-6"
			action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ); ?>">

		<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
		<?php echo fed_loader(); ?>

		<input type="hidden" name="fed_admin_unique" value="fed_admin_setting_user"/>
		<input type="hidden" name="fed_admin_unique_user" value="fed_admin_user_upload"/>

		<div class="space-y-4">
			<div>
				<label class="block text-xs font-bold text-slate-700 mb-1">
					<?php esc_html_e( 'Allowed User Roles for Media Uploads', 'frontend-dashboard' ); ?>
				</label>
				<p class="text-xs text-slate-500 m-0"><?php esc_html_e( 'Check the user roles permitted to upload attachments, featured images, and documents in the frontend.', 'frontend-dashboard' ); ?></p>
			</div>

			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 pt-2">
				<?php
				foreach ( $all_roles as $key => $role ) {
					$c_value = in_array( $key, $fed_upload_permission, false ) ? 'Enable' : 'Disable';
					$input_data = array(
						'default_value' => 'Enable',
						'name'          => 'user[upload_permission][' . $key . ']',
						'value'         => $c_value,
						'label'         => '',
					);
					?>
					<label class="p-3.5 bg-slate-50/80 hover:bg-slate-100/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-2 cursor-pointer transition-colors shadow-2xs">
						<span class="text-xs font-bold text-slate-800 select-none"><?php echo esc_html( $role ); ?></span>
						<?php echo fed_input_box( 'user[upload_permission]', $input_data, 'checkbox' ); ?>
					</label>
					<?php
				}
				?>
			</div>

			<div class="pt-4 border-t border-slate-100 flex items-center justify-end">
				<button type="submit" class="fed-btn-primary h-11 inline-flex items-center justify-center gap-2 px-6 rounded-xl font-semibold text-xs tracking-wide shadow-sm transition-all active:scale-95 cursor-pointer">
					<i class="fas fa-save text-xs" style="color: #ffffff !important;"></i>
					<span style="color: #ffffff !important;"><?php esc_html_e( 'Save Changes', 'frontend-dashboard' ); ?></span>
				</button>
			</div>
		</div>
	</form>
	<?php
}
