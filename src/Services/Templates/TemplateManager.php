<?php

namespace FED\Services\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TemplateManager
 *
 * Manages Dashboard Templates registry, Pro template extensibility, and template resolution.
 */
class TemplateManager {

	/**
	 * Default fallback template key.
	 */
	const DEFAULT_TEMPLATE = 'default';

	/**
	 * Singleton instance.
	 *
	 * @var TemplateManager|null
	 */
	private static $instance = null;

	/**
	 * Registered templates cache.
	 *
	 * @var array|null
	 */
	private $templates = null;

	/**
	 * Get singleton instance.
	 *
	 * @return TemplateManager
	 */
	public static function instance(): TemplateManager {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @param \FED\Hooks\HookLoader|null $loader
	 */
	public function register_hooks( $loader = null ) {
		if ( $loader ) {
			$loader->add_filter( 'frontend-dashboard_template_paths', $this, 'filter_template_paths', 20 );
			$loader->add_action( 'widgets_init', $this, 'register_dashboard_sidebars' );
		} else {
			add_filter( 'frontend-dashboard_template_paths', [ $this, 'filter_template_paths' ], 20 );
			add_action( 'widgets_init', [ $this, 'register_dashboard_sidebars' ] );
		}
	}

	/**
	 * Register Dashboard widget areas.
	 */
	public function register_dashboard_sidebars() {
		register_sidebar(
			[
				'name'          => __( 'FED Right Sidebar', 'frontend-dashboard' ),
				'id'            => 'fed_dashboard_right_sidebar',
				'description'   => __( 'The Frontend Dashboard Right Sidebar widget area for custom widgets and announcements.', 'frontend-dashboard' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s mb-4 p-4 bg-white rounded-xl border border-slate-200">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title text-sm font-bold text-slate-800 mb-2">',
				'after_title'   => '</h3>',
			]
		);
	}

	/**
	 * Get all registered templates.
	 *
	 * Pro add-ons or custom plugins can hook into `fed_registered_dashboard_templates`
	 * to dynamically add new layout models.
	 *
	 * @return array
	 */
	public function get_registered_templates(): array {
		if ( null !== $this->templates ) {
			return $this->templates;
		}

		$core_templates = [
			'default' => [
				'id'          => 'default',
				'name'        => __( 'Modern App Shell (Default)', 'frontend-dashboard' ),
				'description' => __( 'Enterprise full-height sticky sidebar with fluid content canvas, live color theming, and responsive layout.', 'frontend-dashboard' ),
				'version'     => '3.0.0',
				'author'      => 'Buffercode',
				'is_pro'      => false,
				'path'        => BC_FED_PLUGIN_DIR . '/templates/',
				'thumbnail'   => plugins_url( '/assets/admin/images/templates/default.jpg', BC_FED_PLUGIN ),
				'badge'       => __( 'Core Native', 'frontend-dashboard' ),
			],
		];

		$this->templates = apply_filters( 'fed_registered_dashboard_templates', $core_templates );

		return $this->templates;
	}

	/**
	 * Get active template model ID.
	 *
	 * @return string
	 */
	public function get_active_template_id(): string {
		$upl_settings = get_option( 'fed_admin_settings_upl', [] );
		$model        = isset( $upl_settings['settings']['fed_upl_template_model'] )
			? sanitize_text_field( $upl_settings['settings']['fed_upl_template_model'] )
			: self::DEFAULT_TEMPLATE;

		$registered = $this->get_registered_templates();
		if ( ! array_key_exists( $model, $registered ) ) {
			return self::DEFAULT_TEMPLATE;
		}

		return $model;
	}

	/**
	 * Get active template metadata.
	 *
	 * @return array
	 */
	public function get_active_template(): array {
		$registered = $this->get_registered_templates();
		$active_id  = $this->get_active_template_id();

		return isset( $registered[ $active_id ] ) ? $registered[ $active_id ] : $registered[ self::DEFAULT_TEMPLATE ];
	}

	/**
	 * Filter template paths based on active template model.
	 *
	 * @param array $paths
	 * @return array
	 */
	public function filter_template_paths( array $paths ): array {
		$active_template = $this->get_active_template();

		if ( ! empty( $active_template['path'] ) && is_dir( $active_template['path'] ) ) {
			// Priority 25: Active custom/pro template path
			$paths[25] = trailingslashit( $active_template['path'] );
		}

		return $paths;
	}
}