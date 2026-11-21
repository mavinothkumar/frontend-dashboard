<?php
/**
 * Common.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Placeholder Field.
 *
 * @param  array $row  Row.
 */
function fed_get_placeholder_field( array $row ) {
	$val = isset( $row['placeholder'] ) ? $row['placeholder'] : '';
	?>
	<div class="space-y-1.5">
		<label class="block text-xs font-bold text-slate-700">
			<?php esc_html_e( 'Placeholder Text', 'frontend-dashboard' ); ?>
		</label>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo fed_input_box(
			'placeholder',
			array(
				'value' => $val,
				'class' => 'fed-live-preview-placeholder',
			),
			'single_line'
		);
		?>
		<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Hint text shown inside the input when empty.', 'frontend-dashboard' ); ?></p>
	</div>
	<?php
}

/**
 * Get Help / Description Text Field.
 *
 * @param array $row Row.
 */
function fed_get_help_text_field( array $row ) {
	$val = isset( $row['tooltip_body'] ) ? $row['tooltip_body'] : '';
	?>
	<div class="space-y-1.5">
		<label class="block text-xs font-bold text-slate-700">
			<?php esc_html_e( 'Help / Description Text', 'frontend-dashboard' ); ?>
		</label>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo fed_input_box(
			'tooltip_body',
			array(
				'value' => $val,
				'class' => 'fed-live-preview-help-text',
			),
			'single_line'
		);
		?>
		<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Instructions or hint text displayed directly below the field for end users.', 'frontend-dashboard' ); ?></p>
	</div>
	<?php
}

/**
 * Get Default Value Field with Dynamic Smart Tags insertion menu.
 *
 * @param array $row Row data.
 */
function fed_get_default_value_field( array $row ) {
	$extended    = isset( $row['extended'] ) ? ( is_string( $row['extended'] ) ? maybe_unserialize( $row['extended'] ) : $row['extended'] ) : array();
	$default_val = isset( $extended['default_value'] ) ? $extended['default_value'] : '';
	$smart_tags  = function_exists( 'fed_get_smart_tags_list' ) ? fed_get_smart_tags_list() : array();
	?>
	<div class="space-y-1.5 fed-default-value-field-wrap">
		<div class="flex items-center justify-between">
			<label class="block text-xs font-bold text-slate-700">
				<?php esc_html_e( 'Default Value', 'frontend-dashboard' ); ?>
			</label>
			<div class="relative inline-block text-left fed-smart-tags-dropdown-container">
				<button type="button" class="fed-btn-insert-smart-tag text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1.5 cursor-pointer transition-colors bg-transparent border-0 p-0" title="<?php esc_attr_e( 'Insert dynamic smart tag into default value', 'frontend-dashboard' ); ?>">
					<i class="fas fa-magic text-[10px]"></i>
					<span><?php esc_html_e( 'Insert Smart Tag', 'frontend-dashboard' ); ?></span>
					<i class="fas fa-chevron-down text-[8px] opacity-70"></i>
				</button>
				<div class="fed-smart-tags-dropdown hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden" style="box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
					<!-- Search Header -->
					<div class="p-2.5 border-b border-slate-100 bg-slate-50/80">
						<div class="relative flex items-center">
							<i class="fas fa-search absolute left-2.5 text-slate-400 text-[11px] pointer-events-none"></i>
							<input type="text"
								class="fed-smart-tag-search-input w-full pl-7 pr-7 py-1.5 text-xs text-slate-700 bg-white border border-slate-200 rounded-lg outline-none focus:border-indigo-500 transition-colors placeholder:text-slate-400"
								placeholder="<?php esc_attr_e( 'Search tags (e.g. email, user, date)...', 'frontend-dashboard' ); ?>"
								autocomplete="off" />
							<button type="button" class="fed-smart-tag-search-clear hidden absolute right-2 text-slate-400 hover:text-slate-600 border-0 bg-transparent p-0 cursor-pointer text-xs" title="<?php esc_attr_e( 'Clear search', 'frontend-dashboard' ); ?>">
								<i class="fas fa-times-circle"></i>
							</button>
						</div>
					</div>
					<!-- Scrollable Tags List -->
					<div class="fed-smart-tags-list p-2 space-y-2.5" style="max-height: 240px; overflow-y: auto; overflow-x: hidden;">
						<?php foreach ( $smart_tags as $group ) : ?>
							<div class="fed-smart-tag-group space-y-1">
								<div class="fed-smart-tag-group-title text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 py-0.5"><?php echo esc_html( $group['title'] ); ?></div>
								<div class="space-y-0.5">
									<?php foreach ( $group['tags'] as $tag => $label ) : ?>
										<button type="button"
											class="fed-smart-tag-item w-full text-left px-2.5 py-1.5 rounded-lg text-xs hover:bg-indigo-50 hover:text-indigo-700 flex flex-col gap-0.5 transition-colors cursor-pointer border-0 bg-transparent"
											data-tag="<?php echo esc_attr( $tag ); ?>"
											data-label="<?php echo esc_attr( $label ); ?>">
											<span class="font-mono font-semibold text-indigo-600 text-[11px]"><?php echo esc_html( $tag ); ?></span>
											<span class="text-[10px] text-slate-500 leading-tight"><?php echo esc_html( $label ); ?></span>
										</button>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
						<div class="fed-smart-tags-no-results hidden py-6 px-3 text-center">
							<i class="fas fa-search text-slate-300 text-sm mb-1 block"></i>
							<span class="text-xs text-slate-400 font-medium"><?php esc_html_e( 'No matching smart tags found', 'frontend-dashboard' ); ?></span>
						</div>
					</div>
					<!-- Dropdown Footer -->
					<div class="px-3 py-1.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
						<span><?php esc_html_e( 'Click or press Enter to insert', 'frontend-dashboard' ); ?></span>
						<span class="font-mono text-[9px] bg-slate-200/60 px-1.5 py-0.5 rounded text-slate-500">ESC</span>
					</div>
				</div>
			</div>
		</div>
		<input type="text"
			name="extended[default_value]"
			value="<?php echo esc_attr( $default_val ); ?>"
			placeholder="<?php esc_attr_e( 'e.g. {current_user_name} or Initial Value', 'frontend-dashboard' ); ?>"
			class="fed-default-value-input w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all font-mono"
			style="min-height:38px;" />
		<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Pre-filled value on empty forms. Supports static text and dynamic smart tags.', 'frontend-dashboard' ); ?></p>
	</div>
	<?php
}

