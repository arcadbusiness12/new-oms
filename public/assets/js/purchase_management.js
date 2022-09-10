var xhr = {};
$(document).ready(function() {
    $("#filter_products").submit(function(event) {
        event.preventDefault();
        $.ajax({
            method: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: "html",
            beforeSend: function() {
                $('#search_filter').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
                $('#search_filter').prop('disabled', true);
            },
            complete: function() {
                $('#search_filter').html('<i class="fa fa-filter"></i>Search');
                $('#search_filter').prop('disabled', false);
            }
        }).done(function(html) {
            $('.product_list').show();
            if (html.length) {
                $(".product_list").find('.alert-danger').remove();
                $(".product_list").prepend(html);
                $(".instruction_row").show();
            } else {
                $(".product_list").prepend('<div class="alert alert-danger">Product does not attached with the inventory!</div>');
            }
        });
    });
    $("#add_manually").click(function() {
        $this = $(this);
        $.ajax({
            method: "GET",
            url: $(this).attr('data-action'),
            dataType: "html",
            beforeSend: function() {
                $this.html('<i class="fa fa-spin fa-circle-o-notch"></i>');
                $this.prop('disabled', true);
            },
            complete: function() {
                $this.html('Add Manually');
                $this.prop('disabled', false);
            }
        }).done(function(html) {
            $('.product_list').show();
            if (html.length) {
                $(".product_list").find('.alert-danger').remove();
                $(".product_list").prepend(html);
                $(".instruction_row").show();
            } else {
                $(".product_list").prepend('<div class="alert alert-danger">Product Not Found!</div>');
            }
        });
    });
    /*$(document).delegate(".manually_option_size", "change", function (){
        $this = $(this);
        _this_parent = $(this).parents('.product_list_row').find('.manually_option_row');
        var option_id = $(this).val();
        $.ajax({
            method: "POST",
            url: APP_URL + "/purchase_manage/get_manually_produt_option_value",
            data: {
                option_id : option_id
            },
            dataType: "json",
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            beforeSend: function(){
                $this.after('<i class="fa fa-spin fa-circle-o-notch"></i>');
                $this.prop('disabled',true);
            },complete:function(){
                $this.next('.fa-circle-o-notch').remove();
                $this.prop('disabled',false);
            }
        }).done(function (json){
            html = '';
            $.each(json, function(k,v){
                html += '<option value="'+v.option_value_id+'">'+v['name']+'</option>';
            });
            fs = _this_parent.find('[data-option-id-'+$this.find("option.preactive").val()+']');
            fs.html(html);
            fs.removeAttr('data-option-id-'+$this.find("option.preactive").val());
            fs.attr('data-option-id-'+option_id,'');
            $this.find("option").removeClass("preactive");
            $this.find("option[value='"+option_id+"']").addClass("preactive");
        });
    });*/
    $(document).delegate(".add_manually_option", "click", function() {
        $this = $(this);
        _this_parent = $(this).parents('.product_list_row').find('.manually_option_row');
        var data_id = $(this).attr('data-product-id');
        var option_color = $(this).parents('.product_row').find('#manually_option_color').val();
        var option_size = $(this).parents('.product_row').find('#manually_option_size').val();
        $.ajax({
            method: "POST",
            url: APP_URL + "/purchase_manage/get_manually_produt_option",
            data: {
                data_product_id: data_id,
                color: option_color,
                size: option_size,
            },
            dataType: "html",
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            beforeSend: function() {
                $this.html('<i class="fa fa-spin fa-circle-o-notch"></i>');
                $this.prop('disabled', true);
            },
            complete: function() {
                $this.html('Add More');
                $this.prop('disabled', false);
            }
        }).done(function(html) {
            if (html != "") {
                $(_this_parent).find('.option_row').append(html);
            }
        });
    });
    $(document).delegate(".add_selected_options", "click", function() {
        $this = $(this);
        $this_text = $(this).text();
        _this_parent = $(this).parents('.product_list_row').find('.all_options_row');
        var data_id = $(this).attr('data-product-id');
        var option_color = $(_this_parent).find('#manually_option_color').val();
        var option_size = $(_this_parent).find('#manually_option_size').val();
        $.ajax({
            method: "POST",
            url: APP_URL + "/purchase_manage/get_manually_all_options",
            data: {
                data_product_id: data_id,
                color: option_color,
                size: option_size,
            },
            dataType: "html",
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            beforeSend: function() {
                $this.html('<i class="fa fa-spin fa-circle-o-notch"></i>');
                $this.prop('disabled', true);
            },
            complete: function() {
                $this.html($this_text);
                $this.prop('disabled', false);
            }
        }).done(function(html) {
            if (html != "") {
                $this.parents('.product_list_row').find('.manually_option_row').html(html);
            }
        });
    });
    $(document).delegate('#product_title', 'keyup', function() {
        _this = $(this);
        if (typeof xhr['add_product_title_keyup'] != 'undefined' && xhr['add_product_title_keyup'].readyState != 4) {
            xhr['add_product_title_keyup'].abort();
        }
        xhr['add_product_title_keyup'] = $.ajax({
            method: "POST",
            url: APP_URL + "/purchase_manage/get_product_name",
            data: {
                product_name: $(this).val()
            },
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
        }).done(function(data) {
            html = '';
            if (data.titles) {
                $.each(data.titles, function(k, v) {
                    html += '<option value="' + v + '">';
                });
                $('#product_names').html(html);
            }
        });
    });
    $(document).delegate('#product_model', 'keyup', function() {
        _this = $(this);
        if (typeof xhr['add_product_model_keyup'] != 'undefined' && xhr['add_product_model_keyup'].readyState != 4) {
            xhr['add_product_model_keyup'].abort();
        }
        xhr['add_product_model_keyup'] = $.ajax({
            method: "POST",
            url: APP_URL + "/purchase_manage/get_product_model",
            data: {
                product_model: $(this).val()
            },
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
        }).done(function(data) {
            console.log(data);
            html = '';
            if (data.models) {
                $.each(data.models, function(k, v) {
                    html += '<option value="' + v + '">';
                });
                $('#product_models').html(html);
            }
        });
    });
    $(document).delegate('#product_sku', 'keyup', function() {
        _this = $(this);
        if (typeof xhr['get_product_sku_keyup'] != 'undefined' && xhr['get_product_sku_keyup'].readyState != 4) {
            xhr['get_product_sku_keyup'].abort();
        }
        xhr['get_product_sku_keyup'] = $.ajax({
            method: "POST",
            url: APP_URL + "/purchase_manage/get_product_sku",
            data: {
                product_sku: $(this).val()
            },
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
        }).done(function(data) {
            html = '';
            if (data.skus) {
                $.each(data.skus, function(k, v) {
                    html += '<option value="' + v + '">';
                });
                $('#product_skus').html(html);
            }
        });
    });
    $(document).delegate('#product_category', 'keyup', function() {
        _this = $(this);
        if (typeof xhr['get_product_category_keyup'] != 'undefined' && xhr['get_product_category_keyup'].readyState != 4) {
            xhr['get_product_category_keyup'].abort();
        }
        xhr['get_product_category_keyup'] = $.ajax({
            method: "POST",
            url: APP_URL + "/purchase_manage/get_product_category",
            data: {
                product_category: $(this).val()
            },
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
        }).done(function(data) {
            html = '';
            if (data.categories) {
                $.each(data.categories, function(k, v) {
                    html += '<option value="' + v + '">';
                });
                $('#product_categories').html(html);
            }
        });
    });
    $(document).delegate('.add_more_option', 'click', function() {
        _this = $(this);
        _this_parent = $(this).parents('.product_list_row').find('.option_list_row');
        var product_id = $(this).val();
        $.ajax({
            method: "POST",
            url: APP_URL + "/purchase_manage/get_product_order_option",
            data: {
                product_id: $(this).val()
            },
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            beforeSend: function() {
                $(_this).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
                $(_this).prop('disabled', true);
            },
            complete: function() {
                $(_this).html('Add More');
                $(_this).prop('disabled', false);
            },
        }).done(function(html) {
            if (html != "") {
                $(_this_parent).find('table').append(html);
            }
        });
    });
    $('.order_list .order_quantity, .order_list .price, .order_list .local_express_cost').change(function() {
        _this = $(this);
        var _this_order_quantity = $(_this).parents('.single_option_row').find('.order_quantity').val();
        var _this_price = $(_this).parents('.single_option_row').find('.price').val();
        var _this_sum = $(_this).parents('.single_option_row').find('.sum');
        var _this_sub_total = $(_this).parents('.order_list').find('.sub_total');
        var _this_local_cost = $(_this).parents('.order_list').find('.local_express_cost');
        var _this_total = $(_this).parents('.order_list').find('.total');

        $(_this).parents('.options_row').find('.price').val(_this_price);

        $.each($(_this).parents('.options_row').find('.sum'), function(k, v) {
            var order_quantity = $(v).parents('.single_option_row').find('.order_quantity').val();
            var price = $(v).parents('.single_option_row').find('.price').val();
            var particular_sum = order_quantity * price;
            $(v).val(particular_sum);
        });
        /*var sum = _this_price * _this_order_quantity;
        $(_this_sum).val(sum.toFixed(2));*/

        // Sub Total
        var sub_total = 0;
        var _this_all_sum = $(_this).parents('.order_list').find('.sum');
        $.each(_this_all_sum, function(k, v) {
            var sum_val = $(v).val();
            sub_total = Number(sub_total) + Number(sum_val);
        });
        $(_this_sub_total).val(sub_total.toFixed(2));

        // Main Total
        var _this_sub_total_val = $(_this_sub_total).val();
        var _this_local_cost_val = $(_this_local_cost).val();
        var main_total = Number(_this_sub_total_val) + Number(_this_local_cost_val);
        $(_this_total).val(main_total.toFixed(2));
    });

    $('.copy_to_clipboard').click(function() {
        var copyText = $(this);
        copyText.select();
        document.execCommand("copy");
    });
    $(document).delegate('.collapse-comment', 'click', function() {
        var target = $(this).attr('data-target');
        $('#' + target).slideToggle(250);
    });
    $(document).delegate('.collapse-product-option', 'click', function() {
        var target = $(this).attr('data-target');
        $('#' + target).slideToggle(250);
    })
    $(document).delegate('.product_option_dropdown', 'change', function() {
        _this = $(this);
        _this_parent = $(this).parents('.options_row');
        $.ajax({
            method: "POST",
            url: APP_URL + "/purchase_manage/get_product_option_detail",
            data: {
                product_id: $(this).attr('data-product-id'),
                option_id: $(this).attr('data-option-id'),
                option_value_id: $(this).val(),
            },
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            beforeSend: function() {
                $('.product_list').append('<div class="loader"><i class="fa fa-circle-o-notch fa-spin"></i></div>');
            },
            complete: function() {
                $('.product_list').find('.loader').remove();
            },
        }).done(function(json) {
            if (json) {
                $(_this_parent).find('.quantity').html('<b class="fa-2x">' + json.quantity + '</b> Available');
                $(_this_parent).find('.average').html(json.average_quantity + ' pcs / ' + json.duration + ' Days');
                /*$(_this_parent).find('.input_quantity').attr('min', Math.max(1, json.minimum_quantity));*/
            }
        });
    });
    $(document).delegate('#form_pack_order', 'submit', function(e) {
        e.preventDefault();
        $.ajax({
            method: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
        }).done(function(html) {
            $('#pack_order_row').html(html);
            JsBarcode(".barcode").init();
            $('[name="pick_barcode_scan"]').focus();
        });
    });
    $(document).delegate('.product-pack-bacode-checkbox', 'change', function() {
        var total_checkbox = $('.product-pack-bacode-checkbox');
        var checked = $('.product-pack-bacode-checkbox:checked');
        if (checked.length == total_checkbox.length) {
            $('#pack_order .row_update_picked').slideDown(250);
        } else {
            $('#pack_order .row_update_picked').slideUp(250);
        }
    });
    $(document).delegate('#form_return_order', 'submit', function(e) {
        e.preventDefault();
        $.ajax({
            method: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
        }).done(function(html) {
            $('#return_order_row').html(html);
            JsBarcode(".barcode").init();
            $('[name="return_barcode_scan"]').focus();
        });
    });
    $(document).delegate('.product-return-bacode-checkbox', 'change', function() {
        var total_checkbox = $('.product-return-bacode-checkbox');
        var checked = $('.product-return-bacode-checkbox:checked');
        if (checked.length == total_checkbox.length) {
            $('#return_order .row_update_returned').slideDown(250);
        } else {
            $('#return_order .row_update_returned').slideUp(250);
        }
    });
    $(document).delegate('#accounts .pagination-area ul li a', 'click', function(e) {
        e.preventDefault();
        var parent = $(this).parents('.tab-pane').attr('id');
        var url = $(this).attr('href');
        var ac = getUrlVars(url, parent);
        $.ajax({
            method: "POST",
            url: APP_URL + '/purchase_manage/get_ajax_accounts',
            data: ac,
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            beforeSend: function() {
                $('.panel_order_list').append('<div class="loader"><i class="fa fa-circle-o-notch fa-spin"></i></div>');
            },
            complete: function() {
                $('.panel_order_list').find('.loader').remove();
            },
        }).done(function(html) {
            $('#' + parent).html(html);
        });
    });
    $(document).delegate('#supplier_accounts .pagination-area ul li a', 'click', function(e) {
        e.preventDefault();
        var parent = $(this).parents('.tab-pane').attr('id');
        var url = $(this).attr('href');
        var ac = getUrlVars(url, parent);
        $.ajax({
            method: "POST",
            url: APP_URL + '/purchase_manage/get_ajax_supplier_accounts',
            data: ac,
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            beforeSend: function() {
                $('.panel_order_list').append('<div class="loader"><i class="fa fa-circle-o-notch fa-spin"></i></div>');
            },
            complete: function() {
                $('.panel_order_list').find('.loader').remove();
            },
        }).done(function(html) {
            $('#' + parent).html(html);
        });
    });
    $(document).delegate('.account-summary-pagination-area ul li a', 'click', function(e) {
        e.preventDefault();
        var parent = $(this).parents('.tab-pane').attr('id');
        var url = $(this).attr('href');
        var ac = getUrlVars(url, parent);
        $.ajax({
            method: "POST",
            url: APP_URL + '/account_summary_report_ajax',
            data: ac,
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            beforeSend: function() {
                $('.panel_order_list').append('<div class="loader"><i class="fa fa-circle-o-notch fa-spin"></i></div>');
            },
            complete: function() {
                $('.panel_order_list').find('.loader').remove();
            },
        }).done(function(html) {
            $('#result_summary').html(html);
        });
    });

    function getUrlVars(url = '', type = '') {
        var vars = {},
            hash;
        var hashes = url.slice(url.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            //vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        vars['type'] = type;
        return vars;
    }
    // $(document).delegate('.select_supplier ','change',function(){
    //     var supplier = $(this).val();

    //     $.ajax({
    //         method: "POST",
    //         url: APP_URL + "/account_summary_report_ajax",
    //         data: {supplier : supplier},
    //         headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
    //         beforeSend: function(){
    //             $('.panel_order_list').append('<div class="loader"><i class="fa fa-circle-o-notch fa-spin"></i></div>');
    //         },complete:function(){
    //             $('.panel_order_list').find('.loader').remove();
    //         },
    //     }).done(function (html){
    //         $('#result_summary').html(html);
    //     });
    // });
    $(document).on('change keyup', '.select_supplier ', function() {
        var supplier = $('#supplier').val();
        var order_id = $('#order_id').val();

        $.ajax({
            method: "POST",
            url: APP_URL + "/account_summary_report_ajax",
            data: { supplier: supplier, order_id: order_id },
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            beforeSend: function() {
                $('.panel_order_list').append('<div class="loader"><i class="fa fa-circle-o-notch fa-spin"></i></div>');
            },
            complete: function() {
                $('.panel_order_list').find('.loader').remove();
            },
        }).done(function(html) {
            $('#result_summary').html(html);
        });
    });
    $(document).delegate('.submit-cancel-order', 'click', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure to cancel this order?')) {
            return false;
        } else {
            $(this).html('<i class="icon icon-spin icon-circle-o-notch"></i>');
            // $('.cancel-btn').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            var $this = $(this);
            var order = $(this).parents('.order_list');
            var form = $(this).parents('form');
            var action = form.attr('action');
            $.ajax({
                url: action,
                type: 'POST',
                dataType: 'json',
                data: form.serialize() + '&submit=cancel',
                beforeSend: function() {
                    $this.prop('disabled', true);
                },
                success: function(data) {
                    console.log(data);
                    
                    if (data.success) {
                        // $('.stock-cancel-request-tag').html('<div class="label label-warning">Cancel Request Sent</div>');
                        $this.html('<div class="label label-warning">Cancel Request Sent</div>');
                        $(order).remove();
                        $('.message').html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + data.message + '</div>');
                    } else {
                        $('.cancel-btn').html('');
                        $('.message').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + data.message + '</div>');
                    }
                },
                complete: function(r) {
                    // console.log(r.status);
                    $('.cancel-btn').html('');
                    if(r.status == 200) {
                        // $('.stock-cancel-request-tag').html('<div class="label label-warning">Cancel Request Sent</div>');
                        $this.closest('.stock-cancel-request-tag').html('<div class="label label-warning">Cancel Request Sent</div>');
                    }
                    $this.prop('disabled', false);
                }
            });
            return false;
        }
    });
    $(document).delegate('.btn-delete-order', 'click', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure to delete this order?')) {
            return false;
        } else {
            var $this = $(this);
            var order = $(this).parents('.order_list');
            var form = $(this).parents('form');
            var action = form.attr('action');
            $.ajax({
                url: action,
                type: 'POST',
                dataType: 'json',
                data: form.serialize() + '&submit=delete-order',
                beforeSend: function() {
                    $this.prop('disabled', true);
                },
                success: function(data) {
                    if (data.success) {
                        $(order).remove();
                        $('.message').html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + data.message + '</div>');
                    } else {
                        $('.message').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + data.message + '</div>');
                    }
                },
                complete: function() {
                    $this.prop('disabled', false);
                }
            });
            return false;
        }
    });
    $(document).delegate('.update_refund_order', 'click', function() {
        if (!confirm('Are you sure to give refund?')) return false;
    });
    $(document).delegate('input[name=\'print_label\']', 'change', function() {
        var checked = $('input[name=\'print_label\']:checked');
        if (checked.length) {
            $('.print_msg').text('');
        } else {
            $('.print_msg').text('Please Select any one label');
        }
    });
    $(document).delegate('input[name=\'shipped\']', 'change', function() {
        var checked = $('input[name=\'shipped\']:checked');
        if (checked.length) {
            $('.shipped_msg').text('');
        } else {
            $('.shipped_msg').text('Please Select any one label');
        }
    });
    $(document).delegate('#add_complaint_order_form', 'submit', function(e) {
        e.preventDefault();
        $.ajax({
            method: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
        }).done(function(html) {
            $('#add_complaint_order_row').html(html);
        });
    });
    $('#open-payment-modal').click(function() {
        $("#payment-modal .response-message").html('');
        $('#payment-form')[0].reset();
        $('#payment-modal').modal({ backdrop: 'static', keyboard: false })
    });
    $('.pay-payment').click(function() {
        $("#payment-modal .response-message").html('');
        $('#payment-form')[0].reset();
        var _this = $(this).parents('tr');
        var user_id = $(_this).find('[name="user_id"]').val();
        var amount = $(_this).find('[name="amount"]').val();

        $('#payment-modal select[name="supplier"] option[value="' + user_id + '"]').prop('selected', true);
        $('#payment-modal select[name="supplier"]').trigger('change');
        $('#payment-modal input[name="amount"]').val(amount);

        $('#payment-modal').modal({ backdrop: 'static', keyboard: false })
    });
    $('#payment-form').submit(function(e) {
        $("#payment-modal .response-message").html('');
        e.preventDefault();
        var formData = new FormData($(this)[0]);

        $.ajax({
            method: "POST",
            url: $(this).attr('action'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            beforeSend: function() {
                $('#send_payment').text('loading');
                $('#send_payment').prop('disabled', true);
            },
            complete: function() {
                $('#send_payment').text('Send Payment');
                $('#send_payment').prop('disabled', false);
            },
            success: function(json) {
                if (json['error']) {
                    $("#payment-modal .response-message").html('<div class="alert alert-danger">' + json['error'] + '</div>');
                }
                if (json['success']) {
                    $("#payment-modal .response-message").html('<div class="alert alert-success">' + json['success'] + '</div>');
                }
                $('#payment-form')[0].reset();
            }
        });
    });
    $(document).delegate('.ship_to_dubai', 'click', function() {
        var order_id = $(this).attr('data-order-id');
        var shipped_id = $(this).attr('data-shipped-id');
        $('#ship_to_dubai_modal').find('[name="order_id"]').val(order_id);
        $('#ship_to_dubai_modal').find('[name="shipped_id"]').val(shipped_id);

        $('#ship_to_dubai_modal').modal({ backdrop: 'static', keyboard: false });
    });
    // Form Suvmit Button Disable
    $('#form-add-order').submit(function(e) {
        $this = $(this);
        $('#add_purchase_order .error-msg').text('');
        e.preventDefault();
        var form = $('#form-add-order')[0];
        var formData = new FormData(form);
        $.ajax({
            url: APP_URL + '/purchase_manage/add_order',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#submit-add-order').prop('disabled', true);
            },
            success: function(json) {
                console.log(json);
                if (json.success) {
                    location.href = json.redirect;
                } else {
                    $('#add_purchase_order .error-msg').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + json.error + '</div>');
                }
            },
            complete: function() {
                $('#submit-add-order').prop('disabled', false);
            },
            error: function(xhr, exception) {
                alert(xhr.responseText);
                return false;
            }
        });
    });

    /*$('.submit-awaiting-action').click(function(){
        var value = $(this).val();
        $(this).parents('.form-awaiting-action').find('[name="submit"]').val(value);
    });
    $('.form-awaiting-action').submit(function(){
        $('.submit-awaiting-action').prop('disabled', true);
    });

    $('.submit-awaiting-approval').click(function(){
        var value = $(this).val();
        $(this).parents('.form-awaiting-approval').find('[name="submit"]').val(value);
    });
    $('.form-awaiting-approval').submit(function(){
        $('.submit-awaiting-approval').prop('disabled', true);
    });*/

    $('#ship_order').submit(function() {
        $('#submit-ship-order').prop('disabled', true);
    });
    $('.btn-delete-order').click(function() {
        var $this = $(this);
        var order_id = $this.attr('data-order-id');
    })

    // $(document).delegate('#ship_to_dubai_modal form').submit(function() {
    //     $('#submit_ship_to_dubai').prop('disabled', true);
    // });
    $('#btn-delete-orders').click(function() {
        var checked = $('#form-delete-orders input[name="delete_orders[]"]:checked').length;
        if (checked > 0) {
            $('#form-delete-orders').submit();
        } else {
            alert('Select order(s)!');
            return false;
        }
    })

    $('[name="pick_barcode_scan"]').change(function() {
        $this = $(this);
        var barcode = $this.val();
        if (barcode == 'PICK_ORDER') {
            $('#form_update_quantity #submit').trigger('click');
        } else {
            var barcodes = $('[data-barcode="' + barcode + '"]:unchecked');
            var node = barcodes[0];
            $(node).prop('checked', true);
            $this.val('');
            $('.product-pack-bacode-checkbox').trigger('change');
        }
    });
    
    $('[name="return_barcode_scan"]').change(function() {
        $this = $(this);
        var barcode = $this.val();
        if (barcode == 'RETURN_ORDER') {
            $('#form_update_quantity #submit').trigger('click');
        } else {
            var barcodes = $('[data-barcode="' + barcode + '"]:unchecked');
            var node = barcodes[0];
            $(node).prop('checked', true);
            $this.val('');
            $('.product-return-bacode-checkbox').trigger('change');
        }
    });
    $('.product_list_row .product_row img').click(function() {
        $('.imgpath').attr('src', $(this).attr('src'));
        $('.img_model').click();
    })

    $('.submit-comment').click(function() {
        console.log("Yessssssss");
        var $this = $(this).parents('.approval-comment');
        var $this_btn = $(this);
        var $this_btn_text = $(this).text();
        $($this).find('.error-message').text('');
        var order_id = $(this).parents('.approval-comment').find('[name="instruction"]').attr('data-order-id');
        var type = $(this).parents('.approval-comment').find('[name="instruction"]').attr('comment-from');
        var comment = $(this).parents('.approval-comment').find('[name="instruction"]').val();
        if (comment == '') {
            $($this).find('.error-message').text('Enter comments');
        } else {
            var url= $(this).attr('data-action');
            $.ajax({
                url: url,
                method: "POST",
                dataType: 'json',
                data: {
                    order_id: order_id,
                    type: type,
                    comment: comment,
                },
                headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
                beforeSend: function() {
                    $this_btn.html('<i class="fa fa-spin fa-spinner"></i>');
                    $this_btn.prop('disabled', true);
                },
                complete: function() {
                    $this_btn.text($this_btn_text);
                    $this_btn.prop('disabled', false);
                },
            }).done(function(json) {
                if (json.success) {
                    location.reload();
                }
            });
        }
    });
    
    var html = '<a class="btn btn-primary hidden img_model" data-toggle="modal" href="#img-model">image</a>\
        <div class="modal fade" id="img-model">\
            <div class="modal-dialog">\
                <div class="modal-content">\
                    <div class="modal-body">\
                        <img src="" class="img-responsive imgpath" style="width: 100%;">\
                    </div>\
                </div>\
            </div>\
        </div>';
    $("body").append(html);


    $('#subimt-place-order').click(function() {
        var $this = $('#add_product_to_order');
        var checkbox = $this.find('input[name="place_order[]"]:checked');
        if (!checkbox.length) {
            alert('Please select product you want to place order!');
            return false;
        } else {
            form = document.getElementById('add_product_to_order');
            form.target = '_blank';
            form.submit();
        }
    });

    $('.btn-cancel-confirmed-order').on('click', function() {
        var order_id = $(this).data('order-id');
        var supplier_id = $(this).data('supplier-id');
        $('#model-cancel-order').find('input[name="order_id"]').val(order_id);
        $('#model-cancel-order').find('input[name="supplier"]').val(supplier_id);
        $('#model-cancel-order').modal('show');
    });
    $('button[name="update_request"]').on('click', function(event) {
        var $this = $(this);
        event.preventDefault();
        if (!confirm('Are you sure?')) return false;
        $.ajax({
            url: $this.attr('data-action'),
            type: 'POST',
            dataType: 'json',
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            data: {
                order_id: $this.attr('data-order-id'),
                action: $this.val(),
                click_action: $this.attr('data-click-action'),
            },
            beforeSend: function() {
                $this.prop('disabled', true);
            },
            success: function(json) {
                if (json.redirect) {
                    location.reload();
                }
            },
            complete: function() {
                $this.prop('disabled', false);
            }
        });
        return false;
    });

    $('.btn-cancel-awaiting-order').on('click', function() {
        var order_id = $(this).data('order-id');
        var supplier_id = $(this).data('supplier-id');
        $('#model-cancel-order').find('input[name="order_id"]').val(order_id);
        $('#model-cancel-order').find('input[name="supplier"]').val(supplier_id);
        $('#model-cancel-order').modal('show');
    });

 
});