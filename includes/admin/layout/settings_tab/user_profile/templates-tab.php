<?php
/**
 * Dashboard Templates Selector Tab.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Dashboard Templates Switcher Tab
 *
 * @param array $fed_admin_options
 */
function fed_admin_user_profile_templates_tab( $fed_admin_options = array() ) {
	$template_manager = \FED\Services\Templates\TemplateManager::instance();
	$registered       = $template_manager->get_registered_templates();
	$active_id        = $template_manager->get_active_template_id();
	$nonce            = wp_create_nonce( 'fed_nonce' );
	?>
	<div class="bc_fed p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
		<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
			<div>
				<h3 class="text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Dashboard Layout Templates', 'frontend-dashboard' ); ?></h3>
				<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Select the visual application shell for your frontend dashboard. Extend with Pro layouts easily.', 'frontend-dashboard' ); ?></p>
			</div>
			<div class="flex items-center gap-2">
				<span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
					<?php echo count( $registered ); ?> <?php esc_html_e( 'Available', 'frontend-dashboard' ); ?>
				</span>
			</div>
		</div>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ); ?>" class="fed_admin_menu fed_ajax">
			<input type="hidden" name="fed_admin_unique" value="fed_admin_setting_upl" />
			<input type="hidden" name="fed_nonce" value="<?php echo esc_attr( $nonce ); ?>" />

			<!-- Hidden inputs to preserve other UPL settings -->
			<?php if ( isset( $fed_admin_options['settings'] ) && is_array( $fed_admin_options['settings'] ) ) : ?>
				<?php foreach ( $fed_admin_options['settings'] as $k => $v ) : ?>
					<?php if ( 'fed_upl_template_model' !== $k ) : ?>
						<input type="hidden" name="settings[<?php echo esc_attr( $k ); ?>]" value="<?php echo esc_attr( $v ); ?>" />
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>

			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				<?php foreach ( $registered as $tpl_id => $tpl ) : ?>
					<?php
					$is_active = ( $tpl_id === $active_id );
					$is_pro    = ! empty( $tpl['is_pro'] );
					?>
					<div class="group relative rounded-2xl border-2 transition-all overflow-hidden flex flex-col justify-between <?php echo $is_active ? 'border-indigo-600 bg-indigo-50/10 shadow-sm' : 'border-slate-200/90 bg-white hover:border-slate-300'; ?>">
						<div>
							<!-- Template Preview Header / Thumbnail -->
							<div class="h-44 bg-slate-100 relative overflow-hidden flex items-center justify-center border-b border-slate-100">
								<?php if ( ! empty( $tpl['thumbnail'] ) && file_exists( $tpl['thumbnail'] ) ) : ?>
									<img src="<?php echo esc_url( $tpl['thumbnail'] ); ?>" alt="<?php echo esc_attr( $tpl['name'] ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
								<?php else : ?>
									<div class="w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-3xl">
										<i class="fas fa-desktop"></i>
									</div>
								<?php endif; ?>

								<div class="absolute top-3 right-3 flex items-center gap-1.5">
									<?php if ( $is_active ) : ?>
										<span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500 text-white shadow-xs">
											<i class="fas fa-check-circle mr-1"></i> <?php esc_html_e( 'Active', 'frontend-dashboard' ); ?>
										</span>
									<?php elseif ( $is_pro ) : ?>
										<span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-600 text-white shadow-xs">
											<i class="fas fa-crown mr-1"></i> <?php esc_html_e( 'Pro', 'frontend-dashboard' ); ?>
										</span>
									<?php else : ?>
										<span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white text-slate-700 shadow-xs border border-slate-200">
											<?php echo esc_html( $tpl['badge'] ?? __( 'Standard', 'frontend-dashboard' ) ); ?>
										</span>
									<?php endif; ?>
								</div>
							</div>

							<!-- Template Info -->
							<div class="p-5">
								<h4 class="text-sm font-bold text-slate-900 m-0 mb-1.5"><?php echo esc_html( $tpl['name'] ); ?></h4>
								<p class="text-xs text-slate-500 leading-relaxed m-0"><?php echo esc_html( $tpl['description'] ); ?></p>
							</div>
						</div>

						<!-- Action Footer -->
						<div class="p-5 pt-0">
							<label class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold transition-all cursor-pointer select-none <?php echo $is_active ? 'bg-indigo-600 text-white shadow-sm pointer-events-none' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200'; ?>">
								<input type="radio" name="settings[fed_upl_template_model]" value="<?php echo esc_attr( $tpl_id ); ?>" class="sr-only" <?php checked( $is_active ); ?> onchange="this.form.submit();" />
								<span><?php echo esc_html( $is_active ? __( 'Currently Active', 'frontend-dashboard' ) : __( 'Activate Template', 'frontend-dashboard' ) ); ?></span>
							</label>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</form>
	</div>
	<?php
}