<?php
/**
 * Shortcode for FED Transactions.
 *
 * @package Frontend Dashboard
 */

if ( ! shortcode_exists( 'fed_transactions' ) && ! function_exists( 'fed_transactions' ) ) {
	/**
	 * Add Shortcode to the page.
	 *
	 * @return string
	 */
	function fed_transactions() {

		$templates = new FED_Template_Loader( BC_FED_PLUGIN_DIR );
		ob_start();
		$templates->get_template_part( 'payments/transactions' );

		return ob_get_clean();
	}

	add_shortcode( 'fed_transactions', 'fed_transactions' );
}
