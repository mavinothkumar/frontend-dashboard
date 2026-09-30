<?php

namespace FED\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Application
 *
 * Central application kernel for Frontend Dashboard.
 */
class Application {

	/**
	 * @var Application
	 */
	protected static $instance;

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var ServiceProvider[]
	 */
	protected $providers = [];

	/**
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * Application constructor.
	 */
	public function __construct() {
		$this->container = new Container();
		$this->container->instance( Container::class, $this->container );
		$this->container->instance( Application::class, $this );

		self::$instance = $this;
	}

	/**
	 * Get the shared Application instance.
	 *
	 * @return Application
	 */
	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get the internal DI container.
	 *
	 * @return Container
	 */
	public function getContainer() {
		return $this->container;
	}

	/**
	 * Register a service provider.
	 *
	 * @param string $providerClass
	 * @return ServiceProvider
	 */
	public function register( $providerClass ) {
		$provider = new $providerClass( $this->container );
		$provider->register();

		$this->providers[] = $provider;

		if ( $this->booted ) {
			$provider->boot();
		}

		return $provider;
	}

	/**
	 * Boot all registered service providers.
	 *
	 * @return void
	 */
	public function boot() {
		if ( $this->booted ) {
			return;
		}

		foreach ( $this->providers as $provider ) {
			$provider->boot();
		}

		$this->booted = true;
	}

	/**
	 * Resolve a service from the container.
	 *
	 * @param string $abstract
	 * @param array  $parameters
	 * @return mixed
	 */
	public function make( $abstract, array $parameters = [] ) {
		return $this->container->make( $abstract, $parameters );
	}
}
