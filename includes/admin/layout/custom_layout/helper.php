<?php
/**
 * Helper functions.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Common Simple Layout.
 *
 * @param  array  $form  Form.
 */
function fed_common_simple_layout( $form ) {
	$form_method       = isset( $form['form']['method'] ) && ! empty( $form['form']['method'] ) ? esc_attr( $form['form']['method'] ) : 'post';
	$form_class        = isset( $form['form']['class'] ) && ! empty( $form['form']['class'] ) ? esc_attr( $form['form']['class'] ) : '';
	$form_attr         = isset( $form['form']['attr'] ) && ! empty( $form['form']['attr'] ) ? esc_attr( $form['form']['attr'] ) : '';
	$form_loader       = isset( $form['form']['is_loader'] ) && ! empty( $form['form']['attr'] ) ? esc_attr( $form['form']['attr'] ) : fed_loader();
	$form_nonce_action = isset( $form['form']['nonce']['action'] ) && ! empty( $form['form']['nonce']['action'] ) ? esc_attr( $form['form']['nonce']['action'] ) : 'fed_nonce';
	$form_nonce_name   = isset( $form['form']['nonce']['name'] ) && ! empty( $form['form']['nonce']['name'] ) ? esc_attr( $form['form']['nonce']['name'] ) : 'fed_nonce';

	if ( isset( $form['form']['action'] ) && is_array( $form['form']['action'] ) ) {
		$url         = isset( $form['form']['action']['url'] ) && ! empty( $form['form']['action']['url'] ) ? esc_url( $form['form']['action']['url'] ) : admin_url( 'admin-ajax.php' );
		$parameters  = isset( $form['form']['action']['parameters'] ) && is_array( $form['form']['action']['parameters'] ) ? http_build_query( $form['form']['action']['parameters'] ) : '';
		$form_action = isset( $form['form']['action']['action'] ) && ! empty( $form['form']['action']['action'] ) ? $url . '?action=' . esc_attr( $form['form']['action']['action'] ) . '&' . $parameters : $url;
	} else {
		$form_action = admin_url( 'admin-ajax.php?action=fed_admin_setting_form' );
	}
	?>
	<div class="space-y-6">
		<?php
		if ( isset( $form['note']['header'] ) && ! empty( $form['note']['header'] ) ) {
			echo '<div class="p-4 bg-indigo-50/60 border border-indigo-100 rounded-2xl text-xs text-indigo-800">' . esc_html( $form['note']['header'] ) . '</div>';
		}
		?>
		<form method="<?php echo esc_attr( $form_method ); ?>"
				class="<?php echo esc_attr( $form_class ); ?> space-y-6" <?php echo esc_attr( $form_attr ); ?>
				action="<?php echo esc_attr( $form_action ); ?>">

			<?php fed_wp_nonce_field( $form_nonce_action, $form_nonce_name ); ?>
			<?php echo $form_loader; ?>

			<?php
			if ( isset( $form['hidden'] ) && is_array( $form['hidden'] ) ) {
				foreach ( $form['hidden'] as $hindex => $hidden ) {
					echo fed_get_input_details( $hidden );
				}
			}
			?>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
				<?php
				if ( isset( $form['input'] ) && is_array( $form['input'] ) && count( $form['input'] ) ) {
					foreach ( $form['input'] as $iindex => $input ) {
						$is_full_width = ( isset( $input['col'] ) && strpos( $input['col'], '12' ) !== false ) || isset( $input['header'] ) || isset( $input['extra'] );
						$name          = isset( $input['name'] ) && ! empty( $input['name'] ) ? $input['name'] : $iindex;
						?>
						<div class="space-y-2 <?php echo $is_full_width ? 'md:col-span-2' : ''; ?>">
							<?php if ( isset( $input['name'] ) && null !== $input['name'] ) : ?>
								<label class="block text-xs font-bold text-slate-700">
									<?php echo esc_html( $name ); ?>
									<?php if ( ! empty( $input['required'] ) ) : ?>
										<span class="text-rose-500">*</span>
									<?php endif; ?>
								</label>
							<?php endif; ?>

							<?php if ( isset( $input['header'] ) ) : ?>
								<div class="pt-3 pb-1 border-b border-slate-100">
									<label class="block text-xs font-bold text-slate-800">
										<?php echo esc_html( $input['header'] ); ?>
									</label>
								</div>
							<?php endif; ?>

							<?php
							if ( isset( $input['input'] ) ) {
								if ( is_array( $input['input'] ) ) {
									echo fed_get_input_details( $input['input'] );
								} else {
									echo $input['input'];
								}
							}

							if ( isset( $input['extra']['input'] ) && is_array( $input['extra']['input'] ) ) {
								echo '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5 pt-2">';
								foreach ( $input['extra']['input'] as $eindex => $extra ) {
									$input_data = $extra;
									$item_label = isset( $extra['label'] ) ? $extra['label'] : $eindex;
									$input_data['label'] = '';
									?>
									<label class="p-3 bg-slate-50/80 hover:bg-slate-100/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-2 cursor-pointer transition-colors">
										<span class="text-xs font-bold text-slate-800 select-none"><?php echo esc_html( $item_label ); ?></span>
										<?php echo fed_get_input_details( $input_data ); ?>
									</label>
									<?php
								}
								echo '</div>';
							}
							?>

							<?php if ( ! empty( $input['help_message'] ) ) : ?>
								<p class="text-[11px] text-slate-400 m-0"><?php echo wp_strip_all_tags( $input['help_message'] ); ?></p>
							<?php endif; ?>
						</div>
						<?php
					}

					do_action( 'fed_admin_login_settings_template', $form );
					?>

					<div class="md:col-span-2 pt-4 border-t border-slate-100 flex items-center justify-end">
						<button type="submit" class="fed-btn-primary h-11 inline-flex items-center justify-center gap-2 px-6 rounded-xl font-semibold text-xs tracking-wide shadow-sm transition-all active:scale-95 cursor-pointer">
							<i class="fas fa-save text-xs" style="color: #ffffff !important;"></i>
							<span style="color: #ffffff !important;"><?php esc_html_e( 'Save Changes', 'frontend-dashboard' ); ?></span>
						</button>
					</div>
					<?php
				}
				?>
			</div>
		</form>
		<?php
		if ( isset( $form['note']['footer'] ) && ! empty( $form['note']['footer'] ) ) {
			echo '<div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-600">' . esc_html( $form['note']['footer'] ) . '</div>';
		}
		?>
	</div>
	<?php
}


