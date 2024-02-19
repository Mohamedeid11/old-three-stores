$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('body').on('click', '#create_mutiple_orders_notes', function(e) {
    var items = [];
    var table = $('#kt_table_2').DataTable();
    table.$(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    $('#selected_note_orders').val(items);
    $('#ajsuform_yu_mnotes').empty();
    $('#ajsuform_yu_mnotes').html('<div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div>');
    $('#ajsuformreload_mnotes button').prop('disabled', true);
    var action = $("#ajsuformreload_mnotes").attr('action');
    var formData = new FormData($("#ajsuformreload_mnotes")[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#ajsuform_yu_mnotes').html('<div class="alert alert-danger">' + value + '</div>');
            });
            $('#ajsuformreload_mnotes button').prop('disabled', false);
        },
        success: function(data) {
            if (data.success) {
                $("#myModalNOTE").modal('toggle');
                $("#selling_order_search_form").submit();
            } else {
                $('#ajsuform_yu_mnotes').html('<div class="alert alert-danger">' + data.errors + '</div>');
                $('#ajsuformreload_mnotes button').prop('disabled', false);
            }
        }
    });
    return false;
});

$('body').on('click', '.more_order_notes_click', function(e) {
    var url = $(this).attr('data-url');
    var order = $(this).attr('data-num');

    if ($("#load_order_notes_" + order).hasClass('hide')) {
        $("#load_order_notes_" + order).removeClass('hide');
        $.ajax({
            type: 'POST',
            data: { order: order },
            url: url,
            success: function(data) {
                $('.load_order_notes').html('')
                $("#load_order_notes_" + order).html(data);
                $('.orders_selector_mul_reps').select2({ placeholder: "Select Rep" });
                $('.orders_selector_mul_tags').select2({ placeholder: "Select Tags" });

            }
        });
    } else {
        $("#load_order_notes_" + order).html('');
        $("#load_order_notes_" + order).addClass('hide');
    }
    return false;
});

$('body').on('submit', '.inventory_ruined_items_form', function(e) {
    e.preventDefault();
    var num = $(this).attr('data-num');
    $('#inventory_ruined_items_form_res' + num).empty();
    $('#inventory_ruined_items_form_res' + num).html('<div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div>');
    var action = $(this).attr('action');
    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#inventory_ruined_items_form_res' + num).html('<div class="alert alert-danger">' + value + '</div>');
            });
        },
        success: function(data) {
            if (data.success) {
                $('#inventory_ruined_items_form_res' + num).html('<div class="alert alert-success">' + data.message + '</div>');
            } else {
                $('#inventory_ruined_items_form_res' + num).html('<div class="alert alert-danger">' + data.errors + '</div>');
            }
        }
    });
    return false;
});

$('body').on('change', '#mylerz_neighborhood', function() {
    var action = $(this).attr('data-url');
    var city = $(this).val();
    $.ajax({
        type: 'POST',
        data: { city: city },
        url: action,
        success: function(data) {
            $('#mylerz_district').html(data);
        }
    });
});

$('body').on('change', '.discountine_product_checker', function() {
    var action = $(this).attr('data-url');
    $.ajax({
        type: 'POST',
        data: {},
        url: action,
        success: function(data) {

        }
    });
});

$('body').on('change', '.order_notes_checker', function() {
    var item = $(this).attr('data-item');
    var action = $(this).attr('data-url');
    $.ajax({
        type: 'POST',
        data: { item: item },
        url: action,
        success: function(data) {
            $('#note_row_' + item).toggleClass('completed_note');
        }
    });
});



$('body').on('input', '.sell_order_qty', function() {
    var qty = $(this).val();
    var item_id = $(this).attr('item-id');
    var action = $(this).attr('price-url');
    var item = $('#order_item_' + item_id).val();
    $.ajax({
        type: 'POST',
        data: { qty: qty, item: item },
        url: action,
        success: function(data) {
            $('#order_item_price_' + item_id).html(data);
        }
    });
});

