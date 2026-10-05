<?php

namespace FED\Services\Ui;

use FED\Hooks\HookLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ThemeManager
 *
 * Manages dynamic brand colors, CSS custom property tokens, and theme styles.
 */
class ThemeManager {

	const THEME_OPTION = 'fed_theme_tokens';

	public function register_hooks( HookLoader $loader ): void {
		$loader->add_action( 'wp_head', $this, 'injectThemeCssTokens' );
		$loader->add_action( 'admin_head', $this, 'injectThemeCssTokens' );
	}

	/**
	 * Get current theme color tokens.
	 *
	 * @return array
	 */
	public function getTokens(): array {
		$defaults = array(
			'primary'   => '#4f46e5', // Indigo-600
			'secondary' => '#0ea5e9', // Sky-500
			'success'   => '#10b981', // Emerald-500
			'warning'   => '#f59e0b', // Amber-500
			'danger'    => '#ef4444', // Red-500
			'radius'    => '0.75rem', // 12px
		);

		$saved = get_option( self::THEME_OPTION, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
	}

	/**
	 * Save updated theme tokens.
	 *
	 * @param array $tokens
	 * @return bool
	 */
	public function updateTokens( array $tokens ): bool {
		$sanitized = array(
			'primary'   => sanitize_hex_color( $tokens['primary'] ?? '#4f46e5' ),
			'secondary' => sanitize_hex_color( $tokens['secondary'] ?? '#0ea5e9' ),
			'success'   => sanitize_hex_color( $tokens['success'] ?? '#10b981' ),
			'warning'   => sanitize_hex_color( $tokens['warning'] ?? '#f59e0b' ),
			'danger'    => sanitize_hex_color( $tokens['danger'] ?? '#ef4444' ),
			'radius'    => sanitize_text_field( $tokens['radius'] ?? '0.75rem' ),
		);

		return update_option( self::THEME_OPTION, $sanitized );
	}

	/**
	 * Inject dynamic CSS custom properties into page header.
	 */
	public function injectThemeCssTokens(): void {
		$tokens = $this->getTokens();
		?>
		<style id="fed-theme-tokens">
			:root {
				--fed-primary: <?php echo esc_attr( $tokens['primary'] ); ?>;
				--fed-secondary: <?php echo esc_attr( $tokens['secondary'] ); ?>;
				--fed-success: <?php echo esc_attr( $tokens['success'] ); ?>;
				--fed-warning: <?php echo esc_attr( $tokens['warning'] ); ?>;
				--fed-danger: <?php echo esc_attr( $tokens['danger'] ); ?>;
				--fed-radius: <?php echo esc_attr( $tokens['radius'] ); ?>;
			}
		</style>
		<?php
	}
}
