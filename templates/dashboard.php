<?php
/**
 * Dashboard Page
 *
 * @package frontend-dashboard
 */

$menu = fed_get_all_dashboard_display_menus();

/**
 * To identify the first element to make active
 */
$first_element_key = array_keys( $menu );
$first_element     = $first_element_key[0];

do_action( 'fed_before_dashboard_container' );

fed_get_payment_notification();
?>
	<div class="bc_fed fed_dashboard_container">
		<?php echo fed_loader() ?>
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
						fed_display_dashboard_menu( $first_element );
						?>
						<?php fed_get_collapse_menu() ?>
					</ul>
				</div>
			</div>
			<div class="col-md-9 fed_dashboard_items">
				<?php
				fed_display_dashboard_profile( $first_element );

				if ( isset( $menu['logout'] ) ) {
					fed_logout_process( $first_element );
				}

				do_action( 'fed_frontend_dashboard_menu_container', $first_element );

				?>
			</div>
		</div>
	</div>
<?php
do_action( 'fed_after_dashboard_container' );

