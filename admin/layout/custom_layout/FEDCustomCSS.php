<?php

if ( ! defined('ABSPATH')) {
    exit;
}

if ( ! class_exists( 'FEDCustomCSS' ) ) {
    /**
     * Class FEDCustomCSS
     */
    class FEDCustomCSS {

		public function __construct() {
			add_action( 'wp_head', array( $this, 'head' ) );
			add_action( 'wp_footer', array( $this, 'footer' ) );
		}


		public function head() {
			do_action( 'fed_add_inline_css_at_head' );
		}
		public function footer() {
			do_action( 'fed_add_scripts_footer' );
		}
	}

	new FEDCustomCSS();
}