/**
 * Common Layouts Admin Settings.
 *
 * @param  array  $fed_admin_options  Admin Options.
 * @param  array  $tabs  Tabs.
 */
function fed_common_layouts_admin_settings( $fed_admin_options, $tabs ) {
	$no = mt_rand( 1000, 9999 );
	?>
	<div class="flex flex-col lg:flex-row gap-6 items-start w-full fed-settings-subtab-container" id="fed_subtabs_wrap_<?php echo esc_attr( $no ); ?>">
		<!-- Left Subtab Sidebar -->
		<div class="w-full lg:w-64 shrink-0">
			<div class="bg-white rounded-3xl p-3 border border-slate-200/80 shadow-xs space-y-1.5" role="tablist">
				<?php
				$menu_count = 0;
				foreach ( $tabs as $index => $tab ) {
					$active = ( 0 === $menu_count );
					$menu_count ++;
					?>
					<a href="#<?php echo esc_attr( $index ); ?>"
					   data-target="#subtab_pane_<?php echo esc_attr( $index . '_' . $no ); ?>"
					   role="tab"
					   data-toggle="tab"
					   class="fed-subtab-link flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-semibold transition-all cursor-pointer no-underline <?php echo $active ? 'fed-subtab-active bg-indigo-50 border border-indigo-200 text-indigo-700 shadow-2xs font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-transparent'; ?>">
						<div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 <?php echo $active ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-500'; ?>">
							<i class="<?php echo esc_attr( $tab['icon'] ); ?>"></i>
						</div>
						<span class="truncate"><?php echo esc_html( $tab['name'] ); ?></span>
					</a>
				<?php } ?>
			</div>
		</div>

		<!-- Right Subtab Content Pane -->
		<div class="flex-1 min-w-0 w-full">
			<div class="tab-content w-full">
				<?php
				$content_count = 0;
				foreach ( $tabs as $index => $tab ) {
					$active = ( 0 === $content_count );
					$content_count ++;
					?>
					<div role="tabpanel"
						 class="tab-pane <?php echo $active ? 'active block' : 'hidden'; ?>"
						 id="subtab_pane_<?php echo esc_attr( $index . '_' . $no ); ?>"
						 data-pane="<?php echo esc_attr( $index ); ?>">
						<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
							<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
								<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0">
									<i class="<?php echo esc_attr( $tab['icon'] ); ?>"></i>
								</div>
								<div>
									<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php echo esc_html( $tab['name'] ); ?></h3>
									<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Configure options and preferences.', 'frontend-dashboard' ); ?></p>
								</div>
							</div>

							<div>
								<?php
								fed_call_function_method( $tab );
								?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render User Roles Badges with "+N more" pill.
 *
 * @param array $selected   Selected roles (keys or values array).
 * @param array $all_roles  All available roles (optional).
 * @param int   $limit      Number of role names to display before showing "+N". Default 2.
 * @return string HTML output
 */
function fed_render_user_roles_badge( $selected = array(), $all_roles = null, $limit = 2 ) {
	if ( null === $all_roles ) {
		$all_roles = fed_get_user_roles();
	}
	$total_count = count( $all_roles );

	if ( is_array( $selected ) && ! empty( $selected ) ) {
		$keys = array();
		foreach ( $selected as $k => $v ) {
			if ( is_numeric( $k ) && is_string( $v ) ) {
				$keys[] = $v;
			} elseif ( 'Enable' === $v || true === $v || 1 === $v || '1' === $v ) {
				$keys[] = $k;
			}
		}
		$selected = $keys;
	} else {
		$selected = array();
	}

	$selected_count = count( $selected );

	// If empty or all roles selected
	if ( empty( $selected ) || $selected_count >= $total_count ) {
		return '<span class="fed-badge-item inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 shrink-0 whitespace-nowrap"><i class="fas fa-users text-[9px]"></i> ' . esc_html__( 'All Roles', 'frontend-dashboard' ) . '</span>';
	}

	$role_names = array();
	foreach ( $selected as $r_key ) {
		if ( isset( $all_roles[ $r_key ] ) ) {
			$role_names[] = $all_roles[ $r_key ];
		}
	}

	if ( empty( $role_names ) ) {
		return '<span class="fed-badge-item inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 shrink-0 whitespace-nowrap"><i class="fas fa-users text-[9px]"></i> ' . esc_html__( 'All Roles', 'frontend-dashboard' ) . '</span>';
	}

	$visible_names        = array_slice( $role_names, 0, $limit );
	$remaining_count      = count( $role_names ) - count( $visible_names );
	$all_selected_tooltip = esc_attr( implode( ', ', $role_names ) );

	$html = '<span class="fed-badge-item inline-flex items-center gap-1.5 text-[11px] text-slate-600 font-medium shrink-0 whitespace-nowrap" title="' . $all_selected_tooltip . '">';
	$html .= '<i class="fas fa-user-tag text-slate-400 text-[10px]"></i> ';
	$html .= '<span class="font-semibold text-slate-800">' . esc_html( implode( ', ', $visible_names ) ) . '</span>';

	if ( $remaining_count > 0 ) {
		$html .= ' <span class="inline-flex items-center justify-center px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200" title="' . $all_selected_tooltip . '">+' . (int) $remaining_count . '</span>';
	}
	$html .= '</span>';

	return $html;
}

/**
 * Render Interactive User Role Selector Component.
 *
 * @param array $args Arguments.
 */
function fed_render_user_roles_selector( $args = array() ) {
	static $script_printed = false;

	$name_prefix         = isset( $args['name_prefix'] ) ? $args['name_prefix'] : 'user_role';
	$selected            = isset( $args['selected'] ) ? $args['selected'] : array();
	$all_roles           = isset( $args['all_roles'] ) ? $args['all_roles'] : fed_get_user_roles();
	$title               = isset( $args['title'] ) ? $args['title'] : '';
	$description         = isset( $args['description'] ) ? $args['description'] : '';
	$show_mode_switch    = isset( $args['show_mode_switch'] ) ? (bool) $args['show_mode_switch'] : false;
	$default_all_checked = isset( $args['default_all_checked'] ) ? (bool) $args['default_all_checked'] : false;
	$wrapper_class       = isset( $args['wrapper_class'] ) ? $args['wrapper_class'] : '';

	// Normalize selected roles
	$selected_keys = array();
	if ( is_array( $selected ) ) {
		foreach ( $selected as $k => $v ) {
			if ( is_numeric( $k ) && is_string( $v ) ) {
				$selected_keys[] = $v;
			} elseif ( 'Enable' === $v || true === $v || 1 === $v || '1' === $v ) {
				$selected_keys[] = $k;
			}
		}
	}

	$total_roles_count = count( $all_roles );
	$active_roles_count = 0;
	foreach ( $all_roles as $k => $r ) {
		if ( in_array( $k, $selected_keys, true ) || ( empty( $selected_keys ) && $default_all_checked ) ) {
			$active_roles_count ++;
		}
	}

	$is_all_roles_mode = $show_mode_switch && ( empty( $selected_keys ) || ( $active_roles_count === $total_roles_count ) );
	?>
	<div class="fed-role-selector-widget space-y-4 <?php echo esc_attr( $wrapper_class ); ?>">
		<?php if ( ! empty( $title ) || $show_mode_switch ) : ?>
			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-100">
				<?php if ( ! empty( $title ) ) : ?>
					<div>
						<h4 class="text-xs sm:text-sm font-bold text-slate-800 m-0"><?php echo esc_html( $title ); ?></h4>
						<?php if ( ! empty( $description ) ) : ?>
							<p class="text-[11px] text-slate-400 m-0 mt-0.5"><?php echo esc_html( $description ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $show_mode_switch ) : ?>
					<div class="inline-flex p-1 bg-slate-100 rounded-xl shrink-0 border border-slate-200/60">
						<button type="button" class="fed_role_mode_all_btn px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer <?php echo $is_all_roles_mode ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'; ?>">
							<i class="fas fa-globe mr-1.5 text-[10px]"></i> <?php esc_html_e( 'All Roles', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" class="fed_role_mode_specific_btn px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer <?php echo ! $is_all_roles_mode ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'; ?>">
							<i class="fas fa-user-lock mr-1.5 text-[10px]"></i> <?php esc_html_e( 'Specific Roles', 'frontend-dashboard' ); ?>
						</button>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Searchable Specific Roles Container -->
		<div class="fed_specific_roles_wrapper space-y-3 <?php echo $is_all_roles_mode ? 'hidden' : ''; ?>">
			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
				<div class="relative flex-1 max-w-sm">
					<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">
						<i class="fas fa-search"></i>
					</span>
					<input type="text" placeholder="<?php echo esc_attr( sprintf( __( 'Filter %d user roles...', 'frontend-dashboard' ), $total_roles_count ) ); ?>" class="fed_role_search_filter w-full pr-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 transition-all outline-none font-medium" style="padding-left: 36px !important; height: 38px !important;" />
				</div>

				<div class="flex items-center gap-2 shrink-0 text-xs">
					<span class="fed_role_selected_count text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
						<span class="fed_role_selected_num"><?php echo ( $is_all_roles_mode ? $total_roles_count : $active_roles_count ); ?></span> / <?php echo $total_roles_count; ?> <?php esc_html_e( 'Selected', 'frontend-dashboard' ); ?>
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
					$is_checked = in_array( $key, $selected_keys, true ) || ( empty( $selected_keys ) && $default_all_checked );
					$input_name = ( false !== strpos( $name_prefix, '[' ) ) ? $name_prefix . '[' . $key . ']' : $name_prefix . '[' . $key . ']';
					?>
					<label class="fed-role-chip inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border transition-all cursor-pointer select-none <?php echo $is_checked ? 'bg-indigo-50/80 border-indigo-200 text-indigo-900 font-semibold' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'; ?>"
						data-role-name="<?php echo esc_attr( strtolower( $role ) ); ?>"
						data-role-key="<?php echo esc_attr( strtolower( $key ) ); ?>">
						<input type="checkbox"
							name="<?php echo esc_attr( $input_name ); ?>"
							value="Enable"
							class="fed-role-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5 cursor-pointer"
							<?php checked( $is_checked, true ); ?> />
						<span class="text-xs truncate max-w-[150px]"><?php echo esc_html( $role ); ?></span>
					</label>
				<?php } ?>
			</div>
		</div>
	</div>

	<?php if ( ! $script_printed ) : $script_printed = true; ?>
		<script>
			(function($) {
				'use strict';
				function updateWidgetCounter($widget) {
					var total = $widget.find('.fed-role-checkbox').length;
					var selected = $widget.find('.fed-role-checkbox:checked').length;
					$widget.find('.fed_role_selected_num').text(selected);

					$widget.find('.fed-role-chip').each(function() {
						var $chip = $(this);
						var $cb = $chip.find('.fed-role-checkbox');
						if ($cb.is(':checked')) {
							$chip.addClass('bg-indigo-50/80 border-indigo-200 text-indigo-900 font-semibold')
								 .removeClass('bg-white border-slate-200 text-slate-700');
						} else {
							$chip.removeClass('bg-indigo-50/80 border-indigo-200 text-indigo-900 font-semibold')
								 .addClass('bg-white border-slate-200 text-slate-700');
						}
					});
				}

				// Live search filtering
				$(document).on('input', '.fed_role_search_filter', function() {
					var $input = $(this);
					var $widget = $input.closest('.fed-role-selector-widget');
					var query = $.trim($input.val()).toLowerCase();

					$widget.find('.fed-role-chip').each(function() {
						var name = $(this).attr('data-role-name') || '';
						var key = $(this).attr('data-role-key') || '';
						if (!query || name.indexOf(query) !== -1 || key.indexOf(query) !== -1) {
							$(this).removeClass('hidden').css('display', 'inline-flex');
						} else {
							$(this).addClass('hidden').hide();
						}
					});
				});

				// Select All
				$(document).on('click', '.fed_roles_select_all', function(e) {
					e.preventDefault();
					var $widget = $(this).closest('.fed-role-selector-widget');
					$widget.find('.fed-role-chip:visible .fed-role-checkbox').prop('checked', true).trigger('change');
				});

				// Clear All
				$(document).on('click', '.fed_roles_deselect_all', function(e) {
					e.preventDefault();
					var $widget = $(this).closest('.fed-role-selector-widget');
					$widget.find('.fed-role-chip:visible .fed-role-checkbox').prop('checked', false).trigger('change');
				});

				// Checkbox toggle
				$(document).on('change', '.fed-role-checkbox', function() {
					var $widget = $(this).closest('.fed-role-selector-widget');
					updateWidgetCounter($widget);
				});

				// All Roles / Specific Roles Segmented Buttons
				$(document).on('click', '.fed_role_mode_all_btn', function(e) {
					e.preventDefault();
					var $widget = $(this).closest('.fed-role-selector-widget');
					$(this).addClass('bg-white text-indigo-700 shadow-2xs').removeClass('text-slate-600 hover:text-slate-900');
					$widget.find('.fed_role_mode_specific_btn').removeClass('bg-white text-indigo-700 shadow-2xs').addClass('text-slate-600 hover:text-slate-900');
					$widget.find('.fed_specific_roles_wrapper').addClass('hidden');
					$widget.find('.fed-role-checkbox').prop('checked', true).trigger('change');
				});

				$(document).on('click', '.fed_role_mode_specific_btn', function(e) {
					e.preventDefault();
					var $widget = $(this).closest('.fed-role-selector-widget');
					$(this).addClass('bg-white text-indigo-700 shadow-2xs').removeClass('text-slate-600 hover:text-slate-900');
					$widget.find('.fed_role_mode_all_btn').removeClass('bg-white text-indigo-700 shadow-2xs').addClass('text-slate-600 hover:text-slate-900');
					$widget.find('.fed_specific_roles_wrapper').removeClass('hidden');
				});
			})(jQuery);
		</script>
	<?php endif;
}
