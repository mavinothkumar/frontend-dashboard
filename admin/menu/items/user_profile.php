<?php

function fed_get_user_profile_menu()
{
    $user_profile = fed_fetch_table_rows_with_key(BC_FED_USER_PROFILE_DB, 'input_meta');

    if ($user_profile instanceof WP_Error) {
        ?>
        <div class="bc_fed container fed_UP_container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        <button type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-hidden="true">&times;
                        </button>
                        <strong><?php echo $user_profile->get_error_message() ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        fed_get_user_profile_menu_items($user_profile);
    }
}

/**
 * @param $profiles
 */
function fed_get_user_profile_menu_items($profiles)
{
    usort($profiles, 'fed_sort_by_order');
    $_menu = fed_fetch_menu();
    usort($_menu, 'fed_sort_by_order');
    $menu     = fed_get_key_value_array($_menu, 'menu_slug');
    $menu_key = fed_get_key_value_array($menu, 'menu_slug', 'menu');
    ?>
    <div class="bc_fed container fed_tabs_container fed_UP_container">
        <div class="row">
            <div class="col-md-6">
                <div class="fed_UP_page_header">
                    <h3 class="fed_header_font_color">
                        <?php _e('User Profile', 'frontend-dashboard') ?>
                    </h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="fed_UP_select_container">
                    <div class="fed_UP_input_select">
                        <a class="btn btn-primary"
                           href="<?php echo menu_page_url('fed_add_user_profile',
                                           false).'&fed_action=profile' ?>">
                            <i class="fa fa-plus"></i>
                            <?php _e('Add New Extra User Profile Field', 'frontend-dashboard') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 fed_admin_profile_container">
                <div class="row">
                    <div class="col-md-3">
                        <ul class="nav nav-pills nav-stacked" role="tablist">
                            <?php
                            $groupBy = fed_get_menu_value($profiles, $menu_key);
                            $count   = 0;
                            foreach ($groupBy as $index => $group) {
                                $isActive = $count === 0 ? 'active' : '';
                                ?>
                                <li class="<?php echo $isActive; ?>">
                                    <a href="#<?php echo $index ?>" role="tab" data-toggle="tab">
                                        <?php echo '<span class="'.$menu[$index]['menu_image_id'].'"></span>' ?>
                                        <?php esc_attr_e($menu[$index]['menu']) ?>
                                    </a>
                                </li>
                                <?php
                                $count++;
                            }
                            ?>
                            <li class="t-m-20">
                                <a href="<?php menu_page_url('fed_dashboard_menu'); ?>">
                                    <i class="fa fa-plus"></i>
                                    <?php _e('Add New Menu', 'frontend-dashboard') ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-7">
                        <div class="tab-content">
                            <?php
                            $count = 0;
                            foreach ($groupBy as $index => $group) {
                                $isActive = $count === 0 ? 'active' : '';
                                ?>
                                <div class="<?php echo $isActive; ?> tab-pane fade in"
                                     id="<?php echo $index; ?>">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">
                                                <?php echo '<span class="'.$menu[$index]['menu_image_id'].'"></span>' ?>
                                                <?php esc_attr_e($menu[$index]['menu']) ?>
                                            </h3>
                                        </div>
                                        <div class="panel-body fed_sort_menu"
                                             data-url="<?php echo admin_url('admin-ajax.php?action=fed_admin_menu_sorting&table=fed_user_profile&fed_nonce='.wp_create_nonce('fed_nonce')) ?>">
                                            <?php

                                            foreach ($group as $profile) {
                                                $register     = fed_profile_enable_disable($profile['show_register'],
                                                        'register');
                                                $dashboard    = fed_profile_enable_disable($profile['show_dashboard'],
                                                        'dashboard');
                                                $user_profile = fed_profile_enable_disable($profile['show_user_profile'],
                                                        'user_profile');
                                                ?>
                                                <div class="row fed_single_profile ui-state-default"
                                                     id="<?php echo $profile['id'] ?>">
                                                    <form method="post"
                                                          class="fed_user_profile_delete fed_profile_ajax"
                                                          action="<?php echo admin_url('admin-ajax.php?action=fed_user_profile_delete') ?>">
                                                        <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>
                                                        <input type="hidden"
                                                               name="profile_id"
                                                               value="<?php echo $profile['id'] ?>">

                                                        <input type="hidden"
                                                               name="profile_name"
                                                               value="<?php echo $profile['label_name'] ?>">
                                                        <div class="col-md-6">
                                                            <label class="control-label">
                                                                <?php
                                                                printf(__($profile['label_name'],
                                                                        'frontend-dashboard'),
                                                                        $profile['label_name']);
                                                                echo fed_is_required($profile['is_required']); ?>
                                                            </label>
                                                            <?php echo fed_get_input_details($profile) ?>
                                                        </div>

                                                        <div class="col-md-6 p-t-20">
                                                            <?php echo fed_show_help_message(array(
                                                                    'icon'    => 'fa fa-sign-in',
                                                                    'class'   => 'btn '.$register['button'],
                                                                    'title'   => esc_attr($register['title']),
                                                                    'content' => $register['content'],
                                                            )) ?>

                                                            <?php echo fed_show_help_message(array(
                                                                    'icon'    => 'fas fa-tachometer-alt',
                                                                    'class'   => 'btn '.$dashboard['button'],
                                                                    'title'   => esc_attr($dashboard['title']),
                                                                    'content' => $dashboard['content'],
                                                            )) ?>

                                                            <?php echo fed_show_help_message(array(
                                                                    'icon'    => 'fa fa-user',
                                                                    'class'   => 'btn '.$user_profile['button'],
                                                                    'title'   => esc_attr($user_profile['title']),
                                                                    'content' => $user_profile['content'],
                                                            )) ?>

                                                            <span class="p-l-40">
													<a class="btn btn-primary"
                                                       href="<?php echo menu_page_url('fed_add_user_profile',
                                                                       false).'&fed_input_id='.$profile['id'].'&fed_action=profile' ?>">
														<i class="fa fa-pencil" aria-hidden="true"></i>
													</a>
                                                                        <?php if ( ! fed_check_field_is_belongs_to_extra($profile['input_meta'])) { ?>
                                                                            <button class="btn btn-danger fed_profile_delete">
															<i class="fa fa-trash" aria-hidden="true"></i>
														</button>
                                                                        <?php } ?>
													</span>
                                                        </div>

                                                    </form>
                                                </div>

                                            <?php }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $count++;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}