$('body').on('change', '#client_city_selector', function() {
    var city = $(this).val();
    var action = $(this).attr('shipping-url');
    $.ajax({
        type: 'POST',
        data: { city: city },
        url: action,
        success: function(data) {
            $('#order_ship_price').val(data);
        }
    });
});

$('body').on('click', '.sellorder_notes_viewer', function() {
    var order = $(this).attr('order-num');
    var action = $(this).attr('url');
    $(this).parent().parent().parent().parent().removeClass('notes_not_viewed');
    $.ajax({
        type: 'POST',
        data: { order: order },
        url: action,
        success: function(data) {

        }
    });
});

$('body').on('submit', '.ajsuformreloadedit', function(e) {
    e.preventDefault();
    var num = $(this).attr('data-num');
    $('#ajsuform_yu_' + num).empty();
    $('#ajsuform_yu_' + num).html('<div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div>');
    var action = $(this).attr('action');
    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#ajsuform_yu_' + num).html('<div class="alert alert-danger">' + value + '</div>');
            });
        },
        success: function(data) {
            if (data.success) {
                location.reload();
            } else {
                $('#ajsuform_yu_' + num).html('<div class="alert alert-danger">' + data.errors + '</div>');
            }
        }
    });
    return false;
});

$('body').on('submit', '.ajsuformreditloc', function(e) {
    e.preventDefault();
    var num = $(this).attr('data-num');
    $('#ajsuform_yu_loc_' + num).empty();
    $('#ajsuform_yu_loc_' + num).html('<div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div>');
    var action = $(this).attr('action');
    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#ajsuform_yu_loc_' + num).html('<div class="alert alert-danger">' + value + '</div>');
            });
        },
        success: function(data) {
            if (data.success) {
                location.reload();
            } else {
                $('#ajsuform_yu_loc_' + num).html('<div class="alert alert-danger">' + data.errors + '</div>');
            }
        }
    });
    return false;
});

$('body').on('submit', '#ajsuformreload', function(e) {
    e.preventDefault();
    $('#ajsuform_yu').empty();
    $('#ajsuform_yu').html('<div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div>');
    $('#ajsuformreload button').prop('disabled', true);
    var action = $(this).attr('action');
    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#ajsuform_yu').html('<div class="alert alert-danger">' + value + '</div>');
            });
            $('#ajsuformreload button').prop('disabled', false);
        },
        success: function(data) {
            if (data.success) {
                location.reload();
            } else {
                $('#ajsuform_yu').html('<div class="alert alert-danger">' + data.errors + '</div>');
                $('#ajsuformreload button').prop('disabled', false);
            }
        }
    });
    return false;
});

$('body').on('click', '#change_selected_orders_rep', function(e) {
    var items = [];
    var table = $('#kt_table_2').DataTable();
    table.$(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    var type = $(this).attr('task');
    var action = $(this).attr('url');
    var rep = $("#all_orders_rep_seelctor").val();
    $.ajax({
        type: 'POST',
        data: { items: items, type: type, rep: rep },
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#change_selected_orders_rep_res').html('<div class="alert alert-danger">' + value + '</div>');
            });
        },
        success: function(data) {
            if (data.success) {
                $('#change_selected_orders_rep_res').html('<div class="alert alert-success">' + data.message + '</div>');
            } else {
                $('#change_selected_orders_rep_res').html('<div class="alert alert-danger">' + data.errors + '</div>');
            }
        }
    });
    return false;
});

