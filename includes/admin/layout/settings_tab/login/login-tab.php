<?php
/**
 * Login Tab.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Admin Login Tab
 */
function fed_admin_login_tab() {

	$fed_login              = get_option( 'fed_admin_login' );
	$tabs                   = fed_get_admin_login_options( $fed_login );
	?>
	<div class="row">
		<div class="col-md-3 padd_top_20">
			<ul class="nav nav-pills nav-stacked"
				id="fed_admin_setting_login_tabs"
				role="tablist">
				<?php
				$menu_count = 0;
				foreach ( $tabs as $index => $tab ) {
					$active = ( 0 === $menu_count ) ? 'active' : '';
					$menu_count ++;
					?>
					<li role="presentation" class="<?php echo esc_attr( $active ); ?>">
						<a href="#<?php echo esc_attr( $index ); ?>"
						   aria-controls="<?php esc_attr($index); ?>"
						   role="tab"
						   data-toggle="tab">
							<i class="<?php echo esc_attr($tab['icon']); ?>"></i>
							<?php echo esc_attr($tab['name']); ?>
						</a>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="col-md-9">
			<!-- Tab panes -->
			<div class="tab-content">
				<?php
				$content_count = 0;
				foreach ( $tabs as $index => $tab ) {
					$active = ( 0 === $content_count ) ? 'active' : '';
					$content_count ++;
					?>
					<div role="tabpanel"
						 class="tab-pane <?php echo esc_attr($active); ?>"
						 id="<?php echo esc_attr($index); ?>">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title">
									<span class="<?php echo esc_attr($tab['icon']); ?>"></span>
									<?php echo esc_attr($tab['name']); ?>
								</h3>
							</div>
							<div class="panel-body">
								<?php
								call_user_func( $tab['callable'], $tab['arguments'] )
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

/**
 * Admin Login Options.
 *
 * @param  string $fed_login  Login.
 *
 * @return array
 */
function fed_get_admin_login_options( $fed_login ) {
	return apply_filters(
		'fed_customize_admin_login_options', array(
			'fed_admin_login_settings'      => array(
				'icon'      => 'fa fa-cogs',
				'name'      => __( 'Settings', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_login_settings_tab',
				'arguments' => $fed_login,
			),
			'fed_admin_register_settings'   => array(
				'icon'      => 'fas fa-door-open',
				'name'      => __( 'Register', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_register_settings_tab',
				'arguments' => $fed_login,
			),
			'fed_admin_restrict_wp_admin'   => array(
				'icon'      => 'fa fa-user-secret',
				'name'      => __( 'Restrict WP Admin Area', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_restrict_wp_admin_tab',
				'arguments' => $fed_login,
			),
			'fed_admin_username_restrict'   => array(
				'icon'      => 'fa fa-ban',
				'name'      => __( 'Restrict Username', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_username_restrict_tab',
				'arguments' => $fed_login,
			),
			'fed_admin_frontend_login_menu' => array(
				'icon'      => 'fa fa-align-justify',
				'name'      => __( 'Frontend Login Menu', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_frontend_login_menu_tab',
				'arguments' => $fed_login,
			),
		)
	);
}
