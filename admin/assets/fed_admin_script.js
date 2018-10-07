jQuery(document).ready(function ($) {
    var bc_fed = $('.bc_fed');
    var body = $('body');
    var fed_menu_ajax = $('form.fed_menu_ajax');
    /**
     * Admin Page / User Profile Setting Save/Edit
     */
    body.on('submit', '.fed_ajax', function (e) {
        var form = $(this);
        fed_toggle_loader();
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function (results) {
                fed_toggle_loader();
                fedAdminAlert.adminSettings(results);
            }

        });

        e.preventDefault();
    });

    /**
     * Request with Confirmation
     * @type {*}
     */
    $('.fed_ajax_confirmation').on('submit', function (e) {
        var form = $(this);
        swal({
            title: "Are you sure?",
            text: "You want to do this action?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, Please Proceed",
            cancelButtonText: "No, Cancel it"
        }).then(
            function () {
                fed_toggle_loader();
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function (results) {
                        fed_toggle_loader();
                        fedAdminAlert.adminSettings(results);
                    }

                });
            }, function (dismiss) {
                if (dismiss === 'cancel') {
                    swal({
                            title: "Cancelled",
                            type: "error",
                            confirmButtonColor: '#0AAAAA'
                        }
                    );
                }
            });
        e.preventDefault();
    });


    body.on('click', '.fed_upload_container', function (e) {
        var custom_uploader;
        var button_click = $(this);
        e.preventDefault();
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Upload',
            button: {
                text: 'Upload'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function () {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            button_click.find('.fed_upload_icon').addClass('hide');
            button_click.find('.fed_upload_input').val(attachment.id);
            button_click.find('.fed_upload_image_container').html("<img width=100 height=100 src=" + attachment.url + ">");
        });
        //Open the uploader dialog
        custom_uploader.open();
    });

    fed_menu_ajax.on('click', '.fed_menu_save', function (e) {
        var form = $(this).closest('form');
        fed_toggle_loader();
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: {'fed_action': 'save', 'data': form.serialize()},
            success: function (results) {
                // console.log(results);
                fed_toggle_loader();
                fedAdminAlert.adminSettings(results);
            }

        });

        e.preventDefault();
    });

    /**
     * Delete Menu
     */
    fed_menu_ajax.on('click', '.fed_menu_delete', function (e) {
        var form = $(this).closest('form');
        swal({
            title: "Are you sure?",
            text: "You to delete this Menu!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel it!",
            showLoaderOnConfirm: true
        }).then(
            function () {
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: {'fed_action': 'delete', 'data': form.serialize()},
                    success: function (results) {
                        fedAdminAlert.adminSettings(results);
                    }

                });
            },
            function (dismiss) {
                if (dismiss === 'cancel') {
                    swal({
                            title: "Cancelled",
                            type: "error",
                            confirmButtonColor: '#0AAAAA'
                        }
                    );
                }
            });
        fed_toggle_loader();
        e.preventDefault();
    });

    /**
     * Delete User Profile
     */
    $('form.fed_profile_ajax').on('click', '.fed_profile_delete', function (e) {
        //console.log('delete');
        var form = $(this).closest('form');
        swal({
            title: "Are you sure?",
            text: "You to delete this Profile Input!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel it!",
            showLoaderOnConfirm: true
        }).then(
            function () {
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: {'fed_up_action': 'delete', 'data': form.serialize()},
                    success: function (results) {
                        fedAdminAlert.adminSettings(results);
                    }

                });
            },
            function (dismiss) {
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'You have cancelled to do this action',
                        'error'
                    )
                }
            });
        fed_toggle_loader();
        e.preventDefault();
    });

    /**
     * disallow symbols on input meta other than underscore
     */
    $('input.fed_admin_input_meta, input.fed_menu_slug').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9_]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }

        e.preventDefault();
        return false;
    });

    /**
     * Based on the selection of Input type change the form fields.
     */
    $('.fed_add_edit_input_container').on('click', '.fed_button', function (e) {
        var container = $(this).closest('.fed_add_edit_input_container');
        var btn_container = $(this).closest('.fed_buttons_container');
        var selected = $(this).data('button');
        var closest = container.find('.fed_all_input_fields_container');
        closest.find('.fed_input_type_container').addClass('hide');
        closest.find('input[name=input_type]').val(selected);
        btn_container.find('.fed_button').removeClass('active');
        $(this).addClass('active');
        switch (selected) {
            case 'single_line':
                closest.find('.fed_input_single_line_container').removeClass('hide');
                break;
            case 'number':
                closest.find('.fed_input_number_container').removeClass('hide');
                break;
            case 'multi_line':
                closest.find('.fed_input_multi_line_container').removeClass('hide');
                break;
            case 'email':
                closest.find('.fed_input_email_container').removeClass('hide');
                break;
            case 'checkbox':
                closest.find('.fed_input_checkbox_container').removeClass('hide');
                break;
            case 'select':
                closest.find('.fed_input_dropdown_container').removeClass('hide');
                break;
            case 'radio':
                closest.find('.fed_input_radio_container').removeClass('hide');
                break;
            case 'password':
                closest.find('.fed_input_password_container').removeClass('hide');
                break;
            case 'url':
                closest.find('.fed_input_url_container').removeClass('hide');
                break;
            case 'date':
                closest.find('.fed_input_date_container').removeClass('hide');
                break;
            case 'file':
                closest.find('.fed_input_file_container').removeClass('hide');
                break;
            case 'color':
                closest.find('.fed_input_color_container').removeClass('hide');
                break;
            case 'wysiwyg':
                closest.find('.fed_input_wysiwyg_container').removeClass('hide');
                break;


        }
        e.preventDefault();
    });

    $('.fed_add_edit_input_container .fed_button.active').trigger('click');


    /**
     * Auto populate Input Meta
     */
    $('.fed_input_type_container').on('change', '.fed_input_label_for_onchange', function () {
        var value = $(this).val().replace(/[^a-zA-Z0-9 ]/g, "").split(' ').join('_').toLowerCase();
        $(this).closest('form').find('.row .form-group .fed_admin_input_meta').val(value);
    });

    /**
     * Auto populate Menu Slug
     */
    $('.fed_menu_name').on('change', function () {
        var value = $(this).val().replace(/[^a-zA-Z0-9 ]/g, "").split(' ').join('_').replace("^[a-zA-Z0-9_]+$", " ").toLowerCase();
        $(this).closest('form').find('.fed_menu_slug').val(value);
    });

    $('#fed_admin_post_user_role_name').on('change', function () {
        var value = $(this).val().replace(/[^a-zA-Z0-9 ]/g, "").split(' ').join('-').replace("^[a-zA-Z0-9_]+$", " ").toLowerCase();
        $('#fed_admin_post_user_role_slug').val(value);
    });


    $('#fed_admin_setting_tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    var hash = document.location.hash;
    var prefix = "tab_";
    if (hash) {
        $('.nav-tabs a[href="' + hash.replace(prefix, "") + '"]').tab('show');
    }
    // Change hash for page-reload
    $('.nav-tabs a').on('shown', function (e) {
        window.location.hash = e.target.hash.replace("#", "#" + prefix);
    });

    /**
     * User Profile Layout
     */
    $('#fed_LSRB').on('click', function (e) {
        $('#fed_UPL_layout_container').html(fed.fed_LSRB);
        //console.log(fed.fed_LSRB);
        //console.log('yys');
        e.preventDefault();
    });

    $('#fed_LBRS').on('click', function (e) {
        $('#fed_UPL_layout_container').html(fed.fed_LBRS);
        e.preventDefault();
    });

    body.on("click", "div[data-id].fed_single_fa ", function () {
        var menu_name = $(this).closest('.modal-body').find('#fed_menu_box_id').val();
        body.find("." + menu_name).val($(this).data("id"));
    });

    $('.fed_show_fa_list').on('show.bs.modal', function (e) {
        var click = $(e.relatedTarget).data('fed_menu_box_id');
        body.find('#fed_menu_box_id').val(click);
        //console.log(click);
    });

    body.on('click', '.fed_menu_save_button_toggle', function (e) {
        if ($('#fed_add_new_menu_container').hasClass('hide')) {
            $('#fed_add_new_menu_container').removeClass('hide');
            $(this).html('<i class="fa fa-minus"></i> Hide Add New Menu');
        } else {
            $('#fed_add_new_menu_container').addClass('hide')
            $(this).html('<i class="fa fa-plus"></i> Add New Menu');
        }
        e.preventDefault();
    });

    body.on('change', 'select.fed_payment_cycles', function (e) {
        var selected = $(this).val();
        var closest = $(this).closest('.fed_role_based_payment_cycle');
        if (selected === 'custom') {
            if (closest.find('.custom_payment_cycle_container').hasClass('hide')) {
                closest.find('.custom_payment_cycle_container').removeClass('hide');
            }
        } else {
            closest.find('.custom_payment_cycle_container').addClass('hide');
        }
        e.preventDefault();
    });

    //Search for user in Order
    $('.fed_order_search_add').on('submit', function (e) {
        var form = $(this);
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function (results) {
                //console.log(results);
                fedAdminAlert.adminSettings(results);
                if (results.data.extra) {
                    $('#email').val(results.data.extra.email);
                    $('#first_name').val(results.data.extra.first_name);
                    $('#last_name').val(results.data.extra.last_name);
                    $('#user_id').val(results.data.extra.user_id);
                }
            }
        });
        e.preventDefault();
    });

    $('form.fed_admin_add_orders').on('submit', function (e) {
        var form = $(this).closest('form');
        fed_toggle_loader();
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function (results) {
                //console.log(results);
                fed_toggle_loader();
                fedAdminAlert.adminAlertSettings(results);
            }

        });

        e.preventDefault();
    });

    $('select.fed_payment_cycles').trigger('change');

    /**
     * Hide Admin notice
     */
    body.on('click', '.fed_message_hide', function (e) {
        $(this).closest('.notice').hide();
        e.preventDefault();
    });
    body.on('click', '.fed_message_delete', function (e) {
        var form = $(this);
        $.ajax({
            type: 'get',
            url: form.data('url'),
            data: {},
            success: function (results) {
                form.closest('.notice').hide();
                fedAdminAlert.adminSettings(results);
            }
        });
        e.preventDefault();
    });

    /**
     * Admin Main Menu Search
     */
    $('#fed_menu_search').on('input', function (e) {
        var input = $(this);
        var parent = input.closest('.fed_dashboard_menu_items_container');
        var item = $(parent).find('.fed_dashboard_menu_single_item');
        var filter = input.val().toLowerCase();
        if (input.val().length > 0) {
            input.closest('.fed_search_box').find('.fed_menu_search_clear').removeClass('hide');
        } else {
            input.closest('.fed_search_box').find('.fed_menu_search_clear').addClass('hide');
        }
        item.each(function () {
            if (!$(this).hasClassRegEx(filter) && input.val().length > 0) {
                $(this).fadeOut();
            } else {
                $(this).show();
            }
        });
        e.preventDefault();
    });

    /**
     * Admin Main Menu Clear
     */
    $('.fed_menu_search_clear').on('click', function (e) {
        $(this).closest('.fed_search_box').find('input').val('');
        $('#fed_menu_search').trigger('input');
        e.preventDefault();
    });

    /**
     * Single line executions
     */
    body.popover({
        selector: '[data-toggle="popover"]',
        trigger: 'focus'
    });
    $(".flatpickr").flatpickr({});

    /**
     * Initial Setup
     */
    body.on('click', '.fed_initial_setup_close', function (e) {
        var close = $(this);
        close.closest('.fed_initial_setup_container').toggleClass('fed_hide');
        e.preventDefault();
    });

    $(function () {
        var hash = window.location.hash;
        hash && $('ul.nav a[href="' + hash + '"]').tab('show');

        $('.nav-tabs a').click(function (e) {
            $(this).tab('show');
            var scrollmem = $('body').scrollTop() || $('html').scrollTop();
            window.location.hash = this.hash;
            $('html,body').scrollTop(scrollmem);
        });
    });

    function fed_toggle_loader() {
        $('.preview-area').toggleClass('hide');
        // $('body').toggleClass('fed_bg_gray');
    }
});

