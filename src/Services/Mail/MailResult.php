<?php

namespace FED\Services\Mail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MailResult
 *
 * Outcome of a mail dispatch attempt by a MailDriver.
 */
class MailResult {

	/**
	 * Whether the delivery handoff succeeded.
	 */
	protected bool $success;

	/**
	 * Unique message or transaction ID returned by transport (if available).
	 */
	protected ?string $messageId;

	/**
	 * Error message string if dispatch failed.
	 */
	protected ?string $errorMessage;

	/**
	 * Raw transport response payload (SMTP conversation or HTTP JSON body).
	 */
	protected mixed $responseData;

	public function __construct( bool $success, ?string $errorMessage = null, ?string $messageId = null, mixed $responseData = null ) {
		$this->success      = $success;
		$this->errorMessage = $errorMessage;
		$this->messageId    = $messageId;
		$this->responseData = $responseData;
	}

	public static function success( ?string $messageId = null, mixed $responseData = null ): self {
		return new self( true, null, $messageId, $responseData );
	}

	public static function failure( string $errorMessage, mixed $responseData = null ): self {
		return new self( false, $errorMessage, null, $responseData );
	}

	public function isSuccess(): bool {
		return $this->success;
	}

	public function getMessageId(): ?string {
		return $this->messageId;
	}

	public function getErrorMessage(): ?string {
		return $this->errorMessage;
	}

	public function getResponseData(): mixed {
		return $this->responseData;
	}
}
