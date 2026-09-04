<?php
/**
 * Helper functions.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Common Simple Layout.
 *
 * @param  array  $form  Form.
 */
function fed_common_simple_layout( $form ) {
	$form_method       = isset( $form['form']['method'] ) && ! empty( $form['form']['method'] ) ? esc_attr( $form['form']['method'] ) : 'post';
	$form_class        = isset( $form['form']['class'] ) && ! empty( $form['form']['class'] ) ? esc_attr( $form['form']['class'] ) : '';
	$form_attr         = isset( $form['form']['attr'] ) && ! empty( $form['form']['attr'] ) ? esc_attr( $form['form']['attr'] ) : '';
	$form_loader       = isset( $form['form']['is_loader'] ) && ! empty( $form['form']['attr'] ) ? esc_attr( $form['form']['attr'] ) : fed_loader();
	$form_nonce_action = isset( $form['form']['nonce']['action'] ) && ! empty( $form['form']['nonce']['action'] ) ? esc_attr( $form['form']['nonce']['action'] ) : 'fed_nonce';
	$form_nonce_name   = isset( $form['form']['nonce']['name'] ) && ! empty( $form['form']['nonce']['name'] ) ? esc_attr( $form['form']['nonce']['name'] ) : 'fed_nonce';

	if ( isset( $form['form']['action'] ) && is_array( $form['form']['action'] ) ) {
		$url         = isset( $form['form']['action']['url'] ) && ! empty( $form['form']['action']['url'] ) ? esc_url( $form['form']['action']['url'] ) : admin_url( 'admin-ajax.php' );
		$parameters  = isset( $form['form']['action']['parameters'] ) && is_array( $form['form']['action']['parameters'] ) ? http_build_query( $form['form']['action']['parameters'] ) : '';
		$form_action = isset( $form['form']['action']['action'] ) && ! empty( $form['form']['action']['action'] ) ? $url . '?action=' . esc_attr( $form['form']['action']['action'] ) . '&' . $parameters : $url;
	} else {
		$form_action = admin_url( 'admin-ajax.php?action=fed_admin_setting_form' );
	}
	?>
	<div class="space-y-6">
		<?php
		if ( isset( $form['note']['header'] ) && ! empty( $form['note']['header'] ) ) {
			echo '<div class="p-4 bg-indigo-50/60 border border-indigo-100 rounded-2xl text-xs text-indigo-800">' . esc_html( $form['note']['header'] ) . '</div>';
		}
		?>
		<form method="<?php echo esc_attr( $form_method ); ?>"
				class="<?php echo esc_attr( $form_class ); ?> space-y-6" <?php echo esc_attr( $form_attr ); ?>
				action="<?php echo esc_attr( $form_action ); ?>">

			<?php fed_wp_nonce_field( $form_nonce_action, $form_nonce_name ); ?>
			<?php echo $form_loader; ?>

			<?php
			if ( isset( $form['hidden'] ) && is_array( $form['hidden'] ) ) {
				foreach ( $form['hidden'] as $hindex => $hidden ) {
					echo fed_get_input_details( $hidden );
				}
			}
			?>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
				<?php
				if ( isset( $form['input'] ) && is_array( $form['input'] ) && count( $form['input'] ) ) {
					foreach ( $form['input'] as $iindex => $input ) {
						$is_full_width = ( isset( $input['col'] ) && strpos( $input['col'], '12' ) !== false ) || isset( $input['header'] ) || isset( $input['extra'] );
						$name          = isset( $input['name'] ) && ! empty( $input['name'] ) ? $input['name'] : $iindex;
						?>
						<div class="space-y-2 <?php echo $is_full_width ? 'md:col-span-2' : ''; ?>">
							<?php if ( isset( $input['name'] ) && null !== $input['name'] ) : ?>
								<label class="block text-xs font-bold text-slate-700">
									<?php echo esc_html( $name ); ?>
									<?php if ( ! empty( $input['required'] ) ) : ?>
										<span class="text-rose-500">*</span>
									<?php endif; ?>
								</label>
							<?php endif; ?>

							<?php if ( isset( $input['header'] ) ) : ?>
								<div class="pt-3 pb-1 border-b border-slate-100">
									<label class="block text-xs font-bold text-slate-800">
										<?php echo esc_html( $input['header'] ); ?>
									</label>
								</div>
							<?php endif; ?>

							<?php
							if ( isset( $input['input'] ) ) {
								if ( is_array( $input['input'] ) ) {
									echo fed_get_input_details( $input['input'] );
								} else {
									echo $input['input'];
								}
							}

							if ( isset( $input['extra']['input'] ) && is_array( $input['extra']['input'] ) ) {
								echo '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5 pt-2">';
								foreach ( $input['extra']['input'] as $eindex => $extra ) {
									$input_data = $extra;
									$item_label = isset( $extra['label'] ) ? $extra['label'] : $eindex;
									$input_data['label'] = '';
									?>
									<label class="p-3 bg-slate-50/80 hover:bg-slate-100/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-2 cursor-pointer transition-colors">
										<span class="text-xs font-bold text-slate-800 select-none"><?php echo esc_html( $item_label ); ?></span>
										<?php echo fed_get_input_details( $input_data ); ?>
									</label>
									<?php
								}
								echo '</div>';
							}
							?>

							<?php if ( ! empty( $input['help_message'] ) ) : ?>
								<p class="text-[11px] text-slate-400 m-0"><?php echo wp_strip_all_tags( $input['help_message'] ); ?></p>
							<?php endif; ?>
						</div>
						<?php
					}

					do_action( 'fed_admin_login_settings_template', $form );
					?>

					<div class="md:col-span-2 pt-4 border-t border-slate-100 flex items-center justify-end">
						<button type="submit" class="fed-btn-primary h-11 inline-flex items-center justify-center gap-2 px-6 rounded-xl font-semibold text-xs tracking-wide shadow-sm transition-all active:scale-95 cursor-pointer">
							<i class="fas fa-save text-xs" style="color: #ffffff !important;"></i>
							<span style="color: #ffffff !important;"><?php esc_html_e( 'Save Changes', 'frontend-dashboard' ); ?></span>
						</button>
					</div>
					<?php
				}
				?>
			</div>
		</form>
		<?php
		if ( isset( $form['note']['footer'] ) && ! empty( $form['note']['footer'] ) ) {
			echo '<div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-600">' . esc_html( $form['note']['footer'] ) . '</div>';
		}
		?>
	</div>
	<?php
}


