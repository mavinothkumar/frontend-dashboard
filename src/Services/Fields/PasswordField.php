<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PasswordField
 *
 * Password input element.
 */
class PasswordField extends TextField {
	protected $type = 'password';

	public function render() {
		if ( strpos( $this->class_name, 'pr-' ) === false ) {
			$this->class_name = trim( $this->class_name . ' pr-10' );
		}

		$attrs = $this->build_attributes( [
			'type'  => $this->type,
			'value' => $this->value,
		] );

		return sprintf(
			'<div class="relative flex items-center w-full">
				<input %s />
				<button type="button" class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100/80 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer fed-password-toggle-btn" onclick="const input = this.closest(\'.relative\').querySelector(\'input\'); const eye = this.querySelector(\'.fed-eye-icon\'); const eyeSlash = this.querySelector(\'.fed-eye-slash-icon\'); if (input.type === \'password\') { input.type = \'text\'; eye.classList.add(\'hidden\'); eyeSlash.classList.remove(\'hidden\'); } else { input.type = \'password\'; eye.classList.remove(\'hidden\'); eyeSlash.classList.add(\'hidden\'); }" title="%s" aria-label="%s">
					<svg class="w-4 h-4 fed-eye-icon transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
						<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
						<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
					</svg>
					<svg class="w-4 h-4 fed-eye-slash-icon hidden text-slate-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
						<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
					</svg>
				</button>
			</div>',
			$attrs,
			esc_attr__( 'Show / Hide Password', 'frontend-dashboard' ),
			esc_attr__( 'Show / Hide Password', 'frontend-dashboard' )
		);
	}
}
