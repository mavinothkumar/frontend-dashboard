<?php

function fed_admin_dashboard_settings_menu_header() {
	$menu = array(
		'login'                => array(
			'icon_class' => 'fa fa-sign-in',
			'name'       => 'Login',
			'callable'   => 'fed_admin_login_tab',
		),
		'post_options'         => array(
			'icon_class' => 'fa fa-envelope',
			'name'       => 'Post',
			'callable'   => 'fed_admin_post_options_tab',
		),
		'user'                 => array(
			'icon_class' => 'fa fa-sign-in',
			'name'       => 'User',
			'callable'   => 'fed_admin_user_options_tab',
		),
		'user_profile_layout'  => array(
			'icon_class' => 'fa fa-dashboard',
			'name'       => 'User Profile Layout',
			'callable'   => 'fed_user_profile_layout_design',
		)
	);

	return apply_filters( 'fed_admin_dashboard_settings_menu_header', $menu );
}