/**
 * Common Layouts Admin Settings.
 *
 * @param  array  $fed_admin_options  Admin Options.
 * @param  array  $tabs  Tabs.
 */
function fed_common_layouts_admin_settings( $fed_admin_options, $tabs ) {
	$no = mt_rand( 1000, 9999 );
	?>
	<div class="flex flex-col lg:flex-row gap-6 items-start w-full fed-settings-subtab-container" id="fed_subtabs_wrap_<?php echo esc_attr( $no ); ?>">
		<!-- Left Subtab Sidebar -->
		<div class="w-full lg:w-64 shrink-0">
			<div class="bg-white rounded-3xl p-3 border border-slate-200/80 shadow-xs space-y-1.5" role="tablist">
				<?php
				$menu_count = 0;
				foreach ( $tabs as $index => $tab ) {
					$active = ( 0 === $menu_count );
					$menu_count ++;
					?>
					<a href="#<?php echo esc_attr( $index ); ?>"
					   data-target="#subtab_pane_<?php echo esc_attr( $index . '_' . $no ); ?>"
					   role="tab"
					   data-toggle="tab"
					   class="fed-subtab-link flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-semibold transition-all cursor-pointer no-underline <?php echo $active ? 'fed-subtab-active bg-indigo-50 border border-indigo-200 text-indigo-700 shadow-2xs font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-transparent'; ?>">
						<div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 <?php echo $active ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-500'; ?>">
							<i class="<?php echo esc_attr( $tab['icon'] ); ?>"></i>
						</div>
						<span class="truncate"><?php echo esc_html( $tab['name'] ); ?></span>
					</a>
				<?php } ?>
			</div>
		</div>

		<!-- Right Subtab Content Pane -->
		<div class="flex-1 min-w-0 w-full">
			<div class="tab-content w-full">
				<?php
				$content_count = 0;
				foreach ( $tabs as $index => $tab ) {
					$active = ( 0 === $content_count );
					$content_count ++;
					?>
					<div role="tabpanel"
						 class="tab-pane <?php echo $active ? 'active block' : 'hidden'; ?>"
						 id="<?php echo esc_attr( $index ); ?>"
						 id-full="subtab_pane_<?php echo esc_attr( $index . '_' . $no ); ?>">
						<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
							<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
								<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0">
									<i class="<?php echo esc_attr( $tab['icon'] ); ?>"></i>
								</div>
								<div>
									<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php echo esc_html( $tab['name'] ); ?></h3>
									<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Configure options and preferences.', 'frontend-dashboard' ); ?></p>
								</div>
							</div>

							<div>
								<?php
								fed_call_function_method( $tab );
								?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
}
