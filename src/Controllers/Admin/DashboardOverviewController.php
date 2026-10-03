<?php

namespace FED\Controllers\Admin;

use FED\Models\Menu;
use FED\Models\UserProfileField;
use FED\Models\PostField;
use FED\Models\Payment;
use FED\Models\AuditLog;
use FED\Services\Diagnostics\SystemHealthChecker;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DashboardOverviewController
 *
 * Handles data aggregation and rendering for the top-level Frontend Dashboard Executive Overview screen.
 */
class DashboardOverviewController {

	/**
	 * Render the Executive Overview page.
	 */
	public function render(): void {
		$metrics      = $this->get_metrics();
		$checklist    = $this->get_setup_checklist();
		$health       = SystemHealthChecker::run();
		$recent_logs  = $this->get_recent_audit_logs();
		$roles_data   = $this->get_user_roles_breakdown();
		$recent_users = $this->get_recent_users();
		$engine_data  = $this->get_engine_status();

		include BC_FED_PLUGIN_DIR . '/templates/admin/dashboard-overview.php';
	}

	/**
	 * Aggregate core system metrics.
	 *
	 * @return array
	 */
	private function get_metrics(): array {
		$user_counts = count_users();
		$total_users = (int) ( $user_counts['total_users'] ?? 0 );

		// Menus count
		$menus_count = 0;
		try {
			$menus_count = Menu::count();
		} catch ( \Throwable $e ) {
			if ( function_exists( 'fed_fetch_table_rows' ) ) {
				$raw         = fed_fetch_table_rows( 'fed_menu' );
				$menus_count = is_array( $raw ) ? count( $raw ) : 0;
			}
		}

		// Profile & Post Fields
		$profile_fields_count = 0;
		$post_fields_count    = 0;
		try {
			$profile_fields_count = UserProfileField::count();
			$post_fields_count    = PostField::count();
		} catch ( \Throwable $e ) {
			// Fallback
		}

		// Payments & Revenue
		$total_payments = 0;
		$total_revenue  = 0.0;
		try {
			$total_payments = Payment::count();
			$completed      = Payment::where( 'status', 'completed' )->get();
			foreach ( $completed as $pay ) {
				$total_revenue += (float) ( $pay->amount ?? 0 );
			}
		} catch ( \Throwable $e ) {
			// Fallback
		}

		return array(
			'total_users'          => $total_users,
			'active_menus'         => $menus_count,
			'profile_fields_count' => $profile_fields_count,
			'post_fields_count'    => $post_fields_count,
			'total_fields'         => ( $profile_fields_count + $post_fields_count ),
			'total_payments'       => $total_payments,
			'total_revenue'        => number_format( $total_revenue, 2 ),
		);
	}

	/**
	 * Retrieve setup checklist status.
	 *
	 * @return array
	 */
	private function get_setup_checklist(): array {
		$login_options = get_option( 'fed_admin_login', array() );
		$general_opts  = get_option( 'fed_admin_general', array() );

		// Check for frontend dashboard shortcode
		$dashboard_page_id = null;
		$login_page_id     = null;

		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			)
		);

		foreach ( $pages as $page ) {
			if ( has_shortcode( $page->post_content, 'fed_dashboard' ) || has_shortcode( $page->post_content, 'frontend_dashboard' ) ) {
				$dashboard_page_id = $page->ID;
			}
			if ( has_shortcode( $page->post_content, 'fed_login' ) || has_shortcode( $page->post_content, 'frontend_dashboard_login' ) ) {
				$login_page_id = $page->ID;
			}
		}

		$wp_restrict_enabled = ! empty( $login_options['restrict_wp']['role'] ?? array() );
		$can_register        = (bool) get_option( 'users_can_register', false );

		return array(
			'dashboard_page' => array(
				'configured' => (bool) $dashboard_page_id,
				'title'      => $dashboard_page_id ? get_the_title( $dashboard_page_id ) : __( 'Not Configured', 'frontend-dashboard' ),
				'url'        => $dashboard_page_id ? get_permalink( $dashboard_page_id ) : admin_url( 'post-new.php?post_type=page' ),
			),
			'login_page'     => array(
				'configured' => (bool) $login_page_id,
				'title'      => $login_page_id ? get_the_title( $login_page_id ) : __( 'Default /wp-login.php', 'frontend-dashboard' ),
				'url'        => $login_page_id ? get_permalink( $login_page_id ) : admin_url( 'post-new.php?post_type=page' ),
			),
			'wp_restrict'    => array(
				'enabled' => $wp_restrict_enabled,
				'roles'   => $wp_restrict_enabled ? array_keys( $login_options['restrict_wp']['role'] ) : array(),
			),
			'registration'   => array(
				'enabled' => $can_register,
			),
		);
	}

	/**
	 * Get user roles breakdown with member counts.
	 *
	 * @return array
	 */
	private function get_user_roles_breakdown(): array {
		$counts      = count_users();
		$avail_roles = $counts['avail_roles'] ?? array();
		$all_roles   = function_exists( 'fed_get_user_roles' ) ? fed_get_user_roles() : array();

		$breakdown = array();
		foreach ( $all_roles as $slug => $name ) {
			$breakdown[] = array(
				'slug'  => $slug,
				'name'  => $name,
				'count' => (int) ( $avail_roles[ $slug ] ?? 0 ),
			);
		}

		return $breakdown;
	}

	/**
	 * Get latest 5 registered users.
	 *
	 * @return array
	 */
	private function get_recent_users(): array {
		$users = get_users(
			array(
				'number'  => 5,
				'orderby' => 'user_registered',
				'order'   => 'DESC',
			)
		);

		$recent = array();
		foreach ( $users as $u ) {
			$recent[] = array(
				'id'         => $u->ID,
				'name'       => $u->display_name ?: $u->user_login,
				'email'      => $u->user_email,
				'role'       => ! empty( $u->roles ) ? reset( $u->roles ) : 'none',
				'avatar'     => get_avatar_url( $u->ID, array( 'size' => 64 ) ),
				'registered' => $u->user_registered,
			);
		}

		return $recent;
	}

	/**
	 * Get Background Engine and Service Status.
	 *
	 * @return array
	 */
	private function get_engine_status(): array {
		$next_cron = wp_next_scheduled( 'fed_daily_maintenance_cron' );
		/* translators: %s: human-readable time difference */
		$cron_status = $next_cron ? sprintf( __( 'Scheduled in %s', 'frontend-dashboard' ), human_time_diff( $next_cron ) ) : __( 'Active (Daily)', 'frontend-dashboard' );

		return array(
			'rest_api_url' => get_rest_url( null, 'fed/v1' ),
			'cron_status'  => $cron_status,
			'logging_mode' => 'Dual (Database + File System)',
			'theme_active' => 'Emerald Teal (Default)',
		);
	}

	/**
	 * Get latest audit logs.
	 *
	 * @return array
	 */
	private function get_recent_audit_logs(): array {
		try {
			$logs = AuditLog::latest( 'created_at' )->limit( 8 )->get();
			if ( ! empty( $logs ) && is_array( $logs ) ) {
				return $logs;
			}
		} catch ( \Throwable $e ) {
			// Fall through to direct query fallback.
		}

		try {
			global $wpdb;
			$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_ACTIVITY_LOG' ) ? BC_FED_TABLE_ACTIVITY_LOG : 'fed_activity_log' );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$results = $wpdb->get_results( "SELECT * FROM `{$table}` ORDER BY `created_at` DESC LIMIT 8", ARRAY_A );
			return is_array( $results ) ? $results : array();
		} catch ( \Throwable $e ) {
			return array();
		}
	}
}
