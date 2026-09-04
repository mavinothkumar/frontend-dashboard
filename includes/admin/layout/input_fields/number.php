<?php
/**
 * Number.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Input Fields Number.
 *
 * @param  array  $row  Row.
 * @param  string $action  Action.
 * @param  array  $menu_options  Menu Options.
 */
function fed_admin_input_fields_number( $row, $action, $menu_options ) {
	$min_val   = isset( $row['input_min'] ) ? $row['input_min'] : '';
	$max_val   = isset( $row['input_max'] ) ? $row['input_max'] : '';
	$step_val  = isset( $row['input_step'] ) ? $row['input_step'] : '';
	$is_active = ( isset( $row['input_type'] ) && 'number' === $row['input_type'] );
	?>
	<div class="fed_input_type_container fed_input_number_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="number">
		<form method="post"
			  class="fed_admin_menu fed_ajax space-y-7"
			  action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

			<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
			<?php echo fed_loader(); ?>

			<!-- Card: Basic Field Settings -->
			<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
				<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
					<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
						<i class="fas fa-hashtag"></i>
					</div>
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Number Field', 'frontend-dashboard' ); ?></h3>
						<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Numerical input with min, max, and step increment constraints.', 'frontend-dashboard' ); ?></p>
					</div>
				</div>

				<div class="space-y-5">
					<?php fed_get_admin_up_label_input_order( $row ); ?>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
						<?php fed_get_admin_up_input_meta( $row ); ?>
						<?php fed_get_placeholder_field( $row ); ?>
					</div>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
						<?php fed_get_class_field( $row ); ?>
						<?php fed_get_id_field( $row ); ?>
					</div>

					<!-- Min, Max, Step Row -->
					<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-2">
						<div class="space-y-2">
							<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Min Value', 'frontend-dashboard' ); ?></label>
							<?php echo fed_input_box( 'input_min', array( 'value' => $min_val ), 'number' ); ?>
							<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Minimum allowed number.', 'frontend-dashboard' ); ?></p>
						</div>
						<div class="space-y-1.5">
							<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Max Value', 'frontend-dashboard' ); ?></label>
							<?php echo fed_input_box( 'input_max', array( 'value' => $max_val ), 'number' ); ?>
							<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Maximum allowed number.', 'frontend-dashboard' ); ?></p>
						</div>
						<div class="space-y-1.5">
							<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Step Increment', 'frontend-dashboard' ); ?></label>
							<?php echo fed_input_box( 'input_step', array( 'value' => $step_val ), 'number' ); ?>
							<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Step interval (e.g. 1, 5, 0.1).', 'frontend-dashboard' ); ?></p>
						</div>
					</div>
				</div>
			</div>

			<?php
			fed_get_admin_up_display_permission( $row, $action );
			fed_get_admin_up_role_based( $row, $action, $menu_options );
			fed_get_input_type_and_submit_btn( 'number', $action );
			?>
		</form>
	</div>
	<?php
}
