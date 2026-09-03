<?php
namespace FED\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AssetManager
 * 
 * Handles enqueueing Vite compiled CSS and JS assets in WordPress.
 */
class AssetManager {

	private $version;
	private $is_dev = false;

	public function __construct( $version ) {
		$this->version = $version;
		// Detect if we are in local Vite dev mode
		// We can use an environment variable or simply check if WP_ENVIRONMENT_TYPE is local
		if ( defined( 'WP_ENVIRONMENT_TYPE' ) && 'local' === WP_ENVIRONMENT_TYPE ) {
			// Alternatively, you can check if vite dev server is running on port 3000
			$this->is_dev = false; // Set to true manually when running `npm run dev`
		}
	}

	public function enqueue_scripts() {
		if ( $this->is_dev ) {
			// Enqueue Vite client for HMR
			wp_enqueue_script( 'fed-vite-client', 'http://localhost:3000/@vite/client', [], null, true );
			wp_enqueue_script( 'fed-main', 'http://localhost:3000/assets/js/main.js', [], null, true );
			wp_enqueue_style( 'fed-style', 'http://localhost:3000/assets/css/main.css', [], null );
		} else {
			// Production: read manifest.json
			$manifest_path = BC_FED_PLUGIN_DIR . '/assets/dist/.vite/manifest.json';
			if ( file_exists( $manifest_path ) ) {
				$manifest = json_decode( file_get_contents( $manifest_path ), true );
				
				if ( isset( $manifest['assets/js/main.js'] ) ) {
					$js_file = $manifest['assets/js/main.js']['file'];
					wp_enqueue_script( 'fed-main', BC_FED_PLUGIN_URL . '/assets/dist/' . $js_file, [], $this->version, true );
				}
				
				if ( isset( $manifest['assets/css/main.css'] ) ) {
					$css_file = $manifest['assets/css/main.css']['file'];
					wp_enqueue_style( 'fed-style', BC_FED_PLUGIN_URL . '/assets/dist/' . $css_file, [], $this->version );
				}
			}
		}
	}
}
