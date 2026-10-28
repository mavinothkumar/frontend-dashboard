<?php

namespace FED\Services\Mail\Drivers;

use FED\Services\Mail\Contracts\MailDriverInterface;
use FED\Services\Mail\MailMessage;
use FED\Services\Mail\MailResult;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SmtpDriver
 *
 * Dedicated SMTP Relay Driver with TLS/SSL encryption and authenticated sender alignment.
 */
class SmtpDriver implements MailDriverInterface {

	public function getId(): string {
		return 'smtp';
	}

	public function getTitle(): string {
		return __( 'Authenticated SMTP Relay', 'frontend-dashboard' );
	}

	public function getDescription(): string {
		return __( 'Direct SMTP relay (e.g. MailSenpai, Mailgun, SendGrid, Amazon SES, or custom SMTP host).', 'frontend-dashboard' );
	}

	public function send( MailMessage $message ): MailResult {
		$settings = get_option( 'fed_settings_email', array() );

		$host       = (string) fed_get_data( 'smtp.host_name', $settings, '' );
		$port       = (int) fed_get_data( 'smtp.port', $settings, 587 );
		$username   = (string) fed_get_data( 'smtp.user_name', $settings, '' );
		$password   = (string) fed_get_data( 'smtp.password', $settings, '' );
		$encryption = strtolower( (string) fed_get_data( 'smtp.encryption', $settings, 'tls' ) );
		$auth       = fed_get_data( 'smtp.auth', $settings, 'yes' );
		$from_email = fed_get_data( 'credentials.email', $settings, get_option( 'admin_email' ) );
		$from_name  = fed_get_data( 'credentials.from_name', $settings, get_bloginfo( 'name' ) );

		if ( empty( $host ) ) {
			return MailResult::failure( __( 'SMTP Host is not configured in Frontend Dashboard Email Settings.', 'frontend-dashboard' ) );
		}

		$phpmailer_configurator = function ( $phpmailer ) use ( $host, $port, $username, $password, $encryption, $auth, $from_email, $from_name ) {
			$phpmailer->isSMTP();
			$phpmailer->Host     = $host;
			$phpmailer->Port     = ! empty( $port ) ? $port : 587;
			$phpmailer->SMTPAuth = ( 'no' === $auth || false === $auth || '0' === $auth ) ? false : true;
			$phpmailer->Username = $username;
			$phpmailer->Password = $password;

			if ( 'none' === $encryption ) {
				$phpmailer->SMTPSecure  = '';
				$phpmailer->SMTPAutoTLS = false;
			} elseif ( 'starttls' === $encryption || 'tls' === $encryption ) {
				$phpmailer->SMTPSecure = 'tls';
			} elseif ( 'ssl' === $encryption ) {
				$phpmailer->SMTPSecure = 'ssl';
			} else {
				$phpmailer->SMTPSecure = $encryption;
			}

			// Ensure authenticated sender and envelope Return-Path match verified sender
			if ( ! empty( $from_email ) && is_email( $from_email ) ) {
				try {
					$phpmailer->setFrom( $from_email, ! empty( $from_name ) ? $from_name : '' );
					$phpmailer->Sender = $from_email;
				} catch ( \Exception $e ) {
					// Fallback silently if setFrom throws.
				}
			}
		};

		add_action( 'phpmailer_init', $phpmailer_configurator, 999 );

		$headers   = $message->getHeaders();
		$has_ctype = false;
		foreach ( $headers as $h ) {
			if ( stripos( $h, 'content-type' ) !== false ) {
				$has_ctype = true;
				break;
			}
		}

		if ( ! $has_ctype ) {
			$headers[] = $message->isHtml()
				? 'Content-Type: text/html; charset=UTF-8'
				: 'Content-Type: text/plain; charset=UTF-8';
		}

		$mail_error     = null;
		$failed_handler = function ( $wp_error ) use ( &$mail_error ) {
			if ( is_wp_error( $wp_error ) ) {
				$mail_error = $wp_error->get_error_message();
			}
		};

		add_action( 'wp_mail_failed', $failed_handler );

		$sent = wp_mail(
			$message->getTo(),
			$message->getSubject(),
			$message->getBody(),
			$headers,
			$message->getAttachments()
		);

		remove_action( 'wp_mail_failed', $failed_handler );
		remove_action( 'phpmailer_init', $phpmailer_configurator, 999 );

		if ( $sent ) {
			return MailResult::success( $message->getUuid() );
		}

		$error_text = ! empty( $mail_error )
			? $mail_error
			: __( 'SMTP delivery failed. Please check your SMTP host, port, credentials, and SSL/TLS certificate.', 'frontend-dashboard' );

		return MailResult::failure( $error_text );
	}
}
