<?php

namespace FED\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class View
 *
 * Scoped template renderer supporting theme overrides and reusable components.
 */
class View {

	/**
	 * @var string Base template directory.
	 */
	protected $basePath;

	/**
	 * View constructor.
	 *
	 * @param string|null $basePath
	 */
	public function __construct( $basePath = null ) {
		$this->basePath = $basePath ?: ( defined( 'BC_FED_PLUGIN_DIR' ) ? BC_FED_PLUGIN_DIR . '/templates' : '' );
	}

	/**
	 * Render a template file with scoped data.
	 *
	 * @param string $template Template name (e.g. 'dashboard' or 'auth/login')
	 * @param array  $data     Data variables to extract inside template.
	 * @return string Rendered HTML content.
	 */
	public function render( $template, array $data = array() ) {
		$filePath = $this->locateTemplate( $template );

		if ( ! $filePath || ! file_exists( $filePath ) ) {
			return sprintf( '<!-- Template [%s] not found -->', esc_html( $template ) );
		}

		ob_start();
		extract( $data, EXTR_SKIP );
		include $filePath;
		return ob_get_clean();
	}

	/**
	 * Render a reusable component partial.
	 *
	 * @param string $component
	 * @param array  $data
	 * @return string
	 */
	public function component( $component, array $data = array() ) {
		return $this->render( 'components/' . $component, $data );
	}

	/**
	 * Locate template file with theme override support.
	 *
	 * @param string $template
	 * @return string|false
	 */
	protected function locateTemplate( $template ) {
		$template = ltrim( $template, '/' );
		if ( substr( $template, -4 ) !== '.php' ) {
			$template .= '.php';
		}

		// 1. Check in child theme: {child_theme}/frontend-dashboard/{template}
		$childPath = get_stylesheet_directory() . '/frontend-dashboard/' . $template;
		if ( file_exists( $childPath ) ) {
			return $childPath;
		}

		// 2. Check in parent theme: {template_dir}/frontend-dashboard/{template}
		$parentPath = get_template_directory() . '/frontend-dashboard/' . $template;
		if ( file_exists( $parentPath ) ) {
			return $parentPath;
		}

		// 3. Fallback to plugin template folder
		$pluginPath = $this->basePath . '/' . $template;
		if ( file_exists( $pluginPath ) ) {
			return $pluginPath;
		}

		return false;
	}
}
