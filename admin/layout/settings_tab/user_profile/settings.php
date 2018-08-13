<?php
function fed_admin_user_profile_settings_tab( $fed_admin_options ) {
	$fed_upef = array_merge( fed_fetch_user_profile_extra_fields_key_value(), array( '' => 'Let it be default' ) );

	$array = array(
		'form'   => array(
			'method' => '',
			'class'  => 'fed_admin_menu fed_ajax',
			'attr'   => '',
			'action' => array( 'url' => '', 'action' => 'fed_admin_setting_form' ),
			'nonce'  => array( 'action' => '', 'name' => '' ),
			'loader' => '',
		),
		'hidden' => array(
			'fed_admin_unique' => array(
				'input_type' => 'hidden',
				'user_value' => 'fed_admin_setting_upl',
				'input_meta' => 'fed_admin_unique',
			)
		),
		'input'  => array(
			'Change Profile Picture'        => array(
				'col'          => 'col-md-6',
				'name'         => __( 'Change Profile Picture', 'frontend-dashboard' ),
				'input'        => fed_get_input_details( array(
					'input_meta'  => 'settings[fed_upl_change_profile_pic]',
					'input_value' => $fed_upef,
					'user_value'  => isset( $fed_admin_options['settings']['fed_upl_change_profile_pic'] ) ? $fed_admin_options['settings']['fed_upl_change_profile_pic'] : '',
					'input_type'  => 'select'
				) ),
				'help_message' => fed_show_help_message( array( 'content' => 'Image size should be min 600x600 px' ) )
			),
			'Disable Description'           => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Disable Description', 'frontend-dashboard' ),
				'input' => fed_get_input_details( array(
					'input_value' => fed_yes_no( 'ASC' ),
					'input_meta'  => 'settings[fed_upl_disable_desc]',
					'user_value'  => isset( $fed_admin_options['settings']['fed_upl_disable_desc'] ) ? $fed_admin_options['settings']['fed_upl_disable_desc'] : '',
					'input_type'  => 'select'
				) )
			),
			'Number of Recent Post to show' => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Number of Recent Post to show', 'frontend-dashboard' ),
				'input' => fed_get_input_details( array(
					'placeholder' => __( 'Number of Recent Post to show on User Profile', 'frontend-dashboard' ),
					'input_meta'  => 'settings[fed_upl_no_recent_post]',
					'user_value'  => isset( $fed_admin_options['settings']['fed_upl_no_recent_post'] ) ? $fed_admin_options['settings']['fed_upl_no_recent_post'] : '5',
					'input_type'  => 'number'
				) )
			),
			'Collapse Menu Always'          => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Collapse Menu Always', 'frontend-dashboard' ),
				'input' => fed_get_input_details( array(
					'input_value' => fed_yes_no( 'ASC' ),
					'input_meta'  => 'settings[fed_upl_collapse_menu]',
					'user_value'  => isset( $fed_admin_options['settings']['fed_upl_collapse_menu'] ) ? $fed_admin_options['settings']['fed_upl_collapse_menu'] : '',
					'input_type'  => 'select'
				) )
			),
			'Disable Logout'                => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Disable Logout', 'frontend-dashboard' ),
				'input' => fed_get_input_details( array(
					'input_value' => fed_yes_no( 'ASC' ),
					'input_meta'  => 'settings[fed_upl_disable_logout]',
					'user_value'  => isset( $fed_admin_options['settings']['fed_upl_disable_logout'] ) ? $fed_admin_options['settings']['fed_upl_disable_logout'] : '',
					'input_type'  => 'select'
				) )
			),
			'Disable Collapse Menu'         => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Disable Collapse Menu', 'frontend-dashboard' ),
				'input' => fed_get_input_details( array(
					'input_value' => fed_yes_no( 'ASC' ),
					'input_meta'  => 'settings[fed_upl_disable_collapse_menu]',
					'user_value'  => isset( $fed_admin_options['settings']['fed_upl_disable_collapse_menu'] ) ? $fed_admin_options['settings']['fed_upl_disable_collapse_menu'] : '',
					'input_type'  => 'select'
				) )
			),
		)
	);

	$new_value = apply_filters( 'fed_admin_upl_settings_template', $array, $fed_admin_options );

	fed_common_simple_layout( $new_value );
}

