<?php
/**
 * FED Validate.
 *
 * @package frontend-dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FED_Validate' ) ) {
	/**
	 * Class FED_Validate
	 *
	 * @package Admin\Validation
	 */
	class FED_Validate {
		/**
		 * Payload.
		 *
		 * @var $post_payload
		 */
		private $post_payload;

		/**
		 * FED_Validate constructor.
		 *
		 * @param $post_payload
		 */
		public function __construct( $post_payload ) {
			$this->post_payload = $post_payload;
		}

		/**
		 * Patterns.
		 *
		 * @var array $patterns .
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
			'email'    => '([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}',
		);

		/**
		 * Errors.
		 *
		 * @var array $errors
		 */
		public $errors = array();

		/**
		 * Name.
		 *
		 * @param  string $name  .
		 *
		 * @return $this
		 */
		public function name( $name ) {
			/* translators: %s : Name */
			$this->name = sprintf( __( '%s ', 'frontend-dashboard' ), $name );

			return $this;
		}

		/**
		 * Key.
		 *
		 * @param  string $key  Key.
		 *
		 * @return $this
		 */
		public function key( $key ) {
			/* translators: %s : Name */
			$this->key   = sprintf( __( '%s ', 'frontend-dashboard' ), $key );
			$this->name  = ucfirst( str_replace( '_', ' ', $key ) );
			$this->value = isset( $this->post_payload[ $key ] ) ? $this->post_payload[ $key ] : '';

			return $this;
		}

		/**
		 * Value.
		 *
		 * @param  mixed $value  .
		 *
		 * @return $this
		 */
		public function value( $value = null ) {

			$this->value = $value === null ? $this->post_payload[ $this->key ] : $value;

			return $this;
		}

		/**
		 * File.
		 *
		 * @param  mixed $value  Value.
		 *
		 * @return $this
		 */
		public function file( $value ) {

			$this->file = $value;

			return $this;
		}

		/**
		 * Pattern.
		 *
		 * @param  string $name  Name.
		 *
		 * @return $this
		 */
		public function pattern( $name ) {
			if ( 'array' == $name ) {
				if ( ! is_array( $this->value ) ) {
					$this->errors[ $this->key ] = $this->name . ' is Invalid Format';
				}
			} else {
				$regex = '/^(' . $this->patterns[ $name ] . ')$/u';
				if ( '' != $this->value && ! preg_match( $regex, $this->value ) ) {
					$this->errors[ $this->key ] = $this->name . ' is Invalid ';
				}
			}

			return $this;
		}

		/**
		 * Custom Pattern.
		 *
		 * @param  string $pattern  Pattern.
		 *
		 * @return $this
		 */
		public function custom_pattern( $pattern ) {

			$regex = '/^(' . $pattern . ')$/u';
			if ( '' != $this->value && ! preg_match( $regex, $this->value ) ) {
				$this->errors[ $this->key ] = $this->name . ' is Invalid Format';
			}

			return $this;
		}

		/**
		 * Required.
		 *
		 * @return $this
		 */
		public function required() {
			if ( ( isset( $this->file ) && 4 == $this->file['error'] ) || ( '' == $this->value || null == $this->value ) ) {
				$this->errors[ $this->key ] = $this->name . ' is required';
			}

			return $this;
		}

		/**
		 * Is Array.
		 *
		 * @param  int $count  Count.
		 *
		 * @return $this
		 */
		public function is_array( $count = 0 ) {
			if ( ! is_array( $this->value ) && count( $this->value ) >= $count ) {
				$this->errors[ $this->key ] = __( 'Please select ', 'frontend-dashboard' ) . $this->name;
			}

			return $this;
		}

		/**
		 * Min Value.
		 *
		 * @param  int $length  Min Value.
		 *
		 * @return $this
		 */
		public function min( $length ) {

			if ( is_string( $this->value ) ) {

				if ( strlen( $this->value ) < $length ) {
					$this->errors[ $this->key ] = $this->name . ' Should be minimum value';
				}
			} else {
				if ( $this->value < $length ) {
					$this->errors[ $this->key ] = $this->name . ' lesser than the minimum value';
				}
			}

			return $this;
		}

		/**
		 * Max
		 *
		 * @param  int $length  Length.
		 *
		 * @return $this
		 */
		public function max( $length ) {
			if ( is_string( $this->value ) ) {

				if ( strlen( $this->value ) > $length ) {
					$this->errors[ $this->key ] = $this->name . ' greater than the Maximum value';
				}
			} else {
				if ( $this->value > $length ) {
					$this->errors[ $this->key ] = $this->name . ' greater than the Maximum value';
				}
			}

			return $this;
		}

		/**
		 * Equal.
		 *
		 * @param  mixed $value  Value.
		 *
		 * @return $this
		 */
		public function equal( $value ) {

			if ( $this->value != $value ) {
				$this->errors[ $this->key ] = $this->name . ' is not equal';
			}

			return $this;

		}

		/**
		 * Max Size.
		 *
		 * @param  int $size  Size.
		 *
		 * @return $this
		 */
		public function maxSize( $size ) {

			if ( 4 != $this->file['error'] && $this->file['size'] > $size ) {
				$this->errors[ $this->key ] = 'Il file ' . $this->name . ' supera la dimensione massima di ' . number_format(
						$size / 1048576,
						2
					) . ' MB.';
			}

			return $this;

		}

		/**
		 * Extension.
		 *
		 * @param  string $extension  Extension.
		 *
		 * @return $this
		 */
		public function ext( $extension ) {

			if (
				$this->file['error'] != 4 &&
				pathinfo(
					$this->file['name'],
					PATHINFO_EXTENSION
				) != $extension &&
				strtoupper(
					pathinfo(
						$this->file['name'],
						PATHINFO_EXTENSION
					)
				) != $extension
			) {
				$this->errors[ $this->key ] = 'Il file ' . $this->name . ' non è un ' . $extension . '.';
			}

			return $this;

		}

		/**
		 * Is Success.
		 *
		 * @return boolean
		 */
		public function is_success() {
			if ( empty( $this->errors ) ) {
				return true;
			}
		}

		/**
		 * Get Errors.
		 *
		 * @return array $this->errors
		 */
		public function get_errors() {
			if ( ! $this->is_success() ) {
				return $this->errors;
			}
		}

		/**
		 * Display Errors.
		 *
		 * @return string $html
		 */
		public function display_errors() {

			$html = '<ul>';
			foreach ( $this->get_errors() as $error ) {
				$html .= '<li>' . $error . '</li>';
			}
			$html .= '</ul>';

			return $html;

		}

		/**
		 * Result.
		 *
		 * @return bool|string
		 */
		public function result() {

			if ( ! $this->is_success() ) {

				foreach ( $this->get_errors() as $error ) {
					echo "$error\n";
				}
				exit;

			} else {
				return true;
			}

		}

		/**
		 * Is Int.
		 *
		 * @param  mixed $value  Value.
		 *
		 * @return boolean
		 */
		public static function is_int( $value ) {
			if ( filter_var( $value, FILTER_VALIDATE_INT ) ) {
				return true;
			}
		}

		/**
		 * Is Float
		 *
		 * @param  mixed $value  Value.
		 *
		 * @return boolean
		 */
		public static function is_float( $value ) {
			if ( filter_var( $value, FILTER_VALIDATE_FLOAT ) ) {
				return true;
			}
		}

		/**
		 * Is Alpha.
		 *
		 * @param  mixed $value  Value.
		 *
		 * @return boolean
		 */
		public static function is_alpha( $value ) {
			if (
			filter_var(
				$value, FILTER_VALIDATE_REGEXP, array( 'options' => array( 'regexp' => '/^[a-zA-Z]+$/' ) )
			)
			) {
				return true;
			}
		}

		/**
		 * Is Alpa Numeric.
		 *
		 * @param  mixed $value  Value.
		 *
		 * @return boolean
		 */
		public static function is_alphanum( $value ) {
			if (
			filter_var(
				$value, FILTER_VALIDATE_REGEXP, array( 'options' => array( 'regexp' => '/^[a-zA-Z0-9]+$/' ) )
			)
			) {
				return true;
			}
		}

		/**
		 * Is URL.
		 *
		 * @param  mixed $value  Value.
		 *
		 * @return boolean
		 */
		public static function is_url( $value ) {
			if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
				return true;
			}
		}

		/**
		 * Is URI.
		 *
		 * @param  mixed $value  Value.
		 *
		 * @return boolean
		 */
		public static function is_uri( $value ) {
			if (
			filter_var(
				$value, FILTER_VALIDATE_REGEXP,
				array( 'options' => array( 'regexp' => '/^[A-Za-z0-9-\/_]+$/' ) )
			)
			) {
				return true;
			}
		}

		/**
		 * Is Bool.
		 *
		 * @param  mixed $value  Value.
		 *
		 * @return boolean
		 */
		public static function is_bool( $value ) {
			if ( is_bool( filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ) ) {
				return true;
			}
		}

		/**
		 * Is Email.
		 *
		 * @param  mixed $value  Value.
		 *
		 * @return boolean
		 */
		public static function is_email( $value ) {
			if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
				return true;
			}
		}
	}
}