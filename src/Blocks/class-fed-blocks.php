<?php
/**
 * Gutenberg Blocks Registration & Server-Side Rendering.
 *
 * @package Frontend Dashboard.
 */

namespace FED\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FED_Blocks
 */
class FED_Blocks {

	/**
	 * Singleton instance.
	 *
	 * @var FED_Blocks|null
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_filter( 'block_categories_all', array( $this, 'register_block_category' ), 10, 2 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		add_filter( 'fed_show_frontend_script_on_custom_condition', array( $this, 'check_blocks_in_content' ) );
	}

	/**
	 * Get instance.
	 *
	 * @return FED_Blocks
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register Frontend Dashboard Block Category.
	 *
	 * @param array                    $categories Categories list.
	 * @param \WP_Block_Editor_Context $context    Context.
	 * @return array
	 */
	public function register_block_category( $categories, $context ) {
		return array_merge(
			array(
				array(
					'slug'  => 'frontend-dashboard',
					'title' => __( 'Frontend Dashboard', 'frontend-dashboard' ),
					'icon'  => 'dashboard',
				),
			),
			$categories
		);
	}

	/**
	 * Enqueue Block Editor Assets.
	 */
	public function enqueue_editor_assets() {
		$asset_file = plugin_dir_path( BC_FED_PLUGIN ) . 'assets/js/fed-blocks.js';
		$version    = file_exists( $asset_file ) ? filemtime( $asset_file ) : BC_FED_PLUGIN_VERSION;

		wp_register_script(
			'fed-blocks-editor',
			plugins_url( 'assets/js/fed-blocks.js', BC_FED_PLUGIN ),
			array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-block-editor' ),
			$version,
			true
		);

		wp_localize_script(
			'fed-blocks-editor',
			'fedBlocksConfig',
			array(
				'pluginUrl' => BC_FED_PLUGIN_URL,
				'siteUrl'   => site_url(),
				'userRoles' => $this->get_user_roles_list(),
			)
		);

		wp_enqueue_script( 'fed-blocks-editor' );
	}

	/**
	 * Check if page has FED blocks to load frontend scripts.
	 *
	 * @param bool $condition Current condition.
	 * @return bool
	 */
	public function check_blocks_in_content( $condition ) {
		if ( $condition ) {
			return true;
		}

		global $post;
		if ( ! is_a( $post, 'WP_Post' ) || ! function_exists( 'has_block' ) ) {
			return $condition;
		}

		$fed_blocks = array(
			'fed/dashboard',
			'fed/login',
			'fed/transactions',
			'fed/user-role',
			'fed/social-connect',
			'fed/user-management',
		);

		foreach ( $fed_blocks as $block_name ) {
			if ( has_block( $block_name, $post->post_content ) ) {
				return true;
			}
		}

		return $condition;
	}

