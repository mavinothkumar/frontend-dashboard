<?php
if ( ! defined('ABSPATH')) {
    exit;
}
if ( ! class_exists('FEDPaymentMenu')) {
    /**
     * Class FEDPayments
     */
    class FEDPaymentMenu
    {
        public function index()
        {
            $this->layout();
        }

        public function layout()
        {
            $menus = apply_filters('fed_payment_menu', array(
                'gateway'      => array(
                    'icon'    => 'fa fa-money',
                    'name'    => __('Payment Gateway', 'frontend-dashboard'),
                    'submenu' => array(
                        'FEDPayment@settings' => array(
                            'icon' => 'fa fa-cogs',
                            'name' => __('Settings', 'frontend-dashboard'),
                            'menu' => array('FEDPayment@settings'),
                        ),
                    ),
                ),
                'transactions' => array(
                    'icon'    => 'fa fa-list',
                    'name'    => __('Transactions', 'frontend-dashboard'),
                    'submenu' => 'FEDTransaction@transactions',
                ),
                'invoice'      => array(
                    'icon'    => 'fa fa-receipt',
                    'name'    => __('Invoice', 'frontend-dashboard'),
                    'submenu' => array(
                        'FEDInvoice@details'          => array(
                            'icon' => 'far fa-building',
                            'name' => __('Company Details', 'frontend-dashboard'),
                            'menu' => array('FEDInvoice@details'),
                        ),
                        'FEDInvoiceTemplate@template' => array(
                            'icon' => 'fa fa-paint-brush',
                            'name' => __('Template', 'frontend-dashboard'),
                            'menu' => array('FEDInvoiceTemplate@template'),
                        ),
                        'FEDInvoice@user'             => array(
                            'icon' => 'fa fa-user-times',
                            'name' => __('User Address', 'frontend-dashboard'),
                            'menu' => array('FEDInvoice@user'),
                        ),
                    ),
                ),
            ));
            ?>
            <div class="bc_fed container">
				<?php if (count($menus)) { ?>
					<div class="m-t-10">
						<h3 class="fed_header_font_color">Payments</h3>
						<?php $this->header_menu($menus) ?>
					</div>
					<div class="m-t-10">
						<?php $this->body_content($menus) ?>
					</div>
				<?php } else {
					?>
					<div class="m-t-10">
						<?php _e('Something went wrong', 'frontend-dashboard') ?>
					</div>
					<?php
				} ?>
            </div>
            <?php
        }

        /**
         * @param $menus
         */
        public function header_menu($menus)
        {
            ?>
            <ul class="nav nav-tabs" id="" role="tablist">
                <?php foreach ($menus as $index => $item) {
                    $active = '';
                    if (isset($_GET, $_GET['menu']) && esc_html($_GET['menu']) === $index) {
                        $active = 'active';
                    }
                    if ( ! isset($_GET['menu']) && $_GET['page'] === 'fed_payments' && $index === 'gateway') {
                        $active = 'active';
                    }
                    ?>
                    <li class="<?php echo $active; ?>">
                        <a href="<?php echo fed_menu_page_url('fed_payments', array(
                            'menu' => esc_attr($index),
                        )); ?>">
                            <i class="<?php esc_attr_e(fed_get_data('icon', $item)) ?>"></i>
                            <?php esc_attr_e(fed_get_data('name', $item)) ?>
                        </a>
                    </li>
                <?php } ?>

            </ul>
            <?php
        }

        /**
         * @param $menus
         */
        public function body_content($menus)
        {
            $_menu    = fed_get_data('menu');
            $_submenu = fed_get_data('submenu');
            $menu     = ! empty($_menu) ? esc_html($_menu) : fed_get_first_key_in_array($menus);
            $submenu  = ! empty($_submenu) ? esc_html($_submenu) : false;

            if ($menu) {
                if (isset($menus[$menu]['submenu']) && is_array($menus[$menu]['submenu']) && count($menus[$menu]['submenu'])) {
                    ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="list-group">
                                <?php foreach ($menus[$menu]['submenu'] as $index => $sub_menu) {
                                    $active = '';
                                    if( !$submenu ){
                                        $submenu = fed_get_first_key_in_array($menus[$menu]['submenu']);
                                    }
                                    if( in_array($submenu, $sub_menu['menu']) ){
                                        $active = 'active';
                                    }
                                    ?>
									<a href="<?php echo fed_menu_page_url('fed_payments', array('menu' => $menu, 'submenu' => $index)); ?>" class="list-group-item <?php echo $active; ?>">
                                        <i class="<?php echo esc_html($sub_menu['icon']); ?>"></i> <?php echo esc_attr($sub_menu['name']); ?>
                                    </a>
								<?php } ?>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <i class="<?php echo esc_attr($menus[$menu]['submenu'][$submenu]['icon']); ?>"></i>
                                        <?php echo esc_attr($menus[$menu]['submenu'][$submenu]['name']); ?>
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    if (is_string($submenu)) {
                                        fed_execute_method_by_string($submenu, $_GET);
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php
                } else {
                    if (isset($menus[$menu]['submenu']) && is_string($menus[$menu]['submenu'])) {
                        ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <i class="<?php echo esc_attr($menus[$menu]['icon']); ?>"></i>
                                            <?php echo esc_attr($menus[$menu]['name']); ?></h3>
                                    </div>
                                    <div class="panel-body">
                                        <?php fed_execute_method_by_string($menus[$menu]['submenu'], $_GET); ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php
                    } elseif (isset($menus[$menu])) {
                        ?>
                        <div class="row">
                            <div class="col-md-12">
                                <?php _e('Under Construction', 'frontend-dashboard'); ?>
                            </div>
                        </div>
                        <?php
                    } else { ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong><?php _e('Invalid Menu Item | Error FED|Admin|Payment|FEDPaymentMenu@body_content',
                                    'frontend-dashboard'); ?></strong>
                        </div>
                        <?php
                        FED_Log::writeLog('Invalid Menu Item | Error FED|Admin|Payment|FEDPaymentMenu@body_content');
                    }
                }
            } else {
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong><?php _e('Ah! something missing | Error FED|Admin|Payment|FEDPaymentMenu@body_content',
                                    'frontend-dashboard'); ?></strong>
                        </div>
                        <?php
                        FED_Log::writeLog('Ah! something missing | Error FED|Admin|Payment|FEDPaymentMenu@body_content');
                        ?>
                    </div>
                </div>
                <?php
            }
        }
    }

    new FEDPaymentMenu();
}