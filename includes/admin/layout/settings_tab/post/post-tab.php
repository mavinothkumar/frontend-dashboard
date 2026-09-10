<?php
/**
 * Post Tab.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Post Options
 */
function fed_admin_post_options_tab() {
	?>
	<div class="p-6 sm:p-8 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50/60 border border-amber-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 my-3">
		<div class="flex items-start gap-4">
			<div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg font-semibold shrink-0 shadow-xs">
				<i class="fas fa-puzzle-piece"></i>
			</div>
			<div class="space-y-1">
				<h4 class="text-sm font-bold text-slate-900 m-0">
					<?php esc_html_e( 'Custom Posts & Taxonomies Add-on Required', 'frontend-dashboard' ); ?>
				</h4>
				<p class="text-xs text-slate-600 m-0 leading-relaxed max-w-2xl">
					<?php esc_html_e( 'To manage custom post types, taxonomies, and permissions from the frontend dashboard, please install and activate the official add-on.', 'frontend-dashboard' ); ?>
				</p>
			</div>
		</div>
		<div class="shrink-0 flex items-center gap-3 w-full sm:w-auto">
			<a href="https://buffercode.com/plugin/frontend-dashboard-custom-post-and-taxonomies" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs active:scale-95 text-center no-underline">
				<i class="fas fa-external-link-alt text-[10px]"></i>
				<span><?php esc_html_e( 'Get Add-on', 'frontend-dashboard' ); ?></span>
			</a>
		</div>
	</div>
	<?php
}


/**
 * Admin Post Options
 *
 * @param  array $fed_admin_options  Admin Options.
 *
 * @return mixed|void
 */
function fed_get_admin_post_options( $fed_admin_options ) {
	return apply_filters(
		'fed_customize_admin_post_options', array(
			'fed_admin_post_settings'    => array(
				'icon'      => 'fa fa-cogs',
				'name'      => __( 'Settings', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_post_settings_tab',
				'arguments' => $fed_admin_options,
			),
			'fed_admin_post_dashboard'   => array(
				'icon'      => 'fa fa-cog',
				'name'      => __( 'Dashboard Settings', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_post_dashboard_tab',
				'arguments' => $fed_admin_options,
			),
			'fed_admin_post_menu'        => array(
				'icon'      => 'fa fa-list',
				'name'      => __( 'Menu', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_post_menu_tab',
				'arguments' => $fed_admin_options,
			),
			'fed_admin_post_permissions' => array(
				'icon'      => 'fa fa-universal-access',
				'name'      => __( 'Permissions', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_post_permissions_tab',
				'arguments' => $fed_admin_options,
			),
		)
	);
}
