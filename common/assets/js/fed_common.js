jQuery(document).ready(function ($) {

    var b = $('body');
    b.on('submit', '.fed_ajax_print_invoice', function (e) {
        var form = $(this);
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function (results) {
                if (results.success) {
                    var modal = $('#fed_invoice_popup');
                    modal.find('.modal-body').html(results.data.html);
                    modal.modal('show');
                } else {
                    swal({
                        title: results.data.message,
                        type: "error",
                        confirmButtonColor: "#DD6B55"
                    });
                }
            }
        });
        e.preventDefault();
    });
});