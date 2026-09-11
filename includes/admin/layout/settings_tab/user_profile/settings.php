<?php
/**
 * User Profile Settings.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User Profile Settings.
 *
 * @param  array $fed_admin_options  Admin Options.
 */
function fed_admin_user_profile_settings_tab( $fed_admin_options ) {
	$fed_upef = array_merge(
		fed_fetch_user_profile_extra_fields_key_value(),
		array( '' => __( 'Let it be default', 'frontend-dashboard' ) )
	);

	$array = array(
		'form'   => array(
			'method' => '',
			'class'  => 'fed_admin_menu fed_ajax',
			'attr'   => '',
			'action' => array(
				'url'    => '',
				'action' => 'fed_admin_setting_form',
			),
			'nonce'  => array(
				'action' => '',
				'name'   => '',
			),
			'loader' => '',
		),
		'hidden' => array(
			'fed_admin_unique' => array(
				'input_type' => 'hidden',
				'user_value' => 'fed_admin_setting_upl',
				'input_meta' => 'fed_admin_unique',
			),
		),
		'input'  => array(
			'Website Logo'                  => array(
				'col'          => 'col-md-12',
				'name'         => __( 'Dashboard Brand Logo', 'frontend-dashboard' ),
				'input'        => fed_get_input_details(
					array(
						'input_meta' => 'settings[fed_upl_website_logo]',
						'user_value' => isset( $fed_admin_options['settings']['fed_upl_website_logo'] ) ? $fed_admin_options['settings']['fed_upl_website_logo'] : null,
						'input_type' => 'file',
					)
				),
				'help_message' => fed_show_help_message( array(
					'content' => __( 'Upload custom brand logo to display in the dashboard sidebar/header canvas', 'frontend-dashboard' ),
				) ),
			),
			'Website Logo Width'            => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Logo Width (px)', 'frontend-dashboard' ),
				'input' => fed_get_input_details(
					array(
						'placeholder' => __( 'e.g. 160 (optional)', 'frontend-dashboard' ),
						'input_meta'  => 'settings[fed_upl_website_logo_width]',
						'user_value'  => isset( $fed_admin_options['settings']['fed_upl_website_logo_width'] ) ? $fed_admin_options['settings']['fed_upl_website_logo_width'] : '',
						'input_type'  => 'number',
					)
				),
			),
			'Website Logo Height'           => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Logo Height (px)', 'frontend-dashboard' ),
				'input' => fed_get_input_details(
					array(
						'placeholder' => __( 'e.g. 40 (optional)', 'frontend-dashboard' ),
						'input_meta'  => 'settings[fed_upl_website_logo_height]',
						'user_value'  => isset( $fed_admin_options['settings']['fed_upl_website_logo_height'] ) ? $fed_admin_options['settings']['fed_upl_website_logo_height'] : '',
						'input_type'  => 'number',
					)
				),
			),
			'Change Profile Picture'        => array(
				'col'          => 'col-md-6',
				'name'         => __( 'Change Profile Picture', 'frontend-dashboard' ),
				'input'        => fed_get_input_details(
					array(
						'input_meta'  => 'settings[fed_upl_change_profile_pic]',
						'input_value' => $fed_upef,
						'user_value'  => isset( $fed_admin_options['settings']['fed_upl_change_profile_pic'] ) ? $fed_admin_options['settings']['fed_upl_change_profile_pic'] : '',
						'input_type'  => 'select',
					)
				),
				'help_message' => fed_show_help_message( array(
					'content' => __( 'Image size should be min 600x600 px', 'frontend-dashboard' ),
				) ),
			),
			'Disable Description'           => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Disable Description', 'frontend-dashboard' ),
				'input' => fed_get_input_details(
					array(
						'input_value' => fed_yes_no( 'ASC' ),
						'input_meta'  => 'settings[fed_upl_disable_desc]',
						'user_value'  => isset( $fed_admin_options['settings']['fed_upl_disable_desc'] ) ? $fed_admin_options['settings']['fed_upl_disable_desc'] : '',
						'input_type'  => 'select',
					)
				),
			),
			'Number of Recent Post to show' => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Number of Recent Post to show', 'frontend-dashboard' ),
				'input' => fed_get_input_details(
					array(
						'placeholder' => __( 'Number of Recent Post to show on User Profile', 'frontend-dashboard' ),
						'input_meta'  => 'settings[fed_upl_no_recent_post]',
						'user_value'  => isset( $fed_admin_options['settings']['fed_upl_no_recent_post'] ) ? $fed_admin_options['settings']['fed_upl_no_recent_post'] : '5',
						'input_type'  => 'number',
					)
				),
			),
			'Collapse Menu Always'          => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Collapse Menu Always', 'frontend-dashboard' ),
				'input' => fed_get_input_details(
					array(
						'input_value' => fed_yes_no( 'ASC' ),
						'input_meta'  => 'settings[fed_upl_collapse_menu]',
						'user_value'  => isset( $fed_admin_options['settings']['fed_upl_collapse_menu'] ) ? $fed_admin_options['settings']['fed_upl_collapse_menu'] : '',
						'input_type'  => 'select',
					)
				),
			),
			'Disable Logout'                => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Disable Logout', 'frontend-dashboard' ),
				'input' => fed_get_input_details(
					array(
						'input_value' => fed_yes_no( 'ASC' ),
						'input_meta'  => 'settings[fed_upl_disable_logout]',
						'user_value'  => isset( $fed_admin_options['settings']['fed_upl_disable_logout'] ) ? $fed_admin_options['settings']['fed_upl_disable_logout'] : '',
						'input_type'  => 'select',
					)
				),
			),
			'Disable Collapse Menu'         => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Disable Collapse Menu', 'frontend-dashboard' ),
				'input' => fed_get_input_details(
					array(
						'input_value' => fed_yes_no( 'ASC' ),
						'input_meta'  => 'settings[fed_upl_disable_collapse_menu]',
						'user_value'  => isset( $fed_admin_options['settings']['fed_upl_disable_collapse_menu'] ) ? $fed_admin_options['settings']['fed_upl_disable_collapse_menu'] : '',
						'input_type'  => 'select',
					)
				),
			),
		),
	);

	$new_value = apply_filters( 'fed_admin_upl_settings_template', $array, $fed_admin_options );

	fed_common_simple_layout( $new_value );
}