$('body').on('click', '#calculate_selected_orders_amount', function(e) {
    $('#calcualte_selected_orders_amount').html('<div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div>');
    var items = [];
    var table = $('#kt_table_2').DataTable();
    table.$(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    var type = $(this).attr('task');
    var action = $(this).attr('url');
    $.ajax({
        type: 'POST',
        data: { items: items, type: type },
        url: action,
        success: function(data) {
            $('#calcualte_selected_orders_amount').html(data);
        }
    });
});

$('body').on('click', '#calculate_selected_orders_amount_ajax', function(e) {
    $('#calcualte_selected_orders_amount').html('<div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div>');
    var items = [];
    $(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    var type = $(this).attr('task');
    var action = $(this).attr('url');
    $.ajax({
        type: 'POST',
        data: { items: items, type: type },
        url: action,
        success: function(data) {
            $('#calcualte_selected_orders_amount').html(data);
        }
    });
});

$('body').on('click', '#selling_order_changing_status', function(e) {
    $('#change_selected_orders_status_res').html('');
});
$('body').on('click', '#change_selected_orders_status', function(e) {
    $('#change_selected_orders_status_res').html('<div class="fa-3x"><i class="fas fa-spinner fa-spin"></i></div>');
    var items = [];
    $('#change_selected_orders_status').prop('disabled', true);
    var table = $('#kt_table_2').DataTable();
    table.$(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    var type = $(this).attr('task');
    var action = $(this).attr('url');
    var status = $("#all_orders_status_seelctor").val();
    $.ajax({
        type: 'POST',
        data: { items: items, type: type, status: status },
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#change_selected_orders_status_res').html('<div class="alert alert-danger">' + value + '</div>');
            });
            $('#change_selected_orders_status').prop('disabled', false);
        },
        success: function(data) {
            if (data.success) {
                $('#change_selected_orders_status_res').html('<div class="alert alert-success">' + data.message + '</div>');
                $('#change_selected_orders_status').prop('disabled', false);
            } else {
                $('#change_selected_orders_status_res').html('<div class="alert alert-danger">' + data.errors + '</div>');
                $('#change_selected_orders_status').prop('disabled', false);
            }
        }
    });
    return false;
});

$('body').on('click', '#selling_order_changing_tags', function(e) {
    $('#change_selected_orders_tags_res').html('');
});
$('body').on('click', '#change_selected_orders_tags', function(e) {
    $('#change_selected_orders_tags_res').html('<div class="fa-3x"><i class="fas fa-spinner fa-spin"></i></div>');
    var items = [];
    $('#change_selected_orders_tags').prop('disabled', true);
    var table = $('#kt_table_2').DataTable();
    table.$(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    var type = $(this).attr('task');
    var action = $(this).attr('url');
    var tags = $("#all_orders_tags_seelctor").val();
    $.ajax({
        type: 'POST',
        data: { items: items, type: type, tags: tags },
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#change_selected_orders_tags_res').html('<div class="alert alert-danger">' + value + '</div>');
            });
            $('#change_selected_orders_tags').prop('disabled', false);
        },
        success: function(data) {
            if (data.success) {
                $('#change_selected_orders_tags_res').html('<div class="alert alert-success">' + data.message + '</div>');
                $('#change_selected_orders_tags').prop('disabled', false);
            } else {
                $('#change_selected_orders_tags_res').html('<div class="alert alert-danger">' + data.errors + '</div>');
                $('#change_selected_orders_tags').prop('disabled', false);
            }
        }
    });
    return false;
});

$('body').on('click', '#add_tags_to_products', function(e) {
    var items = [];
    $('#add_tags_to_products').prop('disabled', true);
    var table = $('#kt_table_1').DataTable();
    table.$(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    var action = $(this).attr('url');
    var tags = $("#product_tags").val();
    $.ajax({
        type: 'POST',
        data: { items: items, tags: tags },
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#change_selected_orders_status_res').html('<div class="alert alert-danger">' + value + '</div>');
            });
            $('#add_tags_to_products').prop('disabled', false);
        },
        success: function(data) {
            if (data.success) {
                $('#change_selected_orders_status_res').html('<div class="alert alert-success">' + data.message + '</div>');
                $('#add_tags_to_products').prop('disabled', false);
            } else {
                $('#change_selected_orders_status_res').html('<div class="alert alert-danger">' + data.errors + '</div>');
                $('#add_tags_to_products').prop('disabled', false);
            }
        }
    });
    return false;
});

