<?php

namespace FED\Services\Editor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class EditorRenderer
 *
 * Provides multi-editor rendering (Classic TinyMCE, TipTap, Editor.js)
 * with native WordPress Media Library integration.
 */
class EditorRenderer {

	/**
	 * Get the configured editor type for a given post type.
	 *
	 * @param string $post_type
	 * @return string 'classic' | 'tiptap' | 'editorjs'
	 */
	public static function get_editor_type( $post_type = 'post' ) {
		if ( function_exists( 'fed_get_post_settings_by_type' ) ) {
			$post_settings = fed_get_post_settings_by_type( $post_type );
			if ( ! empty( $post_settings['settings']['fed_editor_type'] ) ) {
				return $post_settings['settings']['fed_editor_type'];
			}
		}

		$global_settings = get_option( 'fed_admin_settings_post', array() );
		if ( ! empty( $global_settings['settings']['fed_editor_type'] ) ) {
			return $global_settings['settings']['fed_editor_type'];
		}

		return 'classic';
	}

	/**
	 * Render Post Content Editor based on setting.
	 *
	 * @param string      $content
	 * @param string      $input_meta
	 * @param string      $post_type
	 * @param string|null $editor_type
	 * @return string HTML output
	 */
	public static function render( $content = '', $input_meta = 'post_content', $post_type = 'post', $editor_type = null ) {
		if ( is_admin() && function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
			add_action( 'admin_footer', 'wp_print_media_templates' );
			add_action( 'admin_print_footer_scripts', 'wp_print_media_templates' );
		}

		if ( null === $editor_type ) {
			$editor_type = self::get_editor_type( $post_type );
		}

		switch ( $editor_type ) {
			case 'tiptap':
				return self::render_tiptap( $content, $input_meta );

			case 'editorjs':
				return self::render_editorjs( $content, $input_meta );

			case 'classic':
			default:
				return self::render_classic( $content, $input_meta );
		}
	}

	/**
	 * Clean Gutenberg block comments for modern visual editors.
	 *
	 * @param string $content
	 * @return string
	 */
	public static function clean_content( $content ) {
		if ( empty( $content ) ) {
			return '';
		}
		// Remove <!-- wp:... --> and <!-- /wp:... -->
		return preg_replace( '/<!--\s*\/?wp:[^>]*-->\s*/', '', $content );
	}