	/**
	 * Register Dynamic Blocks.
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// 1. FED Dashboard Block
		register_block_type(
			'fed/dashboard',
			array(
				'api_version'     => 2,
				'category'        => 'frontend-dashboard',
				'title'           => __( 'Frontend Dashboard', 'frontend-dashboard' ),
				'description'     => __( 'Renders the comprehensive Frontend Member Dashboard with profile, menus, tabs, and widgets.', 'frontend-dashboard' ),
				'icon'            => 'dashboard',
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
				),
				'attributes'      => array(
					'align'       => array(
						'type'    => 'string',
						'default' => 'full',
					),
					'theme'       => array(
						'type'    => 'string',
						'default' => 'modern',
					),
					'layout'      => array(
						'type'    => 'string',
						'default' => 'full',
					),
					'default_tab' => array(
						'type'    => 'string',
						'default' => '',
					),
				),
				'render_callback' => array( $this, 'render_dashboard_block' ),
			)
		);

		// 2. FED Login & Registration Block
		register_block_type(
			'fed/login',
			array(
				'api_version'     => 2,
				'category'        => 'frontend-dashboard',
				'title'           => __( 'Frontend Login & Register', 'frontend-dashboard' ),
				'description'     => __( 'Renders modern Sign In, Registration, and Password Reset forms.', 'frontend-dashboard' ),
				'icon'            => 'admin-users',
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
				),
				'attributes'      => array(
					'align'        => array(
						'type'    => 'string',
						'default' => '',
					),
					'view'         => array(
						'type'    => 'string',
						'default' => 'tabs',
					),
					'redirect_url' => array(
						'type'    => 'string',
						'default' => '',
					),
					'default_tab'  => array(
						'type'    => 'string',
						'default' => 'login',
					),
				),
				'render_callback' => array( $this, 'render_login_block' ),
			)
		);

		// 3. FED Transactions Block
		register_block_type(
			'fed/transactions',
			array(
				'api_version'     => 2,
				'category'        => 'frontend-dashboard',
				'title'           => __( 'Transactions & Invoices', 'frontend-dashboard' ),
				'description'     => __( 'Displays payment history, transaction status, and invoice receipts.', 'frontend-dashboard' ),
				'icon'            => 'cart',
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
				),
				'attributes'      => array(
					'align'    => array(
						'type'    => 'string',
						'default' => 'full',
					),
					'per_page' => array(
						'type'    => 'number',
						'default' => 10,
					),
					'status'   => array(
						'type'    => 'string',
						'default' => '',
					),
				),
				'render_callback' => array( $this, 'render_transactions_block' ),
			)
		);

		// 4. FED User Role Content Container
		register_block_type(
			'fed/user-role',
			array(
				'api_version'     => 2,
				'category'        => 'frontend-dashboard',
				'title'           => __( 'Role-Restricted Content', 'frontend-dashboard' ),
				'description'     => __( 'Displays enclosed block content only to users who match selected user roles.', 'frontend-dashboard' ),
				'icon'            => 'lock',
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
				),
				'attributes'      => array(
					'align'         => array(
						'type'    => 'string',
						'default' => '',
					),
					'role'          => array(
						'type'    => 'string',
						'default' => 'subscriber',
					),
					'allowed_roles' => array(
						'type'    => 'array',
						'default' => array( 'subscriber' ),
					),
				),
				'render_callback' => array( $this, 'render_user_role_block' ),
			)
		);

		// 5. FED Social Connect Block
		register_block_type(
			'fed/social-connect',
			array(
				'api_version'     => 2,
				'category'        => 'frontend-dashboard',
				'title'           => __( 'Social Login Buttons', 'frontend-dashboard' ),
				'description'     => __( '1-Click Social Sign-In buttons with Google, Facebook, Apple, and OAuth providers.', 'frontend-dashboard' ),
				'icon'            => 'networking',
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
				),
				'attributes'      => array(
					'align'  => array(
						'type'    => 'string',
						'default' => '',
					),
					'layout' => array(
						'type'    => 'string',
						'default' => 'grid',
					),
				),
				'render_callback' => array( $this, 'render_social_connect_block' ),
			)
		);

		// 6. FED User Management Block
		register_block_type(
			'fed/user-management',
			array(
				'api_version'     => 2,
				'category'        => 'frontend-dashboard',
				'title'           => __( 'Frontend User Management', 'frontend-dashboard' ),
				'description'     => __( 'Searchable user management table for frontend administrators.', 'frontend-dashboard' ),
				'icon'            => 'groups',
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
				),
				'attributes'      => array(
					'align'    => array(
						'type'    => 'string',
						'default' => 'full',
					),
					'per_page' => array(
						'type'    => 'number',
						'default' => 15,
					),
					'roles'    => array(
						'type'    => 'string',
						'default' => '',
					),
				),
				'render_callback' => array( $this, 'render_user_management_block' ),
			)
		);
	}

	/**
	 * Render Dashboard Block.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Inner content.
	 * @return string
	 */
	public function render_dashboard_block( $attributes, $content = '' ) {
		$align = ! empty( $attributes['align'] ) ? 'align' . sanitize_html_class( $attributes['align'] ) : 'alignfull';
		return '<div class="fed-block-dashboard-wrapper ' . esc_attr( $align ) . ' w-full">' . do_shortcode( '[fed_dashboard]' ) . '</div>';
	}

