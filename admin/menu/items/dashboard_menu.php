<?php

function fed_get_dashboard_menu_items()
{
    $menus      = fed_fetch_table_rows_with_key(BC_FED_MENU_DB, 'menu_slug');
    $user_roles = fed_get_user_roles();

    if (isset($_GET, $_GET['sort'])) {
        ?>
        <div class="bc_fed container" style="position: relative;">
            <?php fed_get_dashboard_menu_items_sort() ?>
        </div>
        <?php
    } else {
        ?>
        <div class="bc_fed container">
            <!-- Show Empty form to add Dashboard Menu-->
            <?php fed_get_dashboard_menu_items_add($menus, $user_roles); ?>

            <!--List / Edit Dashboard Menus-->
            <?php fed_get_dashboard_menu_items_list($menus, $user_roles); ?>

            <?php fed_menu_icons_popup(); ?>
        </div>
        <?php
    }
}

/**
 * @param $menus
 * @param $user_roles
 */
function fed_get_dashboard_menu_items_add($menus, $user_roles)
{
    ?>
    <div class="row padd_top_20 hide"
         id="fed_add_new_menu_container">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <b><?php _e('Add New Menu', 'frontend-dashboard') ?></b>
                    </h3>
                </div>
                <div class="panel-body">
                    <form method="post"
                          class="fed_admin_menu fed_menu_ajax"
                          action="<?php echo admin_url('admin-ajax.php?action=fed_admin_setting_form_dashboard_menu') ?>">
                        <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php _e('Menu Name', 'frontend-dashboard') ?></label>
                                            <input type="text"
                                                   title="Menu Name"
                                                   name="fed_menu_name"
                                                   class="form-control fed_menu_name"
                                                   value=""
                                            />
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>
                                                <?php _e('Menu Slug', 'frontend-dashboard') ?>
                                                <?php echo fed_show_help_message(array(
                                                        'title'   => 'Info',
                                                        'content' => 'Please do not change the Slug until its mandatory',
                                                ));
                                                ?>
                                            </label>
                                            <input type="text"
                                                   title="Menu Slug"
                                                   name="fed_menu_slug"
                                                   class="form-control fed_menu_slug"
                                                   value=""
                                            />
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?php _e('Menu Icon', 'frontend-dashboard') ?></label>
                                            <input type="text"
                                                   name="menu_image_id"
                                                   class="form-control menu_image_id"
                                                   data-toggle="modal"
                                                   data-target=".fed_show_fa_list"
                                                   placeholder=""
                                                   data-fed_menu_box_id="menu_image_id"

                                            />
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?php _e('Menu Order', 'frontend-dashboard') ?></label>
                                            <input type="number"
                                                   name="fed_menu_order"
                                                   class="form-control fed_menu_order"
                                                   placeholder=""
                                            />
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php _e('Disable User Profile',
                                                        'frontend-dashboard') ?></label>
                                            <br>
                                            <input title="Disable User Profile" type="checkbox"
                                                   name="show_user_profile"
                                                   value="Disable"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="row padd_top_20">
                                    <div class="col-md-12">
                                        <label><?php _e('Select user role to show this input field',
                                                    'frontend-dashboard') ?></label>
                                    </div>
                                    <div class="col-md-12 ">
                                        <?php
                                        foreach ($user_roles as $key => $user_role) {
                                            ?>
                                            <div class="col-md-2">
                                                <?php echo fed_input_box('user_role', array(
                                                        'default_value' => 'Enable',
                                                        'name'          => 'user_role['.$key.']',
                                                        'label'         => esc_attr($user_role),
                                                        'value'         => 'Enable',
                                                ), 'checkbox'); ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="col-md-12">
                                    <div class="form-group fed_menu_save_button">
                                        <button type="submit"
                                                class="btn btn-primary fed_menu_save">
                                            <i class="fa fa-plus"></i>
                                            <?php _e('Add New Menu', 'frontend-dashboard') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php do_action('fed_add_main_menu_item_bottom') ?>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php

}

/**
 * @param $menus
 * @param $user_roles
 */
function fed_get_dashboard_menu_items_list($menus, $user_roles)
{
    ?>
    <div class="row padd_top_20">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <b><?php _e('Menu Lists', 'frontend-dashboard') ?></b>
                        <a href="<?php echo menu_page_url('fed_dashboard_menu', false).'&sort=yes' ?>"
                           class="btn btn-secondary fed_btn_padd_5 fed_float_right">
                            <i class="fas fa-sort-amount-up"></i>
                            Sort Menu
                        </a>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="fed_dashboard_menu_items_container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button type="button"
                                            role="link"
                                            class="btn btn-primary fed_menu_save fed_menu_save_button_toggle">
                                        <i class="fa fa-plus"></i>
                                        <?php _e('Add New Menu', 'frontend-dashboard') ?>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group fed_search_box  col-md-10">
                                    <input id="fed_menu_search" type="text" class="form-control
												fed_menu_search"
                                           placeholder="<?php _e('Search Menu Name...',
                                                   'frontend-dashboard') ?>">
                                    <span class="input-group-btn">
										        <button class="btn btn-danger fed_menu_search_clear hide" type="button">
													<i class="fa fa-times-circle" aria-hidden="true"></i>
												</button>
										      </span>
                                </div>
                            </div>
                        </div>
                        <div class="panel-group fed_sort_menu"
                             data-url="<?php echo admin_url('admin-ajax.php?action=fed_admin_menu_sorting&table=fed_menu&fed_nonce='.wp_create_nonce('fed_nonce')) ?>"
                             id="fedmenu" role="tablist" aria-multiselectable="true">
                            <?php
                            $collapse = 0;
                            foreach ($menus as $index => $menu) {
                                if ($collapse === 0) {
                                    $collapsed = '';
                                    $in        = 'in';
                                } else {
                                    $collapsed = 'collapsed';
                                    $in        = '';
                                }

                                $collapse++;
                                ?>
                                <div class="fed_dashboard_menu_single_item ui-state-default <?php echo $menu['menu'] ?>"
                                     id="<?php echo $menu['id'] ?>">
                                    <div class="panel panel-secondary-heading">
                                        <div class="panel-heading <?php echo $collapsed; ?>"
                                             role="tab" id="<?php echo $index ?>" data-toggle="collapse"
                                             data-parent="#fedmenu" href="#collapse<?php echo $index ?>"
                                             aria-expanded="true" aria-controls="collapse<?php echo $index
                                        ?>">
                                            <h4 class="panel-title">
                                                <a>
                                                    <?php
                                                    echo '<span class="'.$menu['menu_image_id'].'"></span>'; ?>
                                                    <?php esc_attr_e($menu['menu']) ?>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse<?php echo $index ?>"
                                             class="panel-collapse collapse <?php echo $in ?>"
                                             role="tabpanel" aria-labelledby="<?php echo $index ?>">
                                            <div class="panel-body">
                                                <form method="post"
                                                      class="fed_admin_menu fed_menu_ajax"
                                                      action="<?php echo admin_url('admin-ajax.php?action=fed_admin_setting_form_dashboard_menu') ?>">

                                                    <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>
                                                    <input type="hidden"
                                                           name="menu_id"
                                                           value="<?php echo $menu['id'] ?>"/>
                                                    <input type="hidden"
                                                           name="fed_menu_slug"
                                                           value="<?php echo esc_attr($menu['menu_slug']) ?>"
                                                    />
                                                    <div class="row">
                                                        <div class="col-md-10">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label><?php _e('Menu Name',
                                                                                        'frontend-dashboard') ?></label>
                                                                            <input type="text"
                                                                                   name="fed_menu_name"
                                                                                   class="form-control fed_menu_name"
                                                                                   value="<?php echo esc_attr($menu['menu']) ?>"
                                                                                   required="required"
                                                                                   placeholder="Menu Name"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label><?php _e('Menu Icon',
                                                                                        'frontend-dashboard') ?></label>
                                                                            <input type="text"
                                                                                   name="menu_image_id"
                                                                                   class="form-control <?php echo esc_attr($menu['menu_slug']) ?>"
                                                                                   value="<?php echo esc_attr($menu['menu_image_id']) ?>"
                                                                                   data-toggle="modal"
                                                                                   data-target=".fed_show_fa_list"
                                                                                   placeholder="Menu Icon"
                                                                                   data-fed_menu_box_id="<?php echo esc_attr($menu['menu_slug']) ?>"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 hide">
                                                                        <div class="form-group">
                                                                            <label><?php _e('Menu Order',
                                                                                        'frontend-dashboard') ?></label>
                                                                            <input type="number"
                                                                                   name="fed_menu_order"
                                                                                   class="form-control fed_menu_order"
                                                                                   value="<?php echo esc_attr(
                                                                                           $menu['menu_order']) ?>"
                                                                                   required="required"
                                                                                   placeholder="Menu Order"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group text-center">
                                                                            <?php
                                                                            echo fed_input_box('show_user_profile',
                                                                                    array(
                                                                                            'default_value' => 'Disable',
                                                                                            'label'         => __('Disable User Profile',
                                                                                                    'frontend-dashboard'),
                                                                                            'value'         => esc_attr($menu['show_user_profile']),
                                                                                    ), 'checkbox');
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <button type="submit"
                                                                            class="btn btn-primary fed_menu_save"
                                                                    >
                                                                        <i class="fa fa-save"></i>
                                                                    </button>
                                                                    <?php if ($menu['extra'] !== 'no') { ?>
                                                                        <button type="submit"
                                                                                class="btn btn-danger fed_menu_delete">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-12 padd_top_10">
                                                                    <div class="col-md-2">
                                                                        <label><?php _e('Select User Role(s)') ?></label>
                                                                    </div>
                                                                    <?php
                                                                    foreach ($user_roles as $key => $user_role) {
                                                                        $res     = isset($menu['user_role']) ? $menu['user_role'] : false;
                                                                        $c_value = 'Disable';
                                                                        if ($res) {
                                                                            $c_value = in_array($key,
                                                                                    unserialize($res),
                                                                                    false) ? 'Enable' : 'Disable';
                                                                        }

                                                                        ?>
                                                                        <div class="col-md-2">
                                                                            <?php echo fed_input_box('user_role',
                                                                                    array(
                                                                                            'default_value' => 'Enable',
                                                                                            'name'          => 'user_role['.$key.']',
                                                                                            'label'         => esc_attr($user_role),
                                                                                            'value'         => $c_value,
                                                                                    ), 'checkbox'); ?>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <?php do_action('fed_edit_main_menu_item_bottom',
                                                            $menu) ?>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
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

function fed_get_dashboard_menu_items_sort()
{

    wp_enqueue_script('fed_menu_sort', plugins_url('admin/assets/fed_menu_sort.js', BC_FED_PLUGIN), array('jquery'));
    $menus = fed_get_all_dashboard_display_menus();
    uasort($menus, 'fed_sort_by_order');
//    bcdump($menus);
    $new_menu = array();
    foreach ($menus as $index => $menu) {
        $parent_id  = isset($menu['parent_id']) ? $menu['parent_id'] : 0;
        $menu_order = isset($menu['menu_order']) ? $menu['menu_order'] : mt_rand(99, 99999);

        if ($parent_id == '0') {
            $new_menu[$menu_order] = $menu;
        } else {
            $new_menu[$parent_id]['submenu'][$menu['menu_slug']] = $menu;
        }

    }
    ksort($new_menu, 1);

//    var_dump($new_menu);

    /**
     * if menu_type == user
     * Update Menu Table
     *
     * if menu_type == post
     * Update get_option('fed_cp_admin_settings') menu of parent_id
     *
     * if menu_type == custom
     * Update
     */
    ?>
    <ul class="fed_dashboard_menu_sort listsClass" id="fed_dashboard_menu_sort" data-nonce="<?php echo wp_create_nonce('fed_nonce') ?>" data-url="<?php echo fed_get_form_action('fed_menu_sorting_items'); ?>">
        <?php
        foreach ($new_menu as $newMenu) {
            $isOpen    = '';
            $isSubmenu = false;
            if (isset($newMenu['submenu']) && count($newMenu['submenu'])) {
                $isOpen    = 'sortableListsOpen';
                $isSubmenu = true;
            }
            ?>
            <li class="<?php echo $isOpen; ?>" id="<?php echo $newMenu['menu_type'].'_'.$newMenu['id']; ?>"
                data-module="<?php echo $newMenu['menu_type']; ?>">
                <div><?php echo $newMenu['menu'] ?></div>
                <?php if ($isSubmenu) { ?>
                    <ul class="">
                        <?php foreach ($newMenu['submenu'] as $newSubMenu) { ?>
                            <li id="<?php echo $newSubMenu['menu_type'].'_'.$newSubMenu['id'] ?>"
                                data-module="<?php echo $newSubMenu['menu_type']; ?>">
                                <div><?php echo $newSubMenu['menu']; ?></div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
}