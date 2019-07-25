<?php

if ( ! class_exists('FED_Requests')) {
    /**
     * Class FED_Requests
     */
    class FED_Requests
    {
        public function __construct()
        {
            add_action('wp_ajax_fed_ajax_request', array($this, 'request'));
        }

        public function request()
        {
            $request = $_REQUEST;

            /**
             * Verify Nonce
             */
            fed_verify_nonce($request);

            do_action('fed_before_ajax_request_action_hook_call', $request);

            if (isset($request['fed_action_hook'])) {
                $class = explode('@', urldecode($request['fed_action_hook']));

                if (is_array($class) && isset($class[0])) {
                    fed_ajax_call_function_method(array(
                            'callable'  => array(
                                    'object' => new $class[0],
                                    'method' => isset($class[1]) ? $class[1] : 'update',
                            ),
                            'arguments' => $request,
                    ));
                }
            }
            if (isset($request['fed_action_hook_fn']) && ! empty($request['fed_action_hook_fn']) && is_string($request['fed_action_hook_fn'])) {
                fed_ajax_call_function_method(array(
                        'callable'  => $request['fed_action_hook_fn'],
                        'arguments' => $request,
                ));
            }

            wp_send_json_error(array('message' => 'Invalid Request - FED|route|FED_Requests@request'));
        }
    }

    new FED_Requests();
}