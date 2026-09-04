<?php
/**
 * Checkbox.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param $row
 * @param $action
 * @param $menu_options
 */
function fed_admin_input_fields_checkbox( $row, $action, $menu_options ) {
	$custom_label = isset( $row['extended']['label'] ) ? $row['extended']['label'] : '';
	$is_active    = ( isset( $row['input_type'] ) && 'checkbox' === $row['input_type'] );
	?>
	<div class="fed_input_type_container fed_input_checkbox_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="checkbox">
		<form method="post"
			  class="fed_admin_menu fed_ajax space-y-7"
			  action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

			<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
			<?php echo fed_loader(); ?>

			<!-- Card: Basic Field Settings -->
			<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
				<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
					<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
						<i class="fas fa-check-square"></i>
					</div>
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Checkbox Field', 'frontend-dashboard' ); ?></h3>
						<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Toggle / Checkbox input with optional custom HTML label (e.g. Terms & Conditions).', 'frontend-dashboard' ); ?></p>
					</div>
				</div>

				<div class="space-y-5">
					<?php fed_get_admin_up_label_input_order( $row ); ?>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
						<?php fed_get_admin_up_input_meta( $row ); ?>
						<div class="space-y-2">
							<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Custom Label / Terms Text (HTML Supported)', 'frontend-dashboard' ); ?></label>
							<textarea class="w-full rounded-xl border-slate-200 bg-slate-50 text-xs text-slate-800 p-2.5 outline-none focus:border-indigo-500 focus:bg-white transition-all font-sans"
								name="extended[label]"
								rows="3"
								placeholder="<?php esc_attr_e( 'e.g. I agree to the <a href="/terms">Terms of Service</a>', 'frontend-dashboard' ); ?>"><?php echo esc_textarea( $custom_label ); ?></textarea>
							<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Rendered directly next to the checkbox input.', 'frontend-dashboard' ); ?></p>
						</div>
					</div>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
						<?php fed_get_class_field( $row ); ?>
						<?php fed_get_id_field( $row ); ?>
					</div>
				</div>
			</div>

			<?php
			fed_get_admin_up_display_permission( $row, $action );
			fed_get_admin_up_role_based( $row, $action, $menu_options );
			fed_get_input_type_and_submit_btn( 'checkbox', $action );
			?>
		</form>
	</div>
	<?php
}
