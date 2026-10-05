<?php
/**
 * Label Inspector Layout.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Label Field Inspector.
 *
 * @param array  $row
 * @param string $action
 * @param array  $menu_options
 */
function fed_admin_input_fields_label( $row, $action, $menu_options ) {
	$is_active     = ( isset( $row['input_type'] ) && 'label' === $row['input_type'] );
	$label_content = isset( $row['input_value'] ) ? $row['input_value'] : '';
	?>
	<div class="fed_input_type_container fed_input_label_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="label">
		<form method="post"
				class="fed_admin_menu fed_ajax space-y-7"
				action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

			<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo fed_loader();
			?>

			<!-- Card: Basic Field Settings -->
			<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
				<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
					<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
						<i class="fas fa-tag"></i>
					</div>
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Custom Label / Static HTML', 'frontend-dashboard' ); ?></h3>
						<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Display custom formatted HTML or instructional text in the form.', 'frontend-dashboard' ); ?></p>
					</div>
				</div>

				<div class="space-y-5">
					<?php fed_get_admin_up_label_input_order( $row ); ?>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
						<?php fed_get_admin_up_input_meta( $row ); ?>
						<?php fed_get_class_field( $row ); ?>
					</div>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
						<?php fed_get_id_field( $row ); ?>
					</div>
				</div>
			</div>

			<!-- Card: Label Content -->
			<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-5">
				<div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
					<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
						<i class="fas fa-code"></i>
					</div>
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Label / HTML Content', 'frontend-dashboard' ); ?></h3>
						<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Enter rich HTML or description text that will be shown to users.', 'frontend-dashboard' ); ?></p>
					</div>
				</div>

				<div class="fed_wp_editor_wrapper rounded-2xl overflow-hidden border border-slate-200/90 shadow-2xs bg-white">
					<textarea id="fed_label_html_content_editor" name="input_value" rows="10" class="w-full p-4 border-0 outline-none font-mono text-xs text-slate-800 bg-white" placeholder="<?php esc_attr_e( 'Enter HTML content or text here...', 'frontend-dashboard' ); ?>"><?php echo esc_textarea( $label_content ); ?></textarea>
				</div>
			</div>

			<?php
			fed_get_admin_up_display_permission( $row, $action );
			fed_get_admin_up_role_based( $row, $action, $menu_options );
			fed_get_input_type_and_submit_btn( 'label', $action, $row );
			?>
		</form>
	</div>
	<?php
}