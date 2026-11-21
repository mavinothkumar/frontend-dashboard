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
			$row       = fed_process_user_profile( $rows, $action );
			$row['id'] = $id;
			$selected  = ! empty( $row['input_type'] ) ? $row['input_type'] : 'single_line';
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
			$row       = fed_process_user_profile( $rows, $action );
			$row['id'] = $id;
			$selected  = ! empty( $row['input_type'] ) ? $row['input_type'] : 'single_line';
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
	$editor_tabs  = apply_filters( 'fed_admin_field_editor_tabs', $default_tabs, $row, $action );
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

		/* Enterprise Live Preview & Studio Layout Styles */
		.fed-preview-grid {
			display: grid !important;
			grid-template-columns: repeat(12, minmax(0, 1fr)) !important;
			gap: 1.25rem !important;
			width: 100% !important;
			box-sizing: border-box !important;
		}
		.fed-preview-col-100 {
			grid-column: span 12 / span 12 !important;
			width: 100% !important;
		}
		.fed-preview-col-50 {
			grid-column: span 6 / span 6 !important;
			width: 100% !important;
		}
		.fed-preview-col-66 {
			grid-column: span 8 / span 8 !important;
			width: 100% !important;
		}
		.fed-preview-col-75 {
			grid-column: span 9 / span 9 !important;
			width: 100% !important;
		}
		.fed-preview-col-33 {
			grid-column: span 4 / span 4 !important;
			width: 100% !important;
		}
		.fed-preview-col-25 {
			grid-column: span 3 / span 3 !important;
			width: 100% !important;
		}
		.fed-preview-viewport-wrapper {
			transition: max-width 0.35s cubic-bezier(0.4, 0, 0.2, 1), transform 0.35s ease !important;
			width: 100% !important;
		}
		.fed-preview-viewport-wrapper.is-desktop {
			max-width: 100% !important;
		}
		.fed-preview-viewport-wrapper.is-tablet {
			max-width: 640px !important;
			margin-left: auto !important;
			margin-right: auto !important;
		}
		.fed-preview-viewport-wrapper.is-mobile {
			max-width: 390px !important;
			margin-left: auto !important;
			margin-right: auto !important;
		}
		.fed-preview-viewport-wrapper.is-mobile .fed-preview-col-50,
		.fed-preview-viewport-wrapper.is-mobile .fed-preview-col-33,
		.fed-preview-viewport-wrapper.is-mobile .fed-preview-col-25,
		.fed-preview-viewport-wrapper.is-mobile .fed-preview-col-66,
		.fed-preview-viewport-wrapper.is-mobile .fed-preview-col-75 {
			grid-column: span 12 / span 12 !important;
		}
		.fed-preview-active-input {
			min-height: 42px !important;
			width: 100% !important;
			box-sizing: border-box !important;
		}
		.bc_fed input.fed-has-leading-icon {
			padding-left: 42px !important;
		}
		.bc_fed input.fed-has-trailing-icon {
			padding-right: 42px !important;
		}
		.bc_fed input.fed-has-both-icons {
			padding-left: 42px !important;
			padding-right: 42px !important;
		}
		.bc_fed .fed-preview-icon-wrapper {
			position: absolute !important;
			top: 0 !important;
			bottom: 0 !important;
			left: 0 !important;
			width: 40px !important;
			display: flex !important;
			align-items: center !important;
			justify-content: center !important;
			pointer-events: none !important;
			color: #94a3b8 !important;
			z-index: 5 !important;
		}
		.bc_fed .fed-preview-trailing-btn {
			position: absolute !important;
			top: 0 !important;
			bottom: 0 !important;
			right: 0 !important;
			width: 40px !important;
			display: flex !important;
			align-items: center !important;
			justify-content: center !important;
			color: #94a3b8 !important;
			cursor: pointer !important;
			z-index: 5 !important;
			background: transparent !important;
			border: none !important;
			padding: 0 !important;
		}
		.bc_fed .fed-preview-trailing-btn:hover {
			color: #475569 !important;
		}
		/* Side-by-Side Split Studio Mode */
		@media (min-width: 1280px) {
			.bc_fed .fed_all_input_fields_container.fed-studio-split-mode {
				display: grid !important;
				grid-template-columns: minmax(0, 1fr) minmax(420px, 490px) !important;
				gap: 1.75rem !important;
				align-items: start !important;
			}
			.bc_fed .fed_all_input_fields_container.fed-studio-split-mode > :not([hidden]) ~ :not([hidden]) {
				margin-top: 0 !important;
			}
			.bc_fed .fed_all_input_fields_container.fed-studio-split-mode #fed_live_preview_panel {
				grid-column: 2 !important;
				grid-row: 1 !important;
				order: 2 !important;
				position: sticky !important;
				top: 1.5rem !important;
				z-index: 20 !important;
				max-height: calc(100vh - 3rem) !important;
				overflow-y: auto !important;
				margin-top: 0 !important;
			}
			.bc_fed .fed_all_input_fields_container.fed-studio-split-mode .fed_input_type_container {
				grid-column: 1 !important;
				grid-row: 1 !important;
				order: 1 !important;
				margin-top: 0 !important;
			}
		}
	</style>

	<div class="bc_fed fed-admin-wrap w-full max-w-none px-6 sm:px-10 py-8 font-sans text-slate-800 fed_add_edit_input_container" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-ajax-url="<?php echo esc_url( $ajax_url ); ?>">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo fed_loader();
		?>

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
					<kbd class="hidden sm:inline-block ml-1 px-1.5 py-0.5 text-[10px] font-semibold bg-white/20 text-white rounded border border-white/30">Ctrl+S</kbd>
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
							++$tab_i;
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
				// Render WYSIWYG Live Interactive Form Preview Panel
				fed_render_live_preview_panel( $row, $action );

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
				fed_admin_input_fields_date( $row, $action, $menu_options );
				fed_admin_input_fields_file( $row, $action, $menu_options );
				fed_admin_input_fields_color( $row, $action, $menu_options );
				fed_admin_input_fields_wp_editor( $row, $action, $menu_options );
				fed_admin_input_fields_label( $row, $action, $menu_options );
				fed_admin_input_fields_table( $row, $action, $menu_options );

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

			function initDynamicLabelEditor() {
				var edId = 'fed_label_html_content_editor';
				var $textarea = $( '#' + edId );
				if ( $textarea.length && ! $( '#wp-' + edId + '-wrap' ).length ) {
					if ( typeof wp !== 'undefined' && wp.editor && typeof wp.editor.initialize === 'function' ) {
						try {
							wp.editor.initialize( edId, {
								tinymce: {
									wpautop: true,
									toolbar1: 'formatselect,bold,italic,underline,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,alignjustify,link,unlink,outdent,indent,undo,redo,wp_adv',
									toolbar2: 'fontselect,fontsizeselect,forecolor,backcolor,pastetext,removeformat,charmap,subscript,superscript,table'
								},
								quicktags: {
									buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close'
								},
								mediaButtons: false
							} );
						} catch ( e ) {
							console.warn( 'initDynamicLabelEditor error:', e );
						}
					}
				}
			}

			// Helper to escape HTML characters
			function escapeHtml(str) {
				if (str === null || str === undefined) return '';
				return String(str)
					.replace(/&/g, '&amp;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&#039;');
			}

			function escapeAttr(str) {
				return escapeHtml(str);
			}

			// Extract choice items for select / radio live preview
			function getChoicesData($container) {
				var choices = [];
				var $rows = $container.find('.fed-choice-row');
				if ($rows.length) {
					$rows.each(function() {
						var k = $(this).find('.fed-choice-value, .fed-choice-key').val();
						var l = $(this).find('.fed-choice-label').val();
						if (k || l) {
							choices.push({
								key: k || l,
								label: l || k
							});
						}
					});
				}
				if (!choices.length) {
					var rawJson = $container.find('.fed-choices-raw-sync').val();
					if (rawJson) {
						try {
							var parsed = JSON.parse(rawJson);
							if ($.isArray(parsed)) {
								$.each(parsed, function(i, item) {
									choices.push({
										key: item.key || item.value || ('option_' + (i + 1)),
										label: item.label || item.name || item.key
									});
								});
							}
						} catch (e) {}
					}
				}
				return choices;
			}

			// WYSIWYG Live Interactive Form Preview Engine
			function updateLivePreview() {
				var $panel = $('#fed_live_preview_panel');
				if (!$panel.length) return;

				var $container = $('.fed_input_type_container:visible');
				if (!$container.length) {
					$container = $('.fed_input_' + selectedType + '_container');
				}
				if (!$container.length) return;

				var type = $container.data('field-type') || selectedType || 'single_line';
				var label = $container.find('input[name="label_name"]').val();
				var meta = $container.find('input[name="input_meta"]').val();
				var placeholder = $container.find('input[name="placeholder"]').val() || '';
				var help = $container.find('input[name="tooltip_body"]').val() || '';
				var defaultVal = $container.find('input[name="extended[default_value]"], textarea[name="extended[default_value]"], .fed-default-value-input').first().val() || '';
				var fieldWidth = $container.find('select[name="extended[field_width]"]').val() || '12';

				var $reqInput = $container.find('input[name="is_required"]');
				var isRequired = false;
				if ($reqInput.length) {
					if ($reqInput.is(':checkbox')) {
						isRequired = $reqInput.is(':checked');
					} else {
						isRequired = ($reqInput.val() === 'true' || $reqInput.val() === 'Enable' || $reqInput.val() === 'yes');
					}
				}

				// 1. Label & Required Asterisk
				$panel.find('.fed-preview-label-text').text(label && $.trim(label) !== '' ? label : 'Field Label');
				if (isRequired) {
					$panel.find('.fed-preview-required-star').removeClass('hidden');
				} else {
					$panel.find('.fed-preview-required-star').addClass('hidden');
				}

				// 2. Meta Badge
				$panel.find('.fed-preview-meta-badge').text(meta && $.trim(meta) !== '' ? meta : 'meta_key');

				// 3. Width Badge & 12-Column Responsive Grid Span
				var widthMap = {
					'100': { col: 'fed-preview-col-100', span: 12, percent: '100%' },
					'50':  { col: 'fed-preview-col-50',  span: 6,  percent: '50%' },
					'33':  { col: 'fed-preview-col-33',  span: 4,  percent: '33%' },
					'25':  { col: 'fed-preview-col-25',  span: 3,  percent: '25%' },
					'12':  { col: 'fed-preview-col-100', span: 12, percent: '100%' },
					'6':   { col: 'fed-preview-col-50',  span: 6,  percent: '50%' },
					'4':   { col: 'fed-preview-col-33',  span: 4,  percent: '33%' },
					'3':   { col: 'fed-preview-col-25',  span: 3,  percent: '25%' }
				};
				var wInfo = widthMap[fieldWidth] || { col: 'fed-preview-col-100', span: 12, percent: '100%' };
				$panel.find('.fed-preview-width-badge').text(wInfo.percent);

				var $fieldWrapper = $('#fed_live_preview_field_wrapper');
				$fieldWrapper.removeClass('fed-preview-col-100 fed-preview-col-50 fed-preview-col-33 fed-preview-col-25 col-span-12 col-span-9 col-span-8 col-span-6 col-span-4 col-span-3')
							.addClass(wInfo.col);

				var $companion = $panel.find('.fed-preview-companion-slot');
				if (wInfo.span < 12) {
					var compCol = (wInfo.span === 6) ? 'fed-preview-col-50' : (wInfo.span === 4 ? 'fed-preview-col-66' : 'fed-preview-col-75');
					$companion.removeClass('hidden fed-preview-col-50 fed-preview-col-66 fed-preview-col-75').addClass(compCol).show();
				} else {
					$companion.addClass('hidden').hide();
				}

				// 4. Help Text
				var $helpElem = $panel.find('.fed-preview-help-text');
				if (help && $.trim(help) !== '') {
					$helpElem.text(help).removeClass('hidden');
				} else {
					$helpElem.text('').addClass('hidden');
				}

				// 5. Field Type Name
				var typeTitleMap = {
					'single_line': 'Single Line Text',
					'multi_line': 'Multi-Line Textarea',
					'email': 'Email Address',
					'password': 'Password',
					'number': 'Number',
					'url': 'Website URL',
					'select': 'Dropdown Select',
					'radio': 'Radio Buttons',
					'checkbox': 'Checkbox',
					'date': 'Date Picker',
					'file': 'File Upload',
					'color': 'Color Picker',
					'wp_editor': 'Rich Text Editor',
					'label': 'HTML Content / Notice',
					'table': 'Dynamic Table'
				};
				$panel.find('.fed-preview-type-name').text(typeTitleMap[type] || type.replace(/_/g, ' '));

				// 6. Validation Badges
				var badges = [];
				if (isRequired) {
					badges.push('<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 border border-rose-200 text-rose-700 font-medium"><i class="fas fa-asterisk text-[8px]"></i> Required</span>');
				}
				var pattern = $container.find('.fed-validation-pattern-input').val();
				if (pattern) {
					badges.push('<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-200 text-indigo-700 font-mono" title="' + escapeAttr(pattern) + '"><i class="fas fa-code text-[8px]"></i> Pattern</span>');
				}
				var minLen = $container.find('.fed-validation-minlength-input').val();
				if (minLen) {
					badges.push('<span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-700 font-medium">Min: ' + escapeHtml(minLen) + ' chars</span>');
				}
				var maxLen = $container.find('.fed-validation-maxlength-input').val();
				if (maxLen) {
					badges.push('<span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-700 font-medium">Max: ' + escapeHtml(maxLen) + ' chars</span>');
				}
				var minVal = $container.find('.fed-validation-min-input').val();
				var maxVal = $container.find('.fed-validation-max-input').val();
				var stepVal = $container.find('.fed-validation-step-input').val();
				if (type === 'number') {
					if (minVal) badges.push('<span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-700 font-medium">Min: ' + escapeHtml(minVal) + '</span>');
					if (maxVal) badges.push('<span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-700 font-medium">Max: ' + escapeHtml(maxVal) + '</span>');
					if (stepVal && stepVal !== 'any') badges.push('<span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-700 font-medium">Step: ' + escapeHtml(stepVal) + '</span>');
				}
				var $badgesArea = $panel.find('.fed-preview-validation-badges');
				if (badges.length) {
					$badgesArea.html(badges.join('')).removeClass('hidden');
				} else {
					$badgesArea.html('').addClass('hidden');
				}

				// 7. Interactive Input Slot Rendering
				var $slot = $panel.find('.fed-preview-input-slot');
				var inputHtml = '';
				var markupCode = '';

				var valAttrs = '';
				if (isRequired) valAttrs += ' required';
				if (pattern) valAttrs += ' pattern="' + escapeAttr(pattern) + '"';
				if (minLen) valAttrs += ' minlength="' + escapeAttr(minLen) + '"';
				if (maxLen) valAttrs += ' maxlength="' + escapeAttr(maxLen) + '"';

				switch (type) {
					case 'single_line':
						inputHtml = '<input type="text" class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' + escapeAttr(placeholder) + '" value="' + escapeAttr(defaultVal) + '"' + valAttrs + ' />';
						markupCode = '<input type="text" name="' + escapeAttr(meta) + '" class="form-control" placeholder="' + escapeAttr(placeholder) + '" value="' + escapeAttr(defaultVal) + '"' + valAttrs + ' />';
						break;

					case 'multi_line':
						inputHtml = '<textarea rows="3" class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl p-3.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' + escapeAttr(placeholder) + '"' + valAttrs + '>' + escapeHtml(defaultVal) + '</textarea>';
						markupCode = '<textarea name="' + escapeAttr(meta) + '" rows="3" class="form-control" placeholder="' + escapeAttr(placeholder) + '"' + valAttrs + '>' + escapeHtml(defaultVal) + '</textarea>';
						break;

					case 'email':
						inputHtml = '<div class="relative"><span class="fed-preview-icon-wrapper text-xs"><i class="fas fa-envelope"></i></span><input type="email" class="fed-preview-active-input fed-has-leading-icon w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' + escapeAttr(placeholder || 'user@example.com') + '" value="' + escapeAttr(defaultVal) + '"' + valAttrs + ' /></div>';
						markupCode = '<input type="email" name="' + escapeAttr(meta) + '" class="form-control" placeholder="' + escapeAttr(placeholder || 'user@example.com') + '" value="' + escapeAttr(defaultVal) + '"' + valAttrs + ' />';
						break;

					case 'password':
						inputHtml = '<div class="relative"><span class="fed-preview-icon-wrapper text-xs"><i class="fas fa-lock"></i></span><input type="password" class="fed-preview-pwd-input fed-preview-active-input fed-has-leading-icon fed-has-trailing-icon w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' + escapeAttr(placeholder || '••••••••') + '" value="' + escapeAttr(defaultVal) + '"' + valAttrs + ' /><button type="button" class="fed-preview-toggle-pwd fed-preview-trailing-btn text-xs" title="Toggle visibility"><i class="fas fa-eye text-xs"></i></button></div>';
						markupCode = '<input type="password" name="' + escapeAttr(meta) + '" class="form-control" placeholder="' + escapeAttr(placeholder || '••••••••') + '"' + valAttrs + ' />';
						break;

					case 'number':
						var numAttrs = valAttrs;
						if (minVal) numAttrs += ' min="' + escapeAttr(minVal) + '"';
						if (maxVal) numAttrs += ' max="' + escapeAttr(maxVal) + '"';
						if (stepVal) numAttrs += ' step="' + escapeAttr(stepVal) + '"';
						inputHtml = '<div class="relative"><span class="fed-preview-icon-wrapper text-xs"><i class="fas fa-hashtag"></i></span><input type="number" class="fed-preview-active-input fed-has-leading-icon w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' + escapeAttr(placeholder || '0') + '" value="' + escapeAttr(defaultVal) + '"' + numAttrs + ' /></div>';
						markupCode = '<input type="number" name="' + escapeAttr(meta) + '" class="form-control" placeholder="' + escapeAttr(placeholder || '0') + '" value="' + escapeAttr(defaultVal) + '"' + numAttrs + ' />';
						break;

					case 'url':
						inputHtml = '<div class="relative"><span class="fed-preview-icon-wrapper text-xs"><i class="fas fa-globe"></i></span><input type="url" class="fed-preview-active-input fed-has-leading-icon w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' + escapeAttr(placeholder || 'https://example.com') + '" value="' + escapeAttr(defaultVal) + '"' + valAttrs + ' /></div>';
						markupCode = '<input type="url" name="' + escapeAttr(meta) + '" class="form-control" placeholder="' + escapeAttr(placeholder || 'https://example.com') + '" value="' + escapeAttr(defaultVal) + '"' + valAttrs + ' />';
						break;

					case 'select':
						var choices = getChoicesData($container);
						var isMulti = $container.find('.fed-multi-select-toggle').is(':checked');
						var selectAttrs = isMulti ? ' multiple size="4"' : '';
						var optHtml = '';
						var optCode = '';
						if (!isMulti) {
							optHtml += '<option value="">' + escapeHtml(placeholder || '-- Select an option --') + '</option>';
							optCode += '  <option value="">' + escapeHtml(placeholder || '-- Select an option --') + '</option>\n';
						}
						if (choices.length) {
							$.each(choices, function(i, c) {
								var isSelected = (defaultVal && c.key === defaultVal) || (i === 0 && !placeholder && !isMulti);
								optHtml += '<option value="' + escapeAttr(c.key) + '"' + (isSelected ? ' selected' : '') + '>' + escapeHtml(c.label) + '</option>';
								optCode += '  <option value="' + escapeAttr(c.key) + '"' + (isSelected ? ' selected' : '') + '>' + escapeHtml(c.label) + '</option>\n';
							});
						} else {
							optHtml += '<option value="option_1">Option 1</option><option value="option_2">Option 2</option>';
							optCode += '  <option value="option_1">Option 1</option>\n  <option value="option_2">Option 2</option>\n';
						}
						inputHtml = '<select class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all cursor-pointer"' + selectAttrs + valAttrs + '>' + optHtml + '</select>';
						markupCode = '<select name="' + escapeAttr(meta) + (isMulti ? '[]' : '') + '" class="form-control"' + selectAttrs + valAttrs + '>\n' + optCode + '</select>';
						break;

					case 'radio':
						var radioChoices = getChoicesData($container);
						var rItems = [];
						var rCodes = [];
						if (!radioChoices.length) {
							radioChoices = [{ key: 'option_1', label: 'Option 1' }, { key: 'option_2', label: 'Option 2' }];
						}
						$.each(radioChoices, function(i, c) {
							var isChecked = (defaultVal && c.key === defaultVal) || (i === 0 && !defaultVal);
							rItems.push('<label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/90 bg-white hover:bg-slate-50/80 transition-all cursor-pointer shadow-2xs"><input type="radio" name="fed_preview_radio_' + escapeAttr(meta) + '" value="' + escapeAttr(c.key) + '"' + (isChecked ? ' checked' : '') + ' class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300" /><span class="text-xs font-semibold text-slate-700">' + escapeHtml(c.label) + '</span><span class="text-[10px] text-slate-400 font-mono ml-auto">' + escapeHtml(c.key) + '</span></label>');
							rCodes.push('<label class="radio-inline"><input type="radio" name="' + escapeAttr(meta) + '" value="' + escapeAttr(c.key) + '"' + (isChecked ? ' checked' : '') + ' /> ' + escapeHtml(c.label) + '</label>');
						});
						inputHtml = '<div class="space-y-2 pt-0.5">' + rItems.join('') + '</div>';
						markupCode = rCodes.join('\n');
						break;

					case 'checkbox':
						inputHtml = '<label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200/90 bg-white hover:bg-slate-50/80 transition-all cursor-pointer shadow-2xs"><input type="checkbox" name="fed_preview_cb_' + escapeAttr(meta) + '" value="1"' + (defaultVal ? ' checked' : '') + ' class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300" /><span class="text-xs font-semibold text-slate-800">' + escapeHtml(label || 'Enable Option') + '</span></label>';
						markupCode = '<label class="checkbox-inline"><input type="checkbox" name="' + escapeAttr(meta) + '" value="1"' + (defaultVal ? ' checked' : '') + ' /> ' + escapeHtml(label || 'Enable Option') + '</label>';
						break;

					case 'date':
						inputHtml = '<div class="relative"><span class="fed-preview-icon-wrapper text-xs"><i class="fas fa-calendar-alt"></i></span><input type="date" class="fed-preview-active-input fed-has-leading-icon w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all" value="' + escapeAttr(defaultVal) + '"' + valAttrs + ' /></div>';
						markupCode = '<input type="date" name="' + escapeAttr(meta) + '" class="form-control" value="' + escapeAttr(defaultVal) + '"' + valAttrs + ' />';
						break;

					case 'file':
						inputHtml = '<div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 bg-white hover:bg-indigo-50/20 rounded-2xl p-6 text-center transition-all cursor-pointer group shadow-2xs"><div class="w-10 h-10 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base mb-2 group-hover:scale-110 transition-transform shadow-2xs"><i class="fas fa-cloud-upload-alt"></i></div><p class="text-xs font-semibold text-slate-800 m-0"><span class="text-indigo-600 font-bold">Choose a file</span> or drag & drop</p><p class="text-[11px] text-slate-400 m-0 mt-1">' + escapeHtml(placeholder || 'PDF, PNG, JPG, or DOC (Max 10MB)') + '</p><input type="file" class="hidden fed-preview-real-file-input"' + valAttrs + ' /></div>';
						markupCode = '<input type="file" name="' + escapeAttr(meta) + '" class="form-control"' + valAttrs + ' />';
						break;

					case 'color':
						var colVal = defaultVal || '#4f46e5';
						inputHtml = '<div class="flex items-center gap-3"><input type="color" class="fed-preview-color-picker w-10 h-10 rounded-xl border border-slate-200 cursor-pointer p-0.5 bg-white shadow-2xs" value="' + escapeAttr(colVal) + '" /><input type="text" class="fed-preview-color-text w-32 text-xs font-mono font-medium text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none" value="' + escapeAttr(colVal) + '" readonly /><span class="text-xs text-slate-400">Click swatch to pick color</span></div>';
						markupCode = '<input type="color" name="' + escapeAttr(meta) + '" value="' + escapeAttr(colVal) + '" class="form-control" />';
						break;

					case 'wp_editor':
						inputHtml = '<div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-2xs"><div class="flex items-center gap-1.5 p-2 bg-slate-50 border-b border-slate-200 text-slate-600 text-xs"><button type="button" class="p-1.5 rounded hover:bg-slate-200" title="Bold"><i class="fas fa-bold"></i></button><button type="button" class="p-1.5 rounded hover:bg-slate-200" title="Italic"><i class="fas fa-italic"></i></button><button type="button" class="p-1.5 rounded hover:bg-slate-200" title="Link"><i class="fas fa-link"></i></button><button type="button" class="p-1.5 rounded hover:bg-slate-200" title="List"><i class="fas fa-list-ul"></i></button><button type="button" class="p-1.5 rounded hover:bg-slate-200" title="Quote"><i class="fas fa-quote-right"></i></button></div><div class="p-4 min-h-[90px] text-xs text-slate-700 outline-none fed-preview-editor-body" contenteditable="true">' + (defaultVal ? escapeHtml(defaultVal) : '<span class="text-slate-400">Enter formatted rich text here...</span>') + '</div></div>';
						markupCode = '<' + '?php wp_editor( $content, \'' + escapeAttr(meta) + '\' ); ?' + '>';
						break;

					case 'label':
						var labelHtmlContent = '';
						if (typeof tinymce !== 'undefined') {
							var ed = tinymce.get('fed_label_html_content_editor');
							if (ed) {
								try { labelHtmlContent = ed.getContent(); } catch (e) {}
							}
						}
						if (!labelHtmlContent) {
							labelHtmlContent = $('#fed_label_html_content_editor').val() || '';
						}
						inputHtml = '<div class="fed-preview-label-html bg-white p-4 rounded-2xl border border-slate-200 text-xs text-slate-800 leading-relaxed shadow-2xs">' + (labelHtmlContent ? labelHtmlContent : '<span class="text-slate-400 italic">No HTML content configured yet. Edit content below.</span>') + '</div>';
						markupCode = '<div class="fed-custom-label-content">\n  ' + (labelHtmlContent ? labelHtmlContent : '') + '\n</div>';
						break;

					case 'table':
						inputHtml = '<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs"><table class="w-full text-xs text-left"><thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200"><tr><th class="p-3">Column 1</th><th class="p-3">Column 2</th><th class="p-3">Action</th></tr></thead><tbody class="divide-y divide-slate-100 text-slate-600"><tr><td class="p-3 font-medium text-slate-800">Sample Row 1</td><td class="p-3">Data Value A</td><td class="p-3 text-indigo-600 font-semibold">Edit</td></tr><tr><td class="p-3 font-medium text-slate-800">Sample Row 2</td><td class="p-3">Data Value B</td><td class="p-3 text-indigo-600 font-semibold">Edit</td></tr></tbody></table></div>';
						markupCode = '<table class="table fed-table">\n  <thead><tr><th>Column 1</th><th>Column 2</th></tr></thead>\n  <tbody><tr><td>Sample</td><td>Data</td></tr></tbody>\n</table>';
						break;

					default:
						inputHtml = '<input type="text" class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' + escapeAttr(placeholder) + '" value="' + escapeAttr(defaultVal) + '"' + valAttrs + ' />';
						markupCode = '<input type="text" name="' + escapeAttr(meta) + '" class="form-control" placeholder="' + escapeAttr(placeholder) + '"' + valAttrs + ' />';
						break;
				}

				$slot.html(inputHtml);
				$panel.find('.fed-preview-code-output').text(markupCode);
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

				if (window.fedInitChoicesBuilders) {
					window.fedInitChoicesBuilders();
				}

				// Refresh TinyMCE / WP Editor layout safely when activated
				if ( type === 'label' ) {
					initDynamicLabelEditor();
					setTimeout( function () {
						if ( typeof tinymce !== 'undefined' ) {
							var ed = tinymce.get( 'fed_label_html_content_editor' );
							if ( ed ) {
								try {
									ed.show();
									ed.fire( 'resize' );
								} catch ( e ) {}
							}
						}
					}, 50 );
				} else if ( type === 'wp_editor' ) {
					setTimeout( function () {
						if ( typeof tinymce !== 'undefined' ) {
							var ed = tinymce.get( 'post_content' );
							if ( ed ) {
								try {
									ed.show();
									ed.fire( 'resize' );
								} catch ( e ) {}
							}
						}
					}, 50 );
				}

				// Update Live Interactive Preview
				updateLivePreview();
			}

			// Initialize with selected type
			activateFieldType( selectedType );
			initDynamicLabelEditor();
			updateLivePreview();

			// Device Viewport Switcher Controls
			$(document).on('click', '.fed-preview-viewport-btn', function(e) {
				e.preventDefault();
				var vp = $(this).data('viewport');
				$('.fed-preview-viewport-btn').removeClass('is-active bg-white text-indigo-700 shadow-2xs').addClass('text-slate-600 hover:text-slate-900');
				$(this).addClass('is-active bg-white text-indigo-700 shadow-2xs').removeClass('text-slate-600 hover:text-slate-900');

				var $wrap = $('.fed-preview-viewport-wrapper');
				$wrap.removeClass('is-desktop is-tablet is-mobile');
				var labelMap = {
					'desktop': 'Desktop (100%)',
					'tablet':  'Tablet (640px)',
					'mobile':  'Mobile (390px)'
				};
				if (vp === 'tablet') {
					$wrap.addClass('is-tablet');
				} else if (vp === 'mobile') {
					$wrap.addClass('is-mobile');
				} else {
					$wrap.addClass('is-desktop');
				}
				$('.fed-preview-viewport-label').text(labelMap[vp] || 'Desktop (100%)');
			});

			// Split Studio Side-by-Side vs Banner Layout Toggle
			$(document).on('click', '.fed-preview-layout-toggle-btn', function(e) {
				e.preventDefault();
				var $view = $('#fed_inspector_view');
				var isSplit = $view.hasClass('fed-studio-split-mode');
				if (isSplit) {
					$view.removeClass('fed-studio-split-mode');
					$(this).removeClass('bg-indigo-50 border-indigo-200 text-indigo-700')
							.find('.fed-layout-mode-label').text('Split Studio');
					try { localStorage.setItem('fed_preview_layout_mode', 'banner'); } catch (err) {}
				} else {
					$view.addClass('fed-studio-split-mode');
					$(this).addClass('bg-indigo-50 border-indigo-200 text-indigo-700')
							.find('.fed-layout-mode-label').text('Banner Mode');
					try { localStorage.setItem('fed_preview_layout_mode', 'split'); } catch (err) {}
				}
			});

			// Restore layout preference from localStorage
			try {
				var savedLayout = localStorage.getItem('fed_preview_layout_mode');
				if (savedLayout === 'split') {
					$('#fed_inspector_view').addClass('fed-studio-split-mode');
					$('.fed-preview-layout-toggle-btn').addClass('bg-indigo-50 border-indigo-200 text-indigo-700')
														.find('.fed-layout-mode-label').text('Banner Mode');
				}
			} catch (err) {}

			// Toggle Rendered HTML Markup Inspector
			$(document).on('click', '.fed-preview-code-toggle-btn', function(e) {
				e.preventDefault();
				var $drawer = $('.fed-preview-code-drawer');
				$drawer.toggleClass('hidden');
				if (!$drawer.hasClass('hidden')) {
					$(this).addClass('bg-indigo-50 border-indigo-200 text-indigo-700');
				} else {
					$(this).removeClass('bg-indigo-50 border-indigo-200 text-indigo-700');
				}
			});

			// Copy Rendered HTML Button
			$(document).on('click', '.fed-preview-copy-html-btn', function(e) {
				e.preventDefault();
				var code = $('.fed-preview-code-output').text();
				if (!code) return;
				var $btn = $(this);
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(code).then(function() {
						$btn.html('<i class="fas fa-check text-emerald-500 text-[10px]"></i> <span class="text-emerald-600 font-bold">Copied!</span>');
						setTimeout(function() {
							$btn.html('<i class="fas fa-copy text-[10px]"></i> <span>Copy HTML</span>');
						}, 2000);
					});
				} else {
					var $temp = $('<textarea>').val(code).appendTo('body').select();
					document.execCommand('copy');
					$temp.remove();
					$btn.html('<i class="fas fa-check text-emerald-500 text-[10px]"></i> <span class="text-emerald-600 font-bold">Copied!</span>');
					setTimeout(function() {
						$btn.html('<i class="fas fa-copy text-[10px]"></i> <span>Copy HTML</span>');
					}, 2000);
				}
			});

			// Reset Test Values to Defaults Button
			$(document).on('click', '.fed-preview-reset-btn', function(e) {
				e.preventDefault();
				updateLivePreview();
			});

			// Collapse / Expand Preview Panel Toggle
			$(document).on('click', '.fed-preview-collapse-btn', function(e) {
				e.preventDefault();
				var $body = $('.fed-preview-body');
				var $icon = $(this).find('i');
				var isCollapsing = !$body.hasClass('hidden');
				if (isCollapsing) {
					$body.addClass('hidden');
					$icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
					try { localStorage.setItem('fed_preview_collapsed', '1'); } catch (err) {}
				} else {
					$body.removeClass('hidden');
					$icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
					try { localStorage.setItem('fed_preview_collapsed', '0'); } catch (err) {}
				}
			});

			// Restore collapsed preference from localStorage
			try {
				if (localStorage.getItem('fed_preview_collapsed') === '1') {
					$('.fed-preview-body').addClass('hidden');
					$('.fed-preview-collapse-btn i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
				}
			} catch (err) {}

			// Password Preview Eye Toggle Button
			$(document).on('click', '.fed-preview-toggle-pwd', function(e) {
				e.preventDefault();
				var $inp = $(this).siblings('.fed-preview-pwd-input');
				var $icon = $(this).find('i');
				if ($inp.attr('type') === 'password') {
					$inp.attr('type', 'text');
					$icon.removeClass('fa-eye').addClass('fa-eye-slash');
				} else {
					$inp.attr('type', 'password');
					$icon.removeClass('fa-eye-slash').addClass('fa-eye');
				}
			});

			// Color Picker Preview Swatch Sync
			$(document).on('input change', '.fed-preview-color-picker', function() {
				var val = $(this).val();
				$(this).siblings('.fed-preview-color-text').val(val);
			});

			// File Upload Dropzone Click triggers hidden file input
			$(document).on('click', '.fed-preview-input-slot .border-dashed', function(e) {
				if (!$(e.target).is('input[type="file"]')) {
					$(this).find('.fed-preview-real-file-input').trigger('click');
				}
			});

			// Real-time live update across all field settings in the inspector view
			$(document).on('input keyup change paste', '#fed_inspector_view input, #fed_inspector_view select, #fed_inspector_view textarea', function() {
				updateLivePreview();
			});

			// Hook into Choices Builder to trigger Live Preview updates
			$(document).on('fed_choices_updated', function() {
				updateLivePreview();
			});

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

				// Sync common values (Label, Meta, Required, Menu, Help, Width, Default Value, Validation) from current form to newly selected form
				var $currentVisible = $('.fed_input_type_container:visible');
				var currentLabel        = $currentVisible.find('input[name="label_name"]').val();
				var currentMeta         = $currentVisible.find('input[name="input_meta"]').val();
				var currentOrder        = $currentVisible.find('input[name="input_order"]').val();
				var currentMenu         = $currentVisible.find('select[name="menu"]').val();
				var currentHelp         = $currentVisible.find('input[name="tooltip_body"]').val();
				var currentWidth        = $currentVisible.find('select[name="extended[field_width]"]').val();
				var currentDefault      = $currentVisible.find('input[name="extended[default_value]"]').val();
				var currentPattern      = $currentVisible.find('.fed-validation-pattern-input').val();
				var currentPatternError = $currentVisible.find('.fed-validation-error-input').val();
				var currentMinLength    = $currentVisible.find('.fed-validation-minlength-input').val();
				var currentMaxLength    = $currentVisible.find('.fed-validation-maxlength-input').val();
				var currentMin          = $currentVisible.find('.fed-validation-min-input').val();
				var currentMax          = $currentVisible.find('.fed-validation-max-input').val();
				var currentStep         = $currentVisible.find('.fed-validation-step-input').val();
				var currentPreset       = $currentVisible.find('.fed-validation-preset-select').val();

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
					if (currentHelp && !$newVisible.find('input[name="tooltip_body"]').val()) {
						$newVisible.find('input[name="tooltip_body"]').val(currentHelp);
					}
					if (currentWidth) {
						$newVisible.find('select[name="extended[field_width]"]').val(currentWidth);
					}
					if (currentDefault && !$newVisible.find('input[name="extended[default_value]"]').val()) {
						$newVisible.find('input[name="extended[default_value]"]').val(currentDefault);
					}
					if (currentPattern && !$newVisible.find('.fed-validation-pattern-input').val()) {
						$newVisible.find('.fed-validation-pattern-input').val(currentPattern);
					}
					if (currentPatternError && !$newVisible.find('.fed-validation-error-input').val()) {
						$newVisible.find('.fed-validation-error-input').val(currentPatternError);
					}
					if (currentMinLength && !$newVisible.find('.fed-validation-minlength-input').val()) {
						$newVisible.find('.fed-validation-minlength-input').val(currentMinLength);
					}
					if (currentMaxLength && !$newVisible.find('.fed-validation-maxlength-input').val()) {
						$newVisible.find('.fed-validation-maxlength-input').val(currentMaxLength);
					}
					if (currentMin && !$newVisible.find('.fed-validation-min-input').val()) {
						$newVisible.find('.fed-validation-min-input').val(currentMin);
					}
					if (currentMax && !$newVisible.find('.fed-validation-max-input').val()) {
						$newVisible.find('.fed-validation-max-input').val(currentMax);
					}
					if (currentStep && !$newVisible.find('.fed-validation-step-input').val()) {
						$newVisible.find('.fed-validation-step-input').val(currentStep);
					}
					if (currentPreset && !$newVisible.find('.fed-validation-preset-select').val()) {
						$newVisible.find('.fed-validation-preset-select').val(currentPreset);
					}
				}
			});

			// Validation Preset Selection
			$(document).on('change', '.fed-validation-preset-select', function() {
				var $opt = $(this).find('option:selected');
				var pattern = $opt.data('pattern') || '';
				var error = $opt.data('error') || '';
				var $card = $(this).closest('.fed-validation-card');

				if (pattern) {
					$card.find('.fed-validation-pattern-input').val(pattern).trigger('input').trigger('change');
				}
				if (error && !$card.find('.fed-validation-error-input').val()) {
					$card.find('.fed-validation-error-input').val(error).trigger('input').trigger('change');
				}
			});

			// Auto-sync preset dropdown if pattern is edited manually
			$(document).on('input', '.fed-validation-pattern-input', function() {
				var val = $(this).val();
				var $card = $(this).closest('.fed-validation-card');
				var $select = $card.find('.fed-validation-preset-select');
				var matchedPreset = '';
				$select.find('option').each(function() {
					if ($(this).data('pattern') && $(this).data('pattern') === val) {
						matchedPreset = $(this).val();
					}
				});
				$select.val(matchedPreset);
			});

			// Smart Tags Dropdown Toggle
			$(document).on('click', '.fed-btn-insert-smart-tag', function(e) {
				e.preventDefault();
				e.stopPropagation();
				var $dropdown = $(this).siblings('.fed-smart-tags-dropdown');
				$('.fed-smart-tags-dropdown').not($dropdown).addClass('hidden');

				var willOpen = $dropdown.hasClass('hidden');
				if (willOpen) {
					$dropdown.removeClass('hidden');
					var $searchInput = $dropdown.find('.fed-smart-tag-search-input');
					$searchInput.val('');
					$dropdown.find('.fed-smart-tag-search-clear').addClass('hidden');
					$dropdown.find('.fed-smart-tag-item').show();
					$dropdown.find('.fed-smart-tag-group').show();
					$dropdown.find('.fed-smart-tags-no-results').addClass('hidden');
					setTimeout(function() {
						$searchInput.focus();
					}, 50);
				} else {
					$dropdown.addClass('hidden');
				}
			});

			// Prevent clicks inside Smart Tags Dropdown from closing it
			$(document).on('click', '.fed-smart-tags-dropdown', function(e) {
				e.stopPropagation();
			});

			// Smart Tags Search Filtering
			$(document).on('input', '.fed-smart-tag-search-input', function() {
				var query = $.trim($(this).val()).toLowerCase();
				var $dropdown = $(this).closest('.fed-smart-tags-dropdown');
				var $clearBtn = $dropdown.find('.fed-smart-tag-search-clear');
				var totalMatches = 0;

				if (query.length > 0) {
					$clearBtn.removeClass('hidden');
				} else {
					$clearBtn.addClass('hidden');
				}

				$dropdown.find('.fed-smart-tag-group').each(function() {
					var $group = $(this);
					var groupTitle = $group.find('.fed-smart-tag-group-title').text().toLowerCase();
					var groupMatches = 0;

					$group.find('.fed-smart-tag-item').each(function() {
						var $item = $(this);
						var tag = ($item.data('tag') || '').toString().toLowerCase();
						var label = ($item.data('label') || '').toString().toLowerCase();

						if (!query || tag.indexOf(query) !== -1 || label.indexOf(query) !== -1 || groupTitle.indexOf(query) !== -1) {
							$item.show();
							groupMatches++;
						} else {
							$item.hide();
						}
					});

					if (groupMatches > 0) {
						$group.show();
						totalMatches += groupMatches;
					} else {
						$group.hide();
					}
				});

				if (totalMatches === 0) {
					$dropdown.find('.fed-smart-tags-no-results').removeClass('hidden');
				} else {
					$dropdown.find('.fed-smart-tags-no-results').addClass('hidden');
				}
			});

			// Clear Search button
			$(document).on('click', '.fed-smart-tag-search-clear', function(e) {
				e.preventDefault();
				e.stopPropagation();
				var $dropdown = $(this).closest('.fed-smart-tags-dropdown');
				var $searchInput = $dropdown.find('.fed-smart-tag-search-input');
				$searchInput.val('').trigger('input').focus();
			});

			// Keyboard Navigation in Smart Tags Search
			$(document).on('keydown', '.fed-smart-tag-search-input', function(e) {
				var $dropdown = $(this).closest('.fed-smart-tags-dropdown');
				if (e.which === 27) { // Escape
					e.preventDefault();
					$dropdown.addClass('hidden');
					var $container = $dropdown.closest('.fed-default-value-field-wrap');
					$container.find('.fed-default-value-input').focus();
				} else if (e.which === 13) { // Enter
					e.preventDefault();
					var $firstVisible = $dropdown.find('.fed-smart-tag-item:visible').first();
					if ($firstVisible.length) {
						$firstVisible.trigger('click');
					}
				}
			});

			// Close Smart Tags Dropdown on Outside Click
			$(document).on('click', function(e) {
				if (!$(e.target).closest('.fed-smart-tags-dropdown-container').length) {
					$('.fed-smart-tags-dropdown').addClass('hidden');
				}
			});

			// Insert Smart Tag into Default Value input
			$(document).on('click', '.fed-smart-tag-item', function(e) {
				e.preventDefault();
				e.stopPropagation();
				var tag = $(this).data('tag');
				var $container = $(this).closest('.fed-default-value-field-wrap');
				var $input = $container.find('.fed-default-value-input');

				if ($input.length && tag) {
					var inputElem = $input[0];
					var startPos = inputElem.selectionStart || 0;
					var endPos = inputElem.selectionEnd || 0;
					var oldVal = $input.val();
					var newVal = oldVal.substring(0, startPos) + tag + oldVal.substring(endPos);
					$input.val(newVal);
					inputElem.focus();
					var newPos = startPos + tag.length;
					inputElem.setSelectionRange(newPos, newPos);
					$input.trigger('input').trigger('change');
				}

				$('.fed-smart-tags-dropdown').addClass('hidden');
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

			// Auto populate Input Meta Key live as user types Label Name
			function fedSlugifyMeta(text) {
				if (!text) return '';
				return text.toString()
					.toLowerCase()
					.trim()
					.replace(/[^a-z0-9_ ]/g, '')
					.replace(/\s+/g, '_')
					.replace(/_+/g, '_')
					.replace(/^_+|_+$/g, '')
					.substring(0, 32);
			}

			$(document).on('input keyup paste change', 'input[name="label_name"]', function() {
				var $labelInput = $(this);
				var $form = $labelInput.closest('form');
				var $metaInput = $form.find('input[name="input_meta"]');

				if (!$metaInput.length || $metaInput.prop('readonly') || $metaInput.hasClass('bg-slate-100') || $metaInput.data('locked')) {
					return;
				}

				if ($metaInput.data('fed-manual') && $metaInput.val() !== '') {
					return;
				}

				var slug = fedSlugifyMeta($labelInput.val());
				$metaInput.val(slug);
			});

			$(document).on('input keyup', 'input[name="input_meta"]', function() {
				var $metaInput = $(this);
				if ($metaInput.val() === '') {
					$metaInput.removeData('fed-manual');
				} else {
					$metaInput.data('fed-manual', true);
				}
			});

			// Manual slug re-generation trigger button
			$(document).on('click', '.fed-btn-sync-slug', function(e) {
				e.preventDefault();
				var $form = $(this).closest('form');
				var $labelInput = $form.find('input[name="label_name"]');
				var $metaInput = $form.find('input[name="input_meta"]');
				if ($metaInput.length && !$metaInput.prop('readonly')) {
					var slug = fedSlugifyMeta($labelInput.val());
					$metaInput.val(slug).removeData('fed-manual').focus();
				}
			});

			// Keyboard shortcut: Ctrl + S / Cmd + S to save
			$(document).on('keydown.fed_builder_shortcut', function(e) {
				if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.keyCode === 83)) {
					e.preventDefault();
					e.stopPropagation();
					var $modal = $('#fed_field_builder_modal');
					if ($modal.length && $modal.is(':visible')) {
						var $activeModalForm = $modal.find('.fed_input_type_container:visible form');
						if ($activeModalForm.length) {
							$activeModalForm.submit();
						}
					} else {
						$('.fed-header-save-btn').trigger('click');
					}
				}
			});

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

			// AJAX Save with Toast for Standalone Page
			if (!$('#fed_field_builder_modal').length) {
				$(document).off('submit.fed_standalone_builder').on('submit.fed_standalone_builder', '.fed_admin_menu.fed_ajax', function(e) {
					e.preventDefault();
					e.stopImmediatePropagation();
					var form = $(this);
					var $loader = $('.fed_loader');
					$loader.removeClass('hidden');

					// Sync TinyMCE / WP Editor content to textarea
					if (typeof tinyMCE !== 'undefined') {
						tinyMCE.triggerSave();
					}

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
							var isSuccess = !!(response && (response.success === true || response.status === 'success' || (response.data && response.data.status === 'success')));
							var message = '';
							if (response) {
								if (response.data) {
									if (typeof response.data === 'string') {
										message = response.data;
									} else if (response.data.message) {
										message = response.data.message;
									} else if (response.data.errorMessage) {
										message = response.data.errorMessage;
									} else if (response.data.error) {
										message = response.data.error;
									}
								} else if (response.message) {
									message = response.message;
								}
							}
							if (!message) {
								message = isSuccess ? 'Field settings saved successfully.' : 'Validation error: Please check all required fields.';
							}
							if (typeof fedAdminAlert !== 'undefined' && fedAdminAlert.adminSettings) {
								fedAdminAlert.adminSettings(response);
							} else {
								showToast(message, !isSuccess);
							}
						},
						error: function(xhr) {
							$loader.addClass('hidden');
							var errMsg = 'An error occurred while saving field settings.';
							if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
								errMsg = xhr.responseJSON.data.message;
							}
							showToast(errMsg, true);
						}
					});
				});
			}
		});
	})(jQuery);
	</script>
	<?php
}
