<?php
/**
 * Common Scripts.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the early WP JS shims code string.
 */
function fed_get_early_wp_shims_js() {
	return 'window.wp=(typeof window.wp==="object"&&window.wp!==null)?window.wp:{};window.wp.editor=(typeof window.wp.editor==="object"&&window.wp.editor!==null)?window.wp.editor:{};window.wp.autop=window.wp.autop||{autop:function(t){return t;},removep:function(t){return t;}};window.wp.i18n=(typeof window.wp.i18n==="object"&&window.wp.i18n!==null)?window.wp.i18n:{__:function(t){return t;},_x:function(t){return t;},_n:function(s,p,n){return n===1?s:p;},_nx:function(s,p,n){return n===1?s:p;},isRtl:function(){return false;},setLocaleData:function(){},sprintf:function(t){return t;}};if(!window.wp.i18n.__){window.wp.i18n.__=function(t){return t;};}window.wp.hooks=window.wp.hooks||{addAction:function(){},addFilter:function(){},applyFilters:function(h,v){return v;},doAction:function(){},removeAction:function(){},removeFilter:function(){},hasAction:function(){return false;},hasFilter:function(){return false;}};';
}

/**
 * Enqueue Script at Admin and Front End
 */
add_action( 'admin_enqueue_scripts', 'fed_script_admin' );
add_action( 'wp_enqueue_scripts', 'fed_script_front_end', 10 );
add_action( 'wp_head', 'fed_print_early_wp_shims', 1 );
add_action( 'admin_head', 'fed_print_early_wp_shims', 1 );
add_action( 'login_head', 'fed_print_early_wp_shims', 1 );
add_action( 'wp_print_scripts', 'fed_print_early_wp_shims', 1 );
add_action( 'admin_print_scripts', 'fed_print_early_wp_shims', 1 );

if ( ! function_exists( 'fed_print_early_wp_shims' ) ) {
	/**
	 * Early WP JS Shims to prevent third-party/core script errors on frontend
	 */
	function fed_print_early_wp_shims() {
		static $printed = false;
		if ( $printed ) {
			return;
		}
		$printed = true;
		echo '<script id="fed-early-wp-shims">' . fed_get_early_wp_shims_js() . '</script>';
	}
}

if ( ! function_exists( 'fed_script_admin' ) ) {
	/**
	 * Admin Scripts.
	 *
	 * @param  string $hook  Hook.
	 */
	function fed_script_admin( $hook ) {

		if (
			( in_array(
				$hook, fed_get_script_loading_pages(),
				false
			) ) ||
			( isset( $_GET['page'] ) &&
			  in_array(
				  wp_unslash( $_GET['page'] ), fed_get_script_loading_pages(), false
			  ) )
		) {
			$db_scripts      = get_option( 'fed_general_scripts_styles', array() );
			$default_scripts = new FED_Admin_General();

			foreach ( $default_scripts->default_admin_script() as $index => $scripts ) {
				foreach ( $scripts as $key => $script ) {
					if ( ! isset( $db_scripts['admin'][ $index ][ $key ] ) ) {
						fed_enqueue_scripts( $script, $index, $key );
					}
				}
			}

			do_action( 'fed_enqueue_script_style_admin' );

			wp_localize_script( 'fed_admin_script', 'frontend_dashboard', fed_js_translation() );

			wp_enqueue_media();
		}
	}
}
if ( ! function_exists( 'fed_script_front_end' ) ) {
	/**
	 * Frontend Script.
	 */
	function fed_script_front_end() {
		$custom_condition = apply_filters( 'fed_show_frontend_script_on_custom_condition', false );
		if ( is_user_logged_in() || fed_is_dashboard() || fed_is_shortcode_in_content() || $custom_condition ) {

			$db_scripts      = get_option( 'fed_general_scripts_styles', array() );
			$default_scripts = new FED_Admin_General();
			foreach ( $default_scripts->default_frontend_script() as $index => $scripts ) {
				foreach ( $scripts as $key => $script ) {
					if ( ! isset( $db_scripts['frontend'][ $index ][ $key ] ) ) {
						fed_enqueue_scripts( $script, $index, $key );
					}
				}
			}

			do_action( 'fed_enqueue_script_style_frontend' );

			$shims = fed_get_early_wp_shims_js();
			wp_add_inline_script( 'jquery-core', $shims, 'before' );
			wp_add_inline_script( 'jquery', $shims, 'before' );
			wp_add_inline_script( 'common', $shims, 'before' );
			wp_add_inline_script( 'editor', $shims, 'before' );
			wp_add_inline_script( 'wplink', $shims, 'before' );

			wp_enqueue_script( 'wp-polyfill' );
			wp_enqueue_script( 'wp-hooks' );
			wp_enqueue_script( 'wp-i18n' );
			wp_enqueue_script( 'wp-dom-ready' );
			wp_enqueue_script( 'wp-a11y' );

			if ( fed_is_register() ) {
				wp_enqueue_script( 'password-strength-meter' );
			}

			// Pass PHP value to JavaScript.
			$translation_array = apply_filters( 'fed_convert_php_js_var', fed_js_translation() );

			wp_localize_script( 'fed_script', 'frontend_dashboard', $translation_array );
		}

		wp_enqueue_style( 'fed_global_admin_style' );
	}
}

if ( ! function_exists( 'fed_enqueue_scripts' ) ) {
	/**
	 * Enqueue Scripts.
	 *
	 * @param  array  $script  Script.
	 * @param  string $index  Index.
	 * @param  string $key  Key.
	 */
	function fed_enqueue_scripts( $script, $index, $key ) {
		if ( 'scripts' === $index ) {
			if ( true === $script['wp_core'] ) {
				wp_enqueue_script( $key );
			} else {
				wp_register_script(
					$key, $script['src'], $script['dependencies'], $script['version'],
					$script['in_footer']
				);
				wp_enqueue_script( $key );
			}
		}
		if ( 'styles' === $index ) {
			if ( true === $script['wp_core'] ) {
				wp_enqueue_style( $key );
			} else {
				wp_register_style(
					$key, $script['src'], $script['dependencies'], $script['version'],
					$script['media']
				);
				wp_enqueue_style( $key );
			}
		}
	}
}
