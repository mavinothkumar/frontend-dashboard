<?php

if ( ! class_exists('FED_Validation')) {
    /**
     * Class FED_Validation
     *
     * @package Admin\Validation
     */
    class FED_Validation
    {

        /**
         * @var array $patterns
         */
        public $patterns = array(
                'uri'      => '[A-Za-z0-9-\/_?&=]+',
                'url'      => '[A-Za-z0-9-:.\/_?&=#]+',
                'alpha'    => '[\p{L}]+',
                'words'    => '[\p{L}\s]+',
                'alphanum' => '[\p{L}0-9]+',
                'int'      => '[0-9]+',
                'float'    => '[0-9\.,]+',
                'tel'      => '[0-9+\s()-]+',
                'text'     => '[\p{L}0-9\s-.,;:!"%&()?+\'°#\/@]+',
                'file'     => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+\.[A-Za-z0-9]{2,4}',
                'folder'   => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+',
                'address'  => '[\p{L}0-9\s.,()°-]+',
                'date_dmy' => '[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}',
                'date_ymd' => '[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}',
                'email'    => '[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+',
        );

        /**
         * @var array $errors
         */
        public $errors = array();

        /**
         * Name
         *
         * @param  string  $name
         *
         * @return $this
         */
        public function name($name)
        {

            $this->name = $name;

            return $this;

        }

        /**
         * Value
         *
         * @param  mixed  $value
         *
         * @return $this
         */
        public function value($value)
        {

            $this->value = $value;

            return $this;

        }

        /**
         * File
         *
         * @param  mixed  $value
         *
         * @return $this
         */
        public function file($value)
        {

            $this->file = $value;

            return $this;

        }

        /**
         * Pattern
         *
         * @param  string  $name
         *
         * @return $this
         */
        public function pattern($name)
        {

            if ($name == 'array') {

                if ( ! is_array($this->value)) {
                    $this->errors[] = $this->name.' is Invalid Format';
                }

            } else {

                $regex = '/^('.$this->patterns[$name].')$/u';
                if ($this->value != '' && ! preg_match($regex, $this->value)) {
                    $this->errors[] = $this->name.' is Invalid '.strtoupper($name).' Format';
                }

            }

            return $this;

        }

        /**
         * Custom Pattern
         *
         * @param  string  $pattern
         *
         * @return $this
         */
        public function customPattern($pattern)
        {

            $regex = '/^('.$pattern.')$/u';
            if ($this->value != '' && ! preg_match($regex, $this->value)) {
                $this->errors[] = $this->name.' is Invalid Format';
            }

            return $this;

        }

        /**
         * Required
         *
         * @return $this
         */
        public function required()
        {

            if ((isset($this->file) && $this->file['error'] == 4) || ($this->value == '' || $this->value == null)) {
                $this->errors[] = $this->name.' is required';
            }

            return $this;

        }

        /**
         * Min Value
         *
         * @param  int  $min
         *
         * @return $this
         */
        public function min($length)
        {

            if (is_string($this->value)) {

                if (strlen($this->value) < $length) {
                    $this->errors[] = $this->name.' Should be minimum value';
                }

            } else {

                if ($this->value < $length) {
                    $this->errors[] = $this->name.' lesser than the minimum value';
                }

            }

            return $this;

        }

        /**
         * Max
         *
         * @param  int  $max
         *
         * @return $this
         */
        public function max($length)
        {

            if (is_string($this->value)) {

                if (strlen($this->value) > $length) {
                    $this->errors[] = $this->name.' greater than the Maximum value';
                }

            } else {

                if ($this->value > $length) {
                    $this->errors[] = $this->name.' greater than the Maximum value';
                }

            }

            return $this;

        }

        /**
         * Equal
         *
         *
         * @param  mixed  $value
         *
         * @return $this
         */
        public function equal($value)
        {

            if ($this->value != $value) {
                $this->errors[] = $this->name.' is not equal';
            }

            return $this;

        }

        /**
         * Dimensione massima del file
         *
         * @param  int  $size
         *
         * @return $this
         */
        public function maxSize($size)
        {

            if ($this->file['error'] != 4 && $this->file['size'] > $size) {
                $this->errors[] = 'Il file '.$this->name.' supera la dimensione massima di '.number_format($size / 1048576,
                                2).' MB.';
            }

            return $this;

        }

        /**
         * Estensione (formato) del file
         *
         * @param  string  $extension
         *
         * @return $this
         */
        public function ext($extension)
        {

            if ($this->file['error'] != 4 && pathinfo($this->file['name'],
                            PATHINFO_EXTENSION) != $extension && strtoupper(pathinfo($this->file['name'],
                            PATHINFO_EXTENSION)) != $extension) {
                $this->errors[] = 'Il file '.$this->name.' non è un '.$extension.'.';
            }

            return $this;

        }

        /**
         * Campi validati
         *
         * @return boolean
         */
        public function isSuccess()
        {
            if (empty($this->errors)) {
                return true;
            }
        }

        /**
         * Errori della validazione
         *
         * @return array $this->errors
         */
        public function getErrors()
        {
            if ( ! $this->isSuccess()) {
                return $this->errors;
            }
        }

        /**
         * Visualizza errori in formato Html
         *
         * @return string $html
         */
        public function displayErrors()
        {

            $html = '<ul>';
            foreach ($this->getErrors() as $error) {
                $html .= '<li>'.$error.'</li>';
            }
            $html .= '</ul>';

            return $html;

        }

        /**
         * Visualizza risultato della validazione
         *
         * @return booelan|string
         */
        public function result()
        {

            if ( ! $this->isSuccess()) {

                foreach ($this->getErrors() as $error) {
                    echo "$error\n";
                }
                exit;

            } else {
                return true;
            }

        }

        /**
         * Verifica se il valore è
         * un numero intero
         *
         * @param  mixed  $value
         *
         * @return boolean
         */
        public static function is_int($value)
        {
            if (filter_var($value, FILTER_VALIDATE_INT)) {
                return true;
            }
        }

        /**
         * Verifica se il valore è
         * un numero float
         *
         * @param  mixed  $value
         *
         * @return boolean
         */
        public static function is_float($value)
        {
            if (filter_var($value, FILTER_VALIDATE_FLOAT)) {
                return true;
            }
        }

        /**
         * Verifica se il valore è
         * una lettera dell'alfabeto
         *
         * @param  mixed  $value
         *
         * @return boolean
         */
        public static function is_alpha($value)
        {
            if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z]+$/")))) {
                return true;
            }
        }

        /**
         * Verifica se il valore è
         * una lettera o un numero
         *
         * @param  mixed  $value
         *
         * @return boolean
         */
        public static function is_alphanum($value)
        {
            if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/")))) {
                return true;
            }
        }

        /**
         * Verifica se il valore è
         * un url
         *
         * @param  mixed  $value
         *
         * @return boolean
         */
        public static function is_url($value)
        {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return true;
            }
        }

        /**
         * Verifica se il valore è
         * un uri
         *
         * @param  mixed  $value
         *
         * @return boolean
         */
        public static function is_uri($value)
        {
            if (filter_var($value, FILTER_VALIDATE_REGEXP,
                    array('options' => array('regexp' => "/^[A-Za-z0-9-\/_]+$/")))) {
                return true;
            }
        }

        /**
         * Verifica se il valore è
         * true o false
         *
         * @param  mixed  $value
         *
         * @return boolean
         */
        public static function is_bool($value)
        {
            if (is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
                return true;
            }
        }

        /**
         * Verifica se il valore è
         * un'e-mail
         *
         * @param  mixed  $value
         *
         * @return boolean
         */
        public static function is_email($value)
        {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
        }

    }
}