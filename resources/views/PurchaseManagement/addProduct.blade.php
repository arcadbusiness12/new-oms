@extends('layouts.app')

@section('content')
@push('style')
    <style>
        .heading-box {
            font-size: 14px !important;
        }
        .fa-2x {font-size: 2em;}
        .td-head {vertical-align: top !important;}
        .product_list {padding: 12px;}
        .supplier-box {
            float: left;
        }
    </style>
@endpush
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">

                            <form name="filter_products" id="filter_products" method="get" action="">
                                {{csrf_field()}}
                                <div id="alert-response"></div>
                                <div>
                                    <label class="col-sm-2 control-label text-center col-grid" for="input-product" style="margin: 0;padding-top: 7px;">Choose Product</label>
                                   
                                    <div class="col-sm-2 col-grid">
                                        <input type="text" name="product_sku" id="product_sku" list="product_skus" class="form-control" autocomplete="off" value="" placeholder="Product SKU">
                                        <datalist id="product_skus"></datalist>
                                    </div>  
                                    <div class="col-sm-2 col-grid">
                                        <button type="submit" id="search_filter" class="btn btn-primary btn-block">
                                            <i class="icon icon-filter"></i>
                                            Search
                                        </button>
                                    </div>
                                    <div class="col-sm-2 col-grid">
                                        <button type="button" id="add_manually" class="btn btn-primary btn-block" data-action="">
                                            Add Manually
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Order Products
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                          <form action="<?php echo route('add.purchase.order') ?>" method="post" id="form-add-order" enctype="multipart/form-data">
                            {{csrf_field()}}
                                <?php if(is_array($products) && !empty($products)) {?>
                                <div class="card product_list">
                                    <?php foreach ($products as $product) { 
                                        $quantity = $average_quantity = $duration = $min = 0;
                                        ?>
                                    
                                        <div class="product_list_row" style="position: relative;">
                                        <div class="row product_row">
                                            <div class="col-xs-4 col-sm-2">
                                                <img width="100" src="<?php echo $product['image'] ?>" />
                                            </div>
                                            <div class="col-xs-8 col-sm-8">
                                                <i><?php echo $product['sku'] ?></i>
                                            </div>
                                        </div>
                                        <div class="table-responsive option_list_row">
                                            <table class="table">
                                                <?php
                                                if(isset($product['options']) && is_array($product['options'])) {
                                                $colspan = 2; 
                                                $option_name = array();
                                                $static_option_array = array();
                                                foreach ($product['options'] as $option) { 
                                                    
                                                    if($option['type'] == 'select' || $option['type'] == 'radio') {
                                                        if($option['static_option_id'] != $option['option_id']) {
                                                            foreach($option['option_values'] as $okey => $values) {
                                                                $values['static_option_id'] = $option['static_option_id'];
                                                                $option_name[$option['name']][] = $values;    
                                                            }
                                                            //ksort($option_name[$option['name']]);
                                                            //$option_name[$option['name']] = array_values($option_name[$option['name']]);
                                                            //array_multisort($option_name[$option['name']], SORT_ASC, $option['option_values']);
                                                            $colspan++; 
                                                        }else if($option['static_option_id'] == $option['option_id'] && count($product['options']) == 1){
                                                            foreach($option['option_values'] as $okey => $values) {
                                                                $option_name[$option['name']][] = $values;    
                                                            }
                                                            $colspan++; 
                                                        }else{
                                                            $static_option_array = $option;
                                                            $colspan++; 
                                                        }
                                                    }
            
                                                }
            
                                                foreach ($option_name as $key => $options) {
                                                    $current_i = 0;
                                                    foreach ($options as $option) { $unique = uniqid(); ?>
                                                    <tr class="options_row">
                                                        <?php if($static_option_array && $option['option_id'] != $option['static_option_id']) { ?>
                                                        <td class="col-xs-2 td-head">
                                                            <?php if($current_i == 0) { ?>
                                                            <label class="control-label heading-box">
                                                                <strong>
                                                                <?php echo $static_option_array['name'] ?></strong></label>
                                                            <?php } ?>
                                                            <select name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][option][<?php echo $static_option_array['option_id'] ?>]" class="form-control product_option_dropdown" data-product-id="<?php echo $product['product_id'] ?>" data-option-id="<?php echo $static_option_array['option_id'] ?>">
                                                                <?php foreach($static_option_array['option_values'] as $values) {?>
                                                                    <option value="<?php echo $values['option_value_id'] ?>"><?php echo $values['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <?php } ?>
                                                        <td class="col-xs-2 td-head">
                                                            <?php if($current_i == 0) { ?>
                                                            <label class="control-label heading-box"><strong><?php echo $key ?></strong></label>
                                                            <?php } ?>
                                                            <select name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][option][<?php echo $option['option_id'] ?>]" class="form-control product_option_dropdown" data-product-id="<?php echo $product['product_id'] ?>" data-option-id="<?php echo $option['option_id'] ?>">
                                                                <?php foreach($option_name[$key] as $values) {?>
                                                                    <option value="<?php echo $values['option_value_id'] ?>" <?php if($option['name'] == $values['name']) { ?> selected="selected" <?php } ?> ><?php echo $values['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td class="col-xs-2 td-head">
                                                            <?php if($current_i == 0) { ?>
                                                            <label class="control-label heading-box"><strong>In Stock</strong></label>
                                                            <?php } ?>
                                                            <div class="quantity"><b class="fa-2x"><?php echo $option['quantity'] ?></b> Available</div>
                                                        </td>
                                                        <td class="col-xs-2 td-head">
                                                            <?php if($current_i == 0) { ?>
                                                            <label class="control-label heading-box"><strong>Last Period Sale</strong></label>
                                                            <?php } ?>
                                                            <div class="average"><?php echo (int)$option['average_quantity'] .' pcs / '. (int)$option['duration'] .' Days'; ?></div>
                                                        </td>
                                                        <td class="col-xs-2 td-head">
                                                            <?php if($current_i == 0) { ?>
                                                            <label class="control-label heading-box"><strong>Quantity</strong></label>
                                                            <?php } ?>
                                                            <div>
                                                                <input type="text" pattern="^[1-9][0-9]*$" title="Enter greater than 0" name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][quantity]" class="form-control input_quantity" required/>
                                                                <!-- <input type="number" name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][quantity]" min="<?php echo max(1, $min) ?>" class="form-control input_quantity" required/> -->
                                                            </div>
                                                        </td>
                                                        <td class="col-xs-2 td-head text-right">
                                                            <?php if($current_i == 0) { ?>
                                                            <label class="control-label heading-box"><strong>Remove</strong></label>
                                                            <?php } ?>
                                                            <div>
                                                                <button type="button" class="btn btn-danger" onclick="$(this).parents('tr.options_row').remove();">
                                                                    <i class="icon icon-minus-circle"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php $current_i++; }
                                                } ?>
                                                <?php } else { 
                                                $colspan = 0;
                                                $unique = uniqid();
                                                $quantity = (int)$product['quantity']['quantity']; 
                                                $average_quantity = (int)$product['quantity']['average_quantity']; 
                                                $duration = $product['quantity']['duration']; 
                                                $min = (int)$product['quantity']['minimum_quantity']; ?>
                                                <tr>
                                                    <td class="col-xs-2 td-head">
                                                        <label class="control-label heading-box"><strong>In Stock</strong></label>
                                                        <div class="quantity"><b class="fa-2x"><?php echo $quantity ?></b> Available</div>
                                                    </td>
                                                    <td class="col-xs-2 td-head">
                                                        <label class="control-label heading-box"><strong>Last Period Sale</strong></label>
                                                        <div class="average"><?php echo (int)$average_quantity .' pcs / '. (int)$duration .' Days'; ?></div>
                                                    </td>
                                                    <td class="col-xs-2 td-head">
                                                        <label class="control-label heading-box"><strong>Quantity</strong></label>
                                                        <div>
                                                            <input type="text" pattern="^[1-9][0-9]*$" title="Enter greater than 0" name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][quantity]" class="form-control input_quantity" required/>
                                                            <!-- <input type="number" name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][quantity]" min="<?php echo max(1, $min) ?>" class="form-control input_quantity" required/> -->
                                                        </div>
                                                    </td>
                                                    <td class="col-xs-2"></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <?php for ($i=0; $i < $colspan; $i++) { ?>
                                                    <td class="col-xs-2"></td>
                                                    <?php } ?>
                                                    <div class="loader-box text-center" style="display:none;">
                                                        <div class="preloader-wrapper small active text-center">
                                                            <div class="spinner-layer spinner-green-only">
                                                                <div class="circle-clipper left">
                                                                    <div class="circle"></div>
                                                                </div><div class="gap-patch">
                                                                <div class="circle"></div>
                                                            </div><div class="circle-clipper right">
                                                                <div class="circle"></div>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <td class="col-xs-2" style="text-align: right;">
                                                        <?php if(isset($product['options']) && is_array($product['options']) && count($product['options']) > 1) { ?>
                                                        <div>
                                                            
                                                            <button type="button" name="add_option" class="btn btn-primary btn-sm add_more_option" value="<?php echo $product['product_id'] ?>">Add More</button>
                                                            
                                                        </div>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="col-xs-2"></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div style="position: absolute;top: 0;right: 0;">
                                            <button type="button" class="btn btn-danger btn-delete-order-product">
                                                <i class="icon icon-minus-circle"></i>
                                            </button>
                                        </div>
                                    </div>
            
                                    <?php } ?>
                                    <div class="row instruction_row" style="display: initial!important">
                                        <div class="col-xs-12 col-sm-8 supplier-box">
                                            <textarea name="instruction" class="form-control special_instruction" rows="3" placeholder="Special Instruction" required></textarea>
                                        </div>
                                        <div class="col-xs-7 col-sm-2 supplier-box text-center">
                                            <label class="control-label heading-box"><strong> Supplier </strong></label>
                                            <select name="supplier" class="form-control">
                                                <?php foreach ($suppliers as $supplier) { ?>
                                                    <option value="<?php echo $supplier['user_id'] ?>"><?php echo $supplier['firstname'] .' '. $supplier['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-xs-5 col-sm-2 supplier-box">
                                            <div>
                                              <label for="urgent" class="heading-box">
                                                  <input type="checkbox" name="urgent" style="width: 20px;
                                                height: 20px;" id="urgent"><strong> Urgent </strong></label>
                                            </div>
                                            <div>
                                                <button type="submit" name="submit" value="add_purchase_order" id="submit-add-order" class="btn btn-success">Add Order</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } else { ?>
                                    <div class="card product_list" style="display: none;">
                                        <div class="row instruction_row">
                                            <div class="col-xs-12 col-sm-8 supplier-box">
                                                <textarea name="instruction" class="form-control special_instruction" rows="3" placeholder="Special Instruction" required></textarea>
                                            </div>
                                            <div class="col-xs-7 col-sm-2 supplier-box text-center">
                                                <label class="control-label heading-box"><strong> Supplier </strong></label>
                                                <select name="supplier" class="form-control">
                                                    <?php foreach ($suppliers as $supplier) { ?>
                                                        <option value="<?php echo $supplier['user_id'] ?>"><?php echo $supplier['firstname'] .' '. $supplier['lastname'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-xs-5 col-sm-2 supplier-box">
                                                <div>
                                                  <label for="urgent" class="heading-box"><input type="checkbox" name="urgent" id="urgent" style="width: 20px;
                                                    height: 20px;" class="chk-col-green"><strong> Urgent </strong></label>
                                                </div>
                                                <div>
                                                    <button type="submit" name="submit" value="add_purchase_order" id="submit-add-order" class="btn btn-success">Add Order</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        var xhr = {};
        $(document).delegate('.btn-delete-order-product', 'click', function(){
        var $this = $(this);
        var product_length = $('.product_list_row').length;
        if(product_length > 1){
            $($this).parents('.product_list_row').remove();
        }else{
            $($this).parents('.product_list_row').remove();
            $('.instruction_row').hide();
        }
    });

    $(document).delegate('.add_more_option', 'click', function() {
        _this = $(this);
        _this_parent = $(this).parents('.product_list_row').find('.option_list_row');
        var product_id = $(this).val();
        $.ajax({
            method: "POST",
            url: "{{route('get.purchase.product.order.option')}}",
            data: {
                product_id: $(this).val()
            },
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            beforeSend: function() {
                $('.loader-box').css('display', 'block');
                $(_this).prop('disabled', true);
            },
            complete: function() {
                // $(_this).html('Add More');
                $('.loader-box').css('display', 'none');
                $(_this).prop('disabled', false);
            },
        }).done(function(html) {
            if (html != "") {
                $(_this_parent).find('table').append(html);
            }
        });
    });

    $("#add_manually").click(function() {
        $this = $(this);
        $.ajax({
            method: "GET",
            url: "{{route('add.purchase.product.manually')}}",
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

    $(document).delegate('.select-category', 'change', function() {
        var code = $(this).find(':selected').data("code");
        var row = $(this).data("row");
        console.log(row);
        var url = '{{ route("cheking.for.group.code", ":category") }}';
        url = url.replace(':category', $(this).val());
        $('#option_color').prop('selectedIndex',0);
        $.ajax({
            url: url,
            type: "GET",
            caches: false,
            success: function(respo) {
                console.log('code='+code);
                console.log(respo);
                var nCode = code;
                $('.newCode'+row).val(respo.code);
                $('#sku'+row).val(nCode);
                $('.new-code'+row).val(nCode);
                $('.new-sku'+row).val(respo.newSku);

                var html = '';
                var op = '<option value="">Select Sub-category</option>';
                respo.subCategories.forEach(element => {
                    html += '<option value="'+element.id+'" data-code="'+element.code+'">'+element.name+'</option>';
                });
                var options = op+html;
                $('.subCate-row'+row).html(options);
            }
        });
    });
    $(document).delegate('.sub-category', 'change', function() {
        var code = $(this).find(':selected').data("code");
        var row = $(this).data("row");
        code = code ? code : '';
        var nCode = $('.new-code'+row).val() +''+ code +''+ $('.newCode'+row).val();
        $('#sku'+row).val(nCode);
        $('.manually_option_color'+row).prop('selectedIndex',0);
    });
     $(document).delegate('#manually_option_color', 'change', function() {
        var iCode = $(this).find(':selected').data('id');
        var row = $(this).data("row");
        var cateCode = $('.new-code'+row).val();
            cateCode = cateCode ? cateCode : '';
        var subCatedCode = $('.subCate-row'+row).find(':selected').data('code');
            subCatedCode =  subCatedCode ? subCatedCode : '';
        var nCode = $('.newCode'+row).val();
        var code = cateCode +''+ subCatedCode +''+ nCode +''+ iCode;
        $('#sku'+row).val(code);
    })
    
    $(document).delegate(".add_selected_options", "click", function() {
        $this = $(this);
        $this_text = $(this).text();
        _this_parent = $(this).parents('.product_list_row').find('.all_options_row');
        var data_id = $(this).attr('data-product-id');
        var option_color = $(_this_parent).find('#manually_option_color').val();
        var option_size = $(_this_parent).find('#manually_option_size').val();
        $.ajax({
            method: "POST",
            url: "{{route('get.manually.all.options')}}",
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

    $(document).delegate('.is-to-inventory','click', function() {
        if($("input[type=checkbox]:checked").length > 0) {
            $('.select-category').attr('required', true);
        }else {
            $('.select-category').attr('required', false);
        }
        var v= $(this).val();
        console.log(v);
    });
    $(document).delegate('#product_sku', 'keyup', function() {
        _this = $(this);
        if (typeof xhr['get_product_sku_keyup'] != 'undefined' && xhr['get_product_sku_keyup'].readyState != 4) {
            xhr['get_product_sku_keyup'].abort();
        }
        xhr['get_product_sku_keyup'] = $.ajax({
            method: "POST",
            url: "{{route('get.purchase.product.sku')}}",
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
    
    $('#filter_products').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            method: "POST",
            url: "{{route('add.product')}}",
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
    })
</script>
@endpush

