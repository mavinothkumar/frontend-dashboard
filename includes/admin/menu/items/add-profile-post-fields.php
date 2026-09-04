<?php
/**
 * Add Profile / Post Fields Management Interface.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Profile / Post Fields Page.
 */
function fed_get_add_profile_post_fields() {
	wp_enqueue_script( 'jquery-ui-sortable' );

	$get_payload         = array_merge( \FED\Helpers\InputHelper::get(), \FED\Helpers\InputHelper::post() );
	$id                  = '';
	$add_edit_action     = __( 'Add New ', 'frontend-dashboard' );
	$selected            = 'single_line';
	$action              = isset( $get_payload['fed_action'] ) ? esc_attr( $get_payload['fed_action'] ) : 'profile';
	$preselect_menu      = isset( $get_payload['menu'] ) ? esc_attr( $get_payload['menu'] ) : 'profile';
	$preselect_post_type = isset( $get_payload['post_type'] ) ? esc_attr( $get_payload['post_type'] ) : 'post';

	if ( isset( $get_payload['fed_input_id'] ) && ! empty( $get_payload['fed_input_id'] ) ) {
		$id              = (int) $get_payload['fed_input_id'];
		$add_edit_action = __( 'Edit ', 'frontend-dashboard' );
	}

	if ( ( 'profile' !== $action ) && ( 'post' !== $action ) ) {
		$action = 'profile';
	}

	if ( 'profile' === $action ) {
		$page     = __( 'User Profile', 'frontend-dashboard' );
		$page_url = menu_page_url( 'fed_user_profile', false );

		if ( is_int( $id ) && $id > 0 ) {
			$rows = fed_fetch_table_row_by_id( BC_FED_TABLE_USER_PROFILE, $id );
			if ( $rows instanceof WP_Error ) {
				?>
				<div class="bc_fed max-w-7xl mx-auto px-4 sm:px-6 py-8 font-sans">
					<div class="p-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center gap-3">
						<i class="fas fa-exclamation-circle text-rose-500 text-xl shrink-0"></i>
						<div>
							<h4 class="font-bold text-sm mb-0.5"><?php esc_html_e( 'Database Error', 'frontend-dashboard' ); ?></h4>
							<p class="text-xs m-0"><?php echo esc_html( $rows->get_error_message() ); ?></p>
						</div>
					</div>
				</div>
				<?php
				return;
			}
			$row      = fed_process_user_profile( $rows, $action );
			$selected = ! empty( $row['input_type'] ) ? $row['input_type'] : 'single_line';
		} else {
			$row = fed_get_empty_value_for_user_profile( $action );
			if ( ! empty( $preselect_menu ) ) {
				$row['menu'] = $preselect_menu;
			}
		}
	}

	if ( 'post' === $action ) {
		$page     = __( 'Post Fields', 'frontend-dashboard' );
		$page_url = menu_page_url( 'fed_post_fields', false );

		if ( is_int( $id ) && $id > 0 ) {
			$rows = fed_fetch_table_row_by_id( BC_FED_TABLE_POST, $id );
			if ( $rows instanceof WP_Error ) {
				?>
				<div class="bc_fed max-w-7xl mx-auto px-4 sm:px-6 py-8 font-sans">
					<div class="p-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center gap-3">
						<i class="fas fa-exclamation-circle text-rose-500 text-xl shrink-0"></i>
						<div>
							<h4 class="font-bold text-sm mb-0.5"><?php esc_html_e( 'Database Error', 'frontend-dashboard' ); ?></h4>
							<p class="text-xs m-0"><?php echo esc_html( $rows->get_error_message() ); ?></p>
						</div>
					</div>
				</div>
				<?php
				return;
			}
			$row      = fed_process_user_profile( $rows, $action );
			$selected = ! empty( $row['input_type'] ) ? $row['input_type'] : 'single_line';
		} else {
			$row = fed_get_empty_value_for_user_profile( $action );
			if ( ! empty( $preselect_post_type ) ) {
				$row['post_type'] = $preselect_post_type;
			}
		}
	}

	$menu_options = fed_fetch_menu();
	$buttons      = fed_admin_user_profile_select( $selected );
	$is_editing   = ( is_int( $id ) && $id > 0 );
	$field_label  = isset( $row['label_name'] ) ? $row['label_name'] : '';
	$field_meta   = isset( $row['input_meta'] ) ? $row['input_meta'] : '';
	$field_order  = isset( $row['input_order'] ) ? $row['input_order'] : 0;
	$field_menu   = isset( $row['menu'] ) && ! empty( $row['menu'] ) ? $row['menu'] : $preselect_menu;
	$nonce        = wp_create_nonce( 'fed_nonce' );
	$ajax_url     = admin_url( 'admin-ajax.php' );

	// Categorize buttons for Field Palette (Ninja Forms style)
	$common_types = array( 'single_line', 'multi_line', 'number', 'email', 'password', 'url' );
	$choice_types = array( 'select', 'radio', 'checkbox' );
	$categorized  = array(
		'common'   => array(),
		'choice'   => array(),
		'advanced' => array(),
	);

	foreach ( $buttons['options'] as $btn_key => $btn_data ) {
		if ( in_array( $btn_key, $common_types, true ) ) {
			$categorized['common'][ $btn_key ] = $btn_data;
		} elseif ( in_array( $btn_key, $choice_types, true ) ) {
			$categorized['choice'][ $btn_key ] = $btn_data;
		} else {
			$categorized['advanced'][ $btn_key ] = $btn_data;
		}
	}

	// Menu / Post Type display title
	if ( 'post' === $action ) {
		$public_pts       = fed_get_public_post_types();
		$field_post_type  = isset( $row['post_type'] ) && ! empty( $row['post_type'] ) ? $row['post_type'] : $preselect_post_type;
		$active_menu_name = isset( $public_pts[ $field_post_type ] ) ? $public_pts[ $field_post_type ] : ucfirst( $field_post_type );
	} else {
		$active_menu_name = ucfirst( $field_menu );
		foreach ( $menu_options as $m_opt ) {
			if ( isset( $m_opt['menu_slug'] ) && $m_opt['menu_slug'] === $field_menu ) {
				$active_menu_name = $m_opt['menu'];
				break;
			}
		}
	}

	// Extensible Left-Column Tabs via filter
	$default_tabs = array(
		'fields' => array(
			'label' => __( 'Form Fields', 'frontend-dashboard' ),
			'icon'  => 'fas fa-cubes',
		),
	);
	$editor_tabs = apply_filters( 'fed_admin_field_editor_tabs', $default_tabs, $row, $action );
	?>

	<!-- Scoped Styles -->
	<style>
		.fed-builder-grid {
			display: grid;
			grid-template-columns: 1fr;
			gap: 2rem;
			align-items: start;
			width: 100%;
		}
		@media (min-width: 1024px) {
			.fed-builder-grid {
				grid-template-columns: minmax(420px, 460px) minmax(0, 1fr) !important;
			}
		}
		.fed-btn-primary,
		button.fed-btn-primary,
		a.fed-btn-primary {
			background-color: #4f46e5 !important;
			color: #ffffff !important;
			border: 1px solid #4338ca !important;
			box-shadow: 0 2px 4px -1px rgba(79, 70, 229, 0.2) !important;
		}
		.fed-btn-primary:hover,
		button.fed-btn-primary:hover,
		a.fed-btn-primary:hover {
			background-color: #4338ca !important;
			color: #ffffff !important;
			border-color: #3730a3 !important;
		}
		.fed-btn-secondary,
		button.fed-btn-secondary,
		a.fed-btn-secondary {
			background-color: #f8fafc !important;
			color: #334155 !important;
			border: 1px solid #e2e8f0 !important;
		}
		.fed-btn-secondary:hover,
		button.fed-btn-secondary:hover,
		a.fed-btn-secondary:hover {
			background-color: #f1f5f9 !important;
			color: #1e293b !important;
			border-color: #cbd5e1 !important;
		}
		.bc_fed input[type="text"],
		.bc_fed input[type="number"],
		.bc_fed input[type="email"],
		.bc_fed input[type="password"],
		.bc_fed input[type="url"],
		.bc_fed select,
		.bc_fed textarea {
			border: 1px solid #e2e8f0 !important;
			border-radius: 12px !important;
			background-color: #f8fafc !important;
			font-size: 12px !important;
			color: #334155 !important;
			padding: 8px 12px !important;
			transition: all 0.2s ease !important;
			width: 100% !important;
			box-shadow: none !important;
			box-sizing: border-box !important;
		}
		.bc_fed input[type="text"]:focus,
		.bc_fed input[type="number"]:focus,
		.bc_fed input[type="email"]:focus,
		.bc_fed input[type="password"]:focus,
		.bc_fed input[type="url"]:focus,
		.bc_fed select:focus,
		.bc_fed textarea:focus {
			border-color: #6366f1 !important;
			background-color: #ffffff !important;
			box-shadow: 0 0 0 1px #6366f1 !important;
			outline: none !important;
		}
		.bc_fed #fed_field_types_search {
			padding-left: 40px !important;
			padding-right: 12px !important;
			height: 42px !important;
			font-size: 12px !important;
			border: 1px solid #e2e8f0 !important;
			border-radius: 12px !important;
			background-color: #f8fafc !important;
			width: 100% !important;
			box-sizing: border-box !important;
		}
		.bc_fed #fed_field_types_search:focus {
			border-color: #6366f1 !important;
			background-color: #ffffff !important;
			box-shadow: 0 0 0 1px #6366f1 !important;
		}
		.bc_fed .fed-editor-tab-btn.is-active {
			background-color: #ffffff !important;
			border-color: #6366f1 !important;
			color: #4f46e5 !important;
			box-shadow: 0 1px 3px 0 rgba(99, 102, 241, 0.15) !important;
		}
		.bc_fed .fed-editor-tab-btn.is-active i {
			color: #4f46e5 !important;
		}
		.bc_fed .fed-field-tile.is-active,
		.bc_fed .fed-field-tile.active {
			background-color: #0f172a !important;
			color: #ffffff !important;
			border-color: #6366f1 !important;
			box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.2) !important;
		}
		.bc_fed .fed-field-tile.is-active i,
		.bc_fed .fed-field-tile.active i {
			color: #38bdf8 !important;
		}
		.bc_fed .fed_input_type_container.hide,
		.bc_fed .fed_input_type_container.hidden {
			display: none !important;
		}
	</style>

	<div class="bc_fed fed-admin-wrap w-full max-w-none px-6 sm:px-10 py-8 font-sans text-slate-800 fed_add_edit_input_container" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-ajax-url="<?php echo esc_url( $ajax_url ); ?>">
		<?php echo fed_loader(); ?>

		<!-- Toast Notification Element -->
		<div id="fed_toast_notification" class="fixed bottom-6 right-6 transform translate-y-16 opacity-0 transition-all duration-300 pointer-events-none flex items-center gap-3 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700" style="z-index: 99999999 !important;">
			<span id="fed_toast_icon" class="text-emerald-400 text-base"><i class="fas fa-check-circle"></i></span>
			<span id="fed_toast_message" class="text-xs font-semibold tracking-wide">Changes saved successfully.</span>
		</div>

		<!-- Top Header & Action Bar -->
		<div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xs border border-slate-200/80 mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
			<div class="flex items-center gap-3.5">
				<a href="<?php echo esc_url( $page_url ); ?>" class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shrink-0 no-underline" title="<?php esc_attr_e( 'Back to Fields', 'frontend-dashboard' ); ?>">
					<i class="fas fa-arrow-left text-xs"></i>
				</a>
				<div>
					<div class="flex items-center gap-2.5">
						<h1 class="text-base sm:text-lg font-bold text-slate-900 m-0 p-0">
							<?php if ( $is_editing ) : ?>
								<?php echo ( 'post' === $action ) ? esc_html__( 'Edit Post Field', 'frontend-dashboard' ) : esc_html__( 'Edit Form Field', 'frontend-dashboard' ); ?>: <span class="text-indigo-600 font-bold"><?php echo esc_html( $field_label ); ?></span>
							<?php else : ?>
								<?php echo ( 'post' === $action ) ? esc_html__( 'Add New Post Field', 'frontend-dashboard' ) : esc_html__( 'Add New Form Field', 'frontend-dashboard' ); ?>
							<?php endif; ?>
						</h1>
						<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
							<?php echo esc_html( $active_menu_name ); ?>
						</span>
					</div>
					<p class="text-xs text-slate-500 m-0 mt-0.5">
						<?php esc_html_e( 'Select field type on the left to configure its label, validation, and role permissions.', 'frontend-dashboard' ); ?>
					</p>
				</div>
			</div>

			<div class="flex items-center gap-3 shrink-0">
				<a href="<?php echo esc_url( $page_url ); ?>" class="fed-btn-secondary h-11 inline-flex items-center justify-center gap-2 px-5 rounded-2xl text-xs font-semibold no-underline transition-all cursor-pointer">
					<i class="fas fa-times text-xs"></i>
					<span><?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?></span>
				</a>
				<button type="button" class="fed-header-save-btn fed-btn-primary h-11 inline-flex items-center justify-center gap-2 px-7 rounded-2xl font-bold text-xs transition-all active:scale-95 cursor-pointer shadow-sm">
					<i class="fas fa-save text-xs" style="color: #ffffff !important;"></i>
					<span style="color: #ffffff !important;"><?php esc_html_e( 'Save Field Changes', 'frontend-dashboard' ); ?></span>
				</button>
			</div>
		</div>

		<!-- 2-Column Builder Grid: Left Tabbed Palette + Right Settings Inspector -->
		<div class="fed-builder-grid">

			<!-- ========================================================= -->
			<!-- LEFT COLUMN: Tabbed Navigation & Field Type Palette       -->
			<!-- ========================================================= -->
			<div class="w-full space-y-4">

				<!-- Left Column Tabs (Extensible via filter) -->
				<?php if ( count( $editor_tabs ) > 1 ) : ?>
					<div class="bg-slate-100/80 p-1.5 rounded-2xl flex items-center gap-1.5 border border-slate-200/80">
						<?php
						$tab_i = 0;
						foreach ( $editor_tabs as $tab_key => $tab_info ) :
							$tab_active = ( 0 === $tab_i );
							?>
							<button type="button"
								class="fed-editor-tab-btn flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all cursor-pointer <?php echo $tab_active ? 'is-active bg-white text-indigo-700 shadow-2xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900'; ?>"
								data-tab-target="fed_editor_pane_<?php echo esc_attr( $tab_key ); ?>">
								<?php if ( ! empty( $tab_info['icon'] ) ) : ?>
									<i class="<?php echo esc_attr( $tab_info['icon'] ); ?> text-xs"></i>
								<?php endif; ?>
								<span><?php echo esc_html( $tab_info['label'] ); ?></span>
							</button>
							<?php
							$tab_i++;
						endforeach;
						?>
					</div>
				<?php endif; ?>

				<!-- PANE 1: Form Fields Types Palette -->
				<div id="fed_editor_pane_fields" class="fed-editor-tab-pane bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-5">
					<div class="flex items-center justify-between pb-3 border-b border-slate-100">
						<div class="flex items-center gap-2.5">
							<div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xs shrink-0">
								<i class="fas fa-cubes"></i>
							</div>
							<div>
								<h2 class="text-xs sm:text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Form Fields', 'frontend-dashboard' ); ?></h2>
								<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Click any field type to switch.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>
					</div>

					<!-- Field Types Live Search Filter -->
					<div class="relative w-full">
						<span style="position: absolute !important; left: 14px !important; top: 50% !important; transform: translateY(-50%) !important; pointer-events: none !important; z-index: 5 !important;" class="text-slate-400 text-xs">
							<i class="fas fa-search"></i>
						</span>
						<input type="text" id="fed_field_types_search" placeholder="<?php esc_attr_e( 'Filter field types...', 'frontend-dashboard' ); ?>" />
					</div>

					<!-- Categorized Field Tiles in 2-Columns -->
					<div class="space-y-4" id="fed_field_categories_wrapper">
						<!-- Category: Common Fields -->
						<?php if ( ! empty( $categorized['common'] ) ) : ?>
							<div class="fed-field-group space-y-2">
								<span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block px-1 pb-0.5">
									<?php esc_html_e( 'Common Fields', 'frontend-dashboard' ); ?>
								</span>
								<div class="grid grid-cols-2 gap-2">
									<?php
									foreach ( $categorized['common'] as $t_slug => $t_data ) :
										$t_icon   = fed_get_profile_field_type_icon( $t_slug );
										$t_active = ( $selected === $t_slug );
										?>
										<button type="button"
											class="fed-field-tile fed_button w-full flex items-center justify-between px-3 py-2.5 rounded-xl border text-left transition-all cursor-pointer bg-slate-800 hover:bg-slate-900 border-slate-700 text-white shadow-2xs gap-2 <?php echo $t_active ? 'is-active ring-2 ring-indigo-400 bg-slate-900' : ''; ?>"
											data-button="<?php echo esc_attr( $t_slug ); ?>"
											title="<?php echo esc_attr( $t_data['name'] ); ?>">
											<div class="flex items-center gap-2 min-w-0 flex-1">
												<span class="w-6 h-6 rounded-lg bg-slate-700/80 text-sky-400 flex items-center justify-center text-xs shrink-0">
													<i class="<?php echo esc_attr( $t_icon ); ?>"></i>
												</span>
												<span class="text-xs font-semibold text-slate-100 truncate"><?php echo esc_html( $t_data['name'] ); ?></span>
											</div>
											<i class="fas fa-chevron-right text-[9px] text-slate-500 shrink-0"></i>
										</button>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<!-- Category: Choice & Option Fields -->
						<?php if ( ! empty( $categorized['choice'] ) ) : ?>
							<div class="fed-field-group space-y-2 pt-2 border-t border-slate-100">
								<span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block px-1 pb-0.5">
									<?php esc_html_e( 'Choice & Option Fields', 'frontend-dashboard' ); ?>
								</span>
								<div class="grid grid-cols-2 gap-2">
									<?php
									foreach ( $categorized['choice'] as $t_slug => $t_data ) :
										$t_icon   = fed_get_profile_field_type_icon( $t_slug );
										$t_active = ( $selected === $t_slug );
										?>
										<button type="button"
											class="fed-field-tile fed_button w-full flex items-center justify-between px-3 py-2.5 rounded-xl border text-left transition-all cursor-pointer bg-slate-800 hover:bg-slate-900 border-slate-700 text-white shadow-2xs gap-2 <?php echo $t_active ? 'is-active ring-2 ring-indigo-400 bg-slate-900' : ''; ?>"
											data-button="<?php echo esc_attr( $t_slug ); ?>"
											title="<?php echo esc_attr( $t_data['name'] ); ?>">
											<div class="flex items-center gap-2 min-w-0 flex-1">
												<span class="w-6 h-6 rounded-lg bg-slate-700/80 text-sky-400 flex items-center justify-center text-xs shrink-0">
													<i class="<?php echo esc_attr( $t_icon ); ?>"></i>
												</span>
												<span class="text-xs font-semibold text-slate-100 truncate"><?php echo esc_html( $t_data['name'] ); ?></span>
											</div>
											<i class="fas fa-chevron-right text-[9px] text-slate-500 shrink-0"></i>
										</button>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<!-- Category: Advanced & Addon Fields -->
						<?php if ( ! empty( $categorized['advanced'] ) ) : ?>
							<div class="fed-field-group space-y-2 pt-2 border-t border-slate-100">
								<span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block px-1 pb-0.5">
									<?php esc_html_e( 'Advanced & Addon Fields', 'frontend-dashboard' ); ?>
								</span>
								<div class="grid grid-cols-2 gap-2">
									<?php
									foreach ( $categorized['advanced'] as $t_slug => $t_data ) :
										$t_icon   = fed_get_profile_field_type_icon( $t_slug );
										$t_active = ( $selected === $t_slug );
										?>
										<button type="button"
											class="fed-field-tile fed_button w-full flex items-center justify-between px-3 py-2.5 rounded-xl border text-left transition-all cursor-pointer bg-slate-800 hover:bg-slate-900 border-slate-700 text-white shadow-2xs gap-2 <?php echo $t_active ? 'is-active ring-2 ring-indigo-400 bg-slate-900' : ''; ?>"
											data-button="<?php echo esc_attr( $t_slug ); ?>"
											title="<?php echo esc_attr( $t_data['name'] ); ?>">
											<div class="flex items-center gap-2 min-w-0 flex-1">
												<span class="w-6 h-6 rounded-lg bg-slate-700/80 text-sky-400 flex items-center justify-center text-xs shrink-0">
													<?php if ( ! empty( $t_data['image'] ) && false !== strpos( $t_data['image'], 'http' ) ) : ?>
														<img src="<?php echo esc_url( $t_data['image'] ); ?>" class="w-3.5 h-3.5 object-contain invert brightness-0" />
													<?php else : ?>
														<i class="<?php echo esc_attr( $t_icon ); ?>"></i>
													<?php endif; ?>
												</span>
												<span class="text-xs font-semibold text-slate-100 truncate"><?php echo esc_html( $t_data['name'] ); ?></span>
											</div>
											<i class="fas fa-chevron-right text-[9px] text-slate-500 shrink-0"></i>
										</button>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Custom Extensible Tab Panes via action hook -->
				<?php
				if ( count( $editor_tabs ) > 1 ) {
					foreach ( $editor_tabs as $tab_key => $tab_info ) {
						if ( 'fields' === $tab_key ) {
							continue;
						}
						?>
						<div id="fed_editor_pane_<?php echo esc_attr( $tab_key ); ?>" class="fed-editor-tab-pane bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-5 hidden">
							<?php do_action( 'fed_admin_field_editor_tab_content', $tab_key, $row, $action, $menu_options ); ?>
						</div>
						<?php
					}
				}
				?>

			</div>

			<!-- ========================================================= -->
			<!-- RIGHT COLUMN: Form Field Settings Inspector               -->
			<!-- ========================================================= -->
			<div class="w-full min-w-0 space-y-7 fed_all_input_fields_container" id="fed_inspector_view">
				<?php
				// Render all input type forms inside the inspector
				fed_admin_input_fields_single_line( $row, $action, $menu_options );
				fed_admin_input_fields_multi_line( $row, $action, $menu_options );
				fed_admin_input_fields_mail( $row, $action, $menu_options );
				fed_admin_input_fields_number( $row, $action, $menu_options );
				fed_admin_input_fields_password( $row, $action, $menu_options );
				fed_admin_input_fields_checkbox( $row, $action, $menu_options );
				fed_admin_input_fields_radio( $row, $action, $menu_options );
				fed_admin_input_fields_select( $row, $action, $menu_options );
				fed_admin_input_fields_url( $row, $action, $menu_options );

				// Extensible hook for custom addon field panels
				do_action( 'fed_admin_input_fields_container_extra', $row, $action, $menu_options );
				?>
			</div>

		</div>
	</div>

	<!-- Interactive Visual Builder Script -->
	<script>
	(function($) {
		'use strict';
		$(document).ready(function() {
			var selectedType = '<?php echo esc_js( $selected ); ?>';

			// Scoped Role Selection & Counter Logic (Fixes global counting bug)
			function updateRolesCounter($targetContainer) {
				var $containers = ($targetContainer && $targetContainer.length) ? $targetContainer : $('.fed_input_type_container');
				$containers.each(function() {
					var $c = $(this);
					var total = $c.find('.fed-role-checkbox').length;
					var selected = $c.find('.fed-role-checkbox:checked').length;
					$c.find('.fed_role_selected_num').text(selected);

					$c.find('.fed-role-chip').each(function() {
						var $cb = $(this).find('.fed-role-checkbox');
						if ($cb.is(':checked')) {
							$(this).addClass('bg-indigo-50/80 border-indigo-200 text-indigo-900 font-semibold')
								   .removeClass('bg-white border-slate-200 text-slate-700');
						} else {
							$(this).removeClass('bg-indigo-50/80 border-indigo-200 text-indigo-900 font-semibold')
								   .addClass('bg-white border-slate-200 text-slate-700');
						}
					});
				});
			}

			// Activate specific field type in the inspector
			function activateFieldType(type) {
				selectedType = type;
				$('.fed_input_type_container').addClass('hide hidden');
				var $activeContainer = $('.fed_input_' + type + '_container');
				if ($activeContainer.length) {
					$activeContainer.removeClass('hide hidden');
				} else {
					$activeContainer = $('.fed_input_type_container[data-field-type="' + type + '"]');
					$activeContainer.removeClass('hide hidden');
				}

				// Update palette active highlights
				$('.fed-field-tile').removeClass('is-active active ring-2 ring-indigo-400 bg-slate-900');
				$('.fed-field-tile[data-button="' + type + '"]').addClass('is-active active ring-2 ring-indigo-400 bg-slate-900');

				// Update scoped roles counter for newly activated container
				updateRolesCounter($activeContainer);
			}

			// Initialize with selected type
			activateFieldType(selectedType);

			// Left Tab Navigation Switching
			$(document).on('click', '.fed-editor-tab-btn', function(e) {
				e.preventDefault();
				var targetPaneId = $(this).data('tab-target');
				$('.fed-editor-tab-btn').removeClass('is-active bg-white text-indigo-700 shadow-2xs border border-slate-200/60').addClass('text-slate-600');
				$(this).addClass('is-active bg-white text-indigo-700 shadow-2xs border border-slate-200/60').removeClass('text-slate-600');

				$('.fed-editor-tab-pane').addClass('hidden');
				$('#' + targetPaneId).removeClass('hidden');
			});

			// Field Type Tile Click
			$(document).on('click', '.fed-field-tile, .fed_button', function(e) {
				e.preventDefault();
				var type = $(this).data('button');
				if (!type) return;

				// Sync common values (Label, Meta, Required, Menu) from current form to newly selected form
				var $currentVisible = $('.fed_input_type_container:visible');
				var currentLabel = $currentVisible.find('input[name="label_name"]').val();
				var currentMeta  = $currentVisible.find('input[name="input_meta"]').val();
				var currentOrder = $currentVisible.find('input[name="input_order"]').val();
				var currentMenu  = $currentVisible.find('select[name="menu"]').val();

				activateFieldType(type);

				var $newVisible = $('.fed_input_' + type + '_container');
				if ($newVisible.length) {
					if (currentLabel && !$newVisible.find('input[name="label_name"]').val()) {
						$newVisible.find('input[name="label_name"]').val(currentLabel);
					}
					if (currentMeta && !$newVisible.find('input[name="input_meta"]').val()) {
						$newVisible.find('input[name="input_meta"]').val(currentMeta);
					}
					if (currentOrder) {
						$newVisible.find('input[name="input_order"]').val(currentOrder);
					}
					if (currentMenu) {
						$newVisible.find('select[name="menu"]').val(currentMenu);
					}
				}
			});

			// Field Types Search Filter
			$('#fed_field_types_search').on('input', function() {
				var query = $.trim($(this).val()).toLowerCase();
				if (!query) {
					$('.fed-field-tile').show();
					$('.fed-field-group').show();
					return;
				}
				$('.fed-field-group').each(function() {
					var $group = $(this);
					var visibleInGroup = 0;
					$group.find('.fed-field-tile').each(function() {
						var $tile = $(this);
						var text = $tile.text().toLowerCase();
						if (text.indexOf(query) !== -1) {
							$tile.show();
							visibleInGroup++;
						} else {
							$tile.hide();
						}
					});
					if (visibleInGroup > 0) {
						$group.show();
					} else {
						$group.hide();
					}
				});
			});

			// Scoped Segmented All / Specific Roles Mode Toggle
			$(document).on('click', '.fed_role_mode_all_btn', function(e) {
				e.preventDefault();
				var $section = $(this).closest('.fed_role_section');
				var $container = $(this).closest('.fed_input_type_container');

				$section.find('.fed_role_mode_all_btn').addClass('bg-white text-indigo-700 shadow-2xs').removeClass('text-slate-600');
				$section.find('.fed_role_mode_specific_btn').removeClass('bg-white text-indigo-700 shadow-2xs').addClass('text-slate-600');
				$section.find('.fed_specific_roles_wrapper').addClass('hidden');
				$container.find('.fed-role-checkbox').prop('checked', true);
				updateRolesCounter($container);
			});

			$(document).on('click', '.fed_role_mode_specific_btn', function(e) {
				e.preventDefault();
				var $section = $(this).closest('.fed_role_section');
				var $container = $(this).closest('.fed_input_type_container');

				$section.find('.fed_role_mode_specific_btn').addClass('bg-white text-indigo-700 shadow-2xs').removeClass('text-slate-600');
				$section.find('.fed_role_mode_all_btn').removeClass('bg-white text-indigo-700 shadow-2xs').addClass('text-slate-600');
				$section.find('.fed_specific_roles_wrapper').removeClass('hidden');
				updateRolesCounter($container);
			});

			// Scoped Role Search Filter
			$(document).on('input', '.fed_role_search_filter', function() {
				var query = $.trim($(this).val()).toLowerCase();
				var $wrapper = $(this).closest('.fed_specific_roles_wrapper');
				$wrapper.find('.fed-role-chip').each(function() {
					var name = $(this).data('role-name') || '';
					var key = $(this).data('role-key') || '';
					if (!query || name.indexOf(query) !== -1 || key.indexOf(query) !== -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			});

			// Scoped Select All / Clear All Roles
			$(document).on('click', '.fed_roles_select_all', function(e) {
				e.preventDefault();
				var $container = $(this).closest('.fed_input_type_container');
				$container.find('.fed-role-checkbox').prop('checked', true);
				updateRolesCounter($container);
			});

			$(document).on('click', '.fed_roles_deselect_all', function(e) {
				e.preventDefault();
				var $container = $(this).closest('.fed_input_type_container');
				$container.find('.fed-role-checkbox').prop('checked', false);
				updateRolesCounter($container);
			});

			$(document).on('change', '.fed-role-checkbox', function() {
				var $container = $(this).closest('.fed_input_type_container');
				updateRolesCounter($container);
			});

			// Initialize roles counters across all rendered forms
			updateRolesCounter();

			// Header Save Button
			$('.fed-header-save-btn').on('click', function(e) {
				e.preventDefault();
				var $activeForm = $('.fed_input_type_container:visible form');
				if ($activeForm.length) {
					$activeForm.submit();
				}
			});

			// Toast Helper
			function showToast(msg, isError) {
				var $toast = $('#fed_toast_notification');
				var $icon = $('#fed_toast_icon');
				var $msg = $('#fed_toast_message');

				$msg.text(msg);
				if (isError) {
					$icon.html('<i class="fas fa-exclamation-circle text-rose-400"></i>');
				} else {
					$icon.html('<i class="fas fa-check-circle text-emerald-400"></i>');
				}

				$toast.removeClass('translate-y-16 opacity-0').addClass('translate-y-0 opacity-100');
				setTimeout(function() {
					$toast.removeClass('translate-y-0 opacity-100').addClass('translate-y-16 opacity-0');
				}, 3500);
			}

			// AJAX Save with Toast
			$(document).on('submit', '.fed_admin_menu.fed_ajax', function(e) {
				e.preventDefault();
				var form = $(this);
				var $loader = $('.fed_loader');
				$loader.removeClass('hidden');

				// If in All Roles mode, check all role checkboxes before serializing
				if (form.find('.fed_specific_roles_wrapper').hasClass('hidden')) {
					form.find('.fed-role-checkbox').prop('checked', true);
				}

				$.ajax({
					type: 'POST',
					url: form.attr('action'),
					data: form.serialize(),
					success: function(response) {
						$loader.addClass('hidden');
						var isSuccess = (response && (response.success || response.status === 'success' || (typeof response === 'object' && !response.error)));
						var message = (response && response.data && response.data.message) ? response.data.message : 'Field settings saved successfully.';
						if (!isSuccess && response && response.data && response.data.errorMessage) {
							message = response.data.errorMessage;
						if (typeof fedAdminAlert !== 'undefined' && fedAdminAlert.adminSettings) {
							fedAdminAlert.adminSettings(response);
						} else {
							showToast(message, !isSuccess);
						}
					},
					error: function() {
						$loader.addClass('hidden');
						showToast('An error occurred while saving field settings.', true);
					}
				});
			});
		});
	})(jQuery);
	</script>
	<?php
}
