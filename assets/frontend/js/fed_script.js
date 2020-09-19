/**
 * Frontend Dashboard Script.
 *
 * @package Frontend Dashboard.
 */

jQuery(document).ready(function ($) {
    var b = $('body')
    var dashboard_menu = $('.fed_dashboard_menus')

    $('[data-toggle="popover"]').popover()

    // All Front End submission.
    $('form.fed_form_post').on('submit', function (e) {
      var click = $(this)
      var data = click.serialize()
      var url = frontend_dashboard.fed_login_form_post
      var method = click.attr('method') || 'post'
      fed_toggle_loader()
      $.ajax({
        type: method,
        data: data,
        url: url,
        success: function (results) {
          console.log(results)
          fed_toggle_loader()
          fedAlert.loginStatus(results)
        }
      })
      e.preventDefault()
    })

    //Common submission. Disabiling for next few releases because of new one below.
    // $('form.fed_ajax').on('submit', function (e) {
    //   var form = $(this)
    //   fed_toggle_loader()
    //   $.ajax({
    //     type: 'POST',
    //     url: form.attr('action'),
    //     data: form.serialize(),
    //     success: function (results) {
    //       fed_toggle_loader()
    //       fedAlert.dashboardPostCommon(results)
    //     }
    //
    //   })
    //
    //   e.preventDefault()
    // })

    // New Common Form Submission
    b.on('submit', '.fed_ajax', function (e) {
      var form = $(this)
      fed_toggle_loader()
      $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: form.serialize(),
        success: function (results) {
          fed_toggle_loader()
          if (results.success) {
            if (results.data.message) {
              swal({
                title: results.data.message,
                type: 'success',
              }).then(function () {
                if (results.data.redirect_url) {
                  window.location = results.data.redirect_url
                }
              })
            }
          } else {
            if (results.data.message) {
              swal({
                title: results.data.message,
                type: 'error',
              }).then(function () {
                if (results.data.redirect_url) {
                  window.location = results.data.redirect_url
                }
              })
            }
            if (results.data.errors) {
              var show_errors = ''
              if (Array.isArray(results.data.errors)) {
                results.data.errors.each(function (error) {
                  show_errors += error
                })
              } else {
                show_errors += results.data.errors
              }
              swal({
                title: show_errors,
                type: 'error',
              })
            }
          }
        }
      })
      e.preventDefault()
    })

    $('form.fed_get_qa_ajax').on('click', function (e) {
      var form = $(this)
      fed_toggle_loader()
      $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: form.serialize(),
        success: function (results) {
          form.find('.fed_support_badge').html('')
          fed_toggle_loader()
          $('body').find('#fed_qa_container').html(results.data.message)
        }

      })

      e.preventDefault()
    })

    b.on('submit', 'form.fed_add_new_answer', function (e) {
      var form = $(this)
      fed_toggle_loader()
      $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: form.serialize(),
        success: function (results) {
          fed_toggle_loader()
          $('body').find('#fed_qa_container').html(results.data.message)
        }

      })

      e.preventDefault()
    })

    // User Profile Save.
    $('form.fed_user_profile_save').on('submit', function (e) {
      var click = $(this)
      var data = click.serialize()
      var url = click.attr('action')
      var method = click.attr('method') || 'post'
      $.ajax({
        type: method,
        data: data,
        url: url,
        success: function (results) {
          fedAlert.userProfileSave(results)
        }
      })
      e.preventDefault()
    })

    $.fn.extend({
      animateCss: function (animationName) {
        var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend'
        this.addClass('animated ' + animationName).one(animationEnd, function () {
          $(this).removeClass('animated ' + animationName)
        })
      }
    })

    // Change hash for page-reload.
    $('.nav-tabs a').on('shown', function (e) {
      window.location.hash = e.target.hash.replace('#', '#' + prefix)
    })

    /**
     * Payment loading
     */
    $('form.fed_user_not_paid_form').on('submit', function () {
      fed_toggle_loader()
    })
    /**
     * Dashboard Post Operations
     */
    // Dashboard Menu Selection.
    dashboard_menu.on('click', '.fed_menu_slug', function (e) {
      var menu = $(this)
      var value = menu.data('menu')
      var closest = menu.closest('.fed_dashboard_wrapper').find('.fed_dashboard_items')

      menu.closest('.list-group').find('.fed_menu_slug').removeClass('active')
      menu.addClass('active')

      // console.log(closest);

      closest.find('.fed_dashboard_item').addClass('hide')
      closest.find('.' + value).removeClass('hide')

      e.preventDefault()
    })

    // Dashboard Post Save.
    b.on('submit', 'form.fed_dashboard_add_new_post', function (e) {
      var click = $(this)
      var data = click.serialize()
      var url = click.attr('action')
      var method = click.attr('method') || 'post'
      fed_toggle_loader()
      $.ajax({
        type: method,
        data: data,
        url: url,
        success: function (results) {
          console.log(results)
          $('#fed_post_id_hidden').val(results.data.id)
          fedAlert.dashboardPostCommon(results)
          fed_toggle_loader()
        }
      })
      e.preventDefault()
    })

    // Add new post request.
    b.on('submit', 'form.fed_dashboard_add_new_post_request', function (e) {
      var click = $(this)
      var data = click.serialize()
      var url = click.attr('action')
      var method = click.attr('method') || 'post'
      var root = click.closest('.fed_panel_body_container')
      fed_toggle_loader()
      $.ajax({
        type: method,
        data: data,
        async: false,
        url: url,
        success: function (results) {
          // console.log(results);
          fed_toggle_loader()
          root.html(results)
        },
        complete: function () {
          $('.flatpickr').flatpickr({})
        }
      })
      e.preventDefault()

    })

    // Show Edit post by ID.
    b.on('submit', 'form.fed_dashboard_edit_post_by_id', function (e) {
      var click = $(this)
      var data = click.serialize()
      var url = click.attr('action')
      var method = click.attr('method') || 'post'
      var root = click.closest('.fed_panel_body_container')
      fed_toggle_loader()
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
          // console.log(results);
          fed_toggle_loader()
          root.html(results)
        },
        complete: function () {
          //tinyMCE.remove('#post_content');
          if ($('.flatpickr').length) {
            $('.flatpickr').flatpickr({})
          }
        }
      })
      e.preventDefault()
    })

    // Process Edit post by ID.
    b.on('submit', 'form.fed_dashboard_process_edit_post_request', function (e) {
      var click = $(this)
      var data = click.serialize()
      var url = click.attr('action')
      var method = click.attr('method') || 'post'
      var root = click.closest('.fed_panel_body_container')
      fed_toggle_loader()
      $.ajax({
        type: method,
        data: data,
        url: url,
        success: function (results) {
          fedAlert.dashboardPostCommon(results)
          fed_toggle_loader()
        }
      })
      e.preventDefault()
    })

    // Delete post.
    b.on('submit', 'form.fed_dashboard_delete_post_by_id', function (e) {
      var click = $(this)
      var data = click.serialize()
      var url = click.attr('action')
      var method = click.attr('method') || 'post'
      var root = click.closest('.fed_dashboard_item_field_wrapper')
      fed_toggle_loader()
      swal({
        title: frontend_dashboard.alert.confirmation.title,
        text: frontend_dashboard.alert.confirmation.text,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: frontend_dashboard.alert.confirmation.confirm,
        cancelButtonText: frontend_dashboard.alert.confirmation.cancel,
        showLoaderOnConfirm: true
      }).then(
        function () {
          $.ajax({
            type: method,
            url: url,
            data: data,
            success: function (results) {
              fedAlert.dashboardPostCommon(results)
              if (results.success) root.html('')
            }

          })
        },
        function (dismiss) {
          if (dismiss === 'cancel') {
            swal({
                title: frontend_dashboard.alert.title_cancelled,
                type: 'error',
                confirmButtonColor: '#0AAAAA'
              }
            )
          }
        })
      fed_toggle_loader()

      e.preventDefault()
    })

    // Show Post List Request.
    b.on('submit', '.fed_dashboard_show_post_list_request', function (e) {
      var click = $(this)
      var data = click.serialize()
      var url = click.attr('action')
      var method = click.attr('method') || 'post'
      var root = click.closest('.fed_panel_body_container')
      fed_toggle_loader()
      $.ajax({
        type: method,
        data: data,
        url: url,
        beforeSend: function () {
          if (typeof tinyMCE !== 'undefined') {
            tinyMCE.remove('#post_content')
          }
        },
        success: function (results) {
          // console.log(results);
          fed_toggle_loader()
          root.html(results)
        }
      })
      e.preventDefault()
    })

    /**
     * Upload
     *
     */

    b.on('click', '.fed_upload_container', function (e) {
      var custom_uploader
      var button_click = $(this)
      e.preventDefault()
      custom_uploader = wp.media.frames.file_frame = wp.media({
        title: 'Upload',
        button: {
          text: 'Upload'
        },
        multiple: false
      })
      //When a file is selected, grab the URL and set it as the text field's value
      custom_uploader.on('select', function () {
        // var regex_image_type = /(image)/g;
        attachment = custom_uploader.state().get('selection').first().toJSON()
        console.log(attachment)
        button_click.find('.fed_upload_image_dummy').addClass('fed_hide')
        button_click.find('.fed_upload_input').val(attachment.id)
        console.log(( attachment.mime ).indexOf('image'))
        if (( attachment.mime ).indexOf('image') >= 0) {
          console.log('image')
          button_click.find('.fed_upload_image_actual').removeClass('fed_hide')
          button_click.closest('.fed_upload_wrapper').find('.fed_remove_image').removeClass('fed_hide')
          button_click.find('.fed_upload_image_container img').attr('src', attachment.url)
        } else {
          console.log('pdf')
          console.log(attachment.icon)
          button_click.closest('.fed_upload_wrapper').find('.fed_remove_image').addClass('fed_hide')
          button_click.find('.fed_upload_image_container img').attr('src', attachment.icon)
        }

      })
      //Open the uploader dialog
      custom_uploader.open()
    })

    b.on('mouseover', '.fed_show_on_hover_container', function () {
      $(this).find('.fed_show_on_hover').show()
    })

    b.on('mouseleave', '.fed_show_on_hover_container', function () {
      $(this).find('.fed_show_on_hover').hide()
    })

    // Copy URL from media uploader.
    $('.flatpickr').flatpickr({})

    $('.fed_multi_select').select2()

    $('#fed_support_search').on('input', function (e) {
      var rex = new RegExp($(this).val(), 'i')
      $('.fed_get_qa_ajax').hide().filter(function () {
        return rex.test($(this).text())
      }).show()

      e.preventDefault()
    })

    $('.default_template').on('click', '.fed_collapse_menu', function (e) {
      var click = $(this)
      var parent = click.closest('.fed_dashboard_wrapper')
      parent.find('.fed_dashboard_menus').toggleClass('fed_collapse')
      parent.find('.fed_dashboard_menus').toggleClass('col-md-3').toggleClass('col-md-1')
      parent.find('.fed_dashboard_items').toggleClass('col-md-9').toggleClass('col-md-11')
      parent.find('.flex').toggleClass('flex_center')
      e.preventDefault()
    })

    $('.fed_menu_slug ').on('click', function (e) {
      $(this).closest('.fed_menu_ul').toggleClass('in')
      e.preventDefault()
    })

    var fedAlert = {
      loginStatus: function (results) {
        var error
        if (results.success) {
          swal({
            title: results.data.message || frontend_dashboard.alert.confirmation.title,
            text: frontend_dashboard.alert.redirecting,
            type: 'success',
            showConfirmButton: false,
            timer: 1000,
            confirmButtonColor: '#0AAAAA'
          }).then(
            function () {
            },
            function () {
              window.location.href = results.data.url
            })
        } else {
          if (frontend_dashboard.fed_captcha_details && frontend_dashboard.fed_captcha_details.fed_captcha_enable === 'Enable') {
            grecaptcha.reset()
          }
          if (results.data.user instanceof Array) {
            error = results.data.user.join('</br>')
          } else {
            error = results.data.user
          }
          swal({
            title: error,
            type: 'error',
            confirmButtonColor: '#DD6B55'
          })
        }
      },
      adminSettings: function (results) {
        if (results.success) {
          swal({
            title: results.data.message || frontend_dashboard.alert.something_went_wrong,
            type: 'success',
            confirmButtonColor: '#0AAAAA',
          })
        } else {
          swal({
            title: frontend_dashboard.alert.invalid_form_submission,
            text: frontend_dashboard.alert.please_try_again,
            type: 'error',
            confirmButtonColor: '#DD6B55'
          })
        }

      },
      userProfileSave: function (results) {
        var error
        // console.log(results);
        if (results.success) {
          swal({
            title: results.data.message || frontend_dashboard.alert.something_went_wrong,
            type: 'success',
            text: '',
            confirmButtonColor: '#0AAAAA'
          })
        } else if (results.success == false) {
          if (results.data.user instanceof Array) {
            error = results.data.user.join('</br>')
          } else {
            error = results.data.user
          }
          if (frontend_dashboard.fed_captcha_details.fed_captcha_enable == 'Enable') {
            grecaptcha.reset()
          }
          swal({
            title: error,
            type: 'error',
            text: '',
            confirmButtonColor: '#DD6B55'
          })
        } else {
          swal({
            title: frontend_dashboard.alert.invalid_form_submission,
            text: frontend_dashboard.alert.please_try_again,
            type: 'error',
            confirmButtonColor: '#DD6B55'
          })
        }

      },
      dashboardPostCommon: function (results) {
        if (results.success) {
          swal({
            title: results.data.message || frontend_dashboard.alert.something_went_wrong,
            type: 'success',
            text: '',
            confirmButtonColor: '#0AAAAA',
          })
        } else if (results.success == false) {
          var error = ''
          if (results.data.message instanceof Array) {
            error = results.data.message.join('</br>')
          } else {
            error = results.data.message
          }
          // console.log(results.data.message);
          swal({
            title: error,
            type: 'error',
            confirmButtonColor: '#DD6B55'
          })
        } else {
          swal({
            title: frontend_dashboard.alert.invalid_form_submission,
            text: frontend_dashboard.alert.please_try_again,
            type: 'error',
            confirmButtonColor: '#DD6B55'
          })
        }

      },
    }

    if ($('.fed_datatable').length) {
      $('.fed_datatable').dataTable({ 'autoWidth': false, 'order': [] })
    }

    if ($('input[name=user_pass]').length && $('input[name=confirmation_password]').length) {
      b.on('keyup', 'input[name=user_pass], input[name=confirmation_password]',
        function (e) {
          fed_check_password_strength(
            $('input[name=user_pass]'),
            $('input[name=confirmation_password]'),
            $('.fed_password_strength'),
            $('button[type=submit]'),
            []
          )
          e.preventDefault()
        }
      )
    }

    function fed_toggle_loader () {
      $('.preview-area').toggleClass('hide')
    }
  }
)

