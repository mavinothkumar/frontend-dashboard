<?php
if ( ! shortcode_exists( 'fed_users_chart' ) && ! function_exists( 'fed_users_chart' ) ) {
    /**
     * Add Shortcode to the page.
     *
     * @return string
     */
    function fed_users_chart( ) {

        $templates = new FED_Template_Loader(BC_FED_PLUGIN_DIR);
        ob_start();
        $templates->get_template_part( 'payments/transactions' );
        return ob_get_clean();
    }

    add_shortcode( 'fed_users_chart', 'fed_users_chart' );
}