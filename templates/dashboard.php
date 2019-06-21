<?php
/**
 * Dashboard Page
 *
 * @package frontend-dashboard
 */

$dashboard_container = new FED_Routes($_REQUEST);
$menu                = $dashboard_container->setDashboardMenuQuery();

do_action('fed_before_dashboard_container');
$is_mobile = fed_get_menu_mobile_attributes();
?>
    <div class="bc_fed fed_dashboard_container">
        <?php echo fed_loader() ?>
        <?php do_action('fed_inside_dashboard_container_top'); ?>
        <?php echo fed_show_alert('fed_dashboard_top_message') ?>
        <?php if ( ! $menu instanceof WP_Error) { ?>
            <div class="row fed_dashboard_wrapper">
                <div class="col-md-3 fed_dashboard_menus default_template">
                    <div class="custom-collapse fed_menu_items">
                        <button class="bg_secondary collapse-toggle visible-xs visible-sm <?php echo $is_mobile['d']; ?>"
                                type="button"
                                data-toggle="collapse"
                                role="button"
                                data-target="#fed_default_template"
                                aria-expanded="<?php echo $is_mobile['expand']; ?>">
                            <span class=""><i class="fa fa-bars"></i> Menu</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="fed_frontend_dashboard_menu collapse <?php echo $is_mobile['in']; ?>"
                             id="fed_default_template">
                            <nav>
                                <?php
                                fed_display_dashboard_menu($menu);

                                fed_get_collapse_menu() ?>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="col-md-9 fed_dashboard_items">
                    <?php
                    $dashboard_container->getDashboardContent($menu);
                    ?>
                </div>
            </div>
        <?php }
        if ($menu instanceof WP_Error) {
            ?>
            <div class="row fed_dashboard_wrapper fed_error">
                <?php fed_get_403_error_page() ?>
            </div>
            <?php
        } ?>
        <?php do_action('fed_inside_dashboard_container_bottom'); ?>
    </div>
<?php
do_action('fed_after_dashboard_container');

