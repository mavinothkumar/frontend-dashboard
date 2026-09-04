<?php
/**
 * Permission.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post Permission Tab.
 *
 * @param  array $fed_admin_options  Admin Options.
 */
function fed_admin_post_permissions_tab( $fed_admin_options ) {
	$all_roles             = fed_get_user_roles();
	$post_permission       = isset( $fed_admin_options['permissions']['post_permission'] ) ? array_keys( $fed_admin_options['permissions']['post_permission'] ) : array();
	$fed_upload_permission = isset( $fed_admin_options['permissions']['fed_upload_permission'] ) ? array_keys( $fed_admin_options['permissions']['fed_upload_permission'] ) : array();
	?>

	<form method="post"
			class="fed_admin_menu fed_ajax space-y-6"
			action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ); ?>">

		<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
		<?php echo fed_loader(); ?>

		<input type="hidden" name="fed_admin_unique" value="fed_admin_settings_post"/>
		<input type="hidden" name="fed_admin_unique_post" value="fed_admin_permission_post"/>

		<div class="space-y-6">
			<!-- Post Add/Edit/Delete Permissions -->
			<div class="p-5 bg-slate-50/70 border border-slate-200/80 rounded-2xl">
				<?php
				fed_render_user_roles_selector(
					array(
						'name_prefix'         => 'permissions[post_permission]',
						'selected'            => $post_permission,
						'all_roles'           => $all_roles,
						'default_all_checked' => true,
						'title'               => __( 'Allow User Roles to Add/Edit/Delete Posts', 'frontend-dashboard' ),
						'description'         => __( 'User roles permitted to manage, author, edit, and delete posts on the frontend.', 'frontend-dashboard' ),
					)
				);
				?>
			</div>

			<!-- Media Upload Permissions -->
			<div class="p-5 bg-slate-50/70 border border-slate-200/80 rounded-2xl">
				<?php
				fed_render_user_roles_selector(
					array(
						'name_prefix'         => 'permissions[fed_upload_permission]',
						'selected'            => $fed_upload_permission,
						'all_roles'           => $all_roles,
						'default_all_checked' => true,
						'title'               => __( 'Allow User Roles to Upload Files in Posts', 'frontend-dashboard' ),
						'description'         => __( 'User roles permitted to upload featured images and attachments when composing posts.', 'frontend-dashboard' ),
					)
				);
				?>
			</div>
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

/**
 * Get Default Post Options
 *
 * @param  array $all_roles  User Roles.
 *
 * @return array
 */
function fed_get_default_post_options( $all_roles ) {
	$default = array();
	foreach ( $all_roles as $key => $all_role ) {
		$default['permissions']['post_permission'][ $key ]       = 'Enable';
		$default['permissions']['fed_upload_permission'][ $key ] = 'Enable';
	}
	$default['settings']['fed_post_status'] = 'publish';
	$default['menu']['rename_post']         = 'Post';
	$default['menu']['post_position']       = 2;
	$default['menu']['post_menu_icon']      = 'fa fa-file-text';

	return $default;
}