function fed_admin_user_profile_colors_tab() {
	if ( defined( 'BC_FED_EXTRA_PLUGIN_VERSION' ) ) {
		$fed_admin_options = get_option( 'fed_admin_setting_upl_color' );
		$array             = array(
			'form'   => array(
				'method' => '',
				'class'  => 'fed_admin_menu fed_ajax',
				'attr'   => '',
				'action' => array( 'url' => '', 'action' => 'fed_admin_setting_form' ),
				'nonce'  => array( 'action' => '', 'name' => '' ),
				'loader' => '',
			),
			'hidden' => array(
				'fed_admin_unique' => array(
					'input_type' => 'hidden',
					'user_value' => 'fed_admin_setting_upl_color',
					'input_meta' => 'fed_admin_unique',
				)
			),
			'input'  => array(
				'Primary Background Color'        => array(
					'col'          => 'col-md-6',
					'name'         => __( 'Primary Background Color', 'frontend-dashboard' ),
					'input'        => fed_get_input_details( array(
						'input_meta' => 'color[fed_upl_color_bg_color]',
						'user_value' => isset( $fed_admin_options['color']['fed_upl_color_bg_color'] ) ? $fed_admin_options['color']['fed_upl_color_bg_color'] : '#0AAAAA',
						'input_type' => 'color'
					) ),
					'help_message' => fed_show_help_message( array( 'content' => 'Default Primary Color #0AAAAA' ) )
				),
				'Primary Background Font Color'   => array(
					'col'          => 'col-md-6',
					'name'         => __( 'Primary Background Font Color', 'frontend-dashboard' ),
					'input'        => fed_get_input_details( array(
						'input_meta' => 'color[fed_upl_color_bg_font_color]',
						'user_value' => isset( $fed_admin_options['color']['fed_upl_color_bg_font_color'] ) ? $fed_admin_options['color']['fed_upl_color_bg_font_color'] : '#ffffff',
						'input_type' => 'color'
					) ),
					'help_message' => fed_show_help_message( array( 'content' => 'Default Primary Color #FFFFFF' ) )
				),
				'Secondary Background Color'      => array(
					'col'          => 'col-md-6',
					'name'         => __( 'Secondary Background Color', 'frontend-dashboard' ),
					'input'        => fed_get_input_details( array(
						'input_meta' => 'color[fed_upl_color_sbg_color]',
						'user_value' => isset( $fed_admin_options['color']['fed_upl_color_sbg_color'] ) ? $fed_admin_options['color']['fed_upl_color_sbg_color'] : '#033333',
						'input_type' => 'color'
					) ),
					'help_message' => fed_show_help_message( array( 'content' => 'Default Secondary Color #033333' ) )
				),
				'Secondary Background Font Color' => array(
					'col'          => 'col-md-6',
					'name'         => __( 'Secondary Background Font Color', 'frontend-dashboard' ),
					'input'        => fed_get_input_details( array(
						'input_meta' => 'color[fed_upl_color_sbg_font_color]',
						'user_value' => isset( $fed_admin_options['color']['fed_upl_color_sbg_font_color'] ) ? $fed_admin_options['color']['fed_upl_color_sbg_font_color'] : '#ffffff',
						'input_type' => 'color'
					) ),
					'help_message' => fed_show_help_message( array( 'content' => 'Default Secondary Color #FFFFFF' ) )
				)
			)
		);

		$new_values = apply_filters( 'fed_admin_upl_colors_template', $array, $fed_admin_options );

		fed_common_simple_layout( $new_values );
	} else {
		?>
        <div class="alert alert-info">
            <strong><?php _e( 'Please install Frontend Dashboard Extra Plugin to activate this section', 'frontend-dashboard' ) ?></strong>
			<?php _e( 'Download', 'frontend-dashboard' ) ?>
            <a href="https://buffercode.com/plugin/frontend-dashboard-extra"><?php _e( 'Frontend Dashboard Extra', 'frontend-dashboard' ) ?></a>
        </div>
		<?php
	}
}