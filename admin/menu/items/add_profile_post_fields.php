<?php
function fed_get_add_profile_post_fields()
{
    $id              = '';
    $add_edit_action = __('Add New ', 'frontend-dashboard');
    $selected        = '';
    $action          = isset($_GET['fed_action']) ? esc_attr($_GET['fed_action']) : '';
    if (isset($_GET['fed_input_id'])) {
        $id              = (int) $_GET['fed_input_id'];
        $add_edit_action = __('Edit ', 'frontend-dashboard');
    }

    if ($action !== 'profile' && $action !== 'post') {
        ?>
        <div class="bc_fed container fed_add_page_profile_container text-center">
            <a class="btn btn-primary"
               href="<?php echo menu_page_url('fed_add_user_profile', false).'&fed_action=post' ?>">
                <i class="fa fa-envelope"></i>
                <?php _e('Add Extra Post Field', 'frontend-dashboard') ?>
            </a>
            <a class="btn btn-primary"
               href="<?php echo menu_page_url('fed_add_user_profile', false).'&fed_action=profile' ?>">
                <i class="fa fa-user-plus"></i>
                <?php _e('Add Extra User Profile Field', 'frontend-dashboard') ?>
            </a>
        </div>
        <?php
        return;
    }

    if ($action === 'profile') {
        $page = __('Profile', 'frontend-dashboard');
        $url  = menu_page_url('fed_user_profile', false);
        if (is_int($id)) {
            $rows = fed_fetch_table_row_by_id(BC_FED_USER_PROFILE_DB, $id);
            if ($rows instanceof WP_Error) {
                ?>
                <div class="container fed_UP_container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <button type="button"
                                        class="close"
                                        data-dismiss="alert"
                                        aria-hidden="true">&times;
                                </button>
                                <strong><?php echo $rows->get_error_message() ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                return;
            }
            $row      = fed_process_user_profile($rows, $action);
            $selected = $row['input_type'];
        } else {
            $row = fed_get_empty_value_for_user_profile($action);
        }
    }
    if ($action === 'post') {
        $page = __('Post', 'frontend-dashboard');
        $url  = menu_page_url('fed_post_fields', false);
        if (is_int($id)) {
            $rows = fed_fetch_table_row_by_id(BC_FED_POST_DB, $id);
            if ($rows instanceof WP_Error) {
                ?>
                <div class="container fed_UP_container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <button type="button"
                                        class="close"
                                        data-dismiss="alert"
                                        aria-hidden="true">&times;
                                </button>
                                <strong><?php echo $rows->get_error_message() ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                return;
            }
            $row      = fed_process_user_profile($rows, $action);
            $selected = $row['input_type'];
        } else {
            $row = fed_get_empty_value_for_user_profile($action);
        }
    }
    $menu_options = fed_fetch_menu();
    $buttons      = fed_admin_user_profile_select($selected);
    ?>
    <div class="bc_fed container fed_add_edit_input_container">
        <div class="row fed_admin_up_select">
            <div class="col-md-3">
                <a class="btn btn-primary"
                   href="<?php echo $url; ?>">
                    <i class="fa fa-mail-reply"></i>
                    <?php _e('Back to', 'frontend-dashboard') ?> <?php echo $page ?>
                </a>
            </div>
            <div class="col-md-3 col-lg-offset-1 text-center">
                <h4 class="fed_header_font_color text-uppercase">
                    <?php echo $add_edit_action; ?><?php echo $page ?><?php _e('field', 'frontend-dashboard') ?>
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="fed_buttons_container <?php echo $buttons['class']; ?>">
                    <?php
                    foreach ($buttons['options'] as $index => $button) {
                        $active = $buttons['value'] === $index ? 'active' : '';
                        ?>
                        <div class="fed_button <?php echo $active; ?>" data-button="<?php echo $index; ?>">
                            <div class="fed_button_image">
                                <img src="<?php echo $button['image'] ?>"/>
                            </div>
                            <div class="fed_button_text"><?php echo $button['name'] ?></div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="fed_all_input_fields_container">

            <?php
            //			Input Type
            fed_admin_input_fields_single_line($row, $action, $menu_options);
            //            Email Type
            fed_admin_input_fields_mail($row, $action, $menu_options);
            //            Number Type
            fed_admin_input_fields_number($row, $action, $menu_options);
            //            Password
            fed_admin_input_fields_password($row, $action, $menu_options);
            //            TextArea
            fed_admin_input_fields_multi_line($row, $action, $menu_options);
            //            Checkbox
            fed_admin_input_fields_checkbox($row, $action, $menu_options);
            //            Radio
            fed_admin_input_fields_radio($row, $action, $menu_options);
            //            Select / Dropdown
            fed_admin_input_fields_select($row, $action, $menu_options);
            // URL
            fed_admin_input_fields_url($row, $action, $menu_options);

            do_action('fed_admin_input_fields_container_extra', $row, $action, $menu_options);
            ?>

        </div>
    </div>
    <?php
}