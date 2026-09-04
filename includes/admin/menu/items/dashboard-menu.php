<?php
/**
 * Dashboard Menu Management
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main entry point for Dashboard Menu Page.
 */
function fed_get_dashboard_menu_items() {
	wp_enqueue_script( 'jquery-ui-sortable' );
	global $wpdb;
	$table_name = $wpdb->get_blog_prefix() . BC_FED_TABLE_MENU;

	// Ensure menu_key and menu_value columns exist
	$has_menu_key = $wpdb->get_results( "SHOW COLUMNS FROM `{$table_name}` LIKE 'menu_key'" );
	if ( empty( $has_menu_key ) ) {
		$wpdb->query( "ALTER TABLE `{$table_name}` ADD `menu_key` VARCHAR(255) NULL AFTER `extended`, ADD `menu_value` TEXT NULL AFTER `menu_key`" );
	}

	$menus             = $wpdb->get_results( "SELECT * FROM {$table_name} ORDER BY CAST(menu_order AS UNSIGNED) ASC, id ASC", ARRAY_A );
	$user_roles        = fed_get_user_roles();
	$total_roles_count = count( $user_roles );
	$nonce             = wp_create_nonce( 'fed_nonce' );
	$ajax_url          = admin_url( 'admin-ajax.php' );

	if ( ! is_array( $menus ) ) {
		$menus = array();
	}
	?>
	<!-- Scoped Styles to prevent WordPress Admin style collisions -->
	<style>
		.fed-btn-primary,
		button.fed-btn-primary,
		#fed_open_add_menu_btn,
		#fed_modal_submit_btn {
			background-color: #4f46e5 !important;
			color: #ffffff !important;
			border: 1px solid #4338ca !important;
			box-shadow: 0 2px 4px -1px rgba(79, 70, 229, 0.2) !important;
		}
		.fed-btn-primary:hover,
		button.fed-btn-primary:hover,
		#fed_open_add_menu_btn:hover,
		#fed_modal_submit_btn:hover {
			background-color: #4338ca !important;
			color: #ffffff !important;
			border-color: #3730a3 !important;
		}
		.fed-btn-edit,
		button.fed-btn-edit {
			color: #334155 !important;
			background-color: #f8fafc !important;
			border: 1px solid #e2e8f0 !important;
		}
		.fed-btn-edit:hover,
		button.fed-btn-edit:hover {
			color: #4f46e5 !important;
			background-color: #eef2ff !important;
			border-color: #c7d2fe !important;
		}
		.fed-btn-delete,
		button.fed-btn-delete {
			color: #475569 !important;
			background-color: #f8fafc !important;
			border: 1px solid #e2e8f0 !important;
		}
		.fed-btn-delete:hover,
		button.fed-btn-delete:hover {
			color: #e11d48 !important;
			background-color: #fff1f2 !important;
			border-color: #fecdd3 !important;
		}
		.bc_fed #fed_modal_cancel_btn {
			color: #475569 !important;
			background-color: #f8fafc !important;
			border: 1px solid #e2e8f0 !important;
		}
		.bc_fed #fed_modal_cancel_btn:hover {
			background-color: #f1f5f9 !important;
			color: #1e293b !important;
		}
		#fed_trigger_icon_picker {
			background-color: #e0e7ff !important;
			color: #4338ca !important;
			border: 1px solid #c7d2fe !important;
		}
		#fed_trigger_icon_picker:hover {
			background-color: #c7d2fe !important;
			color: #3730a3 !important;
		}
		/* Role Chips & Pure White Checkboxes */
		.fed-role-chip {
			border: 1px solid #e2e8f0 !important;
			background-color: #f8fafc !important;
			color: #475569 !important;
			padding: 5px 12px !important;
			border-radius: 8px !important;
		}
		.fed-role-chip:hover {
			border-color: #cbd5e1 !important;
			background-color: #f1f5f9 !important;
		}
		.fed-role-chip.is-active {
			border-color: #6366f1 !important;
			background-color: #ffffff !important;
			box-shadow: 0 0 0 1px #6366f1 !important;
		}
		.fed-role-chip input[type="checkbox"] {
			background-color: #ffffff !important;
			border: 1.5px solid #94a3b8 !important;
			border-radius: 4px !important;
			width: 14px !important;
			height: 14px !important;
			min-width: 14px !important;
			margin: 0 !important;
			cursor: pointer !important;
		}
		.fed-role-chip input[type="checkbox"]:checked {
			background-color: #4f46e5 !important;
			border-color: #4f46e5 !important;
		}
		.bc_fed #fed_menu_filter_search,
		.bc_fed input#fed_menu_filter_search {
			padding-left: 36px !important;
			border: 1px solid #e2e8f0 !important;
			border-radius: 12px !important;
			background-color: #f8fafc !important;
			height: 40px !important;
			min-height: 40px !important;
			font-size: 12px !important;
			color: #334155 !important;
			box-shadow: none !important;
		}
		.bc_fed #fed_menu_filter_search:focus,
		.bc_fed input#fed_menu_filter_search:focus {
			border-color: #6366f1 !important;
			background-color: #ffffff !important;
			box-shadow: 0 0 0 1px #6366f1 !important;
		}
		.bc_fed #fed_role_search_filter,
		.bc_fed input#fed_role_search_filter {
			padding-left: 32px !important;
			border: 1px solid #e2e8f0 !important;
			border-radius: 10px !important;
			background-color: #f8fafc !important;
			height: 38px !important;
			min-height: 38px !important;
			box-shadow: none !important;
		}
		.bc_fed #fed_icon_search_input,
		.bc_fed input#fed_icon_search_input {
			padding-left: 36px !important;
			border: 1px solid #e2e8f0 !important;
			border-radius: 12px !important;
			background-color: #f8fafc !important;
			height: 40px !important;
			min-height: 40px !important;
			box-shadow: none !important;
		}
		/* Modal Form Input Height & Alignment Normalization */
		.bc_fed #fed_modal_menu_form input[type="text"],
		.bc_fed #fed_modal_menu_form input[type="url"],
		.bc_fed #fed_modal_menu_form input[type="number"],
		.bc_fed #fed_modal_menu_form select {
			height: 42px !important;
			min-height: 42px !important;
			line-height: normal !important;
			padding: 9px 14px !important;
			border-radius: 12px !important;
			border: 1px solid #cbd5e1 !important;
			background-color: #f8fafc !important;
			box-sizing: border-box !important;
			margin: 0 !important;
			width: 100% !important;
			font-size: 13px !important;
		}
		.bc_fed #fed_modal_menu_form input[type="text"]:focus,
		.bc_fed #fed_modal_menu_form input[type="url"]:focus,
		.bc_fed #fed_modal_menu_form input[type="number"]:focus,
		.bc_fed #fed_modal_menu_form select:focus {
			background-color: #ffffff !important;
			border-color: #6366f1 !important;
			box-shadow: 0 0 0 1px #6366f1 !important;
		}
		.bc_fed #fed_modal_menu_form #fed_form_menu_icon {
			padding-right: 96px !important;
		}
		.bc_fed #fed_trigger_icon_picker {
			height: 34px !important;
			top: 4px !important;
			right: 4px !important;
			padding: 0 14px !important;
			display: inline-flex !important;
			align-items: center !important;
			justify-content: center !important;
			border-radius: 8px !important;
			margin: 0 !important;
			position: absolute !important;
		}
		.bc_fed #fed_selected_icon_preview {
			width: 42px !important;
			height: 42px !important;
			min-width: 42px !important;
			min-height: 42px !important;
			display: flex !important;
			align-items: center !important;
			justify-content: center !important;
			border-radius: 12px !important;
			flex-shrink: 0 !important;
		}
		.bc_fed .fed-modal-field-group {
			margin-bottom: 18px !important;
		}
		/* Menu List Item Precision Alignment */
		.bc_fed .fed-menu-card {
			display: flex !important;
			align-items: center !important;
		}
		.bc_fed .fed-menu-card .fed-card-left {
			display: flex !important;
			align-items: center !important;
		}
		.bc_fed .fed-menu-card .fed-card-details {
			display: flex !important;
			flex-wrap: wrap !important;
			align-items: center !important;
		}
		.bc_fed .fed-menu-card .fed-menu-title {
			margin: 0 !important;
			padding: 0 !important;
			font-size: 14px !important;
			font-weight: 700 !important;
			line-height: 1.2 !important;
			color: #1e293b !important;
			display: inline-flex !important;
			align-items: center !important;
		}
		.bc_fed .fed-menu-card .fed-badge-item {
			display: inline-flex !important;
			align-items: center !important;
			line-height: 1 !important;
		}
		.bc_fed #fed_toast_notification {
			z-index: 99999999 !important;
		}
	</style>

	<div class="bc_fed fed-admin-wrap w-full max-w-none px-4 sm:px-8 py-6 sm:py-8 font-sans text-slate-800">
		<?php echo fed_loader(); ?>

		<!-- Toast Notification Element -->
		<div id="fed_toast_notification" class="fixed bottom-6 right-6 transform translate-y-16 opacity-0 transition-all duration-300 pointer-events-none flex items-center gap-3 bg-slate-900 text-white px-5 py-3 rounded-xl shadow-2xl border border-slate-700" style="z-index: 99999999 !important;">
			<span id="fed_toast_icon" class="text-emerald-400 text-base"><i class="fas fa-check-circle"></i></span>
			<span id="fed_toast_message" class="text-xs font-semibold tracking-wide">Changes saved successfully.</span>
		</div>

		<!-- Page Header & Action Bar (Full Width) -->
		<div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 mb-6 relative overflow-hidden">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
				<div class="flex items-center gap-3.5">
					<div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-xs shrink-0" style="background-color: #4f46e5 !important; color: #ffffff !important;">
						<i class="fas fa-bars text-sm" style="color: #ffffff !important;"></i>
					</div>
					<div>
						<div class="flex items-center gap-2.5">
							<h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight m-0 p-0">
								<?php esc_html_e( 'Dashboard Navigation Menus', 'frontend-dashboard' ); ?>
							</h1>
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
								<?php echo count( $menus ); ?> <?php esc_html_e( 'Items', 'frontend-dashboard' ); ?>
							</span>
						</div>
						<p class="text-xs text-slate-500 m-0 mt-0.5 font-medium">
							<?php esc_html_e( 'Manage frontend user dashboard navigation items, icons, dynamic drag-and-drop ordering, and role-based permissions.', 'frontend-dashboard' ); ?>
						</p>
					</div>
				</div>

				<div class="flex items-center gap-3 shrink-0">
					<button type="button" id="fed_open_add_menu_btn" class="fed-btn-primary h-10 inline-flex items-center justify-center gap-2 px-5 rounded-xl font-semibold text-xs transition-all active:scale-95 cursor-pointer shadow-sm">
						<i class="fas fa-plus text-xs" style="color: #ffffff !important;"></i>
						<span style="color: #ffffff !important;"><?php esc_html_e( 'Add New Menu', 'frontend-dashboard' ); ?></span>
					</button>
				</div>
			</div>

			<!-- Search Bar & Controls Bar -->
			<div class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
				<div class="relative w-full sm:w-96">
					<span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
						<i class="fas fa-search text-xs"></i>
					</span>
					<input type="text" id="fed_menu_filter_search" placeholder="<?php esc_attr_e( 'Search menus by name or slug...', 'frontend-dashboard' ); ?>" class="w-full pr-8 py-2 rounded-xl bg-slate-50 text-xs text-slate-700 font-medium transition-all outline-none" />
					<button type="button" id="fed_menu_filter_clear" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
						<i class="fas fa-times-circle text-xs"></i>
					</button>
				</div>

				<div class="flex items-center gap-2 text-xs font-medium text-slate-500 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-100">
					<i class="fas fa-arrows-alt-v text-indigo-500 text-xs"></i>
					<span><?php esc_html_e( 'Drag items to reorder dynamically', 'frontend-dashboard' ); ?></span>
				</div>
			</div>
		</div>

		<!-- Reorderable List Container (Full Width & Spacious) -->
		<div id="fed_dashboard_menu_sortable" class="space-y-3.5" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-url="<?php echo esc_url( $ajax_url ); ?>">
			<?php
			if ( ! empty( $menus ) ) {
				foreach ( $menus as $menu ) {
					$menu_id           = (int) $menu['id'];
					$menu_name         = esc_attr( $menu['menu'] );
					$menu_slug         = esc_attr( $menu['menu_slug'] );
					$menu_icon         = ! empty( $menu['menu_image_id'] ) ? esc_attr( $menu['menu_image_id'] ) : 'fas fa-link';
					$menu_order        = (int) $menu['menu_order'];
					$is_extra          = ( isset( $menu['extra'] ) && 'no' === $menu['extra'] ) ? 'no' : 'yes';
					$is_system_core    = ( 'no' === $is_extra || in_array( $menu_slug, array( 'profile', 'logout', 'post' ), true ) );
					$show_user_profile = isset( $menu['show_user_profile'] ) ? $menu['show_user_profile'] : 'Enable';
					$raw_roles         = isset( $menu['user_role'] ) ? $menu['user_role'] : '';
					$menu_key          = isset( $menu['menu_key'] ) ? $menu['menu_key'] : '';
					$menu_value        = isset( $menu['menu_value'] ) ? $menu['menu_value'] : '';
					$selected_roles    = array();

					if ( ! empty( $raw_roles ) ) {
						$unserialized = @unserialize( $raw_roles );
						if ( false !== $unserialized && is_array( $unserialized ) ) {
							$selected_roles = $unserialized;
						} elseif ( is_string( $raw_roles ) ) {
							$selected_roles = explode( ',', $raw_roles );
						}
					}

					$url_data_json = '';
					if ( 'url' === $menu_key && ! empty( $menu_value ) ) {
						$url_data = @unserialize( $menu_value );
						if ( is_array( $url_data ) ) {
							$url_data_json = wp_json_encode( $url_data );
						}
					}
					?>
					<div class="fed-menu-card bg-white rounded-xl py-3.5 px-4 sm:px-6 border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-slate-300 transition-all flex flex-col xl:flex-row xl:items-center justify-between gap-4 group cursor-default"
						data-id="<?php echo esc_attr( $menu_id ); ?>"
						data-name="<?php echo esc_attr( $menu['menu'] ); ?>"
						data-slug="<?php echo esc_attr( $menu_slug ); ?>"
						data-icon="<?php echo esc_attr( $menu['menu_image_id'] ); ?>"
						data-order="<?php echo esc_attr( $menu_order ); ?>"
						data-show-profile="<?php echo esc_attr( $show_user_profile ); ?>"
						data-menu-key="<?php echo esc_attr( $menu_key ); ?>"
						data-menu-value="<?php echo esc_attr( 'url' === $menu_key ? $url_data_json : $menu_value ); ?>"
						data-roles="<?php echo esc_attr( wp_json_encode( $selected_roles ) ); ?>"
						data-system="<?php echo $is_system_core ? 'yes' : 'no'; ?>">

						<!-- Left Group: Grip + Icon + Details (Horizontal Sequence) -->
						<div class="fed-card-left flex items-center gap-3.5 min-w-0 flex-1">
							<!-- Drag Handle -->
							<div class="fed-drag-handle flex items-center justify-center w-7 h-7 rounded-lg text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 cursor-grab active:cursor-grabbing transition-colors shrink-0" title="<?php esc_attr_e( 'Drag to reorder', 'frontend-dashboard' ); ?>">
								<i class="fas fa-grip-vertical text-sm"></i>
							</div>

							<!-- Icon Box -->
							<div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
								<i class="<?php echo esc_attr( $menu_icon ); ?>"></i>
							</div>

							<!-- Title & Meta Badges (Clean non-breaking sequence) -->
							<div class="fed-card-details flex flex-wrap items-center gap-2.5 min-w-0 flex-1">
								<span class="fed-menu-title text-sm font-bold text-slate-900 truncate mr-1">
									<?php echo esc_html( $menu['menu'] ); ?>
								</span>

								<!-- Slug Badge -->
								<span class="fed-badge-item inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono bg-slate-100 text-slate-600 border border-slate-200/60 font-medium shrink-0 whitespace-nowrap">
									slug: <?php echo esc_html( $menu_slug ); ?>
								</span>

								<!-- System vs Custom Badge -->
								<?php if ( $is_system_core ) : ?>
									<span class="fed-badge-item inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-100 shrink-0 whitespace-nowrap">
										<i class="fas fa-shield-alt text-[9px]"></i> <?php esc_html_e( 'System', 'frontend-dashboard' ); ?>
									</span>
								<?php else : ?>
									<span class="fed-badge-item inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 shrink-0 whitespace-nowrap">
										<i class="fas fa-star text-[9px]"></i> <?php esc_html_e( 'Custom', 'frontend-dashboard' ); ?>
									</span>
								<?php endif; ?>

								<!-- Page / External URL Badges -->
								<?php if ( 'yes' === $menu_key && ! empty( $menu_value ) ) : ?>
									<?php
									$page_id    = (int) $menu_value;
									$page_title = $page_id ? get_the_title( $page_id ) : __( 'Page', 'frontend-dashboard' );
									?>
									<span class="fed-badge-item inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-100 shrink-0 whitespace-nowrap" title="<?php echo esc_attr( sprintf( __( 'Mapped to Page: %s', 'frontend-dashboard' ), $page_title ) ); ?>">
										<i class="fas fa-file-alt text-[9px]"></i> <?php echo esc_html( $page_title ); ?>
									</span>
								<?php elseif ( 'url' === $menu_key ) : ?>
									<?php
									$url_arr = @unserialize( $menu_value );
									$ext_url = is_array( $url_arr ) && ! empty( $url_arr['url'] ) ? $url_arr['url'] : '';
									?>
									<span class="fed-badge-item inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-100 shrink-0 whitespace-nowrap" title="<?php echo esc_attr( $ext_url ); ?>">
										<i class="fas fa-external-link-alt text-[9px]"></i> <?php esc_html_e( 'External URL', 'frontend-dashboard' ); ?>
									</span>
								<?php endif; ?>

								<!-- Profile Status Indicator -->
								<span class="fed-badge-item inline-flex items-center gap-1.5 text-[11px] font-medium text-slate-500 pl-1 shrink-0 whitespace-nowrap">
									<?php if ( 'Disable' === $show_user_profile ) : ?>
										<span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
										<span><?php esc_html_e( 'Profile Hidden', 'frontend-dashboard' ); ?></span>
									<?php else : ?>
										<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
										<span class="text-emerald-700"><?php esc_html_e( 'Profile Visible', 'frontend-dashboard' ); ?></span>
									<?php endif; ?>
								</span>

								<!-- Roles Summary Pill with +N -->
								<?php echo fed_render_user_roles_badge( $selected_roles, $user_roles ); ?>
							</div>
						</div>

						<!-- Right Actions: Comfortable Edit & Delete -->
						<div class="flex items-center gap-2 shrink-0 whitespace-nowrap pt-2 xl:pt-0 border-t xl:border-t-0 border-slate-100">
							<!-- Edit Button -->
							<button type="button" class="fed-btn-edit h-8.5 inline-flex items-center justify-center gap-1.5 px-3.5 rounded-lg font-semibold text-xs transition-all cursor-pointer">
								<i class="fas fa-edit text-[11px]"></i>
								<span><?php esc_html_e( 'Edit', 'frontend-dashboard' ); ?></span>
							</button>

							<!-- Delete Button -->
							<?php if ( ! $is_system_core ) : ?>
								<button type="button" class="fed-btn-delete h-8.5 inline-flex items-center justify-center gap-1.5 px-3.5 rounded-lg font-semibold text-xs transition-all cursor-pointer" title="<?php esc_attr_e( 'Delete this menu', 'frontend-dashboard' ); ?>">
									<i class="fas fa-trash-alt text-[11px]"></i>
									<span><?php esc_html_e( 'Delete', 'frontend-dashboard' ); ?></span>
								</button>
							<?php else : ?>
								<span class="h-8.5 inline-flex items-center justify-center gap-1.5 px-3 rounded-lg bg-slate-50 text-slate-400 font-semibold text-xs border border-slate-100 cursor-not-allowed" title="<?php esc_attr_e( 'System menus cannot be deleted.', 'frontend-dashboard' ); ?>">
									<i class="fas fa-lock text-[10px]"></i>
									<span><?php esc_html_e( 'Locked', 'frontend-dashboard' ); ?></span>
								</span>
							<?php endif; ?>
						</div>
					</div>
					<?php
				}
			} else {
				?>
				<div class="bg-white rounded-2xl p-10 text-center border border-slate-200/80 shadow-sm">
					<div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4">
						<i class="fas fa-bars"></i>
					</div>
					<h3 class="text-base font-bold text-slate-800 mb-1.5"><?php esc_html_e( 'No Dashboard Menus Found', 'frontend-dashboard' ); ?></h3>
					<p class="text-xs text-slate-500 max-w-md mx-auto mb-5"><?php esc_html_e( 'Get started by creating your first navigation menu.', 'frontend-dashboard' ); ?></p>
					<button type="button" class="fed-trigger-add-btn fed-btn-primary h-10 inline-flex items-center justify-center gap-2 px-5 rounded-xl font-semibold text-xs transition-all">
						<i class="fas fa-plus text-xs" style="color: #ffffff !important;"></i>
						<span style="color: #ffffff !important;"><?php esc_html_e( 'Add Menu', 'frontend-dashboard' ); ?></span>
					</button>
				</div>
				<?php
			}
			?>
		</div>

		<!-- Hidden Fallback Form for AJAX Submission -->
		<form id="fed_menu_hidden_ajax_form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form_dashboard_menu' ) ); ?>" class="hidden">
			<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
			<input type="hidden" name="menu_id" id="fed_hidden_menu_id" value="" />
			<input type="hidden" name="fed_menu_slug" id="fed_hidden_menu_slug" value="" />
			<input type="hidden" name="fed_menu_name" id="fed_hidden_menu_name" value="" />
			<input type="hidden" name="menu_image_id" id="fed_hidden_menu_image_id" value="" />
			<input type="hidden" name="fed_menu_order" id="fed_hidden_menu_order" value="1" />
			<input type="hidden" name="show_user_profile" id="fed_hidden_show_user_profile" value="Enable" />
			<div id="fed_hidden_roles_container"></div>
		</form>

		<!-- ========================================== -->
		<!-- MODAL: ADD / EDIT DASHBOARD MENU (WIDE 2-COL) -->
		<!-- ========================================== -->
		<div id="fed_menu_modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6 md:p-8 transition-opacity duration-200">
			<div class="bg-white rounded-2xl shadow-2xl border border-slate-200/90 w-full max-w-4xl xl:max-w-5xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0 modal-content-box my-6">
				<!-- Modal Header -->
				<div class="px-7 sm:px-8 py-5 sm:py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
					<div class="flex items-center gap-3.5">
						<div id="fed_modal_icon_box" class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-xs shrink-0" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<i class="fas fa-plus text-sm" style="color: #ffffff !important;"></i>
						</div>
						<div>
							<h3 id="fed_modal_title" class="text-base sm:text-lg font-bold text-slate-900 m-0">
								<?php esc_html_e( 'Add New Dashboard Menu', 'frontend-dashboard' ); ?>
							</h3>
							<p id="fed_modal_subtitle" class="text-xs text-slate-500 m-0 mt-0.5 font-medium">
								<?php esc_html_e( 'Configure title, icon, role access, and target links.', 'frontend-dashboard' ); ?>
							</p>
						</div>
					</div>
					<button type="button" id="fed_modal_close_btn" class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer">
						<i class="fas fa-times text-sm"></i>
					</button>
				</div>

				<!-- Modal Body Form (2-Column Breathable Grid) -->
				<form id="fed_modal_menu_form" class="p-6 sm:p-8">
					<input type="hidden" name="menu_id" id="fed_form_menu_id" value="" />
					<input type="hidden" name="fed_nonce" value="<?php echo esc_attr( $nonce ); ?>" />

					<!-- Inline Error Banner for Form Errors -->
					<div id="fed_modal_error_box" class="hidden mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 font-medium flex items-center gap-2.5">
						<i class="fas fa-exclamation-circle text-rose-500 text-sm shrink-0"></i>
						<span id="fed_modal_error_msg"></span>
					</div>

					<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-7 items-start">
						<!-- LEFT COLUMN: Core Attributes -->
						<div class="space-y-5 sm:space-y-6">
							<!-- Name & Slug Row -->
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 items-start">
								<div>
									<label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
										<?php esc_html_e( 'Menu Name / Label', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
									</label>
									<input type="text" id="fed_form_menu_name" name="fed_menu_name" required placeholder="<?php esc_attr_e( 'e.g. Invoices, Analytics', 'frontend-dashboard' ); ?>" class="text-xs text-slate-800 font-medium outline-none" />
								</div>

								<div>
									<label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
										<?php esc_html_e( 'Menu Slug', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
									</label>
									<input type="text" id="fed_form_menu_slug" name="fed_menu_slug" required placeholder="<?php esc_attr_e( 'e.g. invoices', 'frontend-dashboard' ); ?>" class="text-xs text-slate-800 outline-none font-mono font-medium" />
									<p id="fed_slug_hint" class="text-[10px] text-slate-400 mt-1.5 mb-0 leading-tight">
										<?php esc_html_e( 'Unique identifier for URL routing.', 'frontend-dashboard' ); ?>
									</p>
								</div>
							</div>

							<!-- Icon & Order Position Row -->
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 items-start">
								<!-- Icon Field -->
								<div>
									<label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
										<?php esc_html_e( 'Menu Icon', 'frontend-dashboard' ); ?>
									</label>
									<div class="flex items-center gap-2.5">
										<div id="fed_selected_icon_preview" class="bg-indigo-50 border border-indigo-100 text-indigo-600 shadow-2xs">
											<i class="fas fa-link"></i>
										</div>
										<div class="relative flex-1">
											<input type="text" id="fed_form_menu_icon" name="menu_image_id" value="fas fa-link" placeholder="fas fa-icon" class="text-xs text-slate-800 font-mono font-medium outline-none" />
											<button type="button" id="fed_trigger_icon_picker" class="text-xs font-semibold cursor-pointer gap-1.5">
												<i class="fas fa-images text-[10px]"></i>
												<span><?php esc_html_e( 'Browse', 'frontend-dashboard' ); ?></span>
											</button>
										</div>
									</div>
								</div>

								<!-- Menu Order -->
								<div>
									<label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
										<?php esc_html_e( 'Menu Order Position', 'frontend-dashboard' ); ?>
									</label>
									<input type="number" id="fed_form_menu_order" name="fed_menu_order" value="1" min="1" class="text-xs text-slate-800 font-medium outline-none" />
								</div>
							</div>

							<!-- Frontend Profile Tab Visibility Card -->
							<div class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex items-center justify-between">
								<div>
									<h4 class="text-xs font-semibold text-slate-800 m-0 mb-1">
										<?php esc_html_e( 'Frontend Profile Tab Visibility', 'frontend-dashboard' ); ?>
									</h4>
									<p class="text-[11px] text-slate-400 m-0">
										<?php esc_html_e( 'Show or hide this menu inside the profile sidebar.', 'frontend-dashboard' ); ?>
									</p>
								</div>
								<label class="relative inline-flex items-center cursor-pointer ml-3 shrink-0">
									<input type="checkbox" id="fed_form_show_user_profile" name="show_user_profile_toggle" value="yes" class="sr-only peer" checked />
									<div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
								</label>
							</div>
						</div>

						<!-- RIGHT COLUMN: Permissions & Target Link Options -->
						<div class="space-y-5 sm:space-y-6">
							<!-- Role Access Permissions Card -->
							<div class="border border-slate-200/90 rounded-2xl p-4 sm:p-5 bg-white shadow-2xs space-y-3.5">
								<div class="flex items-center justify-between gap-2 pb-2.5 border-b border-slate-100">
									<label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 m-0">
										<?php esc_html_e( 'Role Access Permissions', 'frontend-dashboard' ); ?>
									</label>

									<!-- Segmented Pill Selector -->
									<div class="inline-flex p-0.5 bg-slate-100 rounded-lg shrink-0">
										<button type="button" id="fed_role_mode_all_btn" class="px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all bg-white text-indigo-700 shadow-2xs cursor-pointer">
											<i class="fas fa-globe mr-1 text-[9px]"></i> <?php esc_html_e( 'All Roles', 'frontend-dashboard' ); ?>
										</button>
										<button type="button" id="fed_role_mode_specific_btn" class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
											<i class="fas fa-user-lock mr-1 text-[9px]"></i> <?php esc_html_e( 'Specific', 'frontend-dashboard' ); ?>
										</button>
									</div>
								</div>

								<!-- Specific Roles Container -->
								<div id="fed_specific_roles_wrapper" class="space-y-2.5 pt-0.5 hidden">
									<div class="flex items-center justify-between gap-2">
										<div class="relative flex-1">
											<span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400">
												<i class="fas fa-search text-[9px]"></i>
											</span>
											<input type="text" id="fed_role_search_filter" placeholder="<?php echo esc_attr( sprintf( __( 'Filter %d roles...', 'frontend-dashboard' ), $total_roles_count ) ); ?>" class="w-full pr-2 py-1 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition-all outline-none font-medium" style="padding-left: 28px !important; height: 34px !important; min-height: 34px !important;" />
										</div>

										<div class="flex items-center gap-1.5 shrink-0 text-xs">
											<span id="fed_role_selected_count" class="text-[10px] font-semibold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-100">
												0 / <?php echo $total_roles_count; ?>
											</span>
											<button type="button" id="fed_roles_select_all" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 cursor-pointer">
												<?php esc_html_e( 'All', 'frontend-dashboard' ); ?>
											</button>
											<span class="text-slate-300 text-xs">|</span>
											<button type="button" id="fed_roles_deselect_all" class="text-[11px] font-semibold text-slate-500 hover:text-slate-700 cursor-pointer">
												<?php esc_html_e( 'None', 'frontend-dashboard' ); ?>
											</button>
										</div>
									</div>

									<div id="fed_roles_chips_container" class="flex flex-wrap gap-2 max-h-32 overflow-y-auto p-2.5 border border-slate-100 rounded-xl bg-slate-50/50">
										<?php foreach ( $user_roles as $role_key => $role_name ) : ?>
											<label class="fed-role-chip inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all cursor-pointer select-none" data-role-name="<?php echo esc_attr( strtolower( $role_name ) ); ?>" data-role-key="<?php echo esc_attr( strtolower( $role_key ) ); ?>">
												<input type="checkbox" name="user_role[<?php echo esc_attr( $role_key ); ?>]" value="Enable" class="fed-role-checkbox" checked />
												<span class="truncate max-w-[130px]"><?php echo esc_html( $role_name ); ?></span>
											</label>
										<?php endforeach; ?>
									</div>
								</div>
							</div>

							<!-- Menu Content & Target Link Card (With Searchable Page Combobox) -->
							<div class="border border-slate-200/90 rounded-2xl p-4 sm:p-5 bg-slate-50/60 shadow-2xs space-y-3.5">
								<div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
									<label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 m-0">
										<?php esc_html_e( 'Menu Content & Target Link', 'frontend-dashboard' ); ?>
									</label>
									<span class="text-[10px] text-slate-400 font-medium">
										<?php esc_html_e( 'Optional Override', 'frontend-dashboard' ); ?>
									</span>
								</div>

								<!-- Option 1: Show WordPress Page (AJAX Searchable) -->
								<div class="space-y-2">
									<label class="inline-flex items-center gap-2 cursor-pointer select-none text-xs font-semibold text-slate-700">
										<input type="checkbox" id="fed_checkbox_show_page" name="fed_menu_key" value="yes" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer" />
										<span><?php esc_html_e( 'Show pages for this menu item?', 'frontend-dashboard' ); ?></span>
									</label>

									<div id="fed_page_select_wrapper" class="hidden pl-6 pt-1.5">
										<label class="block text-[10px] font-semibold uppercase text-slate-500 mb-1.5">
											<?php esc_html_e( 'Select WordPress Page (Searchable)', 'frontend-dashboard' ); ?>
										</label>
										<input type="hidden" name="fed_menu_value" id="fed_form_menu_page_id" value="" />

										<div class="relative" id="fed_page_combobox_container">
											<div class="relative flex items-center">
												<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
													<i class="fas fa-search text-xs"></i>
												</span>
												<input type="text" id="fed_page_search_input" placeholder="<?php esc_attr_e( 'Type to search WordPress pages...', 'frontend-dashboard' ); ?>" autocomplete="off" class="w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-xl pr-8 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200 transition-all font-medium" style="padding-left: 34px !important; height: 40px !important;" />
												<button type="button" id="fed_clear_page_btn" class="hidden absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer" title="<?php esc_attr_e( 'Clear page selection', 'frontend-dashboard' ); ?>">
													<i class="fas fa-times-circle text-xs"></i>
												</button>
											</div>

											<!-- AJAX Results Dropdown Panel -->
											<div id="fed_page_dropdown_results" class="hidden absolute z-30 left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-slate-200 max-h-48 overflow-y-auto py-1 text-xs">
												<div id="fed_page_dropdown_loading" class="p-3 text-center text-slate-400 text-xs hidden">
													<i class="fas fa-spinner fa-spin mr-1 text-indigo-500"></i> <?php esc_html_e( 'Searching pages...', 'frontend-dashboard' ); ?>
												</div>
												<div id="fed_page_dropdown_list"></div>
											</div>
										</div>
									</div>
								</div>

								<div class="flex items-center gap-3 my-1">
									<div class="flex-1 border-t border-slate-200/70"></div>
									<span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest px-1">[OR]</span>
									<div class="flex-1 border-t border-slate-200/70"></div>
								</div>

								<!-- Option 2: Convert to External URL -->
								<div class="space-y-2">
									<label class="inline-flex items-center gap-2 cursor-pointer select-none text-xs font-semibold text-slate-700">
										<input type="checkbox" id="fed_checkbox_external_url" name="fed_menu_key" value="url" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer" />
										<span><?php esc_html_e( 'Convert this menu into External URL ?', 'frontend-dashboard' ); ?></span>
									</label>

									<div id="fed_external_url_wrapper" class="hidden pl-6 pt-1.5 grid grid-cols-1 sm:grid-cols-3 gap-2.5">
										<div class="sm:col-span-1">
											<label class="block text-[10px] font-semibold uppercase text-slate-500 mb-1.5">
												<?php esc_html_e( 'Open Link In', 'frontend-dashboard' ); ?>
											</label>
											<select name="fed_menu_value_url[target]" id="fed_form_url_target" class="w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200" style="height: 40px !important;">
												<option value=""><?php esc_html_e( 'Open in?', 'frontend-dashboard' ); ?></option>
												<option value="_self"><?php esc_html_e( 'Same Window', 'frontend-dashboard' ); ?></option>
												<option value="_blank"><?php esc_html_e( 'New Window', 'frontend-dashboard' ); ?></option>
											</select>
										</div>
										<div class="sm:col-span-2">
											<label class="block text-[10px] font-semibold uppercase text-slate-500 mb-1.5">
												<?php esc_html_e( 'External URL', 'frontend-dashboard' ); ?>
											</label>
											<input type="url" name="fed_menu_value_url[url]" id="fed_form_url_input" placeholder="https://example.com" class="w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-xl px-3 py-1.5 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200" style="height: 40px !important;" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Modal Footer Actions (Spacious & Distinct) -->
					<div class="mt-7 pt-5 border-t border-slate-100 flex items-center justify-end gap-3.5">
						<button type="button" id="fed_modal_cancel_btn" class="h-10 px-5 rounded-xl font-semibold text-xs transition-all cursor-pointer">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="submit" id="fed_modal_submit_btn" class="fed-btn-primary h-10 inline-flex items-center justify-center gap-2 px-6 rounded-xl font-semibold text-xs transition-all active:scale-95 cursor-pointer shadow-sm">
							<i class="fas fa-save text-xs" style="color: #ffffff !important;"></i>
							<span id="fed_modal_submit_text" style="color: #ffffff !important;"><?php esc_html_e( 'Save Menu', 'frontend-dashboard' ); ?></span>
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- ========================================== -->
		<!-- MODAL: SEARCHABLE FONT AWESOME ICON PICKER -->
		<!-- ========================================== -->
		<div id="fed_icon_picker_modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6 transition-opacity duration-200">
			<div class="bg-white rounded-2xl shadow-2xl border border-slate-200/90 w-full max-w-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0 icon-modal-content my-6">
				<!-- Modal Header -->
				<div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0">
							<?php esc_html_e( 'Select an Icon', 'frontend-dashboard' ); ?>
						</h3>
						<p class="text-[11px] text-slate-500 m-0 font-medium">
							<?php esc_html_e( 'Choose from FontAwesome icons for your dashboard menu.', 'frontend-dashboard' ); ?>
						</p>
					</div>
					<button type="button" id="fed_close_icon_picker_btn" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer">
						<i class="fas fa-times text-sm"></i>
					</button>
				</div>

				<!-- Search Input -->
				<div class="p-4 sm:p-5 border-b border-slate-100 bg-white">
					<div class="relative">
						<span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
							<i class="fas fa-search text-xs"></i>
						</span>
						<input type="text" id="fed_icon_search_input" placeholder="<?php esc_attr_e( 'Search icons (e.g. user, dashboard, chart, cart, lock)...', 'frontend-dashboard' ); ?>" class="w-full pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 font-medium focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition-all outline-none" style="padding-left: 38px !important;" />
					</div>
				</div>

				<!-- Icons Grid -->
				<div id="fed_icons_grid" class="p-5 sm:p-6 grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2.5 max-h-80 overflow-y-auto">
					<?php
					$fa_icons = fed_font_awesome_list();
					foreach ( $fa_icons as $fa_class => $fa_name ) :
						?>
						<button type="button" class="fed-icon-choice flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-100 hover:border-indigo-300 bg-slate-50/60 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 transition-all cursor-pointer group" data-icon="<?php echo esc_attr( $fa_class ); ?>" title="<?php echo esc_attr( $fa_class ); ?>">
							<i class="<?php echo esc_attr( $fa_class ); ?> text-base mb-1 group-hover:scale-125 transition-transform"></i>
							<span class="text-[9px] text-slate-400 group-hover:text-indigo-600 truncate w-full text-center font-mono">
								<?php echo esc_html( str_replace( array( 'fas fa-', 'far fa-', 'fab fa-' ), '', $fa_class ) ); ?>
							</span>
						</button>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<!-- Custom Delete Confirmation Modal -->
		<div id="fed_delete_confirm_modal" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200" style="z-index: 999999 !important;">
			<div class="delete-modal-content bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 transform scale-95 opacity-0 transition-all duration-200 text-center">
				<div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4" style="background-color: #ffe4e6 !important; color: #e11d48 !important;">
					<i class="fas fa-trash-alt text-lg"></i>
				</div>
				<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1" id="fed_delete_modal_title">
					<?php esc_html_e( 'Delete Menu Item?', 'frontend-dashboard' ); ?>
				</h3>
				<p class="text-xs sm:text-sm text-slate-500 mb-6 leading-relaxed" id="fed_delete_modal_desc">
					<?php esc_html_e( 'Are you sure you want to delete this navigation menu? This action cannot be undone.', 'frontend-dashboard' ); ?>
				</p>
				<div class="flex items-center justify-center gap-3">
					<button type="button" id="fed_cancel_delete_btn" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
						<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" id="fed_confirm_delete_btn" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
						<span><?php esc_html_e( 'Yes, Delete', 'frontend-dashboard' ); ?></span>
					</button>
				</div>
			</div>
		</div>

		<!-- Legacy Icon Popup for backwards compatibility -->
		<?php fed_menu_icons_popup(); ?>
	</div>

	<!-- ======================================================== -->
	<!-- INLINE JAVASCRIPT LOGIC: DRAG & DROP, MODALS, AJAX CRUD  -->
	<!-- ======================================================== -->
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		var $sortableList = $('#fed_dashboard_menu_sortable');
		var $menuModal = $('#fed_menu_modal');
		var $iconModal = $('#fed_icon_picker_modal');
		var $deleteModal = $('#fed_delete_confirm_modal');
		var $hiddenForm = $('#fed_menu_hidden_ajax_form');
		var ajaxUrl = '<?php echo esc_url( $ajax_url ); ?>';
		var nonce = '<?php echo esc_attr( $nonce ); ?>';
		var totalRoles = <?php echo (int) $total_roles_count; ?>;

		// Delete Modal state
		var itemToDelete = null;

		// Helper: Toast Notification
		function showToast(message, isError) {
			var $toast = $('#fed_toast_notification');
			var $msg = $('#fed_toast_message');
			var $icon = $('#fed_toast_icon');

			$msg.text(message);
			if (isError) {
				$icon.html('<i class="fas fa-exclamation-circle text-rose-400"></i>');
			} else {
				$icon.html('<i class="fas fa-check-circle text-emerald-400"></i>');
			}

			$toast.removeClass('translate-y-16 opacity-0 pointer-events-none').addClass('translate-y-0 opacity-100');
			setTimeout(function() {
				$toast.removeClass('translate-y-0 opacity-100').addClass('translate-y-16 opacity-0 pointer-events-none');
			}, 3500);
		}

		// Helper: Update Roles Counter & Chip Styles
		function updateRolesCounter() {
			var checkedCount = $('.fed-role-checkbox:checked').length;
			$('#fed_role_selected_count').text(checkedCount + ' / ' + totalRoles);

			// Highlight active chips with active border
			$('.fed-role-chip').each(function() {
				var isChecked = $(this).find('.fed-role-checkbox').is(':checked');
				if (isChecked) {
					$(this).addClass('is-active');
				} else {
					$(this).removeClass('is-active');
				}
			});
		}

		// Role Mode Switcher: All Roles vs Specific Roles
		function setRoleMode(mode) {
			if (mode === 'all') {
				$('#fed_role_mode_all_btn').addClass('bg-white text-indigo-700 shadow-2xs').removeClass('text-slate-600');
				$('#fed_role_mode_specific_btn').removeClass('bg-white text-indigo-700 shadow-2xs').addClass('text-slate-600');
				$('#fed_specific_roles_wrapper').addClass('hidden');
				$('.fed-role-checkbox').prop('checked', true);
			} else {
				$('#fed_role_mode_specific_btn').addClass('bg-white text-indigo-700 shadow-2xs').removeClass('text-slate-600');
				$('#fed_role_mode_all_btn').removeClass('bg-white text-indigo-700 shadow-2xs').addClass('text-slate-600');
				$('#fed_specific_roles_wrapper').removeClass('hidden');
			}
			updateRolesCounter();
		}

		$('#fed_role_mode_all_btn').on('click', function(e) {
			e.preventDefault();
			setRoleMode('all');
		});

		$('#fed_role_mode_specific_btn').on('click', function(e) {
			e.preventDefault();
			setRoleMode('specific');
		});

		// 1. Dynamic In-Place Drag & Drop Sorting
		if ($sortableList.length && $.fn.sortable) {
			$sortableList.sortable({
				handle: '.fed-drag-handle',
				items: '.fed-menu-card',
				opacity: 0.85,
				placeholder: 'bg-indigo-50 border-2 border-dashed border-indigo-300 rounded-xl h-11 mb-2 transition-all',
				cursor: 'grabbing',
				update: function(event, ui) {
					var orderedIds = [];
					$sortableList.find('.fed-menu-card').each(function(index) {
						var id = $(this).data('id');
						if (id) {
							orderedIds.push(id);
							$(this).data('order', index + 1);
						}
					});

					// Show CSS loader during drag-and-drop save
					$('.preview-area').removeClass('hide');

					// Save via AJAX
					$.ajax({
						url: ajaxUrl,
						type: 'POST',
						data: {
							action: 'fed_admin_menu_sorting',
							fed_nonce: nonce,
							order: orderedIds
						},
						success: function(response) {
							$('.preview-area').addClass('hide');
							if (response && response.success) {
								showToast('Menu order updated successfully!', false);
							} else {
								showToast((response && response.data && response.data.message) || 'Error saving menu order', true);
							}
						},
						error: function() {
							$('.preview-area').addClass('hide');
							showToast('Server communication error during reorder', true);
						}
					});
				}
			});
		}

		// 2. Instant Search / Filter for Dashboard Menus
		$('#fed_menu_filter_search').on('input', function() {
			var query = $(this).val().toLowerCase().trim();
			var $clearBtn = $('#fed_menu_filter_clear');

			if (query.length > 0) {
				$clearBtn.removeClass('hidden');
			} else {
				$clearBtn.addClass('hidden');
			}

			$sortableList.find('.fed-menu-card').each(function() {
				var name = ($(this).data('name') || '').toString().toLowerCase();
				var slug = ($(this).data('slug') || '').toString().toLowerCase();
				if (name.indexOf(query) !== -1 || slug.indexOf(query) !== -1) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});

		$('#fed_menu_filter_clear').on('click', function() {
			$('#fed_menu_filter_search').val('').trigger('input');
		});

		// Helper: AJAX Search WordPress Pages (On Demand)
		var pageSearchTimer = null;
		function fetchPages(query, selectedId, callback) {
			$('#fed_page_dropdown_loading').removeClass('hidden');
			$('#fed_page_dropdown_list').empty();
			$('#fed_page_dropdown_results').removeClass('hidden');

			$.ajax({
				url: ajaxUrl,
				type: 'GET',
				data: {
					action: 'fed_search_wp_pages',
					q: query || '',
					selected_id: selectedId || 0,
					fed_nonce: nonce
				},
				success: function(res) {
					$('#fed_page_dropdown_loading').addClass('hidden');
					if (res && res.success && res.data && res.data.pages) {
						var pages = res.data.pages;
						if (pages.length === 0) {
							$('#fed_page_dropdown_list').html('<div class="p-3 text-center text-slate-400 text-xs">No matching pages found</div>');
						} else {
							var html = '';
							pages.forEach(function(p) {
								html += '<button type="button" class="fed-page-choice-item w-full text-left px-3.5 py-2 hover:bg-indigo-50 hover:text-indigo-700 flex items-center justify-between text-xs text-slate-700 transition-colors cursor-pointer border-b border-slate-50 last:border-0" data-id="' + p.id + '" data-title="' + $('<div>').text(p.title).html() + '">';
								html += '<span class="font-medium truncate mr-2">' + $('<div>').text(p.title).html() + '</span>';
								html += '<span class="text-[10px] text-slate-400 font-mono shrink-0">#' + p.id + '</span>';
								html += '</button>';
							});
							$('#fed_page_dropdown_list').html(html);
						}
						if (typeof callback === 'function') {
							callback(pages);
						}
					}
				},
				error: function() {
					$('#fed_page_dropdown_loading').addClass('hidden');
					$('#fed_page_dropdown_list').html('<div class="p-3 text-center text-rose-500 text-xs">Failed to load pages</div>');
				}
			});
		}

		$('#fed_page_search_input').on('focus', function() {
			var val = $(this).val().trim();
			var selId = $('#fed_form_menu_page_id').val();
			fetchPages(val, selId);
		});

		$('#fed_page_search_input').on('input', function() {
			var q = $(this).val().trim();
			clearTimeout(pageSearchTimer);
			pageSearchTimer = setTimeout(function() {
				fetchPages(q, 0);
			}, 250);
		});

		$(document).on('click', '.fed-page-choice-item', function(e) {
			e.preventDefault();
			var pageId = $(this).data('id');
			var pageTitle = $(this).data('title');
			$('#fed_form_menu_page_id').val(pageId);
			$('#fed_page_search_input').val(pageTitle);
			$('#fed_clear_page_btn').removeClass('hidden');
			$('#fed_page_dropdown_results').addClass('hidden');
		});

		$('#fed_clear_page_btn').on('click', function(e) {
			e.preventDefault();
			$('#fed_form_menu_page_id').val('');
			$('#fed_page_search_input').val('').focus();
			$(this).addClass('hidden');
			fetchPages('', 0);
		});

		$(document).on('click', function(e) {
			if (!$(e.target).closest('#fed_page_combobox_container').length) {
				$('#fed_page_dropdown_results').addClass('hidden');
			}
		});

		// 3. Open Modal for Add
		function openAddModal() {
			$('#fed_modal_error_box').addClass('hidden');
			$('#fed_modal_title').text('Add New Dashboard Menu');
			$('#fed_modal_subtitle').text('Configure title, icon, role access, and target links.');
			$('#fed_modal_icon_box').html('<i class="fas fa-plus text-xs" style="color:#ffffff !important;"></i>');
			$('#fed_modal_submit_text').text('Add Menu');

			$('#fed_form_menu_id').val('');
			$('#fed_form_menu_name').val('');
			$('#fed_form_menu_slug').val('').prop('readonly', false).removeClass('bg-slate-100 cursor-not-allowed');
			$('#fed_slug_hint').text('Unique identifier for URL routing.');
			$('#fed_form_menu_icon').val('fas fa-link');
			$('#fed_selected_icon_preview').html('<i class="fas fa-link"></i>');
			$('#fed_form_menu_order').val($sortableList.find('.fed-menu-card').length + 1);
			$('#fed_form_show_user_profile').prop('checked', true);

			// Reset Page / URL Options
			$('#fed_checkbox_show_page').prop('checked', false);
			$('#fed_checkbox_external_url').prop('checked', false);
			$('#fed_page_select_wrapper').addClass('hidden');
			$('#fed_external_url_wrapper').addClass('hidden');
			$('#fed_form_menu_page_id').val('');
			$('#fed_page_search_input').val('');
			$('#fed_clear_page_btn').addClass('hidden');
			$('#fed_page_dropdown_results').addClass('hidden');
			$('#fed_form_url_target').val('');
			$('#fed_form_url_input').val('');

			// Default role mode to All
			setRoleMode('all');
			$('#fed_role_search_filter').val('');

			$menuModal.removeClass('hidden');
			setTimeout(function() {
				$menuModal.find('.modal-content-box').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
			}, 10);
		}

		$('#fed_open_add_menu_btn, .fed-trigger-add-btn').on('click', function(e) {
			e.preventDefault();
			openAddModal();
		});

		// 4. Open Modal for Edit
		$sortableList.on('click', '.fed-btn-edit', function(e) {
			e.preventDefault();
			$('#fed_modal_error_box').addClass('hidden');
			var $card = $(this).closest('.fed-menu-card');
			var id = $card.data('id');
			var name = $card.data('name');
			var slug = $card.data('slug');
			var icon = $card.data('icon') || 'fas fa-link';
			var order = $card.data('order');
			var showProfile = $card.data('show-profile');
			var menuKey = $card.data('menu-key') || '';
			var menuValue = $card.data('menu-value');
			var roles = $card.data('roles');
			var isSystem = $card.data('system') === 'yes';

			$('#fed_modal_title').text('Edit Menu: ' + name);
			$('#fed_modal_subtitle').text('Update navigation attributes, permissions, and target link.');
			$('#fed_modal_icon_box').html('<i class="fas fa-edit text-xs" style="color:#ffffff !important;"></i>');
			$('#fed_modal_submit_text').text('Update Menu');

			$('#fed_form_menu_id').val(id);
			$('#fed_form_menu_name').val(name);
			$('#fed_form_menu_slug').val(slug);

			if (isSystem) {
				$('#fed_form_menu_slug').prop('readonly', true).addClass('bg-slate-100 cursor-not-allowed');
				$('#fed_slug_hint').text('System menu slugs are protected.');
			} else {
				$('#fed_form_menu_slug').prop('readonly', false).removeClass('bg-slate-100 cursor-not-allowed');
				$('#fed_slug_hint').text('Unique identifier for URL routing.');
			}

			$('#fed_form_menu_icon').val(icon);
			$('#fed_selected_icon_preview').html('<i class="' + icon + '"></i>');
			$('#fed_form_menu_order').val(order);
			$('#fed_form_show_user_profile').prop('checked', showProfile !== 'Disable');
			$('#fed_role_search_filter').val('');

			// Populate Page / External URL Options
			if (menuKey === 'yes') {
				$('#fed_checkbox_show_page').prop('checked', true);
				$('#fed_checkbox_external_url').prop('checked', false);
				$('#fed_page_select_wrapper').removeClass('hidden');
				$('#fed_external_url_wrapper').addClass('hidden');
				$('#fed_form_url_target').val('');
				$('#fed_form_url_input').val('');

				var pageId = menuValue ? parseInt(menuValue, 10) : 0;
				$('#fed_form_menu_page_id').val(pageId || '');
				if (pageId > 0) {
					$('#fed_clear_page_btn').removeClass('hidden');
					$('#fed_page_search_input').val('Loading page #' + pageId + '...');
					fetchPages('', pageId, function(pages) {
						var found = pages.find(function(p) { return p.id == pageId; });
						if (found) {
							$('#fed_page_search_input').val(found.title);
						} else {
							$('#fed_page_search_input').val('Page #' + pageId);
						}
						$('#fed_page_dropdown_results').addClass('hidden');
					});
				} else {
					$('#fed_clear_page_btn').addClass('hidden');
					$('#fed_page_search_input').val('');
				}
			} else if (menuKey === 'url') {
				$('#fed_checkbox_external_url').prop('checked', true);
				$('#fed_checkbox_show_page').prop('checked', false);
				$('#fed_external_url_wrapper').removeClass('hidden');
				$('#fed_page_select_wrapper').addClass('hidden');
				$('#fed_form_menu_page_id').val('');
				$('#fed_page_search_input').val('');
				$('#fed_clear_page_btn').addClass('hidden');

				var urlData = {};
				if (typeof menuValue === 'object' && menuValue !== null) {
					urlData = menuValue;
				} else if (typeof menuValue === 'string') {
					try {
						urlData = JSON.parse(menuValue);
					} catch(e) {
						urlData = { url: menuValue, target: '' };
					}
				}
				$('#fed_form_url_target').val(urlData.target || '');
				$('#fed_form_url_input').val(urlData.url || '');
			} else {
				$('#fed_checkbox_show_page').prop('checked', false);
				$('#fed_checkbox_external_url').prop('checked', false);
				$('#fed_page_select_wrapper').addClass('hidden');
				$('#fed_external_url_wrapper').addClass('hidden');
				$('#fed_form_menu_page_id').val('');
				$('#fed_page_search_input').val('');
				$('#fed_clear_page_btn').addClass('hidden');
				$('#fed_form_url_target').val('');
				$('#fed_form_url_input').val('');
			}

			// Populate roles
			$('.fed-role-checkbox').prop('checked', false);
			if (roles && Array.isArray(roles) && roles.length > 0 && roles.length < totalRoles) {
				setRoleMode('specific');
				roles.forEach(function(roleKey) {
					$('input[name="user_role[' + roleKey + ']"]').prop('checked', true);
				});
			} else {
				setRoleMode('all');
			}
			updateRolesCounter();

			$menuModal.removeClass('hidden');
			setTimeout(function() {
				$menuModal.find('.modal-content-box').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
			}, 10);
		});

		// Toggle mutual exclusion for Page vs URL options
		$('#fed_checkbox_show_page').on('change', function() {
			if ($(this).is(':checked')) {
				$('#fed_checkbox_external_url').prop('checked', false);
				$('#fed_external_url_wrapper').addClass('hidden');
				$('#fed_page_select_wrapper').removeClass('hidden');
				if (!$('#fed_form_menu_page_id').val()) {
					$('#fed_page_search_input').focus();
				}
			} else {
				$('#fed_page_select_wrapper').addClass('hidden');
			}
		});

		$('#fed_checkbox_external_url').on('change', function() {
			if ($(this).is(':checked')) {
				$('#fed_checkbox_show_page').prop('checked', false);
				$('#fed_page_select_wrapper').addClass('hidden');
				$('#fed_external_url_wrapper').removeClass('hidden');
				$('#fed_form_url_input').focus();
			} else {
				$('#fed_external_url_wrapper').addClass('hidden');
			}
		});

		// Close Menu Modal
		function closeMenuModal() {
			$menuModal.find('.modal-content-box').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
			setTimeout(function() {
				$menuModal.addClass('hidden');
				$('#fed_modal_error_box').addClass('hidden');
			}, 200);
		}

		$('#fed_modal_close_btn, #fed_modal_cancel_btn').on('click', function(e) {
			e.preventDefault();
			closeMenuModal();
		});

		// Auto slugify name when creating new menu
		$('#fed_form_menu_name').on('input', function() {
			if (!$('#fed_form_menu_id').val()) {
				var slug = $(this).val().toLowerCase().replace(/[^a-z0-9 ]/g, '').replace(/\s+/g, '_').substring(0, 30);
				$('#fed_form_menu_slug').val(slug);
			}
		});

		// Role Checkboxes: Live Filter Bar for Roles
		$('#fed_role_search_filter').on('input', function() {
			var q = $(this).val().toLowerCase().trim();
			$('#fed_roles_chips_container .fed-role-chip').each(function() {
				var rName = $(this).data('role-name') || '';
				var rKey = $(this).data('role-key') || '';
				if (rName.indexOf(q) !== -1 || rKey.indexOf(q) !== -1) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});

		// Role Selection Quick Actions
		$('#fed_roles_select_all').on('click', function(e) {
			e.preventDefault();
			$('.fed-role-checkbox').prop('checked', true);
			updateRolesCounter();
		});

		$('#fed_roles_deselect_all').on('click', function(e) {
			e.preventDefault();
			$('.fed-role-checkbox').prop('checked', false);
			updateRolesCounter();
		});

		$(document).on('change', '.fed-role-checkbox', function() {
			updateRolesCounter();
		});

		// 5. Submit Modal Form (AJAX Save / Update)
		$('#fed_modal_menu_form').on('submit', function(e) {
			e.preventDefault();
			var $form = $(this);
			var menuName = $('#fed_form_menu_name').val().trim();
			var menuSlug = $('#fed_form_menu_slug').val().trim();
			var showProfile = $('#fed_form_show_user_profile').is(':checked') ? 'Enable' : 'Disable';

			$('#fed_modal_error_box').addClass('hidden');

			if (!menuName) {
				$('#fed_modal_error_msg').text('Please enter a Menu Name.');
				$('#fed_modal_error_box').removeClass('hidden');
				showToast('Please enter a Menu Name.', true);
				$('#fed_form_menu_name').focus();
				return;
			}
			if (!menuSlug) {
				$('#fed_modal_error_msg').text('Please enter a Menu Slug.');
				$('#fed_modal_error_box').removeClass('hidden');
				showToast('Please enter a Menu Slug.', true);
				$('#fed_form_menu_slug').focus();
				return;
			}

			// If All Roles mode is selected, ensure all checkboxes are checked before serialization
			if ($('#fed_specific_roles_wrapper').hasClass('hidden')) {
				$('.fed-role-checkbox').prop('checked', true);
			}

			var serializedData = $form.serialize();
			serializedData += '&show_user_profile=' + encodeURIComponent(showProfile);

			// If neither Page nor URL option is checked, explicitly clear fed_menu_key
			if (!$('#fed_checkbox_show_page').is(':checked') && !$('#fed_checkbox_external_url').is(':checked')) {
				serializedData += '&fed_menu_key=';
			}

			var $submitBtn = $('#fed_modal_submit_btn');
			$submitBtn.prop('disabled', true).addClass('opacity-75');

			// Show CSS loader
			$('.preview-area').removeClass('hide');

			$.ajax({
				url: ajaxUrl + '?action=fed_admin_setting_form_dashboard_menu',
				type: 'POST',
				data: {
					fed_action: 'save',
					data: serializedData
				},
				success: function(response) {
					if (response && response.success) {
						// On success: close modal, show toast, and keep CSS loader active until page finishes reloading
						closeMenuModal();
						showToast((response.data && response.data.message) || 'Dashboard menu saved successfully.', false);
						setTimeout(function() {
							window.location.reload();
						}, 600);
					} else {
						// On error: hide loader, re-enable button, display inline error banner in modal and show toast
						$('.preview-area').addClass('hide');
						$submitBtn.prop('disabled', false).removeClass('opacity-75');
						var errMsg = (response && response.data && response.data.message) || 'Failed to save menu.';
						$('#fed_modal_error_msg').text(errMsg);
						$('#fed_modal_error_box').removeClass('hidden');
						showToast(errMsg, true);
					}
				},
				error: function() {
					$('.preview-area').addClass('hide');
					$submitBtn.prop('disabled', false).removeClass('opacity-75');
					var errMsg = 'An unexpected network or server error occurred.';
					$('#fed_modal_error_msg').text(errMsg);
					$('#fed_modal_error_box').removeClass('hidden');
					showToast(errMsg, true);
				}
			});
		});

		// 6. Delete Menu Handler (Custom Modal Confirmation)
		function closeDeleteModal() {
			$deleteModal.find('.delete-modal-content').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
			setTimeout(function() {
				$deleteModal.addClass('hidden');
				itemToDelete = null;
			}, 200);
		}

		$sortableList.on('click', '.fed-btn-delete', function(e) {
			e.preventDefault();
			var $card = $(this).closest('.fed-menu-card');
			var menuId = $card.data('id');
			var menuName = $card.data('name');

			if (!menuId) return;

			itemToDelete = {
				$card: $card,
				id: menuId,
				name: menuName
			};

			$('#fed_delete_modal_title').text('Delete "' + menuName + '"?');
			$('#fed_delete_modal_desc').text('Are you sure you want to delete "' + menuName + '"? This action cannot be undone.');

			$deleteModal.removeClass('hidden');
			setTimeout(function() {
				$deleteModal.find('.delete-modal-content').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
			}, 10);
		});

		$('#fed_cancel_delete_btn').on('click', function(e) {
			e.preventDefault();
			closeDeleteModal();
		});

		$('#fed_confirm_delete_btn').on('click', function(e) {
			e.preventDefault();
			if (!itemToDelete) return;

			var payload = 'menu_id=' + encodeURIComponent(itemToDelete.id) + '&fed_menu_name=' + encodeURIComponent(itemToDelete.name) + '&fed_nonce=' + encodeURIComponent(nonce);
			var $targetCard = itemToDelete.$card;

			closeDeleteModal();
			$('.preview-area').removeClass('hide');

			$.ajax({
				url: ajaxUrl + '?action=fed_admin_setting_form_dashboard_menu',
				type: 'POST',
				data: {
					fed_action: 'delete',
					data: payload
				},
				success: function(response) {
					$('.preview-area').addClass('hide');
					if (response && response.success) {
						$targetCard.slideUp(200, function() {
							$(this).remove();
						});
						showToast('Menu deleted successfully.', false);
					} else {
						var errMsg = (response && response.data && response.data.message) || 'Failed to delete menu.';
						showToast(errMsg, true);
					}
				},
				error: function() {
					$('.preview-area').addClass('hide');
					showToast('Failed to communicate with server.', true);
				}
			});
		});

		// 7. Searchable Icon Picker Modal
		$('#fed_trigger_icon_picker').on('click', function(e) {
			e.preventDefault();
			$iconModal.removeClass('hidden');
			setTimeout(function() {
				$iconModal.find('.icon-modal-content').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
			}, 10);
		});

		$('#fed_close_icon_picker_btn').on('click', function(e) {
			e.preventDefault();
			$iconModal.find('.icon-modal-content').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
			setTimeout(function() {
				$iconModal.addClass('hidden');
			}, 200);
		});

		$('#fed_icon_search_input').on('input', function() {
			var q = $(this).val().toLowerCase().trim();
			$('#fed_icons_grid .fed-icon-choice').each(function() {
				var iconClass = $(this).data('icon').toLowerCase();
				if (iconClass.indexOf(q) !== -1) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});

		$('#fed_icons_grid').on('click', '.fed-icon-choice', function(e) {
			e.preventDefault();
			var selectedIcon = $(this).data('icon');
			$('#fed_form_menu_icon').val(selectedIcon);
			$('#fed_selected_icon_preview').html('<i class="' + selectedIcon + '"></i>');
			$('#fed_close_icon_picker_btn').trigger('click');
		});

		$('#fed_form_menu_icon').on('input', function() {
			var val = $(this).val().trim() || 'fas fa-link';
			$('#fed_selected_icon_preview').html('<i class="' + val + '"></i>');
		});
	});
	</script>
	<?php
}

/**
 * Dashboard Menu Items Add (Legacy compatibility).
 *
 * @param array $menus Menus.
 * @param array $user_roles User Roles.
 */
function fed_get_dashboard_menu_items_add( $menus, $user_roles ) {
	// Handled directly inside unified modal in fed_get_dashboard_menu_items()
}

/**
 * Dashboard Menu Items List (Legacy compatibility).
 *
 * @param array $menus Menus.
 * @param array $user_roles User Roles.
 */
function fed_get_dashboard_menu_items_list( $menus, $user_roles ) {
	// Handled directly inside modern drag & drop list in fed_get_dashboard_menu_items()
}

/**
 * Dashboard Menu Items Sort Data.
 *
 * @return array
 */
function fed_get_dashboard_menu_items_sort_data() {
	$menus      = fed_get_all_dashboard_display_menus();
	$new_menus  = array();
	$sort_menus = get_option( 'fed_admin_menu_sort' );

	if ( $sort_menus ) {
		foreach ( $sort_menus as $sort_index => $sort_menu ) {
			foreach ( $menus as $index => $menu ) {
				if ( $menu['menu_type'] . '_' . $menu['id'] === $sort_index ) {
					if ( is_null( $sort_menu['parent_id'] ) || empty( $sort_menu['parent_id'] ) ) {
						$new_menus[ $sort_index ]          = $menu;
						$new_menus[ $sort_index ]['order'] = $sort_menu['order'];
					} else {
						$new_menus[ $sort_menu['parent_type'] . '_' . $sort_menu['parent_id'] ]['submenu'][ $sort_index ]          = $menu;
						$new_menus[ $sort_menu['parent_type'] . '_' . $sort_menu['parent_id'] ]['submenu'][ $sort_index ]['order'] = $sort_menu['order'];
					}

					if ( 'user' === $menu['menu_type'] || 'custom' === $menu['menu_type'] ) {
						unset( $menus[ $menu['menu_slug'] ] );
					} else {
						unset( $menus[ $menu['id'] ] );
					}

					break;
				}
			}
		}

		foreach ( $menus as $m => $missing_menu ) {
			$new_menus[ $missing_menu['menu_type'] . '_' . $missing_menu['id'] ]          = $missing_menu;
			$new_menus[ $missing_menu['menu_type'] . '_' . $missing_menu['id'] ]['order'] = mt_rand( 99, 999 );
		}
	} else {
		$new_menus = $menus;
	}

	uasort( $new_menus, 'fed_sort_by_order' );

	return $new_menus;
}

/**
 * Dashboard Menu Items Sort (Legacy compatibility).
 */
function fed_get_dashboard_menu_items_sort() {
	// Replaced with dynamic in-place drag & drop on main page
}
