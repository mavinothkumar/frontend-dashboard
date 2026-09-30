<?php

namespace FED\Core;

use FED\Api\ApiManager;
use FED\Database\MigrationManager;
use FED\Database\Repositories\MenuRepository;
use FED\Database\Repositories\PaymentRepository;
use FED\Database\Repositories\PostRepository;
use FED\Database\Repositories\UserProfileRepository;
use FED\Http\Request;
use FED\Http\Response;
use FED\Http\Security;
use FED\Security\RbacManager;
use FED\Services\Ai\AiManager;
use FED\Services\AssetManager;
use FED\Services\Auth\SocialAuthManager;
use FED\Services\Cron\CronManager;
use FED\Services\Diagnostics\SystemHealthChecker;
use FED\Services\Fields\FieldFactory;
use FED\Services\Logging\AuditLogger;
use FED\Services\Notifications\MailService;
use FED\Services\Notifications\NotificationManager;
use FED\Services\Payments\PaymentGatewayManager;
use FED\Services\Ui\AlertManager;
use FED\Services\Ui\ThemeManager;
use FED\Services\View;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CoreServiceProvider
 *
 * Registers foundational core services into the DI container.
 */
class CoreServiceProvider extends ServiceProvider {

	public function register() {
		// HTTP & Security
		$this->container->singleton( Request::class, function() {
			return Request::capture();
		} );
		$this->container->singleton( Response::class, function() {
			return new Response();
		} );
		$this->container->singleton( Security::class, function() {
			return new Security();
		} );
		$this->container->singleton( RbacManager::class, function() {
			return new RbacManager();
		} );

		// Presentation, Theme & Assets
		$this->container->singleton( View::class, function() {
			return new View();
		} );
		$this->container->singleton( AssetManager::class, function() {
			return new AssetManager( BC_FED_PLUGIN_VERSION );
		} );
		$this->container->singleton( ThemeManager::class, function() {
			return new ThemeManager();
		} );
		$this->container->singleton( AlertManager::class, function() {
			return new AlertManager();
		} );

		// Database Migration Manager
		$this->container->singleton( MigrationManager::class, function() {
			return new MigrationManager();
		} );

		// Database Repositories
		$this->container->singleton( MenuRepository::class, function() {
			return new MenuRepository();
		} );
		$this->container->singleton( UserProfileRepository::class, function() {
			return new UserProfileRepository();
		} );
		$this->container->singleton( PostRepository::class, function() {
			return new PostRepository();
		} );
		$this->container->singleton( PaymentRepository::class, function() {
			return new PaymentRepository();
		} );

		// Field Factory
		$this->container->singleton( FieldFactory::class, function() {
			return new FieldFactory();
		} );

		// Logging & Diagnostics
		$this->container->singleton( AuditLogger::class, function() {
			return new AuditLogger();
		} );
		$this->container->singleton( SystemHealthChecker::class, function() {
			return new SystemHealthChecker();
		} );

		// Cron & Background Scheduler
		$this->container->singleton( CronManager::class, function() {
			return new CronManager();
		} );

		// Notifications & Email
		$this->container->singleton( MailService::class, function() {
			return new MailService();
		} );
		$this->container->singleton( NotificationManager::class, function() {
			return new NotificationManager();
		} );

		// Commercial Pro Provider Managers
		$this->container->singleton( PaymentGatewayManager::class, function() {
			return new PaymentGatewayManager();
		} );
		$this->container->singleton( SocialAuthManager::class, function() {
			return new SocialAuthManager();
		} );
		$this->container->singleton( AiManager::class, function() {
			return new AiManager();
		} );

		// REST API Manager
		$this->container->singleton( ApiManager::class, function( $c ) {
			return new ApiManager(
				$c->make( MenuRepository::class ),
				$c->make( UserProfileRepository::class )
			);
		} );
	}

	public function boot() {
		// Auto-run pending database migrations if any
		$migrationManager = $this->container->make( MigrationManager::class );
		$migrationManager->run();
	}
}
