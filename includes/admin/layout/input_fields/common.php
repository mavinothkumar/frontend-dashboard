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
 * Render Interactive Choices & Options Repeater Builder.
 *
 * @param  mixed   $input_val    Raw option values (legacy string, JSON, array).
 * @param  string  $field_type   Field type: 'select' or 'radio'.
 * @param  string  $is_multi_val Multi-select status for select fields.
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
						$idx++;
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