	/**
	 * Remove admin-only and broken plugins from frontend TinyMCE
	 */
	public static function filter_clean_tinymce_plugins( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wplink', 'wpeditimage', 'wpview', 'wpgallery' ) );
		}
		return $plugins;
	}

	/**
	 * Clean TinyMCE init configuration for frontend
	 */
	public static function filter_clean_tinymce_init( $mceInit, $editor_id = '' ) {
		if ( isset( $mceInit['plugins'] ) ) {
			$plugins            = explode( ',', $mceInit['plugins'] );
			$plugins            = array_diff( $plugins, array( 'wplink', 'wpeditimage', 'wpview', 'wpgallery' ) );
			$mceInit['plugins'] = implode( ',', $plugins );
		}
		return $mceInit;
	}

	/**
	 * Render Classic WP Editor (TinyMCE)
	 */
	protected static function render_classic( $content, $input_meta ) {
		if ( function_exists( 'fed_get_early_wp_shims_js' ) ) {
			$shims = fed_get_early_wp_shims_js();
			wp_add_inline_script( 'jquery-core', $shims, 'before' );
			wp_add_inline_script( 'jquery', $shims, 'before' );
			wp_add_inline_script( 'common', $shims, 'before' );
			wp_add_inline_script( 'editor', $shims, 'before' );
			wp_add_inline_script( 'wplink', $shims, 'before' );
		}

		wp_enqueue_script( 'wp-polyfill' );
		wp_enqueue_script( 'wp-hooks' );
		wp_enqueue_script( 'wp-i18n' );
		wp_enqueue_script( 'wp-dom-ready' );
		wp_enqueue_script( 'wp-a11y' );

		ob_start();
		?>
		<script id="fed-classic-editor-shims">
		window.wp=(typeof window.wp==="object"&&window.wp!==null)?window.wp:{};window.wp.editor=(typeof window.wp.editor==="object"&&window.wp.editor!==null)?window.wp.editor:{};window.wp.autop=window.wp.autop||{autop:function(t){return t;},removep:function(t){return t;}};window.wp.i18n=(typeof window.wp.i18n==="object"&&window.wp.i18n!==null)?window.wp.i18n:{__:function(t){return t;},_x:function(t){return t;},_n:function(s,p,n){return n===1?s:p;},_nx:function(s,p,n){return n===1?s:p;},isRtl:function(){return false;},setLocaleData:function(){},sprintf:function(t){return t;}};if(!window.wp.i18n.__){window.wp.i18n.__=function(t){return t;};}window.wp.hooks=window.wp.hooks||{addAction:function(){},addFilter:function(){},applyFilters:function(h,v){return v;},doAction:function(){},removeAction:function(){},removeFilter:function(){},hasAction:function(){return false;},hasFilter:function(){return false;}};
		</script>
		<div class="fed-classic-editor-wrapper relative">
			<div class="mb-2.5 flex items-center justify-between">
				<button type="button" class="fed-classic-add-media-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition-all border border-indigo-100/80 shadow-2xs cursor-pointer" data-editor="<?php echo esc_attr( $input_meta ); ?>">
					<svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
						<path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
					</svg>
					<span><?php esc_html_e( 'Add Media', 'frontend-dashboard' ); ?></span>
				</button>
			</div>
			<?php
			if ( function_exists( 'wp_editor' ) ) {
				add_filter( 'tiny_mce_plugins', array( __CLASS__, 'filter_clean_tinymce_plugins' ) );
				add_filter( 'tiny_mce_before_init', array( __CLASS__, 'filter_clean_tinymce_init' ), 10, 2 );

				wp_editor(
					$content,
					$input_meta,
					array(
						'media_buttons' => false,
						'quicktags'     => true,
						'textarea_rows' => 12,
						'tinymce'       => array(
							'wp_autoresize_on' => true,
						),
					)
				);

				remove_filter( 'tiny_mce_plugins', array( __CLASS__, 'filter_clean_tinymce_plugins' ) );
				remove_filter( 'tiny_mce_before_init', array( __CLASS__, 'filter_clean_tinymce_init' ) );
			} else {
				?>
				<textarea name="<?php echo esc_attr( $input_meta ); ?>" class="form-control" rows="12"><?php echo esc_textarea( $content ); ?></textarea>
				<?php
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render TipTap Modern Editor
	 */
	protected static function render_tiptap( $content, $input_meta ) {
		$clean_content = self::clean_content( $content );
		ob_start();
		?>
		<div class="fed-tiptap-container rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-2xs">
			<!-- TipTap Toolbar -->
			<div class="fed-tiptap-toolbar flex items-center flex-wrap gap-1 p-2 bg-slate-50/80 border-b border-slate-200/80 text-xs">
				<button type="button" data-action="bold" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Bold', 'frontend-dashboard' ); ?>">
					<i class="fas fa-bold"></i>
				</button>
				<button type="button" data-action="italic" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Italic', 'frontend-dashboard' ); ?>">
					<i class="fas fa-italic"></i>
				</button>
				<button type="button" data-action="strike" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Strikethrough', 'frontend-dashboard' ); ?>">
					<i class="fas fa-strikethrough"></i>
				</button>

				<span class="w-px h-5 bg-slate-200 mx-1"></span>

				<button type="button" data-action="heading" data-level="1" class="px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-bold transition-colors" title="<?php esc_attr_e( 'Heading 1', 'frontend-dashboard' ); ?>">
					H1
				</button>
				<button type="button" data-action="heading" data-level="2" class="px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-bold transition-colors" title="<?php esc_attr_e( 'Heading 2', 'frontend-dashboard' ); ?>">
					H2
				</button>
				<button type="button" data-action="heading" data-level="3" class="px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-bold transition-colors" title="<?php esc_attr_e( 'Heading 3', 'frontend-dashboard' ); ?>">
					H3
				</button>

				<span class="w-px h-5 bg-slate-200 mx-1"></span>

				<button type="button" data-action="bulletList" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Bullet List', 'frontend-dashboard' ); ?>">
					<i class="fas fa-list-ul"></i>
				</button>
				<button type="button" data-action="orderedList" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Numbered List', 'frontend-dashboard' ); ?>">
					<i class="fas fa-list-ol"></i>
				</button>
				<button type="button" data-action="blockquote" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Quote', 'frontend-dashboard' ); ?>">
					<i class="fas fa-quote-left"></i>
				</button>
				<button type="button" data-action="codeBlock" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Code Block', 'frontend-dashboard' ); ?>">
					<i class="fas fa-code"></i>
				</button>
				<button type="button" data-action="horizontalRule" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Divider', 'frontend-dashboard' ); ?>">
					<i class="fas fa-minus"></i>
				</button>

				<span class="w-px h-5 bg-slate-200 mx-1"></span>

				<button type="button" data-action="link" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Link', 'frontend-dashboard' ); ?>">
					<i class="fas fa-link"></i>
				</button>

				<!-- WordPress Media Gallery Button -->
				<button type="button" data-action="image" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold transition-colors" title="<?php esc_attr_e( 'Insert image from WordPress Media Gallery', 'frontend-dashboard' ); ?>">
					<i class="fas fa-images"></i>
					<span><?php esc_html_e( 'Add Media', 'frontend-dashboard' ); ?></span>
				</button>

				<span class="w-px h-5 bg-slate-200 mx-1"></span>

				<button type="button" data-action="undo" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Undo', 'frontend-dashboard' ); ?>">
					<i class="fas fa-undo"></i>
				</button>
				<button type="button" data-action="redo" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="<?php esc_attr_e( 'Redo', 'frontend-dashboard' ); ?>">
					<i class="fas fa-redo"></i>
				</button>
			</div>

			<!-- Editor Editable Canvas -->
			<div class="fed-tiptap-editor-area" data-placeholder="<?php esc_attr_e( 'Write your post content here...', 'frontend-dashboard' ); ?>"></div>

			<!-- Hidden input serialized for form submission -->
			<input type="hidden" name="<?php echo esc_attr( $input_meta ); ?>" class="fed-tiptap-hidden-input" value="<?php echo esc_attr( $clean_content ); ?>" />
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Editor.js Block Editor
	 */
	protected static function render_editorjs( $content, $input_meta ) {
		$clean_content = self::clean_content( $content );
		ob_start();
		?>
		<div class="fed-editorjs-container rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-2xs">
			<div class="flex items-center justify-between p-2.5 bg-slate-50/80 border-b border-slate-200/80 text-xs flex-wrap gap-2">
				<div class="flex items-center gap-2">
					<span class="font-bold text-slate-700 flex items-center gap-1.5">
						<i class="fas fa-cubes text-indigo-500"></i>
						<?php esc_html_e( 'Block Editor', 'frontend-dashboard' ); ?>
					</span>
					<button type="button" class="fed-edjs-toolbar-add-image inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold transition-colors">
						<i class="fas fa-images"></i>
						<span><?php esc_html_e( 'Add Media', 'frontend-dashboard' ); ?></span>
					</button>
				</div>
				<span class="text-[11px] text-slate-400">
					<?php esc_html_e( 'Click on the canvas or press TAB / "+" to add blocks', 'frontend-dashboard' ); ?>
				</span>
			</div>

			<!-- Editor.js Mount Node -->
			<div class="fed-editorjs-holder p-4 min-h-[260px]" data-placeholder="<?php esc_attr_e( 'Click here to write your story...', 'frontend-dashboard' ); ?>"></div>

			<!-- Hidden input serialized for form submission -->
			<input type="hidden" name="<?php echo esc_attr( $input_meta ); ?>" class="fed-editorjs-hidden-input" value="<?php echo esc_attr( $clean_content ); ?>" />
		</div>
		<?php
		return ob_get_clean();
	}
}

/**
 * Global helper function to render the post editor.
 *
 * @param string      $content
 * @param string      $input_meta
 * @param string      $post_type
 * @param string|null $editor_type
 * @return string
 */
function fed_render_post_editor( $content = '', $input_meta = 'post_content', $post_type = 'post', $editor_type = null ) {
	return EditorRenderer::render( $content, $input_meta, $post_type, $editor_type );
}