$('body').on('click', '#check_avilable_items', function(e) {
    var items = [];
    var table = $('#kt_table_2').DataTable();
    table.$(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    var action = $(this).attr('url');
    $.ajax({
        type: 'POST',
        data: { items: items },
        url: action,
        success: function(data) {
            location.reload();
        }
    });
    return false;
});

$('body').on('click', '#delete_selected_orders', function(e) {
    var items = [];
    var table = $('#kt_table_2').DataTable();
    table.$(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    var type = $(this).attr('task');
    var action = $(this).attr('url');
    $.ajax({
        type: 'POST',
        data: { items: items, type: type },
        url: action,
        success: function(data) {
            location.reload();
        }
    });
    return false;
});


$('body').on('click', '.export_selected_notes', function(e) {
    var items = [];
    var table = $('#notestable');
    $(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    if (items.length > 0) {
        var action = $(this).attr('url');
        window.open(action + '/?orders=' + items, '_blank');
    }
    return false;
});


$('body').on('click', '.export_selected_products', function(e) {
    var items = [];
    var table = $('#kt_table_1').DataTable();
    table.$(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    if (items.length > 0) {
        var action = $(this).attr('url');
        window.open(action + '/?products=' + items, '_blank');
    }
    return false;
});

$('body').on('click', '.get_selected_orders_shiping_info', function(e) {
    var items = [];
    var table = $('#kt_table_2').DataTable();
    table.$(".check_single:checked").each(function(index, value) {
        items.push($(value).val());
    });
    if (items.length > 0) {
        var type = $(this).attr('task');
        var action = $(this).attr('url');
        window.open(action + '/' + type + '?orders=' + items, '_blank');
    }
    return false;
});

$('body').on('change', '.selling_order_status', function(e) {
    var action = $(this).attr('url');
    var num = $(this).attr('num');
    var status = $(this).val();
    $.ajax({
        type: 'POST',
        data: { 'num': num, 'status': status },
        url: action,
        success: function(data) {}
    });
    return false;
});

function uniqId() {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < 10; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    result = Math.round(new Date().getTime() + (Math.random() * 100)) + '_' + result;
    return result;
}

$('body').on('submit', '#ajsuform', function(e) {
    e.preventDefault();
    $('#ajsuform button').prop('disabled', true);
    $('#ajsuform_yu').empty();
    $('#ajsuform_yu').html('<div class="fa-3x"><i class="fas fa-spinner fa-spin"></i></div>');
    $('.order_item_details').addClass('height_zero');
    $('.order_item_collapse').removeClass('height_zero');
    var action = $(this).attr('action');
    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#ajsuform_yu').html('<div class="alert alert-danger">' + value + '</div>');
            });
            $('#ajsuform button').prop('disabled', false);
        },
        success: function(data) {
            if (data.success) {
                location.reload();
                $('#ajsuform button').prop('disabled', false);
            } else {
                $('#ajsuform_yu').html('<div class="alert alert-danger">' + data.errors + '</div>');
                $('#ajsuform button').prop('disabled', false);
            }
        }
    });
    return false;
});

$('body').on('submit', '#ajsXform', function(e) {
    e.preventDefault();
    $('#ajsuform_yu').empty();
    $('#ajsuform_yu').html('<div class="fa-3x"><i class="fas fa-spinner fa-spin"></i></div>');
    $('.order_item_details').addClass('height_zero');
    $('.order_item_collapse').removeClass('height_zero');
    var action = $(this).attr('action');
    var uu = $(this).attr('data-url');
    $('#ajsXform button').prop('disabled', true);
    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#ajsuform_yu').html('<div class="alert alert-danger">' + value + '</div>');
            });
            $('#ajsXform button').prop('disabled', false);
        },
        success: function(data) {
            if (data.success) {
                window.location.href = uu;
                $('#ajsXform button').prop('disabled', false);
            } else {
                $('#ajsuform_yu').html('<div class="alert alert-danger">' + data.errors + '</div>');
                $('#ajsXform button').prop('disabled', false);
            }
        }
    });
    return false;
});

