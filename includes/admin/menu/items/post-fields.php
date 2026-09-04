<?php
/**
 * Post Fields Management.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_get_post_fields_menu' ) ) {
	/**
	 * Post Fields Menu Entry Point.
	 */
	function fed_get_post_fields_menu() {
		$get_payload = \FED\Helpers\InputHelper::get();
		if ( isset( $get_payload['fed_action'] ) && 'post' === $get_payload['fed_action'] ) {
			if ( function_exists( 'fed_get_add_profile_post_fields' ) ) {
				fed_get_add_profile_post_fields();
				return;
			}
		}

		$post_fields = fed_fetch_table_rows_with_key( BC_FED_TABLE_POST, 'input_meta' );

		if ( $post_fields instanceof WP_Error ) {
			?>
			<div class="bc_fed max-w-7xl mx-auto px-4 sm:px-6 py-8 font-sans">
				<div class="p-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center gap-3">
					<i class="fas fa-exclamation-circle text-rose-500 text-xl shrink-0"></i>
					<div>
						<h4 class="font-bold text-sm mb-0.5"><?php esc_html_e( 'Database Error', 'frontend-dashboard' ); ?></h4>
						<p class="text-xs m-0"><?php echo esc_html( $post_fields->get_error_message() ); ?></p>
					</div>
				</div>
			</div>
			<?php
		} else {
			fed_get_post_fields_menu_item( $post_fields );
		}
	}
}

if ( ! function_exists( 'fed_get_post_type_icon' ) ) {
	/**
	 * Helper function to return icon for specific post types.
	 *
	 * @param string $post_type Post type slug.
	 * @return string FontAwesome class.
	 */
	function fed_get_post_type_icon( $post_type ) {
		switch ( $post_type ) {
			case 'post':
				return 'fas fa-thumbtack';
			case 'page':
				return 'fas fa-file-alt';
			case 'product':
				return 'fas fa-shopping-bag';
			case 'event':
				return 'fas fa-calendar-alt';
			case 'portfolio':
				return 'fas fa-briefcase';
			default:
				return 'fas fa-folder-open';
		}
	}
}

