<?php
/**
 * Membership Pro.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FEDMPPRO' ) && ! defined( 'BC_FED_MP_PLUGIN' ) ) {
	/**
	 * Class FEDMPPRO
	 */
	class FEDMPPRO {
		/**
		 * FEDMPPRO constructor.
		 */
		public function __construct() {
			add_filter(
				'fed_add_main_sub_menu', array(
					$this,
					'main_sub_menu',
				)
			);
			add_filter(
				'fed_admin_script_loading_pages', array(
					$this,
					'script_loading_pages',
				)
			);
		}

		/**
		 * Script Loading Pages.
		 *
		 * @param  array $pages  Pages.
		 *
		 * @return array
		 */
		public function script_loading_pages( $pages ) {
			return array_merge( $pages, array( 'fed_membership_pro' ) );
		}

		/**
		 * Main Sub Menu.
		 *
		 * @param  array $menu  Menu.
		 *
		 * @return mixed
		 */
		public function main_sub_menu( $menu ) {
			$menu['fed_membership_pro'] = array(
				'page_title' => __( 'Membership Pro', 'frontend-dashboard' ),
				'menu_title' => __( 'Membership Pro', 'frontend-dashboard' ),
				'capability' => 'manage_options',
				'callback'   => array( $this, 'menu' ),
				'position'   => 30,
			);

			return $menu;
		}

		/**
		 * Menu
		 */
		public function menu() {
			$get_payload = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
			$action      = ( isset( $get_payload, $get_payload['action'] ) && ! empty( $get_payload['action'] ) ) ? urldecode(
				$get_payload['action']
			) : false;
			$this->header_menu();
			if ( $action ) {
				$page = in_array( $action, $this->page_list() ) ? $action : array();
				if ( is_string( $page ) ) {
					$action = true;
					fed_execute_method_by_string( $page, $_GET );
				}
			}
			if ( false === $action ) {
				?>
				<div class="bc_fed">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<h3 class="panel-title">Panel title</h3>
									</div>
									<div class="panel-body">
										<?php
										$this->pro();
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		}

		/**
		 * Page List.
		 *
		 * @return array
		 */
		public function page_list() {
			$menus     = $this->header_menu_items();
			$menu_item = array();
			foreach ( $menus as $index => $menu ) {
				$menu_item = array_merge( $menu['menu'], $menu_item );
			}

			return $menu_item;
		}

		/**
		 * Header Menu.
		 */
		public function header_menu() {
			$get_payload = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
			?>
			<div class="bc_fed">
				<div class="m-t-10">
					<ul class="nav nav-tabs" id="" role="tablist">
						<?php
						foreach ( $this->header_menu_items() as $index => $item ) {
							$active = '';
							if (
								isset( $get_payload, $get_payload['action'] ) && in_array(
									$get_payload['action'], $item['menu']
								)
							) {
								$active = 'active';
							}
							if ( ! isset( $get_payload['action'] ) && ( 'fed_membership_pro' === $get_payload['page'] ) && ( 'FEDMPPRO@show' === $index ) ) {
								$active = 'active';
							}
							?>
							<li class="<?php echo esc_attr( $active ); ?>">
								<a href="
								<?php
								echo esc_url(
									fed_menu_page_url(
										'fed_membership_pro', array(
											'action' => $index,
										)
									)
								);
								?>
								">
									<i class="<?php echo esc_attr( $item['icon'] ); ?>"></i>
									<?php
									echo esc_attr( $item['name'] );
									?>
								</a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php
		}


		/**
		 * Header Menu Items.
		 *
		 * @return array
		 */
		public function header_menu_items() {
			return array(
				'FEDMPPRO@membership' => array(
					'icon' => 'fa fa-user',
					'name' => 'Membership',
					'menu' => array( 'FEDMPPRO@membership' ),
				),
				'FEDMPPRO@template'   => array(
					'icon' => 'fa fa-paint-brush',
					'name' => 'Template',
					'menu' => array( 'FEDMPPRO@template' ),
				),
				'FEDMPPRO@settings'   => array(
					'icon' => 'fa fa-cogs',
					'name' => 'Settings',
					'menu' => array( 'FEDMPPRO@settings' ),
				),
			);
		}

		/**
		 * Membership.
		 */
		public function membership() {
			$this->pro();
		}

		/**
		 * Settings.
		 */
		public function settings() {
			$this->pro();
		}

		/**
		 * Template.
		 */
		public function template() {
			$this->pro();
		}

		/**
		 * Pro.
		 */
		public function pro() {
			?>
			<div class="bc_fed">
				<div class="row">
					<div class="col-md-10">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title"><?php esc_attr_e(
										'Membership Pro', 'frontend-dashboard'
									); ?></h3>
							</div>
							<div class="panel-body">
								<div class="row m-b-20">
									<div class="col-md-4">
										<form method="post"
												action="https://buffercode.com/payment/bc/payment_start">
											<input type='hidden' name='redirect_url'
													value="<?php echo fed_current_page_url(); ?>"/>
											<input type='hidden' name='domain'
													value="<?php echo esc_textarea(fed_get_domain_name()); ?>"/>
											<input type='hidden' name='contact_email'
													value="<?php echo esc_textarea( fed_get_admin_email() ); ?>"/>
											<input type='hidden' name='plugin_name'
													value='frontend-dashboard-membership-pro'/>
											<input type='hidden' name='amount' value='29'/>
											<input type='hidden' name='plan_type' value='annual'/>
											<button type="submit" style="
													background:url(<?php echo esc_url(
												plugins_url(
													'assets/admin/images/pro/buy-now-29.png',
													BC_FED_PLUGIN
												)
											) ?>);
													background-repeat: no-repeat;
													width:200px;
													height: 148px;
													border: 0;">
											</button>
										</form>
									</div>
									<div class="col-md-4">
										<form method="post"
												action="https://buffercode.com/payment/bc/payment_start">
											<input type='hidden' name='redirect_url'
													value="<?php echo fed_current_page_url(); ?>"/>
											<input type='hidden' name='domain'
													value="<?php echo fed_get_domain_name(); ?>"/>
											<input type='hidden' name='contact_email'
													value="<?php echo fed_get_admin_email(); ?>"/>
											<input type='hidden' name='plugin_name'
													value='frontend-dashboard-membership-pro'/>
											<input type='hidden' name='amount' value='99'/>
											<input type='hidden' name='plan_type' value='lifetime'/>
											<button type="submit" style="
													background:url(<?php echo plugins_url(
												'assets/admin/images/pro/buy-now-99.png',
												BC_FED_PLUGIN
											) ?>);
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
													href="https://buffercode.com/plugin/frontend-dashboard-membership-pro">
												Frontend Dashboard
												Membership Pro
											</a>
										</h4>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}

	new FEDMPPRO();
}