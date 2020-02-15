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


    b.on('change', '#fed_add_transaction_item', function (e) {
        var change = $(this);
        var url = change.data('url');
        var type = change.val();
        if (type !== '') {
            $.ajax({
                type: 'POST',
                url: url,
                data: {type: type},
                success: function (results) {
                    if (results.success) {
                        $('#fed_transaction_items_container').html(results.data.html);
                    } else {
                        swal({
                            title: frontend_dashboard.alert.something_went_wrong,
                            text: results.data.message,
                            type: "error",
                        })
                    }

                }
            });
        } else {
            $('#fed_transaction_items_container').html('');
        }
        e.preventDefault();
    });


    b.on('click', '.fed_add_transaction_item', function (e) {
        var url = $('#fed_add_transaction_item').data('url');
        var type = $('#fed_add_transaction_item').val();
        if (type !== '') {
            $.ajax({
                type: 'POST',
                url: url,
                data: {type: type},
                success: function (results) {
                    if (results.success) {
                        $('#fed_transaction_items_container').append(results.data.html);
                    } else {
                        swal({
                            title: frontend_dashboard.alert.something_went_wrong,
                            text: results.data.message,
                            type: "error",
                        })
                    }

                }
            });
        } else {
            $('#fed_transaction_items_container').html('');
        }
        e.preventDefault();
    });


    b.on('click', '.fed_delete_transaction_item', function (e) {
        var change = $(this);
        change.closest('.fed_transaction_item').remove();
        e.preventDefault();
    });

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


    // window.onerror = function (msg, url, lineNo, columnNo, error) {
    //     console.log('ss');
    //     var string = msg.toLowerCase();
    //     var substring = "script error";
    //     if (string.indexOf(substring) > -1){
    //         alert('Script Error: See Browser Console for Detail');
    //     } else {
    //         var message = [
    //             'Message: ' + msg,
    //             'URL: ' + url,
    //             'Line: ' + lineNo,
    //             'Column: ' + columnNo,
    //             'Error object: ' + JSON.stringify(error)
    //         ].join(' - ');
    //
    //         alert(message);
    //     }
    //
    //     return false;
    // };

    if (transaction_modal.length) {
        transaction_modal.on('hidden.bs.modal', function () {
            location.reload();
        });
    }
});