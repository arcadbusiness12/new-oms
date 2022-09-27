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
                            Confirmed orders
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                           <div id="status_changed_msg" style="display: none"></div>
                           
                           <?php if($orders['data']) { ?>
                            <?php foreach ($orders['data'] as $order) { ?>
                            <div class="card order_list mb-4">
                                <?php if($order['order_status_id'] == $statuses['to_be_shipped']) { ?>
                                <div class="row top_row <?= (!$order['total']) ? 'hidden' : '' ?>">
                                    <div class="col-xs-4 col-sm-4 text-black mb-4 mt-2"><b>Order Number: #<?php echo $order['order_id'] ?></b>
                                        <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF') && $order['supplier']) { ?>
                                        <br>
                                        <div class="badge badge-secondary font-weight-bold"><?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 text-center">
                                        <?php if($order['order_status_id'] == $statuses['to_be_shipped']) { ?>
                                            <div class="badge badge-warning orange darken-1" style="font-size: 14px;"><b>To Be Shipped</b></div>
                                        <?php } else if($order['order_status_id'] == $statuses['shipped']){ ?>
                                            <div class="badge badge-warning orange darken-1" style="font-size: 14px;"><b>Shipped</b></div>
                                        <?php } else if($order['order_status_id'] == $statuses['cancelled']){ ?>
                                            <!-- <div class="label label-danger">Cancelled</div> -->
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 text-right">
                                        <?php if($order['urgent']) { ?>
                                            <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><b>Urgent</b></div>
                                        <?php } ?>
                                        <div>
                                            <div class="badge badge-secondary font-weight-bold"><b><?php echo date('Y-m-d', strtotime($order['created_at'])) ?></b></div>
                                        </div>
                                    </div>
                                </div>
                                <?php if($order['total']){ 
                                    foreach ($order['order_products'] as $product) { ?>
                                <div class="product_list_row">
                                    <div class="row product_row">
                                        <div class="col-xs-4 col-sm-2">
                                            <img width="100" src="<?php echo $product['image'] ?>" />
                                        </div>
                                        <div class="col-xs-8 col-sm-10">
                                            <strong><?php echo $product['name'] ?></strong><br>
                                            <i><?php echo $product['model'] ?></i>
                                            <div class="options-label">
                                                <?php foreach ($product['quantities'] as $quantity) {
                                                     if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            } 
                                                    ?>
                                                    <?php
                                                    // if()
                                                     foreach ($quantity['options'] as $key => $option) { 
                                                         $sqty = $quantity['order_quantity'];
                                                        if($key !== 'static' && $sqty > 0 ) { ?>
                                                        <div class="box-label text-black">
                                                            <?php echo $option['name'] ?> - <?php echo $option['value'] ?> = <?php echo $sqty; ?>
                                                        </div>
                                                    <?php 
                                                    }
                                                 } ?>
                                                <?php } ?>
                                                <div class="box-label text-black">
                                                    T. Units = <?php echo $product['unit'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } } ?>
                                <div class="row action_row <?= (!$order['total']) ? 'hidden' : '' ?> mb-2">
                                    <div class="col-xs-12 col-sm-2">
                                    <?php if(session('role') == 'SUPPLIER') { ?>
                                    <?php if($order['order_status_id'] == $statuses['to_be_shipped']) { ?>
                                        <?php if($order['stock_cancel']) { ?>
                                            <div class="badge badge-danger font-weight-bold ml-3 active" style="font-size: 14px;"> Cancel Request Sent</div>
                                        <?php } else { $main_order_id = $order['order_id']; ?>
                                          <form action="<?php echo URL::to('/PurchaseManagement/tobe/ship/order/stock/cancel/request') ?>" method="post">  
                                            
                                                {{ csrf_field() }}
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                                <button type="submit" name="submit" value="cancel" class="btn btn-danger form-control submit-cancel-order active ml-3">Stock Cancel</button>
                                            </form>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php } ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-7 text-center">
                                        <button type="button" class="btn btn-default collapse-comment active text-black" data-target="summary<?php echo $order['order_id'] ?>"><b><?php echo number_format($order['total'],2); ?></b></button>
                                    </div>
                                    <div class="col-xs-12 col-sm-3 text-right">
                                        <?php if($order['order_status_id'] == $statuses['to_be_shipped']){ ?>
                                        <?php if(session('role') == 'ADMIN' || session('role') == 'STAFF'){ ?>
                                        <a href="<?php echo route('view.confirmed' , $order['order_id']) ?>"><button type="button" class="btn btn-info form-control active mr-3" style="width: 52%;">View</button></a>
                                        <?php } else { ?>
                                        <a href="<?php echo route('ship.order', $order['order_id']) ?>"><button type="button" class="btn btn-success form-control mr-3" style="width: 52%;">Ship</button></a>
                                        <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div id="summary<?php echo $order['order_id'] ?>" class="summary-panel collapse">
                                    <div class="row">
                                        <div class="col-xs-12 ml-5">
                                            <table class="table">
                                                <tr>
                                                    <th>Option</th>
                                                    <th>Remain Quantity</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                </tr>
                                                <?php foreach ($order['order_products'] as $product) { ?>
                                                <?php foreach ($product['quantities'] as $quantity) { 
                                                     if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            } 
                                                    ?>
                                                        <tr>
                                                        <?php if($quantity['options']) { ?>
                                                        <?php foreach ($quantity['options'] as $key => $option) { 
                                                            if($key !== 'static' && $quantity['order_quantity'] > 0) { ?>
                                                            <td><?php echo $option['name'] . ' - ' . $option['value']; ?></td>
                                                        <?php } } ?>
                                                        <?php }else{ ?>
                                                            <td>-</td>
                                                        <?php } ?>
                                                            <?php if($quantity['order_quantity'] > 0) { ?>
                                                            <td class="testing"><?php echo $quantity['order_quantity'] ?></td>
                                                            <td><?php echo number_format($quantity['price'],2); ?> </td>
                                                            <td><?php echo number_format($quantity['total'],2); ?></td>
                                                            <?php } ?>
                                                        </tr>
                                                <?php } ?>
                                                <?php } ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if($order['shipped_orders']) { ?>
                                    <?php foreach ($order['shipped_orders'] as $shipped_order) { 
                                        if($shipped_order['order_products']) { ?>
                                    <div class="card order_list mb-4">
                                        <div class="row top_row">
                                            <div class="col-xs-4 col-sm-4 text-black mb-4"><b>Order Number: #<?php echo $shipped_order['shipped_id'] ?></b><br>
                                                <?php if(session('role') == 'ADMIN' || session('role') == 'STAFF') { ?>
                                                <div class="badge badge-secondary font-weight-bold"><b>Shipped From: <?php echo ucfirst($shipped_order['shipped']) ?></b></div>
                                                <?php } else { ?>
                                                <div class="badge badge-secondary font-weight-bold"><b>Shipped To: <?php echo ucfirst($shipped_order['shipped']) ?></b></div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-xs-4 col-sm-4 text-center">
                                            <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF') && $order['supplier']) { ?>
                                                <div class="badge badge-secondary font-weight-bold"><b><?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?></b></div>
                                            <?php } ?>
                                            </div>
                                            <?php if(session('role') != 'ADMIN' && session('role') != 'STAFF') { ?>
                                            <div class="col-xs-4 col-sm-4 text-right">
                                                <button type="button" class="btn btn-success ship_to_dubai" data-order-id="<?php echo $shipped_order['order_id'] ?>" data-shipped-id="<?php echo $shipped_order['shipped_id'] ?>"><b>Ship To Dubai</b></button>
                                            </div>
                                            <?php } ?>
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
                                                    <button type="button" class="btn btn-default form-control btn-collapse active collapse-product-option" data-target="product-option<?php echo $shipped_order['shipped_id'] . $product['product_id'] ?>">Details</button>
                                                </div>
                                            </div>
                                            <div id="product-option<?php echo $shipped_order['shipped_id'] . $product['product_id'] ?>" class="options_row table-responsive collapse">
                                                <table class="table">
                                                <?php $i = 0; 
                                                foreach ($product['order_product_quantities'] as $quantity) { 
                                                     if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            } 
                                                    $i++; ?>
                                                    <tr class="single_option_row">
                                                        <?php foreach ($quantity['product_options'] as $option) { ?>
                                                        <td class="col-xs-2">
                                                            <?php if($i == 1) { ?>
                                                            <label class="control-label"><strong> <?php echo $option['name'] ?></strong></label>
                                                            <?php } ?>
                                                            <div><input type="text" class="form-control" value="<?php echo $option['value'] ?>" readonly></div>
                                                        </td>
                                                        <?php } ?>
                                                        <td class="col-xs-2">
                                                            <?php if($i == 1) { ?>
                                                            <label class="control-label"><strong> Shippengd Quantity </strong></label>
                                                            <?php } ?>
                                                            <div><input type="text" class="form-control" value="<?php echo $quantity['quantity'] ?>" readonly></div>
                                                        </td>
                                                        <td class="col-xs-2">
                                                            <?php if($i == 1) { ?>
                                                            <label class="control-label"><strong> Price </strong></label>
                                                            <?php } ?>
                                                            <div><input type="text" class="form-control price" value="<?php echo number_format($quantity['price'],2) ?>" readonly/></div>
                                                        </td>
                                                        <td class="col-xs-2">
                                                            <?php if($i == 1) { ?>
                                                            <label class="control-label"><strong> Sum </strong></label>
                                                            <?php } ?>
                                                            <div><input type="text" class="form-control sum" value="<?php echo number_format($quantity['total'],2) ?>" readonly/></div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </table>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="row instruction_row">
                                            <div class="col-xs-12 col-sm-8"></div>
                                            <div class="col-xs-12 col-sm-4 total_column">
                                                <div class="row">
                                                    <div class="col-xs-7 col-sm-6 text-black">
                                                        <?php foreach ($shipped_order['order_totals'] as $key => $value) { ?>
                                                        <label class="font-weight-bold"><?php echo $value['code'] ?> </label>
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
                                <?php } } ?>
                            </div>
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
<div class="modal fade" id="ship_to_dubai_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo route('ship.to.dubai') ?>" method="post">
            {{ csrf_field() }}
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold">Ship To Dubai</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="order_id"/>
                <input type="hidden" name="shipped_id"/>
                <div class="row text-black">
                    <div class="col-xs-12">
                        <label class="control-label">Shipping Name</label>
                        <div>
                            <input type="text" name="shipping_name" class="form-control" required/>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label class="control-label">Tracking Number</label>
                        <div>
                            <input type="text" name="tracking_number" class="form-control" required/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="submit" value="update_ship_to_dubai" />
                <button type="submit" id="submit_ship_to_dubai" class="btn btn-success">Ship</button>
            </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript" src="{{URL::asset('assets/js/purchase_management.js') }}"></script>
@endpush


