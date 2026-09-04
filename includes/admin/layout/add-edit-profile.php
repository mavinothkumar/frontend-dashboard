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
 * Get Admin User Profile Role Based & Menu Access Settings.
 *
 * @param array  $row          User Profile Details.
 * @param string $action       Action type ('profile' or 'post').
 * @param array  $menu_options Menu options list.
 */
function fed_get_admin_up_role_based( $row, $action, $menu_options ) {
	$all_roles         = fed_get_user_roles();
	$total_roles_count = count( $all_roles );
	$options           = fed_get_key_value_array( $menu_options, 'menu_slug', 'menu' );
	$user_roles        = isset( $row['user_role'] ) && is_array( $row['user_role'] ) ? $row['user_role'] : array();

	// Check if all roles are active or if specific
	$active_roles_count = count( array_filter( $all_roles, function( $k ) use ( $user_roles ) {
		return in_array( $k, $user_roles, true );
	}, ARRAY_FILTER_USE_KEY ) );
	$is_all_roles_mode  = empty( $user_roles ) || ( $active_roles_count === $total_roles_count );
	?>
	<!-- Card: Access Control & Role Permissions -->
	<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
		<!-- Section Header -->
		<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
			<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
				<i class="fas fa-user-shield"></i>
			</div>
			<div>
				<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0">
					<?php echo ( 'post' === $action ) ? esc_html__( 'Access Control & Post Type Placement', 'frontend-dashboard' ) : esc_html__( 'Access Control & Menu Placement', 'frontend-dashboard' ); ?>
				</h3>
				<p class="text-xs text-slate-500 m-0 mt-0.5">
					<?php echo ( 'post' === $action ) ? esc_html__( 'Configure target post type and user role access permissions.', 'frontend-dashboard' ) : esc_html__( 'Configure dashboard menu placement and user role access permissions.', 'frontend-dashboard' ); ?>
				</p>
			</div>
		</div>

		<?php if ( 'profile' === $action ) : ?>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
				<!-- Menu Location Dropdown -->
				<div class="space-y-2">
					<label class="block text-xs font-bold text-slate-700">
						<?php esc_html_e( 'Menu Location', 'frontend-dashboard' ); ?>
					</label>
					<?php
					echo fed_input_box(
						'menu',
						array(
							'default_value' => 'Enable',
							'label'         => __( 'Menu Location', 'frontend-dashboard' ),
							'value'         => isset( $row['menu'] ) ? $row['menu'] : 'profile',
							'options'       => $options,
						),
						'select'
					);
					?>
					<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Which menu tab will render this field.', 'frontend-dashboard' ); ?></p>
				</div>

				<!-- Disable in User Profile Toggle -->
				<?php if ( ! isset( $row['input_meta'] ) || ( 'user_pass' !== $row['input_meta'] && 'confirmation_password' !== $row['input_meta'] ) ) : ?>
					<div class="space-y-2">
						<label class="block text-xs font-bold text-slate-700">
							<?php esc_html_e( 'Profile Visibility', 'frontend-dashboard' ); ?>
						</label>
						<div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3">
							<span class="text-xs font-semibold text-slate-700"><?php esc_html_e( 'Disable in Profile?', 'frontend-dashboard' ); ?></span>
							<?php
							echo fed_input_box(
								'show_user_profile',
								array(
									'default_value' => 'Disable',
									'value'         => isset( $row['show_user_profile'] ) ? $row['show_user_profile'] : 'Disable',
								),
								'checkbox'
							);
							?>
						</div>
						<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Hide this field on standard profile tab.', 'frontend-dashboard' ); ?></p>
					</div>
				<?php endif; ?>

				<!-- Read-Only / Disable User Edit Toggle -->
				<div class="space-y-2">
					<label class="block text-xs font-bold text-slate-700">
						<?php esc_html_e( 'User Edit Permissions', 'frontend-dashboard' ); ?>
					</label>
					<div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3">
						<span class="text-xs font-semibold text-slate-700"><?php esc_html_e( 'Admin-Only Edit (Read-Only)?', 'frontend-dashboard' ); ?></span>
						<?php
						echo fed_input_box(
							'extended[disable_user_access]',
							array(
								'default_value' => 'Disable',
								'value'         => fed_get_data( 'extended.disable_user_access', $row, 'Enable' ),
							),
							'checkbox'
						);
						?>
					</div>
					<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Only Administrator can update this field.', 'frontend-dashboard' ); ?></p>
				</div>
			</div>
		<?php else : ?>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
				<!-- Target Post Type Dropdown -->
				<div class="space-y-2">
					<label class="block text-xs font-bold text-slate-700">
						<?php esc_html_e( 'Target Post Type *', 'frontend-dashboard' ); ?>
					</label>
					<?php
					echo fed_input_box(
						'post_type',
						array(
							'default_value' => 'post',
							'label'         => __( 'Post Type', 'frontend-dashboard' ),
							'value'         => isset( $row['post_type'] ) ? $row['post_type'] : 'post',
							'options'       => fed_get_public_post_types(),
						),
						'select'
					);
					?>
					<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'The WordPress post type where this custom field will be attached.', 'frontend-dashboard' ); ?></p>
				</div>

				<!-- Read-Only / Disable User Edit Toggle -->
				<div class="space-y-2">
					<label class="block text-xs font-bold text-slate-700">
						<?php esc_html_e( 'User Edit Permissions', 'frontend-dashboard' ); ?>
					</label>
					<div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3">
						<span class="text-xs font-semibold text-slate-700"><?php esc_html_e( 'Admin-Only Edit (Read-Only)?', 'frontend-dashboard' ); ?></span>
						<?php
						echo fed_input_box(
							'extended[disable_user_access]',
							array(
								'default_value' => 'Disable',
								'value'         => fed_get_data( 'extended.disable_user_access', $row, 'Enable' ),
							),
							'checkbox'
						);
						?>
					</div>
					<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Only Administrator can update this field.', 'frontend-dashboard' ); ?></p>
				</div>
			</div>
		<?php endif; ?>

		<!-- Sub-Section: User Role Permissions -->
		<div class="fed_role_section pt-6 border-t border-slate-100 space-y-4">
			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
				<div>
					<h4 class="text-xs sm:text-sm font-bold text-slate-800 m-0"><?php esc_html_e( 'User Role Access Permissions', 'frontend-dashboard' ); ?></h4>
					<p class="text-[11px] text-slate-400 m-0 mt-0.5"><?php esc_html_e( 'Specify which user roles are permitted to view and use this field.', 'frontend-dashboard' ); ?></p>
				</div>

				<!-- Role Mode Segmented Selector -->
				<div class="inline-flex p-1 bg-slate-100 rounded-xl shrink-0 border border-slate-200/60">
					<button type="button" class="fed_role_mode_all_btn px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer <?php echo $is_all_roles_mode ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'; ?>">
						<i class="fas fa-globe mr-1.5 text-[10px]"></i> <?php esc_html_e( 'All Roles', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" class="fed_role_mode_specific_btn px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer <?php echo ! $is_all_roles_mode ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'; ?>">
						<i class="fas fa-user-lock mr-1.5 text-[10px]"></i> <?php esc_html_e( 'Specific Roles', 'frontend-dashboard' ); ?>
					</button>
				</div>
			</div>

			<!-- Searchable Specific Roles Container -->
			<div class="fed_specific_roles_wrapper space-y-3 pt-2 <?php echo $is_all_roles_mode ? 'hidden' : ''; ?>">
				<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
					<div class="relative flex-1 max-w-sm">
						<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">
							<i class="fas fa-search"></i>
						</span>
						<input type="text" placeholder="<?php echo esc_attr( sprintf( __( 'Filter %d user roles...', 'frontend-dashboard' ), $total_roles_count ) ); ?>" class="fed_role_search_filter w-full pr-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 transition-all outline-none font-medium" style="padding-left: 36px !important; height: 38px !important;" />
					</div>

					<div class="flex items-center gap-2 shrink-0 text-xs">
						<span class="fed_role_selected_count text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
							<span class="fed_role_selected_num"><?php echo ( $is_all_roles_mode ? $total_roles_count : $active_roles_count ); ?></span> / <?php echo $total_roles_count; ?> <?php esc_html_e( 'Roles Selected', 'frontend-dashboard' ); ?>
						</span>
						<button type="button" class="fed_roles_select_all text-xs font-bold text-indigo-600 hover:text-indigo-800 px-2.5 py-1 rounded-lg bg-indigo-50/60 hover:bg-indigo-50 cursor-pointer">
							<?php esc_html_e( 'Select All', 'frontend-dashboard' ); ?>
						</button>
						<span class="text-slate-300">|</span>
						<button type="button" class="fed_roles_deselect_all text-xs font-bold text-slate-500 hover:text-slate-700 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200/60 cursor-pointer">
							<?php esc_html_e( 'Clear All', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>

				<!-- Scrollable Chips Container -->
				<div class="fed_roles_chips_container flex flex-wrap gap-2.5 max-h-44 overflow-y-auto p-3.5 border border-slate-200/80 rounded-2xl bg-slate-50/70">
					<?php
					foreach ( $all_roles as $key => $role ) {
						$c_value    = ( $is_all_roles_mode || in_array( $key, $user_roles, true ) ) ? 'Enable' : 'Disable';
						$is_checked = ( 'Enable' === $c_value );
						?>
						<label class="fed-role-chip inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border transition-all cursor-pointer select-none <?php echo $is_checked ? 'bg-indigo-50/80 border-indigo-200 text-indigo-900 font-semibold' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'; ?>"
							data-role-name="<?php echo esc_attr( strtolower( $role ) ); ?>"
							data-role-key="<?php echo esc_attr( strtolower( $key ) ); ?>">
							<input type="checkbox"
								name="user_role[<?php echo esc_attr( $key ); ?>]"
								value="Enable"
								class="fed-role-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5 cursor-pointer"
								<?php checked( $is_checked, true ); ?> />
							<span class="text-xs truncate max-w-[140px]"><?php echo esc_html( $role ); ?></span>
						</label>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Get Admin User Profile Display & Form Permissions.
 *
 * @param array  $row    User Profile Details.
 * @param string $action Action Type.
 * @param string $type   Process Type.
 */
function fed_get_admin_up_display_permission( $row, $action, $type = '' ) {
	?>
	<!-- Card: Visibility & Display Permissions -->
	<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
		<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
			<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
				<i class="fas fa-eye"></i>
			</div>
			<div>
				<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Display & Form Rules', 'frontend-dashboard' ); ?></h3>
				<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Control where and how this field appears across frontend forms.', 'frontend-dashboard' ); ?></p>
			</div>
		</div>

		<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
			<?php
			if ( 'profile' === $action ) {
				$is_file_type  = ( 'file' === $type );
				$reg_val       = $is_file_type ? 'Disable' : ( isset( $row['show_register'] ) ? $row['show_register'] : 'Enable' );
				$dash_val      = isset( $row['show_dashboard'] ) ? $row['show_dashboard'] : 'Enable';
				?>
				<!-- Registration Form Toggle -->
				<div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3">
					<div>
						<span class="block text-xs font-bold text-slate-800"><?php esc_html_e( 'Registration Form', 'frontend-dashboard' ); ?></span>
						<span class="text-[11px] text-slate-400"><?php esc_html_e( 'Show on sign up form', 'frontend-dashboard' ); ?></span>
					</div>
					<?php
					echo fed_input_box(
						'show_register',
						array(
							'default_value' => 'Enable',
							'value'         => $reg_val,
							'disabled'      => $is_file_type,
						),
						'checkbox'
					);
					?>
				</div>

				<!-- User Dashboard Toggle -->
				<div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3">
					<div>
						<span class="block text-xs font-bold text-slate-800"><?php esc_html_e( 'User Dashboard', 'frontend-dashboard' ); ?></span>
						<span class="text-[11px] text-slate-400"><?php esc_html_e( 'Show in dashboard tab', 'frontend-dashboard' ); ?></span>
					</div>
					<?php
					echo fed_input_box(
						'show_dashboard',
						array(
							'default_value' => 'Enable',
							'value'         => $dash_val,
						),
						'checkbox'
					);
					?>
				</div>
			<?php } ?>

			<!-- Is Required Toggle -->
			<?php
			$req_val = isset( $row['is_required'] ) ? $row['is_required'] : 'false';
			?>
			<div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3">
				<div>
					<span class="block text-xs font-bold text-slate-800"><?php esc_html_e( 'Required Field', 'frontend-dashboard' ); ?></span>
					<span class="text-[11px] text-slate-400"><?php esc_html_e( 'Mandatory for user submission', 'frontend-dashboard' ); ?></span>
				</div>
				<?php
				echo fed_input_box(
					'is_required',
					array(
						'default_value' => 'true',
						'value'         => $req_val,
					),
					'checkbox'
				);
				?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Get Admin User Profile Label and Input Order Form Group.
 *
 * @param array $row User Profile Details.
 */
function fed_get_admin_up_label_input_order( $row ) {
	$change = '';
	if ( ! fed_check_field_is_belongs_to_extra( isset( $row['input_meta'] ) ? $row['input_meta'] : '' ) ) {
		$change = 'fed_input_label_for_onchange';
	}
	$label_val = isset( $row['label_name'] ) ? $row['label_name'] : '';
	$order_val = isset( $row['input_order'] ) ? $row['input_order'] : 0;
	?>
	<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
		<!-- Label Name -->
		<div class="space-y-2">
			<label class="block text-xs font-bold text-slate-700">
				<?php esc_html_e( 'Label Name *', 'frontend-dashboard' ); ?>
			</label>
			<?php
			echo fed_get_input_details(
				array(
					'input_type' => 'single_line',
					'input_meta' => 'label_name',
					'user_value' => $label_val,
					'class_name' => $change . ' fed-live-preview-label',
				)
			);
			?>
			<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'The visible label displayed on user forms.', 'frontend-dashboard' ); ?></p>
		</div>

		<!-- Input Order -->
		<div class="space-y-2">
			<label class="block text-xs font-bold text-slate-700">
				<?php esc_html_e( 'Input Order *', 'frontend-dashboard' ); ?>
			</label>
			<?php
			echo fed_get_input_details(
				array(
					'input_type' => 'number',
					'input_meta' => 'input_order',
					'user_value' => $order_val,
				)
			);
			?>
			<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Numerical sort order (lower numbers appear first).', 'frontend-dashboard' ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * Get Admin User Profile Input Meta (Database Key).
 *
 * @param array $row User Profile Data.
 */
function fed_get_admin_up_input_meta( $row ) {
	$meta_val = isset( $row['input_meta'] ) ? $row['input_meta'] : '';
	$is_extra = fed_check_field_is_belongs_to_extra( $meta_val );
	?>
	<div class="space-y-2">
		<div class="flex items-center justify-between">
			<label class="block text-xs font-bold text-slate-700">
				<?php esc_html_e( 'Input Meta Key *', 'frontend-dashboard' ); ?>
			</label>
			<?php if ( $is_extra && ! empty( $meta_val ) ) : ?>
				<span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
					<i class="fas fa-lock text-[9px]"></i> <?php esc_html_e( 'Locked Key', 'frontend-dashboard' ); ?>
				</span>
			<?php endif; ?>
		</div>

		<?php
		if ( $is_extra && ! empty( $meta_val ) ) {
			echo fed_input_box(
				'input_meta',
				array(
					'class'    => 'fed_admin_input_meta font-mono text-xs bg-slate-100 text-slate-600',
					'value'    => $meta_val,
					'readonly' => true,
				),
				'single_line'
			);

			echo fed_input_box(
				'fed_extra',
				array(
					'value' => $meta_val,
				),
				'hidden'
			);
		} else {
			echo fed_input_box(
				'input_meta',
				array(
					'class' => 'fed_admin_input_meta font-mono text-xs',
					'value' => $meta_val,
				),
				'single_line'
			);
		}
		?>
		<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Alpha-numeric and underscores only. Used as user_meta key.', 'frontend-dashboard' ); ?></p>
	</div>
	<?php
}

/**
 * Get Input Type Hidden Values and Save Action Button.
 *
 * @param string $input_type Submit Input Type.
 * @param string $action     Action Type.
 */
function fed_get_input_type_and_submit_btn( $input_type, $action ) {
	$get_payload = \FED\Helpers\InputHelper::get();
	$input_id    = isset( $get_payload['fed_input_id'] ) ? esc_attr( $get_payload['fed_input_id'] ) : '';
	$back_url    = ( 'post' === $action ) ? menu_page_url( 'fed_post_fields', false ) : menu_page_url( 'fed_user_profile', false );
	?>
	<div class="pt-3">
		<?php echo fed_input_box( 'input_type', array( 'value' => $input_type ), 'hidden' ); ?>
		<?php echo fed_input_box( 'input_id', array( 'value' => $input_id ), 'hidden' ); ?>
		<?php echo fed_input_box( 'fed_action', array( 'value' => $action ), 'hidden' ); ?>

		<!-- Form Actions Bar -->
		<div class="flex items-center justify-between gap-4 pt-6 border-t border-slate-100">
			<a href="<?php echo esc_url( $back_url ); ?>" class="fed-btn-secondary h-11 inline-flex items-center justify-center gap-2 px-5 rounded-2xl text-xs font-semibold no-underline transition-all cursor-pointer">
				<i class="fas fa-times text-xs"></i>
				<span><?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?></span>
			</a>

			<button type="submit" class="fed-btn-primary h-11 inline-flex items-center justify-center gap-2 px-7 rounded-2xl font-bold text-xs transition-all active:scale-95 cursor-pointer shadow-sm">
				<i class="fas fa-save text-xs" style="color: #ffffff !important;"></i>
				<span style="color: #ffffff !important;"><?php esc_html_e( 'Save Field Changes', 'frontend-dashboard' ); ?></span>
			</button>
		</div>
	</div>
	<?php
}

