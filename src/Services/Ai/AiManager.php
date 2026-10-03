<?php

namespace FED\Services\Ai;

use FED\Contracts\Ai\AiDriverInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AiManager
 *
 * Central registry and coordinator for AI Copilot drivers.
 */
class AiManager {

	/**
	 * @var AiDriverInterface[]
	 */
	protected $drivers = array();

	/**
	 * @var string|null Default driver ID.
	 */
	protected $defaultDriver = null;

	/**
	 * Register an AI driver.
	 *
	 * @param AiDriverInterface $driver
	 * @param bool              $isDefault
	 * @return void
	 */
	public function register( AiDriverInterface $driver, bool $isDefault = false ): void {
		$this->drivers[ $driver->getId() ] = $driver;

		if ( $isDefault || is_null( $this->defaultDriver ) ) {
			$this->defaultDriver = $driver->getId();
		}
	}

	/**
	 * Get a registered driver by ID.
	 *
	 * @param string|null $id
	 * @return AiDriverInterface|null
	 */
	public function get( string $id = null ): ?AiDriverInterface {
		$driverId = $id ?: $this->defaultDriver;
		return isset( $this->drivers[ $driverId ] ) ? $this->drivers[ $driverId ] : null;
	}

	/**
	 * Check if an AI driver is available.
	 *
	 * @param string|null $id
	 * @return bool
	 */
	public function has( string $id = null ): bool {
		$driverId = $id ?: $this->defaultDriver;
		return isset( $this->drivers[ $driverId ] );
	}

	/**
	 * Get all registered drivers.
	 *
	 * @return AiDriverInterface[]
	 */
	public function all(): array {
		return $this->drivers;
	}
}
