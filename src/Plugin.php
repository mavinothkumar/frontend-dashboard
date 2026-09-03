<?php

namespace FED;

use FED\Core\Application;
use FED\Core\CoreServiceProvider;
use FED\Hooks\HookLoader;
use FED\Services\AssetManager;

/**
 * Class Plugin
 *
 * Main entry coordinator bootstrapping Application kernel and WordPress hooks.
 */
class Plugin {

	private static $instance = null;

	/**
	 * @var Application
	 */
	protected $app;

	/**
	 * @var HookLoader
	 */
	protected $loader;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// 1. Initialize Application Kernel & Core Service Providers
		$this->app = Application::getInstance();
		$this->app->register( CoreServiceProvider::class );
		$this->app->boot();

		// 2. Initialize Hook Loader
		$this->loader = new HookLoader();
		$this->define_hooks();
		$this->load_dependencies();

		$this->loader->run();
	}

	/**
	 * Get the application container.
	 *
	 * @return \FED\Core\Container
	 */
	public function getContainer() {
		return $this->app->getContainer();
	}

	private function define_hooks() {
		$asset_manager = $this->app->make( AssetManager::class );

		// Register frontend and admin scripts
		$this->loader->add_action( 'wp_enqueue_scripts', $asset_manager, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $asset_manager, 'enqueue_scripts' );

		// Register Auth Controller hooks
		$auth_controller = new \FED\Controllers\Auth\AuthController();
		$auth_controller->register_hooks( $this->loader );

		// Register User Profile Controller hooks
		$user_profile_controller = new \FED\Controllers\User\UserProfileController();
		$user_profile_controller->register_hooks( $this->loader );

		// Register Shortcodes and page guards
		$shortcode_controller = new \FED\Controllers\Shortcode\ShortcodeController();
		$shortcode_controller->register_hooks( $this->loader );

		// Register Database & Schema Activator
		$database_activator = new \FED\Database\Activator();
		$database_activator->register_hooks( $this->loader );

		// Register Admin Menus
		$admin_menu_controller = new \FED\Controllers\Admin\AdminMenuController();
		$admin_menu_controller->register_hooks( $this->loader );
	}

	private function load_dependencies() {
		// Load legacy procedural dependencies for backward compatibility
		if ( file_exists( BC_FED_PLUGIN_DIR . '/includes/function.php' ) ) {
			require_once BC_FED_PLUGIN_DIR . '/includes/function.php';
		}
	}
}
