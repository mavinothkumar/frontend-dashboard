<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class EditorField
 *
 * WordPress WYSIWYG rich text editor element.
 */
class EditorField extends BaseField {

	protected $type = 'wp_editor';

	public function render() {
		$extended = $this->extended;

		$media_buttons = isset( $extended['settings']['media_buttons'] ) ? ( 'true' === (string) $extended['settings']['media_buttons'] || true === $extended['settings']['media_buttons'] ) : true;
		$quicktags     = isset( $extended['settings']['quicktags'] ) ? ( 'true' === (string) $extended['settings']['quicktags'] || true === $extended['settings']['quicktags'] ) : true;
		$textarea_rows = isset( $extended['settings']['textarea_rows'] ) ? (int) $extended['settings']['textarea_rows'] : 10;
		$editor_height = isset( $extended['settings']['editor_height'] ) ? (int) $extended['settings']['editor_height'] : 250;

		$id_attr = ! empty( $this->id_name ) ? sprintf( ' id="%s"', esc_attr( $this->id_name ) ) : '';

		$editor_html = '';
		if ( function_exists( 'fed_get_wp_editor' ) ) {
			$editor_html = fed_get_wp_editor(
				$this->value,
				$this->name,
				[
					'textarea_name' => $this->name,
					'media_buttons' => $media_buttons,
					'textarea_rows' => $textarea_rows,
					'editor_height' => $editor_height,
					'editor_class'  => $this->class_name,
					'quicktags'     => $quicktags,
				]
			);
		} else {
			ob_start();
			wp_editor(
				$this->value,
				sanitize_key( $this->name ),
				[
					'textarea_name' => $this->name,
					'media_buttons' => $media_buttons,
					'textarea_rows' => $textarea_rows,
					'editor_height' => $editor_height,
					'editor_class'  => $this->class_name,
					'quicktags'     => $quicktags,
				]
			);
			$editor_html = ob_get_clean();
		}

		$shims_html = function_exists( 'fed_get_early_wp_shims_js' ) ? '<script>' . fed_get_early_wp_shims_js() . '</script>' : '';
		return sprintf( '<div class="fed_wp_editor_container"%s>%s%s</div>', $id_attr, $shims_html, $editor_html );
	}
}