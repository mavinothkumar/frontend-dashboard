<?php
function fed_admin_login_settings_tab( $fed_login_settings ) {
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
			'fed_admin_unique'       => array(
				'input_type' => 'hidden',
				'user_value' => 'fed_login_details',
				'input_meta' => 'fed_admin_unique',
			),
			'fed_admin_unique_login' => array(
				'input_type' => 'hidden',
				'user_value' => 'fed_login_settings',
				'input_meta' => 'fed_admin_unique_login',
			),
		),
		'input'  => array(
			'Login Page URL'                => array(
				'col'          => 'col-md-6',
				'name'         => 'Login Page URL',
				'input'        => wp_dropdown_pages( array(
					'name'             => 'fed_admin_login[settings][fed_login_url]',
					'selected'         => isset( $fed_login_settings['settings']['fed_login_url'] ) ? $fed_login_settings['settings']['fed_login_url'] : '',
					'show_option_none' => 'Let it be default',
					'class'            => 'form-control',
					'echo'             => false
				) ),
				'help_message' => fed_show_help_message( array(
					'content' => 'Please add shortcode [fed_login] to show all in one or [fed_login_only] to show only login'
				) )

			),
			'Register Page URL'             => array(
				'col'          => 'col-md-6',
				'name'         => __( 'Register Page URL', 'frontend-dashboard' ),
				'input'        => wp_dropdown_pages( array(
					'name'             => 'fed_admin_login[settings][fed_register_url]',
					'selected'         => isset( $fed_login_settings['settings']['fed_register_url'] ) ? $fed_login_settings['settings']['fed_register_url'] : '',
					'show_option_none' => 'Let it be default',
					'class'            => 'form-control',
					'echo'             => false
				) ),
				'help_message' => fed_show_help_message( array(
					'content' => 'Please add shortcode [fed_register_only] to this page'
				) )
			),
			'Forgot Password Page URL'      => array(
				'col'          => 'col-md-6',
				'name'         => __( 'Forgot Password Page URL', 'frontend-dashboard' ),
				'input'        => wp_dropdown_pages( array(
					'name'             => 'fed_admin_login[settings][fed_forgot_password_url]',
					'selected'         => isset( $fed_login_settings['settings']['fed_forgot_password_url'] ) ? $fed_login_settings['settings']['fed_forgot_password_url'] : '',
					'show_option_none' => 'Let it be default',
					'class'            => 'form-control',
					'echo'             => false
				) ),
				'help_message' => fed_show_help_message( array(
					'content' => 'Please add shortcode [fed_forgot_password_only] to this page'
				) )
			),
			'Redirect After Register URL'   => array(
				'col'          => 'col-md-6',
				'name'         => __( 'Redirect After Register URL', 'frontend-dashboard' ),
				'input'        => wp_dropdown_pages( array(
					'name'             => 'fed_admin_login[settings][fed_redirect_register_url]',
					'selected'         => isset( $fed_login_settings['settings']['fed_redirect_register_url'] ) ? $fed_login_settings['settings']['fed_redirect_register_url'] : '',
					'show_option_none' => 'Let it be default',
					'class'            => 'form-control',
					'echo'             => false,
				) ),
			),
			'Redirect After Logged in URL'  => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Redirect After Logged in URL', 'frontend-dashboard' ),
				'input' => wp_dropdown_pages( array(
					'name'             => 'fed_admin_login[settings][fed_redirect_login_url]',
					'selected'         => isset( $fed_login_settings['settings']['fed_redirect_login_url'] ) ? $fed_login_settings['settings']['fed_redirect_login_url'] : '',
					'show_option_none' => 'Let it be default',
					'class'            => 'form-control',
					'echo'             => false
				) )
			),
			'Redirect After Logged out URL' => array(
				'col'   => 'col-md-6',
				'name'  => __( 'Redirect After Logged out URL', 'frontend-dashboard' ),
				'input' => wp_dropdown_pages( array(
					'name'             => 'fed_admin_login[settings][fed_redirect_logout_url]',
					'selected'         => isset( $fed_login_settings['settings']['fed_redirect_logout_url'] ) ? $fed_login_settings['settings']['fed_redirect_logout_url'] : '',
					'show_option_none' => 'Let it be default',
					'class'            => 'form-control',
					'echo'             => false
				) )
			),
			'Dashboard'                     => array(
				'col'          => 'col-md-6',
				'name'         => __( 'Dashboard', 'frontend-dashboard' ),
				'input'        => wp_dropdown_pages( array(
					'name'             => 'fed_admin_login[settings][fed_dashboard_url]',
					'selected'         => isset( $fed_login_settings['settings']['fed_dashboard_url'] ) ? $fed_login_settings['settings']['fed_dashboard_url'] : '',
					'show_option_none' => 'Let it be default',
					'class'            => 'form-control',
					'echo'             => false
				) ),
				'help_message' => fed_show_help_message( array(
					'content' => 'Please add shortcode [fed_dashboard] to this page'
				) )
			),
		),
		'note'   => array( 'header' => '', 'footer' => '' ),
	);
	apply_filters( 'fed_admin_login_settings_template', $array, $fed_login_settings );

	fed_common_simple_layout( $array );


}

