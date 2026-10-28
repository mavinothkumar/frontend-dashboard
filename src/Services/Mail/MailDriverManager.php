<?php

namespace FED\Services\Mail;

use FED\Services\Mail\Contracts\MailDriverInterface;
use FED\Services\Mail\Drivers\SmtpDriver;
use FED\Services\Mail\Drivers\WpMailDriver;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MailDriverManager
 *
 * Driver Registry managing built-in and third-party email drivers.
 */
class MailDriverManager {

	/**
	 * Registered drivers cache.
	 *
	 * @var array<string, MailDriverInterface>|null
	 */
	protected ?array $drivers = null;

	/**
	 * Get all registered drivers.
	 *
	 * @return array<string, MailDriverInterface>
	 */
	public function getDrivers(): array {
		if ( null !== $this->drivers ) {
			return $this->drivers;
		}

		$default_drivers = array(
			'wp_mail' => new WpMailDriver(),
			'smtp'    => new SmtpDriver(),
		);

		/**
		 * Filters the list of available email drivers in Frontend Dashboard.
		 *
		 * Allows third-party providers (e.g. MailSenpai, SendGrid, Amazon SES)
		 * to register custom drivers implementing MailDriverInterface.
		 *
		 * @param array<string, MailDriverInterface> $default_drivers
		 */
		$filtered_drivers = apply_filters( 'fed_email_drivers', $default_drivers );

		$valid = array();
		if ( is_array( $filtered_drivers ) ) {
			foreach ( $filtered_drivers as $key => $driver ) {
				if ( $driver instanceof MailDriverInterface ) {
					$valid[ $driver->getId() ] = $driver;
				}
			}
		}

		$this->drivers = ! empty( $valid ) ? $valid : $default_drivers;

		return $this->drivers;
	}

	/**
	 * Get a specific driver by ID.
	 *
	 * @param string $id
	 * @return MailDriverInterface
	 */
	public function getDriver( string $id ): MailDriverInterface {
		$drivers = $this->getDrivers();

		// Handle legacy uppercase 'SMTP' and 'WP_MAIL' mappings
		$normalized = strtolower( $id );

		if ( isset( $drivers[ $normalized ] ) ) {
			return $drivers[ $normalized ];
		}

		return $drivers['wp_mail'] ?? new WpMailDriver();
	}

	/**
	 * Get currently active configured driver.
	 *
	 * @return MailDriverInterface
	 */
	public function getActiveDriver(): MailDriverInterface {
		$settings = get_option( 'fed_settings_email', array() );
		$via      = fed_get_data( 'via', $settings, 'WP_MAIL' );

		return $this->getDriver( (string) $via );
	}
}