	/**
	 * Render Login / Auth Block.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Inner content.
	 * @return string
	 */
	public function render_login_block( $attributes, $content = '' ) {
		$view  = isset( $attributes['view'] ) ? sanitize_text_field( $attributes['view'] ) : 'tabs';
		$align = ! empty( $attributes['align'] ) ? 'align' . sanitize_html_class( $attributes['align'] ) : '';

		switch ( $view ) {
			case 'login_only':
				$html = do_shortcode( '[fed_login_only]' );
				break;
			case 'register_only':
				$html = do_shortcode( '[fed_register_only]' );
				break;
			case 'forgot_password_only':
				$html = do_shortcode( '[fed_forgot_password_only]' );
				break;
			case 'tabs':
			default:
				$html = do_shortcode( '[fed_login]' );
				break;
		}

		return '<div class="fed-block-login-wrapper ' . esc_attr( $align ) . '">' . $html . '</div>';
	}

	/**
	 * Render Transactions Block.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Inner content.
	 * @return string
	 */
	public function render_transactions_block( $attributes, $content = '' ) {
		$align = ! empty( $attributes['align'] ) ? 'align' . sanitize_html_class( $attributes['align'] ) : 'alignfull';
		return '<div class="fed-block-transactions-wrapper ' . esc_attr( $align ) . ' w-full">' . do_shortcode( '[fed_transactions]' ) . '</div>';
	}

	/**
	 * Render Role-Restricted Block.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Inner content.
	 * @return string
	 */
	public function render_user_role_block( $attributes, $content = '' ) {
		$role = isset( $attributes['role'] ) ? sanitize_text_field( $attributes['role'] ) : '';

		if ( 'guest' === $role ) {
			if ( ! is_user_logged_in() ) {
				return $content;
			}
			return '';
		}

		if ( ! is_user_logged_in() ) {
			return '';
		}

		$current_user = wp_get_current_user();
		if ( empty( $role ) || in_array( $role, (array) $current_user->roles, true ) ) {
			return $content;
		}

		return '';
	}

	/**
	 * Render Social Connect Block.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Inner content.
	 * @return string
	 */
	public function render_social_connect_block( $attributes, $content = '' ) {
		if ( shortcode_exists( 'fed_social_connect' ) ) {
			return do_shortcode( '[fed_social_connect]' );
		}
		return '<div class="fed-social-connect-placeholder">' . __( 'Social Connect addon is required to render social login buttons.', 'frontend-dashboard' ) . '</div>';
	}

	/**
	 * Render User Management Block.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Inner content.
	 * @return string
	 */
	public function render_user_management_block( $attributes, $content = '' ) {
		if ( shortcode_exists( 'fed_user_management' ) ) {
			return do_shortcode( '[fed_user_management]' );
		}
		return '<div class="fed-user-management-placeholder">' . __( 'Frontend User Management addon is required to render user table.', 'frontend-dashboard' ) . '</div>';
	}

	/**
	 * Helper to get user roles list.
	 *
	 * @return array
	 */
	private function get_user_roles_list() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		$roles = array(
			array(
				'label' => __( 'Guests Only (Logged-Out)', 'frontend-dashboard' ),
				'value' => 'guest',
			),
		);

		foreach ( $wp_roles->get_names() as $key => $name ) {
			$roles[] = array(
				'label' => translate_user_role( $name ),
				'value' => $key,
			);
		}

		return $roles;
	}
}

FED_Blocks::get_instance();
