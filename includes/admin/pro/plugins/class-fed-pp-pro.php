<?php
/**
 * Payment Pro.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FEDPPPRO' ) && ! defined( 'BC_FED_PP_PLUGIN' ) ) {
	/**
	 * Class FEDPPPRO
	 */
	class FEDPPPRO {

		/**
		 * FEDPPPRO constructor.
		 */
		public function __construct() {
			add_filter( 'fed_payment_menu', array( $this, 'menu' ) );
		}

		/**
		 * Menu.
		 *
		 * @param  array $menu  Menu.
		 *
		 * @return mixed
		 */
		public function menu( $menu ) {
			$menu['gateway']['submenu']['FEDPPPRO@stripe']    = array(
				'icon' => 'fab fa-cc-stripe',
				'name' => 'Stripe',
				'menu' => array( 'FEDPPPRO@stripe' ),
			);
			$menu['gateway']['submenu']['FEDPPPRO@braintree'] = array(
				'icon' => 'fas fa-money-check-alt',
				'name' => 'Braintree',
				'menu' => array( 'FEDPPPRO@braintree' ),
			);

			$menu['email'] = array(
				'icon'    => 'fa fa-envelope',
				'name'    => __( 'Email', 'frontend-dashboard' ),
				'submenu' => array(
					'FEDPPPRO@templates' => array(
						'icon' => 'fa fa-paint-brush',
						'name' => __( 'Templates', 'frontend-dashboard' ),
						'menu' => array( 'FEDPPPRO@templates' ),
					),
				),
			);

			return $menu;
		}

		/**
		 * Stripe.
		 */
		public function stripe() {
			$this->pro();
		}

		/**
		 * Braintree.
		 */
		public function braintree() {
			$this->pro();
		}

		/**
		 * Templates.
		 */
		public function templates() {
			$this->pro();
		}

		/**
		 * Pro.
		 */
		public function pro() {
			?>
			<div class="row m-b-20">
				<div class="col-md-4">
					<form method="post" action="https://buffercode.com/payment/bc/payment_start">
						<input type='hidden' name='redirect_url' value="<?php echo fed_current_page_url(); ?>"/>
						<input type='hidden' name='domain' value="<?php echo fed_get_domain_name(); ?>"/>
						<input type='hidden' name='contact_email' value="<?php echo fed_get_admin_email(); ?>"/>
						<input type='hidden' name='plugin_name' value='frontend-dashboard-payment-pro'/>
						<input type='hidden' name='amount' value='29'/>
						<input type='hidden' name='plan_type' value='annual'/>
						<button type="submit" style="
								background:url(
						<?php
						echo esc_url(
							plugins_url(
								'assets/admin/images/pro/buy-now-29.png',
								BC_FED_PLUGIN
							)
						);
						?>
								);
								background-repeat: no-repeat;
								width:200px;
								height: 148px;
								border: 0;">
						</button>
					</form>
				</div>
				<div class="col-md-4">
					<form method="post" action="https://buffercode.com/payment/bc/payment_start">
						<input type='hidden' name='redirect_url' value="<?php echo fed_current_page_url(); ?>"/>
						<input type='hidden' name='domain' value="<?php echo fed_get_domain_name(); ?>"/>
						<input type='hidden' name='contact_email' value="<?php echo fed_get_admin_email(); ?>"/>
						<input type='hidden' name='plugin_name' value='frontend-dashboard-payment-pro'/>
						<input type='hidden' name='amount' value='99'/>
						<input type='hidden' name='plan_type' value='lifetime'/>
						<button type="submit" style="
								background:url(
						<?php
						echo esc_url(
							plugins_url(
								'assets/admin/images/pro/buy-now-99.png',
								BC_FED_PLUGIN
							)
						);
						?>
								);
								background-repeat: no-repeat;
								width:200px;
								height: 148px;
								border: 0;">
						</button>
					</form>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<h4>
						For More Information, Please visit -
						<a target="_blank"
								href="https://buffercode.com/plugin/frontend-dashboard-payment-pro">Frontend Dashboard
							Payment Pro
						</a>
					</h4>
				</div>
			</div>

			<?php

		}
	}

	new FEDPPPRO();
}
