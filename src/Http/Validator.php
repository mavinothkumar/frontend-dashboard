<?php

namespace FED\Http;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Validator
 *
 * Reusable, enterprise-grade validation engine.
 */
class Validator {

	/**
	 * @var array Raw input data.
	 */
	protected $data = array();

	/**
	 * @var array Validation rules.
	 */
	protected $rules = array();

	/**
	 * @var array Custom error messages.
	 */
	protected $messages = array();

	/**
	 * @var array Collected validation errors.
	 */
	protected $errors = array();

	/**
	 * Validator constructor.
	 *
	 * @param array $data
	 * @param array $rules
	 * @param array $messages
	 */
	public function __construct( array $data, array $rules, array $messages = array() ) {
		$this->data     = $data;
		$this->rules    = $rules;
		$this->messages = $messages;

		$this->validate();
	}

	/**
	 * Static factory method.
	 *
	 * @param array $data
	 * @param array $rules
	 * @param array $messages
	 * @return static
	 */
	public static function make( array $data, array $rules, array $messages = array() ) {
		return new static( $data, $rules, $messages );
	}

	/**
	 * Check if validation passes.
	 *
	 * @return bool
	 */
	public function passes(): bool {
		return empty( $this->errors );
	}

	/**
	 * Check if validation fails.
	 *
	 * @return bool
	 */
	public function fails(): bool {
		return ! $this->passes();
	}

	/**
	 * Get all validation errors.
	 *
	 * @return array
	 */
	public function errors(): array {
		return $this->errors;
	}

	/**
	 * Get first error message.
	 *
	 * @return string|null
	 */
	public function firstError(): ?string {
		if ( empty( $this->errors ) ) {
			return null;
		}
		$firstField = reset( $this->errors );
		return is_array( $firstField ) ? reset( $firstField ) : (string) $firstField;
	}

	/**
	 * Get only the validated fields.
	 *
	 * @return array
	 */
	public function validated(): array {
		$validated = array();
		foreach ( array_keys( $this->rules ) as $field ) {
			if ( array_key_exists( $field, $this->data ) ) {
				$validated[ $field ] = $this->data[ $field ];
			}
		}
		return $validated;
	}

	/**
	 * Execute validation rules against data.
	 *
	 * @return void
	 */
	protected function validate(): void {
		foreach ( $this->rules as $field => $fieldRules ) {
			if ( is_string( $fieldRules ) ) {
				$fieldRules = explode( '|', $fieldRules );
			}

			$value = isset( $this->data[ $field ] ) ? $this->data[ $field ] : null;

			foreach ( $fieldRules as $ruleStr ) {
				$ruleParts = explode( ':', $ruleStr, 2 );
				$ruleName  = strtolower( trim( $ruleParts[0] ) );
				$param     = isset( $ruleParts[1] ) ? $ruleParts[1] : null;

				// Skip if optional and empty (unless rule is 'required')
				if ( 'required' !== $ruleName && ( is_null( $value ) || '' === $value ) ) {
					continue;
				}

				$this->applyRule( $field, $value, $ruleName, $param );
			}
		}
	}

	/**
	 * Apply an individual rule.
	 *
	 * @param string $field
	 * @param mixed  $value
	 * @param string $ruleName
	 * @param mixed  $param
	 * @return void
	 */
	protected function applyRule( string $field, $value, string $ruleName, $param = null ): void {
		switch ( $ruleName ) {
			case 'required':
				if ( is_null( $value ) || '' === $value || ( is_array( $value ) && empty( $value ) ) ) {
					$this->addError( $field, 'required', "The {$field} field is required." );
				}
				break;

			case 'email':
				if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
					$this->addError( $field, 'email', "The {$field} must be a valid email address." );
				}
				break;

			case 'numeric':
				if ( ! is_numeric( $value ) ) {
					$this->addError( $field, 'numeric', "The {$field} must be a number." );
				}
				break;

			case 'integer':
			case 'int':
				if ( false === filter_var( $value, FILTER_VALIDATE_INT ) ) {
					$this->addError( $field, 'integer', "The {$field} must be an integer." );
				}
				break;

			case 'string':
				if ( ! is_string( $value ) ) {
					$this->addError( $field, 'string', "The {$field} must be a string." );
				}
				break;

			case 'url':
				if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
					$this->addError( $field, 'url', "The {$field} must be a valid URL." );
				}
				break;

			case 'min':
				$min = (int) $param;
				if ( is_numeric( $value ) && $value < $min ) {
					$this->addError( $field, 'min', "The {$field} must be at least {$min}." );
				} elseif ( is_string( $value ) && mb_strlen( $value ) < $min ) {
					$this->addError( $field, 'min', "The {$field} must be at least {$min} characters." );
				} elseif ( is_array( $value ) && count( $value ) < $min ) {
					$this->addError( $field, 'min', "The {$field} must have at least {$min} items." );
				}
				break;

			case 'max':
				$max = (int) $param;
				if ( is_numeric( $value ) && $value > $max ) {
					$this->addError( $field, 'max', "The {$field} may not be greater than {$max}." );
				} elseif ( is_string( $value ) && mb_strlen( $value ) > $max ) {
					$this->addError( $field, 'max', "The {$field} may not be greater than {$max} characters." );
				} elseif ( is_array( $value ) && count( $value ) > $max ) {
					$this->addError( $field, 'max', "The {$field} may not have more than {$max} items." );
				}
				break;

			case 'in':
				$allowed = explode( ',', (string) $param );
				if ( ! in_array( (string) $value, $allowed, true ) ) {
					$this->addError( $field, 'in', "The selected {$field} is invalid." );
				}
				break;

			case 'confirmed':
				$confirmationKey   = $field . '_confirmation';
				$confirmationValue = isset( $this->data[ $confirmationKey ] ) ? $this->data[ $confirmationKey ] : null;
				if ( $value !== $confirmationValue ) {
					$this->addError( $field, 'confirmed', "The {$field} confirmation does not match." );
				}
				break;

			case 'unique':
				// e.g. unique:users,user_email
				if ( $param ) {
					global $wpdb;
					$parts = explode( ',', $param );
					$table = $wpdb->prefix . sanitize_key( $parts[0] );
					$col   = sanitize_key( isset( $parts[1] ) ? $parts[1] : $field );

					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$exists = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$table}` WHERE `{$col}` = %s", sanitize_text_field( (string) $value ) ) );

					if ( $exists > 0 ) {
						$this->addError( $field, 'unique', "The {$field} has already been taken." );
					}
				}
				break;
		}
	}

	/**
	 * Add an error for a field.
	 *
	 * @param string $field
	 * @param string $rule
	 * @param string $defaultMessage
	 * @return void
	 */
	protected function addError( string $field, string $rule, string $defaultMessage ): void {
		$customKey = "{$field}.{$rule}";
		$message   = isset( $this->messages[ $customKey ] )
			? $this->messages[ $customKey ]
			: ( isset( $this->messages[ $field ] ) ? $this->messages[ $field ] : $defaultMessage );

		$this->errors[ $field ][] = $message;
	}
}
