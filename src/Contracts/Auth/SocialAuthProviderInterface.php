<?php

namespace FED\Contracts\Auth;

use FED\Http\Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface SocialAuthProviderInterface
 *
 * Contract for OAuth and Social Authentication drivers (Google, Facebook, Apple, GitHub).
 */
interface SocialAuthProviderInterface {

	/**
	 * Unique provider slug (e.g. 'google', 'facebook', 'github').
	 *
	 * @return string
	 */
	public function getId(): string;

	/**
	 * Provider display name.
	 *
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Generate the OAuth authorization URL.
	 *
	 * @param string $redirectUri
	 * @return string
	 */
	public function getAuthorizationUrl( string $redirectUri ): string;

	/**
	 * Handle the OAuth return callback and retrieve the social user identity.
	 *
	 * @param Request $request
	 * @return SocialUserDTO
	 */
	public function handleCallback( Request $request ): SocialUserDTO;
}
