jQuery(document).ready(function ($) {

    var b = $('body');
    var transaction_modal = $('#fed_transaction_modal');
    b.on('submit', '.fed_ajax_print_invoice', function (e) {
        var form = $(this);
        var modal = $('#fed_invoice_popup');
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function (results) {
                if (results.success) {
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

    if ($('.fed_invoice_print').length) {
        b.on('click', '.fed_invoice_print', function (e) {
            window.print();
            e.preventDefault();
        });
    }

    b.on('submit', '.fed_transaction_items', function (e) {
        var form = $(this);
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function (results) {
                if (results.success) {
                    form.closest('.fed_transaction_items_container').html(results.data.html);
                }
            }
        });
        e.preventDefault();

    });

    if (transaction_modal.length) {
        transaction_modal.on('hidden.bs.modal', function () {
            location.reload();
        });
    }
});