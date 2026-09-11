<?php
/**
 * Modern Standalone Dashboard App Shell (FED 3.0)
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
<div class="bc_fed fed_dashboard_container min-h-screen font-sans antialiased text-slate-800" style="background-color: var(--fed-body-bg, #F8FAFC); color: var(--fed-text-main, #0F172A);">
	<?php echo fed_loader(); ?>
	<?php do_action( 'fed_inside_dashboard_container_top' ); ?>

	<div class="flex flex-col lg:flex-row min-h-screen w-full fed_dashboard_wrapper">

		<!-- Unified Full-Height Left Sidebar (Modeled after Live Preview) -->
		<aside class="w-full lg:w-64 xl:w-72 shrink-0 border-r border-slate-200/80 flex flex-col justify-between fed_dashboard_menus lg:sticky lg:top-0 lg:h-screen lg:max-h-screen transition-colors"
		       style="background-color: var(--fed-sidebar-bg, #FFFFFF); border-color: var(--fed-border, #E2E8F0);">
			
			<div class="p-4 sm:p-5 flex-1 flex flex-col overflow-y-auto">
				<!-- Brand Header in Sidebar -->
				<div class="flex items-center gap-3 pb-4 mb-4 border-b border-slate-100 shrink-0" style="border-color: var(--fed-border, #E2E8F0);">
					<?php
					$uplOptions = get_option( 'fed_admin_settings_upl', [] );
					$logoId     = ! empty( $uplOptions['settings']['fed_upl_website_logo'] ) ? (int) $uplOptions['settings']['fed_upl_website_logo'] : 0;
					$logoUrl    = $logoId ? wp_get_attachment_image_url( $logoId, 'full' ) : '';
					$logoWidth  = ! empty( $uplOptions['settings']['fed_upl_website_logo_width'] ) ? 'max-width: ' . intval( $uplOptions['settings']['fed_upl_website_logo_width'] ) . 'px;' : 'max-width: 180px;';
					$logoHeight = ! empty( $uplOptions['settings']['fed_upl_website_logo_height'] ) ? 'max-height: ' . intval( $uplOptions['settings']['fed_upl_website_logo_height'] ) . 'px;' : 'max-height: 44px;';
					?>
					<?php if ( $logoUrl ) : ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block py-1">
							<img src="<?php echo esc_url( $logoUrl ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="object-contain" style="<?php echo esc_attr( $logoWidth . ' ' . $logoHeight ); ?>" />
						</a>
					<?php else : ?>
						<div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm shadow-xs shrink-0 transition-colors"
						     style="background-color: var(--fed-primary, #4F46E5); color: var(--fed-primary-font, #FFFFFF);">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
						</div>
						<div class="overflow-hidden">
							<span class="font-bold text-sm tracking-tight block truncate" style="color: var(--fed-sidebar-text, #0F172A);">
								<?php echo esc_html( get_bloginfo( 'name' ) ?: 'Dashboard' ); ?>
							</span>
							<span class="text-[10px] font-semibold block uppercase tracking-wider opacity-60" style="color: var(--fed-sidebar-text, #64748B);">
								<?php esc_html_e( 'Frontend Dashboard', 'frontend-dashboard' ); ?>
							</span>
						</div>
					<?php endif; ?>
				</div>

				<!-- Navigation Menu -->
				<div class="fed_menu_items flex-1">
					<nav class="flex flex-col gap-1" id="fed_default_template">
						<?php
						fed_display_dashboard_menu( $menu );
						?>
					</nav>
				</div>
			</div>

			<!-- Compact User Profile in Sidebar Footer -->
			<div class="p-4 sm:p-5 border-t border-slate-100 flex items-center justify-between gap-3 fed_sidebar_user_section shrink-0 transition-colors"
			     style="border-color: var(--fed-border, #E2E8F0);">
				<div class="flex items-center gap-3 overflow-hidden">
					<div class="relative shrink-0">
						<img class="w-9 h-9 rounded-full object-cover ring-2 ring-white/20 shadow-2xs" 
							 src="<?php echo esc_url( get_avatar_url( $currentUser->ID ) ); ?>" 
							 alt="<?php echo esc_attr( $currentUser->display_name ); ?>">
						<span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
					</div>
					<div class="overflow-hidden">
						<div class="text-xs font-bold truncate fed_sidebar_user_name" style="color: var(--fed-sidebar-text, #0F172A);"><?php echo esc_html( $currentUser->display_name ); ?></div>
						<div class="text-[10px] font-medium truncate fed_sidebar_user_email" style="color: var(--fed-sidebar-text, #64748B); opacity: 0.75;"><?php echo esc_html( $primaryRole ); ?></div>
					</div>
				</div>

				<a href="<?php echo esc_url( wp_logout_url( fed_get_logout_redirect_url() ) ); ?>" 
				   title="<?php esc_attr_e( 'Sign out', 'frontend-dashboard' ); ?>"
				   class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-500/10 transition-colors shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
				</a>
			</div>
		</aside>

		<!-- Right Main Content Canvas -->
		<main class="flex-1 min-w-0 flex flex-col fed_dashboard_items transition-colors" style="background-color: var(--fed-body-bg, #F8FAFC);">
			<!-- Top Breadcrumb & Action Row -->
			<div class="px-6 sm:px-8 py-5 border-b border-slate-200/60 flex items-center justify-between gap-4"
			     style="border-color: var(--fed-border, #E2E8F0); background-color: var(--fed-body-bg, #F8FAFC);">
				<div>
					<h1 class="text-base sm:text-lg font-bold tracking-tight capitalize m-0" style="color: var(--fed-text-main, #0F172A);">
						<?php echo esc_html( str_replace( [ '-', '_' ], ' ', $activeSlug ) ); ?>
					</h1>
					<p class="text-xs font-medium m-0 mt-0.5" style="color: var(--fed-sidebar-text, #64748B); opacity: 0.85;">
						<?php esc_html_e( 'Dashboard', 'frontend-dashboard' ); ?> / <span class="capitalize"><?php echo esc_html( str_replace( [ '-', '_' ], ' ', $activeSlug ) ); ?></span>
					</p>
				</div>
			</div>

			<!-- Main Content Canvas Area -->
			<div class="p-6 sm:p-8 flex-1">
				<?php echo fed_show_alert( 'fed_dashboard_top_message' ); ?>

				<?php if ( ! $menu instanceof WP_Error ) {
					do_action( 'fed_dashboard_content_outside_top' );
					do_action( 'fed_dashboard_content_outside_top_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
					?>
					<div class="rounded-3xl border shadow-xs p-6 sm:p-8 min-h-[520px] fed_dashboard_main_card transition-colors"
					     style="background-color: var(--fed-card-bg, #FFFFFF); border-color: var(--fed-border, #E2E8F0);">
						<?php
						do_action( 'fed_dashboard_content_top' );
						do_action( 'fed_dashboard_content_top_' . fed_get_data( 'menu_request.menu_slug', $menu ) );

						$dashboard_container->getDashboardContent( $menu );

						do_action( 'fed_dashboard_content_bottom' );
						do_action( 'fed_dashboard_content_bottom_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
						?>
					</div>
					<?php
					do_action( 'fed_dashboard_content_outside_bottom' );
					do_action( 'fed_dashboard_content_outside_bottom_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
				} ?>

				<?php if ( $menu instanceof WP_Error ) { ?>
					<div class="bg-rose-50 border-l-4 border-rose-500 p-6 rounded-2xl shadow-xs fed_dashboard_wrapper fed_error text-rose-800">
						<?php fed_get_403_error_page(); ?>
					</div>
				<?php } ?>

				<?php do_action( 'fed_inside_dashboard_container_bottom' ); ?>
			</div>
		</main>

	</div>
</div>
<?php
do_action( 'fed_after_dashboard_container' );

