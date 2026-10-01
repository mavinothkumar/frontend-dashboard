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
 * Get Built-in & Remote Catalog of Frontend Dashboard Add-ons
 *
 * @return array
 */
function fed_get_addons_catalog() {
	// 1. Base Metadata & Visual Definitions
	$meta_registry = array(
		'frontend-dashboard-notification'              => array(
			'category'      => 'communication',
			'category_name' => __( 'Communication', 'frontend-dashboard' ),
			'icon'          => 'fas fa-bell',
			'icon_bg'       => 'bg-gradient-to-br from-amber-500 to-orange-600 text-white',
			'tags'          => array( 'Notification Bell', 'Toast Popups', 'Broadcast Alerts', 'Trigger Events' ),
			'settings_url'  => admin_url( 'admin.php?page=fed_dashboard_menu' ),
			'description'   => __( 'Frontend Dashboard Notification allows admins to create and display custom floating notifications, toast popups, and broadcast messages to users on frontend dashboard pages.', 'frontend-dashboard' ),
		),
		'frontend-dashboard-custom-post-and-taxonomies' => array(
			'category'      => 'core',
			'category_name' => __( 'Core & Posts', 'frontend-dashboard' ),
			'icon'          => 'fas fa-layer-group',
			'icon_bg'       => 'bg-gradient-to-br from-indigo-500 to-blue-600 text-white',
			'tags'          => array( 'Custom Posts', 'Taxonomies', 'Frontend Submissions', 'Field Builder' ),
			'settings_url'  => admin_url( 'admin.php?page=fed_custom_post' ),
			'description'   => __( 'Frontend Dashboard Custom Post is an add-on to add, customize, and manage custom post types and custom taxonomies (categories & tags) directly inside the Frontend Dashboard.', 'frontend-dashboard' ),
		),
		'frontend-dashboard-custom-post'                => array(
			'category'      => 'core',
			'category_name' => __( 'Core & Posts', 'frontend-dashboard' ),
			'icon'          => 'fas fa-layer-group',
			'icon_bg'       => 'bg-gradient-to-br from-indigo-500 to-blue-600 text-white',
			'tags'          => array( 'Custom Posts', 'Taxonomies', 'Frontend Submissions', 'Field Builder' ),
			'settings_url'  => admin_url( 'admin.php?page=fed_custom_post' ),
			'description'   => __( 'Frontend Dashboard Custom Post is an add-on to add, customize, and manage custom post types and custom taxonomies (categories & tags) directly inside the Frontend Dashboard.', 'frontend-dashboard' ),
		),
		'frontend-dashboard-social-chat'               => array(
			'category'      => 'communication',
			'category_name' => __( 'Communication', 'frontend-dashboard' ),
			'icon'          => 'fas fa-comments',
			'icon_bg'       => 'bg-gradient-to-br from-emerald-500 to-green-600 text-white',
			'tags'          => array( 'WhatsApp Support', 'Telegram Widget', 'Floating Chat', 'Member Helpdesk' ),
			'settings_url'  => admin_url( 'admin.php?page=fed_dashboard_menu' ),
			'description'   => __( 'Frontend Dashboard Social Chat connects users directly with support or technical teams via WhatsApp, Telegram, and floating chat widgets.', 'frontend-dashboard' ),
		),
		'frontend-dashboard-captcha'                   => array(
			'category'      => 'security',
			'category_name' => __( 'Security & Auth', 'frontend-dashboard' ),
			'icon'          => 'fas fa-shield-alt',
			'icon_bg'       => 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white',
			'tags'          => array( 'reCAPTCHA v2/v3', 'Cloudflare Turnstile', 'Math Captcha', 'Anti-Spam' ),
			'settings_url'  => admin_url( 'admin.php?page=fed_settings_login' ),
			'description'   => __( 'Frontend Dashboard Captcha protects against spam bot submissions in Login, Registration, and frontend forms using Google reCAPTCHA, Cloudflare Turnstile, or Math Captcha.', 'frontend-dashboard' ),
		),
		'frontend-dashboard-message'                   => array(
			'category'      => 'communication',
			'category_name' => __( 'Communication', 'frontend-dashboard' ),
			'icon'          => 'fas fa-envelope-open-text',
			'icon_bg'       => 'bg-gradient-to-br from-sky-500 to-blue-600 text-white',
			'tags'          => array( 'Inbox & Sent', 'Conversation Threads', 'Attachments', 'Email Alerts' ),
			'settings_url'  => admin_url( 'admin.php?page=fed_dashboard_menu' ),
			'description'   => __( 'Internal peer-to-peer and admin-to-user private messaging system with conversations, attachments, and email notifications.', 'frontend-dashboard' ),
		),
		'frontend-dashboard-user-management'            => array(
			'category'      => 'user',
			'category_name' => __( 'User Management', 'frontend-dashboard' ),
			'icon'          => 'fas fa-user-shield',
			'icon_bg'       => 'bg-gradient-to-br from-violet-500 to-purple-600 text-white',
			'tags'          => array( 'User Table', 'Role Filter', 'Bulk Actions', 'Frontend Manager' ),
			'settings_url'  => admin_url( 'admin.php?page=fed_user_profile' ),
		),
		'frontend-dashboard-social-connect'            => array(
			'category'      => 'security',
			'category_name' => __( 'Security & Auth', 'frontend-dashboard' ),
			'icon'          => 'fas fa-users',
			'icon_bg'       => 'bg-gradient-to-br from-cyan-500 to-blue-600 text-white',
			'tags'          => array( 'OAuth 2.0', 'Google Login', 'Facebook Login', '1-Click Sign-in' ),
			'settings_url'  => admin_url( 'admin.php?page=fed_settings_login' ),
		),
		'frontend-dashboard-payment'                   => array(
			'category'      => 'monetization',
			'category_name' => __( 'Monetization', 'frontend-dashboard' ),
			'icon'          => 'fas fa-credit-card',
			'icon_bg'       => 'bg-gradient-to-br from-amber-500 to-yellow-600 text-white',
			'tags'          => array( 'Stripe Checkout', 'PayPal Express', 'PDF Invoices', 'Pay-per-Post' ),
			'settings_url'  => admin_url( 'admin.php?page=fed_payment' ),
		),
	);

	// 2. Fetch or load cached API data
	$api_data = get_transient( 'fed_plugin_list_api' );
	if ( false === $api_data && function_exists( 'get_plugin_list' ) ) {
		$api_data = get_plugin_list();
		if ( $api_data ) {
			set_transient( 'fed_plugin_list_api', $api_data, 12 * HOUR_IN_SECONDS );
		}
	}

	$plugins_raw = array();
	if ( $api_data ) {
		$decoded = json_decode( $api_data );
		if ( isset( $decoded->plugins ) ) {
			$plugins_raw = (array) $decoded->plugins;
		}
	}

	// 3. Fallback Built-in Official Catalog if API response is empty
	if ( empty( $plugins_raw ) ) {
		$plugins_raw = array(
			'frontend-dashboard-notification'              => (object) array(
				'id'           => 'BC_FED_NTF_PLUGIN',
				'version'      => '1.1',
				'directory'    => 'frontend-dashboard-notification/frontend-dashboard-notification.php',
				'title'        => 'Frontend Dashboard Notification',
				'description'  => 'Frontend Dashboard Notification is an add-on for Frontend Dashboard WordPress plugin which allows user to show notification in Frontend Dashboard page.',
				'thumbnail'    => 'https://buffercode.com/photos/1/plugins/frontend-dashboard-notification/frontend-dashboard-notification-600.png',
				'download_url' => 'https://buffercode.com/plugin/frontend-dashboard-notification',
				'install_slug' => 'frontend-dashboard-notification',
				'pricing'      => (object) array( 'type' => 'Free', 'amount' => '0', 'currency' => '$', 'currency_code' => 'USD', 'purchase_url' => '' ),
			),
			'frontend-dashboard-custom-post-and-taxonomies' => (object) array(
				'id'           => 'FED_CP_PLUGIN',
				'version'      => '3.0',
				'directory'    => 'frontend-dashboard-custom-post/frontend-dashboard-custom-post.php',
				'title'        => 'Frontend Dashboard Custom Post and Taxonomies',
				'description'  => 'Frontend Dashboard Custom Post is an add-on to add and customize the custom posts and taxonomies (category and tag) inside the Frontend Dashboard.',
				'thumbnail'    => 'https://buffercode.com/photos/1/plugins/frontend-dashboard-custom-post-taxonomies/custom_900.png',
				'download_url' => 'https://buffercode.com/plugin/frontend-dashboard-custom-post-and-taxonomies',
				'install_slug' => 'frontend-dashboard-custom-post',
				'pricing'      => (object) array( 'type' => 'Free', 'amount' => '0', 'currency' => '$', 'currency_code' => 'USD', 'purchase_url' => '' ),
			),
			'frontend-dashboard-social-chat'               => (object) array(
				'id'           => 'BC_FED_SCHAT_PLUGIN',
				'version'      => '1.3',
				'directory'    => 'frontend-dashboard-social-chat/frontend-dashboard-social-chat.php',
				'title'        => 'Frontend Dashboard Social Chat',
				'description'  => 'Frontend Dashboard Social Chat WordPress plugin makes users to connect the website support or technical teams via WhatsApp.',
				'thumbnail'    => 'https://buffercode.com/photos/1/plugins/social-chat/frontend-dashboard-social-chat-600.png',
				'download_url' => 'https://buffercode.com/plugin/frontend-dashboard-social-chat',
				'install_slug' => 'frontend-dashboard-social-chat',
				'pricing'      => (object) array( 'type' => 'Free', 'amount' => '0', 'currency' => '$', 'currency_code' => 'USD', 'purchase_url' => '' ),
			),
			'frontend-dashboard-captcha'                   => (object) array(
				'id'           => 'BC_FED_CAPTCHA_PLUGIN',
				'version'      => '3.0.0',
				'directory'    => 'frontend-dashboard-captcha/frontend-dashboard-captcha.php',
				'title'        => 'Frontend Dashboard Captcha',
				'description'  => 'Frontend Dashboard Captcha WordPress plugin is a supportive plugin for Frontend Dashboard to protect against spam in Login and Register form.',
				'thumbnail'    => 'https://buffercode.com/photos/1/plugins/frontend-dashboard-captcha/captcha-600.png',
				'download_url' => 'https://buffercode.com/plugin/frontend-dashboard-captcha',
				'install_slug' => 'frontend-dashboard-captcha',
				'pricing'      => (object) array( 'type' => 'Free', 'amount' => '0', 'currency' => '$', 'currency_code' => 'USD', 'purchase_url' => '' ),
			),
		);
	}

	// 4. Also check local plugins directory for any local add-ons (e.g. message)
	if ( ! isset( $plugins_raw['frontend-dashboard-message'] ) && file_exists( WP_PLUGIN_DIR . '/frontend-dashboard-message/frontend-dashboard-notification.php' ) ) {
		$plugins_raw['frontend-dashboard-message'] = (object) array(
			'id'           => 'BC_FED_MSG_PLUGIN',
			'version'      => '1.0',
			'directory'    => 'frontend-dashboard-message/frontend-dashboard-notification.php',
			'title'        => 'Frontend Dashboard Message',
			'description'  => 'Internal peer-to-peer and admin-to-user private messaging system with conversations, attachments, and email notifications.',
			'thumbnail'    => '',
			'download_url' => 'https://buffercode.com/plugin/frontend-dashboard-message',
			'install_slug' => 'frontend-dashboard-message',
			'pricing'      => (object) array( 'type' => 'Free', 'amount' => '0', 'currency' => '$', 'currency_code' => 'USD', 'purchase_url' => '' ),
		);
	}

	// 5. Build standardized catalog array (excluding deprecated and pro add-ons for free release)
	$deprecated_slugs = array(
		'frontend-dashboard-templates',
		'frontend-dashboard-pages',
		'frontend-dashboard-extra',
	);

	$catalog = array();
	foreach ( $plugins_raw as $slug => $item ) {
		// Skip self
		if ( 'frontend-dashboard' === $slug ) {
			continue;
		}

		// Skip deprecated add-ons
		if ( in_array( $slug, $deprecated_slugs, true ) ) {
			continue;
		}

		$is_pro = isset( $item->pricing->type ) && 'Pro' === $item->pricing->type;

		// Currently hide Pro versions for the free version release
		if ( $is_pro ) {
			continue;
		}

		// Dependency check: Must require frontend-dashboard
		$required_plugins = isset( $item->required_plugins ) ? (array) $item->required_plugins : array();
		$is_fed_addon     = in_array( 'frontend-dashboard', $required_plugins, true )
							|| ( empty( $required_plugins ) && 0 === strpos( $slug, 'frontend-dashboard-' ) );

		if ( ! $is_fed_addon ) {
			continue;
		}

		$meta      = isset( $meta_registry[ $slug ] ) ? $meta_registry[ $slug ] : array();
		$directory = isset( $item->directory ) ? $item->directory : ( $slug . '/' . $slug . '.php' );

		// Clean up description: strip HTML tags (like <p class="graf">) from API and normalize spaces
		$raw_desc   = ! empty( $meta['description'] ) ? $meta['description'] : ( isset( $item->description ) ? $item->description : '' );
		$clean_desc = trim( wp_strip_all_tags( (string) $raw_desc ) );
		$clean_desc = html_entity_decode( $clean_desc, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$clean_desc = (string) preg_replace( '/\s+/', ' ', $clean_desc );

		$catalog[ $slug ] = array(
			'slug'          => $slug,
			'id'            => isset( $item->id ) ? $item->id : '',
			'title'         => isset( $item->title ) ? $item->title : $slug,
			'version'       => isset( $item->version ) ? $item->version : '1.0',
			'directory'     => $directory,
			'file'          => $directory,
			'description'   => $clean_desc,
			'thumbnail'     => isset( $item->thumbnail ) ? $item->thumbnail : '',
			'download_url'  => isset( $item->download_url ) ? $item->download_url : 'https://buffercode.com/',
			'install_slug'  => isset( $item->install_slug ) ? $item->install_slug : $slug,
			'pricing'       => isset( $item->pricing ) ? $item->pricing : (object) array( 'type' => 'Free' ),
			'is_pro'        => false,
			'category'      => isset( $meta['category'] ) ? $meta['category'] : 'core',
			'category_name' => isset( $meta['category_name'] ) ? $meta['category_name'] : __( 'Core & Posts', 'frontend-dashboard' ),
			'icon'          => isset( $meta['icon'] ) ? $meta['icon'] : 'fas fa-puzzle-piece',
			'icon_bg'       => isset( $meta['icon_bg'] ) ? $meta['icon_bg'] : 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white',
			'tags'          => isset( $meta['tags'] ) ? $meta['tags'] : array( 'Official Extension', 'Free' ),
			'settings_url'  => isset( $meta['settings_url'] ) ? $meta['settings_url'] : '',
		);
	}

	return $catalog;
}

/**
 * Get Catalog of Other Plugins by BufferCode (Standalone Ecosystem Products)
 *
 * @return array
 */
function fed_get_other_plugins_catalog() {
	$current_slug = 'frontend-dashboard';

	// Visual metadata registry for standalone BufferCode products
	$meta_registry = array(
		'ad-fuz'  => array(
			'tagline'       => __( 'Ad Management & Campaign Delivery Engine', 'frontend-dashboard' ),
			'category_name' => __( 'Ad Management', 'frontend-dashboard' ),
			'icon'          => 'fas fa-bullhorn',
			'icon_bg'       => 'bg-gradient-to-br from-violet-600 to-indigo-600 text-white',
			'tags'          => array( 'Banner Ads', 'Video Ads', 'Campaign Schedules', 'Impression Telemetry' ),
			'is_wp_org'     => true,
		),
		'gatefuz' => array(
			'tagline'       => __( 'Decoupled Multi-Gateway Payment Solution', 'frontend-dashboard' ),
			'category_name' => __( 'Payments & Checkout', 'frontend-dashboard' ),
			'icon'          => 'fas fa-credit-card',
			'icon_bg'       => 'bg-gradient-to-br from-emerald-600 to-teal-600 text-white',
			'tags'          => array( '7 Global Gateways', 'Stripe & PayPal', 'Decoupled Architecture', 'Centralized Ledger' ),
			'is_wp_org'     => false,
		),
	);

	$api_data = get_transient( 'fed_plugin_list_api' );
	if ( false === $api_data && function_exists( 'get_plugin_list' ) ) {
		$api_data = get_plugin_list();
		if ( $api_data ) {
			set_transient( 'fed_plugin_list_api', $api_data, 12 * HOUR_IN_SECONDS );
		}
	}

	$plugins_raw = array();
	if ( $api_data ) {
		$decoded = json_decode( $api_data );
		if ( isset( $decoded->plugins ) ) {
			$plugins_raw = (array) $decoded->plugins;
		}
	}

	// Fallback definitions in case the API is offline or hasn't returned them
	if ( empty( $plugins_raw ) || ( ! isset( $plugins_raw['ad-fuz'] ) && ! isset( $plugins_raw['gatefuz'] ) ) ) {
		$fallback = array(
			'ad-fuz'  => (object) array(
				'id'               => 'AD_FUZ_PLUGIN',
				'version'          => '1.3.13',
				'directory'        => 'ad-fuz/ad-fuz.php',
				'title'            => 'Ad Fuz',
				'description'      => 'AdFuz gives you a structured, developer-grade workflow to create ads, schedule campaigns with precise date ranges, and serve them through defined ad spaces all from your WordPress dashboard.',
				'thumbnail'        => 'https://buffercode.com/storage/plugins/banner-1544x500_1790584184_8QxCE_cover.webp',
				'download_url'     => 'https://buffercode.com/plugin/ad-fuz',
				'install_slug'     => 'ad-fuz',
				'required_plugins' => array(),
			),
			'gatefuz' => (object) array(
				'id'               => 'GATEFUZ_PLUGIN',
				'version'          => '1.0.7',
				'directory'        => 'gatefuz/gatefuz.php',
				'title'            => 'GateFuz',
				'description'      => 'Most WordPress payment solutions lock you into heavy, bloated eCommerce frameworks just to accept a single charge or recurring subscription. GateFuz redefines payments on WordPress with a developer-first, decoupled architecture supporting 7 global gateways.',
				'thumbnail'        => 'https://buffercode.com/storage/plugins/banner-1544x500_1790689495_HCWJS_cover.webp',
				'download_url'     => 'https://buffercode.com/plugin/gatefuz',
				'install_slug'     => 'gatefuz',
				'required_plugins' => array(),
			),
		);
		$plugins_raw = array_merge( $plugins_raw, $fallback );
	}

	$other_plugins = array();
	foreach ( $plugins_raw as $slug => $item ) {
		if ( $slug === $current_slug ) {
			continue;
		}

		$required = isset( $item->required_plugins ) ? (array) $item->required_plugins : array();

		// Standalone plugin: empty required_plugins and not a frontend-dashboard add-on
		$is_standalone = empty( $required ) && 0 !== strpos( $slug, 'frontend-dashboard-' );
		if ( ! $is_standalone ) {
			continue;
		}

		$meta      = isset( $meta_registry[ $slug ] ) ? $meta_registry[ $slug ] : array();
		$directory = isset( $item->directory ) ? $item->directory : ( $slug . '/' . $slug . '.php' );

		$raw_desc   = isset( $item->description ) ? $item->description : '';
		$clean_desc = trim( wp_strip_all_tags( (string) $raw_desc ) );
		$clean_desc = html_entity_decode( $clean_desc, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$clean_desc = (string) preg_replace( '/\s+/', ' ', $clean_desc );

		$other_plugins[ $slug ] = array(
			'slug'          => $slug,
			'id'            => isset( $item->id ) ? $item->id : '',
			'title'         => isset( $item->title ) ? $item->title : $slug,
			'tagline'       => isset( $meta['tagline'] ) ? $meta['tagline'] : '',
			'version'       => isset( $item->version ) ? $item->version : '1.0',
			'directory'     => $directory,
			'file'          => $directory,
			'description'   => $clean_desc,
			'thumbnail'     => isset( $item->thumbnail ) ? $item->thumbnail : '',
			'download_url'  => isset( $item->download_url ) ? $item->download_url : 'https://buffercode.com/plugin/' . $slug,
			'install_slug'  => isset( $item->install_slug ) ? $item->install_slug : $slug,
			'pricing'       => isset( $item->pricing ) ? $item->pricing : (object) array( 'type' => 'Free' ),
			'category_name' => isset( $meta['category_name'] ) ? $meta['category_name'] : __( 'Standalone Plugin', 'frontend-dashboard' ),
			'icon'          => isset( $meta['icon'] ) ? $meta['icon'] : 'fas fa-box-open',
			'icon_bg'       => isset( $meta['icon_bg'] ) ? $meta['icon_bg'] : 'bg-gradient-to-br from-indigo-500 to-purple-600 text-white',
			'tags'          => isset( $meta['tags'] ) ? $meta['tags'] : array( 'BufferCode', 'WordPress' ),
			'is_wp_org'     => isset( $meta['is_wp_org'] ) ? $meta['is_wp_org'] : ( 'ad-fuz' === $slug ),
		);
	}

	return $other_plugins;
}

/**
 * Get Plugin Pages Menu / Add-ons Marketplace Page
 */
function fed_get_plugin_pages_menu() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$catalog       = fed_get_addons_catalog();
	$other_plugins = fed_get_other_plugins_catalog();

	// Calculate live statistics for Frontend Dashboard Add-ons
	$total_addons     = count( $catalog );
	$active_count     = 0;
	$installed_count  = 0;
	$pro_count        = 0;

	foreach ( $catalog as $slug => &$item ) {
		$file = $item['file'];
		$item['is_installed'] = file_exists( WP_PLUGIN_DIR . '/' . $file );
		$item['is_active']    = $item['is_installed'] && is_plugin_active( $file );

		// Local version detection
		$local_version = '';
		if ( ! empty( $item['id'] ) ) {
			if ( defined( $item['id'] . '_VERSION' ) ) {
				$local_version = constant( $item['id'] . '_VERSION' );
			} elseif ( defined( $item['id'] ) ) {
				$local_version = constant( $item['id'] );
			}
		}
		$item['local_version'] = $local_version;
		$item['has_update']    = ( $item['is_active'] && ! empty( $local_version ) && version_compare( $item['version'], $local_version, '>' ) );

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

	// Calculate live status for other BufferCode ecosystem plugins
	foreach ( $other_plugins as $slug => &$item ) {
		$file                 = $item['file'];
		$item['is_installed'] = file_exists( WP_PLUGIN_DIR . '/' . $file );
		$item['is_active']    = $item['is_installed'] && is_plugin_active( $file );
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
		
		<?php if ( function_exists( 'fed_render_addon_compatibility_banner' ) ) : ?>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo fed_render_addon_compatibility_banner();
			?>
		<?php endif; ?>

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
				<!-- Installed Total -->
				<div class="bg-blue-50/80 rounded-xl p-4 border border-blue-100">
					<div class="text-xs font-semibold uppercase tracking-wider text-blue-700">
						<?php esc_html_e( 'Installed Total', 'frontend-dashboard' ); ?>
					</div>
					<div class="text-2xl font-extrabold text-blue-700 mt-1 flex items-baseline">
						<?php echo esc_html( $installed_count ); ?>
						<span class="ml-2 text-xs font-medium text-blue-600"><?php esc_html_e( 'On Server', 'frontend-dashboard' ); ?></span>
					</div>
				</div>
				<!-- Free Extensions -->
				<div class="bg-indigo-50/80 rounded-xl p-4 border border-indigo-100">
					<div class="text-xs font-semibold uppercase tracking-wider text-indigo-700">
						<?php esc_html_e( 'Free Extensions', 'frontend-dashboard' ); ?>
					</div>
					<div class="text-2xl font-extrabold text-indigo-700 mt-1 flex items-baseline">
						<?php echo esc_html( $total_addons ); ?>
						<span class="ml-2 text-xs font-medium text-indigo-600"><?php esc_html_e( '100% Free', 'frontend-dashboard' ); ?></span>
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
					<button type="button" data-category="communication" class="fed-main-tab-btn inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all focus:outline-none">
						<i class="fas fa-comments mr-2 text-xs"></i>
						<?php esc_html_e( 'Communication', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" data-category="ecosystem" class="fed-main-tab-btn inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all focus:outline-none">
						<i class="fas fa-cubes mr-2 text-xs text-indigo-500"></i>
						<?php esc_html_e( 'Other BufferCode Plugins', 'frontend-dashboard' ); ?>
						<span class="fed-tab-count ml-2 px-2 py-0.5 rounded-full text-xs bg-slate-200 text-slate-700 font-bold"><?php echo esc_html( count( $other_plugins ) ); ?></span>
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
					</select>
				</div>
			</div>
		</div>

		<!-- Addons Grid -->
		<div id="fed_addons_grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
			<?php foreach ( $catalog as $slug => $addon ) : ?>
				<?php
				$is_active     = $addon['is_active'];
				$is_installed  = $addon['is_installed'];
				$is_pro        = ! empty( $addon['is_pro'] );
				$has_update      = ! empty( $addon['has_update'] );
				$local_version   = ! empty( $addon['local_version'] ) ? $addon['local_version'] : $addon['version'];
				$is_incompatible = ( $is_active && ! empty( $local_version ) && version_compare( $local_version, '3.0.0', '<' ) );

				$status_attr = 'available';
				if ( $is_active ) {
					$status_attr = 'active';
				} elseif ( $is_installed ) {
					$status_attr = 'inactive';
				}
				if ( $is_pro ) {
					$status_attr .= ' pro';
				}
				if ( $is_incompatible ) {
					$status_attr .= ' incompatible';
				}
				$card_border_class = $is_incompatible ? 'border-rose-300 ring-2 ring-rose-200/80 shadow-md shadow-rose-100/50' : 'border-slate-200/80 shadow-sm';
				?>
				<div class="fed-addon-card bg-white rounded-2xl border <?php echo esc_attr( $card_border_class ); ?> flex flex-col justify-between overflow-hidden"
					data-category="<?php echo esc_attr( $addon['category'] ); ?>"
					data-status="<?php echo esc_attr( $status_attr ); ?>"
					data-installed="<?php echo $is_installed ? 'true' : 'false'; ?>"
					data-active="<?php echo $is_active ? 'true' : 'false'; ?>"
					data-pro="<?php echo $is_pro ? 'true' : 'false'; ?>"
					data-incompatible="<?php echo $is_incompatible ? 'true' : 'false'; ?>"
					data-title="<?php echo esc_attr( strtolower( $addon['title'] ) ); ?>"
					data-desc="<?php echo esc_attr( strtolower( $addon['description'] ) ); ?>"
					data-tags="<?php echo esc_attr( strtolower( implode( ' ', $addon['tags'] ) ) ); ?>">
					
					<div>
						<!-- Card Thumbnail Image or Gradient Banner -->
						<div class="relative w-full h-36 bg-slate-100 overflow-hidden border-b border-slate-100 flex items-center justify-center">
							<?php if ( ! empty( $addon['thumbnail'] ) ) : ?>
								<img src="<?php echo esc_url( $addon['thumbnail'] ); ?>" alt="<?php echo esc_attr( $addon['title'] ); ?>" class="w-full h-full object-cover object-center transition-transform duration-300 hover:scale-105" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.classList.remove('hidden');}" />
							<?php endif; ?>
							<div class="<?php echo ! empty( $addon['thumbnail'] ) ? 'hidden ' : ''; ?>w-full h-full flex flex-col items-center justify-center <?php echo esc_attr( $addon['icon_bg'] ); ?>">
								<i class="<?php echo esc_attr( $addon['icon'] ); ?> text-4xl text-white/90 mb-1.5"></i>
								<span class="text-xs font-bold text-white/90 tracking-wide px-4 text-center"><?php echo esc_html( $addon['title'] ); ?></span>
							</div>
							<div class="absolute top-3 left-3">
								<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-900/70 text-white backdrop-blur-sm">
									<?php echo esc_html( $addon['category_name'] ); ?>
								</span>
							</div>
							<div class="absolute top-3 right-3">
								<?php if ( $is_active && $is_incompatible ) : ?>
									<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-sm backdrop-blur-sm">
										<i class="fas fa-exclamation-triangle text-[10px] mr-1.5"></i>
										<?php esc_html_e( 'v3.0.0+ Required', 'frontend-dashboard' ); ?>
									</span>
								<?php elseif ( $is_active ) : ?>
									<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500 text-white shadow-sm backdrop-blur-sm">
										<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse mr-1.5"></span>
										<?php esc_html_e( 'Active', 'frontend-dashboard' ); ?>
									</span>
								<?php elseif ( $is_installed ) : ?>
									<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-800/80 text-white backdrop-blur-sm">
										<?php esc_html_e( 'Inactive', 'frontend-dashboard' ); ?>
									</span>
								<?php elseif ( $is_pro ) : ?>
									<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-600 text-white shadow-sm">
										<i class="fas fa-crown text-[10px] mr-1"></i>
										<?php esc_html_e( 'Pro', 'frontend-dashboard' ); ?>
									</span>
								<?php else : ?>
									<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-600 text-white shadow-sm">
										<?php esc_html_e( 'Free', 'frontend-dashboard' ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>

						<div class="p-5">
							<!-- Card Top Row -->
							<div class="flex items-start justify-between gap-3 mb-3">
								<div>
									<h3 class="font-bold text-slate-900 text-base leading-snug">
										<?php echo esc_html( $addon['title'] ); ?>
									</h3>
									<div class="flex items-center space-x-2 mt-1">
										<span class="text-xs font-semibold text-slate-500">
											v<?php echo esc_html( $is_active ? $local_version : $addon['version'] ); ?>
										</span>
										<span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span>
										<span class="text-xs font-medium text-slate-500">
											<?php echo esc_html( $addon['category_name'] ); ?>
										</span>
										<?php if ( $has_update ) : ?>
											<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
												<i class="fas fa-arrow-circle-up mr-1"></i>
												v<?php echo esc_html( $addon['version'] ); ?> Available
											</span>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<!-- Description -->
							<p class="text-slate-600 text-xs leading-relaxed mb-4 line-clamp-3">
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
					</div>

					<!-- Card Footer / Actions -->
					<div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-2 flex-wrap">
						<div>
							<?php if ( ! empty( $addon['download_url'] ) ) : ?>
								<a href="<?php echo esc_url( $addon['download_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors inline-flex items-center">
									<i class="fas fa-external-link-alt mr-1.5 text-slate-400 text-[10px]"></i>
									<?php esc_html_e( 'Details', 'frontend-dashboard' ); ?>
								</a>
							<?php endif; ?>
						</div>

						<div class="flex items-center space-x-2 flex-wrap">
							<?php if ( ! empty( $addon['is_merged'] ) ) : ?>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_dashboard_menu' ) ); ?>" class="inline-flex items-center px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
									<i class="fas fa-sliders-h mr-1.5"></i>
									<?php esc_html_e( 'Manage in Menus', 'frontend-dashboard' ); ?>
								</a>
								<?php if ( $is_active ) : ?>
									<button type="button" class="fed-btn-deactivate inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg transition-all border border-rose-200"
										data-plugin="<?php echo esc_attr( $addon['file'] ); ?>"
										data-title="<?php echo esc_attr( $addon['title'] ); ?>">
										<i class="fas fa-power-off mr-1"></i>
										<?php esc_html_e( 'Deactivate', 'frontend-dashboard' ); ?>
									</button>
								<?php endif; ?>
							<?php elseif ( $is_active ) : ?>
								<?php if ( $has_update ) : ?>
									<a href="<?php echo esc_url( $addon['download_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
										<i class="fas fa-sync-alt mr-1"></i>
										<?php esc_html_e( 'Update', 'frontend-dashboard' ); ?>
									</a>
								<?php endif; ?>
								<?php if ( ! empty( $addon['settings_url'] ) ) : ?>
									<a href="<?php echo esc_url( $addon['settings_url'] ); ?>" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
										<i class="fas fa-sliders-h mr-1.5"></i>
										<?php esc_html_e( 'Configure', 'frontend-dashboard' ); ?>
									</a>
								<?php endif; ?>
								<button type="button" class="fed-btn-deactivate inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg transition-all border border-rose-200"
									data-plugin="<?php echo esc_attr( $addon['file'] ); ?>"
									data-title="<?php echo esc_attr( $addon['title'] ); ?>">
									<i class="fas fa-power-off mr-1"></i>
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
								<?php if ( ! empty( $addon['pricing']->amount ) && is_object( $addon['pricing']->amount ) ) : ?>
									<?php foreach ( $addon['pricing']->amount as $atype => $amount_obj ) : ?>
										<form method="post" action="<?php echo esc_url( $addon['pricing']->purchase_url ); ?>" class="inline-block">
											<input type="hidden" name="redirect_url" value="<?php echo esc_url( fed_current_page_url() ); ?>"/>
											<input type="hidden" name="domain" value="<?php echo esc_attr( fed_get_domain_name() ); ?>"/>
											<input type="hidden" name="contact_email" value="<?php echo esc_attr( fed_get_admin_email() ); ?>"/>
											<input type="hidden" name="plugin_name" value="<?php echo esc_attr( $slug ); ?>"/>
											<input type="hidden" name="amount" value="<?php echo esc_attr( $amount_obj->amount ); ?>"/>
											<input type="hidden" name="plan_type" value="<?php echo esc_attr( $atype ); ?>"/>
											<button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
												<i class="fas fa-shopping-cart mr-1 text-[11px]"></i>
												<?php echo esc_html( $amount_obj->name . ' ' . $addon['pricing']->currency . $amount_obj->amount ); ?>
											</button>
										</form>
									<?php endforeach; ?>
								<?php else : ?>
									<a href="<?php echo esc_url( $addon['download_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3.5 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
										<i class="fas fa-crown mr-1.5"></i>
										<?php esc_html_e( 'Get Pro', 'frontend-dashboard' ); ?>
									</a>
								<?php endif; ?>
							<?php else : ?>
								<form method="post" class="fed_ajax_plugin_install inline-block"
									action="<?php echo esc_url( fed_get_ajax_form_action( 'fed_api_ajax_request' ) . '&fed_action_hook=FEDInstallAddons@install' ); ?>">
									<?php wp_nonce_field( 'updates' ); ?>
									<input type="hidden" name="slug" value="<?php echo esc_attr( $addon['install_slug'] ); ?>">
									<button type="submit" class="inline-flex items-center px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
										<i class="fas fa-download mr-1.5"></i>
										<?php esc_html_e( 'Install & Activate', 'frontend-dashboard' ); ?>
									</button>
								</form>
							<?php endif; ?>
						</div>
					</div>

				</div>
			<?php endforeach; ?>
		</div>

		<!-- Other BufferCode Plugins Showcase -->
		<div id="fed_other_plugins_section" class="mt-12 pt-8 border-t border-slate-200/80">
			<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
				<div>
					<div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 mb-2">
						<i class="fas fa-cubes text-[11px]"></i>
						<?php esc_html_e( 'BufferCode Ecosystem', 'frontend-dashboard' ); ?>
					</div>
					<h2 class="text-xl font-bold text-slate-900 tracking-tight">
						<?php esc_html_e( 'More WordPress Plugins by BufferCode', 'frontend-dashboard' ); ?>
					</h2>
					<p class="text-sm text-slate-500 mt-0.5">
						<?php esc_html_e( 'Standalone WordPress solutions crafted with the same lightweight, modular philosophy.', 'frontend-dashboard' ); ?>
					</p>
				</div>
			</div>

			<div id="fed_other_plugins_grid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<?php foreach ( $other_plugins as $slug => $item ) : ?>
					<?php
					$is_active     = $item['is_active'];
					$is_installed  = $item['is_installed'];
					$status_attr   = $is_active ? 'active' : ( $is_installed ? 'inactive' : 'available' );
					$card_border   = 'border-slate-200/80 shadow-sm hover:shadow-md';
					?>
					<div class="fed-addon-card fed-ecosystem-card bg-white rounded-2xl border <?php echo esc_attr( $card_border ); ?> flex flex-col justify-between overflow-hidden"
						data-category="ecosystem"
						data-status="<?php echo esc_attr( $status_attr ); ?>"
						data-installed="<?php echo $is_installed ? 'true' : 'false'; ?>"
						data-active="<?php echo $is_active ? 'true' : 'false'; ?>"
						data-pro="false"
						data-incompatible="false"
						data-title="<?php echo esc_attr( strtolower( $item['title'] ) ); ?>"
						data-desc="<?php echo esc_attr( strtolower( $item['description'] ) ); ?>"
						data-tags="<?php echo esc_attr( strtolower( implode( ' ', $item['tags'] ) ) ); ?>">
						
						<div>
							<!-- Card Thumbnail Image or Gradient Banner -->
							<div class="relative w-full h-40 bg-slate-100 overflow-hidden border-b border-slate-100 flex items-center justify-center">
								<?php if ( ! empty( $item['thumbnail'] ) ) : ?>
									<img src="<?php echo esc_url( $item['thumbnail'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" class="w-full h-full object-cover object-center transition-transform duration-300 hover:scale-105" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.classList.remove('hidden');}" />
								<?php endif; ?>
								<div class="<?php echo ! empty( $item['thumbnail'] ) ? 'hidden ' : ''; ?>w-full h-full flex flex-col items-center justify-center <?php echo esc_attr( $item['icon_bg'] ); ?>">
									<i class="<?php echo esc_attr( $item['icon'] ); ?> text-4xl text-white/90 mb-1.5"></i>
									<span class="text-xs font-bold text-white/90 tracking-wide px-4 text-center"><?php echo esc_html( $item['title'] ); ?></span>
								</div>
								<div class="absolute top-3 left-3">
									<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-900/70 text-white backdrop-blur-sm">
										<?php echo esc_html( $item['category_name'] ); ?>
									</span>
								</div>
								<div class="absolute top-3 right-3">
									<?php if ( $is_active ) : ?>
										<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500 text-white shadow-sm backdrop-blur-sm">
											<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse mr-1.5"></span>
											<?php esc_html_e( 'Active', 'frontend-dashboard' ); ?>
										</span>
									<?php elseif ( $is_installed ) : ?>
										<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-800/80 text-white backdrop-blur-sm">
											<?php esc_html_e( 'Inactive', 'frontend-dashboard' ); ?>
										</span>
									<?php else : ?>
										<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-600 text-white shadow-sm backdrop-blur-sm">
											<?php esc_html_e( 'Standalone', 'frontend-dashboard' ); ?>
										</span>
									<?php endif; ?>
								</div>
							</div>

							<div class="p-5">
								<!-- Card Top Row -->
								<div class="flex items-start justify-between gap-3 mb-2">
									<div>
										<h3 class="font-bold text-slate-900 text-lg leading-snug">
											<?php echo esc_html( $item['title'] ); ?>
										</h3>
										<?php if ( ! empty( $item['tagline'] ) ) : ?>
											<p class="text-xs font-medium text-indigo-600 mt-0.5">
												<?php echo esc_html( $item['tagline'] ); ?>
											</p>
										<?php endif; ?>
										<div class="flex items-center space-x-2 mt-1.5">
											<span class="text-xs font-semibold text-slate-500">
												v<?php echo esc_html( $item['version'] ); ?>
											</span>
											<span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span>
											<span class="text-xs font-medium text-slate-500">
												<?php esc_html_e( 'By BufferCode', 'frontend-dashboard' ); ?>
											</span>
										</div>
									</div>
								</div>

								<!-- Description -->
								<p class="text-slate-600 text-xs leading-relaxed mb-4 line-clamp-3">
									<?php echo esc_html( $item['description'] ); ?>
								</p>

								<!-- Feature Tags -->
								<div class="flex flex-wrap gap-1.5 mb-2">
									<?php foreach ( $item['tags'] as $tag ) : ?>
										<span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600">
											<?php echo esc_html( $tag ); ?>
										</span>
									<?php endforeach; ?>
								</div>
							</div>
						</div>

						<!-- Card Footer / Actions -->
						<div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-2 flex-wrap">
							<div>
								<a href="<?php echo esc_url( $item['download_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 transition-colors inline-flex items-center">
									<i class="fas fa-external-link-alt mr-1.5 text-slate-400 text-[10px]"></i>
									<?php esc_html_e( 'Details & Docs', 'frontend-dashboard' ); ?>
								</a>
							</div>

							<div class="flex items-center space-x-2 flex-wrap">
								<?php if ( $is_active ) : ?>
									<button type="button" class="fed-btn-deactivate inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg transition-all border border-rose-200"
										data-plugin="<?php echo esc_attr( $item['file'] ); ?>"
										data-title="<?php echo esc_attr( $item['title'] ); ?>">
										<i class="fas fa-power-off mr-1"></i>
										<?php esc_html_e( 'Deactivate', 'frontend-dashboard' ); ?>
									</button>
								<?php elseif ( $is_installed ) : ?>
									<button type="button" class="fed-btn-activate inline-flex items-center px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm"
										data-plugin="<?php echo esc_attr( $item['file'] ); ?>"
										data-title="<?php echo esc_attr( $item['title'] ); ?>">
										<i class="fas fa-play mr-1.5"></i>
										<?php esc_html_e( 'Activate', 'frontend-dashboard' ); ?>
									</button>
								<?php elseif ( ! empty( $item['is_wp_org'] ) ) : ?>
									<form method="post" class="fed_ajax_plugin_install inline-block"
										action="<?php echo esc_url( fed_get_ajax_form_action( 'fed_api_ajax_request' ) . '&fed_action_hook=FEDInstallAddons@install' ); ?>">
										<?php wp_nonce_field( 'updates' ); ?>
										<input type="hidden" name="slug" value="<?php echo esc_attr( $item['install_slug'] ); ?>">
										<button type="submit" class="inline-flex items-center px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
											<i class="fas fa-download mr-1.5"></i>
											<?php esc_html_e( 'Install & Activate', 'frontend-dashboard' ); ?>
										</button>
									</form>
								<?php else : ?>
									<a href="<?php echo esc_url( $item['download_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
										<i class="fas fa-external-link-alt mr-1.5 text-xs"></i>
										<?php echo esc_html( sprintf( __( 'Get %s', 'frontend-dashboard' ), $item['title'] ) ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>

					</div>
				<?php endforeach; ?>
			</div>
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
		const addonsGrid = document.getElementById('fed_addons_grid');
		const otherSection = document.getElementById('fed_other_plugins_section');
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

			// Toggle parent section containers based on selected category tab
			if (addonsGrid) {
				if (currentCategory === 'ecosystem') {
					addonsGrid.style.display = 'none';
				} else {
					addonsGrid.style.display = 'grid';
				}
			}

			if (otherSection) {
				if (currentCategory === 'core' || currentCategory === 'security' || currentCategory === 'communication') {
					otherSection.style.display = 'none';
				} else {
					otherSection.style.display = 'block';
				}
			}

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