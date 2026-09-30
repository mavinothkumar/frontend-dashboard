<?php

namespace FED\Services\Auth;

use FED\Contracts\Auth\SocialAuthProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SocialAuthManager
 *
 * Central registry for Social Authentication drivers.
 */
class SocialAuthManager {

	/**
	 * @var SocialAuthProviderInterface[]
	 */
	protected $providers = [];

	/**
	 * Register a social authentication provider.
	 *
	 * @param SocialAuthProviderInterface $provider
	 * @return void
	 */
	public function register( SocialAuthProviderInterface $provider ): void {
		$this->providers[ $provider->getId() ] = $provider;
	}

	/**
	 * Retrieve a registered provider by ID.
	 *
	 * @param string $id
	 * @return SocialAuthProviderInterface|null
	 */
	public function get( string $id ): ?SocialAuthProviderInterface {
		return isset( $this->providers[ $id ] ) ? $this->providers[ $id ] : null;
	}

	/**
	 * Check if a provider is registered.
	 *
	 * @param string $id
	 * @return bool
	 */
	public function has( string $id ): bool {
		return isset( $this->providers[ $id ] );
	}

	/**
	 * Get all registered providers.
	 *
	 * @return SocialAuthProviderInterface[]
	 */
	public function all(): array {
		return $this->providers;
	}
}
