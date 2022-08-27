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
                            <form name="form_stock_level" id="form_stock_level" action="{{route('stock.report')}}" method="get">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="control-label">Product SKU</label>
                                        <input type="text" name="product_sku" id="product_sku" list="product_skus" class="form-control" value="<?php if(isset($old_input['product_sku'])) { echo $old_input['product_sku']; } ?>" autocomplete="off" placeholder="Product SKU">
                                        <datalist id="product_skus"></datalist>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="status">Product Model</label>
                                        <input type="text" name="product_model" id="product_model" list="product_models" class="form-control" autocomplete="off" value="<?php if(isset($old_input['product_model'])) {echo $old_input['product_model']; } ?>" placeholder="Product Model">
                                            <datalist id="product_models"></datalist>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-6">
                                        <label class="control-label">From Date</label>
                                        <input type="text" name="from_date" id="date_added" class="date-time-picker form-control" autocomplete="off" placeholder="From Date"  data-options='{
                                            "timepicker":false,
                                            "format":"Y-m-d"
                                            }' value="<?php if(isset($old_input['from_date'])) { echo $old_input['from_date']; } ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">To Date</label>
                                        <input type="text" name="to_date" id="date_modified" class="date-time-picker form-control" data-options='{
                                            "timepicker":false,
                                            "format":"Y-m-d"
                                            }' autocomplete="off" placeholder="To Date" value="<?php if(isset($old_input['to_date'])) { echo $old_input['to_date']; } ?>">
                                    </div>
                                    <div class="col-sm-12 text-right">
                                        <br>
                                        <button type="submit" id="search_filter" class="btn btn-primary">Search</button>
                                        <!-- <button type="button" onclick="$('#add_product_to_order').attr('action', '<?php echo URL::to('/inventory_manage/reportExport'); ?>').submit();" class="btn btn-danger">Export</button> -->
                                        <!-- <button type="button" id="subimt-place-order" class="btn btn-info">Order</button> -->
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
                            Stock Reports
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
    </script>
@endpush

