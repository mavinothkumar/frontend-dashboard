<?php

if ( ! class_exists('FED_Validation')) {
    /**
     * Class FED_Validation
     *
     * @package Admin\Validation
     */
    class FED_Validation
    {

        public $errors = array();
        private $request;

        /**
         * @param $request
         * @param  array  $rules
         */
        public function validate($request, $rules = array())
        {
            $this->request = isset($request) ? $request : $_REQUEST;


        }

    }
}