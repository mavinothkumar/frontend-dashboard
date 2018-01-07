<?php

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
					$active = $menu_count === 0 ? 'active' : '';
					$menu_count ++;
					?>
					<li role="presentation"
						class="<?php echo $active; ?>">
						<a href="#<?php echo $index; ?>"
						   aria-controls="<?php echo $index; ?>"
						   role="tab"
						   data-toggle="tab">
							<i class="<?php echo $tab['icon']; ?>"></i>
							<?php printf( __( '%s', 'frontend-dashboard' ), $tab['name'] ) ?>
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
					$active = $content_count === 0 ? 'active' : '';
					$content_count ++;
					?>
					<div role="tabpanel"
						 class="tab-pane <?php echo $active; ?>"
						 id="<?php echo $index; ?>">
						<?php
						call_user_func( $tab['callable'], $tab['arguments'] )
						?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
}

function fed_get_admin_user_options( $fed_admin_options ) {
	return apply_filters( 'fed_customize_admin_user_options', array(
		'fed_admin_user_profile_settings'  => array(
			'icon'      => 'fa fa-user-plus',
			'name'      => __( 'Add/Delete Custom Role', 'frontend-dashboard' ),
			'callable'  => 'fed_admin_user_role_tab',
			'arguments' => $fed_admin_options
		),
		'fed_admin_user_upload_permission' => array(
			'icon'      => 'fa fa-upload',
			'name'      => __( 'User Upload Permission', 'frontend-dashboard' ),
			'callable'  => 'fed_admin_user_upload_permission_tab',
			'arguments' => $fed_admin_options
		),
	) );
}