$('body').on('click', '.collapse_details_box', function(e) {
    var box = $(this).attr('box');
    $('#single_order_item_box_' + box + ' .order_item_details').addClass('height_zero');
    $('#single_order_item_box_' + box + ' .order_item_collapse').removeClass('height_zero');
    return false;
});

$('body').on('click', '.uncollapse_details_box', function(e) {
    var box = $(this).attr('box');
    $('#single_order_item_box_' + box + ' .order_item_details').removeClass('height_zero');
    $('#single_order_item_box_' + box + ' .order_item_collapse').addClass('height_zero');
    return false;
});
$('body').on('click', '.delete_order_item', function(e) {
    var box = $(this).attr('box');
    $('#single_order_item_box_' + box).remove();
    return false;
});

$('body').on('click', '.buy_delete_order_item', function(e) {
    var box = $(this).attr('box');
    $('#single_order_item_box_' + box).remove();
    var action = $('.buyorder_items_qty').attr('data-url');
    var formData = new FormData($('#ajsuform')[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        success: function(data) {
            $('#buyorder_qty').html(data.qty);
            $('#buyorder_total').html(data.total);
        }
    });

    return false;
});

$('body').on('change', '.order_product_item', function(e) {
    var action = $(this).attr('options-url');
    var item_id = $(this).attr('item-id');
    var item = $(this).val();
    var type = $('#add_order_item').attr('item-type');
    $('#order_item_options_' + item_id).html('<div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div>');
    if (type == 'buy') {
        $.ajax({
            type: 'POST',
            data: { 'item': item, 'type': type, 'item_id': item_id, 'column': 'color' },
            url: action,
            success: function(data) {
                $('#color_order_item_' + item_id).html(data);
            }
        });
        $.ajax({
            type: 'POST',
            data: { 'item': item, 'type': type, 'item_id': item_id, 'column': 'size' },
            url: action,
            success: function(data) {
                $('#size_order_item_' + item_id).html(data);
            }
        });
    } else {
        $.ajax({
            type: 'POST',
            data: { 'item': item, 'type': type, 'item_id': item_id },
            url: action,
            success: function(data) {
                $('#order_item_options_' + item_id).html(data);
            }
        });
    }
    if ($('#order_item_price_' + item_id).length) {
        var qty = $('#sell_order_qty_' + item_id).val();
        var action = $(this).attr('price-url');
        $.ajax({
            type: 'POST',
            data: { qty: qty, item: item },
            url: action,
            success: function(data) {
                $('#order_item_price_' + item_id).html(data);
            }
        });
    }
    if ($('#order_item_available_units_' + item_id).length) {
        var action = $(this).attr('available-url');
        var color = 0;
        var size = 0;
        $.ajax({
            type: 'POST',
            data: { item: item, color: color, size: size },
            url: action,
            success: function(data) {
                $('#order_item_available_units_' + item_id).html(data);
            }
        });
    }
    return false;
});

$('body').on('change', '.item_color_selector, .item_size_selector', function(e) {
    var item_id = $(this).attr('data-itemid');
    var action = $(this).attr('available-url');
    var color = $("#item_color_selector" + item_id).val();
    var item = $(this).attr('data-item');
    var size = $("#item_size_selector" + item_id).val();
    $.ajax({
        type: 'POST',
        data: { item: item, color: color, size: size },
        url: action,
        success: function(data) {
            $('#order_item_available_units_' + item_id).html(data);
        }
    });
    return false;
});