var fedAdminAlert = {
    adminSettings: function (results) {
        console.log(results);
        if (results.success) {
            swal({
                title: results.data.message || 'Something Went Wrong',
                type: "success",
                confirmButtonColor: '#0AAAAA',
            }).then(
                function () {
                    if (results.data.reload) {
                        if (window.location == results.data.reload) {
                            window.location.reload();
                        } else {
                            window.location = results.data.reload
                        }
                    }
                });
        }
        else if ((results.success) === false) {
            swal({
                title: results.data.message || 'Something Went Wrong',
                type: "error",
                confirmButtonColor: "#DD6B55"
            }).then(function () {
                if (results.data.reload) {
                    if (window.location == results.data.reload) {
                        location.reload();
                    } else {
                        window.location = results.data.reload
                    }
                }
            });
        }
        else {
            swal({
                title: "Invalid form submission",
                text: "Please try again",
                type: "error",
                confirmButtonColor: "#DD6B55"
            }).then(function () {
                if (results.data.reload) {
                    if (window.location == results.data.reload) {
                        location.reload();
                    } else {
                        window.location = results.data.reload
                    }
                }
            });
        }

    },
    adminAlertSettings: function (results) {
        if (results.success) {
            swal({
                title: results.data.message || 'Something Went Wrong',
                type: "success",
                confirmButtonColor: '#0AAAAA',
            });
        }
        else if (results.success === false) {
            var error;
            if (results.data.message instanceof Array) {
                error = results.data.message.join('</br>');
            } else {
                error = results.data.message;
            }
            swal({
                title: error,
                type: "error",
                confirmButtonColor: "#DD6B55",
                html: true

            });
        }
        else {
            swal({
                title: "Invalid form submission",
                text: "Please try again",
                type: "error",
                confirmButtonColor: "#DD6B55"
            });
        }

    }
};

jQuery.fed_toggle_loader = function () {
    $('.preview-area').toggleClass('hide');
    if ($('.fed_loader_message').length) {
        window.setTimeout(function () {
            $('.fed_loader_message').toggleClass('hide');
        }, 2000);
    }
};

(function ($) {
    $.fn.hasClassRegEx = function (regex) {
        var classes = $(this).attr('class');

        if (!classes || !regex) {
            return false;
        }

        classes = classes.split(' ');
        var len = classes.length;

        for (var i = 0; i < len; i++) {
            if (classes[i].toLowerCase().match(regex)) {
                return true;
            }
        }

        return false;
    };
})(jQuery);