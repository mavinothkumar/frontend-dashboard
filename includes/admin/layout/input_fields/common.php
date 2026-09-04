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