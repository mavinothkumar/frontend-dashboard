<?php
/**
 * Admin General.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FED_Admin_General
 */
if ( ! class_exists( 'FED_Admin_General' ) ) {
	/**
	 * Class FED_Admin_General
	 */
	class FED_Admin_General {

		/**
		 * FED_Admin_General constructor.
		 */
		public function __construct() {
			add_action( 'wp_ajax_fed_admin_script_menu', array( $this, 'save_admin_script' ) );
		}

		/**
		 * General Tab.
		 */
		public function fed_admin_general_tab() {
			$fed_general = get_option( 'fed_admin_general' );
			$tabs        = $this->fed_get_admin_general_options( $fed_general );
			fed_common_layouts_admin_settings( $fed_general, $tabs );
		}

		/**
		 * General Options.
		 *
		 * @param  array $options  Options.
		 *
		 * @return mixed|void
		 */
		public function fed_get_admin_general_options( $options ) {
			return apply_filters(
				'fed_customize_admin_general_options', array(
					'fed_admin_scripts'    => array(
						'icon'      => 'fas fa-code',
						'name'      => __( 'Admin Scripts', 'frontend-dashboard' ),
						'callable'  => array(
							'object' => $this,
							'method' => 'fed_admin_script_menu_tab',
						),
						'arguments' => $options,
					),
					'fed_frontend_scripts' => array(
						'icon'      => 'fas fa-code',
						'name'      => __( 'Frontend Scripts', 'frontend-dashboard-extra' ),
						'callable'  => array(
							'object' => $this,
							'method' => 'fed_frontend_script_menu_tab',
						),
						'arguments' => $options,
					),
				)
			);
		}

		/**
		 * Save Admin Script Menu
		 */
		public function save_admin_script() {
			$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
			fed_verify_nonce( $request );
			$db_value = get_option( 'fed_general_scripts_styles', array() );
			$type     = 'admin';
			$default  = $this->default_admin_script();
			if ( isset( $request['fed_admin_script_type'] ) ) {
				$type    = 'frontend';
				$default = $this->default_frontend_script();
			}
			$admin_script = array();

			foreach ( $default as $index => $script ) {
				foreach ( $script as $key => $value ) {
					if ( isset( $request[ $type ][ $index ][ $key ] ) ) {
						$admin_script[ $index ][ $key ] = $key;
					}
				}
			}

			$db_value[ $type ] = $admin_script;

			update_option( 'fed_general_scripts_styles', $db_value );

			wp_send_json_success( array( 'message' => 'Successfully updated' ) );

		}

		/**
		 * Default Admin Script.
		 *
		 * @return mixed|void
		 */
		public function default_admin_script() {
			$scripts = apply_filters(
				'fed_default_admin_scripts_styles', array(
					'scripts' => array(
						'jquery'                => array(
							'wp_core'     => true,
							'name'        => 'JQuery',
							'plugin_name' => 'Frontend Dashboard',
						),
						'jquery-ui-core'        => array(
							'wp_core'     => true,
							'name'        => 'JQuery UI Core',
							'plugin_name' => 'Frontend Dashboard',
						),
						'jquery-ui-sortable'    => array(
							'wp_core'     => true,
							'name'        => 'JQuery UI Sortable',
							'plugin_name' => 'Frontend Dashboard',
						),
						'fed_sweetalert'        => array(
							'wp_core'      => false,
							'name'         => 'SweetAlert',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/sweetalert2.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_fontawesome'       => array(
							'wp_core'      => false,
							'name'         => 'FontAwesome',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/fontawesome.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_fontawesome-shims' => array(
							'wp_core'      => false,
							'name'         => 'FontAwesome Shims',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url(
								'/assets/frontend/js/fontawesome-shims.js',
								BC_FED_PLUGIN
							),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_admin_script'      => array(
							'wp_core'      => false,
							'name'         => 'FED Admin Script',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/admin/js/fed_admin_script.js', BC_FED_PLUGIN ),
							'dependencies' => array( 'jquery' ),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_bootstrap_script'  => array(
							'wp_core'      => false,
							'name'         => 'Bootstrap',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/bootstrap.js', BC_FED_PLUGIN ),
							'dependencies' => array( 'jquery' ),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_jscolor_script'    => array(
							'wp_core'      => false,
							'name'         => 'JSColor',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/jscolor.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_select2_script'    => array(
							'wp_core'      => false,
							'name'         => 'Select2',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/select2.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_flatpickr'         => array(
							'wp_core'      => false,
							'name'         => 'FlatPickr',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/flatpickr.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_datatables'        => array(
							'wp_core'      => false,
							'name'         => 'Data Tables',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/datatables.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_common'            => array(
							'wp_core'      => false,
							'name'         => 'FED Common Script',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/fed_common.js', BC_FED_PLUGIN ),
							'dependencies' => array( 'jquery' ),
							'version'      => false,
							'in_footer'    => true,
						),
					),
					'styles'  => array(
						'fed_admin_bootstrap'          => array(
							'wp_core'      => false,
							'name'         => 'Bootstrap',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/bootstrap.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_frontend_sweetalert'      => array(
							'wp_core'      => false,
							'name'         => 'SweetAlert',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/sweetalert2.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_admin_font_awesome'       => array(
							'wp_core'      => false,
							'name'         => 'FontAwesome',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/fontawesome.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_admin_font_awesome-shims' => array(
							'wp_core'      => false,
							'name'         => 'FontAwesomeShims',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url(
								'/assets/frontend/css/fontawesome-shims.css',
								BC_FED_PLUGIN
							),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_flatpikcr'                => array(
							'wp_core'      => false,
							'name'         => 'FlatPikcr',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/flatpickr.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_select2'                  => array(
							'wp_core'      => false,
							'name'         => 'Select 2',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/select2.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_datatables'               => array(
							'wp_core'      => false,
							'name'         => 'DataTables',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/datatables.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_admin_style'              => array(
							'wp_core'      => false,
							'name'         => 'FED Admin',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/admin/css/fed_admin_style.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_global_admin_style'       => array(
							'wp_core'      => false,
							'name'         => 'FED Global',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url(
								'/assets/admin/css/fed_global_admin_style.css',
								BC_FED_PLUGIN
							),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
					),
				)
			);

			return $scripts;
		}

		/**
		 * Admin Script Menu Tab.
		 */
		public function fed_admin_script_menu_tab() {
			$this->render_scripts_styles_view( 'admin' );
		}

		/**
		 * Render Modernized Interactive Scripts & Styles Dequeue Control UI.
		 *
		 * @param string $type Context type: 'admin' or 'frontend'.
		 */
		private function render_scripts_styles_view( $type = 'admin' ) {
			$scripts        = $this->admin_scripts_styles( $type );
			$default        = 'admin' === $type ? $this->default_admin_script() : $this->default_frontend_script();
			$is_frontend    = ( 'frontend' === $type );
			$context_title  = $is_frontend ? __( 'Frontend Portal & Pages', 'frontend-dashboard' ) : __( 'WordPress Admin Dashboard', 'frontend-dashboard' );
			$context_slug   = $is_frontend ? 'frontend' : 'admin';
			$ajax_url       = fed_get_ajax_form_action( 'fed_admin_script_menu' );

			$scripts_list   = isset( $default['scripts'] ) ? $default['scripts'] : array();
			$styles_list    = isset( $default['styles'] ) ? $default['styles'] : array();

			$saved_scripts  = isset( $scripts['scripts'] ) ? (array) $scripts['scripts'] : array();
			$saved_styles   = isset( $scripts['styles'] ) ? (array) $scripts['styles'] : array();

			$dequeued_scripts_count = count( $saved_scripts );
			$dequeued_styles_count  = count( $saved_styles );
			$total_dequeued         = $dequeued_scripts_count + $dequeued_styles_count;
			$total_assets           = count( $scripts_list ) + count( $styles_list );
			?>
			<form method="post" class="fed_admin_menu fed_ajax space-y-6" action="<?php echo esc_url( $ajax_url ); ?>">
				<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
				<?php if ( $is_frontend ) : ?>
					<input type="hidden" name="fed_admin_script_type" value="frontend"/>
				<?php endif; ?>

				<?php echo fed_loader(); ?>

				<!-- Information & Caution Callout Banners -->
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<!-- Info Banner -->
					<div class="p-4 sm:p-5 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex items-start gap-3.5">
						<div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm shrink-0 shadow-xs mt-0.5">
							<i class="fas fa-info-circle"></i>
						</div>
						<div class="space-y-1">
							<h4 class="text-xs font-bold text-slate-900 m-0">
								<?php esc_html_e( 'Asset Dequeue Policy', 'frontend-dashboard' ); ?>
							</h4>
							<p class="text-xs text-slate-600 m-0 leading-relaxed">
								<?php
								echo sprintf(
									/* translators: %s: context title */
									esc_html__( 'Select specific scripts or stylesheets to dequeue (disable) on %s. Useful if your theme or active plugins already bundle these libraries.', 'frontend-dashboard' ),
									'<strong class="text-slate-800">' . esc_html( $context_title ) . '</strong>'
								);
								?>
							</p>
						</div>
					</div>

					<!-- Caution Banner -->
					<div class="p-4 sm:p-5 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex items-start gap-3.5">
						<div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm shrink-0 shadow-xs mt-0.5">
							<i class="fas fa-exclamation-triangle"></i>
						</div>
						<div class="space-y-1">
							<h4 class="text-xs font-bold text-amber-950 m-0">
								<?php esc_html_e( 'Caution & Warning', 'frontend-dashboard' ); ?>
							</h4>
							<p class="text-xs text-amber-900/80 m-0 leading-relaxed">
								<?php esc_html_e( 'Do not dequeue core libraries unless you are certain they exist elsewhere. Disabling essential assets (like jQuery, SweetAlert, or Form scripts) may break interactive features.', 'frontend-dashboard' ); ?>
							</p>
						</div>
					</div>
				</div>

				<!-- Live Search & Stats Toolbar -->
				<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
					<div class="relative flex-1 max-w-md">
						<span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400 text-xs">
							<i class="fas fa-search"></i>
						</span>
						<input type="text"
							   placeholder="<?php echo esc_attr( sprintf( __( 'Search %d assets (e.g. jQuery, SweetAlert, Select2)...', 'frontend-dashboard' ), $total_assets ) ); ?>"
							   class="fed-asset-search-input w-full pr-3 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 focus:border-indigo-500 text-xs text-slate-800 placeholder:text-slate-400 transition-all outline-none font-medium"
							   style="padding-left: 38px !important; height: 42px !important;" />
					</div>

					<div class="flex items-center gap-2.5 shrink-0">
						<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200/80">
							<span class="w-2 h-2 rounded-full <?php echo $total_dequeued > 0 ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500'; ?>"></span>
							<span class="fed-dequeued-count font-bold"><?php echo (int) $total_dequeued; ?></span> / <?php echo (int) $total_assets; ?> <?php esc_html_e( 'Dequeued', 'frontend-dashboard' ); ?>
						</span>
					</div>
				</div>

				<!-- Two-Column Grid: Scripts & Styles -->
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
					<!-- Scripts Column -->
					<div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-2xs space-y-4">
						<div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
							<div class="flex items-center gap-3">
								<div class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xs shrink-0">
									<i class="fas fa-code"></i>
								</div>
								<div>
									<h4 class="text-xs font-bold text-slate-900 m-0"><?php esc_html_e( 'JavaScript Scripts', 'frontend-dashboard' ); ?></h4>
									<span class="text-[11px] text-slate-400 font-medium"><?php echo count( $scripts_list ); ?> <?php esc_html_e( 'libraries registered', 'frontend-dashboard' ); ?></span>
								</div>
							</div>
							<span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 font-mono">JS</span>
						</div>

						<div class="space-y-2.5 fed-assets-list">
							<?php
							foreach ( $scripts_list as $key => $script ) :
								$is_checked = in_array( $key, $saved_scripts, true );
								$is_wp_core = ! empty( $script['wp_core'] );
								$origin     = ! empty( $script['plugin_name'] ) ? $script['plugin_name'] : ( $is_wp_core ? 'WordPress Core' : 'Frontend Dashboard' );
								?>
								<label class="fed-asset-item group relative flex items-center justify-between p-3.5 bg-slate-50/60 hover:bg-slate-50 border <?php echo $is_checked ? 'border-amber-300 bg-amber-50/40' : 'border-slate-200/80'; ?> rounded-2xl cursor-pointer transition-all">
									<div class="flex items-center gap-3 min-w-0 pr-3">
										<div class="w-8 h-8 rounded-xl <?php echo $is_checked ? 'bg-amber-100 text-amber-700' : 'bg-white text-slate-500 border border-slate-200/60'; ?> flex items-center justify-center text-xs shrink-0 transition-colors">
											<i class="fab fa-js-square text-sm"></i>
										</div>
										<div class="min-w-0">
											<div class="flex items-center gap-2 flex-wrap">
												<span class="text-xs font-bold text-slate-900 tracking-tight fed-asset-name"><?php echo esc_html( $script['name'] ); ?></span>
												<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white text-slate-500 border border-slate-200/80 font-mono fed-asset-handle"><?php echo esc_html( $key ); ?></span>
											</div>
											<span class="text-[11px] text-slate-400 block mt-0.5 font-medium"><?php echo esc_html( $origin ); ?></span>
										</div>
									</div>

									<div class="shrink-0 flex items-center pl-2">
										<input type="checkbox"
											   name="<?php echo esc_attr( $context_slug ); ?>[scripts][<?php echo esc_attr( $key ); ?>]"
											   value="<?php echo esc_attr( $key ); ?>"
											   <?php checked( $is_checked, true ); ?>
											   class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer transition-all" />
									</div>
								</label>
							<?php endforeach; ?>
						</div>
					</div>

					<!-- Styles Column -->
					<div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-2xs space-y-4">
						<div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
							<div class="flex items-center gap-3">
								<div class="w-8 h-8 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center text-xs shrink-0">
									<i class="fas fa-paint-brush"></i>
								</div>
								<div>
									<h4 class="text-xs font-bold text-slate-900 m-0"><?php esc_html_e( 'CSS Stylesheets', 'frontend-dashboard' ); ?></h4>
									<span class="text-[11px] text-slate-400 font-medium"><?php echo count( $styles_list ); ?> <?php esc_html_e( 'stylesheets registered', 'frontend-dashboard' ); ?></span>
								</div>
							</div>
							<span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 font-mono">CSS</span>
						</div>

						<div class="space-y-2.5 fed-assets-list">
							<?php
							foreach ( $styles_list as $key => $style ) :
								$is_checked = in_array( $key, $saved_styles, true );
								$is_wp_core = ! empty( $style['wp_core'] );
								$origin     = ! empty( $style['plugin_name'] ) ? $style['plugin_name'] : ( $is_wp_core ? 'WordPress Core' : 'Frontend Dashboard' );
								?>
								<label class="fed-asset-item group relative flex items-center justify-between p-3.5 bg-slate-50/60 hover:bg-slate-50 border <?php echo $is_checked ? 'border-amber-300 bg-amber-50/40' : 'border-slate-200/80'; ?> rounded-2xl cursor-pointer transition-all">
									<div class="flex items-center gap-3 min-w-0 pr-3">
										<div class="w-8 h-8 rounded-xl <?php echo $is_checked ? 'bg-amber-100 text-amber-700' : 'bg-white text-slate-500 border border-slate-200/60'; ?> flex items-center justify-center text-xs shrink-0 transition-colors">
											<i class="fab fa-css3-alt text-sm"></i>
										</div>
										<div class="min-w-0">
											<div class="flex items-center gap-2 flex-wrap">
												<span class="text-xs font-bold text-slate-900 tracking-tight fed-asset-name"><?php echo esc_html( $style['name'] ); ?></span>
												<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white text-slate-500 border border-slate-200/80 font-mono fed-asset-handle"><?php echo esc_html( $key ); ?></span>
											</div>
											<span class="text-[11px] text-slate-400 block mt-0.5 font-medium"><?php echo esc_html( $origin ); ?></span>
										</div>
									</div>

									<div class="shrink-0 flex items-center pl-2">
										<input type="checkbox"
											   name="<?php echo esc_attr( $context_slug ); ?>[styles][<?php echo esc_attr( $key ); ?>]"
											   value="<?php echo esc_attr( $key ); ?>"
											   <?php checked( $is_checked, true ); ?>
											   class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer transition-all" />
									</div>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<!-- Bottom Action Bar -->
				<div class="pt-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
					<div class="flex items-center gap-2 text-xs text-slate-500">
						<i class="fas fa-check-square text-indigo-600"></i>
						<span><?php esc_html_e( 'Checked items will be dequeued from page execution.', 'frontend-dashboard' ); ?></span>
					</div>
					<button type="submit" class="fed-btn-primary h-11 inline-flex items-center justify-center gap-2 px-6 rounded-xl font-semibold text-xs tracking-wide shadow-sm transition-all active:scale-95 cursor-pointer">
						<i class="fas fa-save text-xs" style="color: #ffffff !important;"></i>
						<span style="color: #ffffff !important;"><?php esc_html_e( 'Save Asset Preferences', 'frontend-dashboard' ); ?></span>
					</button>
				</div>
			</form>

			<script>
			(function($) {
				$(document).on('input', '.fed-asset-search-input', function() {
					var query = $(this).val().toLowerCase().trim();
					var $form = $(this).closest('form');
					$form.find('.fed-asset-item').each(function() {
						var name   = $(this).find('.fed-asset-name').text().toLowerCase();
						var handle = $(this).find('.fed-asset-handle').text().toLowerCase();
						if (query === '' || name.indexOf(query) > -1 || handle.indexOf(query) > -1) {
							$(this).removeClass('hidden').addClass('flex');
						} else {
							$(this).removeClass('flex').addClass('hidden');
						}
					});
				});

				$(document).on('change', '.fed-asset-item input[type="checkbox"]', function() {
					var $card = $(this).closest('.fed-asset-item');
					var $icon = $card.find('.w-8.h-8');
					if ($(this).is(':checked')) {
						$card.addClass('border-amber-300 bg-amber-50/40').removeClass('border-slate-200/80');
						$icon.addClass('bg-amber-100 text-amber-700').removeClass('bg-white text-slate-500 border border-slate-200/60');
					} else {
						$card.removeClass('border-amber-300 bg-amber-50/40').addClass('border-slate-200/80');
						$icon.removeClass('bg-amber-100 text-amber-700').addClass('bg-white text-slate-500 border border-slate-200/60');
					}

					var $form = $(this).closest('form');
					var count = $form.find('.fed-asset-item input[type="checkbox"]:checked').length;
					$form.find('.fed-dequeued-count').text(count);
				});
			})(jQuery);
			</script>
			<?php
		}

		/**
		 * @param  string $type
		 *
		 * @return mixed|void
		 */
		public function admin_scripts_styles( $type = 'admin' ) {
			$scripts = get_option( 'fed_general_scripts_styles' );

			return isset( $scripts[ $type ] ) ? $scripts[ $type ] : array();

		}

		/**
		 * Default Frontend Script.
		 *
		 * @return mixed|void
		 */
		public function default_frontend_script() {
			return apply_filters(
				'fed_default_frontend_scripts_styles', array(
					'scripts' => array(
						'fed_sweetalert'        => array(
							'wp_core'      => false,
							'name'         => 'SweetAlert',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/sweetalert2.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_fontawesome'       => array(
							'wp_core'      => false,
							'name'         => 'FontAwesome',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/fontawesome.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_fontawesome-shims' => array(
							'wp_core'      => false,
							'name'         => 'FontAwesome Shims',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url(
								'/assets/frontend/js/fontawesome-shims.js',
								BC_FED_PLUGIN
							),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_script'            => array(
							'wp_core'      => false,
							'name'         => 'FED Frontend Script',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/fed_script.js', BC_FED_PLUGIN ),
							'dependencies' => array( 'jquery' ),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_common'            => array(
							'wp_core'      => false,
							'name'         => 'FED Common Script',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/fed_common.js', BC_FED_PLUGIN ),
							'dependencies' => array( 'jquery' ),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_bootstrap_script'  => array(
							'wp_core'      => false,
							'name'         => 'Bootstrap',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/bootstrap.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_jscolor_script'    => array(
							'wp_core'      => false,
							'name'         => 'JSColor',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/jscolor.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_select2_script'    => array(
							'wp_core'      => false,
							'name'         => 'Select2',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/select2.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_flatpickr'         => array(
							'wp_core'      => false,
							'name'         => 'FlatPickr',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/flatpickr.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
						'fed_datatables'        => array(
							'wp_core'      => false,
							'name'         => 'Data Tables',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/js/datatables.js', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => false,
							'in_footer'    => true,
						),
					),
					'styles'  => array(
						'fed_frontend_bootstrap'       => array(
							'wp_core'      => false,
							'name'         => 'Bootstrap',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/bootstrap.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_frontend_sweetalert'      => array(
							'wp_core'      => false,
							'name'         => 'SweetAlert',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/sweetalert2.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_admin_font_awesome'       => array(
							'wp_core'      => false,
							'name'         => 'FontAwesome',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/fontawesome.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_admin_font_awesome-shims' => array(
							'wp_core'      => false,
							'name'         => 'FontAwesomeShims',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url(
								'/assets/frontend/css/fontawesome-shims.css',
								BC_FED_PLUGIN
							),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_flatpikcer'               => array(
							'wp_core'      => false,
							'name'         => 'FlatPikcr',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/flatpickr.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_select2'                  => array(
							'wp_core'      => false,
							'name'         => 'Select 2',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/select2.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_frontend_style'           => array(
							'wp_core'      => false,
							'name'         => 'FED Frontend Style',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/common-style.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_global_admin_style'       => array(
							'wp_core'      => false,
							'name'         => 'FED Global',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url(
								'/assets/admin/css/fed_global_admin_style.css',
								BC_FED_PLUGIN
							),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_frontend_animate'         => array(
							'wp_core'      => false,
							'name'         => 'FED Animate',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/animate.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
						'fed_datatables'               => array(
							'wp_core'      => false,
							'name'         => 'DataTables',
							'plugin_name'  => 'Frontend Dashboard',
							'src'          => plugins_url( '/assets/frontend/css/datatables.css', BC_FED_PLUGIN ),
							'dependencies' => array(),
							'version'      => BC_FED_PLUGIN_VERSION,
							'media'        => 'all',
						),
					),
				)
			);
		}

		/**
		 * Frontend Script Menu Tab.
		 */
		public function fed_frontend_script_menu_tab() {
			$this->render_scripts_styles_view( 'frontend' );
		}

	}

	new FED_Admin_General();
}