$('body').on('click', '#add_order_item', function(e) {
    var action = $(this).attr('button-url');
    var type = $(this).attr('item-type');
    $.ajax({
        type: 'POST',
        data: { 'order_item': uniqId(), 'type': type },
        url: action,
        success: function(data) {

            if (type == 'buy') {
                $('#order_products').append(data);
            } else {
                $('#order_products').prepend(data);

            }
            $('.order_product_item').select2({ placeholder: "Select Order Item" });
        }
    });
    return false;
});

$('body').on('keyup change paste', '.buyorder_items_qty, .buyorder_items_price', function(e) {
    var action = $(this).attr('data-url');
    var item = $(this).attr('item-id');
    var qty = $('#buyorder_qty_' + item).val();
    var price = $('#buyorder_price_' + item).val();

    $.ajax({
        type: 'POST',
        data: { qty: qty, price: price, 'single_row': true },
        url: action,
        success: function(data) {
            $('#buyorder_subtotal_' + item).html(data.subtotal);
        }
    });
    var formData = new FormData($('#ajsuform')[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        success: function(data) {
            $('#buyorder_qty').html(data.qty);
            $('#buyorder_total').html(data.total);
        }
    });
    return false;
});

$('body').on('keyup', '#client_search', function(e) {
    var action = $(this).attr('data-url');
    var search = $(this).val();
    var lengthOfSearch = search.length;

    var errorClass = 'error';

    if (lengthOfSearch > 11) {
        $(this).addClass(errorClass);
    } else {
        $(this).removeClass(errorClass);
    }


    var numericSearch = parseInt(search, 10); // or Number(search);

    if (!isNaN(numericSearch) && search.length === 11 && search.startsWith("01")) {

        $.ajax({
            type: 'POST',
            data: {search: search},
            url: action,
            success: function (data) {
                $('#order_client_info').html(data);
                if ($('#client_city_selector').length) {
                    $('#client_city_selector').select2();
                }
            }
        });
    }
    else {
        $('#order_client_info').html('');

    }
    return false;
});

$('body').on('input change paste', '#main_cat_selector', function(e) {
    var action = $(this).attr('data-url');
    var cat = $(this).val();
    $.ajax({
        type: 'POST',
        data: { cat: cat },
        url: action,
        success: function(data) {
            $('#cat_selector').html(data);
        }
    });
    return false;
});

$('body').on('click', "#checkAll", function() {
    $('.check_single:checkbox').not(this).prop('checked', this.checked);
});

$('body').on('click', "#checkAllJX", function() {
    $('input:checkbox').not(this).prop('checked', this.checked);
});

$('body').on('change', "#mylerz_shipping_checker", function() {
    if ($(this).prop('checked')) {
        var action = $(this).attr('data-url');
        $.ajax({
            type: 'POST',
            data: {},
            url: action,
            success: function(data) {
                $('#mylerz_checker_result').html(data);
            }
        });
    } else {
        $('#mylerz_checker_result').empty();
    }
    return false;
});

$('body').on('keyup', function(event) {

    if (event.ctrlKey && event.key === 'm') {
        event.preventDefault();
        window.open('http://three-store.com/selling_order/create', '_blank');
    }
    return false;
});


$('body').on('submit', '#new_expanse', function(e) {
    e.preventDefault();
    var action = $(this).attr('action');
    $('#new_expanse_res').html('<div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div>');

    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#new_expanse_res').html('<div class="alert alert-danger">' + value + '</div>');
            });
        },
        success: function(data) {
            if (data.success) {
                $('#new_expanse_res').html('<div class="alert alert-success">Expanse Added Successfully</div>');
            } else {
                $('#new_expanse_res').html('<div class="alert alert-danger">' + data.errors + '</div>');
            }
        }
    });
    return false;
});