if ( ! function_exists( 'fed_get_post_fields_menu_item' ) ) {
	/**
	 * Render Post Fields Menu Items and Post Types UI.
	 *
	 * @param array $profiles Post custom fields from database.
	 */
	function fed_get_post_fields_menu_item( $profiles ) {
		wp_enqueue_script( 'jquery-ui-sortable' );

		if ( ! is_array( $profiles ) ) {
			$profiles = array();
		}
		usort( $profiles, 'fed_sort_by_order' );

		$public_post_types = fed_get_public_post_types();
		if ( ! is_array( $public_post_types ) || empty( $public_post_types ) ) {
			$public_post_types = array(
				'post' => __( 'Posts', 'frontend-dashboard' ),
				'page' => __( 'Pages', 'frontend-dashboard' ),
			);
		}

		// Group fields by post type
		$group_by = array();
		foreach ( $public_post_types as $pt_slug => $pt_label ) {
			$group_by[ $pt_slug ] = array();
		}

		foreach ( $profiles as $profile ) {
			$pt = isset( $profile['post_type'] ) && ! empty( $profile['post_type'] ) ? $profile['post_type'] : 'post';
			if ( isset( $group_by[ $pt ] ) ) {
				$group_by[ $pt ][] = $profile;
			} else {
				$first_key = ! empty( $public_post_types ) ? array_keys( $public_post_types )[0] : 'post';
				$fallback_key = isset( $group_by['post'] ) ? 'post' : $first_key;
				if ( isset( $group_by[ $fallback_key ] ) ) {
					$group_by[ $fallback_key ][] = $profile;
				}
			}
		}

		// Sort fields within each post type group
		foreach ( $group_by as $pt => &$fields_list ) {
			usort( $fields_list, 'fed_sort_by_order' );
		}
		unset( $fields_list );

		$total_fields_count     = count( $profiles );
		$total_post_types_count = count( $public_post_types );
		$nonce                  = wp_create_nonce( 'fed_nonce' );
		$ajax_url               = admin_url( 'admin-ajax.php' );
		$add_new_field_url      = add_query_arg( array( 'fed_action' => 'post' ), menu_page_url( 'fed_post_fields', false ) );
		$dashboard_menu_url     = menu_page_url( 'fed_dashboard_menu', false );
		?>

		<!-- Scoped Styles -->
		<style>
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
			.fed-btn-edit,
			button.fed-btn-edit,
			a.fed-btn-edit {
				color: #334155 !important;
				background-color: #f8fafc !important;
				border: 1px solid #e2e8f0 !important;
			}
			.fed-btn-edit:hover,
			button.fed-btn-edit:hover,
			a.fed-btn-edit:hover {
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
			.bc_fed #fed_field_filter_search {
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
			.bc_fed #fed_field_filter_search:focus {
				border-color: #6366f1 !important;
				background-color: #ffffff !important;
				box-shadow: 0 0 0 1px #6366f1 !important;
			}
			.bc_fed .fed-menu-tab-btn.is-active {
				background-color: #ffffff !important;
				border-color: #6366f1 !important;
				color: #4f46e5 !important;
				box-shadow: 0 1px 3px 0 rgba(99, 102, 241, 0.15) !important;
			}
			.bc_fed .fed-menu-tab-btn.is-active .fed-tab-icon {
				background-color: #4f46e5 !important;
				color: #ffffff !important;
			}
			.bc_fed .fed-menu-tab-btn.is-active .fed-tab-count {
				background-color: #eef2ff !important;
				color: #4f46e5 !important;
				border-color: #c7d2fe !important;
			}
			.bc_fed .fed-field-card {
				display: flex !important;
				align-items: center !important;
			}
			.bc_fed .fed-field-card.is-hidden,
			.bc_fed .fed-field-card.hidden {
				display: none !important;
			}
			.bc_fed .text-center {
				text-align: center !important;
			}
			.bc_fed .text-center p,
			.bc_fed p.text-center {
				text-align: center !important;
				margin-left: auto !important;
				margin-right: auto !important;
			}
			.bc_fed #fed_toast_notification {
				z-index: 99999999 !important;
			}
		</style>

		<div class="bc_fed fed-admin-wrap w-full max-w-none px-4 sm:px-8 py-6 sm:py-8 font-sans text-slate-800" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-ajax-url="<?php echo esc_url( $ajax_url ); ?>">
			<?php echo fed_loader(); ?>

			<!-- Toast Notification Element -->
			<div id="fed_toast_notification" class="fixed bottom-6 right-6 transform translate-y-16 opacity-0 transition-all duration-300 pointer-events-none flex items-center gap-3 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700" style="z-index: 99999999 !important;">
				<span id="fed_toast_icon" class="text-emerald-400 text-base"><i class="fas fa-check-circle"></i></span>
				<span id="fed_toast_message" class="text-xs font-semibold tracking-wide">Changes saved successfully.</span>
			</div>

			<!-- Modern Global Loading Overlay -->
			<div id="fed_global_loading_overlay" class="fixed inset-0 z-[10000000] hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs transition-all duration-200" style="z-index: 10000000 !important;">
				<div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100 flex flex-col items-center gap-4 text-center max-w-xs w-full mx-4">
					<div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xl shadow-xs animate-spin">
						<i class="fas fa-circle-notch"></i>
					</div>
					<div>
						<h4 class="text-sm font-bold text-slate-800 m-0" id="fed_loading_overlay_title"><?php esc_html_e( 'Loading Builder...', 'frontend-dashboard' ); ?></h4>
						<p class="text-xs text-slate-500 m-0 mt-1" id="fed_loading_overlay_desc"><?php esc_html_e( 'Fetching form field configurations.', 'frontend-dashboard' ); ?></p>
					</div>
				</div>
			</div>

			<!-- Page Header & Action Bar (Full Width) -->
			<div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 mb-6 relative overflow-hidden">
				<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
					<div class="flex items-center gap-3.5">
						<div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-xs shrink-0" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<i class="fas fa-newspaper text-sm" style="color: #ffffff !important;"></i>
						</div>
						<div>
							<div class="flex items-center gap-2.5">
								<h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight m-0 p-0">
									<?php esc_html_e( 'Post Form Fields', 'frontend-dashboard' ); ?>
								</h1>
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
									<?php echo (int) $total_fields_count; ?> <?php esc_html_e( 'Fields', 'frontend-dashboard' ); ?>
								</span>
							</div>
							<p class="text-xs text-slate-500 m-0 mt-0.5 font-medium">
								<?php esc_html_e( 'Manage, customize, and arrange custom fields organized by WordPress post types.', 'frontend-dashboard' ); ?>
							</p>
						</div>
					</div>

					<!-- Header Actions -->
					<div class="flex items-center gap-2.5 shrink-0">
						<a href="<?php echo esc_url( $add_new_field_url ); ?>" class="fed-open-add-field-modal fed-btn-primary h-10 inline-flex items-center justify-center gap-2 px-5 rounded-xl font-semibold text-xs transition-all active:scale-95 cursor-pointer shadow-sm no-underline" data-post-type="post">
							<i class="fas fa-plus text-xs" style="color: #ffffff !important;"></i>
							<span style="color: #ffffff !important;"><?php esc_html_e( 'Add New Post Field', 'frontend-dashboard' ); ?></span>
						</a>
					</div>
				</div>

				<!-- Search & Controls Subbar -->
				<div class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
					<div class="relative w-full sm:w-96">
						<span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
							<i class="fas fa-search text-xs"></i>
						</span>
						<input type="text" id="fed_field_filter_search" placeholder="<?php esc_attr_e( 'Search fields by label name or meta key...', 'frontend-dashboard' ); ?>" class="w-full pr-8 py-2 rounded-xl bg-slate-50 text-xs text-slate-700 font-medium transition-all outline-none" />
						<button type="button" id="fed_field_filter_clear" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
							<i class="fas fa-times-circle text-xs"></i>
						</button>
					</div>

					<div class="flex items-center gap-2 text-xs font-medium text-slate-500 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-100">
						<i class="fas fa-arrows-alt-v text-indigo-500 text-xs"></i>
						<span><?php esc_html_e( 'Drag field rows to reorder position dynamically', 'frontend-dashboard' ); ?></span>
					</div>
				</div>
			</div>

			<!-- Main Full-Width Flex Layout (Sidebar Post Type Tabs + Content Fields Panel) -->
			<div class="flex flex-col lg:flex-row gap-6 items-start">
				<!-- LEFT SIDEBAR: Post Types Navigation Tabs -->
				<div class="w-full lg:w-72 xl:w-80 shrink-0 space-y-3">
					<div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs space-y-2">
						<div class="px-2 py-1.5 flex items-center justify-between border-b border-slate-100 pb-2.5">
							<span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
								<?php esc_html_e( 'Post Types', 'frontend-dashboard' ); ?>
							</span>
							<span class="text-[11px] font-semibold text-slate-400">
								<?php echo (int) $total_post_types_count; ?> <?php esc_html_e( 'Active', 'frontend-dashboard' ); ?>
							</span>
						</div>

						<!-- Post Type Tab Buttons List -->
						<div class="space-y-1.5 pt-1" id="fed_menu_tabs_nav">
							<?php
							$tab_idx = 0;
							foreach ( $public_post_types as $pt_slug => $pt_name ) :
								$pt_slug_esc = esc_attr( $pt_slug );
								$pt_name_esc = esc_html( $pt_name );
								$pt_icon     = fed_get_post_type_icon( $pt_slug );
								$field_cnt   = isset( $group_by[ $pt_slug ] ) ? count( $group_by[ $pt_slug ] ) : 0;
								$is_default  = ( 0 === $tab_idx );
								?>
								<button type="button"
									class="fed-menu-tab-btn w-full flex items-center justify-between p-3 rounded-xl border transition-all text-left cursor-pointer group <?php echo $is_default ? 'is-active bg-white border-indigo-500 text-indigo-700 shadow-2xs' : 'bg-slate-50/70 border-slate-200/70 text-slate-700 hover:bg-slate-100/80 hover:border-slate-300'; ?>"
									data-tab-target="fed_tab_content_<?php echo esc_attr( $pt_slug_esc ); ?>"
									data-menu-slug="<?php echo esc_attr( $pt_slug_esc ); ?>"
									data-post-type="<?php echo esc_attr( $pt_slug_esc ); ?>">
									<div class="flex items-center gap-3 min-w-0">
										<div class="fed-tab-icon w-8 h-8 rounded-lg flex items-center justify-center text-xs shrink-0 transition-colors <?php echo $is_default ? 'bg-indigo-600 text-white' : 'bg-white border border-slate-200/80 text-slate-500 group-hover:text-indigo-600 group-hover:border-indigo-200'; ?>">
											<i class="<?php echo esc_attr( $pt_icon ); ?>"></i>
										</div>
										<span class="text-xs font-bold truncate">
											<?php echo esc_html( $pt_name_esc ); ?>
										</span>
									</div>
									<span class="fed-tab-count text-xs font-bold px-2 py-0.5 rounded-full border transition-colors <?php echo $is_default ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-100 text-slate-500 border-slate-200/60'; ?>">
										<?php echo (int) $field_cnt; ?>
									</span>
								</button>
								<?php
								$tab_idx++;
							endforeach;
							?>
						</div>
					</div>

					<!-- Shortcut info card -->
					<div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 text-xs text-indigo-950 space-y-2">
						<div class="flex items-center gap-2 font-bold text-indigo-900">
							<i class="fas fa-info-circle text-indigo-600"></i>
							<span><?php esc_html_e( 'Post Type Fields', 'frontend-dashboard' ); ?></span>
						</div>
						<p class="text-[11px] text-indigo-800/80 m-0 leading-relaxed">
							<?php esc_html_e( 'Custom fields assigned to each post type will appear on frontend dashboard post submission and edit forms for authorized user roles.', 'frontend-dashboard' ); ?>
						</p>
					</div>
				</div>

				<!-- RIGHT CONTENT PANEL: Form Fields per Post Type Tab (Full Width Responsive) -->
				<div class="flex-1 min-w-0 w-full">
					<?php
					$pane_idx = 0;
					foreach ( $public_post_types as $pt_slug => $pt_name ) :
						$pt_slug_esc = esc_attr( $pt_slug );
						$pt_name_esc = esc_html( $pt_name );
						$pt_icon     = fed_get_post_type_icon( $pt_slug );
						$m_fields    = isset( $group_by[ $pt_slug ] ) ? $group_by[ $pt_slug ] : array();
						$is_visible  = ( 0 === $pane_idx );
						?>
						<div id="fed_tab_content_<?php echo esc_attr( $pt_slug_esc ); ?>" class="fed-tab-pane space-y-4 <?php echo $is_visible ? '' : 'hidden'; ?>" data-menu-slug="<?php echo esc_attr( $pt_slug_esc ); ?>" data-post-type="<?php echo esc_attr( $pt_slug_esc ); ?>">
							<!-- Section Header Card -->
							<div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
								<div class="flex items-center gap-3 min-w-0">
									<div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-sm shrink-0">
										<i class="<?php echo esc_attr( $pt_icon ); ?>"></i>
									</div>
									<div>
										<div class="flex items-center gap-2.5">
											<h2 class="text-sm sm:text-base font-bold text-slate-900 m-0 p-0">
												<?php echo esc_html( $pt_name_esc ); ?> <?php esc_html_e( 'Fields', 'frontend-dashboard' ); ?>
											</h2>
											<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
												<span class="fed-tab-fields-counter"><?php echo count( $m_fields ); ?></span> <?php esc_html_e( 'Fields', 'frontend-dashboard' ); ?>
											</span>
										</div>
										<p class="text-[11px] text-slate-500 m-0 mt-0.5 font-medium">
											<?php
											/* translators: %s: Post Type Name */
											printf( esc_html__( 'Custom fields rendered inside the "%s" frontend submission forms.', 'frontend-dashboard' ), esc_html( $pt_name_esc ) );
											?>
										</p>
									</div>
								</div>

								<div class="flex items-center gap-2 shrink-0">
									<a href="<?php echo esc_url( add_query_arg( array( 'fed_action' => 'post', 'post_type' => $pt_slug_esc ), menu_page_url( 'fed_post_fields', false ) ) ); ?>" class="fed-open-add-field-modal fed-btn-primary h-9 inline-flex items-center justify-center gap-2 px-4 rounded-xl text-xs font-semibold transition-all active:scale-95 cursor-pointer shadow-xs no-underline" data-post-type="<?php echo esc_attr( $pt_slug_esc ); ?>">
										<i class="fas fa-plus text-xs" style="color: #ffffff !important;"></i>
										<span style="color: #ffffff !important;"><?php esc_html_e( 'Add Field', 'frontend-dashboard' ); ?></span>
									</a>
								</div>
							</div>

							<!-- Fields List / Reorderable Container -->
							<div class="fed-fields-sortable space-y-3"
								data-post-type="<?php echo esc_attr( $pt_slug_esc ); ?>"
								data-url="<?php echo esc_url( $ajax_url ); ?>">
								<?php
								if ( ! empty( $m_fields ) ) {
									foreach ( $m_fields as $field ) {
										$field_id     = (int) $field['id'];
										$field_label  = esc_attr( $field['label_name'] );
										$field_meta   = esc_attr( $field['input_meta'] );
										$field_type   = esc_attr( $field['input_type'] );
										$field_order  = (int) $field['input_order'];
										$is_extra     = ! fed_check_field_is_belongs_to_extra( $field['input_meta'] );
										$type_icon    = function_exists( 'fed_get_profile_field_type_icon' ) ? fed_get_profile_field_type_icon( $field['input_type'] ) : 'fas fa-pen-nib';

										// Required check
										$is_required = ( true === $field['is_required'] || 'true' === $field['is_required'] || 'enable' === strtolower( $field['is_required'] ) );

										$edit_url = add_query_arg(
											array(
												'fed_input_id' => $field_id,
												'fed_action'   => 'post',
											),
											menu_page_url( 'fed_post_fields', false )
										);
										?>
										<div class="fed-field-card bg-white rounded-xl py-3.5 px-4 sm:px-6 border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-slate-300 transition-all flex flex-col xl:flex-row xl:items-center justify-between gap-3.5 group cursor-default"
											id="fed_field_row_<?php echo esc_attr( $field_id ); ?>"
											data-id="<?php echo esc_attr( $field_id ); ?>"
											data-label="<?php echo esc_attr( strtolower( $field_label ) ); ?>"
											data-meta="<?php echo esc_attr( strtolower( $field_meta ) ); ?>"
											data-type="<?php echo esc_attr( $field_type ); ?>">

											<!-- Left: Drag handle + Type Icon + Info (Single Line on Desktop) -->
											<div class="flex items-center gap-3.5 min-w-0 flex-1">
												<!-- Drag Handle -->
												<div class="fed-drag-handle flex items-center justify-center w-7 h-7 rounded-lg text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 cursor-grab active:cursor-grabbing transition-colors shrink-0" title="<?php esc_attr_e( 'Drag to reorder', 'frontend-dashboard' ); ?>">
													<i class="fas fa-grip-vertical text-sm"></i>
												</div>

												<!-- Type Icon Box -->
												<div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-sm shrink-0 shadow-2xs" title="<?php echo esc_attr( sprintf( __( 'Input Type: %s', 'frontend-dashboard' ), $field_type ) ); ?>">
													<i class="<?php echo esc_attr( $type_icon ); ?>"></i>
												</div>

												<!-- Details & Badges (Clean horizontal flow) -->
												<div class="flex flex-wrap items-center gap-2.5 min-w-0 flex-1">
													<div class="flex items-center gap-1.5 mr-1 shrink-0">
														<span class="text-xs sm:text-sm font-bold text-slate-900 truncate">
															<?php echo esc_html( $field['label_name'] ); ?>
														</span>
														<?php if ( $is_required ) : ?>
															<span class="text-rose-500 font-bold text-xs" title="<?php esc_attr_e( 'Required Field', 'frontend-dashboard' ); ?>">*</span>
														<?php endif; ?>
													</div>

													<!-- Meta Key Pill -->
													<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono bg-slate-100 text-slate-600 border border-slate-200/60 shrink-0" title="<?php esc_attr_e( 'Database Meta Key', 'frontend-dashboard' ); ?>">
														<?php echo esc_html( $field['input_meta'] ); ?>
													</span>

													<!-- Field Type Pill -->
													<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 shrink-0">
														<?php echo esc_html( ucwords( str_replace( '_', ' ', $field_type ) ) ); ?>
													</span>

													<!-- Required Status Pill -->
													<?php if ( $is_required ) : ?>
														<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80 shrink-0">
															<?php esc_html_e( 'Required', 'frontend-dashboard' ); ?>
														</span>
													<?php endif; ?>
												</div>
											</div>

											<!-- Right: Actions (Clean horizontal row) -->
											<div class="flex items-center justify-between sm:justify-end gap-3.5 shrink-0 pt-2 xl:pt-0 border-t xl:border-t-0 border-slate-100 whitespace-nowrap">
												<div class="flex items-center gap-2 shrink-0">
													<!-- Edit Button -->
													<a href="<?php echo esc_url( $edit_url ); ?>" class="fed-open-edit-field-modal fed-btn-edit h-8.5 inline-flex items-center justify-center gap-1.5 px-3.5 rounded-lg font-semibold text-xs transition-all cursor-pointer no-underline shadow-2xs" data-id="<?php echo esc_attr( $field_id ); ?>" data-post-type="<?php echo esc_attr( $pt_slug_esc ); ?>">
														<i class="fas fa-edit text-[11px]"></i>
														<span><?php esc_html_e( 'Edit', 'frontend-dashboard' ); ?></span>
													</a>

													<!-- Delete Button -->
													<?php if ( $is_extra ) : ?>
														<button type="button"
															class="fed-btn-delete fed-trigger-delete-field h-8.5 inline-flex items-center justify-center gap-1.5 px-3.5 rounded-lg font-semibold text-xs transition-all cursor-pointer shadow-2xs"
															data-id="<?php echo esc_attr( $field_id ); ?>"
															data-name="<?php echo esc_attr( $field['label_name'] ); ?>"
															title="<?php esc_attr_e( 'Delete this post field', 'frontend-dashboard' ); ?>">
															<i class="fas fa-trash-alt text-[11px]"></i>
															<span><?php esc_html_e( 'Delete', 'frontend-dashboard' ); ?></span>
														</button>
													<?php endif; ?>
												</div>
											</div>
										</div>
										<?php
									}
								} else {
									?>
									<div class="bg-white rounded-2xl p-8 sm:p-12 text-center border border-slate-200/80 shadow-2xs flex flex-col items-center justify-center">
										<div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xl mb-3">
											<i class="fas fa-layer-group"></i>
										</div>
										<h4 class="text-sm font-bold text-slate-800 mb-1 text-center w-full">
											<?php esc_html_e( 'No Custom Fields in this Post Type', 'frontend-dashboard' ); ?>
										</h4>
										<p class="text-xs text-slate-500 max-w-sm mx-auto mb-4 text-center">
											<?php
											/* translators: %s: Post Type Name */
											printf( esc_html__( 'No custom fields are currently assigned to "%s". Click below to add your first post field.', 'frontend-dashboard' ), esc_html( $pt_name_esc ) );
											?>
										</p>
										<a href="<?php echo esc_url( add_query_arg( array( 'fed_action' => 'post', 'post_type' => $pt_slug_esc ), menu_page_url( 'fed_post_fields', false ) ) ); ?>" class="fed-open-add-field-modal fed-btn-primary h-9 inline-flex items-center justify-center gap-1.5 px-4 rounded-xl font-semibold text-xs transition-all shadow-xs no-underline" data-post-type="<?php echo esc_attr( $pt_slug_esc ); ?>">
											<i class="fas fa-plus text-xs" style="color: #ffffff !important;"></i>
											<span style="color: #ffffff !important;"><?php esc_html_e( 'Add Field to this Post Type', 'frontend-dashboard' ); ?></span>
										</a>
									</div>
									<?php
								}
								?>

								<!-- Dynamic No Search Results Container -->
								<div class="fed-search-no-results hidden bg-white rounded-2xl p-8 sm:p-12 text-center border border-slate-200/80 shadow-2xs flex flex-col items-center justify-center">
									<div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-base mb-2.5">
										<i class="fas fa-search"></i>
									</div>
									<h4 class="text-xs sm:text-sm font-bold text-slate-800 mb-1 text-center w-full">
										<?php esc_html_e( 'No fields match your search in this post type', 'frontend-dashboard' ); ?>
									</h4>
									<p class="text-[11px] text-slate-500 max-w-sm mx-auto mb-3 text-center fed-other-tabs-match-hint"></p>
									<button type="button" class="fed-btn-secondary fed-clear-search-btn h-8 inline-flex items-center gap-1.5 px-3.5 rounded-lg text-xs font-semibold cursor-pointer">
										<i class="fas fa-times text-[10px]"></i>
										<span><?php esc_html_e( 'Clear Search', 'frontend-dashboard' ); ?></span>
									</button>
								</div>
							</div>
						</div>
						<?php
						$pane_idx++;
					endforeach;
					?>
				</div>
			</div>
		</div>

		<!-- Custom Delete Confirmation Modal -->
		<div id="fed_delete_field_modal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs transition-all duration-200" style="z-index: 99999 !important;">
			<div class="delete-modal-content bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full mx-4 shadow-2xl border border-slate-100 text-center transform scale-95 opacity-0 transition-all duration-200">
				<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs">
					<i class="fas fa-trash-alt"></i>
				</div>
				<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
					<?php esc_html_e( 'Delete Form Field?', 'frontend-dashboard' ); ?>
				</h3>
				<p class="text-xs text-slate-500 leading-relaxed mb-6" id="fed_delete_field_desc">
					<?php esc_html_e( 'Are you sure you want to delete this field? This action cannot be undone and may remove user submitted data.', 'frontend-dashboard' ); ?>
				</p>
				<div class="flex items-center justify-center gap-3">
					<button type="button" id="fed_cancel_delete_field_btn" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
						<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" id="fed_confirm_delete_field_btn" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
						<?php esc_html_e( 'Yes, Delete Field', 'frontend-dashboard' ); ?>
					</button>
				</div>
			</div>
		</div>

		<!-- True Full-Screen Form Field Builder Overlay -->
		<div id="fed_field_builder_modal" class="fixed inset-0 z-[999999] overflow-y-auto hidden bg-slate-900/80 backdrop-blur-md transition-opacity duration-200" style="z-index: 999999 !important; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important;">
			<div class="fed-builder-modal-box w-full min-h-screen bg-slate-100 p-4 sm:p-8 flex flex-col items-center transform scale-98 opacity-0 transition-all duration-200 relative">
				<!-- Floating Top-Right Close Button -->
				<button type="button" class="fed-close-builder-modal fixed top-5 right-6 z-[1000000] w-10 h-10 rounded-2xl bg-white hover:bg-slate-200 border border-slate-200/80 text-slate-700 shadow-md flex items-center justify-center transition-all cursor-pointer hover:scale-105 active:scale-95" title="<?php esc_attr_e( 'Close & Return to Form Fields', 'frontend-dashboard' ); ?>">
					<i class="fas fa-times text-sm"></i>
				</button>
				<div id="fed_field_builder_modal_content" class="w-full">
					<!-- Dynamically injected builder markup -->
				</div>
			</div>
		</div>

		<!-- Interactive Client-Side JavaScript -->
		<script>
		(function($) {
			'use strict';

			$(document).ready(function() {
				var $wrap = $('.fed-admin-wrap');
				var nonce = $wrap.data('nonce');
				var ajaxUrl = $wrap.data('ajax-url');
				var pendingDeleteId = null;
				var pendingDeleteName = null;
				var pendingDeleteRow = null;

				// ----------------------------------------------------
				// TOAST NOTIFICATION HELPER
				// ----------------------------------------------------
				function showToast(message, isError) {
					var $toast = $('#fed_toast_notification');
					var $msg = $('#fed_toast_message');
					var $icon = $('#fed_toast_icon');

					$msg.text(message);
					if (isError) {
						$icon.html('<i class="fas fa-exclamation-circle text-rose-400"></i>');
						$toast.addClass('border-rose-500/50');
					} else {
						$icon.html('<i class="fas fa-check-circle text-emerald-400"></i>');
						$toast.removeClass('border-rose-500/50');
					}

					$toast.removeClass('translate-y-16 opacity-0 pointer-events-none').addClass('translate-y-0 opacity-100');

					setTimeout(function() {
						$toast.removeClass('translate-y-0 opacity-100').addClass('translate-y-16 opacity-0 pointer-events-none');
					}, 3500);
				}

				// ----------------------------------------------------
				// TAB NAVIGATION SWITCHER
				// ----------------------------------------------------
				function activateTab(targetId) {
					var $btn = $('.fed-menu-tab-btn[data-tab-target="' + targetId + '"]');
					if (!$btn.length) return;

					$('.fed-menu-tab-btn').removeClass('is-active bg-white border-indigo-500 text-indigo-700 shadow-2xs')
						.addClass('bg-slate-50/70 border-slate-200/70 text-slate-700 hover:bg-slate-100/80 hover:border-slate-300');
					$('.fed-menu-tab-btn .fed-tab-icon').removeClass('bg-indigo-600 text-white')
						.addClass('bg-white border border-slate-200/80 text-slate-500');
					$('.fed-menu-tab-btn .fed-tab-count').removeClass('bg-indigo-50 text-indigo-700 border-indigo-200')
						.addClass('bg-white text-slate-500 border-slate-200');

					$btn.addClass('is-active bg-white border-indigo-500 text-indigo-700 shadow-2xs')
						.removeClass('bg-slate-50/70 border-slate-200/70 text-slate-700 hover:bg-slate-100/80 hover:border-slate-300');
					$btn.find('.fed-tab-icon').addClass('bg-indigo-600 text-white')
						.removeClass('bg-white border border-slate-200/80 text-slate-500');
					$btn.find('.fed-tab-count').addClass('bg-indigo-50 text-indigo-700 border-indigo-200')
						.removeClass('bg-white text-slate-500 border-slate-200');

					$('.fed-tab-pane').addClass('hidden');
					$('#' + targetId).removeClass('hidden');

					// Re-apply search filter in the newly activated tab
					var query = $.trim($('#fed_field_filter_search').val());
					if (query.length > 0) {
						filterFields(query);
					}
				}

				$(document).on('click', '.fed-menu-tab-btn', function(e) {
					e.preventDefault();
					var targetId = $(this).data('tab-target');
					activateTab(targetId);
				});

				$(document).on('click', '.fed-switch-tab-link', function(e) {
					e.preventDefault();
					var slug = $(this).data('slug');
					activateTab('fed_tab_content_' + slug);
				});

				// ----------------------------------------------------
				// LIVE SEARCH FILTER
				// ----------------------------------------------------
				function filterFields(query) {
					var q = $.trim(query).toLowerCase();
					var $allPanes = $('.fed-tab-pane');
					var $activePane = $('.fed-tab-pane:not(.hidden)');
					var activeSlug = $activePane.data('post-type');

					if (!q) {
						$('.fed-field-card').removeClass('is-hidden hidden');
						$('.fed-search-no-results').addClass('hidden');
						$('#fed_field_filter_clear').addClass('hidden');

						// Restore natural field counts on tab badges
						$('.fed-menu-tab-btn').each(function() {
							var $btn = $(this);
							var slug = $btn.data('post-type');
							var count = $('#fed_tab_content_' + slug).find('.fed-field-card').length;
							$btn.find('.fed-tab-count').text(count);
						});
						return;
					}

					$('#fed_field_filter_clear').removeClass('hidden');

					var matchesPerSlug = {};
					var otherMatchesInfo = [];

					$allPanes.each(function() {
						var $pane = $(this);
						var slug = $pane.data('post-type');
						var paneMatches = 0;

						$pane.find('.fed-field-card').each(function() {
							var $card = $(this);
							var label = ($card.data('label') || '').toString().toLowerCase();
							var meta = ($card.data('meta') || '').toString().toLowerCase();
							var type = ($card.data('type') || '').toString().toLowerCase();

							if (label.indexOf(q) !== -1 || meta.indexOf(q) !== -1 || type.indexOf(q) !== -1) {
								$card.removeClass('is-hidden hidden');
								paneMatches++;
							} else {
								$card.addClass('is-hidden hidden');
							}
						});

						matchesPerSlug[slug] = paneMatches;
						$('.fed-menu-tab-btn[data-post-type="' + slug + '"] .fed-tab-count').text(paneMatches);

						if (slug !== activeSlug && paneMatches > 0) {
							var menuName = $('.fed-menu-tab-btn[data-post-type="' + slug + '"]').find('.text-xs').text().trim();
							otherMatchesInfo.push({ slug: slug, name: menuName, count: paneMatches });
						}
					});

					var activeMatches = matchesPerSlug[activeSlug] || 0;
					var $noResults = $activePane.find('.fed-search-no-results');

					if (activeMatches === 0) {
						$noResults.removeClass('hidden');
						if (otherMatchesInfo.length > 0) {
							var hintsHtml = '<span>Found in other post types: </span>';
							otherMatchesInfo.forEach(function(item) {
								hintsHtml += '<button type="button" class="fed-switch-tab-link text-indigo-600 font-bold underline ml-1.5 cursor-pointer" data-slug="' + item.slug + '">' + item.name + ' (' + item.count + ' match' + (item.count > 1 ? 'es' : '') + ')</button>';
							});
							$noResults.find('.fed-other-tabs-match-hint').html(hintsHtml);
						} else {
							$noResults.find('.fed-other-tabs-match-hint').text('No fields match "' + query + '" across any post type.');
						}
					} else {
						$noResults.addClass('hidden');
					}
				}

				$('#fed_field_filter_search').on('input keyup', function() {
					filterFields($(this).val());
				});

				$('#fed_field_filter_clear, .fed-clear-search-btn').on('click', function() {
					$('#fed_field_filter_search').val('').focus();
					filterFields('');
				});

				// ----------------------------------------------------
				// DRAG & DROP SORTABLE
				// ----------------------------------------------------
				$('.fed-fields-sortable').each(function() {
					var $sortableList = $(this);
					$sortableList.sortable({
						handle: '.fed-drag-handle',
						items: '.fed-field-card',
						placeholder: 'bg-indigo-50/70 border-2 border-dashed border-indigo-300 rounded-xl h-16 my-2',
						opacity: 0.85,
						update: function(event, ui) {
							var orderIds = [];
							$sortableList.find('.fed-field-card').each(function() {
								var fId = $(this).data('id');
								if (fId) {
									orderIds.push(fId);
								}
							});

							var $loader = $('.fed_loader');
							$loader.removeClass('hidden');

							$.ajax({
								type: 'POST',
								url: ajaxUrl,
								data: {
									action: 'fed_admin_menu_sorting',
									table: 'fed_post',
									order: orderIds,
									fed_nonce: nonce
								},
								success: function(response) {
									$loader.addClass('hidden');
									if (response && response.success) {
										showToast(response.data && response.data.message ? response.data.message : 'Order updated successfully.', false);
									} else {
										showToast(response && response.data && response.data.message ? response.data.message : 'Could not save order.', true);
									}
								},
								error: function() {
									$loader.addClass('hidden');
									showToast('Network error while saving order.', true);
								}
							});
						}
					});
				});

				// ----------------------------------------------------
				// DELETE CONFIRMATION MODAL & ACTION
				// ----------------------------------------------------
				$(document).on('click', '.fed-trigger-delete-field', function(e) {
					e.preventDefault();
					var $btn = $(this);
					pendingDeleteId = $btn.data('id');
					pendingDeleteName = $btn.data('name');
					pendingDeleteRow = $btn.closest('.fed-field-card');

					$('#fed_delete_field_desc').text('Are you sure you want to delete "' + pendingDeleteName + '"? This action cannot be undone.');
					$('#fed_delete_field_modal').removeClass('hidden').css('display', 'flex');
					setTimeout(function() {
						$('#fed_delete_field_modal .delete-modal-content').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
					}, 10);
				});

				function closeDeleteModal() {
					$('#fed_delete_field_modal .delete-modal-content').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
					setTimeout(function() {
						$('#fed_delete_field_modal').addClass('hidden').css('display', 'none');
						pendingDeleteId = null;
						pendingDeleteName = null;
						pendingDeleteRow = null;
					}, 200);
				}

				$('#fed_cancel_delete_field_btn').on('click', function() {
					closeDeleteModal();
				});

				$('#fed_delete_field_modal').on('click', function(e) {
					if ($(e.target).is('#fed_delete_field_modal')) {
						closeDeleteModal();
					}
				});

				$('#fed_confirm_delete_field_btn').on('click', function() {
					if (!pendingDeleteId) {
						return;
					}

					var fieldId = pendingDeleteId;
					var fieldName = pendingDeleteName;
					var $row = pendingDeleteRow;
					var $loader = $('.fed_loader');

					closeDeleteModal();
					$loader.removeClass('hidden');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_user_profile_delete',
							fed_up_action: 'delete',
							data: 'post_id=' + fieldId + '&profile_name=' + encodeURIComponent(fieldName) + '&fed_nonce=' + nonce
						},
						success: function(response) {
							$loader.addClass('hidden');
							if (response && response.success) {
								if ($row && $row.length) {
									$row.fadeOut(300, function() {
										var $pane = $row.closest('.fed-tab-pane');
										var postType = $pane.data('post-type');
										$row.remove();

										// Update counters
										var remaining = $pane.find('.fed-field-card').length;
										$pane.find('.fed-tab-fields-counter').text(remaining);
										$('.fed-menu-tab-btn[data-post-type="' + postType + '"] .fed-tab-count').text(remaining);
									});
								}
								showToast((response.data && response.data.message) ? response.data.message : 'Post field deleted successfully.', false);
							} else {
								showToast((response && response.data && response.data.message) ? response.data.message : 'Could not delete field.', true);
							}
						},
						error: function() {
							$loader.addClass('hidden');
							showToast('Server error while deleting post field.', true);
						}
					});
				});

				// ----------------------------------------------------
				// FULL-PAGE POPUP / BUILDER MODAL WITH SPINNERS
				// ----------------------------------------------------
				function showLoading(title, desc) {
					if (title) $('#fed_loading_overlay_title').text(title);
					if (desc) $('#fed_loading_overlay_desc').text(desc);
					$('#fed_global_loading_overlay').removeClass('hidden').css('display', 'flex');
				}

				function hideLoading() {
					$('#fed_global_loading_overlay').addClass('hidden').css('display', 'none');
				}

				function openBuilderModal(fieldId, postType, $triggerBtn) {
					showLoading(
						fieldId ? 'Loading Field Settings...' : 'Opening Form Builder...',
						fieldId ? 'Fetching post field configurations & role permissions.' : 'Preparing new post field workspace.'
					);

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_get_field_builder_modal',
							fed_nonce: nonce,
							fed_action: 'post',
							fed_input_id: fieldId || '',
							post_type: postType || 'post'
						},
						success: function(response) {
							hideLoading();
							if ($triggerBtn && $triggerBtn.length) {
								$triggerBtn.removeClass('opacity-75 pointer-events-none');
								$triggerBtn.find('.fed-btn-spinner').remove();
								$triggerBtn.find('i').show();
							}

							if (response && response.success && response.data && response.data.html) {
								$('#fed_field_builder_modal_content').html(response.data.html);
								
								// Replace back/cancel links with close modal action
								$('#fed_field_builder_modal_content a[title="Back to Fields"]').attr('href', '#').addClass('fed-close-builder-modal');
								$('#fed_field_builder_modal_content a:contains("Cancel")').attr('href', '#').addClass('fed-close-builder-modal');

								$('#fed_field_builder_modal').removeClass('hidden');
								setTimeout(function() {
									$('#fed_field_builder_modal .fed-builder-modal-box').removeClass('scale-98 opacity-0').addClass('scale-100 opacity-100');
								}, 10);
							} else {
								showToast('Could not load field builder form.', true);
							}
						},
						error: function() {
							hideLoading();
							if ($triggerBtn && $triggerBtn.length) {
								$triggerBtn.removeClass('opacity-75 pointer-events-none');
								$triggerBtn.find('.fed-btn-spinner').remove();
								$triggerBtn.find('i').show();
							}
							showToast('Error loading field builder.', true);
						}
					});
				}

				function closeBuilderModal() {
					$('#fed_field_builder_modal .fed-builder-modal-box').removeClass('scale-100 opacity-100').addClass('scale-98 opacity-0');
					setTimeout(function() {
						$('#fed_field_builder_modal').addClass('hidden');
						$('#fed_field_builder_modal_content').empty();
					}, 200);
				}

				$(document).on('click', '.fed-open-add-field-modal', function(e) {
					e.preventDefault();
					var $btn = $(this);
					var postType = $btn.data('post-type') || $('.fed-tab-pane:not(.hidden)').data('post-type') || 'post';

					// Add spinner to button
					$btn.addClass('opacity-75 pointer-events-none');
					$btn.find('i').hide();
					if (!$btn.find('.fed-btn-spinner').length) {
						$btn.prepend('<i class="fas fa-circle-notch fa-spin fed-btn-spinner mr-1.5 text-xs"></i>');
					}

					openBuilderModal('', postType, $btn);
				});

				$(document).on('click', '.fed-open-edit-field-modal', function(e) {
					e.preventDefault();
					var $btn = $(this);
					var fieldId = $btn.data('id');
					var postType = $btn.data('post-type') || $('.fed-tab-pane:not(.hidden)').data('post-type') || 'post';

					// Add spinner to button
					$btn.addClass('opacity-75 pointer-events-none');
					$btn.find('i').hide();
					if (!$btn.find('.fed-btn-spinner').length) {
						$btn.prepend('<i class="fas fa-circle-notch fa-spin fed-btn-spinner mr-1.5 text-xs text-indigo-600"></i>');
					}

					openBuilderModal(fieldId, postType, $btn);
				});

				$(document).on('click', '.fed-close-builder-modal', function(e) {
					e.preventDefault();
					closeBuilderModal();
				});

				$('#fed_field_builder_modal').on('click', function(e) {
					if ($(e.target).is('#fed_field_builder_modal')) {
						closeBuilderModal();
					}
				});

				// Intercept AJAX Save in Builder Modal to refresh page/UI smoothly
				$(document).on('submit', '#fed_field_builder_modal form.fed_ajax', function(e) {
					e.preventDefault();
					var form = $(this);
					showLoading('Saving Field Changes...', 'Persisting configuration to database...');

					$.ajax({
						type: 'POST',
						url: form.attr('action'),
						data: form.serialize(),
						success: function(response) {
							hideLoading();
							var isSuccess = (response && (response.success || response.status === 'success' || (typeof response === 'object' && !response.error)));
							var message = (response && response.data && response.data.message) ? response.data.message : 'Field settings saved successfully.';
							if (!isSuccess && response && response.data && response.data.errorMessage) {
								message = response.data.errorMessage;
							}
							showToast(message, !isSuccess);

							if (isSuccess) {
								closeBuilderModal();
								// Smooth reload to show newly updated fields in list
								setTimeout(function() {
									window.location.reload();
								}, 600);
							}
						},
						error: function() {
							hideLoading();
							showToast('An error occurred while saving field settings.', true);
						}
					});
				});

			});
		})(jQuery);
		</script>
		<?php
	}
}
