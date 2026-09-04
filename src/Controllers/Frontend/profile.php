<?php
/**
 * Profile Frontend Controller.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Display Profile.
 *
 * @param string $menu Menu slug.
 * @return array|bool
 */
function fed_process_dashboard_display_profile( $menu ) {
	return fed_fetch_user_profile_by_menu_slug( $menu );
}

/**
 * Get Display Dashboard Profile.
 *
 * @param array $menu_item Menu Item.
 */
function fed_display_dashboard_profile( $menu_item ) {
	$profiles = fed_process_dashboard_display_profile( $menu_item['menu_slug'] );
	$user     = get_userdata( get_current_user_id() );
	$menus    = fed_process_dashboard_display_menu();

	$index     = $menu_item['menu_slug'];
	$menu_name = isset( $menus[ $index ]['menu'] ) ? esc_attr( $menus[ $index ]['menu'] ) : __( 'Profile', 'frontend-dashboard' );
	$iconClass = isset( $menus[ $index ]['menu_image_id'] ) ? $menus[ $index ]['menu_image_id'] : 'fa fa-user';

	$menu_default_page = apply_filters( 'fed_menu_default_page', true, $menus, $index );
	?>
	<div class="fed_dashboard_item">
		
		<!-- Section Header -->
		<div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-100">
			<div>
				<h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
					<span class="<?php echo esc_attr( $iconClass ); ?> text-indigo-600 text-lg"></span>
					<?php echo esc_html( apply_filters( 'fed_user_profile_menu_container_title', $menu_name, $menus[ $index ] ?? [] ) ); ?>
				</h2>
				<p class="text-xs text-slate-500 mt-1">
					<?php esc_html_e( 'Manage and update your account details, personal profile, and preferences.', 'frontend-dashboard' ); ?>
				</p>
			</div>
		</div>

		<div>
			<?php
			do_action( 'fed_dashboard_panel_inside_top' );
			do_action( 'fed_dashboard_panel_inside_top_' . fed_get_data( 'menu_slug', $menu_item ) );
			echo fed_show_alert( 'fed_profile_save_message' );

			if ( $menu_default_page ) {
				if ( $profiles && is_array( $profiles ) ) {
					usort( $profiles, 'fed_sort_by_order' );
					?>
					<form method="post"
						  class="space-y-6"
						  action="<?php echo esc_url( add_query_arg( [ 'fed_nonce' => wp_create_nonce( 'fed_nonce' ) ], fed_get_form_action( 'fed_save_user_profile' ) ) ); ?>">
						<?php wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
						<input type="hidden" name="tab_id" value="<?php echo esc_attr( $index ); ?>"/>
						<input type="hidden" name="menu_type" value="<?php echo esc_attr( $menu_item['menu_type'] ); ?>"/>
						<input type="hidden" name="menu_slug" value="<?php echo esc_attr( $menu_item['menu_slug'] ); ?>"/>

						<?php do_action( 'fed_dashboard_profile_form_top' ); ?>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<?php
							foreach ( $profiles as $single_item ) {
								if ( 'user_pass' !== $single_item['input_meta'] || 'confirmation_password' !== $single_item['input_meta'] ) {
									$single_item['user_value'] = $user ? $user->get( $single_item['input_meta'] ) : '';
								}

								if ( in_array( $single_item['input_meta'], fed_no_update_fields(), true ) ) {
									$single_item['readonly'] = true;
								}

								if ( ! empty( $single_item['user_role'] ) ) {
									$allowedRoles = maybe_unserialize( $single_item['user_role'] );
									if ( is_array( $allowedRoles ) && $user && count( array_intersect( (array) $user->roles, $allowedRoles ) ) <= 0 ) {
										continue;
									}
								}

								$isFullWidth = in_array( $single_item['input_type'] ?? '', [ 'textarea', 'multiline', 'address' ], true );
								?>
								<div class="fed_dashboard_item_field <?php echo $isFullWidth ? 'md:col-span-2' : ''; ?>">
									<label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
										<?php echo wp_kses_post( $single_item['label_name'] ); ?>
										<?php if ( ! empty( $single_item['is_required'] ) && 'Enable' === $single_item['is_required'] ) : ?>
											<span class="text-red-500">*</span>
										<?php endif; ?>
									</label>
									<div class="mt-1">
										<?php echo fed_get_input_details( $single_item ); ?>
									</div>
								</div>
								<?php
							}
							?>
						</div>

						<?php do_action( 'fed_dashboard_profile_form_bottom' ); ?>

						<div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
							<button type="submit"
									class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold rounded-xl shadow-xs transition-all duration-150 cursor-pointer">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
								<?php esc_html_e( 'Save Changes', 'frontend-dashboard' ); ?>
							</button>
						</div>

					</form>
					<?php
				} else {
					?>
					<div class="text-center py-12 text-slate-400">
						<svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
						<p class="text-sm font-medium"><?php esc_html_e( 'No fields configured for this section.', 'frontend-dashboard' ); ?></p>
					</div>
					<?php
				}
			} else {
				do_action( 'fed_override_default_page', $menus, $index );
			}

			do_action( 'fed_dashboard_panel_inside_bottom' );
			do_action( 'fed_dashboard_panel_inside_bottom_' . fed_get_data( 'menu_slug', $menu_item ) );
			?>
		</div>
	</div>
	<?php
}