<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ColorField
 *
 * Modern HEX color picker with live swatch preview.
 */
class ColorField extends BaseField {

	protected $type = 'color';

	public function render() {
		$value       = ! empty( $this->value ) ? trim( $this->value ) : '';
		$placeholder = ! empty( $this->placeholder ) ? trim( $this->placeholder ) : '#4F46E5';

		$current_color = ! empty( $value ) ? $value : $placeholder;
		if ( '#' !== substr( $current_color, 0, 1 ) ) {
			$current_color = '#' . $current_color;
		}

		$hex_color = $current_color;
		if ( preg_match( '/^#([A-Fa-f0-9]{3})$/', $hex_color, $m ) ) {
			$hex_color = '#' . $m[1][0] . $m[1][0] . $m[1][1] . $m[1][1] . $m[1][2] . $m[1][2];
		} elseif ( ! preg_match( '/^#([A-Fa-f0-9]{6})$/', $hex_color ) ) {
			$hex_color = '#4F46E5';
		}
		$hex_color = strtoupper( $hex_color );

		ob_start();
		?>
		<div class="fed_color_picker_container flex items-center gap-3 w-full max-w-xs <?php echo esc_attr( $this->class_name ); ?>">
			<!-- Live Color Swatch & Native Picker Trigger -->
			<div class="relative w-11 h-11 rounded-2xl border-2 border-slate-200/90 shadow-2xs shrink-0 overflow-hidden cursor-pointer group hover:border-indigo-500/80 hover:shadow-xs focus-within:ring-4 focus-within:ring-indigo-500/10 transition-all duration-200 <?php echo $this->is_disabled ? 'opacity-60 cursor-not-allowed pointer-events-none' : ''; ?>" title="<?php esc_attr_e( 'Click to pick a color', 'frontend-dashboard' ); ?>">
				<div class="fed_color_swatch absolute inset-0 w-full h-full rounded-2xl transition-all duration-150 group-hover:scale-105" style="background-color: <?php echo esc_attr( $hex_color ); ?>;"></div>
				<input type="color"
					   class="fed_color_native absolute inset-0 w-full h-full opacity-0 cursor-pointer p-0 m-0 border-0"
					   value="<?php echo esc_attr( strtolower( $hex_color ) ); ?>"
					   <?php echo $this->is_disabled ? 'disabled="disabled"' : ''; ?>
					   <?php echo $this->is_readonly ? 'readonly="readonly"' : ''; ?>
					   tabindex="-1">
			</div>

			<!-- Hex Value Text Input -->
			<div class="relative flex-1">
				<div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-mono font-bold text-xs">
					<i class="fas fa-hashtag text-[11px] text-slate-300"></i>
				</div>
				<input type="text"
					   name="<?php echo esc_attr( $this->name ); ?>"
					   <?php if ( ! empty( $this->id_name ) ) : ?>id="<?php echo esc_attr( $this->id_name ); ?>"<?php endif; ?>
					   class="fed_color_input w-full font-mono text-xs uppercase font-bold text-slate-800 bg-white border border-slate-200/90 rounded-2xl pl-8 pr-9 py-2.5 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
					   value="<?php echo esc_attr( $hex_color ); ?>"
					   placeholder="<?php echo esc_attr( $placeholder ); ?>"
					   maxlength="7"
					   spellcheck="false"
					   autocomplete="off"
					   <?php echo $this->is_required ? 'required="required"' : ''; ?>
					   <?php echo $this->is_disabled ? 'disabled="disabled"' : ''; ?>
					   <?php echo $this->is_readonly ? 'readonly="readonly"' : ''; ?>>
				<div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 group-hover:text-indigo-600 transition-colors">
					<i class="fas fa-eye-dropper text-xs text-slate-300"></i>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}