<?php

function fed_get_post_fields_menu()
{
    $post_fields = fed_fetch_table_rows_with_key(BC_FED_POST_DB, 'input_meta');
    if ($post_fields instanceof WP_Error) {
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
                        <strong><?php echo $post_fields->get_error_message() ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        fed_get_post_fields_menu_item($post_fields);
    }
}

/**
 * @param $profiles
 */
function fed_get_post_fields_menu_item($profiles)
{
    ?>
    <div class="bc_fed container fed_post_container">
        <div class="row">
            <div class="col-md-6">
                <div class="fed_UP_page_header">
                    <h3 class="fed_header_font_color"><?php _e('Post Fields', 'frontend-dashboard') ?></h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="fed_UP_select_container">
                    <div class="fed_UP_input_select">
                        <a class="btn btn-primary"
                           href="<?php echo menu_page_url('fed_add_user_profile', false).'&fed_action=post' ?>">
                            <i class="fa fa-plus"></i>
                            <?php _e('Add New Custom Post Field', 'frontend-dashboard') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (count($profiles) <= 0) {
            ?>
            <div class="row fed_alert_danger">
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        <button type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-hidden="true">&times;
                        </button>
                        <strong>
                            <?php _e('Sorry! there are no custom post fields added!', 'frontend-dashboard') ?>
                        </strong>
                    </div>
                </div>
            </div>
            <?php
        } else {
            usort($profiles, 'fed_sort_by_order');
            $menu = fed_get_public_post_types();
            ?>
            <div class="row">
                <div class="col-md-12 fed_admin_profile_container">
                    <div class="row">
                        <div class="col-md-3">
                            <ul class="nav nav-pills nav-stacked" role="tablist">
                                <?php
                                $profilesValue = fed_array_group_by_key($profiles, 'post_type');
                                $groupBy       = fed_compare_two_arrays_get_second_value($menu, $profilesValue);
                                $count         = 0;
                                foreach ($groupBy as $index => $group) {
                                    $isActive = $count === 0 ? 'active' : '';
                                    ?>
                                    <li class="<?php echo $isActive; ?>">
                                        <a href="#<?php echo $index ?>" role="tab"
                                           data-toggle="tab"><?php echo $menu[$index] ?></a>
                                    </li>
                                    <?php
                                    $count++;
                                }
                                ?>
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
                                                <h3 class="panel-title"><?php echo $menu[$index]; ?></h3>
                                            </div>
                                            <div class="panel-body fed_sort_menu"
                                                 data-url="<?php echo admin_url('admin-ajax.php?action=fed_admin_menu_sorting&table=fed_post&fed_nonce='.wp_create_nonce('fed_nonce')) ?>">
                                                <?php

                                                foreach ($group as $profile) {
                                                    ?>
                                                    <div class="row fed_single_profile ui-state-default"
                                                         id="<?php echo $profile['id'] ?>">
                                                        <form method="post"
                                                              class="fed_user_profile_delete fed_profile_ajax"
                                                              action="<?php echo admin_url('admin-ajax.php?action=fed_user_profile_delete') ?>">
                                                            <?php fed_wp_nonce_field('fed_nonce',
                                                                    'fed_nonce') ?>
                                                            <input type="hidden"
                                                                   name="post_id"
                                                                   value="<?php echo $profile['id'] ?>">

                                                            <input type="hidden"
                                                                   name="profile_name"
                                                                   value="<?php echo $profile['label_name'] ?>">
                                                            <div class="col-md-6">
                                                                <label class="control-label">
                                                                    <?php
                                                                    echo $profile['label_name'].fed_is_required($profile['is_required']); ?>
                                                                </label>
                                                                <?php echo fed_get_input_details($profile) ?>
                                                            </div>

                                                            <div class="col-md-6 p-t-20">
                                                                <a class="btn btn-primary"
                                                                   href="<?php echo menu_page_url('fed_add_user_profile',
                                                                                   false).'&fed_input_id='.$profile['id'].'&fed_action=post' ?>">
                                                                    <i class="fa fa-pencil"
                                                                       aria-hidden="true"></i>
                                                                </a>
                                                                <?php if ( ! fed_check_field_is_belongs_to_extra($profile['input_meta'])) { ?>
                                                                    <button class="btn btn-danger fed_profile_delete">
                                                                        <i class="fa fa-trash"
                                                                           aria-hidden="true"></i>
                                                                    </button>
                                                                <?php } ?>
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
            <?php
        }
        ?>
    </div>
    <?php
}