@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3" id="accounts">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="col-xs-10 block-header">
                        <ul class="nav nav-tabs" role="tablist">
                            <?php $i = 1; foreach ($tabs as $key => $value) { ?>
                            <li role="presentation" class="<?php if($i == 1) { ?>active<?php } ?> top-tabs tab-{{$key}}" data-tab="{{$key}}" data-tamount="{{$orders[$key]['total_amount']}}">
                                <div class="badge badge-secondary"><?php echo ($orders[$key]['total_count'] > 0) ? $orders[$key]['total_count'] : 0; ?></div>
                                <a href="#<?php echo $key ?>" aria-controls="<?php echo $key ?>" role="tab" data-toggle="tab"><?php echo $key ?></a>
                            </li>
                            <div class="total_amount_label total_amount-{{$key}} text-black">
                                <label><b>Total Amount : <?php echo (float)$orders[$key]['total_amount'] ?> AED</b></label>
                            </div>
                            <?php $i++; } ?>
                        </ul>
                    </div>
                    
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="card no-b">
                                <div class="card-header white">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <form name="filter_orders" id="filter_orders" method="get" action="<?php echo route('accounts') ?>">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <div class="form-line">
                                                                <input type="text" name="date_from" id="date_from" class="date-time-picker form-control" autocomplete="off" data-options='{
                                                                    "timepicker":false,
                                                                    "format":"Y-m-d"
                                                                    }' placeholder="Date From" value="{{isset($old_input['date_from'])?$old_input['date_from']:''}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <div class="form-line">
                                                                
                                                                <input type="text" name="date_to" id="date_to" class="date-time-picker form-control" data-options='{
                                                                    "timepicker":false,
                                                                    "format":"Y-m-d"
                                                                    }' autocomplete="off"  placeholder="Date To" value="{{isset($old_input['date_to'])?$old_input['date_to']:''}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <div class="form-line">
                                                                <select name="supplier" class="form-control custom-select">
                                                                    <option value=""></option>
                                                                    <?php foreach ($suppliers as $supplier) { ?>
                                                                    <option value="<?php echo $supplier['user_id'] ?>" <?php if(isset($old_input['supplier']) && $old_input['supplier'] == $supplier['user_id']) { ?> selected="selected" <?php } ?> ><?php echo $supplier['firstname'] ?> <?php echo $supplier['lastname'] ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <button type="submit" id="search_filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> Filter</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div> 
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="body-card">
                        <div class="panel-heading">
                            Accounts
                          </div>
                          
                           <div id="status_changed_msg" style="display: none"></div>
                           <div class="tab-content">
                           <?php if($orders) { 
                            //    echo "<pre>"; print_r($orders); die;
                               ?>
                            <?php $i = 1; foreach ($orders as $tab => $order_value) { 
                                ?>
                            <div role="tabpanel" class="tab-pane <?php if($i == 1) { ?>active<?php } ?>" id="<?php echo $tab ?>">
                                <?php if(isset($order_value) && count($order_value) > 0) { 

                                    
                                    $order_value = $order_value->toArray();
                                    ?>
                                <?php foreach ($order_value['data'] as $order) {
                                    if(!is_array($order)) continue;
                                    // $orId =  $order['order_id'];
                            //    echo "<pre>"; print_r($order); die;
                                    ?>
                                <div class="card order_list mb-4">
                                    <div class="row top_row">
                                        <div class="col-xs-8 col-sm-6 text-black"><b>Order Number: # <?php echo $order['order_id']; ?></b></div>
                                        <div class="col-xs-4 col-sm-6 text-right">
                                            <?php if($order['urgent']) { ?>
                                                <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><b>Urgent</b></div>
                                            <?php } ?>
                                            <?php if($order['ship_by_sea'] == 1) { ?>
                                                <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><b>Ship By Sea</b></div>
                                            <?php } else { ?>
                                                <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><b>Ship By Air</b></div>
                                            <?php  } ?>
                                        </div>
                                    </div>
                                    <?php foreach ($order['order_products'] as $product) { ?>
                                    <div class="product_list_row">
                                        <div class="row product_row">
                                            <div class="col-xs-4 col-sm-2">
                                                <img width="100" src="<?php echo $product['image'] ?>" />
                                            </div>
                                            <div class="col-xs-6 col-sm-8">
                                                <strong><?php echo $product['name'] ?></strong><br>
                                                <i><?php echo $product['model'] ?></i>
                                            </div>
                                            <div class="col-xs-2 col-sm-2">
                                                <button type="button" class="btn btn-default form-control btn-collapse collapse-product-option" data-target="product-option<?php echo $tab . $order['order_id'] . $product['product_id'] ?>"><?php echo number_format($product['product_total'],2) ?></button>
                                            </div>
                                        </div>
                                        <div id="product-option<?php echo $tab . $order['order_id'] . $product['product_id'] ?>" class="options_row table-responsive collapsible-content">
                                            <table class="table">
                                            <?php $i = 0; foreach ($product['order_product_base_quantities'] as $quantity) { 
                                                // if($product['product_id'] != $quantity['order_product_id']) {
                                                //                                 continue;
                                                //                             }
                                                $i++; ?>
                                                <tr class="single_option_row">
                                                    <?php foreach ($quantity['product_options'] as $option) { ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label"><?php echo $option['name'] ?></label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control" value="<?php echo $option['value'] ?>" readonly></div>
                                                    </td>
                                                    <?php } ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Quantity</label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control" value="<?php echo $quantity['quantity'] ?>" readonly></div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Order Quantity</label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control order_quantity" value="<?php echo $quantity['order_quantity'] ?>" readonly></div>
                                                    </td>
                                                    <?php if($quantity['order_quantity'] > 0) { ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Price</label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control price" value="<?php echo number_format($quantity['price'],2) ?>" readonly/></div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Sum</label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control sum" value="<?php echo number_format($quantity['total'],2) ?>" readonly/></div>
                                                    </td>
                                                    <?php } else { ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">&nbsp;</label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control not-available" value="Not Available" disabled /></div>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                            </table>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="row instruction_row">
                                        <div class="col-xs-12 col-sm-8">
                                            <div>
                                                <input type="text" name="supplier_link" value="<?php echo $order['link'] ?>" class="form-control copy_to_clipboard" placeholder="Supplier Link" readonly/>
                                            </div>
                                            <div class="row button-row">
                                                <div class="col-xs-12 col-sm-4">
                                                    <button type="button" class="btn btn-default form-control active collapse-comment" data-target="history<?php echo $tab.$order['order_id'] ?>">Comment</button>
                                                </div>
                                                <div class="col-xs-8"></div>
                                            </div>
                                            <div id="history<?php echo $tab.$order['order_id'] ?>" class="history-panel">
                                            <?php foreach ($order['order_histories'] as $history) { ?>
                                                <div class="text-black">
                                                    <label><strong><?php echo $history['name'] ?>: </strong></label>
                                                    <i><?php echo $history['comment'] ?></i>
                                                </div>
                                            <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 total_column">
                                            <div class="row">
                                                <div class="col-xs-7 col-sm-7 text-black">
                                                    <?php foreach ($order['order_totals'] as $key => $value) { ?>
                                                        <label><b><?php echo $value['code'] ?></b></label>
                                                        <?php } ?>
                                                </div>
                                                <div class="col-xs-5 col-sm-5">
                                                    <?php foreach ($order['order_totals'] as $key => $value) { ?>
                                                        <input type="text" value="<?php echo number_format($value['value'],2) ?>" class="form-control" readonly/>
                                                        <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="pagination-area text-right">
                                    <?php echo $order_value['data']['pagination']; ?>
                                </div>
                                <?php } else { ?>
                                <div class="alert alert-info">No Orders Found!</div>
                                <?php } ?>
                            </div>
                            <?php $i++;} ?>
                            <?php } ?>
                           </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript" src="{{URL::asset('assets/js/purchase_management.js') }}"></script>
<link rel="stylesheet" href="{{URL::asset('assets/css/purchase.css') }}">

<script>
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
    $('.top-tabs').on('click', function() {
        var tab = $(this).data('tab');
        $('.top-tabs').removeClass('active');
        $('.tab-'+tab).addClass('active');
        $('.total_amount_label').css('display', 'none');
        $('.total_amount-'+tab).css('display', 'block');
    })
</script>
@endpush

