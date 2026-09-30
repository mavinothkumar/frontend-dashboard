<?php
/**
 * Date Inspector Layout.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Date Field Inspector.
 *
 * @param array  $row
 * @param string $action
 * @param array  $menu_options
 */
function fed_admin_input_fields_date( $row, $action, $menu_options ) {
	$is_active = ( isset( $row['input_type'] ) && 'date' === $row['input_type'] );
	$extended  = isset( $row['extended'] ) ? ( is_string( $row['extended'] ) ? maybe_unserialize( $row['extended'] ) : $row['extended'] ) : array();
	if ( ! is_array( $extended ) ) {
		$extended = array();
	}
	?>
	<div class="fed_input_type_container fed_input_date_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="date">
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
						<i class="fas fa-calendar-alt"></i>
					</div>
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Date & Time Field', 'frontend-dashboard' ); ?></h3>
						<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Date & time picker with customizable format, mode, and time options.', 'frontend-dashboard' ); ?></p>
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

					<!-- Date & Time Options -->
					<div class="pt-4 border-t border-slate-100 space-y-3">
						<h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2"><?php esc_html_e( 'Date & Time Configurations', 'frontend-dashboard' ); ?></h4>
						<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Date Format', 'frontend-dashboard' ); ?></label>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo fed_input_box(
									'date_format',
									array(
										'name'    => 'extended[date_format]',
										'value'   => isset( $extended['date_format'] ) ? $extended['date_format'] : '',
										'options' => fed_get_date_formats(),
									),
									'select'
								);
								?>
							</div>
							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Enable Time', 'frontend-dashboard' ); ?></label>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo fed_input_box(
									'enable_time',
									array(
										'name'    => 'extended[enable_time]',
										'value'   => isset( $extended['enable_time'] ) ? $extended['enable_time'] : '',
										'options' => array(
											'false' => __( 'False', 'frontend-dashboard' ),
											'true'  => __( 'True', 'frontend-dashboard' ),
										),
									),
									'select'
								);
								?>
							</div>
							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Date Mode', 'frontend-dashboard' ); ?></label>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo fed_input_box(
									'date_mode',
									array(
										'name'    => 'extended[date_mode]',
										'value'   => isset( $extended['date_mode'] ) ? $extended['date_mode'] : '',
										'options' => fed_get_date_mode(),
									),
									'select'
								);
								?>
							</div>
							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Time Format', 'frontend-dashboard' ); ?></label>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo fed_input_box(
									'time_24hr',
									array(
										'name'    => 'extended[time_24hr]',
										'value'   => isset( $extended['time_24hr'] ) ? $extended['time_24hr'] : '',
										'options' => array(
											'true'  => __( '24 Hours', 'frontend-dashboard' ),
											'false' => __( '12 Hours (AM/PM)', 'frontend-dashboard' ),
										),
									),
									'select'
								);
								?>
							</div>
							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Enable Seconds', 'frontend-dashboard' ); ?></label>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo fed_input_box(
									'enable_seconds',
									array(
										'name'    => 'extended[enable_seconds]',
										'value'   => isset( $extended['enable_seconds'] ) ? $extended['enable_seconds'] : '',
										'options' => array(
											'false' => __( 'False', 'frontend-dashboard' ),
											'true'  => __( 'True', 'frontend-dashboard' ),
										),
									),
									'select'
								);
								?>
							</div>
							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Min Date', 'frontend-dashboard' ); ?></label>
								<input
									type="text"
									name="extended[min_date]"
									value="<?php echo esc_attr( isset( $extended['min_date'] ) ? $extended['min_date'] : '' ); ?>"
									placeholder="<?php esc_attr_e( 'e.g. today or 2026-01-01', 'frontend-dashboard' ); ?>"
									class="w-full text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all"
									style="min-height:38px;"
								/>
							</div>
							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Max Date', 'frontend-dashboard' ); ?></label>
								<input
									type="text"
									name="extended[max_date]"
									value="<?php echo esc_attr( isset( $extended['max_date'] ) ? $extended['max_date'] : '' ); ?>"
									placeholder="<?php esc_attr_e( 'e.g. today or 2026-12-31', 'frontend-dashboard' ); ?>"
									class="w-full text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all"
									style="min-height:38px;"
								/>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php
			fed_get_admin_up_display_permission( $row, $action );
			fed_get_admin_up_role_based( $row, $action, $menu_options );
			fed_get_input_type_and_submit_btn( 'date', $action, $row );
			?>
		</form>
	</div>
	<?php
}