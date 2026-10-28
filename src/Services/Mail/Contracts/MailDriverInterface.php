<?php

namespace FED\Services\Mail\Contracts;

use FED\Services\Mail\MailMessage;
use FED\Services\Mail\MailResult;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface MailDriverInterface
 *
 * Contract for all email dispatch drivers (WP Mail, Native SMTP, or 3rd-party services).
 */
interface MailDriverInterface {

	/**
	 * Unique driver identifier (e.g. 'wp_mail', 'smtp', 'mailsenpai').
	 *
	 * @return string
	 */
	public function getId(): string;

	/**
	 * Human-readable title for admin settings and logs.
	 *
	 * @return string
	 */
	public function getTitle(): string;

	/**
	 * Short description of the driver.
	 *
	 * @return string
	 */
	public function getDescription(): string;

	/**
	 * Send an email message.
	 *
	 * @param MailMessage $message
	 * @return MailResult
	 */
	public function send( MailMessage $message ): MailResult;
}
