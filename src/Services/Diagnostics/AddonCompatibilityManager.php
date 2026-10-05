<?php

namespace FED\Services\Diagnostics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AddonCompatibilityManager
 *
 * Validates version compatibility for all active Frontend Dashboard add-ons.
 * Ensures all add-ons are >= 3.0.0 and alerts administrators via header notices and admin banners.
 */
class AddonCompatibilityManager {

	/**
	 * Minimum required version for all Frontend Dashboard add-ons.
	 */
	const MIN_REQUIRED_VERSION = '3.0.0';

	/**
	 * Singleton instance.
	 *
	 * @var AddonCompatibilityManager|null
	 */
	private static $instance = null;

	/**
	 * Cached list of incompatible add-ons.
	 *
	 * @var array|null
	 */
	private $incompatible_addons = null;

	/**
	 * Get singleton instance.
	 *
	 * @return AddonCompatibilityManager
	 */
	public static function instance(): AddonCompatibilityManager {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @param \FED\Hooks\HookLoader|null $loader
	 */
	public function register_hooks( $loader = null ) {
		if ( $loader ) {
			$loader->add_action( 'admin_notices', $this, 'render_admin_notice' );
			$loader->add_action( 'network_admin_notices', $this, 'render_admin_notice' );
		} else {
			add_action( 'admin_notices', array( $this, 'render_admin_notice' ) );
			add_action( 'network_admin_notices', array( $this, 'render_admin_notice' ) );
		}
	}

	/**
	 * Check if any active add-ons are incompatible (< 3.0.0).
	 *
	 * @return bool
	 */
	public function has_incompatible_addons(): bool {
		$incompatible = $this->get_incompatible_addons();
		return ! empty( $incompatible );
	}

	/**
	 * Get list of all currently active incompatible add-ons.
	 *
	 * @param bool $force_refresh
	 * @return array Array of incompatible add-on details.
	 */
	public function get_incompatible_addons( bool $force_refresh = false ): array {
		if ( null !== $this->incompatible_addons && ! $force_refresh ) {
			return $this->incompatible_addons;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins  = get_plugins();
		$incompatible = array();

		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			// Skip Frontend Dashboard Core
			if ( 'frontend-dashboard/frontend-dashboard.php' === $plugin_file || ( 'frontend-dashboard.php' === basename( $plugin_file ) && 'frontend-dashboard' === dirname( $plugin_file ) ) ) {
				continue;
			}

			// Check if plugin is an active Frontend Dashboard add-on
			if ( ! $this->is_fed_addon( $plugin_file, $plugin_data ) ) {
				continue;
			}

			if ( ! is_plugin_active( $plugin_file ) ) {
				continue;
			}

			$version = $this->resolve_addon_version( $plugin_file, $plugin_data );

			// Check if version is less than 3.0.0
			if ( empty( $version ) || version_compare( $version, self::MIN_REQUIRED_VERSION, '<' ) ) {
				$slug                         = dirname( $plugin_file );
				$incompatible[ $plugin_file ] = array(
					'name'             => ! empty( $plugin_data['Name'] ) ? $plugin_data['Name'] : $slug,
					'slug'             => $slug,
					'file'             => $plugin_file,
					'current_version'  => ! empty( $version ) ? $version : __( 'Unknown', 'frontend-dashboard' ),
					'required_version' => self::MIN_REQUIRED_VERSION,
					'plugin_uri'       => ! empty( $plugin_data['PluginURI'] ) ? $plugin_data['PluginURI'] : 'https://buffercode.com',
				);
			}
		}

		$this->incompatible_addons = $incompatible;
		return $this->incompatible_addons;
	}

	/**
	 * Determine if a given plugin is an official Frontend Dashboard add-on.
	 *
	 * @param string $plugin_file
	 * @param array  $plugin_data
	 * @return bool
	 */
	private function is_fed_addon( string $plugin_file, array $plugin_data ): bool {
		$slug = dirname( $plugin_file );
		$name = isset( $plugin_data['Name'] ) ? $plugin_data['Name'] : '';

		// 1. Directory prefix matches frontend-dashboard-
		if ( 0 === strpos( $slug, 'frontend-dashboard-' ) ) {
			return true;
		}

		// 2. Name contains Frontend Dashboard
		if ( false !== stripos( $name, 'Frontend Dashboard' ) && false === stripos( $name, 'Frontend Dashboard Core' ) ) {
			return true;
		}

		// 3. Check known catalog slugs
		if ( function_exists( 'fed_get_addons_catalog' ) ) {
			$catalog = fed_get_addons_catalog();
			if ( isset( $catalog[ $slug ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Resolve the exact version of an add-on from headers or defined constants.
	 *
	 * @param string $plugin_file
	 * @param array  $plugin_data
	 * @return string
	 */
	private function resolve_addon_version( string $plugin_file, array $plugin_data ): string {
		// 1. Try defined PHP constants for specific add-ons
		$slug         = dirname( $plugin_file );
		$constant_map = array(
			'frontend-dashboard-extra'           => 'BC_FED_EXTRA_PLUGIN_VERSION',
			'frontend-dashboard-social-connect'  => 'BC_FED_SC_PLUGIN_VERSION',
			'frontend-dashboard-user-management' => 'BC_FED_UM_PLUGIN_VERSION',
			'frontend-dashboard-notification'    => 'BC_FED_NTF_PLUGIN_VERSION',
			'frontend-dashboard-custom-post'     => 'BC_FED_CP_PLUGIN_VERSION',
			'frontend-dashboard-templates'       => 'BC_FED_TEMPLATES_PLUGIN_VERSION',
			'frontend-dashboard-pages'           => 'BC_FED_PAGES_PLUGIN_VERSION',
			'frontend-dashboard-social-chat'     => 'BC_FED_SCHAT_PLUGIN_VERSION',
			'frontend-dashboard-captcha'         => 'BC_FED_CAPTCHA_PLUGIN_VERSION',
			'frontend-dashboard-message'         => 'BC_FED_MSG_PLUGIN_VERSION',
		);

		if ( isset( $constant_map[ $slug ] ) && defined( $constant_map[ $slug ] ) ) {
			return (string) constant( $constant_map[ $slug ] );
		}

		// 2. Try plugin header version
		if ( ! empty( $plugin_data['Version'] ) ) {
			return (string) $plugin_data['Version'];
		}

		return '1.0.0';
	}

	/**
	 * Render WordPress Admin Notice in Header.
	 */
	public function render_admin_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$incompatible = $this->get_incompatible_addons();
		if ( empty( $incompatible ) ) {
			return;
		}

		$addons_url  = admin_url( 'admin.php?page=fed_plugin_pages' );
		$plugins_url = admin_url( 'plugins.php' );
		?>
		<div class="notice notice-error is-dismissible fed-compatibility-notice" style="border-left-color: #ef4444; background: #ffffff; padding: 16px 20px; border-radius: 12px; box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.08); margin: 20px 20px 20px 0;">
			<div style="display: flex; align-items: flex-start; gap: 14px;">
				<div style="width: 40px; height: 40px; border-radius: 10px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
					<span class="dashicons dashicons-warning" style="font-size: 24px; width: 24px; height: 24px;"></span>
				</div>
				<div style="flex: 1;">
					<div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
						<h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #1e293b;">
							<?php esc_html_e( 'Frontend Dashboard — Incompatible Add-on(s) Detected', 'frontend-dashboard' ); ?>
						</h3>
						<span style="background: #ef4444; color: #ffffff; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
							<?php esc_html_e( 'v3.0.0+ Required', 'frontend-dashboard' ); ?>
						</span>
					</div>
					<p style="margin: 0 0 10px 0; color: #475569; font-size: 13px; line-height: 1.5;">
						<?php
						printf(
							/* translators: 1: Frontend Dashboard version, 2: Required version */
							esc_html__( 'Frontend Dashboard v%1$s introduces major architectural enhancements. All related add-ons must be updated to version %2$s or higher to ensure compatibility and avoid errors.', 'frontend-dashboard' ),
							'<strong>' . esc_html( BC_FED_PLUGIN_VERSION ) . '</strong>',
							'<strong>' . esc_html( self::MIN_REQUIRED_VERSION ) . '</strong>'
						);
						?>
					</p>

					<div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px;">
						<?php foreach ( $incompatible as $addon ) : ?>
							<div style="display: inline-flex; align-items: center; gap: 6px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 4px 10px; font-size: 12px; color: #991b1b;">
								<strong style="color: #1e293b;"><?php echo esc_html( $addon['name'] ); ?></strong>
								<span style="color: #dc2626; background: #ffffff; padding: 1px 6px; border-radius: 4px; border: 1px solid #fca5a5; font-size: 11px; font-weight: 600;">
									<?php
									/* translators: %s: installed version number */
									echo esc_html( sprintf( __( 'Installed: v%s', 'frontend-dashboard' ), $addon['current_version'] ) );
									?>
								</span>
								<span style="color: #15803d; font-size: 11px; font-weight: 600;">
									<?php
									/* translators: %s: required version number */
									echo esc_html( sprintf( __( '→ Requires: v%s+', 'frontend-dashboard' ), $addon['required_version'] ) );
									?>
								</span>
							</div>
						<?php endforeach; ?>
					</div>

					<div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
						<a href="<?php echo esc_url( $addons_url ); ?>" class="button button-primary" style="background: #4f46e5; border-color: #4338ca; color: #ffffff; font-weight: 600; border-radius: 8px; padding: 4px 14px; text-decoration: none;">
							<?php esc_html_e( 'Manage Add-ons', 'frontend-dashboard' ); ?>
						</a>
						<a href="<?php echo esc_url( $plugins_url ); ?>" class="button" style="border-radius: 8px; font-weight: 500;">
							<?php esc_html_e( 'View Installed Plugins', 'frontend-dashboard' ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render In-App Header Banner inside Frontend Dashboard custom admin pages.
	 *
	 * @return string HTML banner markup.
	 */
	public function render_in_app_banner(): string {
		$incompatible = $this->get_incompatible_addons();
		if ( empty( $incompatible ) ) {
			return '';
		}

		$addons_url = admin_url( 'admin.php?page=fed_plugin_pages' );

		ob_start();
		?>
		<div class="mb-6 rounded-2xl bg-gradient-to-r from-rose-500/10 via-amber-500/10 to-rose-500/5 border border-rose-200 p-5 sm:p-6 shadow-xs relative overflow-hidden">
			<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
				<div class="flex items-start gap-4">
					<div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
						<i class="fas fa-exclamation-triangle text-base"></i>
					</div>
					<div>
						<div class="flex items-center gap-2.5 flex-wrap">
							<h3 class="text-sm font-bold text-slate-900 m-0">
								<?php esc_html_e( 'Incompatible Add-on(s) Detected', 'frontend-dashboard' ); ?>
							</h3>
							<span class="inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-bold bg-rose-600 text-white uppercase tracking-wider">
								<?php esc_html_e( 'v3.0.0+ Required', 'frontend-dashboard' ); ?>
							</span>
						</div>
						<p class="text-xs text-slate-600 m-0 mt-1.5 leading-relaxed max-w-3xl">
							<?php
							printf(
								/* translators: 1: Frontend Dashboard version, 2: Required version */
								esc_html__( 'Frontend Dashboard v%1$s requires all active add-ons to be updated to version %2$s or higher. The following add-on(s) need to be updated:', 'frontend-dashboard' ),
								'<strong>' . esc_html( BC_FED_PLUGIN_VERSION ) . '</strong>',
								'<strong>' . esc_html( self::MIN_REQUIRED_VERSION ) . '</strong>'
							);
							?>
						</p>

						<div class="flex flex-wrap gap-2 mt-3">
							<?php foreach ( $incompatible as $addon ) : ?>
								<div class="inline-flex items-center gap-2 bg-white/90 border border-rose-200/90 rounded-lg px-2.5 py-1 text-xs text-slate-800 shadow-2xs">
									<span class="font-semibold text-slate-900"><?php echo esc_html( $addon['name'] ); ?></span>
									<span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 font-mono text-2xs font-bold">
										<?php echo esc_html( 'v' . $addon['current_version'] ); ?>
									</span>
									<span class="text-emerald-700 font-mono text-2xs font-bold">
										<?php echo esc_html( '→ v' . $addon['required_version'] . '+' ); ?>
									</span>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="sm:shrink-0 flex items-center gap-2">
					<a href="<?php echo esc_url( $addons_url ); ?>" class="fed-btn-primary inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold no-underline shadow-xs hover:shadow-md transition-all">
						<i class="fas fa-sync-alt"></i>
						<span><?php esc_html_e( 'Update Add-ons', 'frontend-dashboard' ); ?></span>
					</a>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
