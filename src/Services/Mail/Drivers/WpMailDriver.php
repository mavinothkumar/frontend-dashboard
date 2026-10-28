<?php

namespace FED\Services\Mail\Drivers;

use FED\Services\Mail\Contracts\MailDriverInterface;
use FED\Services\Mail\MailMessage;
use FED\Services\Mail\MailResult;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WpMailDriver
 *
 * Default WordPress core mail dispatch driver.
 */
class WpMailDriver implements MailDriverInterface {

	public function getId(): string {
		return 'wp_mail';
	}

	public function getTitle(): string {
		return __( 'Default WordPress Mail', 'frontend-dashboard' );
	}

	public function getDescription(): string {
		return __( 'Standard wp_mail() function without external credentials or SMTP.', 'frontend-dashboard' );
	}

	public function send( MailMessage $message ): MailResult {
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

		if ( $sent ) {
			return MailResult::success( $message->getUuid() );
		}

		$error_text = ! empty( $mail_error )
			? $mail_error
			: __( 'wp_mail() returned false. Host mail() or server MTA configuration may be disabled.', 'frontend-dashboard' );

		return MailResult::failure( $error_text );
	}
}
