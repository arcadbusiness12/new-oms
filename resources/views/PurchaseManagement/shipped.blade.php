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
                            <div class="order_list mb-4">
                                <div class="hidden-section">
                                    <div class="row top_row">
                                        <div class="col-xs-4 col-sm-4 text-black mb-4 mt-2"><b>Order Number: #<?php echo $order['order_id'] ?> Test</b></div>
                                        <div class="col-xs-4 col-sm-4 text-center">
                                            <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF') && $order['order_supplier']) { ?>
                                            <div class="badge badge-secondary font-weight-bold"><?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?></div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 text-right">
                                            <?php if($order['urgent']) { ?>
                                                <div class="badge badge-warning orange darken-1"  style="font-size: 15px;"><b>Urgent</b></div>
                                            <?php } ?>
                                            <?php if(isset($order['ship_by_sea']) && $order['ship_by_sea']) { ?>
                                                <div class="badge badge-warning orange darken-1">Ship By Sea</div>
                                            <?php } ?>
                                            <div>
                                                <div class="badge badge-secondary font-weight-bold"><b><?php echo date('Y-m-d', strtotime($order['created_at'])) ?></b></div>
                                            </div>
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
                                                <button type="button" class="btn btn-default form-control active btn-collapse active collapse-product-option" data-target="product-option<?php echo $order['order_id'] . $product['product_id'] ?>">Details</button>
                                            </div>
                                        </div>
                                        <div id="product-option<?php echo $order['order_id'] . $product['product_id'] ?>" class="options_row table-responsive collapse">
                                            <table class="table">
                                            <?php $i = 0; foreach ($product['order_product_quantities'] as $quantity) { $i++; ?>
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
                                                        <div><input type="text" class="form-control" value="<?php echo $quantity['order_quantity'] ?>" readonly></div>
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
                                                    <?php }else{ ?>
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
                                            <div class="supplier_link">
                                                <input type="text" value="<?php echo $order['link'] ?>" class="form-control" placeholder="Supplier Link" readonly/>
                                            </div>
                                            <div class="row button-row mt-2 mb-2">
                                                <div class="col-xs-12 col-sm-4">
                                                    <button type="button" class="btn btn-default active form-control collapse-comment" data-target="history<?php echo $order['order_id'] ?>">Comment</button>
                                                </div>
                                            </div>
                                            <div id="history<?php echo $order['order_id'] ?>" class="history-panel p-2 mb-2">
                                            <?php foreach ($order['order_histories'] as $history) { ?>
                                                <div class="text-black">
                                                    <label style="font-weight: 500;"><strong><?php echo $history['name'] ?>:</strong> </label>
                                                    <i><?php echo $history['comment'] ?></i>
                                                </div>
                                            <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 total_column">
                                            <div class="row">
                                                <div class="col-xs-7 col-sm-6 text-black">
                                                    <?php foreach ($order['order_totals'] as $key => $value) { ?>
                                                    <label class="font-weight-bold"><?php echo $value['code'] ?></label>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-5 col-sm-6">
                                                    <?php foreach ($order['order_totals'] as $key => $value) { ?>
                                                    <input type="text" value="<?php echo number_format($value['value'],2) ?>" class="form-control" readonly/>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if($order['shipped_orders']) { ?>
                                    <?php foreach ($order['shipped_orders'] as $shipped_order) { ?>
                                     <div class="card order_list mb-4 mt-4 ">
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
                                        <?php foreach ($shipped_order['order_products'] as $product) { ?>
                                        <div class="product_list_row">
                                            <div class="row product_row">
                                                <div class="col-xs-4 col-sm-2">
                                                    <img width="100" src="<?php echo $product['image'] ?>" />
                                                </div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <strong><?php echo $product['name'] ?></strong><br>
                                                    <i><?php echo $product['model'] ?></i>
                                                </div>
                                                <div class="col-xs-12 col-sm-2">
                                                    <button type="button" class="btn btn-default form-control btn-collapse collapse-product-option" data-target="product-option<?php echo $shipped_order['shipped_id'] . $product['product_id'] ?>">Details</button>
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
                                                            <label class="control-label">Shipped Quantity</label>
                                                            <?php } ?>
                                                            <div><input type="text" class="form-control" value="<?php echo $quantity['quantity'] ?>" readonly></div>
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
                                        <div class="col-sm-12">
                                        </div>
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
                                        <!--  supplier cancel order option start -->
                                        <div class="row">
                                            <div class="col-sm-2 stock-cancel-request-tag m-4">
                                             @if(session('role') == 'SUPPLIER')
                                               @if($order['stock_cancel'])
                                                    <div class="badge badge-danger font-weight-bold ml-3 active" style="font-size: 14px;">Cancel Request Sent</div>
                                                @else
                                                    <form action="<?php echo URL::to('/PurchaseManagement/tobe/ship/order/stock/cancel/request') ?>" method="post">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                                        <input type="hidden" name="shiped_order_id" value="<?php echo $shipped_order['shipped_id'] ?>" />
                                                        <button type="submit" name="submit" value="cancel" class="btn btn-danger form-control active submit-cancel-order">Stock Cancel <span class="cancel-btn"></span> </button>
                                                    </form>
                                                @endif
                                             @endif
                                            </div>
                                        </div>
                                        <!--  supplier cancel order option end -->
                                    </div>
                                    <?php } ?>
                                <?php } ?>
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
@push('scripts')
<script type="text/javascript" src="{{URL::asset('assets/js/purchase_management.js') }}"></script>
@endpush


