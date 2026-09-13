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

		add_filter( 'script_loader_tag', array( $this, 'filter_script_loader_tag' ), 10, 3 );
	}

	/**
	 * Ensure Vite bundles load with type="module" to prevent global namespace pollution
	 */
	public function filter_script_loader_tag( $tag, $handle, $src ) {
		if ( 'fed-main' === $handle || 'fed-vite-client' === $handle ) {
			if ( false === strpos( $tag, 'type="module"' ) && false === strpos( $tag, 'type=\'module\'' ) ) {
				$tag = str_replace( '<script ', '<script type="module" ', $tag );
			}
		}
		return $tag;
	}

	public function print_early_shims() {
		static $printed = false;
		if ( $printed ) {
			return;
		}
		$printed = true;
		echo '<script id="fed-early-shims">window.wp=(typeof window.wp==="object"&&window.wp!==null)?window.wp:{};window.wp.editor=(typeof window.wp.editor==="object"&&window.wp.editor!==null)?window.wp.editor:{};window.wp.autop=window.wp.autop||{autop:function(t){return t;},removep:function(t){return t;}};window.wp.i18n=(typeof window.wp.i18n==="object"&&window.wp.i18n!==null)?window.wp.i18n:{__:function(t){return t;},_x:function(t){return t;},_n:function(s,p,n){return n===1?s:p;},_nx:function(s,p,n){return n===1?s:p;},isRtl:function(){return false;},setLocaleData:function(){},sprintf:function(t){return t;}};if(!window.wp.i18n.__){window.wp.i18n.__=function(t){return t;};}window.wp.hooks=window.wp.hooks||{addAction:function(){},addFilter:function(){},applyFilters:function(h,v){return v;},doAction:function(){},removeAction:function(){},removeFilter:function(){},hasAction:function(){return false;},hasFilter:function(){return false;}};</script>';
	}

	public function enqueue_scripts() {
		$context    = is_admin() ? 'admin' : 'frontend';
		$db_scripts = get_option( 'fed_general_scripts_styles', array() );
		$is_style_disabled  = isset( $db_scripts[ $context ]['styles']['fed-style'] );
		$is_script_disabled = isset( $db_scripts[ $context ]['scripts']['fed-main'] );

		$shims = 'window.wp=(typeof window.wp==="object"&&window.wp!==null)?window.wp:{};window.wp.editor=(typeof window.wp.editor==="object"&&window.wp.editor!==null)?window.wp.editor:{};window.wp.autop=window.wp.autop||{autop:function(t){return t;},removep:function(t){return t;}};window.wp.i18n=(typeof window.wp.i18n==="object"&&window.wp.i18n!==null)?window.wp.i18n:{__:function(t){return t;},_x:function(t){return t;},_n:function(s,p,n){return n===1?s:p;},_nx:function(s,p,n){return n===1?s:p;},isRtl:function(){return false;},setLocaleData:function(){},sprintf:function(t){return t;}};if(!window.wp.i18n.__){window.wp.i18n.__=function(t){return t;};}window.wp.hooks=window.wp.hooks||{addAction:function(){},addFilter:function(){},applyFilters:function(h,v){return v;},doAction:function(){},removeAction:function(){},removeFilter:function(){},hasAction:function(){return false;},hasFilter:function(){return false;}};';

		wp_add_inline_script( 'jquery-core', $shims, 'before' );
		wp_add_inline_script( 'jquery', $shims, 'before' );

		wp_enqueue_script( 'wp-polyfill' );
		wp_enqueue_script( 'wp-hooks' );
		wp_enqueue_script( 'wp-i18n' );
		wp_enqueue_script( 'wp-dom-ready' );
		wp_enqueue_script( 'wp-a11y' );

		$dependencies = [ 'jquery' ];

		if ( is_admin() ) {
			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
		}

		if ( $this->is_dev ) {
			// Enqueue Vite client for HMR
			if ( ! $is_script_disabled ) {
				wp_enqueue_script( 'fed-vite-client', 'http://localhost:3000/@vite/client', [], null, true );
				wp_enqueue_script( 'fed-main', 'http://localhost:3000/assets/js/main.js', $dependencies, null, true );
			}
			if ( ! $is_style_disabled ) {
				wp_enqueue_style( 'fed-style', 'http://localhost:3000/assets/css/main.css', [], null );
			}
		} else {
			// Production: read manifest.json
			$manifest_path = BC_FED_PLUGIN_DIR . '/assets/dist/.vite/manifest.json';
			if ( file_exists( $manifest_path ) ) {
				$manifest = json_decode( file_get_contents( $manifest_path ), true );
				
				if ( ! $is_script_disabled && isset( $manifest['assets/js/main.js'] ) ) {
					$js_file = $manifest['assets/js/main.js']['file'];
					wp_enqueue_script( 'fed-main', BC_FED_PLUGIN_URL . '/assets/dist/' . $js_file, $dependencies, $this->version, true );
				}
				
				if ( ! $is_style_disabled && isset( $manifest['assets/css/main.css'] ) ) {
					$css_file = $manifest['assets/css/main.css']['file'];
					wp_enqueue_style( 'fed-style', BC_FED_PLUGIN_URL . '/assets/dist/' . $css_file, [], $this->version );
				}
			}
		}
	}
}
