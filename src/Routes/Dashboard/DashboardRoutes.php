<?php

namespace FED\Routes\Dashboard;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DashboardRoutes
 *
 * Coordinates frontend dashboard menu routing and active panel state.
 */
class DashboardRoutes {

	/**
	 * @var array
	 */
	public $request;

	/**
	 * DashboardRoutes constructor.
	 *
	 * @param array|string $request
	 */
	public function __construct( $request ) {
		$this->request = is_array( $request ) ? $request : [];
	}

	/**
	 * Render active dashboard content.
	 *
	 * @param array $menu
	 */
	public function getDashboardContent( $menu ) {
		$menuType = isset( $menu['menu_request']['menu_type'] ) ? $menu['menu_request']['menu_type'] : '';
		$menuSlug = isset( $menu['menu_request']['menu_slug'] ) ? $menu['menu_request']['menu_slug'] : '';
		$userId   = get_current_user_id();

		// Gatekeeper Access Check (Allow Pro addons like Memberships / Roles to control access)
		$isAllowed = apply_filters( 'fed_user_can_access_menu', true, $menu, $userId );

		if ( ! $isAllowed ) {
			do_action( 'fed_dashboard_menu_access_denied', $menu, $userId );

			if ( ! did_action( 'fed_dashboard_menu_custom_denied_rendered' ) ) {
				$this->renderPaywallBanner( $menu );
			}
			return;
		}

		if ( 'user' === $menuType && function_exists( 'fed_display_dashboard_profile' ) ) {
			fed_display_dashboard_profile( $menu['menu_request'] );
		}
		if ( 'logout' === $menuType && function_exists( 'fed_logout_process' ) ) {
			fed_logout_process( $menu['menu_request'] );
		}

		do_action( 'fed_frontend_dashboard_menu_container', $this->request, $menu );
		do_action( 'fed_frontend_dashboard_menu_container_' . $menuSlug, $this->request, $menu );
	}

	/**
	 * Render standard paywall / restricted access banner.
	 *
	 * @param array $menu
	 */
	public function renderPaywallBanner( $menu ) {
		$menuSlug = isset( $menu['menu_request']['menu_slug'] ) ? esc_html( ucfirst( str_replace( [ '-', '_' ], ' ', $menu['menu_request']['menu_slug'] ) ) ) : 'This Section';
		?>
		<div class="fed_paywall_container text-center py-12 px-6 flex flex-col items-center justify-center max-w-lg mx-auto">
			<div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-5 text-2xl shadow-xs">
				<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
			</div>
			<h3 class="text-xl font-bold text-slate-800 mb-2">
				<?php
				/* translators: %s: menu section name */
				printf( esc_html__( 'Access to %s is Restricted', 'frontend-dashboard' ), esc_html( $menuSlug ) );
				?>
			</h3>
			<p class="text-sm text-slate-500 mb-6 leading-relaxed">
				<?php esc_html_e( 'You do not have the required membership level or permissions to view this dashboard module. Upgrade your account or contact support to gain instant access.', 'frontend-dashboard' ); ?>
			</p>
			<?php do_action( 'fed_dashboard_paywall_action_buttons', $menu ); ?>
		</div>
		<?php
	}

	/**
	 * Set Dashboard Menu Query.
	 *
	 * @return array|bool|WP_Error
	 */
	public function setDashboardMenuQuery() {
		$menu = function_exists( 'fed_get_dashboard_menu_items_sort_data' )
			? fed_get_dashboard_menu_items_sort_data()
			: [];

		if ( empty( $menu ) ) {
			return new WP_Error( 'no_menus', __( 'No dashboard menus available.', 'frontend-dashboard' ) );
		}

		$first_element_key = array_keys( $menu );
		$first_element     = $first_element_key[0];

		$hasFullQuery   = count( array_diff( $this->getDefaultMenuQuery(), array_keys( $this->request ) ) ) === 0;
		$requested_slug = ! empty( $this->request['menu_slug'] ) ? sanitize_key( $this->request['menu_slug'] ) : '';
		$matched_menu   = null;

		if ( $requested_slug ) {
			foreach ( $menu as $item ) {
				if ( isset( $item['menu_slug'] ) && $item['menu_slug'] === $requested_slug ) {
					$matched_menu = $item;
					break;
				}
			}
		}

		if ( $matched_menu ) {
			$menu_items = [
				'menu_request' => [
					'menu_type' => isset( $matched_menu['menu_type'] ) ? $matched_menu['menu_type'] : ( isset( $this->request['menu_type'] ) ? sanitize_key( $this->request['menu_type'] ) : 'custom' ),
					'menu_slug' => $matched_menu['menu_slug'],
					'menu_id'   => isset( $matched_menu['id'] ) ? $matched_menu['id'] : ( isset( $this->request['menu_id'] ) ? absint( $this->request['menu_id'] ) : 0 ),
					'fed_nonce' => wp_create_nonce( 'fed_nonce' ),
				],
			];
		} elseif ( ! $hasFullQuery ) {
			$menu_items = [
				'menu_request' => [
					'menu_type' => isset( $menu[ $first_element ]['menu_type'] ) ? $menu[ $first_element ]['menu_type'] : 'user',
					'menu_slug' => isset( $menu[ $first_element ]['menu_slug'] ) ? $menu[ $first_element ]['menu_slug'] : 'profile',
					'menu_id'   => isset( $menu[ $first_element ]['id'] ) ? $menu[ $first_element ]['id'] : 1,
					'fed_nonce' => wp_create_nonce( 'fed_nonce' ),
				],
			];
		} else {
			$menu_items = [
				'menu_request' => [
					'menu_type' => $this->request['menu_type'],
					'menu_slug' => $this->request['menu_slug'],
					'menu_id'   => isset( $this->request['menu_id'] ) ? $this->request['menu_id'] : 0,
					'fed_nonce' => wp_create_nonce( 'fed_nonce' ),
				],
			];
		}

		$menu_items['menu_items'] = $menu;

		set_query_var( 'fed_menu_items', $menu_items );
		wp_cache_set( 'fed_dashboard_menu_' . get_current_user_id(), $menu_items, 'frontend-dashboard', 60 );

		return $menu_items;
	}

	/**
	 * Get Default Menu Query parameters.
	 *
	 * @return array
	 */
	public function getDefaultMenuQuery() {
		return apply_filters( 'fed_get_default_menu_query', [ 'menu_type', 'menu_slug', 'fed_nonce' ] );
	}

	/**
	 * Get Default Menu Types.
	 *
	 * @return array
	 */
	public function getDefaultMenuType() {
		return function_exists( 'fed_get_default_menu_type' )
			? fed_get_default_menu_type()
			: [ 'post', 'user', 'logout', 'collapse', 'custom' ];
	}
}
