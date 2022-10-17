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
                        <div class="panel-heading">
                            Cancelled orders
                          </div>
                          
                           <div id="status_changed_msg" style="display: none"></div>
                           
                           <?php if($orders['data']) { ?>
                            <?php foreach ($orders['data'] as $order) { ?>
                            <div class="card order_list mb-4">
                                <div class="row top_row">
                                    <div class="col-xs-4 col-sm-4 text-black mb-4 mt-2"><b>Order Number: #<?php echo (isset($order['shipped_id'])) ? $order['shipped_id'] : $order['order_id'] ?></b></div>
                                    <div class="col-xs-4 col-sm-4 text-center">
                                        <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF') && $order['order_supplier']) { ?>
                                        <div class="badge badge-secondary font-weight-bold" >Supplier : <?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 text-right">
                                        <?php if($order['urgent']) { ?>
                                            <div class="badge badge-warning orange darken-1"  style="font-size: 15px;"><b>Urgent</b></div>
                                        <?php } ?>
                                        <?php if(isset($order['ship_by_sea']) && $order['ship_by_sea']) { ?>
                                            <div class="badge badge-warning orange darken-1" style="font-size: 15px;">Ship By Sea</div>
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
                                            <button type="button" class="btn btn-default form-control active btn-collapse text-black collapse-product-option" data-target="product-option<?php echo $order['order_id'] . $product['product_id'] ?>">Details</button>
                                        </div>
                                    </div>
                                    <div id="product-option<?php echo $order['order_id'] . $product['product_id'] ?>" class="options_row table-responsive collapse">
                                        <table class="table">
                                            <?php if(isset($product['options']) && $product['options']) { ?>
                                            <?php $i = 0; foreach ($product['options'] as $option) { $i++; ?>
                                            <tr class="single_option_row">
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Option</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control" value="<?php echo $option['name'] ?> - <?php echo $option['value'] ?>" readonly></div>
                                                </td>
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Quantity</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control" value="<?php echo @$option['quantity'] ?>" readonly></div>
                                                </td>
                                                
                                                @if($option['order_quantity'])
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Order Quantity</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control" value="<?php echo $option['order_quantity'] ?>" readonly></div>
                                                </td>
                                                @endif
                                                @if($option['remain_quantity'])
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Remain Quantity</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control" value="<?php echo @$option['remain_quantity'] ?>" readonly></div>
                                                </td>
                                                @endif
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Price</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control price" value="<?php echo $option['price'] ?>" readonly/></div>
                                                </td>
                                            </tr>
                                           
                                            <?php } ?>
                                            <?php }else{ ?>
                                            <tr class="single_option_row">
                                                <td class="col-xs-2">
                                                    <label class="control-label">Quantity</label>
                                                    <div><input type="text" class="form-control" value="<?php echo @$product['quantity'] ?>" readonly></div>
                                                </td>
                                                <td class="col-xs-2">
                                                    <label class="control-label">Order Quantity</label>
                                                    <div><input type="text" class="form-control" value="<?php echo @$product['order_quantity'] ?>" readonly></div>
                                                </td>
                                                <td class="col-xs-2">
                                                    <label class="control-label">Shipped Quantity</label>
                                                    <div><input type="text" class="form-control order_quantity" value="<?php echo @$product['shipped_quantity'] ?>" readonly></div>
                                                </td>
                                                <td class="col-xs-2">
                                                    <label class="control-label">Received Quantity</label>
                                                    <div><input type="text" class="form-control order_quantity" value="<?php echo @$product['received_quantity'] ?>" readonly></div>
                                                </td>
                                                <td class="col-xs-2">
                                                    <label class="control-label">Price</label>
                                                    <div><input type="text" class="form-control price" value="<?php echo @$product['price'] ?>" readonly/></div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
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


