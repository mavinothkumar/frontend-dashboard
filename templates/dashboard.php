<?php
/**
 * Dashboard Page
 *
 * @package frontend-dashboard
 */

$dashboard_container = new FED_Routes( $_REQUEST );
$menu                = $dashboard_container->setDashboardMenuQuery();

do_action( 'fed_before_dashboard_container' );
?>
	<div class="bc_fed fed_dashboard_container">
		<?php echo fed_loader() ?>
        <?php do_action( 'fed_inside_dashboard_container_top' ); ?>
        <?php echo fed_show_alert('fed_dashboard_top_message') ?>
		<?php if ( ! $menu instanceof WP_Error ) { ?>
			<div class="row fed_dashboard_wrapper">
				<div class="col-md-3 fed_dashboard_menus default_template">
					<div class="custom-collapse fed_menu_items">
						<button class="bg_secondary collapse-toggle visible-xs collapsed" type="button" data-toggle="collapse" data-parent="custom-collapse" data-target="#fed_default_template">
							<span class=""><i class="fa fa-bars"></i></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<ul class="list-group fed_menu_ul collapse" id="fed_default_template">
							<?php
							fed_display_dashboard_menu( $menu );

							fed_get_collapse_menu() ?>
						</ul>
					</div>
				</div>
				<div class="col-md-9 fed_dashboard_items">
					<?php
					$dashboard_container->getDashboardContent($menu);
					?>
				</div>
			</div>
		<?php }
		if ( $menu instanceof WP_Error ) {
			?>
			<div class="row fed_dashboard_wrapper fed_error">
				<?php fed_get_403_error_page() ?>
			</div>
			<?php
		} ?>
        <?php do_action( 'fed_inside_dashboard_container_bottom' ); ?>
	</div>
<?php
do_action( 'fed_after_dashboard_container' );

