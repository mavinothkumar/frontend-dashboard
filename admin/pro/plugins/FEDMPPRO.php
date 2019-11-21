<?php
if ( ! defined('ABSPATH')) {
    exit;
}
if ( ! class_exists('FEDMPPRO') && ! defined('BC_FED_MP_PLUGIN')) {
    /**
     * Class FEDMPPRO
     */
    class FEDMPPRO
    {
        public function __construct()
        {
            add_filter('fed_add_main_sub_menu', array(
                $this,
                'main_sub_menu',
            ));
            add_filter('fed_admin_script_loading_pages', array(
                $this,
                'script_loading_pages',
            ));
        }

        public function script_loading_pages($pages)
        {
            return array_merge($pages, array('fed_membership_pro'));
        }

        public function main_sub_menu($menu)
        {
            $menu['fed_membership_pro'] = array(
                'page_title' => __('Membership Pro', 'frontend-dashboard'),
                'menu_title' => __('Membership Pro', 'frontend-dashboard'),
                'capability' => 'manage_options',
                'callback'   => array($this, 'menu'),
                'position'   => 30,
            );

            return $menu;
        }

        public function menu()
        {
            $action = isset($_GET, $_GET['action']) && ! empty($_GET['action']) ? urldecode($_GET['action']) : false;
            $this->header_menu();
            if ($action) {
                $page = in_array($action, $this->page_list()) ? $action : array();
                if (is_string($page)) {
                    $action = true;
                    fed_execute_method_by_string($page, $_GET);
                }
            }
            if ($action === false) {
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

        public function page_list()
        {
            $menus     = $this->header_menu_items();
            $menu_item = array();
            foreach ($menus as $index => $menu) {
                $menu_item = array_merge($menu['menu'], $menu_item);
            }

            return $menu_item;
        }

        public function header_menu()
        {
            ?>
            <div class="bc_fed">
                <div class="m-t-10">
                    <ul class="nav nav-tabs" id="" role="tablist">
                        <?php foreach ($this->header_menu_items() as $index => $item) {
                            $active = '';
                            if (isset($_GET, $_GET['action']) && in_array($_GET['action'], $item['menu'])) {
                                $active = 'active';
                            }
                            if ( ! isset($_GET['action']) && $_GET['page'] === 'fed_membership_pro' && $index === 'FEDMPPRO@show') {
                                $active = 'active';
                            }
                            ?>
                            <li class="<?php echo $active; ?>">
                                <a href="<?php echo fed_menu_page_url('fed_membership_pro', array(
                                    'action' => $index,
                                )); ?>">
                                    <i class="<?php echo $item['icon']; ?>"></i>
                                    <?php echo $item['name']; ?>
                                </a>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
            </div>
            <?php
        }


        /**
         * @return array
         */
        public function header_menu_items()
        {
            return array(
                'FEDMPPRO@membership'  => array(
                    'icon' => 'fa fa-user',
                    'name' => 'Membership',
                    'menu' => array('FEDMPPRO@membership'),
                ),
                'FEDMPPRO@template'    => array(
                    'icon' => 'fa fa-paint-brush',
                    'name' => 'Template',
                    'menu' => array('FEDMPPRO@template'),
                ), 'FEDMPPRO@settings' => array(
                    'icon' => 'fa fa-cogs',
                    'name' => 'Settings',
                    'menu' => array('FEDMPPRO@settings'),
                ),
            );

        }

        public function membership()
        {
            $this->pro();
        }

        public function settings()
        {
            $this->pro();
        }

        public function template()
        {
            $this->pro();
        }

        public function pro()
        {
            ?>
            <div class="bc_fed">
                <div class="row">
                    <div class="col-md-10">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Membership Pro</h3>
                            </div>
                            <div class="panel-body">
                                <div class="row m-b-20">
                                    <div class="col-md-4">
                                        <form method="post"
                                              action="https://buffercode.com/payment/bc/payment_start">
                                            <input type='hidden' name='redirect_url' value="<?php echo fed_current_page_url(); ?>"/>
                                            <input type='hidden' name='domain' value="<?php echo fed_get_domain_name(); ?>"/>
                                            <input type='hidden' name='contact_email' value="<?php echo fed_get_admin_email(); ?>"/>
                                            <input type='hidden' name='plugin_name' value='frontend-dashboard-membership-pro'/>
                                            <input type='hidden' name='amount' value='29'/>
                                            <input type='hidden' name='plan_type' value='annual'/>
                                            <button type="submit" style="
                                                    background:url(<?php echo plugins_url('admin/assets/images/pro/buy-now-29.png',
                                                BC_FED_PLUGIN) ?>);
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
                                            <input type='hidden' name='redirect_url' value="<?php echo fed_current_page_url(); ?>"/>
                                            <input type='hidden' name='domain' value="<?php echo fed_get_domain_name(); ?>"/>
                                            <input type='hidden' name='contact_email' value="<?php echo fed_get_admin_email(); ?>"/>
                                            <input type='hidden' name='plugin_name' value='frontend-dashboard-membership-pro'/>
                                            <input type='hidden' name='amount' value='99'/>
                                            <input type='hidden' name='plan_type' value='lifetime'/>
                                            <button type="submit" style="
                                                    background:url(<?php echo plugins_url('admin/assets/images/pro/buy-now-99.png',
                                                BC_FED_PLUGIN) ?>);
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
                                            <a target="_blank" href="https://buffercode.com/plugin/frontend-dashboard-membership-pro">
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