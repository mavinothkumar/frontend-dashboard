<?php
/**
 * Select
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Select.
 *
 * @param  array  $row  Row.
 * @param  string $action  Action.
 * @param  array  $menu_options  Menu Options.
 */
function fed_admin_input_fields_select( $row, $action, $menu_options ) {
	$input_val    = isset( $row['input_value'] ) ? $row['input_value'] : '';
	$is_multi_val = isset( $row['extended']['multiple'] ) ? $row['extended']['multiple'] : '';
	$is_active    = ( isset( $row['input_type'] ) && 'select' === $row['input_type'] );
	?>
	<div class="fed_input_type_container fed_input_select_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="select">
		<form method="post"
			  class="fed_admin_menu fed_ajax space-y-7"
			  action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

			<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
			<?php echo fed_loader(); ?>

			<!-- Card: Basic Field Settings -->
			<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
				<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
					<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
						<i class="fas fa-list-ul"></i>
					</div>
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Select / Dropdown Field', 'frontend-dashboard' ); ?></h3>
						<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Dropdown selection menu with optional multi-select support.', 'frontend-dashboard' ); ?></p>
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
				</div>
			</div>

			<!-- Card: Dropdown Choices / Options & Multi-Select -->
			<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-5">
				<div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100">
					<div class="flex items-center gap-3.5">
						<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
							<i class="fas fa-layer-group"></i>
						</div>
						<div>
							<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Dropdown Choices & Configuration', 'frontend-dashboard' ); ?></h3>
							<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Define options in key,value pairs and enable multi-selection if needed.', 'frontend-dashboard' ); ?></p>
						</div>
					</div>

					<div class="p-2.5 bg-slate-50/80 border border-slate-200/80 rounded-xl flex items-center gap-3">
						<span class="text-xs font-bold text-slate-700"><?php esc_html_e( 'Enable Multi-Select?', 'frontend-dashboard' ); ?></span>
						<?php
						echo fed_input_box(
							'extended[multiple]',
							array(
								'default_value' => 'Enable',
								'value'         => $is_multi_val,
							),
							'checkbox'
						);
						?>
					</div>
				</div>

				<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
					<div class="space-y-2">
						<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Options (key,value format)', 'frontend-dashboard' ); ?></label>
						<?php
						echo fed_input_box(
							'input_value',
							array(
								'placeholder' => __( "val1,First Option\nval2,Second Option\nval3,Third Option", 'frontend-dashboard' ),
								'rows'        => 6,
								'value'       => $input_val,
							),
							'multi_line'
						);
						?>
						<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Each line or pipe (|) creates a separate dropdown item.', 'frontend-dashboard' ); ?></p>
					</div>

					<div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
						<span class="text-xs font-bold text-slate-700 uppercase tracking-wider block"><?php esc_html_e( 'Format Guide & Preview', 'frontend-dashboard' ); ?></span>
						<p class="text-xs text-slate-600 font-mono bg-white p-2.5 rounded-lg border border-slate-200/70">
							key1,Value 1 | key2,Value 2 | key3,Value 3
						</p>
						<div class="space-y-1.5 pt-1">
							<span class="text-[11px] font-semibold text-slate-500 block"><?php esc_html_e( 'Rendered Dropdown Output:', 'frontend-dashboard' ); ?></span>
							<select disabled class="w-full rounded-xl border border-slate-200 bg-white text-xs text-slate-700 p-2.5 cursor-not-allowed">
								<option><?php esc_html_e( '-- Select Option --', 'frontend-dashboard' ); ?></option>
								<option><?php esc_html_e( 'Value 1', 'frontend-dashboard' ); ?></option>
								<option><?php esc_html_e( 'Value 2', 'frontend-dashboard' ); ?></option>
								<option><?php esc_html_e( 'Value 3', 'frontend-dashboard' ); ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<?php
			fed_get_admin_up_display_permission( $row, $action );
			fed_get_admin_up_role_based( $row, $action, $menu_options );
			fed_get_input_type_and_submit_btn( 'select', $action );
			?>
		</form>
	</div>
	<?php
}
