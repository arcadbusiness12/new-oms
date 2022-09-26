@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    @include('PurchaseManagement.orderTabs')
                    
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="body-card">
                        <div class="message">
                            <?php if(Session::has('message')) { ?>
                            <div class="alert <?php echo Session::get('alert-class', 'alert-success') ?> alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <?php echo Session::get('message') ?>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="error_message">
                            <?php if(Session::has('error_message')) { ?>
                            <div class="alert <?php echo Session::get('alert-class', 'alert-danger') ?> alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <?php echo Session::get('error_message') ?>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="panel-heading">
                            Shipped orders
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                           <div id="status_changed_msg" style="display: none"></div>
                           
                           <?php if($orders['data']) { ?>
                            <?php foreach ($orders['data'] as $order) { ?>
                                    <?php if($order['shipped_orders']) { ?>
                                        <?php foreach ($order['shipped_orders'] as $shipped_order) { ?>
                                        <div class="card order_list mb-4" style="margin: 0">
                                            <div class="row top_row ">
                                                <div class="col-xs-4 col-sm-4 text-black mb-2 mt-2"><b>Order Number: #<?php echo $shipped_order['shipped_id'] ?></b></div>
                                                    <div class="col-xs-4 col-sm-4 mt-2 text-center">
                                                    <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF') && $order['order_supplier']) { ?>
                                                    <div class="badge badge-secondary"><?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?></div>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 text-right mt-2 from-to-text">
                                                    <?php if(session('role') == 'ADMIN' || session('role') == 'STAFF') { ?>
                                                    <div class="badge badge-secondary">Shipped From: <?php echo ucfirst($shipped_order['shipped']) ?></div>
                                                    <?php } else { ?>
                                                    <div class="badge badge-secondary">Shipped To: <?php echo ucfirst($shipped_order['shipped']) ?></div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="row top_row ">
                                                <div class="col-sm-8 offset-sm-2 text-center col-grid purchase-order-progress">
                                                    @include('PurchaseManagement.orderProgressBar')
                                                    
                                                </div>
                                            </div>
                                            <?php foreach ($shipped_order['order_products'] as $product) { ?>
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
                                                        <button type="button" class="btn btn-default form-control btn-collapse active text-black collapse-product-option" data-target="product-option<?php echo $shipped_order['shipped_id'] . $product['product_id'] ?>">Details</button>
                                                    </div>
                                                </div>
                                                <div id="product-option<?php echo $shipped_order['shipped_id'] . $product['product_id'] ?>" class="options_row table-responsive collapse">
                                                    <table class="table">
                                                    <?php $i = 0; foreach ($product['order_product_quantities'] as $quantity) { 
                                                        if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            } 
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
                                                                <label class="control-label">Order Quantity</label>
                                                                <?php } ?>
                                                                <div><input type="text" class="form-control" value="<?php echo $quantity['quantity'] ?>" readonly></div>
                                                            </td>
                                                            <td class="col-xs-2">
                                                                <?php if($i == 1) { ?>
                                                                <label class="control-label">Received Quantity</label>
                                                                <?php } ?>
                                                                <div><input type="text" class="form-control" value="<?php echo $quantity['received_quantity'] ?>" readonly></div>
                                                            </td>
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
                                                        </tr>
                                                    <?php } ?>
                                                    </table>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <div class="row instruction_row m-2">
                                                <div class="col-xs-12 col-sm-8">
                                                    <?php if($shipped_order['shipping']) { ?>
                                                    <div class="row">
                                                    <?php foreach ($shipped_order['shipping'] as $key => $value) { ?>
                                                        <div class="col-xs-6 col-sm-6 text-black table-responsive">
                                                            <label class="font-weight-bold"><?php echo ucfirst($key) ?></label>
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <th>Shipping</th>
                                                                    <td><?php echo $value['name']?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Tracking</th>
                                                                    <td><?php echo $value['tracking']?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <td><?php echo $value['date']?></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    <?php } ?>
                                                    <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-4 total_column">
                                                    <div class="row">
                                                        <div class="col-xs-7 col-sm-6 text-black">
                                                            <?php foreach ($shipped_order['order_totals'] as $key => $value) { ?>
                                                            <label class="font-weight-bold"><?php echo $value['code'] ?></label>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="col-xs-5 col-sm-6">
                                                            <?php foreach ($shipped_order['order_totals'] as $key => $value) { ?>
                                                            <input type="text" value="<?php echo number_format($value['value'],2) ?>" class="form-control" readonly/>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php } else { ?>
                                <div class="alert alert-info">No Orders Found!</div>
                                <?php } ?>

                            <div class="row pull-right">
                               <div class="col-xs-12">
                                 <?php echo $pagination ?>
                               </div>
                            </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript" src="{{URL::asset('assets/js/purchase_management.js') }}"></script>
@endpush