/**
 * Get Class Field.
 *
 * @param  array $row  Row.
 */
function fed_get_class_field( array $row ) {
	$val = isset( $row['class_name'] ) ? $row['class_name'] : '';
	?>
	<div class="space-y-1.5">
		<label class="block text-xs font-bold text-slate-700">
			<?php esc_html_e( 'Custom CSS Class', 'frontend-dashboard' ); ?>
		</label>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo fed_input_box(
			'class_name',
			array(
				'value' => $val,
			),
			'single_line'
		);
		?>
		<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Additional CSS classes separated by space.', 'frontend-dashboard' ); ?></p>
	</div>
	<?php
}

/**
 * Get ID field.
 *
 * @param  array $row  Row.
 */
function fed_get_id_field( array $row ) {
	$val = isset( $row['id_name'] ) ? $row['id_name'] : '';
	?>
	<div class="space-y-1.5">
		<label class="block text-xs font-bold text-slate-700">
			<?php esc_html_e( 'Element ID', 'frontend-dashboard' ); ?>
		</label>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo fed_input_box(
			'id_name',
			array(
				'value' => $val,
			),
			'single_line'
		);
		?>
		<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Unique HTML ID attribute for this field.', 'frontend-dashboard' ); ?></p>
	</div>
	<?php
}

/**
 * Get Field Width / Column Span Field.
 *
 * @param array $row Row.
 */
function fed_get_field_width_field( array $row ) {
	$extended   = isset( $row['extended'] ) ? ( is_string( $row['extended'] ) ? maybe_unserialize( $row['extended'] ) : $row['extended'] ) : array();
	$width_val  = isset( $extended['field_width'] ) ? $extended['field_width'] : '100';
	$width_opts = array(
		'100' => __( '100% (Full Width - 1 Col)', 'frontend-dashboard' ),
		'50'  => __( '50% (Half Width - 2 Cols)', 'frontend-dashboard' ),
		'33'  => __( '33% (One-Third - 3 Cols)', 'frontend-dashboard' ),
		'25'  => __( '25% (One-Quarter - 4 Cols)', 'frontend-dashboard' ),
	);
	?>
	<div class="space-y-1.5">
		<label class="block text-xs font-bold text-slate-700">
			<?php esc_html_e( 'Field Width (Column Span)', 'frontend-dashboard' ); ?>
		</label>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo fed_input_box(
			'extended[field_width]',
			array(
				'value'   => $width_val,
				'options' => $width_opts,
				'class'   => 'fed-live-preview-width',
			),
			'select'
		);
		?>
		<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Controls the responsive column width on frontend forms.', 'frontend-dashboard' ); ?></p>
	</div>
	<?php
}

/**
 * Render Interactive Choices & Options Repeater Builder.
 *
 * @param  mixed  $input_val    Raw option values (legacy string, JSON, array).
 * @param  string $field_type   Field type: 'select' or 'radio'.
 * @param  string $is_multi_val Multi-select status for select fields.
 */
