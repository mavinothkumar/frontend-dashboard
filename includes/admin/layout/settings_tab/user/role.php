<?php
/**
 * User Role
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User Role.
 *
 * @param  array $fed_admin_options  Admin Options.
 */
function fed_admin_user_role_tab( $fed_admin_options ) {
	$user_roles = fed_get_extra_user_roles();
	?>
	<div class="space-y-6">
		<!-- Add New Custom Role Form -->
		<form method="post"
				class="fed_admin_menu fed_ajax"
				action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ); ?>">
			<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
			<?php echo fed_loader(); ?>

			<input type="hidden" name="fed_admin_unique" value="fed_admin_setting_user"/>
			<input type="hidden" name="fed_admin_unique_user" value="fed_admin_setting_role"/>

			<div class="bg-slate-50/80 rounded-2xl p-5 border border-slate-200/80 space-y-4">
				<div class="flex items-center gap-2 pb-3 border-b border-slate-200/80">
					<i class="fas fa-plus-circle text-indigo-600 text-xs"></i>
					<h4 class="text-xs font-bold text-slate-800 m-0"><?php esc_html_e( 'Add New Custom User Role', 'frontend-dashboard' ); ?></h4>
				</div>

				<div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
					<div class="sm:col-span-5 space-y-1.5">
						<label class="block text-xs font-bold text-slate-700">
							<?php esc_html_e( 'Role Display Name', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
						</label>
						<input type="text" name="user[role][role_name]" id="fed_admin_post_user_role_name" placeholder="e.g. Branch Manager" required="required" class="w-full text-xs" />
					</div>
					<div class="sm:col-span-5 space-y-1.5">
						<label class="block text-xs font-bold text-slate-700">
							<?php esc_html_e( 'Role Slug', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
						</label>
						<input type="text" name="user[role][role_slug]" id="fed_admin_post_user_role_slug" placeholder="e.g. branch_manager" required="required" class="w-full text-xs font-mono fed_convert_space_to_underscore" />
					</div>
					<div class="sm:col-span-2">
						<button type="submit" class="fed-btn-primary w-full h-10 inline-flex items-center justify-center gap-1.5 rounded-xl font-semibold text-xs tracking-wide shadow-sm transition-all active:scale-95 cursor-pointer">
							<i class="fas fa-plus text-xs" style="color: #ffffff !important;"></i>
							<span style="color: #ffffff !important;"><?php esc_html_e( 'Add Role', 'frontend-dashboard' ); ?></span>
						</button>
					</div>
				</div>
			</div>
		</form>

		<!-- Existing Custom User Roles List -->
		<div class="space-y-3">
			<div class="flex items-center justify-between pb-2 border-b border-slate-100">
				<div class="flex items-center gap-2">
					<i class="fas fa-users-cog text-slate-500 text-xs"></i>
					<h4 class="text-xs font-bold text-slate-800 m-0"><?php esc_html_e( 'Registered Custom Roles', 'frontend-dashboard' ); ?></h4>
				</div>
				<span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
					<?php echo count( $user_roles ); ?> <?php esc_html_e( 'Custom Roles', 'frontend-dashboard' ); ?>
				</span>
			</div>

			<?php if ( empty( $user_roles ) ) : ?>
				<div class="py-8 text-center bg-slate-50/60 rounded-2xl border border-dashed border-slate-200">
					<i class="fas fa-user-shield text-2xl text-slate-300 mb-2"></i>
					<p class="text-xs text-slate-500 m-0 font-medium"><?php esc_html_e( 'No custom user roles added yet.', 'frontend-dashboard' ); ?></p>
					<p class="text-[11px] text-slate-400 m-0 mt-0.5"><?php esc_html_e( 'Use the form above to create specialized user roles.', 'frontend-dashboard' ); ?></p>
				</div>
			<?php else : ?>
				<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
					<?php foreach ( $user_roles as $key => $user_role ) : ?>
						<div class="p-3.5 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3 shadow-2xs">
							<div class="min-w-0">
								<div class="text-xs font-bold text-slate-900 truncate"><?php echo esc_html( $user_role ); ?></div>
								<div class="text-[10px] font-mono text-slate-500 truncate mt-0.5"><code><?php echo esc_html( $key ); ?></code></div>
							</div>
							<form method="post"
									class="fed_admin_user_role_delete fed_ajax_confirmation shrink-0"
									action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ); ?>">
								<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
								<input type="hidden" name="fed_admin_unique" value="fed_admin_setting_user"/>
								<input type="hidden" name="fed_admin_unique_user" value="fed_admin_setting_role_delete"/>
								<input type="hidden" name="user[role][role_slug]" value="<?php echo esc_attr( $key ); ?>"/>
								<input type="hidden" name="user[role][role_name]" value="<?php echo esc_attr( $user_role ); ?>"/>
								<button type="submit" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center text-xs transition-colors cursor-pointer" title="<?php esc_attr_e( 'Delete Role', 'frontend-dashboard' ); ?>">
									<i class="fas fa-trash-alt"></i>
								</button>
							</form>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
