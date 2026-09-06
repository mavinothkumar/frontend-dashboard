<?php
/**
 * Modern Standalone Dashboard App Shell
 *
 * @package Frontend Dashboard
 */

$dashboard_container = new \FED\Routes\Dashboard\DashboardRoutes( $_REQUEST );
$menu                = $dashboard_container->setDashboardMenuQuery();
do_action( 'fed_before_dashboard_container' );

$currentUser = wp_get_current_user();
$userRoles   = (array) $currentUser->roles;
$primaryRole = ! empty( $userRoles[0] ) ? ucfirst( $userRoles[0] ) : 'Member';
$activeSlug  = is_array( $menu ) && isset( $menu['menu_request']['menu_slug'] ) ? $menu['menu_request']['menu_slug'] : 'dashboard';
?>
<div class="bc_fed fed_dashboard_container min-h-screen bg-slate-50/60 font-sans text-slate-800 antialiased">
	<?php echo fed_loader(); ?>
	<?php do_action( 'fed_inside_dashboard_container_top' ); ?>

	<!-- Top App Shell Header -->
	<header class="sticky top-0 z-30 bg-white border-b border-slate-200/80 shadow-xs backdrop-blur-md bg-white/95 fed_dashboard_top_header">
		<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10">
			<div class="flex items-center justify-between h-16">
				
				<!-- Left: Brand / Section Title -->
				<div class="flex items-center gap-4">
					<div class="flex items-center gap-2.5">
						<div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white shadow-sm shadow-indigo-500/20">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
						</div>
						<span class="font-bold text-slate-900 text-lg tracking-tight hidden sm:inline">Dashboard</span>
					</div>

					<div class="hidden md:flex items-center text-sm text-slate-400">
						<span class="mx-2">/</span>
						<span class="text-slate-600 font-medium capitalize"><?php echo esc_html( str_replace( [ '-', '_' ], ' ', $activeSlug ) ); ?></span>
					</div>
				</div>

				<!-- Right: User Profile & Actions -->
				<div class="flex items-center gap-3">
					<div class="flex items-center gap-3 pl-3 border-l border-slate-200">
						<img class="w-9 h-9 rounded-full object-cover ring-2 ring-indigo-50 shadow-xs" 
							 src="<?php echo esc_url( get_avatar_url( $currentUser->ID ) ); ?>" 
							 alt="<?php echo esc_attr( $currentUser->display_name ); ?>">
						
						<div class="hidden sm:block text-left">
							<div class="text-sm font-semibold text-slate-900 leading-tight"><?php echo esc_html( $currentUser->display_name ); ?></div>
							<div class="text-xs text-slate-500 font-medium"><?php echo esc_html( $primaryRole ); ?></div>
						</div>

						<a href="<?php echo esc_url( wp_logout_url( fed_get_logout_redirect_url() ) ); ?>" 
						   title="<?php esc_attr_e( 'Sign out', 'frontend-dashboard' ); ?>"
						   class="ml-1 p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
						</a>
					</div>
				</div>

			</div>
		</div>
	</header>

	<!-- Main App Shell Body -->
	<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-6">
		<?php echo fed_show_alert( 'fed_dashboard_top_message' ); ?>

		<?php if ( ! $menu instanceof WP_Error ) {
			do_action( 'fed_dashboard_content_outside_top' );
			do_action( 'fed_dashboard_content_outside_top_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
			?>
			<div class="flex flex-col lg:flex-row gap-6 xl:gap-8 fed_dashboard_wrapper w-full">
				
				<!-- Sidebar Navigation -->
				<aside class="w-full lg:w-72 flex-shrink-0 fed_dashboard_menus">
					
					<!-- User Card -->
					<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 mb-4 text-center">
						<div class="relative inline-block mx-auto mb-3">
							<img class="w-16 h-16 rounded-2xl object-cover ring-4 ring-indigo-50 mx-auto shadow-xs" 
								 src="<?php echo esc_url( get_avatar_url( $currentUser->ID ) ); ?>" 
								 alt="<?php echo esc_attr( $currentUser->display_name ); ?>">
							<span class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></span>
						</div>
						<h3 class="font-bold text-slate-900 text-base leading-tight mb-0.5"><?php echo esc_html( $currentUser->display_name ); ?></h3>
						<p class="text-xs text-slate-500 font-medium mb-3"><?php echo esc_html( $currentUser->user_email ); ?></p>
						<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
							<?php echo esc_html( $primaryRole ); ?>
						</span>
					</div>

					<!-- Navigation Menu -->
					<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3 fed_menu_items">
						<div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
							<?php esc_html_e( 'Navigation', 'frontend-dashboard' ); ?>
						</div>
						<nav class="flex flex-col gap-0.5" id="fed_default_template">
							<?php
							fed_display_dashboard_menu( $menu );
							fed_get_collapse_menu();
							?>
						</nav>
					</div>

				</aside>

				<!-- Main Dashboard Content Container -->
				<main class="flex-1 min-w-0 fed_dashboard_items">
					<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 min-h-[580px]">
						<?php
						do_action( 'fed_dashboard_content_top' );
						do_action( 'fed_dashboard_content_top_' . fed_get_data( 'menu_request.menu_slug', $menu ) );

						$dashboard_container->getDashboardContent( $menu );

						do_action( 'fed_dashboard_content_bottom' );
						do_action( 'fed_dashboard_content_bottom_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
						?>
					</div>
				</main>

			</div>
			<?php
			do_action( 'fed_dashboard_content_outside_bottom' );
			do_action( 'fed_dashboard_content_outside_bottom_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
		}

		if ( $menu instanceof WP_Error ) {
			?>
			<div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-2xl shadow-xs fed_dashboard_wrapper fed_error text-red-800">
				<?php fed_get_403_error_page(); ?>
			</div>
			<?php
		} ?>

		<?php do_action( 'fed_inside_dashboard_container_bottom' ); ?>
	</div>
</div>
<?php
do_action( 'fed_after_dashboard_container' );
