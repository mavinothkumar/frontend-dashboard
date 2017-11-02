<?php

if ( ! class_exists( 'FEDCustomCSS' ) ) {
	class FEDCustomCSS {

		public function __construct() {
			add_action( 'wp_head', array( $this, 'fed_add_inline_css' ) );
		}


		public function fed_add_inline_css() {
			do_action( 'fed_add_inline_css_at_head' );
		}
	}

	new FEDCustomCSS();
}