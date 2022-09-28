@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="card no-b">
                                <div class="card-header white">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <form name="filter_orders" id="filter_orders" method="get" action="<?php echo route('to.be.shipped.stock.cancelled.requests') ?>">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="form-group form-float">
                                                            <div class="form-line">
                                                                <lebel class="form-label" for="order_id">Order ID</lebel>
                                                                <input type="text" name="order_id" id="order_id" class="form-control" autocomplete="off" value="{{ isset($old_input['order_id'])?$old_input['order_id']:'' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-line">
                                                            <lebel class="form-label" for="order_id">Supplier</lebel>
                                                        <select name="supplier" class="form-control">
                                                            <option value=""></option>
                                                            <?php foreach ($suppliers as $supplier) { ?>
                                                                <option value="<?php echo $supplier['user_id'] ?>" <?php if(isset($old_input['supplier']) && $old_input['supplier'] == $supplier['user_id']) { ?> selected="selected" <?php } ?> ><?php echo $supplier['firstname'] .' '. $supplier['lastname'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-3">
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
                            Cancelled orders
                          </div>
                          
                           <div id="status_changed_msg" style="display: none"></div>
                           
                           <?php if($orders['data']) { ?>
                            <?php foreach ($orders['data'] as $order) { ?>
                                <div class="card order_list mb-4">
                                    <div class="row top_row">
                                        <div class="col-xs-4 col-sm-4 text-black mb-4 mt-2"><b>Order Number: #<?php echo $order['order_id'] ?></b></div>
                                        <div class="col-xs-4 col-sm-4 text-center">
                                            <?php if(session('role') == 'ADMIN') { ?>
                                            <?php if($order['order_supplier']) { ?>
                                            <div class="badge badge-secondary font-weight-bold">Supplier : <?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?></div>
                                            <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 text-right">
                                            <?php if($order['purchased_order']['urgent']) { ?>
                                                <div class="badge badge-warning orange darken-1"  style="font-size: 15px;">Urgent</div>
                                            <?php } ?>
                                            <?php if(isset($order['purchased_order']['ship_by_sea']) && $order['purchased_order']['ship_by_sea']) { ?>
                                                <div class="badge badge-warning orange darken-1">Ship By Sea</div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php foreach ($order['purchased_order']['order_products'] as $product) { ?>
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
                                                <button type="button" class="btn btn-default form-control btn-collapse active text-black collapse-product-option" data-target="product-option<?php echo $order['order_id'] . $product['product_id'] ?>">Details</button>
                                            </div>
                                        </div>
                                        <div id="product-option<?php echo $order['order_id'] . $product['product_id'] ?>" class="options_row table-responsive collapse">
                                            <table class="table">
                                                <?php if(isset($product['products_sizes']) && $product['products_sizes']) { ?>
                                                <?php $i = 0; foreach ($product['products_sizes'] as $option) { $i++; ?>
                                                <tr class="single_option_row">
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Option</label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control" value="<?php echo $option['name'] ?> - <?php echo $option['value'] ?>" readonly></div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Order Quantity</label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control" value="<?php echo $option['order_quantity'] ?>" readonly></div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Remain Quantity</label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control" value="<?php echo $option['remain_quantity'] ?>" readonly></div>
                                                    </td>
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
                                                        <label class="control-label">Order Quantity</label>
                                                        <div><input type="text" class="form-control" value="<?php echo $product['order_quantity'] ?>" readonly></div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <label class="control-label">Remain Quantity</label>
                                                        <div><input type="text" class="form-control order_quantity" value="<?php echo $product['remain_quantity'] ?>" readonly></div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <label class="control-label">Price</label>
                                                        <div><input type="text" class="form-control price" value="<?php echo $product['price'] ?>" readonly/></div>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="row">
                                        <div class="col-xs-4 mb-4">
                                        <form action="<?php echo route('update.to.be.stock.cancel.order.request') ?>" method="post">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>">
                                            <input type="hidden" name="supplier" value="<?php echo ($order['order_supplier']) ? $order['order_supplier']['user_id'] : '' ?>">
                                            <div class="col-sm-4  col-sm-4 col-grid">
                                              <button type="submit" name="submit" value="approve_stock_cancelled" onclick="return confirm('Are You Sure Want To Approve ?')" class="btn btn-success form-control">Approve</button>
                                            </div>
                                            <div class="col-sm-4  col-sm-4 col-grid">
                                               <button type="submit" name="submit" value="cancel_stock_cancelled" onclick="return confirm('Are You Sure Want To Cancel ?')" class="btn btn-danger form-control">Cancel</button>
                                            </div>
                                        </form>
                                        </div>
                                        <div class="col-xs-8"></div>
                                    </div>
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


