<?php
/**
 * Dashboard Page
 *
 * @package frontend-dashboard
 */

$dashboard_container = new \FED\Routes\Dashboard\DashboardRoutes( $_REQUEST );
$menu                = $dashboard_container->setDashboardMenuQuery();
do_action( 'fed_before_dashboard_container' );
$is_mobile = fed_get_menu_mobile_attributes();
?>
	<div class="bc_fed fed_dashboard_container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 font-sans text-gray-800">
		<?php echo fed_loader(); ?>
		<?php do_action( 'fed_inside_dashboard_container_top' ); ?>
		<?php echo fed_show_alert( 'fed_dashboard_top_message' ) ?>
		
		<?php if ( ! $menu instanceof WP_Error ) {
			do_action( 'fed_dashboard_content_outside_top' );
			do_action( 'fed_dashboard_content_outside_top_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
			?>
			<div class="flex flex-col md:flex-row gap-8 fed_dashboard_wrapper">
				
				<!-- Sidebar Area -->
				<div class="w-full md:w-64 flex-shrink-0 fed_dashboard_menus default_template">
					<div class="bg-white shadow rounded-lg overflow-hidden fed_menu_items border border-gray-100">
						
						<!-- Mobile Toggle -->
						<button class="w-full flex items-center justify-between p-4 bg-gray-50 text-gray-700 font-semibold md:hidden <?php echo esc_attr( $is_mobile['d'] ); ?>"
								type="button"
								data-toggle="collapse"
								role="button"
								data-target="#fed_default_template"
								aria-expanded="<?php echo esc_attr( $is_mobile['expand'] ); ?>">
							<span class="flex items-center gap-2">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
								Dashboard Menu
							</span>
							<svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
						</button>
						
						<!-- Menu Items -->
						<div class="fed_frontend_dashboard_menu md:block <?php echo esc_attr( $is_mobile['in'] ? 'block' : 'hidden' ); ?>"
								id="fed_default_template">
							<nav class="flex flex-col py-2">
								<?php
								// Note: fed_display_dashboard_menu() internal HTML might also need Tailwind classes in future steps
								fed_display_dashboard_menu( $menu );
								fed_get_collapse_menu(); 
								?>
							</nav>
						</div>
					</div>
				</div>
				
				<!-- Main Content Area -->
				<div class="flex-1 min-w-0 fed_dashboard_items bg-white shadow rounded-lg p-6 border border-gray-100">
					<?php
					do_action( 'fed_dashboard_content_top' );
					do_action( 'fed_dashboard_content_top_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
					
					$dashboard_container->getDashboardContent( $menu );
					
					do_action( 'fed_dashboard_content_bottom' );
					do_action( 'fed_dashboard_content_bottom_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
					?>
				</div>
			</div>
			<?php
			do_action( 'fed_dashboard_content_outside_bottom' );
			do_action( 'fed_dashboard_content_outside_bottom_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
		}
		
		if ( $menu instanceof WP_Error ) {
			?>
			<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm fed_dashboard_wrapper fed_error">
				<?php fed_get_403_error_page(); ?>
			</div>
			<?php
		} ?>
		
		<?php do_action( 'fed_inside_dashboard_container_bottom' ); ?>
	</div>
<?php
do_action( 'fed_after_dashboard_container' );
