<?php

namespace FED\Services\Mail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MailMessage
 *
 * Immutable Value Object representing an email payload in Frontend Dashboard.
 */
class MailMessage {

	/**
	 * Unique message UUID for idempotency and log correlation.
	 */
	protected string $uuid;

	/**
	 * Logical event identifier (e.g. 'forgot_password', 'new_user', 'test_email').
	 */
	protected string $eventName;

	/**
	 * Recipient email address.
	 */
	protected string $to;

	/**
	 * Email subject line.
	 */
	protected string $subject;

	/**
	 * Email body content (HTML or plain text).
	 */
	protected string $body;

	/**
	 * Optional email headers.
	 */
	protected array $headers;

	/**
	 * Optional file attachments.
	 */
	protected array $attachments;

	/**
	 * Whether the message body is HTML.
	 */
	protected bool $isHtml;

	/**
	 * Optional contextual metadata (e.g. user_id, ip_address).
	 */
	protected array $metadata;

	/**
	 * Constructor.
	 *
	 * @param string      $to
	 * @param string      $subject
	 * @param string      $body
	 * @param array       $headers
	 * @param array       $attachments
	 * @param string      $eventName
	 * @param bool        $isHtml
	 * @param array       $metadata
	 * @param string|null $uuid
	 */
	public function __construct(
		string $to,
		string $subject,
		string $body,
		array $headers = array(),
		array $attachments = array(),
		string $eventName = 'generic',
		bool $isHtml = true,
		array $metadata = array(),
		?string $uuid = null
	) {
		$this->to          = sanitize_email( $to );
		$this->subject     = sanitize_text_field( $subject );
		$this->body        = $body;
		$this->headers     = $headers;
		$this->attachments = $attachments;
		$this->eventName   = sanitize_key( $eventName );
		$this->isHtml      = $isHtml;
		$this->metadata    = $metadata;
		$this->uuid        = ! empty( $uuid ) ? sanitize_text_field( $uuid ) : wp_generate_uuid4();
	}

	public function getUuid(): string {
		return $this->uuid;
	}

	public function getEventName(): string {
		return $this->eventName;
	}

	public function getTo(): string {
		return $this->to;
	}

	public function getSubject(): string {
		return $this->subject;
	}

	public function getBody(): string {
		return $this->body;
	}

	public function getHeaders(): array {
		return $this->headers;
	}

	public function getAttachments(): array {
		return $this->attachments;
	}

	public function isHtml(): bool {
		return $this->isHtml;
	}

	public function getMetadata(): array {
		return $this->metadata;
	}
}
