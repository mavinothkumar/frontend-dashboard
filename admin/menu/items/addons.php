<?php
if ( ! defined('ABSPATH')) {
    exit;
}

function fed_get_plugin_pages_menu()
{
    if (false === ($api = get_transient('fed_plugin_list_api'))) {
        $api = get_plugin_list();
    }
    if ($api) {
        set_transient('fed_plugin_list_api', $api, 12 * HOUR_IN_SECONDS);
        $plugins = json_decode($api);
        ?>
        <div class="bc_fed container fed_plugins">
            <div class="row  padd_top_20">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <?php _e('Add-Ons', 'frontend-dashboard') ?>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <?php echo fed_loader('hide', 'Please wait, its working') ?>
                            <div class="row m-b-10">
                                <div class="col-md-12">
                                    <a target="_blank" class="btn btn-secondary btn-lg" href="https://demo.frontenddashboard.com">
                                        <i class="fa fa-eye"></i>
                                        Frontend Dashboard Demo
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo fed_show_alert('fed_activation_message'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <?php foreach ($plugins->plugins as $slug=>$single) {
                                    if ($single->pricing->type === 'Free') {
                                        $type = '<i class="fas fa-lock-open"></i>';
                                        $bgColor = '';
                                    }
                                	else {
                                        $type = '<i class="fas fa-lock"></i>';
                                        $bgColor = 'bg_pro_color';
                                    }
                                    ?>
                                    <div class="col-md-12">
                                        <div class="padd_5 margin_bottom_20 fed_border_2px fed_font_size_20">
                                            <div class="row  <?php echo $bgColor; ?>">
                                                <div class="col-md-1">
                                                    <img class="img-responsive" width="100px"
                                                         src="<?php echo $single->thumbnail; ?>"
                                                         alt="">
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="fed_addons_title fed_flex_space_between">
                                                        <div class="fed_p_b_10">
                                                            <?php echo $single->title; ?>
                                                            <small>
                                                                (
                                                                v
                                                                <?php
                                                                if (is_plugin_active($single->directory)) {
                                                                    echo constant($single->id.'_VERSION');
                                                                } else {
                                                                    echo $single->version;
                                                                } ?>
                                                                )
                                                            </small>
                                                        </div>
                                                        <div class="">
                                                            <div class="fed_p_l_10">
                                                                <?php echo $type ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="fed_addons_description">
                                                        <p>
                                                            <?php echo wp_trim_words($single->description,
                                                                    100); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="fed_plugin_link">
                                                        <div class="fed_flex_space_between">
                                                            <?php
                                                            if (is_plugin_active($single->directory)) {
                                                                ?>
                                                                <button class="btn btn-info">
                                                                    <i class="fa fa-check"
                                                                       aria-hidden="true"></i>
                                                                    Installed
                                                                </button>
                                                                <?php
                                                                if ($single->version > constant($single->id.'_VERSION'
                                                                        )) {
                                                                    ?>
                                                                    <button class="btn btn-danger">
                                                                        <i class="fa fa-refresh"
                                                                           aria-hidden="true"></i>
                                                                        Update
                                                                    </button>
                                                                    <?php
                                                                }

                                                            } else {
                                                                                                                                if ($single->pricing->type === 'Free') {
                                                                    $path = WP_PLUGIN_DIR.'/'.$single->install_slug;
                                                                    if (is_dir($path)) {
                                                                        ?>
                                                                        <form method="post"
                                                                              action="<?php echo fed_get_form_action('fed_request').'&fed_action_hook=FEDInstallAddons@activate'; ?>">
                                                                            <?php fed_wp_nonce_field(); ?>
                                                                            <input type="hidden" name="plugin_name" value="<?php echo $single->install_slug.'/'.$single->install_slug.'.php'; ?>" />
                                                                            <button type="submit"
                                                                                    class="btn btn-primary">
                                                                                Activate
                                                                            </button>
                                                                        </form>
                                                                        <?php
                                                                    } else {
                                                                        ?>
                                                                        <form method="post"
                                                                              class="fed_ajax_plugin_install"
                                                                              action="<?php echo fed_get_ajax_form_action('fed_api_ajax_request').'&fed_action_hook=FEDInstallAddons@install'; ?>">
                                                                            <?php wp_nonce_field('updates') ?>
                                                                            <input type="hidden" name="slug"
                                                                                   value="<?php echo isset($single->install_slug) ? $single->install_slug : $slug; ?>">
                                                                            <button type="submit"
                                                                                    class="btn btn-primary">
                                                                                <i class="fa fa-download"
                                                                                   aria-hidden="true"></i> <?php _e('Install & Activate',
                                                                                    'frontend-dashboard') ?></button>
                                                                        </form>
                                                                        <?php
                                                                    }
                                                                }
                                                                if ($single->pricing->type === 'Pro') {
                                                                    ?>
                                                                    <?php
                                                                    foreach ($single->pricing->amount as $atype => $amount) {
                                                                        ?>
                                                                        <form method="post"
                                                                              action="<?php echo $single->pricing->purchase_url; ?>">
                                                                            <input type='hidden' name='redirect_url'
                                                                                   value="<?php echo fed_current_page_url(); ?>"/>
                                                                            <input type='hidden' name='domain'
                                                                                   value="<?php echo fed_get_domain_name(); ?>"/>
                                                                            <input type='hidden'
                                                                                   name='contact_email'
                                                                                   value="<?php echo fed_get_admin_email(); ?>"/>
                                                                            <input type='hidden' name='plugin_name'
                                                                                   value='<?php echo $slug; ?>'/>
                                                                            <input type='hidden' name='amount'
                                                                                   value='<?php echo $amount->amount; ?>'/>
                                                                            <input type='hidden' name='plan_type'
                                                                                   value='<?php echo $atype;?>' />
                                                                            <button type="submit"
                                                                                    class="btn btn-primary">
                                                                                <i class="fa fa-shopping-cart"
                                                                                   aria-hidden="true"></i>
                                                                                <?php echo 'Buy '.$amount->name.' '.$single->pricing->currency.$amount->amount; ?>
                                                                            </button>
                                                                        </form>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                            <a href="<?php echo $single->download_url ?>">
                                                                <button class="btn btn-warning">
                                                                    <i class="fa fa-eye"
                                                                       aria-hidden="true"></i>
                                                                    View
                                                                </button>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php
    } else {
        ?>
        <div class="bc_fed container fed_plugins">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong><?php _e('Sorry there is some issue in internet connectivity.',
                            'frontend-dashboard') ?></strong>
            </div>
            <?php echo fed_loader(''); ?>
        </div>
        <?php
    }

}