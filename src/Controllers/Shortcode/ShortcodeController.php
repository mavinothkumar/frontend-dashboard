<?php

namespace FED\Controllers\Shortcode;

use FED\Helpers\TemplateLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ShortcodeController
 *
 * Handles shortcode registrations and page template redirects.
 */
class ShortcodeController {

	/**
	 * @var TemplateLoader
	 */
	protected $templates;

	public function __construct( TemplateLoader $templates = null ) {
		$this->templates = $templates ?: new TemplateLoader();
	}

	/**
	 * Register shortcodes and hooks with HookLoader.
	 *
	 * @param \FED\Hooks\HookLoader $loader
	 */
	public function register_hooks( $loader ) {
		// Register shortcodes directly
		add_shortcode( 'fed_dashboard', [ $this, 'render_dashboard' ] );
		add_shortcode( 'fed_login', [ $this, 'render_login' ] );
		add_shortcode( 'fed_transactions', [ $this, 'render_transactions' ] );
		add_shortcode( 'fed_user', [ $this, 'render_user_role' ] );
		add_shortcode( 'fed_user_role', [ $this, 'render_user_role' ] );
		add_shortcode( 'fed_author', [ $this, 'render_author' ] );

		// Template redirect guards
		$loader->add_action( 'template_redirect', $this, 'redirect_unauthenticated_dashboard' );
		$loader->add_action( 'template_redirect', $this, 'redirect_authenticated_login' );

		// Enable shortcodes in text widgets
		add_filter( 'widget_text', 'do_shortcode' );
	}

	/**
	 * Render [fed_dashboard] shortcode.
	 *
	 * @return string
	 */
	public function render_dashboard() {
		ob_start();
		$this->templates->get_template_part( 'dashboard' );
		return ob_get_clean();
	}

	/**
	 * Render [fed_login] shortcode.
	 *
	 * @return string
	 */
	public function render_login() {
		ob_start();
		if ( is_user_logged_in() ) {
			$this->templates->get_template_part( 'login/registered', 'user' );
		} else {
			$this->templates->get_template_part( 'login/unregistered', 'user' );
		}
		return ob_get_clean();
	}

	/**
	 * Render [fed_transactions] shortcode.
	 *
	 * @return string
	 */
	public function render_transactions() {
		ob_start();
		$this->templates->get_template_part( 'payments/transactions' );
		return ob_get_clean();
	}

	/**
	 * Render [fed_user] shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public function render_user_role( $atts ) {
		$role = shortcode_atts(
			[ 'role' => 'subscriber' ],
			$atts,
			'fed_user'
		);

		ob_start();
		$this->templates->set_template_data( $role, 'fed_user_attr' );
		$this->templates->get_template_part( 'user_role' );
		return ob_get_clean();
	}

	/**
	 * Render [fed_author] shortcode.
	 *
	 * @return string
	 */
	public function render_author() {
		ob_start();
		$this->templates->get_template_part( 'author' );
		return ob_get_clean();
	}

	/**
	 * Redirect unauthenticated users trying to access dashboard page.
	 */
	public function redirect_unauthenticated_dashboard() {
		if ( ! is_user_logged_in() ) {
			$location   = function_exists( 'fed_get_dashboard_url' ) ? fed_get_dashboard_url() : false;
			$login_page = function_exists( 'fed_get_login_url' ) ? fed_get_login_url() : false;

			if ( $location && get_permalink() === $location ) {
				$redirect = ( false === $login_page ) ? esc_url( wp_login_url() ) : $login_page;
				if ( $redirect && get_permalink() !== $redirect ) {
					wp_safe_redirect( $redirect );
					exit();
				}
			}
		}
	}

	/**
	 * Redirect logged-in users away from the login page.
	 */
	public function redirect_authenticated_login() {
		if ( is_user_logged_in() ) {
			$login_page = function_exists( 'fed_get_login_url' ) ? fed_get_login_url() : false;

			if ( $login_page ) {
				$url_to_post_id = function_exists( 'wpcom_vip_url_to_postid' )
					? wpcom_vip_url_to_postid( $login_page )
					: url_to_postid( $login_page );

				if ( $url_to_post_id && is_page( $url_to_post_id ) ) {
					$location = function_exists( 'fed_get_login_redirect_url' )
						? fed_get_login_redirect_url()
						: '';

					if ( ! $location ) {
						$location = function_exists( 'fed_get_dashboard_url' )
							? fed_get_dashboard_url()
							: home_url();
					}

					if ( $location && get_permalink() !== $location ) {
						wp_safe_redirect( $location );
						exit();
					}
				}
			}
		}
	}
}
