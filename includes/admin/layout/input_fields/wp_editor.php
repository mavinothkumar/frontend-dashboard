<?php
/**
 * WP Editor Inspector Layout.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Editor Field Inspector.
 *
 * @param array  $row
 * @param string $action
 * @param array  $menu_options
 */
function fed_admin_input_fields_wp_editor( $row, $action, $menu_options ) {
	$is_active = ( isset( $row['input_type'] ) && 'wp_editor' === $row['input_type'] );
	?>
	<div class="fed_input_type_container fed_input_wp_editor_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="wp_editor">
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
						<i class="fas fa-edit"></i>
					</div>
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'WP Editor (Rich Text)', 'frontend-dashboard' ); ?></h3>
						<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'WordPress visual and HTML rich text WYSIWYG editor.', 'frontend-dashboard' ); ?></p>
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

					<!-- Editor Configurations -->
					<div class="pt-4 border-t border-slate-100 space-y-3">
						<h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2"><?php esc_html_e( 'Editor Configurations', 'frontend-dashboard' ); ?></h4>
						<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
							<div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3">
								<div>
									<span class="block text-xs font-bold text-slate-800"><?php esc_html_e( 'Media Upload', 'frontend-dashboard' ); ?></span>
									<span class="text-[11px] text-slate-400"><?php esc_html_e( 'Add media button', 'frontend-dashboard' ); ?></span>
								</div>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo fed_input_box(
									'extended[settings][media_buttons]',
									array(
										'default_value' => 'true',
										'value'         => fed_get_data( 'extended.settings.media_buttons', $row, 'true' ),
									),
									'checkbox'
								);
								?>
							</div>

							<div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3">
								<div>
									<span class="block text-xs font-bold text-slate-800"><?php esc_html_e( 'Quicktags', 'frontend-dashboard' ); ?></span>
									<span class="text-[11px] text-slate-400"><?php esc_html_e( 'HTML formatting tags', 'frontend-dashboard' ); ?></span>
								</div>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo fed_input_box(
									'extended[settings][quicktags]',
									array(
										'default_value' => 'true',
										'value'         => fed_get_data( 'extended.settings.quicktags', $row, 'true' ),
									),
									'checkbox'
								);
								?>
							</div>

							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Textarea Rows', 'frontend-dashboard' ); ?></label>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo fed_input_box(
									'extended[settings][textarea_rows]',
									array(
										'value' => fed_get_data( 'extended.settings.textarea_rows', $row, 10 ),
									),
									'number'
								);
								?>
							</div>

							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Editor Height (px)', 'frontend-dashboard' ); ?></label>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo fed_input_box(
									'extended[settings][editor_height]',
									array(
										'value' => fed_get_data( 'extended.settings.editor_height', $row, 250 ),
									),
									'number'
								);
								?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php
			fed_get_admin_up_display_permission( $row, $action );
			fed_get_admin_up_role_based( $row, $action, $menu_options );
			fed_get_input_type_and_submit_btn( 'wp_editor', $action, $row );
			?>
		</form>
	</div>
	<?php
}