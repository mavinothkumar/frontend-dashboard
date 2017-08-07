jQuery(document).ready(function ($) {

        var b = $('body');

        b.popover({
            selector: '[data-toggle="popover"]',
            trigger: 'hover'
        });

        // Login, Register, Reset Password TagButton Navigation
        $('.fed_login_menus').on('click', '.fed_tab_menus', function () {
            var click = $(this);
            var click_id = click.attr('id');

            click.closest('.fed_login_wrapper').find('.fed_tab_menus').removeClass('fed_selected');
            click.addClass('fed_selected');

            $('.fed_login_content').find('.fed_tab_content').addClass('hide ').animateCss('pulse');
            $('.fed_login_content').find("[data-id='" + click_id + "']").removeClass('hide');
        });

        // All Front End submission

        $('form.fed_form_post').on('submit', function (e) {
            var click = $(this);
            var data = click.serialize();
            var url = fed.fed_login_form_post;
            var method = click.attr('method') || 'post';
            $.ajax({
                type: method,
                data: data,
                url: url,
                success: function (results) {
                    fedAlert.loginStatus(results);
                }
            });
            e.preventDefault();
        });

        //Common submission

        $('form.fed_ajax').on('submit', function (e) {
            var form = $(this);
            $('#preview-area').find('.spinner_circle').removeClass('hide');
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function (results) {
                    console.log(results);
                    $('#preview-area').find('.spinner_circle').addClass('hide');
                    fedAlert.dashboardPostCommon(results);
                }

            });

            e.preventDefault();
        });

        $('form.fed_get_qa_ajax').on('click', function (e) {
            var form = $(this);
            $('#preview-area').find('.spinner_circle').removeClass('hide');
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function (results) {
                    form.find('.fed_support_badge').html('');
                    $('#preview-area').find('.spinner_circle').addClass('hide');
                    $('body').find('#fed_qa_container').html(results.data.message);
                }

            });

            e.preventDefault();
        });

        b.on('submit', 'form.fed_add_new_answer', function (e) {
            var form = $(this);
            $('#preview-area').find('.spinner_circle').removeClass('hide');
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function (results) {
                    console.log(results);
                    $('#preview-area').find('.spinner_circle').addClass('hide');
                    $('body').find('#fed_qa_container').html(results.data.message);
                }

            });

            e.preventDefault();
        });


        // User Profile Save
        $('form.fed_user_profile_save').on('submit', function (e) {
            var click = $(this);
            var data = click.serialize();
            var url = click.attr('action');
            var method = click.attr('method') || 'post';
            $.ajax({
                type: method,
                data: data,
                url: url,
                success: function (results) {
                    fedAlert.userProfileSave(results);
                }
            });
            e.preventDefault();
        });

        $.fn.extend({
            animateCss: function (animationName) {
                var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
                this.addClass('animated ' + animationName).one(animationEnd, function () {
                    $(this).removeClass('animated ' + animationName);
                });
            }
        });


        // Change hash for page-reload
        $('.nav-tabs a').on('shown', function (e) {
            window.location.hash = e.target.hash.replace("#", "#" + prefix);
        });

        /**
         * Payment loading
         */
        $('form.fed_user_not_paid_form').on('submit', function () {
            $('#preview-area').find('.spinner_circle').removeClass('hide');
        });
        /**
         * Dashboard Post Operations
         */
        // Dashboard Menu Selection
        $('.fed_dashboard_menus').on('click', '.fed_menu_slug', function (e) {
            var menu = $(this);
            var value = menu.data('menu');
            var closest = menu.closest('.fed_dashboard_wrapper').find('.fed_dashboard_items');

            menu.closest('.list-group').find('.fed_menu_slug').removeClass('active');
            menu.addClass('active');

            // console.log(closest);

            closest.find('.fed_dashboard_item').addClass('hide');
            closest.find('.' + value).removeClass('hide');


            e.preventDefault();
        });

        // Dashboard Post Save
        b.on('submit', 'form.fed_dashboard_add_new_post', function (e) {
            var click = $(this);
            var data = click.serialize();
            var url = click.attr('action');
            var method = click.attr('method') || 'post';
            $('#preview-area').find('.spinner_circle').removeClass('hide');
            $.ajax({
                type: method,
                data: data,
                url: url,
                success: function (results) {
                    fedAlert.dashboardPostCommon(results);
                    $('#preview-area').find('.spinner_circle').addClass('hide');
                }
            });
            e.preventDefault();
        });

        // Add new post request
        b.on('submit', 'form.fed_dashboard_add_new_post_request', function (e) {
            var click = $(this);
            var data = click.serialize();
            var url = click.attr('action');
            var method = click.attr('method') || 'post';
            var root = click.closest('.fed_panel_body_container');
            $('#preview-area').find('.spinner_circle').removeClass('hide');
            $.ajax({
                type: method,
                data: data,
                async: false,
                url: url,
                success: function (results) {
                    $('#preview-area').find('.spinner_circle').addClass('hide');
                    root.html(results);
                },
                complete: function () {
                    $(".flatpickr").flatpickr({});
                }
            });
            e.preventDefault();

        });

        // Show Edit post by ID
        b.on('submit', 'form.fed_dashboard_edit_post_by_id', function (e) {
            var click = $(this);
            var data = click.serialize();
            var url = click.attr('action');
            var method = click.attr('method') || 'post';
            var root = click.closest('.fed_panel_body_container');
            $('#preview-area').find('.spinner_circle').removeClass('hide');
            $.ajax({
                type: method,
                data: data,
                url: url,
                async: false,
                beforeSend: function () {
                    //tinyMCE.add('#post_content');
                    // tinyMCE.remove('#post_content');
                },
                success: function (results) {
                    $('#preview-area').find('.spinner_circle').addClass('hide');
                    root.html(results);
                },
                complete: function () {
                    //tinyMCE.remove('#post_content');
                    jQuery(".flatpickr").flatpickr({});
                }
            });
            e.preventDefault();
        });

        // Process Edit post by ID
        b.on('submit', 'form.fed_dashboard_process_edit_post_request', function (e) {
            var click = $(this);
            var data = click.serialize();
            var url = click.attr('action');
            var method = click.attr('method') || 'post';
            var root = click.closest('.fed_panel_body_container');
            //$('#preview-area').find('.spinner_circle').removeClass('hide');
            $.ajax({
                type: method,
                data: data,
                url: url,
                success: function (results) {
                    fedAlert.dashboardPostCommon(results);
                    //$('#preview-area').find('.spinner_circle').addClass('hide');
                }
            });
            e.preventDefault();
        });

        // Delete post
        b.on('submit', 'form.fed_dashboard_delete_post_by_id', function (e) {
            var click = $(this);
            var data = click.serialize();
            var url = click.attr('action');
            var method = click.attr('method') || 'post';
            var root = click.closest('.fed_dashboard_item_field_wrapper');
            $('#preview-area').find('.spinner_circle').removeClass('hide');
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
                        type: method,
                        url: url,
                        data: data,
                        success: function (results) {
                            fedAlert.dashboardPostCommon(results);
                            if (results.success) root.html('');
                        }

                    });
                },
                function (dismiss) {
                    if (dismiss === 'cancel') {
                        swal({
                                title: "Cancelled",
                                type: "error",
                                confirmButtonColor: '#00b5ad'
                            }
                        );
                    }
                });
            $('#preview-area').find('.spinner_circle').addClass('hide');

            e.preventDefault();
        });

        // Show Post List Request
        b.on('submit', '.fed_dashboard_show_post_list_request', function (e) {

            var click = $(this);
            var data = click.serialize();
            var url = click.attr('action');
            var method = click.attr('method') || 'post';
            var root = click.closest('.fed_panel_body_container');
            $('#preview-area').find('.spinner_circle').removeClass('hide');
            $.ajax({
                type: method,
                data: data,
                url: url,
                beforeSend: function () {
                    tinyMCE.remove('#post_content');
                },
                success: function (results) {
                    $('#preview-area').find('.spinner_circle').addClass('hide');
                    root.html(results);
                }
            });
            e.preventDefault();
        });

        b.on('click', '.fed_post_pagination li', function (e) {
            var click = $(this);
            var data = {'question_id': click.data('id')};
            var url = click.closest('.fed_post_pagination').data('href');
            var method = 'get';
            var root = click.closest('.fed_panel_body_container');

            $('#preview-area').find('.spinner_circle').removeClass('hide');

            if (click.hasClass('active')) {
                $('#preview-area').find('.spinner_circle').addClass('hide');
                return false;
            }
            $.ajax({
                type: method,
                data: data,
                url: url,
                success: function (results) {
                    $('#preview-area').find('.spinner_circle').addClass('hide');
                    root.html(results);
                }
            });
            e.preventDefault();
        });

        /**
         * Upload
         *
         */

        b.on('click', '.fed_upload_container', function (e) {
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
                attachment = custom_uploader.state().get('selection').first().toJSON();
                // console.log(attachment);
                button_click.find('.fed_upload_icon').addClass('hide');
                button_click.find('.fed_upload_input').val(attachment.id);
                button_click.find('.fed_upload_image_container').html("<img width=100 height=100 src=" + attachment.url + ">");
            });
            //Open the uploader dialog
            custom_uploader.open();
        });
        // Copy URL from media uploader

        $(".flatpickr").flatpickr({});

        $('#fed_support_search').on('input', function (e) {
            var rex = new RegExp($(this).val(), 'i');
            $('.fed_get_qa_ajax').hide().filter(function () {
                return rex.test($(this).text());
            }).show();

            e.preventDefault();
        });


        var fedAlert = {
            loginStatus: function (results) {
                var error;
                if (results.success) {
                    swal({
                        title: results.data.message,
                        text: "Please wait, you are redirecting..",
                        type: "success",
                        showConfirmButton: false,
                        timer: 1000,
                        confirmButtonColor:'#00b5ad'
                    }).then(
                        function () {},
                        function () {
                        window.location.href = results.data.url;
                    });
                } else {
                    if (fed.fed_captcha_details.fed_captcha_enable == 'Enable') {
                        grecaptcha.reset();
                    }

                    if (results.data.user instanceof Array) {
                        error = results.data.user.join('</br>');
                    } else {
                        error = results.data.user;
                    }
                    swal({
                        title: error,
                        type: "error",
                        confirmButtonColor: "#DD6B55",
                        html: true
                    });
                }
            },
            adminSettings: function (results) {
                if (results.success) {
                    swal({
                        title: results.data.message || 'Something Went Wrong',
                        type: "success",
                        confirmButtonColor:'#00b5ad'
                    });
                } else {
                    swal({
                        title: "Invalid form submission",
                        text: "Please try again",
                        type: "error",
                        confirmButtonColor: "#DD6B55"
                    });
                }

            },
            userProfileSave: function (results) {
                var error;
                if (results.success) {
                    swal({
                        title: results.data.message || 'Something Went Wrong',
                        type: "success",
                        confirmButtonColor:'#00b5ad'
                    });
                } else if (results.success == false) {
                    if (results.data.user instanceof Array) {
                        error = results.data.user.join('</br>');
                    } else {
                        error = results.data.user;
                    }
                    if (fed.fed_captcha_details.fed_captcha_enable == 'Enable') {
                        grecaptcha.reset();
                    }
                    swal({
                        title: error,
                        type: "error",
                        html: true,
                        confirmButtonColor: "#DD6B55"

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

            },
            dashboardPostCommon: function (results) {
                var error;
                if (results.success) {
                    swal({
                        title: results.data.message || 'Something Went Wrong',
                        type: "success",
                        confirmButtonColor:'#00b5ad'
                    });
                } else if (results.success == false) {
                    if (results.data.message instanceof Array) {
                        error = results.data.message.join('</br>');
                    } else {
                        error = results.data.message;
                    }
                    swal({
                        title: error,
                        type: "error",
                        html: true,
                        confirmButtonColor: "#DD6B55"
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

            },

        };

        // var hash = document.location.hash;
        // if (hash) {
        //     var new_class = hash.replace('#', '');
        //     console.log(new_class);
        //     $('.fed_menu_slug').removeClass('active');
        //     $('a[href="' + hash + '"].fed_menu_slug').addClass('active');
        //     //$('.fed_dashboard_items .fed_dashboard_item').addClass('hide');
        //     $('.fed_dashboard_items').find('.fed_dashboard_item' + new_class).removeClass('hide');
        // }


    }
);


var CaptchaCallback = function () {
    var fedRegister = document.getElementById('fedRegisterCaptcha');
    var fedLogin = document.getElementById('fedLoginCaptcha');
    if (fedRegister !== null) {
        grecaptcha.render('fedRegisterCaptcha', {'sitekey': fed.fed_captcha_details.fed_captcha_site_key});
    }
    if (fedLogin !== null) {
        grecaptcha.render('fedLoginCaptcha', {'sitekey': fed.fed_captcha_details.fed_captcha_site_key});

    }
};