jQuery.fed_toggle_loader = function () {
  jQuery('.preview-area').toggleClass('hide')
  if (jQuery('.fed_loader_message').length) {
    window.setTimeout(function () {
      jQuery('.fed_loader_message').toggleClass('hide')
    }, 2000)
  }
}

jQuery.fed_generate_random_number = function () {
  return Math.random().toString(36).substring(7)
}

var CaptchaCallback = function () {
  var fedRegister = document.getElementById('fedRegisterCaptcha')
  var fedLogin = document.getElementById('fedLoginCaptcha')
  if (fedRegister !== null) {
    grecaptcha.render('fedRegisterCaptcha', { 'sitekey': frontend_dashboard.fed_captcha_details.fed_captcha_site_key })
  }
  if (fedLogin !== null) {
    grecaptcha.render('fedLoginCaptcha', { 'sitekey': frontend_dashboard.fed_captcha_details.fed_captcha_site_key })

  }
}

function fed_check_password_strength ($pass1,
  $pass2,
  $strengthResult,
  $submitButton,
  blacklistArray) {
  var pass1 = $pass1.val()
  var pass2 = $pass2.val()
  if (pass1.length <= 8) {
    strength = 3
  } else if ( ! pass1.match(/[0-9]+/)) {
    strength = 4
  } else if ( ! pass1.match(/[a-z]+/)) {
    strength = 5
  } else if ( ! pass1.match(/[A-Z]+/)) {
    strength = 6
  } else if ( ! pass1.match(/[!@#$%^&*()]+/)) {
    strength = 7
  } else if (pass1 !== pass2 && pass2.length > 0) {
    strength = 2
  } else {
    strength = 1
  }

  switch (strength) {
    case 1:
      $strengthResult.removeClass('bad').addClass('strong').html('Strong')
      break
    case 2:
      $strengthResult.removeClass('strong').addClass('bad').html('Password Mismatch')
      break
    case 3:
      $strengthResult.removeClass('strong').addClass('bad').html('Length should be greater than 8')
      break
    case 4:
      $strengthResult.removeClass('strong').addClass('bad').html('At least one number')
      break
    case 5:
      $strengthResult.removeClass('strong').addClass('bad').html('At least one lowercase')
      break
    case 6:
      $strengthResult.removeClass('strong').addClass('bad').html('At least one uppercase')
      break
    case 7:
      $strengthResult.removeClass('strong').addClass('bad').html('At least one Symbol ! @ # $ % ^ & * ( )')
      break
  }
  return strength
}
