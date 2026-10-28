<?php

namespace FED\Services\Notifications;

use FED\Core\Logger;
use FED\Services\Mail\MailMessage;
use FED\Services\Mail\MailQueueManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MailService
 *
 * Responsive HTML email sender routed through the unified FED Mail Dispatcher.
 */
class MailService {

	protected MailQueueManager $queueManager;

	public function __construct( ?MailQueueManager $queueManager = null ) {
		$this->queueManager = $queueManager ?? new MailQueueManager();
	}

	/**
	 * Send an HTML email via the unified mail subsystem.
	 *
	 * @param string|array $to
	 * @param string       $subject
	 * @param string       $contentBody
	 * @param array        $attachments
	 * @param array        $headers
	 * @param string       $eventName
	 * @param bool         $forceSync
	 * @return bool
	 */
	public function send(
		$to,
		string $subject,
		string $contentBody,
		array $attachments = array(),
		array $headers = array(),
		string $eventName = 'generic',
		bool $forceSync = false
	): bool {
		$recipients = is_array( $to ) ? $to : array( $to );
		$all_sent   = true;

		$html = $this->wrapTemplate( $subject, $contentBody );

		foreach ( $recipients as $recipient ) {
			$recipient = sanitize_email( $recipient );
			if ( empty( $recipient ) || ! is_email( $recipient ) ) {
				continue;
			}

			$message = new MailMessage(
				$recipient,
				$subject,
				$html,
				$headers,
				$attachments,
				$eventName,
				true
			);

			$result = $this->queueManager->dispatch( $message, $forceSync );

			if ( ! $result->isSuccess() ) {
				Logger::warning( "Failed to dispatch email [{$subject}] to {$recipient}: " . ( $result->getErrorMessage() ?? 'unknown error' ) );
				$all_sent = false;
			}
		}

		return $all_sent;
	}

	/**
	 * Wrap raw email content in modern responsive HTML template.
	 *
	 * @param string $title
	 * @param string $body
	 * @return string
	 */
	protected function wrapTemplate( string $title, string $body ): string {
		$siteName = get_bloginfo( 'name' );
		$siteUrl  = home_url();
		$year     = gmdate( 'Y' );

		$html  = '<!DOCTYPE html><html><head><meta charset="utf-8">';
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
		$html .= '<title>' . esc_html( $title ) . '</title>';
		$html .= '<style>';
		$html .= 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 24px; line-height: 1.6; }';
		$html .= '.email-container { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }';
		$html .= '.email-header { background: #4f46e5; padding: 28px 32px; text-align: center; color: #ffffff; }';
		$html .= '.email-header h1 { margin: 0; font-size: 20px; font-weight: 700; color: #ffffff; }';
		$html .= '.email-body { padding: 32px; }';
		$html .= '.email-footer { background: #f1f5f9; padding: 20px 32px; text-align: center; font-size: 12px; color: #64748b; }';
		$html .= 'a.btn { display: inline-block; background: #4f46e5; color: #ffffff !important; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; margin-top: 16px; }';
		$html .= '</style></head><body>';
		$html .= '<div class="email-container">';
		$html .= '<div class="email-header"><h1>' . esc_html( $siteName ) . '</h1></div>';
		$html .= '<div class="email-body">' . $body . '</div>';
		$html .= '<div class="email-footer">&copy; ' . esc_html( $year ) . ' <a href="' . esc_url( $siteUrl ) . '" style="color: #64748b;">' . esc_html( $siteName ) . '</a>. ' . esc_html__( 'All rights reserved.', 'frontend-dashboard' ) . '</div>';
		$html .= '</div></body></html>';

		return $html;
	}
}
