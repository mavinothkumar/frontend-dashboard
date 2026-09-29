<?php

namespace FED\Licensing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LicenseAdminController
 *
 * Coordinates dynamic admin submenu registration and renders the unified Licenses Management Hub.
 */
class LicenseAdminController {

	/**
	 * Register the dynamic submenu hook.
	 */
	public static function register_menu() {
		add_filter( 'fed_add_main_sub_menu', array( __CLASS__, 'filter_sub_menu' ) );
	}

	/**
	 * Filter: fed_add_main_sub_menu
	 *
	 * Only injects "Licenses" submenu when at least one Pro extension is registered and active.
	 *
	 * @param array $menu
	 * @return array
	 */
	public static function filter_sub_menu( $menu ) {
		if ( LicenseRegistry::has_pro_addons() ) {
			$menu['fed_licenses'] = array(
				'page_title' => __( 'Licenses', 'frontend-dashboard' ),
				'menu_title' => __( 'Licenses', 'frontend-dashboard' ),
				'capability' => 'manage_options',
				'callback'   => array( __CLASS__, 'render' ),
				'position'   => 85,
			);
		}

		return $menu;
	}

	/**
	 * Render the Licenses Management Hub page.
	 */
	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'frontend-dashboard' ) );
		}

		$addons   = LicenseRegistry::get_registered_addons( true );
		$licenses = LicenseManager::instance()->get_licenses();

		$total_addons    = count( $addons );
		$active_licenses = 0;

		$inactive_addons = array();
		$active_addons   = array();

		foreach ( $addons as $slug => $addon ) {
			$license   = isset( $licenses[ $slug ] ) ? $licenses[ $slug ] : array();
			$status    = ! empty( $license['status'] ) ? $license['status'] : 'inactive';
			$is_active = ( 'active' === $status );

			$addon_data = array_merge( $addon, array(
				'license'   => $license,
				'status'    => $status,
				'is_active' => $is_active,
			) );

			if ( $is_active ) {
				$active_licenses++;
				$active_addons[ $slug ] = $addon_data;
			} else {
				$inactive_addons[ $slug ] = $addon_data;
			}
		}

		$inactive_count = count( $inactive_addons );
		$site_domain    = LicenseApiClient::get_site_domain();
		$nonce          = wp_create_nonce( 'fed_license_admin_nonce' );

		?>
		<div class="wrap bc_fed fed-licenses-wrap max-w-8xl mx-auto px-4 sm:px-6 py-5 font-sans">
			
			<!-- Scoped Styling -->
			<style>
				.fed-licenses-wrap * {
					box-sizing: border-box;
				}
				.fed-lic-header {
					background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
					border-radius: 14px;
					padding: 20px 24px;
					color: #ffffff;
					margin-bottom: 22px;
					box-shadow: 0 8px 20px -4px rgba(30, 27, 75, 0.25);
					display: flex;
					align-items: center;
					justify-content: space-between;
					flex-wrap: wrap;
					gap: 16px;
				}
				.fed-lic-header-title h1 {
					color: #ffffff !important;
					font-size: 20px !important;
					font-weight: 700 !important;
					margin: 0 0 4px 0 !important;
					padding: 0 !important;
					display: flex;
					align-items: center;
					gap: 8px;
				}
				.fed-lic-header-title p {
					color: #c7d2fe;
					font-size: 13px;
					margin: 0;
				}
				.fed-lic-header-tools {
					display: flex;
					align-items: center;
					gap: 12px;
					flex-wrap: wrap;
				}
				.fed-lic-search-wrap {
					display: flex;
					align-items: center;
					position: relative;
				}
				.fed-lic-search-toggle {
					background: rgba(255, 255, 255, 0.14);
					border: 1px solid rgba(255, 255, 255, 0.25);
					color: #ffffff;
					width: 38px;
					height: 38px;
					border-radius: 10px;
					display: flex;
					align-items: center;
					justify-content: center;
					cursor: pointer;
					transition: all 0.2s ease;
				}
				.fed-lic-search-toggle:hover,
				.fed-lic-search-toggle.is-open {
					background: #ffffff;
					color: #4338ca;
					box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
				}
				.fed-lic-search-box {
					display: none;
					align-items: center;
					position: relative;
					margin-left: 8px;
				}
				.fed-lic-search-box input {
					background: #ffffff !important;
					border: 1px solid #cbd5e1 !important;
					color: #0f172a !important;
					border-radius: 8px !important;
					padding: 7px 30px 7px 12px !important;
					font-size: 13px !important;
					width: 240px !important;
					outline: none !important;
					box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
					transition: width 0.2s ease !important;
				}
				.fed-lic-search-box input:focus {
					border-color: #6366f1 !important;
					box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
				}
				.fed-lic-search-clear {
					position: absolute;
					right: 8px;
					background: none;
					border: none;
					color: #94a3b8;
					cursor: pointer;
					font-size: 13px;
					line-height: 1;
					padding: 2px 4px;
				}
				.fed-lic-search-clear:hover {
					color: #0f172a;
				}
				.fed-lic-stat-badge {
					background: rgba(255, 255, 255, 0.12);
					border: 1px solid rgba(255, 255, 255, 0.2);
					backdrop-filter: blur(8px);
					border-radius: 8px;
					padding: 6px 14px;
					display: flex;
					align-items: center;
					gap: 8px;
				}
				.fed-lic-stat-badge .num {
					font-size: 15px;
					font-weight: 800;
					color: #ffffff;
					line-height: 1;
				}
				.fed-lic-stat-badge .lbl {
					font-size: 11px;
					text-transform: uppercase;
					letter-spacing: 0.04em;
					color: #a5b4fc;
					font-weight: 600;
				}
				.fed-lic-btn-refresh-all {
					background: #ffffff;
					color: #4338ca !important;
					border: none;
					border-radius: 8px;
					padding: 8px 16px;
					font-weight: 700;
					font-size: 12px;
					cursor: pointer;
					transition: all 0.2s ease;
					box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
					display: inline-flex;
					align-items: center;
					gap: 6px;
					text-decoration: none;
				}
				.fed-lic-btn-refresh-all:hover {
					background: #f8fafc;
					transform: translateY(-1px);
					box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2);
				}

				/* Section Header */
				.fed-lic-section {
					margin-bottom: 24px;
				}
				.fed-lic-section-header {
					display: flex;
					align-items: center;
					justify-content: space-between;
					margin-bottom: 10px;
					padding: 0 4px;
				}
				.fed-lic-section-title {
					display: flex;
					align-items: center;
					gap: 8px;
					margin: 0;
					font-size: 14px;
					font-weight: 700;
					color: #1e293b;
					text-transform: uppercase;
					letter-spacing: 0.03em;
				}
				.fed-lic-count-badge {
					font-size: 11px;
					font-weight: 700;
					padding: 2px 8px;
					border-radius: 9999px;
				}
				.fed-lic-count-badge.inactive {
					background: #fef3c7;
					color: #92400e;
					border: 1px solid #fde68a;
				}
				.fed-lic-count-badge.active {
					background: #ecfdf5;
					color: #065f46;
					border: 1px solid #a7f3d0;
				}

				/* Full Width List Table Panel */
				.fed-lic-list-panel {
					background: #ffffff;
					border: 1px solid #e2e8f0;
					border-radius: 12px;
					box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
					overflow: hidden;
				}
				.fed-lic-row {
					display: flex;
					align-items: center;
					justify-content: space-between;
					padding: 10px 18px;
					border-bottom: 1px solid #f1f5f9;
					transition: background 0.15s ease;
					gap: 16px;
					min-height: 54px;
				}
				.fed-lic-row:last-child {
					border-bottom: none;
				}
				.fed-lic-row:hover {
					background: #f8fafc;
				}

				/* Col 1: Left Brand Info */
				.fed-lic-col-info {
					display: flex;
					align-items: center;
					gap: 12px;
					flex: 1 1 32%;
					min-width: 260px;
				}
				.fed-lic-col-icon {
					width: 36px;
					height: 36px;
					border-radius: 8px;
					background: #f1f5f9;
					display: flex;
					align-items: center;
					justify-content: center;
					font-size: 18px;
					flex-shrink: 0;
				}
				.fed-lic-col-title-wrap {
					display: flex;
					flex-direction: column;
					gap: 2px;
				}
				.fed-lic-col-title-row {
					display: flex;
					align-items: center;
					gap: 8px;
					flex-wrap: wrap;
				}
				.fed-lic-col-name {
					font-size: 13.5px;
					font-weight: 700;
					color: #0f172a;
					margin: 0;
					line-height: 1.2;
				}
				.fed-lic-col-version {
					font-size: 11px;
					font-weight: 600;
					color: #64748b;
					background: #f1f5f9;
					padding: 1px 6px;
					border-radius: 4px;
					font-family: ui-monospace, SFMono-Regular, monospace;
				}
				.fed-lic-col-links {
					display: flex;
					align-items: center;
					gap: 8px;
					font-size: 11.5px;
				}
				.fed-lic-col-links a {
					color: #4f46e5;
					text-decoration: none;
					font-weight: 500;
				}
				.fed-lic-col-links a:hover {
					text-decoration: underline;
				}
				.fed-lic-col-links .sep {
					color: #cbd5e1;
				}

				/* Col 2: Key Input Field */
				.fed-lic-col-key {
					display: flex;
					flex-direction: column;
					flex: 1 1 38%;
					min-width: 280px;
					max-width: 420px;
				}
				.fed-lic-input-box {
					display: flex;
					align-items: center;
					position: relative;
					width: 100%;
				}
				.fed-lic-key-input {
					width: 100% !important;
					height: 34px !important;
					padding: 6px 36px 6px 10px !important;
					font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
					font-size: 12px !important;
					border-radius: 6px !important;
					border: 1px solid #cbd5e1 !important;
					color: #0f172a !important;
					background: #f8fafc !important;
					transition: all 0.15s ease !important;
				}
				.fed-lic-key-input:focus {
					background: #ffffff !important;
					border-color: #6366f1 !important;
					box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
					outline: none !important;
				}
				.fed-lic-toggle-visibility {
					position: absolute;
					right: 8px;
					background: none;
					border: none;
					color: #64748b;
					cursor: pointer;
					padding: 2px;
					font-size: 13px;
					line-height: 1;
				}
				.fed-lic-toggle-visibility:hover {
					color: #0f172a;
				}
				.fed-lic-msg {
					font-size: 11px;
					margin-top: 4px;
					padding: 3px 8px;
					border-radius: 4px;
					display: none;
				}
				.fed-lic-msg.is-success {
					display: block;
					background: #ecfdf5;
					color: #065f46;
					border: 1px solid #a7f3d0;
				}
				.fed-lic-msg.is-error {
					display: block;
					background: #fef2f2;
					color: #991b1b;
					border: 1px solid #fecaca;
				}

				/* Col 3: Status & Action Buttons */
				.fed-lic-col-actions {
					display: flex;
					align-items: center;
					justify-content: flex-end;
					gap: 10px;
					flex: 0 0 auto;
					min-width: 220px;
				}
				.fed-lic-badge {
					display: inline-flex;
					align-items: center;
					font-size: 10px;
					font-weight: 700;
					padding: 3px 8px;
					border-radius: 9999px;
					text-transform: uppercase;
					letter-spacing: 0.04em;
				}
				.fed-lic-badge.active {
					background: #ecfdf5;
					color: #059669;
					border: 1px solid #a7f3d0;
				}
				.fed-lic-badge.inactive {
					background: #f1f5f9;
					color: #64748b;
					border: 1px solid #e2e8f0;
				}
				.fed-lic-badge.invalid {
					background: #fef2f2;
					color: #dc2626;
					border: 1px solid #fecaca;
				}
				.fed-btn-act {
					background: #4f46e5;
					color: #ffffff !important;
					border: 1px solid #4338ca;
					border-radius: 6px;
					padding: 6px 14px;
					font-size: 12px;
					font-weight: 600;
					cursor: pointer;
					transition: all 0.15s ease;
					display: inline-flex;
					align-items: center;
					gap: 5px;
					height: 32px;
				}
				.fed-btn-act:hover {
					background: #4338ca;
				}
				.fed-btn-deact {
					background: #ffffff;
					color: #dc2626 !important;
					border: 1px solid #fca5a5;
					border-radius: 6px;
					padding: 5px 10px;
					font-size: 11.5px;
					font-weight: 600;
					cursor: pointer;
					transition: all 0.15s ease;
					height: 32px;
				}
				.fed-btn-deact:hover {
					background: #fef2f2;
					border-color: #ef4444;
				}
				.fed-btn-check {
					background: #f8fafc;
					color: #475569 !important;
					border: 1px solid #e2e8f0;
					border-radius: 6px;
					padding: 5px 10px;
					font-size: 11.5px;
					font-weight: 600;
					cursor: pointer;
					transition: all 0.15s ease;
					height: 32px;
				}
				.fed-btn-check:hover {
					background: #f1f5f9;
					color: #0f172a !important;
				}

				/* Empty State */
				.fed-lic-empty-state {
					padding: 20px;
					text-align: center;
					color: #64748b;
					font-size: 13px;
					background: #f8fafc;
					border-radius: 10px;
					border: 1px dashed #cbd5e1;
				}
				.fed-lic-spinner {
					display: none;
					width: 12px;
					height: 12px;
					border: 2px solid rgba(255, 255, 255, 0.3);
					border-radius: 50%;
					border-top-color: #ffffff;
					animation: fed-spin 0.8s linear infinite;
				}
				@keyframes fed-spin {
					to { transform: rotate(360deg); }
				}
				.is-loading .fed-lic-spinner {
					display: inline-block;
				}
				.is-loading .fed-btn-text {
					opacity: 0.7;
				}
				.fed-btn-act.is-success {
					background: #059669 !important;
					border-color: #047857 !important;
					color: #ffffff !important;
				}
				.fed-btn-deact.is-success {
					background: #475569 !important;
					border-color: #334155 !important;
					color: #ffffff !important;
				}

				/* Custom Confirmation Modal */
				.fed-lic-modal-overlay {
					position: fixed;
					inset: 0;
					background: rgba(15, 23, 42, 0.65);
					backdrop-filter: blur(4px);
					-webkit-backdrop-filter: blur(4px);
					display: flex;
					align-items: center;
					justify-content: center;
					z-index: 100001;
					opacity: 0;
					pointer-events: none;
					transition: opacity 0.2s ease-in-out;
				}
				.fed-lic-modal-overlay.show {
					opacity: 1;
					pointer-events: auto;
				}
				.fed-lic-modal-card {
					background: #ffffff;
					border-radius: 16px;
					max-width: 440px;
					width: 90%;
					padding: 24px;
					box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
					border: 1px solid #e2e8f0;
					transform: scale(0.95);
					transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
				}
				.fed-lic-modal-overlay.show .fed-lic-modal-card {
					transform: scale(1);
				}
				.fed-lic-modal-icon {
					width: 46px;
					height: 46px;
					border-radius: 12px;
					background: #fef2f2;
					color: #ef4444;
					border: 1px solid #fee2e2;
					display: flex;
					align-items: center;
					justify-content: center;
					margin-bottom: 14px;
				}
				.fed-lic-modal-card h3 {
					font-size: 17px;
					font-weight: 700;
					color: #0f172a;
					margin: 0 0 8px 0;
				}
				.fed-lic-modal-card p {
					font-size: 13px;
					color: #64748b;
					line-height: 1.55;
					margin: 0 0 22px 0;
				}
				.fed-lic-modal-actions {
					display: flex;
					align-items: center;
					justify-content: flex-end;
					gap: 10px;
				}
				.fed-lic-modal-btn-cancel {
					background: #f1f5f9;
					color: #475569;
					border: 1px solid #e2e8f0;
					padding: 8px 16px;
					border-radius: 8px;
					font-size: 13px;
					font-weight: 600;
					cursor: pointer;
					transition: all 0.15s ease;
				}
				.fed-lic-modal-btn-cancel:hover {
					background: #e2e8f0;
					color: #0f172a;
				}
				.fed-lic-modal-btn-confirm {
					background: #dc2626;
					color: #ffffff !important;
					border: 1px solid #b91c1c;
					padding: 8px 18px;
					border-radius: 8px;
					font-size: 13px;
					font-weight: 600;
					cursor: pointer;
					box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
					transition: all 0.15s ease;
				}
				.fed-lic-modal-btn-confirm:hover {
					background: #b91c1c;
				}

				/* Toast Floating Notifications */
				.fed-lic-toast-container {
					position: fixed;
					top: 42px;
					right: 28px;
					z-index: 100005;
					display: flex;
					flex-direction: column;
					gap: 10px;
					pointer-events: none;
				}
				.fed-lic-toast {
					background: #ffffff;
					border-radius: 12px;
					box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.18), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
					border: 1px solid #e2e8f0;
					padding: 12px 18px;
					display: flex;
					align-items: center;
					gap: 12px;
					font-size: 13px;
					font-weight: 600;
					color: #1e293b;
					pointer-events: auto;
					transform: translateY(-20px);
					opacity: 0;
					transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
					max-width: 380px;
				}
				.fed-lic-toast.show {
					transform: translateY(0);
					opacity: 1;
				}
				.fed-lic-toast.toast-success {
					border-left: 4px solid #10b981;
				}
				.fed-lic-toast.toast-error {
					border-left: 4px solid #ef4444;
				}
				.fed-lic-toast.toast-info {
					border-left: 4px solid #4f46e5;
				}
				.fed-lic-toast-icon {
					display: flex;
					align-items: center;
					justify-content: center;
					font-size: 16px;
				}
				.fed-lic-toast-text {
					flex: 1;
					line-height: 1.4;
				}
			</style>

			<!-- Toast Container -->
			<div class="fed-lic-toast-container" id="fed_lic_toast_container"></div>

			<!-- Default Popup Confirm Modal -->
			<div id="fed_lic_confirm_modal" class="fed-lic-modal-overlay">
				<div class="fed-lic-modal-card" role="dialog" aria-modal="true">
					<div class="fed-lic-modal-icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
					</div>
					<h3 id="fed_lic_modal_title"><?php esc_html_e( 'Deactivate Extension License?', 'frontend-dashboard' ); ?></h3>
					<p id="fed_lic_modal_msg"><?php esc_html_e( 'Are you sure you want to deactivate this license? Automated updates and verified features will be suspended until reactivated.', 'frontend-dashboard' ); ?></p>
					<div class="fed-lic-modal-actions">
						<button type="button" class="fed-lic-modal-btn-cancel" id="fed_lic_modal_cancel">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" class="fed-lic-modal-btn-confirm" id="fed_lic_modal_confirm">
							<?php esc_html_e( 'Confirm Deactivate', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Header Summary Banner -->
			<div class="fed-lic-header">
				<div class="fed-lic-header-title">
					<h1><span>🔑</span> <?php esc_html_e( 'Pro Licenses & Updates Hub', 'frontend-dashboard' ); ?></h1>
					<p><?php printf( esc_html__( 'Manage your verified license keys and automated updates for active domain: %s', 'frontend-dashboard' ), '<strong>' . esc_html( $site_domain ) . '</strong>' ); ?></p>
				</div>
				<div class="fed-lic-header-tools">
					<!-- Expandable Search Box -->
					<div class="fed-lic-search-wrap">
						<button type="button" class="fed-lic-search-toggle" id="fed-btn-toggle-search" title="<?php esc_attr_e( 'Search Extensions (Click to open)', 'frontend-dashboard' ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
						</button>
						<div class="fed-lic-search-box" id="fed-search-box">
							<input type="text" id="fed-search-input" placeholder="<?php esc_attr_e( 'Search extensions...', 'frontend-dashboard' ); ?>" autocomplete="off" />
							<button type="button" class="fed-lic-search-clear" id="fed-btn-clear-search" title="<?php esc_attr_e( 'Clear Search', 'frontend-dashboard' ); ?>">✕</button>
						</div>
					</div>

					<div class="fed-lic-stat-badge">
						<span class="num"><?php echo esc_html( $total_addons ); ?></span>
						<span class="lbl"><?php esc_html_e( 'Extensions', 'frontend-dashboard' ); ?></span>
					</div>
					<div class="fed-lic-stat-badge">
						<span class="num" id="fed-stat-active-num"><?php echo esc_html( $active_licenses ); ?></span>
						<span class="lbl"><?php esc_html_e( 'Active', 'frontend-dashboard' ); ?></span>
					</div>
					<button type="button" class="fed-lic-btn-refresh-all" id="fed-btn-check-all-updates">
						<span class="fed-lic-spinner"></span>
						<span>🔄 <?php esc_html_e( 'Check Updates', 'frontend-dashboard' ); ?></span>
					</button>
				</div>
			</div>

			<!-- SECTION 1: INACTIVE LICENSES (TOP) - Shown only when there are extensions needing activation -->
			<?php if ( ! empty( $inactive_addons ) ) : ?>
			<div class="fed-lic-section" id="fed-section-inactive">
				<div class="fed-lic-section-header">
					<h3 class="fed-lic-section-title">
						<span>⏳</span> <?php esc_html_e( 'Inactive Licenses', 'frontend-dashboard' ); ?>
						<span class="fed-lic-count-badge inactive"><?php echo esc_html( $inactive_count ); ?></span>
					</h3>
				</div>

				<div class="fed-lic-list-panel">
					<?php
					foreach ( $inactive_addons as $slug => $addon ) :
						$license   = $addon['license'];
						$key       = ! empty( $license['key'] ) ? $license['key'] : '';
						$status    = $addon['status'];
						$message   = ! empty( $license['message'] ) ? $license['message'] : '';
						?>
						<div class="fed-lic-row" id="fed-card-<?php echo esc_attr( $slug ); ?>" data-slug="<?php echo esc_attr( $slug ); ?>" data-name="<?php echo esc_attr( strtolower( $addon['name'] ) ); ?>">
							<!-- Col 1: Brand Info -->
							<div class="fed-lic-col-info">
								<div class="fed-lic-col-icon"><?php echo esc_html( $addon['icon'] ); ?></div>
								<div class="fed-lic-col-title-wrap">
									<div class="fed-lic-col-title-row">
										<h4 class="fed-lic-col-name"><?php echo esc_html( $addon['name'] ); ?></h4>
										<span class="fed-lic-col-version"><?php echo esc_html( 'v' . $addon['version'] ); ?></span>
									</div>
									<div class="fed-lic-col-links">
										<?php if ( ! empty( $addon['purchase_url'] ) ) : ?>
											<a href="<?php echo esc_url( $addon['purchase_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Buy License ↗', 'frontend-dashboard' ); ?></a>
										<?php endif; ?>
										<?php if ( ! empty( $addon['doc_url'] ) ) : ?>
											<span class="sep">•</span>
											<a href="<?php echo esc_url( $addon['doc_url'] ); ?>" target="_blank" rel="noopener noreferrer" style="color: #64748b;"><?php esc_html_e( 'Docs ↗', 'frontend-dashboard' ); ?></a>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<!-- Col 2: Key Input -->
							<div class="fed-lic-col-key">
								<div class="fed-lic-input-box">
									<input 
										type="password" 
										id="fed-key-<?php echo esc_attr( $slug ); ?>" 
										class="fed-lic-key-input" 
										value="<?php echo esc_attr( $key ); ?>" 
										placeholder="<?php esc_attr_e( 'Enter Transaction ID / Key...', 'frontend-dashboard' ); ?>"
										autocomplete="off"
									/>
									<button type="button" class="fed-lic-toggle-visibility" title="<?php esc_attr_e( 'Toggle Visibility', 'frontend-dashboard' ); ?>" onclick="fedToggleKeyVisibility('<?php echo esc_attr( $slug ); ?>')">👁️</button>
								</div>
								<div class="fed-lic-msg <?php echo esc_attr( ! empty( $message ) ? 'is-error' : '' ); ?>" id="fed-msg-<?php echo esc_attr( $slug ); ?>">
									<?php echo esc_html( $message ); ?>
								</div>
							</div>

							<!-- Col 3: Status & Action -->
							<div class="fed-lic-col-actions">
								<span class="fed-lic-badge <?php echo esc_attr( $status ); ?>" id="fed-badge-<?php echo esc_attr( $slug ); ?>">
									<?php echo esc_html( 'invalid' === $status ? __( 'Invalid', 'frontend-dashboard' ) : __( 'Inactive', 'frontend-dashboard' ) ); ?>
								</span>
								<button type="button" class="fed-btn-act" onclick="fedActivateLicense('<?php echo esc_attr( $slug ); ?>', '<?php echo esc_attr( addslashes( $addon['name'] ) ); ?>')">
									<span class="fed-lic-spinner"></span>
									<span class="fed-btn-text">⚡ <?php esc_html_e( 'Activate', 'frontend-dashboard' ); ?></span>
								</button>
							</div>
						</div>
					<?php endforeach; ?>
					<div id="fed-search-empty-inactive" style="display: none; padding: 16px; text-align: center; color: #64748b; font-size: 13px;">
						<?php esc_html_e( 'No inactive extensions match your search.', 'frontend-dashboard' ); ?>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<!-- SECTION 2: ACTIVE LICENSES (BOTTOM) - Shown only when active licenses exist -->
			<?php if ( ! empty( $active_addons ) ) : ?>
			<div class="fed-lic-section" id="fed-section-active">
				<div class="fed-lic-section-header">
					<h3 class="fed-lic-section-title">
						<span>🛡️</span> <?php esc_html_e( 'Active Licenses', 'frontend-dashboard' ); ?>
						<span class="fed-lic-count-badge active"><?php echo esc_html( $active_licenses ); ?></span>
					</h3>
				</div>

				<div class="fed-lic-list-panel">
						<?php
						foreach ( $active_addons as $slug => $addon ) :
							$license = $addon['license'];
							$key     = ! empty( $license['key'] ) ? $license['key'] : '';
							$message = ! empty( $license['message'] ) ? $license['message'] : '';
							?>
							<div class="fed-lic-row" id="fed-card-<?php echo esc_attr( $slug ); ?>" data-slug="<?php echo esc_attr( $slug ); ?>" data-name="<?php echo esc_attr( strtolower( $addon['name'] ) ); ?>">
								<!-- Col 1: Brand Info -->
								<div class="fed-lic-col-info">
									<div class="fed-lic-col-icon"><?php echo esc_html( $addon['icon'] ); ?></div>
									<div class="fed-lic-col-title-wrap">
										<div class="fed-lic-col-title-row">
											<h4 class="fed-lic-col-name"><?php echo esc_html( $addon['name'] ); ?></h4>
											<span class="fed-lic-col-version"><?php echo esc_html( 'v' . $addon['version'] ); ?></span>
										</div>
										<div class="fed-lic-col-links">
											<?php if ( ! empty( $addon['renew_url'] ) ) : ?>
												<a href="<?php echo esc_url( $addon['renew_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Renew ↗', 'frontend-dashboard' ); ?></a>
												<span class="sep">•</span>
											<?php endif; ?>
											<?php if ( ! empty( $addon['doc_url'] ) ) : ?>
												<a href="<?php echo esc_url( $addon['doc_url'] ); ?>" target="_blank" rel="noopener noreferrer" style="color: #64748b;"><?php esc_html_e( 'Docs ↗', 'frontend-dashboard' ); ?></a>
											<?php endif; ?>
											<?php if ( ! empty( $addon['settings_url'] ) ) : ?>
												<span class="sep">•</span>
												<a href="<?php echo esc_url( $addon['settings_url'] ); ?>" style="color: #4f46e5; font-weight: 700; text-decoration: none;">
													<span>⚙️ <?php esc_html_e( 'Configure Settings →', 'frontend-dashboard' ); ?></span>
												</a>
											<?php endif; ?>
										</div>
									</div>
								</div>

								<!-- Col 2: Key Display -->
								<div class="fed-lic-col-key">
									<div class="fed-lic-input-box">
										<input 
											type="password" 
											id="fed-key-<?php echo esc_attr( $slug ); ?>" 
											class="fed-lic-key-input" 
											value="<?php echo esc_attr( $key ); ?>" 
											readonly
										/>
										<button type="button" class="fed-lic-toggle-visibility" title="<?php esc_attr_e( 'Toggle Visibility', 'frontend-dashboard' ); ?>" onclick="fedToggleKeyVisibility('<?php echo esc_attr( $slug ); ?>')">👁️</button>
									</div>
									<div class="fed-lic-msg is-success" id="fed-msg-<?php echo esc_attr( $slug ); ?>">
										<?php echo esc_html( $message ? $message : __( 'License verified and active on this domain.', 'frontend-dashboard' ) ); ?>
									</div>
								</div>

								<!-- Col 3: Status & Actions -->
								<div class="fed-lic-col-actions">
									<span class="fed-lic-badge active" id="fed-badge-<?php echo esc_attr( $slug ); ?>">
										<?php esc_html_e( 'Active', 'frontend-dashboard' ); ?>
									</span>
									<button type="button" class="fed-btn-deact" onclick="fedPromptDeactivate('<?php echo esc_attr( $slug ); ?>', '<?php echo esc_attr( addslashes( $addon['name'] ) ); ?>')">
										<span class="fed-lic-spinner"></span>
										<span class="fed-btn-text"><?php esc_html_e( 'Deactivate', 'frontend-dashboard' ); ?></span>
									</button>
									<button type="button" class="fed-btn-check" onclick="fedRefreshLicense('<?php echo esc_attr( $slug ); ?>', '<?php echo esc_attr( addslashes( $addon['name'] ) ); ?>')">
										<span class="fed-lic-spinner"></span>
										<span class="fed-btn-text">🔄 <?php esc_html_e( 'Refresh', 'frontend-dashboard' ); ?></span>
									</button>
								</div>
							</div>
						<?php endforeach; ?>
						<div id="fed-search-empty-active" style="display: none; padding: 16px; text-align: center; color: #64748b; font-size: 13px;">
							<?php esc_html_e( 'No active extensions match your search.', 'frontend-dashboard' ); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( empty( $inactive_addons ) && empty( $active_addons ) ) : ?>
				<div class="fed-lic-empty-state" style="padding: 36px 20px; margin-top: 16px;">
					<span style="font-size: 14px;">✨ <?php esc_html_e( 'No extensions currently detected. When an extension is activated, its license settings will appear here.', 'frontend-dashboard' ); ?></span>
				</div>
			<?php endif; ?>

		</div>

		<!-- Client JavaScript -->
		<script>
			const fedLicNonce = '<?php echo esc_js( $nonce ); ?>';
			const fedAjaxUrl  = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';

			// Toast System
			function fedShowToast(type, text, duration = 3000) {
				const container = document.getElementById('fed_lic_toast_container');
				if (!container) return;

				const toast = document.createElement('div');
				toast.className = 'fed-lic-toast toast-' + type;

				let iconSvg = 'ℹ️';
				if (type === 'success') {
					iconSvg = '✅';
				} else if (type === 'error') {
					iconSvg = '❌';
				}

				toast.innerHTML = '<span class="fed-lic-toast-icon">' + iconSvg + '</span><span class="fed-lic-toast-text">' + text + '</span>';
				container.appendChild(toast);

				// Trigger animation
				setTimeout(() => toast.classList.add('show'), 10);

				setTimeout(() => {
					toast.classList.remove('show');
					setTimeout(() => toast.remove(), 250);
				}, duration);
			}

			// Modal System
			let fedPendingDeactivateSlug = null;
			const fedConfirmModal = document.getElementById('fed_lic_confirm_modal');
			const fedModalCancel  = document.getElementById('fed_lic_modal_cancel');
			const fedModalConfirm = document.getElementById('fed_lic_modal_confirm');
			const fedModalMsg     = document.getElementById('fed_lic_modal_msg');

			function fedPromptDeactivate(slug, addonName) {
				fedPendingDeactivateSlug = slug;
				if (fedModalMsg) {
					fedModalMsg.textContent = '<?php echo esc_js( __( 'Are you sure you want to deactivate the license for', 'frontend-dashboard' ) ); ?> "' + addonName + '"? <?php echo esc_js( __( 'Automated updates and verified features will be suspended until reactivated.', 'frontend-dashboard' ) ); ?>';
				}
				if (fedConfirmModal) {
					fedConfirmModal.classList.add('show');
				}
			}

			function fedCloseModal() {
				if (fedConfirmModal) {
					fedConfirmModal.classList.remove('show');
				}
				fedPendingDeactivateSlug = null;
			}

			fedModalCancel?.addEventListener('click', fedCloseModal);
			fedConfirmModal?.addEventListener('click', function(e) {
				if (e.target === fedConfirmModal) {
					fedCloseModal();
				}
			});

			fedModalConfirm?.addEventListener('click', function() {
				if (fedPendingDeactivateSlug) {
					const slug = fedPendingDeactivateSlug;
					fedCloseModal();
					fedExecuteDeactivate(slug);
				}
			});

			// Search Toggle & Filter
			const fedSearchToggle = document.getElementById('fed-btn-toggle-search');
			const fedSearchBox    = document.getElementById('fed-search-box');
			const fedSearchInput  = document.getElementById('fed-search-input');
			const fedSearchClear  = document.getElementById('fed-btn-clear-search');

			fedSearchToggle?.addEventListener('click', function() {
				if (fedSearchBox.style.display === 'none' || !fedSearchBox.style.display) {
					fedSearchBox.style.display = 'flex';
					fedSearchToggle.classList.add('is-open');
					fedSearchInput.focus();
				} else {
					fedSearchBox.style.display = 'none';
					fedSearchToggle.classList.remove('is-open');
					fedSearchInput.value = '';
					fedFilterRows('');
				}
			});

			fedSearchClear?.addEventListener('click', function() {
				fedSearchInput.value = '';
				fedFilterRows('');
				fedSearchInput.focus();
			});

			fedSearchInput?.addEventListener('input', function() {
				fedFilterRows(this.value.trim().toLowerCase());
			});

			function fedFilterRows(query) {
				let inactiveVisible = 0;
				let activeVisible   = 0;

				document.querySelectorAll('.fed-lic-row').forEach(row => {
					const name = (row.getAttribute('data-name') || '').toLowerCase();
					const slug = (row.getAttribute('data-slug') || '').toLowerCase();
					const matches = !query || name.includes(query) || slug.includes(query);

					if (matches) {
						row.style.display = 'flex';
						if (row.closest('#fed-section-inactive')) inactiveVisible++;
						if (row.closest('#fed-section-active')) activeVisible++;
					} else {
						row.style.display = 'none';
					}
				});

				const emptyInactive = document.getElementById('fed-search-empty-inactive');
				const emptyActive   = document.getElementById('fed-search-empty-active');
				if (emptyInactive) {
					emptyInactive.style.display = (query && inactiveVisible === 0) ? 'block' : 'none';
				}
				if (emptyActive) {
					emptyActive.style.display = (query && activeVisible === 0) ? 'block' : 'none';
				}
			}

			function fedToggleKeyVisibility(slug) {
				const input = document.getElementById('fed-key-' + slug);
				if (input) {
					input.type = input.type === 'password' ? 'text' : 'password';
				}
			}

			function fedSetLoading(slug, btnSelector, loading, text) {
				const card = document.getElementById('fed-card-' + slug);
				if (!card) return;
				const btn = card.querySelector(btnSelector);
				if (btn) {
					const btnText = btn.querySelector('.fed-btn-text');
					if (loading) {
						btn.classList.add('is-loading');
						btn.disabled = true;
						if (text && btnText) btnText.textContent = text;
					} else {
						btn.classList.remove('is-loading');
						btn.disabled = false;
						if (text && btnText) btnText.textContent = text;
					}
				}
			}

			// Activation Flow
			function fedActivateLicense(slug, addonName) {
				const input = document.getElementById('fed-key-' + slug);
				const key = input ? input.value.trim() : '';
				const msgEl = document.getElementById('fed-msg-' + slug);

				if (!key) {
					if (msgEl) {
						msgEl.className = 'fed-lic-msg is-error';
						msgEl.textContent = '<?php echo esc_js( __( 'Please enter a license key / transaction ID.', 'frontend-dashboard' ) ); ?>';
					}
					if (input) input.focus();
					fedShowToast('error', '<?php echo esc_js( __( 'Please enter a license key / transaction ID.', 'frontend-dashboard' ) ); ?>');
					return;
				}

				fedSetLoading(slug, '.fed-btn-act', true, '<?php echo esc_js( __( 'Verifying...', 'frontend-dashboard' ) ); ?>');
				fedShowToast('info', '<?php echo esc_js( __( 'Connecting to BufferCode to verify license...', 'frontend-dashboard' ) ); ?>');

				const data = new FormData();
				data.append('action', 'fed_activate_addon_license');
				data.append('nonce', fedLicNonce);
				data.append('slug', slug);
				data.append('key', key);

				fetch(fedAjaxUrl, {
					method: 'POST',
					body: data
				})
				.then(res => res.json())
				.then(res => {
					if (res.success) {
						// Success state
						const card = document.getElementById('fed-card-' + slug);
						const btn  = card ? card.querySelector('.fed-btn-act') : null;
						if (btn) {
							btn.classList.remove('is-loading');
							btn.classList.add('is-success');
							const btnText = btn.querySelector('.fed-btn-text');
							if (btnText) btnText.textContent = '✓ <?php echo esc_js( __( 'Verified & Active!', 'frontend-dashboard' ) ); ?>';
						}
						if (msgEl) {
							msgEl.className = 'fed-lic-msg is-success';
							msgEl.textContent = '✓ ' + (res.data && res.data.message ? res.data.message : '<?php echo esc_js( __( 'License activated successfully!', 'frontend-dashboard' ) ); ?>');
						}

						fedShowToast('success', '✓ <?php echo esc_js( __( 'License activated successfully! Reloading...', 'frontend-dashboard' ) ); ?>');
						setTimeout(() => location.reload(), 1200);
					} else {
						// Error state
						fedSetLoading(slug, '.fed-btn-act', false, '⚡ <?php echo esc_js( __( 'Activate', 'frontend-dashboard' ) ); ?>');
						const errorMsg = res.data && res.data.message ? res.data.message : '<?php echo esc_js( __( 'Activation failed.', 'frontend-dashboard' ) ); ?>';
						if (msgEl) {
							msgEl.className = 'fed-lic-msg is-error';
							msgEl.textContent = '✕ ' + errorMsg;
						}
						fedShowToast('error', errorMsg);
					}
				})
				.catch(err => {
					fedSetLoading(slug, '.fed-btn-act', false, '⚡ <?php echo esc_js( __( 'Activate', 'frontend-dashboard' ) ); ?>');
					if (msgEl) {
						msgEl.className = 'fed-lic-msg is-error';
						msgEl.textContent = '✕ <?php echo esc_js( __( 'Network error during license activation.', 'frontend-dashboard' ) ); ?>';
					}
					fedShowToast('error', '<?php echo esc_js( __( 'Network error during license activation.', 'frontend-dashboard' ) ); ?>');
				});
			}

			// Deactivation Flow
			function fedExecuteDeactivate(slug) {
				fedSetLoading(slug, '.fed-btn-deact', true, '<?php echo esc_js( __( 'Deactivating...', 'frontend-dashboard' ) ); ?>');
				fedShowToast('info', '<?php echo esc_js( __( 'Deactivating license with BufferCode...', 'frontend-dashboard' ) ); ?>');

				const data = new FormData();
				data.append('action', 'fed_deactivate_addon_license');
				data.append('nonce', fedLicNonce);
				data.append('slug', slug);

				fetch(fedAjaxUrl, {
					method: 'POST',
					body: data
				})
				.then(res => res.json())
				.then(res => {
					const card = document.getElementById('fed-card-' + slug);
					const btn  = card ? card.querySelector('.fed-btn-deact') : null;
					if (btn) {
						btn.classList.remove('is-loading');
						btn.classList.add('is-success');
						const btnText = btn.querySelector('.fed-btn-text');
						if (btnText) btnText.textContent = '✓ <?php echo esc_js( __( 'Deactivated', 'frontend-dashboard' ) ); ?>';
					}
					fedShowToast('success', '✓ <?php echo esc_js( __( 'License deactivated successfully! Reloading...', 'frontend-dashboard' ) ); ?>');
					setTimeout(() => location.reload(), 1200);
				})
				.catch(err => {
					fedSetLoading(slug, '.fed-btn-deact', false, '<?php echo esc_js( __( 'Deactivate', 'frontend-dashboard' ) ); ?>');
					fedShowToast('error', '<?php echo esc_js( __( 'Failed to deactivate license.', 'frontend-dashboard' ) ); ?>');
				});
			}

			// Refresh Status Flow
			function fedRefreshLicense(slug, addonName) {
				fedSetLoading(slug, '.fed-btn-check', true, '🔄 <?php echo esc_js( __( 'Refreshing...', 'frontend-dashboard' ) ); ?>');
				fedShowToast('info', '<?php echo esc_js( __( 'Checking license status with BufferCode...', 'frontend-dashboard' ) ); ?>');

				const data = new FormData();
				data.append('action', 'fed_refresh_addon_license');
				data.append('nonce', fedLicNonce);
				data.append('slug', slug);

				fetch(fedAjaxUrl, {
					method: 'POST',
					body: data
				})
				.then(res => res.json())
				.then(res => {
					if (res.success) {
						fedShowToast('success', '✓ <?php echo esc_js( __( 'License status refreshed! Reloading...', 'frontend-dashboard' ) ); ?>');
						setTimeout(() => location.reload(), 1200);
					} else {
						fedSetLoading(slug, '.fed-btn-check', false, '🔄 <?php echo esc_js( __( 'Refresh', 'frontend-dashboard' ) ); ?>');
						const errorMsg = res.data && res.data.message ? res.data.message : '<?php echo esc_js( __( 'Failed to refresh status.', 'frontend-dashboard' ) ); ?>';
						fedShowToast('error', errorMsg);
					}
				})
				.catch(err => {
					fedSetLoading(slug, '.fed-btn-check', false, '🔄 <?php echo esc_js( __( 'Refresh', 'frontend-dashboard' ) ); ?>');
					fedShowToast('error', '<?php echo esc_js( __( 'Network error refreshing license.', 'frontend-dashboard' ) ); ?>');
				});
			}

			// Global Check Updates
			document.getElementById('fed-btn-check-all-updates')?.addEventListener('click', function() {
				const btn = this;
				btn.classList.add('is-loading');
				btn.disabled = true;
				fedShowToast('info', '<?php echo esc_js( __( 'Checking for extension updates with BufferCode...', 'frontend-dashboard' ) ); ?>');

				const data = new FormData();
				data.append('action', 'fed_check_all_updates');
				data.append('nonce', fedLicNonce);

				fetch(fedAjaxUrl, {
					method: 'POST',
					body: data
				})
				.then(res => res.json())
				.then(res => {
					btn.classList.remove('is-loading');
					btn.disabled = false;
					const msg = res.data && res.data.message ? res.data.message : '<?php echo esc_js( __( 'Update check completed.', 'frontend-dashboard' ) ); ?>';
					fedShowToast('success', '✓ ' + msg);
					setTimeout(() => location.reload(), 1200);
				})
				.catch(err => {
					btn.classList.remove('is-loading');
					btn.disabled = false;
					fedShowToast('error', '<?php echo esc_js( __( 'Error checking for updates.', 'frontend-dashboard' ) ); ?>');
				});
			});
		</script>
		<?php
	}
}
