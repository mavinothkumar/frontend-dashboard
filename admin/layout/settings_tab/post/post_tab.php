<?php
/**
 * Post Options
 */
function fed_admin_post_options_tab() {
	?>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-warning">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				Please install <strong><a href="https://buffercode.com/plugin/frontend-dashboard-custom-post-and-taxonomies">Frontend Dashboard Custom Post and Taxonomies</a></strong> Plugin to customise it
			</div>
		</div>
	</div>
	<?php
}


function fed_get_admin_post_options( $fed_admin_options ) {
	return apply_filters( 'fed_customize_admin_post_options', array(
		'fed_admin_post_settings'    => array(
			'icon'      => 'fa fa-cogs',
			'name'      => __( 'Settings', 'frontend-dashboard' ),
			'callable'  => 'fed_admin_post_settings_tab',
			'arguments' => $fed_admin_options
		),
		'fed_admin_post_dashboard'   => array(
			'icon'      => 'fa fa-cog',
			'name'      => __( 'Dashboard Settings', 'frontend-dashboard' ),
			'callable'  => 'fed_admin_post_dashboard_tab',
			'arguments' => $fed_admin_options
		),
		'fed_admin_post_menu'        => array(
			'icon'      => 'fa fa-list',
			'name'      => __( 'Menu', 'frontend-dashboard' ),
			'callable'  => 'fed_admin_post_menu_tab',
			'arguments' => $fed_admin_options
		),
		'fed_admin_post_permissions' => array(
			'icon'      => 'fa fa-universal-access',
			'name'      => __( 'Permissions', 'frontend-dashboard' ),
			'callable'  => 'fed_admin_post_permissions_tab',
			'arguments' => $fed_admin_options
		),
	) );
}
