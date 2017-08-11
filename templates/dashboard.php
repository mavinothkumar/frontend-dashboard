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

//$a = new FEDSupport();
//var_dump($a->showUserQuestionAnswers());


do_action( 'fed_before_dashboard_container' );

fed_get_payment_notification();
?>
	<div class="bc_fed fed_dashboard_container">
		<?php echo fed_loader() ?>
		<div class="row fed_dashboard_wrapper">
			<div class="col-md-3 fed_dashboard_menus">
				<div class="list-group">
					<?php
					fed_display_dashboard_menu( $first_element );
					?>
				</div>
				<?php fed_get_collapse_menu() ?>
			</div>
			<div class="col-md-9 fed_dashboard_items">
				<?php
				fed_display_dashboard_profile( $first_element );

				/**
				 * Show Post options only on enabled users
				 */
				if ( isset( $menu['post'] ) ) {
					fed_display_dashboard_post( $first_element );
				}

				/**
				 * Show Payment Options only for enabled users
				 */
				if ( isset( $menu['payment'] ) ) {
					fed_display_dashboard_payment( $first_element );
				}

				/**
				 * Show Support Options only for enabled users
				 */
				if ( isset( $menu['support'] ) ) {
					fed_display_dashboard_support( $first_element );
				}

				if ( isset( $menu['logout'] ) ) {
					fed_logout_process($first_element);
				}

				?>
			</div>
		</div>
	</div>
<?php
do_action( 'fed_after_dashboard_container' );

