<?php
/**
 * Add-Ons & Extensions Marketplace
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Built-in Catalog of Frontend Dashboard Add-ons
 *
 * @return array
 */
function fed_get_addons_catalog() {
	return array(
		'frontend-dashboard-custom-post'    => array(
			'slug'          => 'frontend-dashboard-custom-post',
			'file'          => 'frontend-dashboard-custom-post/frontend-dashboard-custom-post.php',
			'title'         => __( 'Custom Post & Taxonomies', 'frontend-dashboard' ),
			'version'       => '3.0',
			'category'      => 'core',
			'category_name' => __( 'Core & Posts', 'frontend-dashboard' ),
			'description'   => __( 'Empower users to submit, edit, and manage custom post types (Products, Portfolios, Listings, Articles) and custom taxonomies directly from frontend with role permissions.', 'frontend-dashboard' ),
			'tags'          => array( 'Custom Posts', 'Taxonomies', 'Frontend Submissions', 'Field Builder' ),
			'icon'          => 'fas fa-layer-group',
			'icon_bg'       => 'bg-gradient-to-br from-indigo-500 to-blue-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_custom_post' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard-custom-post-and-taxonomies',
			'is_pro'        => false,
		),
		'frontend-dashboard-captcha'        => array(
			'slug'          => 'frontend-dashboard-captcha',
			'file'          => 'frontend-dashboard-captcha/frontend-dashboard-captcha.php',
			'title'         => __( 'Captcha & Anti-Spam Security', 'frontend-dashboard' ),
			'version'       => '3.0.0',
			'category'      => 'security',
			'category_name' => __( 'Security & Auth', 'frontend-dashboard' ),
			'description'   => __( 'Protect your login, register, and password-reset forms against spam bots with Google reCAPTCHA v2/v3, Cloudflare Turnstile, and Math Captcha challenges.', 'frontend-dashboard' ),
			'tags'          => array( 'reCAPTCHA v2/v3', 'Cloudflare Turnstile', 'Math Captcha', 'Anti-Spam' ),
			'icon'          => 'fas fa-shield-alt',
			'icon_bg'       => 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_settings_login' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard-captcha',
			'is_pro'        => false,
		),
		'frontend-dashboard-social-connect' => array(
			'slug'          => 'frontend-dashboard-social-connect',
			'file'          => 'frontend-dashboard-social-connect/frontend-dashboard-social-connect.php',
			'title'         => __( 'Social Connect & 1-Click Login', 'frontend-dashboard' ),
			'version'       => '1.5',
			'category'      => 'security',
			'category_name' => __( 'Security & Auth', 'frontend-dashboard' ),
			'description'   => __( 'Enable seamless 1-click social logins and registrations with 20+ major providers including Google, Facebook, Twitter (X), LinkedIn, GitHub, Apple, and Discord.', 'frontend-dashboard' ),
			'tags'          => array( 'OAuth 2.0', 'Google Login', 'Facebook Login', '1-Click Sign-in' ),
			'icon'          => 'fas fa-users',
			'icon_bg'       => 'bg-gradient-to-br from-cyan-500 to-blue-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_settings_login' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard-social-connect',
			'is_pro'        => false,
		),
		'frontend-dashboard-user-management' => array(
			'slug'          => 'frontend-dashboard-user-management',
			'file'          => 'frontend-dashboard-user-management/frontend-dashboard-user-management.php',
			'title'         => __( 'Frontend User Management', 'frontend-dashboard' ),
			'version'       => '1.0',
			'category'      => 'user',
			'category_name' => __( 'User Management', 'frontend-dashboard' ),
			'description'   => __( 'Allow team managers and admins to search, filter, view, edit, approve, or delete user accounts directly on the frontend dashboard without wp-admin access.', 'frontend-dashboard' ),
			'tags'          => array( 'User Table', 'Role Filter', 'Bulk Actions', 'Manager Dashboard' ),
			'icon'          => 'fas fa-user-shield',
			'icon_bg'       => 'bg-gradient-to-br from-violet-500 to-purple-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_user_profile' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard-user-management',
			'is_pro'        => false,
		),
		'frontend-dashboard-templates'      => array(
			'slug'          => 'frontend-dashboard-templates',
			'file'          => 'frontend-dashboard-templates/frontend-dashboard-templates.php',
			'title'         => __( 'Custom Layouts & Themes', 'frontend-dashboard' ),
			'version'       => '1.8',
			'category'      => 'templates',
			'category_name' => __( 'UI & Templates', 'frontend-dashboard' ),
			'description'   => __( 'Upgrade your user area with pre-built dashboard layouts, customizable sidebars, modern login/register landing themes, and sleek card grid layouts.', 'frontend-dashboard' ),
			'tags'          => array( 'Layout Engine', 'Sidebar Variations', 'Login Themes', 'Responsive UI' ),
			'icon'          => 'fas fa-palette',
			'icon_bg'       => 'bg-gradient-to-br from-pink-500 to-rose-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_dashboard_menu' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard',
			'is_pro'        => false,
		),
		'frontend-dashboard-social-chat'    => array(
			'slug'          => 'frontend-dashboard-social-chat',
			'file'          => 'frontend-dashboard-social-chat/frontend-dashboard-social-chat.php',
			'title'         => __( 'Live Chat & Support Helpdesk', 'frontend-dashboard' ),
			'version'       => '1.3',
			'category'      => 'communication',
			'category_name' => __( 'Communication', 'frontend-dashboard' ),
			'description'   => __( 'Embed floating live chat widgets including WhatsApp direct message, Telegram channel/bot triggers, and live support chat right inside your dashboard.', 'frontend-dashboard' ),
			'tags'          => array( 'WhatsApp Support', 'Telegram Widget', 'Floating Chat', 'Member Helpdesk' ),
			'icon'          => 'fas fa-comments',
			'icon_bg'       => 'bg-gradient-to-br from-emerald-500 to-green-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_dashboard_menu' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard-social-chat',
			'is_pro'        => false,
		),
		'frontend-dashboard-message'        => array(
			'slug'          => 'frontend-dashboard-message',
			'file'          => 'frontend-dashboard-message/frontend-dashboard-notification.php',
			'title'         => __( 'Private Messaging & Inbox', 'frontend-dashboard' ),
			'version'       => '1.0',
			'category'      => 'communication',
			'category_name' => __( 'Communication', 'frontend-dashboard' ),
			'description'   => __( 'Full peer-to-peer and admin-to-user private messaging system featuring threaded conversations, attachments, unread counters, and email triggers.', 'frontend-dashboard' ),
			'tags'          => array( 'Inbox & Sent', 'Conversation Threads', 'File Attachments', 'Email Alerts' ),
			'icon'          => 'fas fa-envelope-open-text',
			'icon_bg'       => 'bg-gradient-to-br from-sky-500 to-blue-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_dashboard_menu' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard-message',
			'is_pro'        => false,
		),
		'frontend-dashboard-notification'   => array(
			'slug'          => 'frontend-dashboard-notification',
			'file'          => 'frontend-dashboard-notification/frontend-dashboard-notification.php',
			'title'         => __( 'Realtime Notifications & Alerts', 'frontend-dashboard' ),
			'version'       => '1.1',
			'category'      => 'communication',
			'category_name' => __( 'Communication', 'frontend-dashboard' ),
			'description'   => __( 'Deliver instant in-app alerts, bell dropdown notifications, and toast alerts for user events, submission status changes, and announcements.', 'frontend-dashboard' ),
			'tags'          => array( 'Notification Bell', 'Toast Popups', 'Broadcast Alerts', 'Trigger Events' ),
			'icon'          => 'fas fa-bell',
			'icon_bg'       => 'bg-gradient-to-br from-amber-500 to-orange-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_dashboard_menu' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard-notification',
			'is_pro'        => false,
		),
		'frontend-dashboard-pages'          => array(
			'slug'          => 'frontend-dashboard-pages',
			'file'          => 'frontend-dashboard-pages/frontend-dashboard-pages.php',
			'title'         => __( 'Custom Pages & Tab Builder', 'frontend-dashboard' ),
			'version'       => '1.5.5',
			'category'      => 'core',
			'category_name' => __( 'Core & Posts', 'frontend-dashboard' ),
			'description'   => __( 'Effortlessly map any standard WordPress page, custom template, or shortcode-powered content directly into custom tabs inside user dashboard navigation.', 'frontend-dashboard' ),
			'tags'          => array( 'Page Mapping', 'Shortcode Embeds', 'Custom Tabs', 'Access Control' ),
			'icon'          => 'fas fa-file-alt',
			'icon_bg'       => 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_dashboard_menu' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard-pages',
			'is_pro'        => false,
		),
		'frontend-dashboard-extra'          => array(
			'slug'          => 'frontend-dashboard-extra',
			'file'          => 'frontend-dashboard-extra/frontend-dashboard-extra.php',
			'title'         => __( 'Extra Tools & Widgets Pack', 'frontend-dashboard' ),
			'version'       => '3.0',
			'category'      => 'core',
			'category_name' => __( 'Core & Posts', 'frontend-dashboard' ),
			'description'   => __( 'Extend your member dashboard with extra profile widgets, user count badges, quick actions, role-based shortcodes, and utility helper blocks.', 'frontend-dashboard' ),
			'tags'          => array( 'Profile Widgets', 'Statistics Badges', 'Role Shortcuts', 'Helper Tools' ),
			'icon'          => 'fas fa-puzzle-piece',
			'icon_bg'       => 'bg-gradient-to-br from-teal-500 to-emerald-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_user_profile' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard-extra',
			'is_pro'        => false,
		),
		'frontend-dashboard-payment'        => array(
			'slug'          => 'frontend-dashboard-payment',
			'file'          => 'frontend-dashboard-payment/frontend-dashboard-payment.php',
			'title'         => __( 'Payments & Billing Pro', 'frontend-dashboard' ),
			'version'       => '3.0',
			'category'      => 'monetization',
			'category_name' => __( 'Monetization', 'frontend-dashboard' ),
			'description'   => __( 'Monetize post submissions, paid membership tiers, and user subscriptions with integrated PayPal, Stripe, PDF invoices, and automated billing.', 'frontend-dashboard' ),
			'tags'          => array( 'Stripe Checkout', 'PayPal Express', 'PDF Invoices', 'Pay-per-Post' ),
			'icon'          => 'fas fa-credit-card',
			'icon_bg'       => 'bg-gradient-to-br from-amber-500 to-yellow-600 text-white',
			'settings_url'  => admin_url( 'admin.php?page=fed_payment' ),
			'docs_url'      => 'https://buffercode.com/plugin/frontend-dashboard',
			'is_pro'        => true,
			'purchase_url'  => 'https://buffercode.com/plugin/frontend-dashboard',
		),
	);
}

