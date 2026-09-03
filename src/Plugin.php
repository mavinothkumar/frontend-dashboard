<?php

namespace FED;

use FED\Hooks\HookLoader;
use FED\Services\AssetManager;

class Plugin {

    private static $instance = null;
    protected $loader;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->loader = new HookLoader();
        $this->define_hooks();
        $this->load_dependencies();
        
        $this->loader->run();
    }

    private function define_hooks() {
        $asset_manager = new AssetManager( BC_FED_PLUGIN_VERSION );
        
        // Register frontend scripts
        $this->loader->add_action('wp_enqueue_scripts', $asset_manager, 'enqueue_scripts');
        
        // Example: Register admin scripts if needed
        // $this->loader->add_action('admin_enqueue_scripts', $asset_manager, 'enqueue_scripts');
    }

    private function load_dependencies() {
        // Load legacy non-OOP procedural helpers if necessary
        if (file_exists(BC_FED_PLUGIN_DIR . '/includes/function.php')) {
            require_once BC_FED_PLUGIN_DIR . '/includes/function.php';
        }
    }
}