$('body').on('submit', '.expanse_update_from', function(e) {
    e.preventDefault();
    var exp = $(this).attr('data-expanse');
    var action = $(this).attr('action');
    $('#update_expanse_res' + exp).html('');
    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value) {
                $('#update_expanse_res' + exp).html('<div class="alert alert-danger">' + value + '</div>');
            });
        },
        success: function(data) {
            if (data.success) {
                location.reload();
            } else {
                $('#update_expanse_res' + exp).html('<div class="alert alert-danger">' + data.errors + '</div>');
            }
        }
    });
    return false;
});

var loader = `
    <div className="linear-background">
        <div className="inter-crop"></div>
        <div className="inter-right--top"></div>
        <div className="inter-right--bottom"></div>
    </div>
    `;
$(document).on('click', '.showTimeLine', function () {
    var route_time_line=$(this).attr('data-route');

    $('#modal-body').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>')
    $('#operationType').text('timeline')

    $('#timeLineModal').modal('show')



    setTimeout(function () {
        $('#modal-body').load(route_time_line)
    }, 500)

});


$(document).on('click','.showNotes',function (){
   var order_id=$(this).attr('order-num')
    var route_notes=$(this).attr('data-route-notes')
    $('#operationType').text('Notes')
    $('#modal-body').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>')
    $('#timeLineModal').modal('show')



    setTimeout(function () {
        $('#modal-body').load(route_notes)
    }, 500)



})



$(document).on('click', '.deleteOrderBtn', function () {

    var id = $(this).data('id');
    swal.fire({
        title: "Are you sure to delete?",
        text: "Can't you undo then?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Ok",
        cancelButtonText: "Cancel",
        okButtonText: "Ok",
        closeOnConfirm: false
    }).then((result) => {
        if (!result.isConfirmed) {
            return true;
        }


    var url = $(this).attr('data-route');
    $.ajax({
        url: url,
        data:{
            'type':'json',
        },
        type: 'DELETE',
        // beforeSend: function () {
        //     $('.loader-ajax').show()
        //
        // },

        success: function (data) {

            window.setTimeout(function () {
                // $('.loader-ajax').hide()

                toastr.success( 'تم اضافة البيانات بنجاح');
                $(`#tr_${id}`).remove();
            }, 1000);
        }, error: function (data) {

            if (data.code === 500) {
                toastr.error('there is an error')
            }


            if (data.code === 422) {
                var errors = $.parseJSON(data.responseText);

                $.each(errors, function (key, value) {
                    if ($.isPlainObject(value)) {
                        $.each(value, function (key, value) {
                            toastr.error(value)
                        });

                    } else {

                    }
                });
            }
        }

    });
    });
});

$(document).on('submit', 'Form#addFormNotes', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var url = $('#addFormNotes').attr('action');
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        beforeSend: function () {
            $('#addButton').html('<span style="margin-right: 4px;">انتظر ..</span><i class="bx bx-loader bx-spin"></i>').attr('disabled', true);
        },
        success: function (data) {
            if (data.success == true) {
                // $('#main-datatable').DataTable().ajax.reload(null, false);
                // show custom message or use the default
                toastr.success('تمت العمليه بنجاح');
                $('#addFormNotes')[0].reset()
                $('#timeLineModal').modal('hide')


            } else {

                            toastr.error(data.errors);

            }
            $('#addButton').html(`Update`).attr('disabled', false);
        },
        error: function (data) {
            if (data.status === 500) {
                toastr.error('عذرا هناك خطأ فني 😞');
            } else if (data.status === 422) {
                var errors = $.parseJSON(data.responseText);
                $.each(errors, function (key, value) {
                    if ($.isPlainObject(value)) {
                        $.each(value, function (key, value) {
                            toastr.error(value);
                        });
                    }
                });
            } else
                toastr.error('عذرا هناك خطأ فني 😞');
            $('#addButton').html(`Update`).attr('disabled', false);
        },//end error method

        cache: false,
        contentType: false,
        processData: false
    });
});  