/**
 * Get Plugin Pages Menu / Add-ons Marketplace Page
 */
function fed_get_plugin_pages_menu() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$catalog = fed_get_addons_catalog();

	// Calculate live statistics
	$total_addons     = count( $catalog );
	$active_count     = 0;
	$installed_count  = 0;
	$pro_count        = 0;

	foreach ( $catalog as $slug => &$item ) {
		$file = $item['file'];
		$item['is_installed'] = file_exists( WP_PLUGIN_DIR . '/' . $file );
		$item['is_active']    = $item['is_installed'] && is_plugin_active( $file );

		if ( $item['is_active'] ) {
			$active_count++;
		}
		if ( $item['is_installed'] ) {
			$installed_count++;
		}
		if ( ! empty( $item['is_pro'] ) ) {
			$pro_count++;
		}
	}
	unset( $item );

	$available_count = $total_addons - $active_count;
	$nonce = wp_create_nonce( 'fed_nonce' );
	?>
	<style>
		.fed-addons-wrap {
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
			color: #1e293b;
			width: 100% !important;
			box-sizing: border-box !important;
		}
		.fed-addons-wrap .fed-search-input-wrap {
			position: relative !important;
			display: flex !important;
			align-items: center !important;
		}
		.fed-addons-wrap .fed-search-input-wrap .fed-search-icon {
			position: absolute !important;
			left: 14px !important;
			top: 50% !important;
			transform: translateY(-50%) !important;
			pointer-events: none !important;
			color: #94a3b8 !important;
			font-size: 14px !important;
			z-index: 2 !important;
		}
		.fed-addons-wrap .fed-search-input-wrap input.fed-search-input {
			padding-left: 42px !important;
			padding-right: 36px !important;
			height: 42px !important;
			line-height: 42px !important;
			box-sizing: border-box !important;
		}
		.fed-main-tab-btn {
			transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
		}
		.fed-main-tab-btn.fed-tab-active {
			background: #2563eb !important;
			color: #ffffff !important;
			box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.25), 0 2px 4px -2px rgba(37, 99, 235, 0.25);
		}
		.fed-main-tab-btn.fed-tab-active .fed-tab-count {
			background: rgba(255, 255, 255, 0.25) !important;
			color: #ffffff !important;
		}
		.fed-addon-card {
			transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
		}
		.fed-addon-card:hover {
			transform: translateY(-3px);
			box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08), 0 6px 12px -4px rgba(0, 0, 0, 0.04);
		}
		.fed-status-modal {
			position: fixed;
			inset: 0;
			background: rgba(15, 23, 42, 0.6);
			backdrop-filter: blur(4px);
			display: flex;
			align-items: center;
			justify-content: center;
			z-index: 99999;
			opacity: 0;
			pointer-events: none;
			transition: opacity 0.2s ease-in-out;
		}
		.fed-status-modal.show {
			opacity: 1;
			pointer-events: auto;
		}
		#fed_addons_global_loader {
			position: fixed;
			inset: 0;
			background: rgba(15, 23, 42, 0.7);
			backdrop-filter: blur(4px);
			display: flex;
			align-items: center;
			justify-content: center;
			z-index: 100000;
			opacity: 0;
			pointer-events: none;
			transition: opacity 0.2s ease-in-out;
		}
		#fed_addons_global_loader.show {
			opacity: 1;
			pointer-events: auto;
		}
	</style>

	<div class="fed-addons-wrap w-full pr-6 py-6">
		
		<!-- Top Notification Banner -->
		<div id="fed_addons_alert" class="hidden mb-6 rounded-xl p-4 text-sm font-medium border flex items-center justify-between shadow-sm">
			<div class="flex items-center space-x-3">
				<i id="fed_addons_alert_icon" class="fas fa-info-circle text-lg"></i>
				<span id="fed_addons_alert_msg"></span>
			</div>
			<button type="button" onclick="document.getElementById('fed_addons_alert').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 focus:outline-none">
				<i class="fas fa-times"></i>
			</button>
		</div>

		<!-- Executive Header -->
		<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8 mb-8">
			<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 border-b border-slate-100">
				<div>
					<div class="flex items-center space-x-3">
						<div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
							<i class="fas fa-puzzle-piece text-2xl"></i>
						</div>
						<div>
							<h1 class="text-2xl font-bold text-slate-900 tracking-tight">
								<?php esc_html_e( 'Add-Ons & Extensions Marketplace', 'frontend-dashboard' ); ?>
							</h1>
							<p class="text-sm text-slate-500 mt-0.5">
								<?php esc_html_e( 'Power up Frontend Dashboard with official modular extensions, auth integrations, and monetization tools.', 'frontend-dashboard' ); ?>
							</p>
						</div>
					</div>
				</div>
				<div class="flex items-center flex-wrap gap-3">
					<button type="button" id="fed_btn_refresh_catalog" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition-all focus:outline-none shadow-sm">
						<i class="fas fa-sync-alt mr-2 text-slate-500"></i>
						<?php esc_html_e( 'Refresh Catalog', 'frontend-dashboard' ); ?>
					</button>
					<a href="https://demo.frontenddashboard.com" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold rounded-xl text-sm transition-all focus:outline-none shadow-sm border border-blue-200/60">
						<i class="fas fa-external-link-alt mr-2"></i>
						<?php esc_html_e( 'Live Demo', 'frontend-dashboard' ); ?>
					</a>
				</div>
			</div>

			<!-- KPI Cards -->
			<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
				<!-- Total Catalog -->
				<div class="bg-slate-50/80 rounded-xl p-4 border border-slate-100">
					<div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
						<?php esc_html_e( 'Total Extensions', 'frontend-dashboard' ); ?>
					</div>
					<div class="text-2xl font-extrabold text-slate-800 mt-1 flex items-baseline">
						<?php echo esc_html( $total_addons ); ?>
						<span class="ml-2 text-xs font-medium text-slate-400"><?php esc_html_e( 'Available', 'frontend-dashboard' ); ?></span>
					</div>
				</div>
				<!-- Active on Site -->
				<div class="bg-emerald-50/80 rounded-xl p-4 border border-emerald-100">
					<div class="text-xs font-semibold uppercase tracking-wider text-emerald-700">
						<?php esc_html_e( 'Active Extensions', 'frontend-dashboard' ); ?>
					</div>
					<div class="text-2xl font-extrabold text-emerald-700 mt-1 flex items-baseline">
						<?php echo esc_html( $active_count ); ?>
						<span class="ml-2 text-xs font-medium text-emerald-600"><?php esc_html_e( 'Running', 'frontend-dashboard' ); ?></span>
					</div>
				</div>
				<!-- Installed / Inactive -->
				<div class="bg-blue-50/80 rounded-xl p-4 border border-blue-100">
					<div class="text-xs font-semibold uppercase tracking-wider text-blue-700">
						<?php esc_html_e( 'Installed Total', 'frontend-dashboard' ); ?>
					</div>
					<div class="text-2xl font-extrabold text-blue-700 mt-1 flex items-baseline">
						<?php echo esc_html( $installed_count ); ?>
						<span class="ml-2 text-xs font-medium text-blue-600"><?php esc_html_e( 'On Server', 'frontend-dashboard' ); ?></span>
					</div>
				</div>
				<!-- Pro & Premium -->
				<div class="bg-purple-50/80 rounded-xl p-4 border border-purple-100">
					<div class="text-xs font-semibold uppercase tracking-wider text-purple-700">
						<?php esc_html_e( 'Pro & Monetization', 'frontend-dashboard' ); ?>
					</div>
					<div class="text-2xl font-extrabold text-purple-700 mt-1 flex items-baseline">
						<?php echo esc_html( $pro_count ); ?>
						<span class="ml-2 text-xs font-medium text-purple-600"><?php esc_html_e( 'Integrations', 'frontend-dashboard' ); ?></span>
					</div>
				</div>
			</div>
		</div>

		<!-- Category Tabs and Search Bar -->
		<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 mb-8">
			<div class="flex flex-col 2xl:flex-row 2xl:items-center 2xl:justify-between gap-4">
				<!-- Category Filter Buttons -->
				<div class="flex items-center flex-wrap gap-2.5" id="fed_addons_category_nav">
					<button type="button" data-category="all" class="fed-main-tab-btn fed-tab-active inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all focus:outline-none">
						<i class="fas fa-th-large mr-2 text-xs"></i>
						<?php esc_html_e( 'All', 'frontend-dashboard' ); ?>
						<span class="fed-tab-count ml-2 px-2 py-0.5 rounded-full text-xs bg-slate-200 text-slate-700 font-bold"><?php echo esc_html( $total_addons ); ?></span>
					</button>
					<button type="button" data-category="core" class="fed-main-tab-btn inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all focus:outline-none">
						<i class="fas fa-layer-group mr-2 text-xs"></i>
						<?php esc_html_e( 'Core & Posts', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" data-category="security" class="fed-main-tab-btn inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all focus:outline-none">
						<i class="fas fa-shield-alt mr-2 text-xs"></i>
						<?php esc_html_e( 'Security & Auth', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" data-category="user" class="fed-main-tab-btn inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all focus:outline-none">
						<i class="fas fa-user-shield mr-2 text-xs"></i>
						<?php esc_html_e( 'User Management', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" data-category="communication" class="fed-main-tab-btn inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all focus:outline-none">
						<i class="fas fa-comments mr-2 text-xs"></i>
						<?php esc_html_e( 'Communication', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" data-category="templates" class="fed-main-tab-btn inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all focus:outline-none">
						<i class="fas fa-palette mr-2 text-xs"></i>
						<?php esc_html_e( 'UI & Templates', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" data-category="monetization" class="fed-main-tab-btn inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all focus:outline-none">
						<i class="fas fa-credit-card mr-2 text-xs"></i>
						<?php esc_html_e( 'Monetization', 'frontend-dashboard' ); ?>
					</button>
				</div>

				<!-- Search and Status Filters -->
				<div class="flex items-center flex-shrink-0 space-x-3 w-full 2xl:w-auto">
					<div class="fed-search-input-wrap relative flex-1 2xl:w-72">
						<i class="fas fa-search fed-search-icon"></i>
						<input type="text" id="fed_addons_search_input" placeholder="<?php esc_attr_e( 'Search add-ons...', 'frontend-dashboard' ); ?>" class="fed-search-input w-full bg-slate-50 hover:bg-slate-100/80 focus:bg-white text-slate-800 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" />
						<button type="button" id="fed_addons_search_clear" class="hidden absolute right-3 top-1/2 -translate-y-1/2 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
							<i class="fas fa-times-circle text-sm"></i>
						</button>
					</div>

					<select id="fed_addons_status_filter" class="h-[42px] py-2 px-3.5 bg-slate-50 hover:bg-slate-100/80 focus:bg-white text-slate-700 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none font-medium">
						<option value="all"><?php esc_html_e( 'All Status', 'frontend-dashboard' ); ?></option>
						<option value="active"><?php esc_html_e( 'Active Only', 'frontend-dashboard' ); ?></option>
						<option value="inactive"><?php esc_html_e( 'Inactive Only', 'frontend-dashboard' ); ?></option>
						<option value="installed"><?php esc_html_e( 'Installed Only', 'frontend-dashboard' ); ?></option>
						<option value="pro"><?php esc_html_e( 'Pro / Premium', 'frontend-dashboard' ); ?></option>
					</select>
				</div>
			</div>
		</div>

		<!-- Addons Grid -->
		<div id="fed_addons_grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
			<?php foreach ( $catalog as $slug => $addon ) : ?>
				<?php
				$is_active    = $addon['is_active'];
				$is_installed = $addon['is_installed'];
				$is_pro       = ! empty( $addon['is_pro'] );

				$status_attr = 'available';
				if ( $is_active ) {
					$status_attr = 'active';
				} elseif ( $is_installed ) {
					$status_attr = 'inactive';
				}
				if ( $is_pro ) {
					$status_attr .= ' pro';
				}
				?>
				<div class="fed-addon-card bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between overflow-hidden"
					data-category="<?php echo esc_attr( $addon['category'] ); ?>"
					data-status="<?php echo esc_attr( $status_attr ); ?>"
					data-installed="<?php echo $is_installed ? 'true' : 'false'; ?>"
					data-active="<?php echo $is_active ? 'true' : 'false'; ?>"
					data-pro="<?php echo $is_pro ? 'true' : 'false'; ?>"
					data-title="<?php echo esc_attr( strtolower( $addon['title'] ) ); ?>"
					data-desc="<?php echo esc_attr( strtolower( $addon['description'] ) ); ?>"
					data-tags="<?php echo esc_attr( strtolower( implode( ' ', $addon['tags'] ) ) ); ?>">
					
					<div class="p-6">
						<!-- Card Top -->
						<div class="flex items-start justify-between gap-4 mb-4">
							<div class="flex items-center space-x-3.5">
								<div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shadow-sm <?php echo esc_attr( $addon['icon_bg'] ); ?>">
									<i class="<?php echo esc_attr( $addon['icon'] ); ?>"></i>
								</div>
								<div>
									<h3 class="font-bold text-slate-900 text-base leading-snug">
										<?php echo esc_html( $addon['title'] ); ?>
									</h3>
									<div class="flex items-center space-x-2 mt-1">
										<span class="text-xs font-semibold text-slate-400">
											v<?php echo esc_html( $addon['version'] ); ?>
										</span>
										<span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span>
										<span class="text-xs font-medium text-slate-500">
											<?php echo esc_html( $addon['category_name'] ); ?>
										</span>
									</div>
								</div>
							</div>

							<!-- Status Pill -->
							<div>
								<?php if ( $is_active ) : ?>
									<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
										<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1.5"></span>
										<?php esc_html_e( 'Active', 'frontend-dashboard' ); ?>
									</span>
								<?php elseif ( $is_installed ) : ?>
									<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
										<?php esc_html_e( 'Inactive', 'frontend-dashboard' ); ?>
									</span>
								<?php elseif ( $is_pro ) : ?>
									<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
										<i class="fas fa-crown text-[10px] mr-1 text-purple-500"></i>
										<?php esc_html_e( 'Pro Extension', 'frontend-dashboard' ); ?>
									</span>
								<?php else : ?>
									<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
										<?php esc_html_e( 'Available', 'frontend-dashboard' ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>

						<!-- Description -->
						<p class="text-slate-600 text-sm leading-relaxed mb-4">
							<?php echo esc_html( $addon['description'] ); ?>
						</p>

						<!-- Feature Tags -->
						<div class="flex flex-wrap gap-1.5 mb-2">
							<?php foreach ( $addon['tags'] as $tag ) : ?>
								<span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600">
									<?php echo esc_html( $tag ); ?>
								</span>
							<?php endforeach; ?>
						</div>
					</div>

					<!-- Card Footer / Actions -->
					<div class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between gap-3">
						<div>
							<?php if ( ! empty( $addon['docs_url'] ) ) : ?>
								<a href="<?php echo esc_url( $addon['docs_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors inline-flex items-center">
									<i class="fas fa-book-open mr-1.5 text-slate-400"></i>
									<?php esc_html_e( 'Docs', 'frontend-dashboard' ); ?>
								</a>
							<?php endif; ?>
						</div>

						<div class="flex items-center space-x-2">
							<?php if ( $is_active ) : ?>
								<?php if ( ! empty( $addon['settings_url'] ) ) : ?>
									<a href="<?php echo esc_url( $addon['settings_url'] ); ?>" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
										<i class="fas fa-sliders-h mr-1.5"></i>
										<?php esc_html_e( 'Configure', 'frontend-dashboard' ); ?>
									</a>
								<?php endif; ?>
								<button type="button" class="fed-btn-deactivate inline-flex items-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg transition-all border border-rose-200"
									data-plugin="<?php echo esc_attr( $addon['file'] ); ?>"
									data-title="<?php echo esc_attr( $addon['title'] ); ?>">
									<i class="fas fa-power-off mr-1.5"></i>
									<?php esc_html_e( 'Deactivate', 'frontend-dashboard' ); ?>
								</button>
							<?php elseif ( $is_installed ) : ?>
								<button type="button" class="fed-btn-activate inline-flex items-center px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm"
									data-plugin="<?php echo esc_attr( $addon['file'] ); ?>"
									data-title="<?php echo esc_attr( $addon['title'] ); ?>">
									<i class="fas fa-play mr-1.5"></i>
									<?php esc_html_e( 'Activate', 'frontend-dashboard' ); ?>
								</button>
							<?php elseif ( $is_pro ) : ?>
								<a href="<?php echo esc_url( $addon['purchase_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3.5 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
									<i class="fas fa-shopping-cart mr-1.5"></i>
									<?php esc_html_e( 'Get Pro', 'frontend-dashboard' ); ?>
								</a>
							<?php else : ?>
								<a href="<?php echo esc_url( $addon['docs_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
									<i class="fas fa-download mr-1.5"></i>
									<?php esc_html_e( 'Install', 'frontend-dashboard' ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>

				</div>
			<?php endforeach; ?>
		</div>

		<!-- Empty State (Hidden by default) -->
		<div id="fed_addons_empty_state" class="hidden bg-white rounded-2xl border border-slate-200/80 p-12 text-center my-8 shadow-sm">
			<div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-4">
				<i class="fas fa-search text-2xl"></i>
			</div>
			<h4 class="text-lg font-bold text-slate-800 mb-1">
				<?php esc_html_e( 'No matching extensions found', 'frontend-dashboard' ); ?>
			</h4>
			<p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">
				<?php esc_html_e( 'Try refining your search keyword or switching category tabs to find the extension you are looking for.', 'frontend-dashboard' ); ?>
			</p>
			<button type="button" id="fed_addons_reset_filters" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
				<i class="fas fa-undo mr-2"></i>
				<?php esc_html_e( 'Reset Filters', 'frontend-dashboard' ); ?>
			</button>
		</div>

	</div>

	<!-- Custom Theme Deactivation / Action Modal -->
	<div id="fed_addons_confirm_modal" class="fed-status-modal">
		<div class="bg-white rounded-2xl max-w-md w-full mx-4 shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
			<div class="p-6">
				<div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4 text-xl border border-amber-100">
					<i class="fas fa-exclamation-triangle"></i>
				</div>
				<h3 id="fed_modal_title" class="text-lg font-bold text-slate-900 mb-2">
					<?php esc_html_e( 'Deactivate Extension?', 'frontend-dashboard' ); ?>
				</h3>
				<p id="fed_modal_message" class="text-sm text-slate-600 leading-relaxed mb-6">
					<?php esc_html_e( 'Are you sure you want to deactivate this extension? Any frontend features associated with it will be temporarily unavailable until reactivated.', 'frontend-dashboard' ); ?>
				</p>
				<div class="flex items-center justify-end space-x-3">
					<button type="button" id="fed_modal_cancel" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition-all focus:outline-none">
						<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" id="fed_modal_confirm" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-semibold transition-all shadow-md focus:outline-none">
						<?php esc_html_e( 'Confirm Deactivate', 'frontend-dashboard' ); ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Global AJAX Progress Loader -->
	<div id="fed_addons_global_loader">
		<div class="bg-white rounded-2xl p-6 shadow-2xl border border-slate-100 flex items-center space-x-4 max-w-sm w-full mx-4">
			<div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin flex-shrink-0"></div>
			<div>
				<div id="fed_addons_loader_title" class="text-sm font-bold text-slate-900"><?php esc_html_e( 'Processing Request', 'frontend-dashboard' ); ?></div>
				<div id="fed_addons_loader_desc" class="text-xs text-slate-500 mt-0.5"><?php esc_html_e( 'Please wait while we update your plugins...', 'frontend-dashboard' ); ?></div>
			</div>
		</div>
	</div>

	<!-- Add-ons Marketplace Client-Side Script -->
	<script>
	(function() {
		const ajaxUrl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		const fedNonce = <?php echo wp_json_encode( $nonce ); ?>;

		// UI Elements
		const categoryNav = document.getElementById('fed_addons_category_nav');
		const searchInput = document.getElementById('fed_addons_search_input');
		const searchClear = document.getElementById('fed_addons_search_clear');
		const statusFilter = document.getElementById('fed_addons_status_filter');
		const cards = Array.from(document.querySelectorAll('.fed-addon-card'));
		const emptyState = document.getElementById('fed_addons_empty_state');
		const resetBtn = document.getElementById('fed_addons_reset_filters');
		const alertBox = document.getElementById('fed_addons_alert');
		const alertMsg = document.getElementById('fed_addons_alert_msg');
		const alertIcon = document.getElementById('fed_addons_alert_icon');
		const globalLoader = document.getElementById('fed_addons_global_loader');
		const loaderTitle = document.getElementById('fed_addons_loader_title');
		const loaderDesc = document.getElementById('fed_addons_loader_desc');
		const confirmModal = document.getElementById('fed_addons_confirm_modal');
		const modalTitle = document.getElementById('fed_modal_title');
		const modalMsg = document.getElementById('fed_modal_message');
		const modalCancel = document.getElementById('fed_modal_cancel');
		const modalConfirm = document.getElementById('fed_modal_confirm');
		const refreshBtn = document.getElementById('fed_btn_refresh_catalog');

		let currentCategory = 'all';
		let pendingAction = null;

		// Filter & Search Logic
		function applyFilters() {
			const query = (searchInput.value || '').trim().toLowerCase();
			const status = statusFilter.value;
			let visibleCount = 0;

			cards.forEach(card => {
				const cardCat = card.getAttribute('data-category');
				const cardStatus = card.getAttribute('data-status');
				const cardInstalled = card.getAttribute('data-installed') === 'true';
				const cardActive = card.getAttribute('data-active') === 'true';
				const cardPro = card.getAttribute('data-pro') === 'true';

				const title = card.getAttribute('data-title');
				const desc = card.getAttribute('data-desc');
				const tags = card.getAttribute('data-tags');

				// Category Check
				const matchCategory = (currentCategory === 'all' || cardCat === currentCategory);

				// Status Check
				let matchStatus = true;
				if (status === 'active') matchStatus = cardActive;
				else if (status === 'inactive') matchStatus = cardInstalled && !cardActive;
				else if (status === 'installed') matchStatus = cardInstalled;
				else if (status === 'pro') matchStatus = cardPro;

				// Search Query Check
				let matchSearch = true;
				if (query.length > 0) {
					matchSearch = title.includes(query) || desc.includes(query) || tags.includes(query);
				}

				if (matchCategory && matchStatus && matchSearch) {
					card.style.display = 'flex';
					visibleCount++;
				} else {
					card.style.display = 'none';
				}
			});

			if (query.length > 0) {
				searchClear.classList.remove('hidden');
			} else {
				searchClear.classList.add('hidden');
			}

			if (visibleCount === 0) {
				emptyState.classList.remove('hidden');
			} else {
				emptyState.classList.add('hidden');
			}
		}

		// Category Nav Click
		if (categoryNav) {
			categoryNav.addEventListener('click', function(e) {
				const btn = e.target.closest('button[data-category]');
				if (!btn) return;
				e.preventDefault();

				categoryNav.querySelectorAll('.fed-main-tab-btn').forEach(b => {
					b.classList.remove('fed-tab-active');
				});
				btn.classList.add('fed-tab-active');

				currentCategory = btn.getAttribute('data-category');
				applyFilters();
			});
		}

		// Search & Filter Events
		if (searchInput) {
			searchInput.addEventListener('input', applyFilters);
		}
		if (searchClear) {
			searchClear.addEventListener('click', function() {
				searchInput.value = '';
				applyFilters();
				searchInput.focus();
			});
		}
		if (statusFilter) {
			statusFilter.addEventListener('change', applyFilters);
		}
		if (resetBtn) {
			resetBtn.addEventListener('click', function() {
				currentCategory = 'all';
				if (categoryNav) {
					categoryNav.querySelectorAll('.fed-main-tab-btn').forEach(b => {
						b.classList.toggle('fed-tab-active', b.getAttribute('data-category') === 'all');
					});
				}
				searchInput.value = '';
				statusFilter.value = 'all';
				applyFilters();
			});
		}

		// Notification display
		function showAlert(type, message) {
			if (!alertBox) return;
			alertBox.className = 'mb-6 rounded-xl p-4 text-sm font-medium border flex items-center justify-between shadow-sm';
			if (type === 'success') {
				alertBox.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-200');
				alertIcon.className = 'fas fa-check-circle text-lg text-emerald-600';
			} else {
				alertBox.classList.add('bg-rose-50', 'text-rose-800', 'border-rose-200');
				alertIcon.className = 'fas fa-exclamation-circle text-lg text-rose-600';
			}
			alertMsg.textContent = message;
			alertBox.classList.remove('hidden');
			alertBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}

		// Show / Hide Global Loader
		function showLoader(title, desc) {
			if (!globalLoader) return;
			loaderTitle.textContent = title || 'Processing Request';
			loaderDesc.textContent = desc || 'Please wait while we update your plugins...';
			globalLoader.classList.add('show');
		}
		function hideLoader() {
			if (globalLoader) {
				globalLoader.classList.remove('show');
			}
		}

		// Modal Controls
		function showModal(title, message, confirmCallback) {
			if (!confirmModal) return;
			modalTitle.textContent = title;
			modalMsg.textContent = message;
			pendingAction = confirmCallback;
			confirmModal.classList.add('show');
		}
		function closeModal() {
			if (confirmModal) {
				confirmModal.classList.remove('show');
			}
			pendingAction = null;
		}

		if (modalCancel) {
			modalCancel.addEventListener('click', closeModal);
		}
		if (modalConfirm) {
			modalConfirm.addEventListener('click', function() {
				const action = pendingAction;
				closeModal();
				if (typeof action === 'function') {
					action();
				}
			});
		}

		// AJAX Helper
		function sendAjax(action, data, title, desc) {
			showLoader(title, desc);

			const formData = new FormData();
			formData.append('action', action);
			formData.append('fed_nonce', fedNonce);
			for (const key in data) {
				formData.append(key, data[key]);
			}

			fetch(ajaxUrl, {
				method: 'POST',
				body: formData
			})
			.then(res => res.json())
			.then(resp => {
				if (resp.success) {
					showAlert('success', resp.data && resp.data.message ? resp.data.message : 'Operation completed successfully!');
					setTimeout(() => {
						if (resp.data && resp.data.reload) {
							window.location.href = resp.data.reload;
						} else {
							window.location.reload();
						}
					}, 800);
				} else {
					hideLoader();
					showAlert('error', resp.data && resp.data.message ? resp.data.message : 'An error occurred. Please try again.');
				}
			})
			.catch(err => {
				hideLoader();
				showAlert('error', 'Network error or server unreachable: ' + err.message);
			});
		}

		// Activate Add-on Click
		document.addEventListener('click', function(e) {
			const btn = e.target.closest('.fed-btn-activate');
			if (!btn) return;
			e.preventDefault();

			const pluginFile = btn.getAttribute('data-plugin');
			const pluginTitle = btn.getAttribute('data-title');

			sendAjax(
				'fed_addon_activate',
				{ plugin_file: pluginFile },
				'Activating Extension',
				'Enabling ' + pluginTitle + ' and loading dependencies...'
			);
		});

		// Deactivate Add-on Click
		document.addEventListener('click', function(e) {
			const btn = e.target.closest('.fed-btn-deactivate');
			if (!btn) return;
			e.preventDefault();

			const pluginFile = btn.getAttribute('data-plugin');
			const pluginTitle = btn.getAttribute('data-title');

			showModal(
				'Deactivate ' + pluginTitle + '?',
				'Are you sure you want to deactivate ' + pluginTitle + '? Any frontend menus, shortcodes, and forms enabled by this extension will be paused until reactivated.',
				function() {
					sendAjax(
						'fed_addon_deactivate',
						{ plugin_file: pluginFile },
						'Deactivating Extension',
						'Safely disabling ' + pluginTitle + '...'
					);
				}
			);
		});

		// Refresh Catalog Click
		if (refreshBtn) {
			refreshBtn.addEventListener('click', function(e) {
				e.preventDefault();
				sendAjax(
					'fed_addon_refresh_catalog',
					{},
					'Refreshing Add-ons Catalog',
					'Querying official registry for latest extension releases...'
				);
			});
		}

	})();
	</script>
	<?php
}