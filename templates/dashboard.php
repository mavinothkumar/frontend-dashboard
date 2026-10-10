<?php
/**
 * Modern Standalone Dashboard App Shell (FED 3.0)
 *
 * @package Frontend Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dashboard_container = new \FED\Routes\Dashboard\DashboardRoutes( $_REQUEST );
$menu                = $dashboard_container->setDashboardMenuQuery();
do_action( 'fed_before_dashboard_container' );

$currentUser = wp_get_current_user();
$userRoles   = (array) $currentUser->roles;
$primaryRole = ! empty( $userRoles[0] ) ? ucfirst( $userRoles[0] ) : 'Member';
$activeSlug  = is_array( $menu ) && isset( $menu['menu_request']['menu_slug'] ) ? $menu['menu_request']['menu_slug'] : 'dashboard';

$uplOptions = get_option( 'fed_admin_settings_upl', array() );
$logoId     = ! empty( $uplOptions['settings']['fed_upl_website_logo'] ) ? (int) $uplOptions['settings']['fed_upl_website_logo'] : 0;
$logoUrl    = $logoId ? wp_get_attachment_image_url( $logoId, 'full' ) : '';
$logoWidth  = ! empty( $uplOptions['settings']['fed_upl_website_logo_width'] ) ? 'max-width: ' . intval( $uplOptions['settings']['fed_upl_website_logo_width'] ) . 'px;' : 'max-width: 180px;';
$logoHeight = ! empty( $uplOptions['settings']['fed_upl_website_logo_height'] ) ? 'max-height: ' . intval( $uplOptions['settings']['fed_upl_website_logo_height'] ) . 'px;' : 'max-height: 44px;';
?>
<style id="fed-mobile-nav-styles">
@media (max-width: 1023px) {
	.bc_fed.fed_dashboard_container .fed_dashboard_wrapper {
		display: flex !important;
		flex-direction: column !important;
		min-height: 100vh !important;
		width: 100% !important;
	}
	.bc_fed.fed_dashboard_container aside.fed_dashboard_menus {
		position: fixed !important;
		top: 0 !important;
		left: 0 !important;
		bottom: 0 !important;
		z-index: 999999 !important;
		width: 280px !important;
		max-width: 85vw !important;
		height: 100vh !important;
		height: 100dvh !important;
		min-height: 100vh !important;
		display: flex !important;
		flex-direction: column !important;
		justify-content: space-between !important;
		box-sizing: border-box !important;
		transform: translateX(-100%) !important;
		transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
		box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
	}
	.bc_fed.fed_dashboard_container aside.fed_dashboard_menus.fed_mobile_open,
	body.fed_mobile_nav_open aside.fed_dashboard_menus {
		transform: translateX(0) !important;
	}
	.bc_fed .fed_mobile_backdrop.active,
	body.fed_mobile_nav_open .fed_mobile_backdrop {
		display: block !important;
		opacity: 1 !important;
	}
	.bc_fed.fed_dashboard_container main.fed_dashboard_items {
		width: 100% !important;
		min-width: 0 !important;
		flex: 1 1 auto !important;
	}
}
</style>
<div class="bc_fed fed_dashboard_container min-h-screen font-sans antialiased text-slate-800" style="background-color: var(--fed-body-bg, #F8FAFC); color: var(--fed-text-main, #0F172A);">
	<?php echo fed_loader(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php do_action( 'fed_inside_dashboard_container_top' ); ?>

	<!-- Mobile Top Navigation Bar (Visible on < 1024px) -->
	<header class="lg:hidden fed_mobile_header sticky top-0 z-40 flex items-center justify-between px-4 py-3 border-b shadow-2xs"
		style="background-color: var(--fed-sidebar-bg, #FFFFFF); border-color: var(--fed-border, #E2E8F0);">
		<div class="flex items-center gap-3">
			<button type="button" 
				onclick="window.fedToggleMobileNav(event)"
				class="fed_mobile_nav_toggle p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors focus:outline-none cursor-pointer"
				aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'frontend-dashboard' ); ?>">
				<svg class="w-6 h-6 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
			</button>
			<?php if ( $logoUrl ) : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block py-1">
					<img src="<?php echo esc_url( $logoUrl ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="object-contain" style="<?php echo esc_attr( $logoWidth . ' ' . $logoHeight ); ?>" />
				</a>
			<?php else : ?>
				<div class="flex items-center gap-2">
					<div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shadow-2xs shrink-0"
						style="background-color: var(--fed-primary, #4F46E5); color: var(--fed-primary-font, #FFFFFF);">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
					</div>
					<span class="font-bold text-sm tracking-tight truncate max-w-[160px] sm:max-w-xs" style="color: var(--fed-sidebar-text, #0F172A);">
						<?php echo esc_html( get_bloginfo( 'name' ) ?: 'Dashboard' ); ?>
					</span>
				</div>
			<?php endif; ?>
		</div>
		<div class="flex items-center gap-2">
			<img class="w-8 h-8 rounded-full object-cover ring-2 ring-white shadow-2xs" 
				src="<?php echo esc_url( get_avatar_url( $currentUser->ID ) ); ?>" 
				alt="<?php echo esc_attr( $currentUser->display_name ); ?>">
		</div>
	</header>

	<!-- Mobile Off-Canvas Backdrop -->
	<div class="fed_mobile_backdrop fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs transition-opacity duration-300 hidden lg:hidden" 
		onclick="window.fedCloseMobileNav(event)"
		aria-hidden="true"></div>

	<div class="flex flex-col lg:flex-row min-h-screen w-full fed_dashboard_wrapper relative">

		<!-- Unified Left Sidebar (Sticky on Desktop, Off-Canvas Drawer on Mobile) -->
		<aside class="w-full lg:w-64 xl:w-72 shrink-0 border-r border-slate-200/80 flex flex-col justify-between fed_dashboard_menus transition-colors"
				style="background-color: var(--fed-sidebar-bg, #FFFFFF); border-color: var(--fed-border, #E2E8F0);">
			
			<div class="fed_sidebar_scrollable p-4 sm:p-5 flex-1 flex flex-col" style="flex: 1 1 auto; display: flex; flex-direction: column; overflow-y: auto; min-height: 0; box-sizing: border-box;">
				<!-- Brand Header in Sidebar with Mobile Close Button -->
				<div class="flex items-center justify-between gap-3 pb-4 mb-4 border-b border-slate-100 shrink-0" style="border-color: var(--fed-border, #E2E8F0);">
					<div class="flex items-center gap-3 overflow-hidden flex-1 min-w-0">
						<?php if ( $logoUrl ) : ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block py-1">
								<img src="<?php echo esc_url( $logoUrl ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="object-contain" style="<?php echo esc_attr( $logoWidth . ' ' . $logoHeight ); ?>" />
							</a>
						<?php else : ?>
							<div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm shadow-xs shrink-0 transition-colors"
								style="width: 36px; height: 36px; min-width: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background-color: var(--fed-primary, #4F46E5); color: var(--fed-primary-font, #FFFFFF);">
								<svg style="width: 20px; height: 20px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
							</div>
							<div class="overflow-hidden" style="min-width: 0; flex: 1 1 auto; overflow: hidden;">
								<span class="font-bold text-sm tracking-tight block truncate" style="font-size: 14px; font-weight: 700; color: var(--fed-sidebar-text, #0F172A); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">
									<?php echo esc_html( get_bloginfo( 'name' ) ?: 'Dashboard' ); ?>
								</span>
								<span class="text-[10px] font-semibold block uppercase tracking-wider opacity-60" style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--fed-sidebar-text, #64748B); opacity: 0.6; display: block;">
									<?php esc_html_e( 'Frontend Dashboard', 'frontend-dashboard' ); ?>
								</span>
							</div>
						<?php endif; ?>
					</div>
					<!-- Mobile Close (X) Button -->
					<button type="button" 
						onclick="window.fedCloseMobileNav(event)"
						class="lg:hidden fed_mobile_nav_close p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors focus:outline-none shrink-0 cursor-pointer" 
						aria-label="<?php esc_attr_e( 'Close menu', 'frontend-dashboard' ); ?>">
						<svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
					</button>
				</div>

				<!-- Navigation Menu -->
				<div class="fed_menu_items flex-1" style="flex: 1 1 auto;">
					<?php do_action( 'fed_dashboard_sidebar_before_menu', $currentUser, $menu ); ?>
					<nav class="flex flex-col gap-1" id="fed_default_template" style="display: flex; flex-direction: column; gap: 4px;">
						<?php
						fed_display_dashboard_menu( $menu );
						?>
					</nav>
					<?php do_action( 'fed_dashboard_sidebar_after_menu', $currentUser, $menu ); ?>
				</div>
			</div>

			<!-- Compact User Profile in Sidebar Footer -->
			<div class="fed_sidebar_user_section p-4 sm:p-5 border-t border-slate-100 flex items-center justify-between gap-3 shrink-0 transition-colors"
				style="border-top: 1px solid var(--fed-border, #E2E8F0); background-color: var(--fed-sidebar-bg, #FFFFFF);">
				<div class="fed_sidebar_user_info flex items-center gap-3 overflow-hidden" style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1 1 auto; overflow: hidden;">
					<div class="relative shrink-0" style="position: relative; width: 36px; height: 36px; min-width: 36px; max-width: 36px; flex-shrink: 0;">
						<img class="w-9 h-9 rounded-full object-cover ring-2 ring-white/20 shadow-2xs" 
							style="width: 36px; height: 36px; border-radius: 9999px; object-fit: cover; display: block;"
							src="<?php echo esc_url( get_avatar_url( $currentUser->ID ) ); ?>" 
							alt="<?php echo esc_attr( $currentUser->display_name ); ?>">
						<span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full" style="position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; background-color: #10b981; border: 2px solid #ffffff; border-radius: 9999px; display: block;"></span>
					</div>
					<div class="overflow-hidden" style="min-width: 0; flex: 1 1 auto; overflow: hidden; text-align: left;">
						<div class="text-xs font-bold truncate fed_sidebar_user_name" style="font-size: 13px; font-weight: 700; color: var(--fed-sidebar-text, #0F172A); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.25; display: block;"><?php echo esc_html( $currentUser->display_name ); ?></div>
						<div class="text-[10px] font-medium truncate fed_sidebar_user_email" style="font-size: 11px; font-weight: 500; color: var(--fed-sidebar-text, #64748B); opacity: 0.75; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.25; margin-top: 2px; display: block;"><?php echo esc_html( $primaryRole ); ?></div>
					</div>
				</div>

				<a href="<?php echo esc_url( wp_logout_url( fed_get_logout_redirect_url() ) ); ?>" 
					title="<?php esc_attr_e( 'Sign out', 'frontend-dashboard' ); ?>"
					class="fed_sidebar_logout_btn p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-500/10 transition-colors shrink-0"
					style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; min-width: 32px; max-width: 32px; padding: 6px; border-radius: 8px; color: #94a3b8; flex-shrink: 0; text-decoration: none; margin-left: auto; box-sizing: border-box; transition: all 0.15s ease;">
					<svg style="width: 18px; height: 18px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
				</a>
			</div>
		</aside>

		<!-- Right Main Content Canvas -->
		<main class="flex-1 min-w-0 flex flex-col fed_dashboard_items transition-colors" style="background-color: var(--fed-body-bg, #F8FAFC); flex: 1 1 auto; min-width: 0; display: flex; flex-direction: column;">
			<!-- Top Breadcrumb & Action Row -->
			<div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-slate-200/60 flex items-center justify-between gap-4"
				style="border-color: var(--fed-border, #E2E8F0); background-color: var(--fed-body-bg, #F8FAFC);">
				<div>
					<h1 class="text-base sm:text-lg font-bold tracking-tight capitalize m-0" style="color: var(--fed-text-main, #0F172A);">
						<?php echo esc_html( str_replace( array( '-', '_' ), ' ', $activeSlug ) ); ?>
					</h1>
					<p class="text-xs font-medium m-0 mt-0.5" style="color: var(--fed-sidebar-text, #64748B); opacity: 0.85;">
						<?php esc_html_e( 'Dashboard', 'frontend-dashboard' ); ?> / <span class="capitalize"><?php echo esc_html( str_replace( array( '-', '_' ), ' ', $activeSlug ) ); ?></span>
					</p>
				</div>
				<div class="fed_dashboard_header_actions flex items-center gap-3">
					<?php do_action( 'fed_dashboard_header_actions', $activeSlug, $currentUser ); ?>
				</div>
			</div>

			<!-- Main Content Canvas Area -->
			<div class="p-4 sm:p-6 lg:p-8 flex-1">
				<?php echo fed_show_alert( 'fed_dashboard_top_message' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<?php
				if ( ! $menu instanceof WP_Error ) {
					do_action( 'fed_dashboard_content_outside_top' );
					do_action( 'fed_dashboard_content_outside_top_' . fed_get_data( 'menu_request.menu_slug', $menu ) );
					?>
					<div class="rounded-2xl sm:rounded-3xl border shadow-xs p-4 sm:p-6 lg:p-8 min-h-[520px] fed_dashboard_main_card transition-colors"
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
				}
				?>

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
<script id="fed-mobile-nav-script">
window.fedToggleMobileNav = function(e) {
	if (e) {
		if (typeof e.preventDefault === 'function') e.preventDefault();
		if (typeof e.stopPropagation === 'function') e.stopPropagation();
	}
	var sidebar  = document.querySelector('aside.fed_dashboard_menus');
	var isOpen   = document.body.classList.contains('fed_mobile_nav_open') || (sidebar && sidebar.classList.contains('fed_mobile_open'));

	if (isOpen) {
		window.fedCloseMobileNav();
	} else {
		window.fedOpenMobileNav();
	}
};

window.fedOpenMobileNav = function(e) {
	if (e && typeof e.preventDefault === 'function') e.preventDefault();
	var sidebar  = document.querySelector('aside.fed_dashboard_menus');
	var backdrop = document.querySelector('.fed_mobile_backdrop');
	document.body.classList.add('fed_mobile_nav_open');
	if (sidebar) sidebar.classList.add('fed_mobile_open');
	if (backdrop) {
		backdrop.classList.remove('hidden');
		backdrop.classList.add('active');
	}
	document.body.style.overflow = 'hidden';
};

window.fedCloseMobileNav = function(e) {
	if (e && typeof e.preventDefault === 'function') e.preventDefault();
	var sidebar  = document.querySelector('aside.fed_dashboard_menus');
	var backdrop = document.querySelector('.fed_mobile_backdrop');
	document.body.classList.remove('fed_mobile_nav_open');
	if (sidebar) sidebar.classList.remove('fed_mobile_open');
	if (backdrop) {
		backdrop.classList.remove('active');
		backdrop.classList.add('hidden');
	}
	document.body.style.overflow = '';
};

// Handle Escape key to close
document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape' && document.body.classList.contains('fed_mobile_nav_open')) {
		window.fedCloseMobileNav();
	}
});

// Auto-close drawer on real navigation links (without closing on submenu expanders)
document.addEventListener('click', function(e) {
	if (!document.body.classList.contains('fed_mobile_nav_open')) return;
	var target = e.target.closest('a');
	if (!target) return;
	var sidebar = document.querySelector('aside.fed_dashboard_menus');
	if (sidebar && sidebar.contains(target)) {
		var href = target.getAttribute('href');
		if (href && href !== '#' && href.indexOf('javascript:') !== 0 && !target.classList.contains('fed_menu_slug')) {
			if (window.innerWidth < 1024) {
				window.fedCloseMobileNav();
			}
		}
	}
});
</script>
<?php
do_action( 'fed_after_dashboard_container' );