function fed_admin_user_profile_colors_tab() {
	if ( defined( 'BC_FED_EXTRA_PLUGIN_VERSION' ) ) {
		$fed_admin_options = get_option( 'fed_admin_setting_upl_color' );
		$colors = isset( $fed_admin_options['color'] ) && is_array( $fed_admin_options['color'] ) ? $fed_admin_options['color'] : array();

		// Default enterprise values
		$c_bg_color     = ! empty( $colors['fed_upl_color_bg_color'] ) ? $colors['fed_upl_color_bg_color'] : '#4F46E5';
		$c_bg_font      = ! empty( $colors['fed_upl_color_bg_font_color'] ) ? $colors['fed_upl_color_bg_font_color'] : '#FFFFFF';
		$c_sbg_color    = ! empty( $colors['fed_upl_color_sbg_color'] ) ? $colors['fed_upl_color_sbg_color'] : '#06B6D4';
		$c_sbg_font     = ! empty( $colors['fed_upl_color_sbg_font_color'] ) ? $colors['fed_upl_color_sbg_font_color'] : '#FFFFFF';
		$c_sidebar_bg   = ! empty( $colors['fed_upl_color_sidebar_bg'] ) ? $colors['fed_upl_color_sidebar_bg'] : '#FFFFFF';
		$c_sidebar_text = ! empty( $colors['fed_upl_color_sidebar_text'] ) ? $colors['fed_upl_color_sidebar_text'] : '#64748B';
		$c_active_bg    = ! empty( $colors['fed_upl_color_active_bg'] ) ? $colors['fed_upl_color_active_bg'] : '#EEF2FF';
		$c_active_text  = ! empty( $colors['fed_upl_color_active_text'] ) ? $colors['fed_upl_color_active_text'] : '#4F46E5';
		$c_body_bg      = ! empty( $colors['fed_upl_color_body_bg'] ) ? $colors['fed_upl_color_body_bg'] : '#F8FAFC';
		$c_card_bg      = ! empty( $colors['fed_upl_color_card_bg'] ) ? $colors['fed_upl_color_card_bg'] : '#FFFFFF';
		$c_text_main    = ! empty( $colors['fed_upl_color_text_main'] ) ? $colors['fed_upl_color_text_main'] : '#0F172A';
		$c_border       = ! empty( $colors['fed_upl_color_border'] ) ? $colors['fed_upl_color_border'] : '#E2E8F0';

		$presets = array(
			'indigo'    => array(
				'name'    => __( 'Indigo Modern', 'frontend-dashboard' ),
				'desc'    => __( 'Clean tech default', 'frontend-dashboard' ),
				'badge'   => '#4F46E5',
				'colors'  => array(
					'fed_upl_color_bg_color'        => '#4F46E5',
					'fed_upl_color_bg_font_color'   => '#FFFFFF',
					'fed_upl_color_sbg_color'       => '#06B6D4',
					'fed_upl_color_sbg_font_color'  => '#FFFFFF',
					'fed_upl_color_sidebar_bg'      => '#FFFFFF',
					'fed_upl_color_sidebar_text'    => '#64748B',
					'fed_upl_color_active_bg'       => '#EEF2FF',
					'fed_upl_color_active_text'     => '#4F46E5',
					'fed_upl_color_body_bg'         => '#F8FAFC',
					'fed_upl_color_card_bg'         => '#FFFFFF',
					'fed_upl_color_text_main'       => '#0F172A',
					'fed_upl_color_border'          => '#E2E8F0',
				),
			),
			'slate'     => array(
				'name'    => __( 'Slate Executive', 'frontend-dashboard' ),
				'desc'    => __( 'Corporate navy & slate', 'frontend-dashboard' ),
				'badge'   => '#0F172A',
				'colors'  => array(
					'fed_upl_color_bg_color'        => '#2563EB',
					'fed_upl_color_bg_font_color'   => '#FFFFFF',
					'fed_upl_color_sbg_color'       => '#38BDF8',
					'fed_upl_color_sbg_font_color'  => '#FFFFFF',
					'fed_upl_color_sidebar_bg'      => '#0F172A',
					'fed_upl_color_sidebar_text'    => '#94A3B8',
					'fed_upl_color_active_bg'       => '#1E293B',
					'fed_upl_color_active_text'     => '#38BDF8',
					'fed_upl_color_body_bg'         => '#F1F5F9',
					'fed_upl_color_card_bg'         => '#FFFFFF',
					'fed_upl_color_text_main'       => '#0F172A',
					'fed_upl_color_border'          => '#CBD5E1',
				),
			),
			'emerald'   => array(
				'name'    => __( 'Emerald FinTech', 'frontend-dashboard' ),
				'desc'    => __( 'High-trust banking green', 'frontend-dashboard' ),
				'badge'   => '#059669',
				'colors'  => array(
					'fed_upl_color_bg_color'        => '#059669',
					'fed_upl_color_bg_font_color'   => '#FFFFFF',
					'fed_upl_color_sbg_color'       => '#10B981',
					'fed_upl_color_sbg_font_color'  => '#FFFFFF',
					'fed_upl_color_sidebar_bg'      => '#064E3B',
					'fed_upl_color_sidebar_text'    => '#A7F3D0',
					'fed_upl_color_active_bg'       => '#047857',
					'fed_upl_color_active_text'     => '#FFFFFF',
					'fed_upl_color_body_bg'         => '#F0FDF4',
					'fed_upl_color_card_bg'         => '#FFFFFF',
					'fed_upl_color_text_main'       => '#064E3B',
					'fed_upl_color_border'          => '#D1FAE5',
				),
			),
			'midnight'  => array(
				'name'    => __( 'Midnight SaaS', 'frontend-dashboard' ),
				'desc'    => __( 'Deep dark workspace', 'frontend-dashboard' ),
				'badge'   => '#1E1B4B',
				'colors'  => array(
					'fed_upl_color_bg_color'        => '#6366F1',
					'fed_upl_color_bg_font_color'   => '#FFFFFF',
					'fed_upl_color_sbg_color'       => '#8B5CF6',
					'fed_upl_color_sbg_font_color'  => '#FFFFFF',
					'fed_upl_color_sidebar_bg'      => '#111827',
					'fed_upl_color_sidebar_text'    => '#9CA3AF',
					'fed_upl_color_active_bg'       => '#1F2937',
					'fed_upl_color_active_text'     => '#A5B4FC',
					'fed_upl_color_body_bg'         => '#030712',
					'fed_upl_color_card_bg'         => '#111827',
					'fed_upl_color_text_main'       => '#F9FAFB',
					'fed_upl_color_border'          => '#1F2937',
				),
			),
			'violet'    => array(
				'name'    => __( 'Royal Violet', 'frontend-dashboard' ),
				'desc'    => __( 'Modern creator & luxury', 'frontend-dashboard' ),
				'badge'   => '#7C3AED',
				'colors'  => array(
					'fed_upl_color_bg_color'        => '#7C3AED',
					'fed_upl_color_bg_font_color'   => '#FFFFFF',
					'fed_upl_color_sbg_color'       => '#EC4899',
					'fed_upl_color_sbg_font_color'  => '#FFFFFF',
					'fed_upl_color_sidebar_bg'      => '#2E1065',
					'fed_upl_color_sidebar_text'    => '#DDD6FE',
					'fed_upl_color_active_bg'       => '#4C1D95',
					'fed_upl_color_active_text'     => '#F472B6',
					'fed_upl_color_body_bg'         => '#FAF5FF',
					'fed_upl_color_card_bg'         => '#FFFFFF',
					'fed_upl_color_text_main'       => '#3B0764',
					'fed_upl_color_border'          => '#EDE9FE',
				),
			),
			'amber'     => array(
				'name'    => __( 'Sunset Amber', 'frontend-dashboard' ),
				'desc'    => __( 'Warm energetic dashboard', 'frontend-dashboard' ),
				'badge'   => '#D97706',
				'colors'  => array(
					'fed_upl_color_bg_color'        => '#D97706',
					'fed_upl_color_bg_font_color'   => '#FFFFFF',
					'fed_upl_color_sbg_color'       => '#F97316',
					'fed_upl_color_sbg_font_color'  => '#FFFFFF',
					'fed_upl_color_sidebar_bg'      => '#78350F',
					'fed_upl_color_sidebar_text'    => '#FDE68A',
					'fed_upl_color_active_bg'       => '#92400E',
					'fed_upl_color_active_text'     => '#FFFFFF',
					'fed_upl_color_body_bg'         => '#FFFBEB',
					'fed_upl_color_card_bg'         => '#FFFFFF',
					'fed_upl_color_text_main'       => '#451A03',
					'fed_upl_color_border'          => '#FEF3C7',
				),
			),
			'teal'      => array(
				'name'    => __( 'Teal Clean Pro', 'frontend-dashboard' ),
				'desc'    => __( 'Medical & analytics clarity', 'frontend-dashboard' ),
				'badge'   => '#0D9488',
				'colors'  => array(
					'fed_upl_color_bg_color'        => '#0D9488',
					'fed_upl_color_bg_font_color'   => '#FFFFFF',
					'fed_upl_color_sbg_color'       => '#06B6D4',
					'fed_upl_color_sbg_font_color'  => '#FFFFFF',
					'fed_upl_color_sidebar_bg'      => '#134E4A',
					'fed_upl_color_sidebar_text'    => '#99F6E4',
					'fed_upl_color_active_bg'       => '#115E59',
					'fed_upl_color_active_text'     => '#5EEAD4',
					'fed_upl_color_body_bg'         => '#F0FDFA',
					'fed_upl_color_card_bg'         => '#FFFFFF',
					'fed_upl_color_text_main'       => '#134E4A',
					'fed_upl_color_border'          => '#CCFBF1',
				),
			),
		);
		?>
		<div class="space-y-8" id="fed_theme_customizer_wrap">
			<!-- Header & Description Banner -->
			<div class="p-6 bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-950 rounded-3xl text-white shadow-xl relative overflow-hidden">
				<div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
				<div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
					<div>
						<div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-indigo-200 backdrop-blur-md mb-2">
							<i class="fas fa-palette text-indigo-400"></i> <?php esc_html_e( 'Enterprise Theme Customizer', 'frontend-dashboard' ); ?>
						</div>
						<h2 class="text-xl md:text-2xl font-black tracking-tight text-white"><?php esc_html_e( 'Dashboard Color & Branding Engine', 'frontend-dashboard' ); ?></h2>
						<p class="text-xs md:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
							<?php esc_html_e( 'Customize every layer of your Frontend Dashboard — from sidebar navigation, brand primary buttons, and accents to page canvas and container cards. Select a 1-click curated preset or fine-tune individual colors.', 'frontend-dashboard' ); ?>
						</p>
					</div>
					<div class="flex items-center gap-2">
						<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold rounded-xl">
							<span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
							<?php esc_html_e( 'Real-time Live Sync', 'frontend-dashboard' ); ?>
						</span>
					</div>
				</div>
			</div>

			<!-- 1-Click Curated Presets Bar -->
			<div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
				<div class="flex items-center justify-between mb-4">
					<div>
						<h3 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
							<i class="fas fa-magic text-indigo-600"></i> <?php esc_html_e( '1-Click Enterprise Presets', 'frontend-dashboard' ); ?>
						</h3>
						<p class="text-xs text-slate-500 mt-0.5"><?php esc_html_e( 'Select an expertly crafted color scheme to apply across your entire dashboard instantly.', 'frontend-dashboard' ); ?></p>
					</div>
				</div>
				<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">
					<?php foreach ( $presets as $pkey => $preset ) : ?>
						<button type="button"
						        class="fed-preset-btn flex flex-col items-start text-left p-3.5 rounded-2xl border border-slate-200/90 bg-slate-50/50 hover:bg-indigo-50/60 hover:border-indigo-300 transition-all duration-200 group relative cursor-pointer"
						        data-preset="<?php echo esc_attr( wp_json_encode( $preset['colors'] ) ); ?>">
							<div class="flex items-center gap-1.5 w-full mb-2">
								<span class="w-3.5 h-3.5 rounded-full shadow-xs border border-white shrink-0" style="background-color: <?php echo esc_attr( $preset['badge'] ); ?>;"></span>
								<span class="w-3.5 h-3.5 rounded-full shadow-xs border border-white shrink-0" style="background-color: <?php echo esc_attr( $preset['colors']['fed_upl_color_sbg_color'] ); ?>;"></span>
								<span class="w-3.5 h-3.5 rounded-full shadow-xs border border-white shrink-0" style="background-color: <?php echo esc_attr( $preset['colors']['fed_upl_color_sidebar_bg'] ); ?>;"></span>
							</div>
							<span class="text-xs font-black text-slate-800 group-hover:text-indigo-600 leading-tight"><?php echo esc_html( $preset['name'] ); ?></span>
							<span class="text-[10px] text-slate-600 mt-0.5 leading-tight"><?php echo esc_html( $preset['desc'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Form & Live Preview Grid -->
			<form method="post"
			      class="fed_admin_menu fed_ajax space-y-6"
			      action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ); ?>"
			      id="fed_color_customizer_form">

				<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
				<?php echo fed_loader(); ?>
				<input type="hidden" name="fed_admin_unique" value="fed_admin_setting_upl_color">

				<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
					<!-- Color Controls Column (7 Cols) -->
					<div class="lg:col-span-7 space-y-6">

						<!-- Section 1: Brand & Primary Actions -->
						<div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
							<div class="flex items-center gap-2.5 pb-4 mb-5 border-b border-slate-100">
								<div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
									<i class="fa fa-magic fas fa-magic"></i>
								</div>
								<div>
									<h4 class="text-sm font-black text-slate-900"><?php esc_html_e( 'Brand & Primary Actions', 'frontend-dashboard' ); ?></h4>
									<p class="text-xs text-slate-600"><?php esc_html_e( 'Buttons, badges, highlights, and primary actionable elements.', 'frontend-dashboard' ); ?></p>
								</div>
							</div>

							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Primary Button / Brand Accent', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_bg_color]',
										'user_value' => $c_bg_color,
										'id_name'    => 'color_fed_upl_color_bg_color',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Main submit buttons, active badges & pills.', 'frontend-dashboard' ); ?></span>
								</div>

								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Primary Button Text', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_bg_font_color]',
										'user_value' => $c_bg_font,
										'id_name'    => 'color_fed_upl_color_bg_font_color',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Label text color on primary buttons.', 'frontend-dashboard' ); ?></span>
								</div>

								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Secondary / Accent Color', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_sbg_color]',
										'user_value' => $c_sbg_color,
										'id_name'    => 'color_fed_upl_color_sbg_color',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Secondary buttons, hover highlights & links.', 'frontend-dashboard' ); ?></span>
								</div>

								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Secondary Button Text', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_sbg_font_color]',
										'user_value' => $c_sbg_font,
										'id_name'    => 'color_fed_upl_color_sbg_font_color',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Label text on secondary action buttons.', 'frontend-dashboard' ); ?></span>
								</div>
							</div>
						</div>

						<!-- Section 2: Navigation & Sidebar -->
						<div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
							<div class="flex items-center gap-2.5 pb-4 mb-5 border-b border-slate-100">
								<div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm">
									<i class="fa fa-bars fas fa-bars"></i>
								</div>
								<div>
									<h4 class="text-sm font-black text-slate-900"><?php esc_html_e( 'Navigation & Sidebar Shell', 'frontend-dashboard' ); ?></h4>
									<p class="text-xs text-slate-600"><?php esc_html_e( 'Sidebar background, inactive items, and active tab highlights.', 'frontend-dashboard' ); ?></p>
								</div>
							</div>

							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Sidebar Background', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_sidebar_bg]',
										'user_value' => $c_sidebar_bg,
										'id_name'    => 'color_fed_upl_color_sidebar_bg',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Sidebar menu background surface.', 'frontend-dashboard' ); ?></span>
								</div>

								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Sidebar Inactive Item Text', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_sidebar_text]',
										'user_value' => $c_sidebar_text,
										'id_name'    => 'color_fed_upl_color_sidebar_text',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Text & icon color of inactive nav items.', 'frontend-dashboard' ); ?></span>
								</div>

								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Active Tab Background', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_active_bg]',
										'user_value' => $c_active_bg,
										'id_name'    => 'color_fed_upl_color_active_bg',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Active menu item pill background.', 'frontend-dashboard' ); ?></span>
								</div>

								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Active Tab Text / Icon', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_active_text]',
										'user_value' => $c_active_text,
										'id_name'    => 'color_fed_upl_color_active_text',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Active menu item label & icon color.', 'frontend-dashboard' ); ?></span>
								</div>
							</div>
						</div>

						<!-- Section 3: Canvas & Surfaces -->
						<div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
							<div class="flex items-center gap-2.5 pb-4 mb-5 border-b border-slate-100">
								<div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold text-sm">
									<i class="fa fa-th-large fas fa-layer-group"></i>
								</div>
								<div>
									<h4 class="text-sm font-black text-slate-900"><?php esc_html_e( 'Canvas, Cards & Typography', 'frontend-dashboard' ); ?></h4>
									<p class="text-xs text-slate-600"><?php esc_html_e( 'Page canvas backdrop, content card surfaces, text, and dividers.', 'frontend-dashboard' ); ?></p>
								</div>
							</div>

							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Dashboard Body Background', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_body_bg]',
										'user_value' => $c_body_bg,
										'id_name'    => 'color_fed_upl_color_body_bg',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Overall page canvas backdrop.', 'frontend-dashboard' ); ?></span>
								</div>

								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Content Card Background', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_card_bg]',
										'user_value' => $c_card_bg,
										'id_name'    => 'color_fed_upl_color_card_bg',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Dashboard cards and panels surface.', 'frontend-dashboard' ); ?></span>
								</div>

								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Main Text & Headings', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_text_main]',
										'user_value' => $c_text_main,
										'id_name'    => 'color_fed_upl_color_text_main',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Primary text and header typography color.', 'frontend-dashboard' ); ?></span>
								</div>

								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e( 'Borders & Dividers', 'frontend-dashboard' ); ?></label>
									<?php
									echo fed_form_color( array(
										'input_meta' => 'color[fed_upl_color_border]',
										'user_value' => $c_border,
										'id_name'    => 'color_fed_upl_color_border',
									) );
									?>
									<span class="text-[11px] text-slate-600 mt-1 block"><?php esc_html_e( 'Card borders, table dividers & inputs.', 'frontend-dashboard' ); ?></span>
								</div>
							</div>
						</div>

						<!-- Action Button -->
						<div class="flex items-center gap-3 pt-2">
							<button type="submit"
							        class="fed-submit-btn inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer">
								<i class="fa fa-check fas fa-check"></i> <?php esc_html_e( 'Save Dashboard Theme', 'frontend-dashboard' ); ?>
							</button>
						</div>

					</div>

					<!-- Live Miniature Dashboard Preview Column (5 Cols) -->
					<div class="lg:col-span-5">
						<div class="sticky top-6 space-y-4">
							<div class="flex items-center justify-between">
								<h4 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
									<i class="fa fa-desktop fas fa-desktop text-indigo-600"></i> <?php esc_html_e( 'Live Real-Time Preview', 'frontend-dashboard' ); ?>
								</h4>
								<span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">
									<?php esc_html_e( 'Instant Mockup', 'frontend-dashboard' ); ?>
								</span>
							</div>

							<!-- Mini Dashboard Frame -->
							<div id="fed_mini_preview"
							     class="rounded-3xl border shadow-xl overflow-hidden transition-all duration-300"
							     style="background-color: <?php echo esc_attr( $c_body_bg ); ?>; border-color: <?php echo esc_attr( $c_border ); ?>;">
								
								<!-- Window Top Bar -->
								<div class="px-4 py-2.5 flex items-center justify-between border-b"
								     style="background-color: <?php echo esc_attr( $c_card_bg ); ?>; border-color: <?php echo esc_attr( $c_border ); ?>;">
									<div class="flex items-center gap-1.5">
										<span class="w-2.5 h-2.5 rounded-full bg-rose-400 inline-block"></span>
										<span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>
										<span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span>
									</div>
									<div class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md"
									     style="background-color: <?php echo esc_attr( $c_body_bg ); ?>; color: <?php echo esc_attr( $c_text_main ); ?>;">
										my-site.com/dashboard
									</div>
									<div class="w-10"></div>
								</div>

								<!-- Dashboard Split Body -->
								<div class="flex min-h-[340px]">
									<!-- Mini Sidebar -->
									<div id="preview_sidebar"
									     class="w-36 p-3 flex flex-col justify-between border-r shrink-0 transition-colors"
									     style="background-color: <?php echo esc_attr( $c_sidebar_bg ); ?>; border-color: <?php echo esc_attr( $c_border ); ?>;">
										<div class="space-y-3">
											<!-- Logo Area -->
											<div class="flex items-center gap-2 pb-2 border-b" style="border-color: <?php echo esc_attr( $c_border ); ?>;">
												<div class="w-6 h-6 rounded-lg flex items-center justify-center font-bold text-xs shadow-xs"
												     id="preview_logo_icon"
												     style="background-color: <?php echo esc_attr( $c_bg_color ); ?>; color: <?php echo esc_attr( $c_bg_font ); ?>;">
													<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
												</div>
												<span class="text-xs font-black" id="preview_logo_text" style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">FED 3.0</span>
											</div>

											<!-- Nav Menu -->
											<div class="space-y-1">
												<!-- Active Item -->
												<div id="preview_nav_active"
												     class="px-2.5 py-1.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-colors shadow-xs"
												     style="background-color: <?php echo esc_attr( $c_active_bg ); ?>; color: <?php echo esc_attr( $c_active_text ); ?>;">
													<svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/></svg>
													<span>Overview</span>
												</div>

												<!-- Inactive Items -->
												<div class="preview_nav_inactive px-2.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-colors"
												     style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">
													<svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
													<span>Posts</span>
												</div>
												<div class="preview_nav_inactive px-2.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-colors"
												     style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">
													<svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
													<span>Profile</span>
												</div>
												<div class="preview_nav_inactive px-2.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-colors"
												     style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">
													<svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
													<span>Settings</span>
												</div>
											</div>
										</div>

										<!-- Mini User Info -->
										<div class="flex items-center gap-2 pt-2 border-t" style="border-color: <?php echo esc_attr( $c_border ); ?>;">
											<div class="w-6 h-6 rounded-full bg-slate-300 flex items-center justify-center text-[10px] font-bold text-slate-700">A</div>
											<div class="overflow-hidden">
												<div class="text-[10px] font-bold truncate" id="preview_user_name" style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">Admin</div>
												<div class="text-[9px] truncate" id="preview_user_role" style="color: <?php echo esc_attr( $c_sidebar_text ); ?>; opacity: 0.75;">Pro Member</div>
											</div>
										</div>
									</div>

									<!-- Mini Main Canvas -->
									<div class="flex-1 p-4 space-y-3.5 overflow-hidden">
										<!-- Header & Action Row -->
										<div class="flex items-center justify-between gap-2">
											<div>
												<h5 class="text-xs font-black" id="preview_heading" style="color: <?php echo esc_attr( $c_text_main ); ?>;">Analytics Overview</h5>
												<p class="text-[10px]" id="preview_subheading" style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">Real-time dashboard</p>
											</div>
											<button type="button"
											        id="preview_primary_btn"
											        class="px-2.5 py-1 rounded-xl text-[10px] font-black inline-flex items-center gap-1 shadow-xs transition-colors pointer-events-none"
											        style="background-color: <?php echo esc_attr( $c_bg_color ); ?>; color: <?php echo esc_attr( $c_bg_font ); ?>;">
												<svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
												New Post
											</button>
										</div>

										<!-- Stat Widgets -->
										<div class="grid grid-cols-2 gap-2">
											<div class="preview_card p-2.5 rounded-2xl border shadow-2xs transition-colors"
											     style="background-color: <?php echo esc_attr( $c_card_bg ); ?>; border-color: <?php echo esc_attr( $c_border ); ?>;">
												<div class="preview_card_label text-[9px] uppercase font-bold tracking-wider" style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">Total Views</div>
												<div class="preview_card_num text-sm font-black mt-0.5" style="color: <?php echo esc_attr( $c_text_main ); ?>;">28,490</div>
												<div class="text-[9px] font-bold mt-1 inline-block px-1.5 py-0.5 rounded-md shadow-2xs"
												     id="preview_accent_badge"
												     style="background-color: <?php echo esc_attr( $c_sbg_color ); ?>; color: <?php echo esc_attr( $c_sbg_font ); ?>;">
													+14.2%
												</div>
											</div>

											<div class="preview_card p-2.5 rounded-2xl border shadow-2xs transition-colors"
											     style="background-color: <?php echo esc_attr( $c_card_bg ); ?>; border-color: <?php echo esc_attr( $c_border ); ?>;">
												<div class="preview_card_label text-[9px] uppercase font-bold tracking-wider" style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">Submissions</div>
												<div class="preview_card_num text-sm font-black mt-0.5" style="color: <?php echo esc_attr( $c_text_main ); ?>;">1,248</div>
												<div class="text-[9px] font-bold mt-1 inline-block px-1.5 py-0.5 rounded-md"
												     id="preview_active_badge"
												     style="background-color: <?php echo esc_attr( $c_active_bg ); ?>; color: <?php echo esc_attr( $c_active_text ); ?>;">
													Active
												</div>
											</div>
										</div>

										<!-- Content Block / Table Mockup -->
										<div class="preview_card p-3 rounded-2xl border shadow-2xs space-y-2 transition-colors"
										     style="background-color: <?php echo esc_attr( $c_card_bg ); ?>; border-color: <?php echo esc_attr( $c_border ); ?>;">
											<div class="flex items-center justify-between pb-1.5 border-b" style="border-color: <?php echo esc_attr( $c_border ); ?>;">
												<span class="preview_card_heading text-[10px] font-bold" style="color: <?php echo esc_attr( $c_text_main ); ?>;">Recent Activities</span>
												<span class="text-[9px] font-semibold" style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">View All</span>
											</div>
											<div class="space-y-1.5">
												<div class="flex items-center justify-between text-[10px]">
													<span class="preview_card_item_text" style="color: <?php echo esc_attr( $c_text_main ); ?>;">Post #104 Published</span>
													<span class="text-[9px]" style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">2m ago</span>
												</div>
												<div class="flex items-center justify-between text-[10px]">
													<span class="preview_card_item_text" style="color: <?php echo esc_attr( $c_text_main ); ?>;">Profile Image Updated</span>
													<span class="text-[9px]" style="color: <?php echo esc_attr( $c_sidebar_text ); ?>;">1h ago</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<p class="text-[11px] text-slate-600 text-center">
								<?php esc_html_e( 'The preview updates synchronously as you adjust color pickers or apply presets.', 'frontend-dashboard' ); ?>
							</p>
						</div>
					</div>
				</div>
			</form>
		</div>

		<script>
		jQuery(document).ready(function($) {
			function updatePreview() {
				var bg_color     = $('input[name="color[fed_upl_color_bg_color]"]').val() || '#4F46E5';
				var bg_font      = $('input[name="color[fed_upl_color_bg_font_color]"]').val() || '#FFFFFF';
				var sbg_color    = $('input[name="color[fed_upl_color_sbg_color]"]').val() || '#06B6D4';
				var sbg_font     = $('input[name="color[fed_upl_color_sbg_font_color]"]').val() || '#FFFFFF';
				var sidebar_bg   = $('input[name="color[fed_upl_color_sidebar_bg]"]').val() || '#FFFFFF';
				var sidebar_text = $('input[name="color[fed_upl_color_sidebar_text]"]').val() || '#64748B';
				var active_bg    = $('input[name="color[fed_upl_color_active_bg]"]').val() || '#EEF2FF';
				var active_text  = $('input[name="color[fed_upl_color_active_text]"]').val() || '#4F46E5';
				var body_bg      = $('input[name="color[fed_upl_color_body_bg]"]').val() || '#F8FAFC';
				var card_bg      = $('input[name="color[fed_upl_color_card_bg]"]').val() || '#FFFFFF';
				var text_main    = $('input[name="color[fed_upl_color_text_main]"]').val() || '#0F172A';
				var border       = $('input[name="color[fed_upl_color_border]"]').val() || '#E2E8F0';

				// Apply to Preview
				$('#fed_mini_preview').css({ 'background-color': body_bg, 'border-color': border });
				$('#preview_sidebar').css({ 'background-color': sidebar_bg, 'border-color': border });
				$('#preview_nav_active').css({ 'background-color': active_bg, 'color': active_text });
				$('#preview_active_badge').css({ 'background-color': active_bg, 'color': active_text });
				$('.preview_nav_inactive').css({ 'color': sidebar_text });
				$('#preview_logo_text, #preview_user_name, #preview_user_role, #preview_subheading').css({ 'color': sidebar_text });
				$('#preview_primary_btn').css({ 'background-color': bg_color, 'color': bg_font });
				$('#preview_logo_icon').css({ 'background-color': bg_color, 'color': bg_font });
				$('#preview_accent_badge').css({ 'background-color': sbg_color, 'color': sbg_font });
				$('#preview_heading, .preview_card_num, .preview_card_heading, .preview_card_item_text').css({ 'color': text_main });
				$('.preview_card_label').css({ 'color': sidebar_text });
				$('.preview_card').css({ 'background-color': card_bg, 'border-color': border });
			}

			// Listen to color changes
			$(document).on('input change', '.fed_color_input, .fed_color_native', function() {
				updatePreview();
			});

			// Handle 1-Click Preset selection
			$(document).on('click', '.fed-preset-btn', function(e) {
				e.preventDefault();
				var data = $(this).data('preset');
				if (typeof data === 'string') {
					try { data = JSON.parse(data); } catch(err) {}
				}
				if (!data || typeof data !== 'object') return;

				$.each(data, function(key, val) {
					var $input = $('input[name="color[' + key + ']"]');
					if ($input.length) {
						$input.val(val);
						var $container = $input.closest('.fed_color_picker_container');
						$container.find('.fed_color_native').val(val);
						$container.find('.fed_color_swatch').css('background-color', val);
					}
				});

				updatePreview();

				// Visual feedback on preset button
				$('.fed-preset-btn').removeClass('ring-2 ring-indigo-500 bg-indigo-50/80');
				$(this).addClass('ring-2 ring-indigo-500 bg-indigo-50/80');
			});
		});
		</script>
		<?php
	} else {
		?>
		<div class="alert alert-info">
			<strong>
				<?php
				esc_attr_e(
					'Please install Frontend Dashboard Extra Plugin to activate this section',
					'frontend-dashboard'
				);
				?>
			</strong>
			<?php esc_attr_e( 'Download', 'frontend-dashboard' ); ?>
			<a href="https://buffercode.com/plugin/frontend-dashboard-extra">
				<?php
				esc_attr_e( 'Frontend Dashboard Extra', 'frontend-dashboard' );
				?>
			</a>
		</div>
		<?php
	}
}
