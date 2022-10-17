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
                                            <form name="filter_orders" id="filter_orders" method="get" action="<?php echo route('add.complaint') ?>">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group form-float">
                                                            <div class="form-line">
                                                                <label class="form-label" for="order_id">Order ID</label>
                                                                <input type="text" name="order_id" id="order_id" class="form-control" autocomplete="off" value="{{ isset($old_input['order_id'])?$old_input['order_id']:'' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-line">
                                                            <label class="form-label" for="order_id">Order ID</label>
                                                            <select name="supplier" class="form-control">
                                                                <option value="">Select Supplier</option>
                                                                <?php foreach ($suppliers as $supplier) { ?>
                                                                    <option value="<?php echo $supplier['user_id'] ?>" <?php if(isset($old_input['supplier']) && $old_input['supplier'] == $supplier['user_id']) { ?> selected="selected" <?php } ?> ><?php echo $supplier['firstname'] .' '. $supplier['lastname'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
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
            <?php if(Session::has('message')) { ?>
                <div class="alert <?php echo Session::get('alert-class', 'alert-success') ?> alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo Session::get('message') ?>
                </div>
                <?php } ?>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="body-card">
                        <div class="panel-heading">
                            Add Complaint
                          </div>
                          
                           <div id="status_changed_msg" style="display: none"></div>
                           
                           <?php if($orders) { ?>
                            <?php foreach ($orders as $order) { ?>
                            <div class="card order_list mb-4">
                                <div class="row top_row">
                                    <div class="col-xs-4 col-sm-4 mb-4 text-black"><b>Order Number: #<?php echo $order['shipped_id'] ?></b></div>
                                    <div class="col-xs-4 col-sm-4 text-center">
                                        <?php if(session('role') == 'ADMIN' && $order['supplier']) { ?>
                                        <div class="badge badge-secondary font-weight-bold"><?php echo ucfirst($order['supplier']['firstname'] . " " . $order['supplier']['lastname']) ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 text-right">
                                        <?php if($order['urgent']) { ?>
                                            <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><b> Urgent </b></div>
                                        <?php } ?>
                                        <?php if($order['ship_by_sea']) { ?>
                                            <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><b> Ship By Sea </b></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php foreach ($order['products'] as $product) { ?>
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
                                            <button type="button" class="btn btn-default form-control btn-collapse collapse-product-option active" data-target="product-option<?php echo $order['shipped_id'] . $product['product_id'] ?>">Details</button>
                                        </div>
                                    </div>
                                    <div id="product-option<?php echo $order['shipped_id'] . $product['product_id'] ?>" class="options_row table-responsive collapse">
                                        <table class="table">
                                        <?php $i = 0; foreach ($product['quantities'] as $quantity) { $i++; ?>
                                            <tr class="single_option_row">
                                                <?php foreach ($quantity['options'] as $option) { ?>
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
                                                    <div><input type="text" class="form-control" value="<?php echo $quantity['received'] ?>" readonly></div>
                                                </td>
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Missing Quantity</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control order_quantity" value="<?php echo $quantity['remain'] ?>" readonly></div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </table>
                                    </div>
                                </div>
                                <?php } ?>
                                <form action="<?php echo route('update.complaint.order') ?>" method="post">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-xs-8 col-sm-8">
                                            <div class="form-group">
                                                <label class="pl-4 text-black" style="font-size: 18px"><strong>Comments</strong></label>
                                                <div class="history-panel">
                                                <?php if($order['history']) { ?>
                                                <?php foreach ($order['history'] as $k => $history) { ?>
                                                <?php foreach ($history as $user => $comment) { ?>
                                                    <div>
                                                        <label class="text-black"><strong><?php echo ucfirst($user); ?>:</strong></label>
                                                        <i><?php echo $comment ?></i>
                                                    </div>
                                                <?php } ?>
                                                <?php } ?>
                                                <?php } ?>
                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>">
                                                    <input type="hidden" name="shipped_id" value="<?php echo $order['shipped_id'] ?>">
                                                    <div>
                                                        <label class="control-label text-black"><strong> Admin Reply:</strong></label>
                                                        <textarea name="comment" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-sm-4">
                                            <div class="form-group">
                                                <label></label>
                                                <button type="submit" name="submit" value="update_complaint_order" class="btn btn-success btn-block active">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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


