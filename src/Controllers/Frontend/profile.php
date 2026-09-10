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
	$userRoles         = $user ? (array) $user->roles : [];
	$primaryRole       = ! empty( $userRoles[0] ) ? ucfirst( $userRoles[0] ) : 'Member';
	$registeredDate    = $user && ! empty( $user->user_registered ) ? date_i18n( get_option( 'date_format' ), strtotime( $user->user_registered ) ) : '';
	?>
	<div class="fed_dashboard_item space-y-6">

		<!-- Profile Hero Banner -->
		<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white shadow-sm border border-slate-800/80">
			<!-- Subtle background decorative glow -->
			<div class="absolute -right-12 -top-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
			<div class="absolute -left-12 -bottom-12 w-64 h-64 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>

			<div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
				<!-- Left: Avatar & Identity -->
				<div class="flex items-center gap-5">
					<div class="relative flex-shrink-0">
						<img class="w-20 h-20 sm:w-22 sm:h-22 rounded-2xl object-cover ring-4 ring-white/10 shadow-md bg-slate-800"
							 src="<?php echo esc_url( get_avatar_url( $user ? $user->ID : 0, [ 'size' => 180 ] ) ); ?>"
							 alt="<?php echo esc_attr( $user ? $user->display_name : '' ); ?>">
						<span class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-500 border-2 border-slate-900 rounded-full" title="<?php esc_attr_e( 'Active', 'frontend-dashboard' ); ?>"></span>
					</div>
					<div>
						<div class="flex flex-wrap items-center gap-2.5 mb-1">
							<h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white">
								<?php echo esc_html( $user ? $user->display_name : 'User' ); ?>
							</h1>
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
								<?php echo esc_html( $primaryRole ); ?>
							</span>
						</div>
						<p class="text-xs sm:text-sm text-slate-300 font-mono flex items-center gap-1.5 mb-2">
							<span>@<?php echo esc_html( $user ? $user->user_login : '' ); ?></span>
						</p>
						<p class="text-xs text-slate-400">
							<?php esc_html_e( 'Manage and update your account details, personal profile, and preferences.', 'frontend-dashboard' ); ?>
						</p>
					</div>
				</div>

				<!-- Right: Quick Metadata -->
				<?php if ( $registeredDate ) : ?>
					<div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-1 text-xs text-slate-400 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-800/80 w-full sm:w-auto">
						<span class="text-slate-400"><?php esc_html_e( 'Member Since', 'frontend-dashboard' ); ?></span>
						<span class="font-semibold text-slate-200"><?php echo esc_html( $registeredDate ); ?></span>
					</div>
				<?php endif; ?>
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

					// Separate fields into logical sections
					$account_fields  = [];
					$security_fields = [];
					$extra_fields    = [];

					foreach ( $profiles as $single_item ) {
						if ( 'user_pass' !== $single_item['input_meta'] && 'confirmation_password' !== $single_item['input_meta'] ) {
							$meta_key  = $single_item['input_meta'];
							$field_val = '';
							if ( $user && $user->ID ) {
								$meta_val = get_user_meta( $user->ID, $meta_key, true );
								if ( '' !== $meta_val && false !== $meta_val ) {
									$field_val = maybe_unserialize( $meta_val );
								} elseif ( $user->has_prop( $meta_key ) ) {
									$field_val = $user->get( $meta_key );
								}
							}
							$single_item['user_value'] = $field_val;
						} else {
							// Password fields are optional during profile edit
							$single_item['is_required'] = false;
							$single_item['placeholder'] = ( 'user_pass' === $single_item['input_meta'] )
								? __( 'Enter new password (optional)', 'frontend-dashboard' )
								: __( 'Confirm new password', 'frontend-dashboard' );
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

						if ( in_array( $single_item['input_meta'], [ 'user_login', 'user_email' ], true ) ) {
							$account_fields[] = $single_item;
						} elseif ( in_array( $single_item['input_meta'], [ 'user_pass', 'confirmation_password' ], true ) ) {
							$security_fields[] = $single_item;
						} else {
							$extra_fields[] = $single_item;
						}
					}
					?>
					<form method="post"
						  class="space-y-6"
						  action="<?php echo esc_url( add_query_arg( [ 'fed_nonce' => wp_create_nonce( 'fed_nonce' ) ], fed_get_form_action( 'fed_save_user_profile' ) ) ); ?>">
						<?php wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
						<input type="hidden" name="tab_id" value="<?php echo esc_attr( $index ); ?>"/>
						<input type="hidden" name="menu_type" value="<?php echo esc_attr( $menu_item['menu_type'] ); ?>"/>
						<input type="hidden" name="menu_slug" value="<?php echo esc_attr( $menu_item['menu_slug'] ); ?>"/>

						<?php do_action( 'fed_dashboard_profile_form_top' ); ?>

						<!-- Section 1: Account Information -->
						<?php if ( ! empty( $account_fields ) ) : ?>
							<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
								<div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
									<div class="flex items-center gap-3">
										<div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-semibold">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
										</div>
										<div>
											<h3 class="text-sm font-bold text-slate-900"><?php esc_html_e( 'Account Information', 'frontend-dashboard' ); ?></h3>
											<p class="text-xs text-slate-500"><?php esc_html_e( 'Primary account identity and contact details', 'frontend-dashboard' ); ?></p>
										</div>
									</div>
								</div>
								<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
									<?php foreach ( $account_fields as $field ) : ?>
										<?php
										$is_readonly = ! empty( $field['readonly'] );
										$is_login    = 'user_login' === $field['input_meta'];
										$is_email    = 'user_email' === $field['input_meta'];
										?>
										<div class="fed_dashboard_item_field">
											<div class="flex items-center justify-between mb-2">
												<label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
													<?php echo wp_kses_post( $field['label_name'] ); ?>
													<?php if ( ! empty( $field['is_required'] ) && 'Enable' === $field['is_required'] ) : ?>
														<span class="text-red-500">*</span>
													<?php endif; ?>
												</label>
												<?php if ( $is_readonly ) : ?>
													<span class="inline-flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 border border-slate-200">
														<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
														<?php esc_html_e( 'Non-editable', 'frontend-dashboard' ); ?>
													</span>
												<?php endif; ?>
											</div>
											<div class="mt-1">
												<?php echo fed_get_input_details( $field ); ?>
											</div>
											<p class="text-xs text-slate-400 mt-1.5">
												<?php
												if ( $is_login ) {
													esc_html_e( 'Usernames are unique across the platform and cannot be modified.', 'frontend-dashboard' );
												} elseif ( $is_email ) {
													esc_html_e( 'Used for account notifications and security alerts.', 'frontend-dashboard' );
												} elseif ( 'url' === ( $field['input_type'] ?? '' ) || 'user_url' === ( $field['input_meta'] ?? '' ) ) {
													esc_html_e( 'Please include http:// or https:// (e.g. https://example.com)', 'frontend-dashboard' );
												}
												?>
											</p>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<!-- Section 2: Security & Password -->
						<?php if ( ! empty( $security_fields ) ) : ?>
							<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
								<div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
									<div class="flex items-center gap-3">
										<div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-semibold">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
										</div>
										<div>
											<h3 class="text-sm font-bold text-slate-900"><?php esc_html_e( 'Security & Password', 'frontend-dashboard' ); ?></h3>
											<p class="text-xs text-slate-500"><?php esc_html_e( 'Leave password fields empty to keep your existing password', 'frontend-dashboard' ); ?></p>
										</div>
									</div>
								</div>
								<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
									<?php foreach ( $security_fields as $field ) : ?>
										<?php
										$is_pass = 'user_pass' === $field['input_meta'];
										?>
										<div class="fed_dashboard_item_field">
											<label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
												<?php echo wp_kses_post( $field['label_name'] ); ?>
											</label>
											<div class="mt-1">
												<?php echo fed_get_input_details( $field ); ?>
											</div>
											<p class="text-xs text-slate-400 mt-1.5">
												<?php
												if ( $is_pass ) {
													esc_html_e( 'Enter a new password or leave blank to keep unchanged.', 'frontend-dashboard' );
												} else {
													esc_html_e( 'Repeat your new password to confirm.', 'frontend-dashboard' );
												}
												?>
											</p>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<!-- Section 3: Additional Profile Fields (if any) -->
						<?php if ( ! empty( $extra_fields ) ) : ?>
							<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
								<div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
									<div class="flex items-center gap-3">
										<div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-semibold">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
										</div>
										<div>
											<h3 class="text-sm font-bold text-slate-900"><?php esc_html_e( 'Personal & Additional Details', 'frontend-dashboard' ); ?></h3>
											<p class="text-xs text-slate-500"><?php esc_html_e( 'Customize your personal information and preferences', 'frontend-dashboard' ); ?></p>
										</div>
									</div>
								</div>
								<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
									<?php
									foreach ( $extra_fields as $single_item ) {
										$isFullWidth = in_array( $single_item['input_type'] ?? '', [ 'multi_line', 'textarea', 'multiline', 'address' ], true );
										$isUrl       = ( 'url' === ( $single_item['input_type'] ?? '' ) || 'user_url' === ( $single_item['input_meta'] ?? '' ) );
										$isEmail     = ( 'email' === ( $single_item['input_type'] ?? '' ) || 'user_email' === ( $single_item['input_meta'] ?? '' ) );
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
											<?php if ( $isUrl ) : ?>
												<p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1">
													<svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
													<?php esc_html_e( 'Please include http:// or https:// (e.g. https://example.com)', 'frontend-dashboard' ); ?>
												</p>
											<?php elseif ( $isEmail ) : ?>
												<p class="text-xs text-slate-400 mt-1.5">
													<?php esc_html_e( 'Used for contact and notification purposes.', 'frontend-dashboard' ); ?>
												</p>
											<?php endif; ?>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						<?php endif; ?>

						<?php do_action( 'fed_dashboard_profile_form_bottom' ); ?>

						<style>
							.fed-profile-save-btn,
							button.fed-profile-save-btn {
								height: 38px !important;
								min-height: 38px !important;
								max-height: 38px !important;
								padding: 0 18px !important;
								font-size: 12px !important;
								font-weight: 600 !important;
								line-height: 1 !important;
								color: #ffffff !important;
								background-color: #4f46e5 !important;
								border: 1px solid #4338ca !important;
								border-radius: 10px !important;
								box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
								display: inline-flex !important;
								align-items: center !important;
								justify-content: center !important;
								gap: 6px !important;
								text-transform: none !important;
								letter-spacing: 0.01em !important;
								cursor: pointer !important;
								box-sizing: border-box !important;
							}
							.fed-profile-save-btn:hover,
							button.fed-profile-save-btn:hover {
								background-color: #4338ca !important;
								border-color: #3730a3 !important;
								color: #ffffff !important;
								box-shadow: 0 2px 4px -1px rgba(79, 70, 229, 0.2) !important;
							}
							.fed-profile-save-btn span {
								font-size: 12px !important;
								font-weight: 600 !important;
								line-height: 1 !important;
								color: #ffffff !important;
								text-transform: none !important;
								letter-spacing: 0.01em !important;
							}
							.fed-profile-save-btn svg {
								width: 14px !important;
								height: 14px !important;
								min-width: 14px !important;
								max-width: 14px !important;
								color: #ffffff !important;
								stroke: #ffffff !important;
							}
						</style>

						<!-- Form Action Footer Bar -->
						<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs px-6 py-3.5 flex items-center justify-end">
							<button type="submit"
									id="fed_profile_submit_btn"
									class="fed-profile-save-btn inline-flex items-center justify-center gap-1.5 px-4.5 rounded-xl font-semibold text-xs tracking-wide text-white shadow-2xs hover:shadow-xs active:scale-[0.98] transition-all duration-150 cursor-pointer w-full sm:w-auto"
									style="height: 38px !important; min-height: 38px !important; padding: 0 18px !important; font-size: 12px !important; line-height: 1 !important; font-weight: 600 !important; background-color: #4f46e5 !important; color: #ffffff !important; border: 1px solid #4338ca !important; border-radius: 10px !important; text-transform: none !important; letter-spacing: 0.01em !important;">
								<svg class="w-3.5 h-3.5 shrink-0" style="width: 14px !important; height: 14px !important; color: #ffffff !important; stroke: #ffffff !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
								</svg>
								<span style="font-size: 12px !important; line-height: 1 !important; color: #ffffff !important; font-weight: 600 !important; text-transform: none !important; letter-spacing: 0.01em !important;"><?php esc_html_e( 'Save Changes', 'frontend-dashboard' ); ?></span>
							</button>
						</div>

					</form>
					<?php
				} else {
					?>
					<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs text-center py-12 px-6 text-slate-400">
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