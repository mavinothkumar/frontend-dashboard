<?php

namespace FED\Api;

use FED\Api\Endpoints\AuthEndpoints;
use FED\Api\Endpoints\MenuEndpoints;
use FED\Api\Endpoints\ProfileEndpoints;
use FED\Api\Endpoints\PostEndpoints;
use FED\Database\Repositories\MenuRepository;
use FED\Database\Repositories\UserProfileRepository;
use FED\Hooks\HookLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ApiManager
 *
 * Coordinates registration of all WP REST API endpoints for Frontend Dashboard.
 */
class ApiManager {

	/**
	 * @var MenuRepository
	 */
	protected $menuRepo;

	/**
	 * @var UserProfileRepository
	 */
	protected $profileRepo;

	public function __construct( MenuRepository $menuRepo, UserProfileRepository $profileRepo ) {
		$this->menuRepo    = $menuRepo;
		$this->profileRepo = $profileRepo;
	}

	public function register_hooks( HookLoader $loader ): void {
		$loader->add_action( 'rest_api_init', $this, 'register_routes' );
	}

	public function register_routes(): void {
		( new AuthEndpoints() )->register_routes();
		( new MenuEndpoints( $this->menuRepo ) )->register_routes();
		( new ProfileEndpoints( $this->profileRepo ) )->register_routes();
		( new PostEndpoints() )->register_routes();
		( new \FED\Api\Endpoints\PaymentWebhookEndpoint() )->register_routes();
	}
}
