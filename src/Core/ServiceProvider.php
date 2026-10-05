<?php

namespace FED\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Class ServiceProvider
 *
 * Contract for modular service registration and booting.
 */
abstract class ServiceProvider {

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * ServiceProvider constructor.
	 *
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Register services and bindings in the container.
	 *
	 * @return void
	 */
	abstract public function register();

	/**
	 * Boot any services after all bindings are registered.
	 *
	 * @return void
	 */
	public function boot() {
		// Optional boot hook for providers
	}
}
