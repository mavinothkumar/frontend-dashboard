<?php
/**
 * Modern Page Templates & Block Template Manager.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FED_Page_Template
 */
class FED_Page_Template {

	/**
	 * Instance reference.
	 *
	 * @var FED_Page_Template
	 */
	private static $instance = null;

	/**
	 * Array of registered page templates.
	 *
	 * @var array
	 */
	protected $templates = array();

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Classic template hooks
		add_filter( 'theme_page_templates', array( $this, 'add_new_template' ) );
		add_filter( 'theme_post_templates', array( $this, 'add_new_template' ) );
		add_filter( 'theme_templates', array( $this, 'add_new_template' ) );
		add_filter( 'template_include', array( $this, 'view_project_template' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'register_project_templates' ) );

		// Enable page-attributes for standard posts so template switcher is always enabled in Block Editor
		add_action( 'init', array( $this, 'enable_post_attributes' ) );

		// Modern Block Theme (FSE) template registration
		add_action( 'init', array( $this, 'register_fse_block_templates' ) );
		add_filter( 'pre_get_block_template', array( $this, 'filter_block_template' ), 10, 3 );
		add_filter( 'get_block_template', array( $this, 'filter_block_template' ), 10, 3 );
		add_filter( 'get_block_templates', array( $this, 'filter_block_templates_list' ), 10, 3 );
	}

	/**
	 * Get registered templates list.
	 *
	 * @return array
	 */
	public function get_templates() {
		if ( empty( $this->templates ) ) {
			$this->templates = apply_filters(
				'fed_add_new_page_template_url',
				array(
					'template-loaders/page-template/layouts/fed-full-width.php'   => __( 'FED Full Width (With Header & Footer)', 'frontend-dashboard' ),
					'template-loaders/page-template/layouts/fed-container.php'    => __( 'FED Container (With Header & Footer)', 'frontend-dashboard' ),
					'template-loaders/page-template/layouts/fed-canvas.php'       => __( 'FED Canvas / Blank (No Header, No Footer)', 'frontend-dashboard' ),
					'template-loaders/page-template/layouts/fed-canvas-full.php'  => __( 'FED Full Width Canvas (No Header, No Footer)', 'frontend-dashboard' ),
					// Backward compatibility aliases
					'template-loaders/page-template/layouts/fed-login.php'        => __( 'FED Login (Legacy)', 'frontend-dashboard' ),
					'template-loaders/page-template/layouts/fed-dashboard.php'    => __( 'FED Dashboard (Legacy)', 'frontend-dashboard' ),
				)
			);
		}
		return $this->templates;
	}

	/**
	 * Enable page-attributes support on posts for Gutenberg template switcher.
	 */
	public function enable_post_attributes() {
		add_post_type_support( 'post', 'page-attributes' );
	}

	/**
	 * Get singleton instance.
	 *
	 * @return FED_Page_Template
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Add templates to page attributes dropdown.
	 *
	 * @param array $posts_templates Current templates.
	 * @return array
	 */
	public function add_new_template( $posts_templates ) {
		return array_merge( $posts_templates, $this->get_templates() );
	}

	/**
	 * Cache injection for theme page templates.
	 *
	 * @param array $atts Post attributes.
	 * @return array
	 */
	public function register_project_templates( $atts ) {
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}
		wp_cache_delete( $cache_key, 'themes' );
		$templates = array_merge( $templates, $this->get_templates() );
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;
	}

	/**
	 * Intercept template include to load plugin layout.
	 *
	 * @param string $template Path to template file.
	 * @return string
	 */
	public function view_project_template( $template ) {
		global $post;
		if ( ! $post ) {
			return $template;
		}

		$assigned = get_post_meta( $post->ID, '_wp_page_template', true );
		if ( ! $assigned ) {
			return $template;
		}

		// Check for legacy vendor path prefixes and normalize
		$normalized = str_replace( 'vendor/', '', $assigned );
		$templates  = $this->get_templates();

		if ( isset( $templates[ $assigned ] ) || isset( $templates[ $normalized ] ) ) {
			$file = plugin_dir_path( BC_FED_PLUGIN ) . $normalized;
			if ( file_exists( $file ) ) {
				return $file;
			}
		}

		return $template;
	}

	/**
	 * Register FSE Block Templates (WordPress 6.7+).
	 */
	public function register_fse_block_templates() {
		if ( ! function_exists( 'register_block_template' ) ) {
			return;
		}

		$fse_templates = array(
			'fed-full-width'   => array(
				'title'       => __( 'FED Full Width (With Header & Footer)', 'frontend-dashboard' ),
				'description' => __( 'Edge-to-edge layout with site header and footer.', 'frontend-dashboard' ),
				'file'        => 'fed-full-width.html',
			),
			'fed-container'    => array(
				'title'       => __( 'FED Container (With Header & Footer)', 'frontend-dashboard' ),
				'description' => __( 'Centered constrained container with site header and footer.', 'frontend-dashboard' ),
				'file'        => 'fed-container.html',
			),
			'fed-canvas'       => array(
				'title'       => __( 'FED Canvas / Blank (No Header, No Footer)', 'frontend-dashboard' ),
				'description' => __( 'Clean distraction-free canvas for authentication and forms.', 'frontend-dashboard' ),
				'file'        => 'fed-canvas.html',
			),
			'fed-canvas-full'  => array(
				'title'       => __( 'FED Full Width Canvas (No Header, No Footer)', 'frontend-dashboard' ),
				'description' => __( 'Edge-to-edge standalone full-width canvas without header/footer.', 'frontend-dashboard' ),
				'file'        => 'fed-canvas-full.html',
			),
		);

		foreach ( $fse_templates as $slug => $data ) {
			$file_path = plugin_dir_path( BC_FED_PLUGIN ) . 'templates/block-templates/' . $data['file'];
			$content   = file_exists( $file_path ) ? file_get_contents( $file_path ) : '';

			register_block_template(
				'frontend-dashboard//' . $slug,
				array(
					'title'       => $data['title'],
					'description' => $data['description'],
					'content'     => $content,
					'post_types'  => array( 'page', 'post' ),
				)
			);
		}
	}

	/**
	 * Filter Block Template lookup for FSE Block Themes (WP 5.9+).
	 *
	 * @param \WP_Block_Template|null $block_template Block template object.
	 * @param string                  $id             Template ID.
	 * @param string                  $template_type  Template type.
	 * @return \WP_Block_Template|null
	 */
	public function filter_block_template( $block_template, $id, $template_type ) {
		if ( $block_template || 'wp_template' !== $template_type ) {
			return $block_template;
		}

		$slug = '';
		if ( strpos( $id, '//' ) !== false ) {
			list( $theme, $slug ) = explode( '//', $id, 2 );
		} else {
			$slug = $id;
		}

		$fse_templates = array(
			'fed-full-width'   => array(
				'title'       => __( 'FED Full Width (With Header & Footer)', 'frontend-dashboard' ),
				'description' => __( 'Edge-to-edge layout with site header and footer.', 'frontend-dashboard' ),
				'file'        => 'fed-full-width.html',
			),
			'fed-container'    => array(
				'title'       => __( 'FED Container (With Header & Footer)', 'frontend-dashboard' ),
				'description' => __( 'Centered constrained container with site header and footer.', 'frontend-dashboard' ),
				'file'        => 'fed-container.html',
			),
			'fed-canvas'       => array(
				'title'       => __( 'FED Canvas / Blank (No Header, No Footer)', 'frontend-dashboard' ),
				'description' => __( 'Clean distraction-free canvas for authentication and forms.', 'frontend-dashboard' ),
				'file'        => 'fed-canvas.html',
			),
			'fed-canvas-full'  => array(
				'title'       => __( 'FED Full Width Canvas (No Header, No Footer)', 'frontend-dashboard' ),
				'description' => __( 'Edge-to-edge standalone full-width canvas without header/footer.', 'frontend-dashboard' ),
				'file'        => 'fed-canvas-full.html',
			),
		);

		if ( isset( $fse_templates[ $slug ] ) && class_exists( 'WP_Block_Template' ) ) {
			$data      = $fse_templates[ $slug ];
			$file_path = plugin_dir_path( BC_FED_PLUGIN ) . 'templates/block-templates/' . $data['file'];

			if ( file_exists( $file_path ) ) {
				$theme_name               = get_stylesheet();
				$template                 = new \WP_Block_Template();
				$template->id             = $theme_name . '//' . $slug;
				$template->theme          = $theme_name;
				$template->plugin         = 'frontend-dashboard';
				$template->slug           = $slug;
				$template->source         = 'plugin';
				$template->origin         = 'plugin';
				$template->type           = 'wp_template';
				$template->title          = $data['title'];
				$template->description    = $data['description'];
				$template->content        = file_get_contents( $file_path );
				$template->status         = 'publish';
				$template->has_theme_file = true;
				$template->is_custom      = true;
				$template->post_types     = array( 'page', 'post' );

				return $template;
			}
		}

		return $block_template;
	}

	/**
	 * Add plugin block templates to query list.
	 *
	 * @param array  $query_result Array of block templates.
	 * @param array  $query        Query args.
	 * @param string $template_type Template type.
	 * @return array
	 */
	public function filter_block_templates_list( $query_result, $query, $template_type ) {
		if ( 'wp_template' !== $template_type ) {
			return $query_result;
		}

		$fse_templates = array(
			'fed-full-width'   => array(
				'title'       => __( 'FED Full Width (With Header & Footer)', 'frontend-dashboard' ),
				'description' => __( 'Edge-to-edge layout with site header and footer.', 'frontend-dashboard' ),
				'file'        => 'fed-full-width.html',
			),
			'fed-container'    => array(
				'title'       => __( 'FED Container (With Header & Footer)', 'frontend-dashboard' ),
				'description' => __( 'Centered constrained container with site header and footer.', 'frontend-dashboard' ),
				'file'        => 'fed-container.html',
			),
			'fed-canvas'       => array(
				'title'       => __( 'FED Canvas / Blank (No Header, No Footer)', 'frontend-dashboard' ),
				'description' => __( 'Clean distraction-free canvas for authentication and forms.', 'frontend-dashboard' ),
				'file'        => 'fed-canvas.html',
			),
			'fed-canvas-full'  => array(
				'title'       => __( 'FED Full Width Canvas (No Header, No Footer)', 'frontend-dashboard' ),
				'description' => __( 'Edge-to-edge standalone full-width canvas without header/footer.', 'frontend-dashboard' ),
				'file'        => 'fed-canvas-full.html',
			),
		);

		$theme_name = get_stylesheet();

		// Collect existing slugs in query result
		$existing_slugs = array();
		if ( is_array( $query_result ) ) {
			foreach ( $query_result as $item ) {
				if ( is_object( $item ) && isset( $item->slug ) ) {
					$existing_slugs[] = $item->slug;
				}
			}
		} else {
			$query_result = array();
		}

		$slugs_to_include = ! empty( $query['slug__in'] ) ? (array) $query['slug__in'] : array();
		$slugs_to_skip    = ! empty( $query['slug__not_in'] ) ? (array) $query['slug__not_in'] : array();
		$post_type        = isset( $query['post_type'] ) ? $query['post_type'] : '';

		foreach ( $fse_templates as $slug => $data ) {
			if ( in_array( $slug, $existing_slugs, true ) ) {
				continue;
			}

			// If specific slugs are requested, skip if not in list
			if ( ! empty( $slugs_to_include ) && ! in_array( $slug, $slugs_to_include, true ) ) {
				continue;
			}

			// If specific slugs are skipped, skip if in list
			if ( ! empty( $slugs_to_skip ) && in_array( $slug, $slugs_to_skip, true ) ) {
				continue;
			}

			// If query filters for specific post type, ensure match
			if ( $post_type && ! in_array( $post_type, array( 'page', 'post' ), true ) ) {
				continue;
			}

			$file = plugin_dir_path( BC_FED_PLUGIN ) . 'templates/block-templates/' . $data['file'];
			if ( file_exists( $file ) && class_exists( 'WP_Block_Template' ) ) {
				$template                 = new \WP_Block_Template();
				$template->id             = $theme_name . '//' . $slug;
				$template->theme          = $theme_name;
				$template->plugin         = 'frontend-dashboard';
				$template->slug           = $slug;
				$template->source         = 'plugin';
				$template->origin         = 'plugin';
				$template->type           = 'wp_template';
				$template->title          = $data['title'];
				$template->description    = $data['description'];
				$template->content        = file_get_contents( $file );
				$template->status         = 'publish';
				$template->has_theme_file = true;
				$template->is_custom      = true;
				$template->post_types     = array( 'page', 'post' );

				$query_result[] = $template;
			}
		}

		return $query_result;
	}
}

add_action( 'plugins_loaded', array( 'FED_Page_Template', 'get_instance' ) );
