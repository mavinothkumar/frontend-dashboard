<?php
/**
 * Custom CSS.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FEDCustomCSS' ) ) {
	/**
	 * Class FEDCustomCSS
	 */
	class FEDCustomCSS {

		/**
		 * FEDCustomCSS constructor.
		 */
		public function __construct() {
			add_action( 'wp_head', array( $this, 'head' ) );
			add_action( 'wp_footer', array( $this, 'footer' ) );
		}


		/**
		 * Head.
		 */
		public function head() {
			do_action( 'fed_add_inline_css_at_head' );
		}

		/**
		 * Footer.
		 */
		public function footer() {
			do_action( 'fed_add_scripts_footer' );
		}
	}

	new FEDCustomCSS();
}
