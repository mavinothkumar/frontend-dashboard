<?php

function fed_resp_api_init() {
//    // Here we are registering our route for a collection of products.
//    register_rest_route( 'my-shop/v1', '/products', array(
//        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
//        'methods'  => WP_REST_Server::READABLE,
//        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
//        'callback' => 'prefix_get_products',
//    ) );
//    // Here we are registering our route for single products. The (?P<id>[\d]+) is our path variable for the ID, which, in this example, can only be some form of positive number.
//    register_rest_route( 'my-shop/v1', '/products/(?P<id>[\d]+)', array(
//        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
//        'methods'  => WP_REST_Server::READABLE,
//        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
//        'callback' => 'prefix_get_product',
//    ) );
}

//add_action( 'rest_api_init', 'fed_resp_api_init' );