<?php
/**
 * User Profile Layout
 */
function fed_user_profile_layout_design() {
	$fed_admin_options = get_option( 'fed_admin_settings_upl' );
	$tabs = fed_user_profile_layout_options($fed_admin_options);
	?>
        <div class="bc_fed row">
            <div class="col-md-3 padd_top_20">
                <ul class="nav nav-pills nav-stacked"
                    id="fed_admin_setting_user_profile_layout_tabs"
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
				                <?php echo $tab['name']; ?>
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
function fed_user_profile_layout_options($fed_admin_options) {
	return apply_filters( 'fed_customize_admin_user_profile_layout_options', array(
		'fed_admin_user_profile_layout_settings'    => array(
			'icon'      => 'fa fa-cogs',
			'name'      => __( 'Settings', 'fed' ),
			'callable'  => 'fed_admin_user_profile_settings_tab',
			'arguments' => $fed_admin_options
		),
	) );
}
