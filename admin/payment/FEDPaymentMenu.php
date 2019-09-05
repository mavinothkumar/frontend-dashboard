<?php
if ( ! defined('ABSPATH')) {
    exit;
}
if ( ! class_exists( 'FEDPaymentMenu' ) ) {
	/**
	 * Class FEDPayments
	 */
	class FEDPaymentMenu {
		public function __construct() {

		}

		public function index() {
			$this->layout();
		}

		public function layout() {
			$menus = apply_filters( 'fed_payment_menu', array(
				'gateway'      => array(
					'icon' => 'fa fa-money',
					'name' => 'Payment Gateway',
					'submenu' => array(
						'FEDPayment@settings' => array(
							'icon' => 'fa fa-cogs',
							'name' => 'Settings',
							'menu' => array( 'FEDPayment@settings' ),
						),
					),
				),
				'transactions' => array(
					'icon'   => 'fa fa-list',
					'name'   => 'Transactions',
					'submenu' => 'FEDTransaction@transactions',
				),
			) );
			?>
            <div class="bc_fed">
                <div class="container">
					<?php if ( count( $menus ) ) { ?>
                        <div class="m-t-10">
                            <h3 class="fed_header_font_color">Payments</h3>
							<?php $this->header_menu( $menus ) ?>
                        </div>
                        <div class="m-t-10">
							<?php $this->body_content( $menus ) ?>
                        </div>
					<?php } else {
						?>
                        <div class="m-t-10">
							<?php _e( 'Something went wrong', 'frontend-dashboard' ) ?>
                        </div>
						<?php
					} ?>
                </div>
            </div>
			<?php
		}

		/**
		 * @param $menus
		 */
		public function header_menu( $menus ) {
			?>
            <ul class="nav nav-tabs" id="" role="tablist">
				<?php foreach ( $menus as $index => $item ) {
					$active = '';
					if ( isset( $_GET, $_GET['menu'] ) && esc_html( $_GET['menu'] ) === $index ) {
						$active = 'active';
					}
					if ( ! isset( $_GET['menu'] ) && $_GET['page'] === 'fed_payments' && $index === 'gateway' ) {
						$active = 'active';
					}
					?>
                    <li class="<?php echo $active; ?>">
                        <a href="<?php echo fed_menu_page_url( 'fed_payments', array(
							'menu' => $index,
						) ); ?>">
                            <i class="<?php echo $item['icon']; ?>"></i>
							<?php echo $item['name']; ?>
                        </a>
                    </li>
				<?php } ?>

            </ul>
			<?php
		}

		/**
		 * @param $menus
		 */
		public function body_content( $menus ) {
			$menu    = isset( $_GET, $_GET['menu'] ) && ! empty( $_GET['menu'] ) ? esc_html( $_GET['menu'] ) : fed_get_first_key_in_array( $menus );
			$submenu = isset( $_GET, $_GET['submenu'] ) && ! empty( $_GET['submenu'] ) ? esc_html( $_GET['submenu'] ) : false;

			if ( $menu ) {
				if ( isset( $menus[ $menu ]['submenu'] ) && is_array($menus[ $menu ]['submenu']) && count( $menus[ $menu ]['submenu'] ) ) {
					?>
                    <div class="row">
                        <div class="col-md-3">
                            <ul class="list-group">
								<?php foreach ( $menus[ $menu ]['submenu'] as $index => $sub_menu ) {
									$active = '';
									if ( ! $submenu ) {
										$submenu = fed_get_first_key_in_array( $menus[ $menu ]['submenu'] );
									}
									if ( in_array( $submenu, $sub_menu['menu'] ) ) {
										$active = 'active';
									}
									?>
                                    <li class="list-group-item <?php echo $active; ?>">

                                        <a href="<?php echo fed_menu_page_url( 'fed_payments', array(
											'menu'    => $menu,
											'submenu' => $index,
										) ); ?>">
                                            <i class="<?php echo esc_html( $sub_menu['icon'] ); ?>"></i>
											<?php echo esc_attr( $sub_menu['name'] ); ?>
                                        </a>
                                    </li>
								<?php } ?>
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <i class="<?php echo  esc_attr($menus[$menu]['submenu'][$submenu]['icon']); ?>"></i>
                                        <?php echo esc_attr($menus[$menu]['submenu'][$submenu]['name']); ?>
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    if ( is_string( $submenu ) ) {
                                        fed_execute_method_by_string( $submenu, $_GET );
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>
					<?php
				} else {
					if ( isset( $menus[ $menu ]['submenu'] ) && is_string( $menus[ $menu ]['submenu'] ) ) {
						?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"> <i class="<?php echo  esc_attr($menus[$menu]['icon']); ?>"></i>
                                            <?php echo esc_attr($menus[$menu]['name']); ?></h3>
                                    </div>
                                    <div class="panel-body">
                                        <?php fed_execute_method_by_string( $menus[ $menu ]['submenu'], $_GET ); ?>
                                    </div>
                                </div>

                            </div>
                        </div>
						<?php
					} elseif ( isset( $menus[ $menu ] ) ) {
						?>
                        <div class="row">
                            <div class="col-md-12">
								<?php _e( 'Under Construction', 'frontend-dashboard' ); ?>
                            </div>
                        </div>
						<?php
					} else { ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong><?php _e( 'Invalid Menu Item | Error FED|Admin|Payment|FEDPaymentMenu@body_content',
									'frontend-dashboard' ); ?></strong>
                        </div>
						<?php
						FED_Log::writeLog( 'Invalid Menu Item | Error FED|Admin|Payment|FEDPaymentMenu@body_content' );
					}
				}
			} else {
				?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong><?php _e( 'Ah! something missing | Error FED|Admin|Payment|FEDPaymentMenu@body_content',
		                            'frontend-dashboard' ); ?></strong>
                        </div>
                        <?php
						FED_Log::writeLog( 'Ah! something missing | Error FED|Admin|Payment|FEDPaymentMenu@body_content' );

						?>
                    </div>
                </div>
				<?php
			}
		}
	}

	new FEDPaymentMenu();
}