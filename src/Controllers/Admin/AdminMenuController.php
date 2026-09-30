<?php

namespace FED\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AdminMenuController
 *
 * Coordinates WordPress wp-admin menus and settings tabs.
 */
class AdminMenuController {

	/**
	 * @var \FED_AdminMenu|null
	 */
	protected $admin_menu;

	public function __construct() {
		if ( class_exists( 'FED_AdminMenu' ) ) {
			$this->admin_menu = new \FED_AdminMenu();
		}
	}

	/**
	 * Register hooks with HookLoader.
	 *
	 * @param \FED\Hooks\HookLoader $loader
	 */
	public function register_hooks( $loader ) {
		// If FED_AdminMenu is loaded, its constructor or menu() registers the admin_menu.
		// If not, we hook into admin_menu as fallback.
		if ( ! $this->admin_menu && class_exists( 'FED_AdminMenu' ) ) {
			$this->admin_menu = new \FED_AdminMenu();
		}
	}
}
