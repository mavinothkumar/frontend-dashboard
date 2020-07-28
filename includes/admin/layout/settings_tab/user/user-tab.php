<?php
/**
 * User Tab.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User Option Tab
 */
function fed_admin_user_options_tab() {
	$fed_admin_options = get_option( 'fed_admin_settings_user' );
	$tabs              = fed_get_admin_user_options( $fed_admin_options );
	?>
	<div class="row">
		<div class="col-md-3 padd_top_20">
			<ul class="nav nav-pills nav-stacked"
					id="fed_admin_setting_user_profile_tabs"
					role="tablist">
				<?php
				$menu_count = 0;
				foreach ( $tabs as $index => $tab ) {
					$active = ( 0 === $menu_count ) ? 'active' : '';
					$menu_count ++;
					?>
					<li role="presentation"
							class="<?php echo esc_attr( $active ); ?>">
						<a href="#<?php echo esc_attr( $index ); ?>"
								aria-controls="<?php echo esc_attr( $index ); ?>"
								role="tab"
								data-toggle="tab">
							<i class="<?php echo esc_attr( $tab['icon'] ); ?>"></i>
							<?php echo esc_attr( $tab['name'] ); ?>
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
							class="tab-pane <?php echo esc_attr( $active ); ?>"
							id="<?php echo esc_attr( $index ); ?>">
						<?php
						fed_call_function_method( $tab )
						?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Admin User Options.
 *
 * @param  array $fed_admin_options  Admin options.
 *
 * @return mixed|void
 */
function fed_get_admin_user_options( $fed_admin_options ) {
	return apply_filters(
		'fed_customize_admin_user_options',
		array(
			'fed_admin_user_profile_settings'  => array(
				'icon'      => 'fa fa-user-plus',
				'name'      => __( 'Add/Delete Custom Role', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_user_role_tab',
				'arguments' => $fed_admin_options,
			),
			'fed_admin_user_upload_permission' => array(
				'icon'      => 'fa fa-upload',
				'name'      => __( 'User Upload Permission', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_user_upload_permission_tab',
				'arguments' => $fed_admin_options,
			),
		),
		$fed_admin_options
	);
}
