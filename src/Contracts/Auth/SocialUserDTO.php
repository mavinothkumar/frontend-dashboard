<?php

namespace FED\Contracts\Auth;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SocialUserDTO
 *
 * Data Transfer Object representing an authenticated social identity.
 */
class SocialUserDTO {

	public $id;
	public $email;
	public $name;
	public $avatarUrl;
	public $provider;
	public $raw;

	public function __construct( string $id, string $email, string $name = '', string $avatarUrl = '', string $provider = '', array $raw = [] ) {
		$this->id        = $id;
		$this->email     = $email;
		$this->name      = $name;
		$this->avatarUrl = $avatarUrl;
		$this->provider  = $provider;
		$this->raw       = $raw;
	}
}
