<?php

namespace FED\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Container
 *
 * Lightweight, high-performance Dependency Injection Container.
 */
class Container {

	/**
	 * @var array The container's bindings.
	 */
	protected $bindings = array();

	/**
	 * @var array The container's shared instances.
	 */
	protected $instances = array();

	/**
	 * Register a binding with the container.
	 *
	 * @param string          $abstract
	 * @param \Closure|string $concrete
	 * @param bool            $shared
	 */
	public function bind( $abstract, $concrete = null, $shared = false ) {
		if ( is_null( $concrete ) ) {
			$concrete = $abstract;
		}

		$this->bindings[ $abstract ] = array(
			'concrete' => $concrete,
			'shared'   => $shared,
		);
	}

	/**
	 * Register a shared binding (singleton) in the container.
	 *
	 * @param string          $abstract
	 * @param \Closure|string $concrete
	 */
	public function singleton( $abstract, $concrete = null ) {
		$this->bind( $abstract, $concrete, true );
	}

	/**
	 * Register an existing instance as shared in the container.
	 *
	 * @param string $abstract
	 * @param mixed  $instance
	 * @return mixed
	 */
	public function instance( $abstract, $instance ) {
		$this->instances[ $abstract ] = $instance;
		return $instance;
	}

	/**
	 * Resolve the given type from the container.
	 *
	 * @param string $abstract
	 * @param array  $parameters
	 * @return mixed
	 * @throws \Exception
	 */
	public function make( $abstract, array $parameters = array() ) {
		// Return existing singleton if already instantiated
		if ( isset( $this->instances[ $abstract ] ) ) {
			return $this->instances[ $abstract ];
		}

		$concrete = $abstract;
		$shared   = false;

		if ( isset( $this->bindings[ $abstract ] ) ) {
			$concrete = $this->bindings[ $abstract ]['concrete'];
			$shared   = $this->bindings[ $abstract ]['shared'];
		}

		// If concrete is a Closure, invoke it
		if ( $concrete instanceof \Closure ) {
			$object = $concrete( $this, $parameters );
		} else {
			$object = $this->build( $concrete, $parameters );
		}

		if ( $shared ) {
			$this->instances[ $abstract ] = $object;
		}

		return $object;
	}

	/**
	 * Check if the given abstract type has been bound.
	 *
	 * @param string $abstract
	 * @return bool
	 */
	public function has( $abstract ) {
		return isset( $this->bindings[ $abstract ] ) || isset( $this->instances[ $abstract ] );
	}

	/**
	 * Instantiate a concrete instance with reflection.
	 *
	 * @param string $concrete
	 * @param array  $parameters
	 * @return object
	 * @throws \Exception
	 */
	protected function build( $concrete, array $parameters = array() ) {
		if ( ! class_exists( $concrete ) ) {
			throw new \Exception( esc_html( "Target class [$concrete] does not exist." ) );
		}

		$reflector = new \ReflectionClass( $concrete );

		if ( ! $reflector->isInstantiable() ) {
			throw new \Exception( esc_html( "Target [$concrete] is not instantiable." ) );
		}

		$constructor = $reflector->getConstructor();

		if ( is_null( $constructor ) ) {
			return new $concrete();
		}

		$dependencies = $constructor->getParameters();
		$instances    = array();

		foreach ( $dependencies as $dependency ) {
			$name = $dependency->getName();

			if ( array_key_exists( $name, $parameters ) ) {
				$instances[] = $parameters[ $name ];
				continue;
			}

			$type = $dependency->getType();
			if ( $type && ! $type->isBuiltin() ) {
				$className   = $type->getName();
				$instances[] = $this->make( $className );
				continue;
			}

			if ( $dependency->isDefaultValueAvailable() ) {
				$instances[] = $dependency->getDefaultValue();
				continue;
			}

			throw new \Exception( esc_html( "Unresolvable dependency [{$dependency->getName()}] in class [{$concrete}]." ) );
		}

		return $reflector->newInstanceArgs( $instances );
	}
}