function fed_render_choices_builder( $input_val, $field_type = 'select', $is_multi_val = '' ) {
	$options_map = function_exists( 'fed_parse_field_options' ) ? fed_parse_field_options( $input_val ) : array();
	if ( empty( $options_map ) ) {
		$options_map = array(
			'option_1' => __( 'Option 1', 'frontend-dashboard' ),
			'option_2' => __( 'Option 2', 'frontend-dashboard' ),
		);
	}

	$json_data = array();
	foreach ( $options_map as $k => $v ) {
		$json_data[] = array(
			'key'   => (string) $k,
			'label' => (string) $v,
		);
	}
	$raw_json   = wp_json_encode( $json_data );
	$builder_id = 'fed_choices_builder_' . $field_type . '_' . wp_rand( 1000, 9999 );
	$title      = ( 'radio' === $field_type ) ? __( 'Radio Choices & Values', 'frontend-dashboard' ) : __( 'Dropdown Choices & Configuration', 'frontend-dashboard' );
	$desc       = ( 'radio' === $field_type ) ? __( 'Easily add, reorder, and configure choices for this radio button group.', 'frontend-dashboard' ) : __( 'Easily add, reorder, and configure options for this dropdown menu.', 'frontend-dashboard' );
	?>
	<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 fed-choices-builder" id="<?php echo esc_attr( $builder_id ); ?>" data-type="<?php echo esc_attr( $field_type ); ?>">
		<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
			<div class="flex items-center gap-3.5">
				<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
					<i class="<?php echo ( 'radio' === $field_type ) ? 'fas fa-dot-circle' : 'fas fa-layer-group'; ?>"></i>
				</div>
				<div>
					<h3 class="text-sm font-bold text-slate-900 m-0"><?php echo esc_html( $title ); ?></h3>
					<p class="text-xs text-slate-500 m-0 mt-0.5"><?php echo esc_html( $desc ); ?></p>
				</div>
			</div>

			<div class="flex items-center gap-2 flex-wrap">
				<?php if ( 'select' === $field_type ) : ?>
					<div class="px-3 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center gap-2.5">
						<span class="text-xs font-semibold text-slate-700"><?php esc_html_e( 'Multi-Select', 'frontend-dashboard' ); ?></span>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo fed_input_box(
							'extended[multiple]',
							array(
								'default_value' => 'Enable',
								'value'         => $is_multi_val,
								'class'         => 'fed-multi-select-toggle',
							),
							'checkbox'
						);
						?>
					</div>
				<?php endif; ?>

				<button type="button" class="fed-btn-bulk-toggle px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition-all cursor-pointer">
					<i class="fas fa-bolt text-amber-500 text-xs"></i>
					<span><?php esc_html_e( 'Bulk Add', 'frontend-dashboard' ); ?></span>
				</button>

				<button type="button" class="fed-btn-clear-all px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 text-slate-500 text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition-all cursor-pointer">
					<i class="fas fa-trash-alt text-xs"></i>
					<span><?php esc_html_e( 'Clear All', 'frontend-dashboard' ); ?></span>
				</button>
			</div>
		</div>

		<!-- Hidden serialized / JSON input synced on change -->
		<textarea name="input_value" class="fed-choices-raw-sync hidden" style="display:none !important;"><?php echo esc_textarea( $raw_json ); ?></textarea>

		<!-- Bulk Add Drawer (Hidden by default) -->
		<div class="fed-bulk-drawer hidden p-5 bg-gradient-to-br from-slate-50 to-indigo-50/30 rounded-2xl border border-indigo-100/80 space-y-3">
			<div class="flex items-center justify-between">
				<div class="flex items-center gap-2">
					<i class="fas fa-bolt text-amber-500 text-sm"></i>
					<span class="text-xs font-bold text-slate-800"><?php esc_html_e( 'Quick / Bulk Paste Options', 'frontend-dashboard' ); ?></span>
				</div>
				<span class="text-[11px] text-slate-500"><?php esc_html_e( '1 choice per line (e.g. "Option Name" or "key|Option Name")', 'frontend-dashboard' ); ?></span>
			</div>
			<textarea class="fed-bulk-textarea w-full rounded-xl border border-slate-200 bg-white p-3 text-xs text-slate-800 font-mono outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all" rows="4" placeholder="<?php esc_attr_e( "Small\nMedium\nLarge\n\nOr key-value pairs:\nus|United States\nca|Canada", 'frontend-dashboard' ); ?>"></textarea>
			<div class="flex items-center gap-2 justify-end">
				<button type="button" class="fed-btn-bulk-cancel px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 bg-transparent cursor-pointer">
					<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
				</button>
				<button type="button" class="fed-btn-bulk-append px-3.5 py-1.5 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 transition-all cursor-pointer">
					<?php esc_html_e( '+ Append Choices', 'frontend-dashboard' ); ?>
				</button>
				<button type="button" class="fed-btn-bulk-replace px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer shadow-2xs">
					<?php esc_html_e( 'Replace All Choices', 'frontend-dashboard' ); ?>
				</button>
			</div>
		</div>

		<!-- Main 2-Column Section: Options Table & Live Preview -->
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
			<!-- Column 1: Options Repeater Table -->
			<div class="lg:col-span-7 space-y-3">
				<div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 pb-1 border-b border-slate-100">
					<div class="grid grid-cols-12 gap-2 w-full pr-14">
						<span class="col-span-6"><?php esc_html_e( 'Option Label (Visible)', 'frontend-dashboard' ); ?></span>
						<span class="col-span-6"><?php esc_html_e( 'Option Key (Stored)', 'frontend-dashboard' ); ?></span>
					</div>
					<span class="w-14 text-right"><?php esc_html_e( 'Action', 'frontend-dashboard' ); ?></span>
				</div>

				<div class="fed-choices-list space-y-2.5 max-h-[380px] overflow-y-auto pr-1">
					<?php
					$idx = 1;
					foreach ( $options_map as $key => $label ) :
						?>
						<div class="fed-choice-row group flex items-center gap-2 p-2 bg-slate-50/60 hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all">
							<div class="fed-row-num w-6 h-6 rounded-lg bg-white border border-slate-200/90 text-[10px] font-bold text-slate-500 flex items-center justify-center shrink-0 shadow-2xs">
								<?php echo (int) $idx; ?>
							</div>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-2 flex-1">
								<div>
									<input type="text" class="fed-choice-label w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none transition-all" placeholder="<?php esc_attr_e( 'e.g. Option Label', 'frontend-dashboard' ); ?>" value="<?php echo esc_attr( $label ); ?>" />
								</div>
								<div>
									<input type="text" class="fed-choice-key w-full rounded-xl border border-slate-200 bg-white/80 px-3 py-1.5 text-xs font-mono text-slate-600 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 outline-none transition-all" placeholder="<?php esc_attr_e( 'e.g. option_key', 'frontend-dashboard' ); ?>" value="<?php echo esc_attr( $key ); ?>" />
								</div>
							</div>
							<div class="flex items-center gap-1 shrink-0">
								<button type="button" class="fed-choice-duplicate-btn p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all cursor-pointer" title="<?php esc_attr_e( 'Duplicate', 'frontend-dashboard' ); ?>">
									<i class="fas fa-copy text-xs"></i>
								</button>
								<button type="button" class="fed-choice-delete-btn p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all cursor-pointer" title="<?php esc_attr_e( 'Delete Option', 'frontend-dashboard' ); ?>">
									<i class="fas fa-trash-alt text-xs"></i>
								</button>
							</div>
						</div>
						<?php
						++$idx;
					endforeach;
					?>
				</div>

				<div class="pt-2">
					<button type="button" class="fed-btn-add-choice w-full sm:w-auto px-4 py-2 bg-indigo-50 hover:bg-indigo-100/90 text-indigo-700 text-xs font-bold rounded-2xl border border-indigo-200/90 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-2xs">
						<i class="fas fa-plus text-xs"></i>
						<span><?php esc_html_e( 'Add Option', 'frontend-dashboard' ); ?></span>
					</button>
				</div>
			</div>

			<!-- Column 2: Live Interactive Preview & Helper Card -->
			<div class="lg:col-span-5 p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/60 border border-slate-200/80 space-y-4">
				<div class="flex items-center justify-between">
					<span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
						<i class="fas fa-eye text-indigo-600 text-xs"></i>
						<?php esc_html_e( 'Live Preview', 'frontend-dashboard' ); ?>
					</span>
					<span class="fed-choices-count-badge text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100">
						<?php echo count( $options_map ); ?> <?php esc_html_e( 'Choices', 'frontend-dashboard' ); ?>
					</span>
				</div>

				<div class="space-y-2 pt-1">
					<span class="text-[11px] font-semibold text-slate-500 block"><?php esc_html_e( 'Rendered Output on Frontend Form:', 'frontend-dashboard' ); ?></span>
					<div class="fed-preview-render-area bg-white p-3 rounded-xl border border-slate-200/80 shadow-2xs min-h-[50px] flex items-center">
						<!-- Preview dynamically populated by JS -->
					</div>
				</div>

				<div class="pt-2 border-t border-slate-200/60 text-[11px] text-slate-500 space-y-1">
					<p class="m-0 flex items-start gap-1.5">
						<i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0 text-xs"></i>
						<span><?php esc_html_e( 'Typing a label auto-generates the stored key if left blank.', 'frontend-dashboard' ); ?></span>
					</p>
					<p class="m-0 flex items-start gap-1.5">
						<i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0 text-xs"></i>
						<span><?php esc_html_e( '100% compatible with existing and legacy option values.', 'frontend-dashboard' ); ?></span>
					</p>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render Input Validation & Constraints card for Form Field Builder.
 *
 * @param array  $row        Field configuration data.
 * @param string $field_type Current field type slug.
 */
function fed_get_validation_constraints_card( array $row, $field_type = 'single_line' ) {
	$extended   = isset( $row['extended'] ) ? ( is_string( $row['extended'] ) ? maybe_unserialize( $row['extended'] ) : $row['extended'] ) : array();
	$validation = isset( $extended['validation'] ) && is_array( $extended['validation'] ) ? $extended['validation'] : array();

	$pattern       = isset( $validation['pattern'] ) ? $validation['pattern'] : '';
	$pattern_error = isset( $validation['pattern_error'] ) ? $validation['pattern_error'] : '';
	$minlength     = isset( $validation['minlength'] ) ? $validation['minlength'] : '';
	$maxlength     = isset( $validation['maxlength'] ) ? $validation['maxlength'] : '';

	$min_val  = isset( $validation['min'] ) && '' !== $validation['min'] ? $validation['min'] : ( isset( $row['input_min'] ) ? $row['input_min'] : '' );
	$max_val  = isset( $validation['max'] ) && '' !== $validation['max'] ? $validation['max'] : ( isset( $row['input_max'] ) ? $row['input_max'] : '' );
	$step_val = isset( $validation['step'] ) && '' !== $validation['step'] ? $validation['step'] : ( isset( $row['input_step'] ) ? $row['input_step'] : '' );

	$presets = function_exists( 'fed_get_validation_presets_list' ) ? fed_get_validation_presets_list() : array();
	?>
	<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7 fed-validation-card">
		<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
			<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
				<i class="fas fa-check-double"></i>
			</div>
			<div>
				<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Input Validation & Constraints', 'frontend-dashboard' ); ?></h3>
				<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Configure regex pattern validation, character boundaries, and custom error messages.', 'frontend-dashboard' ); ?></p>
			</div>
		</div>

		<?php if ( 'number' === $field_type ) : ?>
			<div class="space-y-5">
				<div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
					<div class="space-y-1.5">
						<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Minimum Allowed Value (min)', 'frontend-dashboard' ); ?></label>
						<input type="number" step="any" name="extended[validation][min]" value="<?php echo esc_attr( $min_val ); ?>" placeholder="<?php esc_attr_e( 'e.g. 0', 'frontend-dashboard' ); ?>" class="fed-validation-min-input w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;" />
						<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Minimum number allowed.', 'frontend-dashboard' ); ?></p>
					</div>
					<div class="space-y-1.5">
						<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Maximum Allowed Value (max)', 'frontend-dashboard' ); ?></label>
						<input type="number" step="any" name="extended[validation][max]" value="<?php echo esc_attr( $max_val ); ?>" placeholder="<?php esc_attr_e( 'e.g. 1000', 'frontend-dashboard' ); ?>" class="fed-validation-max-input w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;" />
						<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Maximum number allowed.', 'frontend-dashboard' ); ?></p>
					</div>
					<div class="space-y-1.5">
						<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Step Increment (step)', 'frontend-dashboard' ); ?></label>
						<input type="text" name="extended[validation][step]" value="<?php echo esc_attr( $step_val ); ?>" placeholder="<?php esc_attr_e( 'e.g. 1 or 0.01', 'frontend-dashboard' ); ?>" class="fed-validation-step-input w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;" />
						<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Step interval or precision.', 'frontend-dashboard' ); ?></p>
					</div>
				</div>

				<div class="space-y-1.5">
					<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Custom Validation Error Message', 'frontend-dashboard' ); ?></label>
					<input type="text" name="extended[validation][pattern_error]" value="<?php echo esc_attr( $pattern_error ); ?>" placeholder="<?php esc_attr_e( 'e.g. Please enter a valid number within the permitted range', 'frontend-dashboard' ); ?>" class="fed-validation-error-input w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;" />
					<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Feedback shown to end users when field value violates constraints.', 'frontend-dashboard' ); ?></p>
				</div>
			</div>
		<?php else : ?>
			<div class="space-y-5">
				<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
					<div class="space-y-1.5">
						<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Validation Pattern Preset', 'frontend-dashboard' ); ?></label>
						<select class="fed-validation-preset-select w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all cursor-pointer" style="min-height:38px;">
							<option value=""><?php esc_html_e( '-- Select a Preset (Optional) --', 'frontend-dashboard' ); ?></option>
							<?php foreach ( $presets as $pkey => $preset ) : ?>
								<option value="<?php echo esc_attr( $pkey ); ?>"
									data-pattern="<?php echo esc_attr( $preset['pattern'] ); ?>"
									data-error="<?php echo esc_attr( $preset['error'] ); ?>"
									<?php echo ( $pattern === $preset['pattern'] ) ? 'selected="selected"' : ''; ?>>
									<?php echo esc_html( $preset['label'] ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Pick a standard pattern or write your own custom regex below.', 'frontend-dashboard' ); ?></p>
					</div>

					<div class="space-y-1.5">
						<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Regular Expression Pattern (Regex)', 'frontend-dashboard' ); ?></label>
						<input type="text" name="extended[validation][pattern]" value="<?php echo esc_attr( $pattern ); ?>" placeholder="<?php esc_attr_e( 'e.g. ^[a-zA-Z0-9_-]+$', 'frontend-dashboard' ); ?>" class="fed-validation-pattern-input w-full text-xs font-mono font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;" />
						<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Regular expression without enclosing delimiters (HTML5 & PCRE).', 'frontend-dashboard' ); ?></p>
					</div>
				</div>

				<div class="space-y-1.5">
					<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Custom Validation Error Message', 'frontend-dashboard' ); ?></label>
					<input type="text" name="extended[validation][pattern_error]" value="<?php echo esc_attr( $pattern_error ); ?>" placeholder="<?php esc_attr_e( 'e.g. Please enter a valid 5-digit postal code', 'frontend-dashboard' ); ?>" class="fed-validation-error-input w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;" />
					<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Displayed when the input fails validation. Also sets browser HTML5 title tooltip.', 'frontend-dashboard' ); ?></p>
				</div>

				<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
					<div class="space-y-1.5">
						<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Minimum Length (Characters)', 'frontend-dashboard' ); ?></label>
						<input type="number" min="0" name="extended[validation][minlength]" value="<?php echo esc_attr( $minlength ); ?>" placeholder="<?php esc_attr_e( 'e.g. 5', 'frontend-dashboard' ); ?>" class="fed-validation-minlength-input w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;" />
						<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Minimum character length required (leave blank for none).', 'frontend-dashboard' ); ?></p>
					</div>
					<div class="space-y-1.5">
						<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Maximum Length (Characters)', 'frontend-dashboard' ); ?></label>
						<input type="number" min="0" name="extended[validation][maxlength]" value="<?php echo esc_attr( $maxlength ); ?>" placeholder="<?php esc_attr_e( 'e.g. 100', 'frontend-dashboard' ); ?>" class="fed-validation-maxlength-input w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;" />
						<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Maximum character limit allowed (leave blank for none).', 'frontend-dashboard' ); ?></p>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render initial HTML input control inside live preview.
 *
 * @param array  $row        Field details.
 * @param string $field_type Field type slug.
 * @return string
 */
function fed_get_live_preview_initial_input_html( array $row, $field_type = 'single_line' ) {
	$placeholder = isset( $row['placeholder'] ) ? $row['placeholder'] : '';
	$extended    = isset( $row['extended'] ) ? ( is_string( $row['extended'] ) ? maybe_unserialize( $row['extended'] ) : $row['extended'] ) : array();
	$default_val = isset( $extended['default_value'] ) ? $extended['default_value'] : '';
	$is_req      = isset( $row['is_required'] ) && ( 'true' === $row['is_required'] || true === $row['is_required'] || 'yes' === $row['is_required'] || 'Enable' === $row['is_required'] );
	$req_attr    = $is_req ? ' required' : '';

	switch ( $field_type ) {
		case 'multi_line':
			return '<textarea rows="3" class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl p-3.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' . esc_attr( $placeholder ) . '"' . $req_attr . '>' . esc_textarea( $default_val ) . '</textarea>';
		case 'email':
			return '<div class="relative"><div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs"><i class="fas fa-envelope"></i></div><input type="email" class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' . esc_attr( ! empty( $placeholder ) ? $placeholder : 'user@example.com' ) . '" value="' . esc_attr( $default_val ) . '"' . $req_attr . ' /></div>';
		case 'password':
			return '<div class="relative"><div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs"><i class="fas fa-lock"></i></div><input type="password" class="fed-preview-pwd-input fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl pl-9 pr-10 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' . esc_attr( ! empty( $placeholder ) ? $placeholder : '••••••••' ) . '" value="' . esc_attr( $default_val ) . '"' . $req_attr . ' /><button type="button" class="fed-preview-toggle-pwd absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer" title="Toggle visibility"><i class="fas fa-eye text-xs"></i></button></div>';
		case 'number':
			return '<div class="relative"><div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs"><i class="fas fa-hashtag"></i></div><input type="number" class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' . esc_attr( ! empty( $placeholder ) ? $placeholder : '0' ) . '" value="' . esc_attr( $default_val ) . '"' . $req_attr . ' /></div>';
		case 'url':
			return '<div class="relative"><div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs"><i class="fas fa-globe"></i></div><input type="url" class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' . esc_attr( ! empty( $placeholder ) ? $placeholder : 'https://example.com' ) . '" value="' . esc_attr( $default_val ) . '"' . $req_attr . ' /></div>';
		case 'select':
			return '<select class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all cursor-pointer"' . $req_attr . '><option value="">' . esc_html( ! empty( $placeholder ) ? $placeholder : __( '-- Select an option --', 'frontend-dashboard' ) ) . '</option><option value="option_1" selected>Option 1</option><option value="option_2">Option 2</option></select>';
		case 'radio':
			return '<div class="space-y-2 pt-0.5"><label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/90 bg-white hover:bg-slate-50/80 transition-all cursor-pointer shadow-2xs"><input type="radio" name="preview_radio" value="option_1" checked class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300" /><span class="text-xs font-semibold text-slate-700">Option 1</span><span class="text-[10px] text-slate-400 font-mono ml-auto">option_1</span></label><label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/90 bg-white hover:bg-slate-50/80 transition-all cursor-pointer shadow-2xs"><input type="radio" name="preview_radio" value="option_2" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300" /><span class="text-xs font-semibold text-slate-700">Option 2</span><span class="text-[10px] text-slate-400 font-mono ml-auto">option_2</span></label></div>';
		case 'checkbox':
			return '<label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200/90 bg-white hover:bg-slate-50/80 transition-all cursor-pointer shadow-2xs"><input type="checkbox" name="preview_cb" value="1" class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300" /><span class="text-xs font-semibold text-slate-800">' . esc_html( ! empty( $row['label_name'] ) ? $row['label_name'] : __( 'Enable Option', 'frontend-dashboard' ) ) . '</span></label>';
		case 'date':
			return '<div class="relative"><div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs"><i class="fas fa-calendar-alt"></i></div><input type="date" class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all" value="' . esc_attr( $default_val ) . '"' . $req_attr . ' /></div>';
		case 'file':
			return '<div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 bg-white hover:bg-indigo-50/20 rounded-2xl p-6 text-center transition-all cursor-pointer group shadow-2xs"><div class="w-10 h-10 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base mb-2 group-hover:scale-110 transition-transform shadow-2xs"><i class="fas fa-cloud-upload-alt"></i></div><p class="text-xs font-semibold text-slate-800 m-0"><span class="text-indigo-600 font-bold">' . esc_html__( 'Choose a file', 'frontend-dashboard' ) . '</span> ' . esc_html__( 'or drag & drop', 'frontend-dashboard' ) . '</p><p class="text-[11px] text-slate-400 m-0 mt-1">' . esc_html( ! empty( $placeholder ) ? $placeholder : __( 'PDF, PNG, JPG, or DOC (Max 10MB)', 'frontend-dashboard' ) ) . '</p><input type="file" class="hidden fed-preview-real-file-input"' . $req_attr . ' /></div>';
		case 'color':
			$col_val = ! empty( $default_val ) ? $default_val : '#4f46e5';
			return '<div class="flex items-center gap-3"><input type="color" class="fed-preview-color-picker w-10 h-10 rounded-xl border border-slate-200 cursor-pointer p-0.5 bg-white shadow-2xs" value="' . esc_attr( $col_val ) . '" /><input type="text" class="fed-preview-color-text w-32 text-xs font-mono font-medium text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none" value="' . esc_attr( $col_val ) . '" readonly /><span class="text-xs text-slate-400">' . esc_html__( 'Click swatch to pick color', 'frontend-dashboard' ) . '</span></div>';
		case 'single_line':
		default:
			return '<input type="text" class="fed-preview-active-input w-full text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400" placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $default_val ) . '"' . $req_attr . ' />';
	}
}

/**
 * Render WYSIWYG Live Interactive Form Preview Panel.
 *
 * @param array  $row    Form Field details.
 * @param string $action Action type ('profile' or 'post').
 */
function fed_render_live_preview_panel( array $row, $action = 'profile' ) {
	$label_name  = isset( $row['label_name'] ) && '' !== $row['label_name'] ? $row['label_name'] : __( 'Field Label', 'frontend-dashboard' );
	$meta_key    = isset( $row['input_meta'] ) && '' !== $row['input_meta'] ? $row['input_meta'] : 'meta_key';
	$placeholder = isset( $row['placeholder'] ) ? $row['placeholder'] : '';
	$help_text   = isset( $row['tooltip_body'] ) ? $row['tooltip_body'] : '';
	$is_req      = isset( $row['is_required'] ) && ( 'true' === $row['is_required'] || true === $row['is_required'] || 'yes' === $row['is_required'] || 'Enable' === $row['is_required'] );
	$field_type  = isset( $row['input_type'] ) && ! empty( $row['input_type'] ) ? $row['input_type'] : 'single_line';
	$extended    = isset( $row['extended'] ) ? ( is_string( $row['extended'] ) ? maybe_unserialize( $row['extended'] ) : $row['extended'] ) : array();
	$width_val   = isset( $extended['field_width'] ) ? (string) $extended['field_width'] : '100';

	$width_percent = '100%';
	$width_class   = 'fed-preview-col-100';
	if ( '50' === $width_val || '6' === $width_val ) {
		$width_percent = '50%';
		$width_class   = 'fed-preview-col-50';
	} elseif ( '33' === $width_val || '4' === $width_val ) {
		$width_percent = '33%';
		$width_class   = 'fed-preview-col-33';
	} elseif ( '25' === $width_val || '3' === $width_val ) {
		$width_percent = '25%';
		$width_class   = 'fed-preview-col-25';
	}

	$url_path = ( 'post' === $action ) ? 'dashboard/new-post' : 'dashboard/profile';
	?>
	<div class="fed-live-preview-card bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xs space-y-6 transition-all" id="fed_live_preview_panel">
		<!-- Top Bar: Header, Live Badge, Studio Toggle, Viewport Toggles, Actions -->
		<div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-100">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-sm shrink-0 shadow-2xs">
					<i class="fas fa-desktop"></i>
				</div>
				<div>
					<div class="flex items-center gap-2">
						<h3 class="text-xs sm:text-sm font-bold text-slate-900 m-0">
							<?php esc_html_e( 'Live Interactive Form Preview', 'frontend-dashboard' ); ?>
						</h3>
						<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
							<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
							<?php esc_html_e( 'WYSIWYG Live', 'frontend-dashboard' ); ?>
						</span>
					</div>
					<p class="text-[11px] text-slate-400 m-0 mt-0.5">
						<?php esc_html_e( 'Real-time interactive rendering of the frontend input as users see it.', 'frontend-dashboard' ); ?>
					</p>
				</div>
			</div>

			<!-- Action Controls -->
			<div class="flex items-center gap-2 flex-wrap">
				<!-- Studio Side-by-Side vs Banner Layout Toggle -->
				<button type="button" class="fed-preview-layout-toggle-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition-all cursor-pointer shadow-2xs" title="<?php esc_attr_e( 'Toggle Split Studio Side-by-Side View on wide screens', 'frontend-dashboard' ); ?>">
					<i class="fas fa-columns text-indigo-500 text-xs"></i>
					<span class="fed-layout-mode-label hidden sm:inline-block"><?php esc_html_e( 'Split Studio', 'frontend-dashboard' ); ?></span>
				</button>

				<!-- Device Viewport Switcher -->
				<div class="inline-flex items-center p-1 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-600 text-xs gap-1">
					<button type="button" class="fed-preview-viewport-btn px-2.5 py-1 rounded-lg text-xs font-semibold transition-all cursor-pointer is-active bg-white text-indigo-700 shadow-2xs" data-viewport="desktop" title="<?php esc_attr_e( 'Desktop View (100% Fluid)', 'frontend-dashboard' ); ?>">
						<i class="fas fa-desktop text-[11px]"></i>
						<span class="hidden sm:inline-block ml-1"><?php esc_html_e( 'Desktop', 'frontend-dashboard' ); ?></span>
					</button>
					<button type="button" class="fed-preview-viewport-btn px-2.5 py-1 rounded-lg text-xs font-semibold transition-all cursor-pointer text-slate-600 hover:text-slate-900" data-viewport="tablet" title="<?php esc_attr_e( 'Tablet View (640px)', 'frontend-dashboard' ); ?>">
						<i class="fas fa-tablet-alt text-[11px]"></i>
						<span class="hidden sm:inline-block ml-1"><?php esc_html_e( 'Tablet', 'frontend-dashboard' ); ?></span>
					</button>
					<button type="button" class="fed-preview-viewport-btn px-2.5 py-1 rounded-lg text-xs font-semibold transition-all cursor-pointer text-slate-600 hover:text-slate-900" data-viewport="mobile" title="<?php esc_attr_e( 'Mobile View (390px)', 'frontend-dashboard' ); ?>">
						<i class="fas fa-mobile-alt text-[11px]"></i>
						<span class="hidden sm:inline-block ml-1"><?php esc_html_e( 'Mobile', 'frontend-dashboard' ); ?></span>
					</button>
				</div>

				<!-- Toggle HTML Code Inspector -->
				<button type="button" class="fed-preview-code-toggle-btn inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-medium transition-all cursor-pointer shadow-2xs" title="<?php esc_attr_e( 'View Rendered HTML Markup', 'frontend-dashboard' ); ?>">
					<i class="fas fa-code text-[11px] text-indigo-500"></i>
					<span class="hidden md:inline-block"><?php esc_html_e( 'HTML', 'frontend-dashboard' ); ?></span>
				</button>

				<!-- Reset Test Values Button -->
				<button type="button" class="fed-preview-reset-btn inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-medium transition-all cursor-pointer shadow-2xs" title="<?php esc_attr_e( 'Reset test values to defaults', 'frontend-dashboard' ); ?>">
					<i class="fas fa-redo-alt text-[10px]"></i>
					<span class="hidden md:inline-block"><?php esc_html_e( 'Reset', 'frontend-dashboard' ); ?></span>
				</button>

				<!-- Collapse / Expand Toggle Button -->
				<button type="button" class="fed-preview-collapse-btn p-1.5 w-8 h-8 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-800 transition-all cursor-pointer flex items-center justify-center shadow-2xs" title="<?php esc_attr_e( 'Toggle Preview Visibility', 'frontend-dashboard' ); ?>">
					<i class="fas fa-chevron-up text-xs transition-transform duration-200"></i>
				</button>
			</div>
		</div>

		<!-- Live Preview Body -->
		<div class="fed-preview-body transition-all duration-300 space-y-4">
			<!-- Device Viewport Mockup Wrapper -->
			<div class="fed-preview-viewport-wrapper is-desktop w-full mx-auto transition-all duration-300">
				<!-- Realistic Browser Mockup Chrome -->
				<div class="rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden bg-slate-50/70">
					<!-- Browser Header Bar -->
					<div class="flex items-center justify-between px-4 py-2.5 bg-slate-100/90 border-b border-slate-200/80 gap-3">
						<div class="flex items-center gap-1.5 shrink-0">
							<span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
							<span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
							<span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
						</div>
						<div class="flex-1 max-w-sm mx-auto bg-white/90 border border-slate-200/80 rounded-lg px-3 py-1 text-[11px] text-slate-500 font-mono text-center truncate flex items-center justify-center gap-1.5 shadow-2xs">
							<i class="fas fa-lock text-[9px] text-emerald-600"></i>
							<span><?php echo esc_html( $url_path ); ?></span>
						</div>
						<div class="shrink-0 text-[10px] font-semibold text-slate-400 uppercase font-mono hidden sm:inline-block fed-preview-viewport-label">
							<?php esc_html_e( 'Desktop (100%)', 'frontend-dashboard' ); ?>
						</div>
					</div>

					<!-- Form Canvas Surface -->
					<div class="p-5 sm:p-7 bg-slate-50/60">
						<!-- Simulated Frontend Form Card -->
						<div class="bg-white rounded-2xl p-5 sm:p-7 border border-slate-200/90 shadow-xs">
							<!-- 12-Column Grid Simulator -->
							<div class="fed-preview-grid">
								<!-- Rendered Field Column (Dynamic Width) -->
								<div id="fed_live_preview_field_wrapper" class="<?php echo esc_attr( $width_class ); ?> space-y-2 transition-all duration-300">
									<!-- Label Bar -->
									<div class="flex items-center justify-between gap-2">
										<label class="block text-xs sm:text-sm font-semibold text-slate-800 fed-preview-label m-0">
											<span class="fed-preview-label-text"><?php echo esc_html( $label_name ); ?></span>
											<span class="text-rose-500 font-bold ml-0.5 fed-preview-required-star <?php echo $is_req ? '' : 'hidden'; ?>">*</span>
										</label>
										<!-- Metadata badges -->
										<div class="flex items-center gap-1.5 text-[10px]">
											<span class="fed-preview-meta-badge bg-white px-2 py-0.5 rounded-md border border-slate-200 text-slate-600 font-mono font-normal"><?php echo esc_html( $meta_key ); ?></span>
											<span class="fed-preview-width-badge bg-indigo-50 px-1.5 py-0.5 rounded-md border border-indigo-100 text-indigo-700 font-bold"><?php echo esc_html( $width_percent ); ?></span>
										</div>
									</div>

									<!-- Rendered Input Slot -->
									<div class="fed-preview-input-slot relative">
										<?php
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo fed_get_live_preview_initial_input_html( $row, $field_type );
										?>
									</div>

									<!-- Help / Description Text -->
									<p class="text-xs text-slate-500 m-0 mt-1 fed-preview-help-text leading-relaxed <?php echo empty( $help_text ) ? 'hidden' : ''; ?>">
										<?php echo esc_html( $help_text ); ?>
									</p>

									<!-- Dynamic Validation Badges -->
									<div class="fed-preview-validation-badges flex flex-wrap gap-1.5 pt-1 text-[10px] hidden">
									</div>
								</div>

								<!-- Companion Grid Slot Filler for multi-column visualization -->
								<div class="fed-preview-companion-slot <?php echo ( 'fed-preview-col-100' === $width_class ) ? 'hidden' : ''; ?> border-2 border-dashed border-slate-200/80 rounded-2xl p-4 flex items-center justify-center text-slate-400 text-xs font-medium bg-slate-50/50">
									<div class="flex items-center gap-2 text-slate-400">
										<i class="fas fa-columns text-slate-300"></i>
										<span><?php esc_html_e( 'Adjacent Field Column Space', 'frontend-dashboard' ); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Live Preview Status Bar (Full Width Outside Device Viewport) -->
			<div class="flex flex-wrap items-center justify-between gap-2 pt-2 px-1 text-[11px] text-slate-400 border-t border-slate-100">
				<div class="flex items-center gap-1.5">
					<i class="fas fa-mouse-pointer text-[10px] text-indigo-500"></i>
					<span><?php esc_html_e( 'Interactive: You can type, select, and test validation right in this preview box.', 'frontend-dashboard' ); ?></span>
				</div>
				<div class="fed-preview-status-pill text-[10px] font-mono text-slate-400">
					<?php esc_html_e( 'Field Type:', 'frontend-dashboard' ); ?> <span class="text-indigo-600 font-bold fed-preview-type-name"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $field_type ) ) ); ?></span>
				</div>
			</div>

			<!-- Collapsible Rendered HTML Markup Inspector (Full Width Outside Device Viewport) -->
			<div class="fed-preview-code-drawer hidden mt-4 pt-4 border-t border-slate-200/80 space-y-2">
				<div class="flex items-center justify-between">
					<span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
						<i class="fas fa-code text-indigo-500 text-xs"></i>
						<?php esc_html_e( 'Rendered HTML Output Markup', 'frontend-dashboard' ); ?>
					</span>
					<button type="button" class="fed-preview-copy-html-btn inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 cursor-pointer">
						<i class="fas fa-copy text-[10px]"></i>
						<span><?php esc_html_e( 'Copy HTML', 'frontend-dashboard' ); ?></span>
					</button>
				</div>
				<pre class="fed-preview-code-output bg-slate-900 text-sky-300 p-4 rounded-2xl text-xs font-mono overflow-x-auto whitespace-pre-wrap select-all border border-slate-800 m-0"></pre>
			</div>
		</div>
	</div>
	<?php
}
