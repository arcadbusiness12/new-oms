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
                           
                           <?php if($orders) { ?>
                            <?php foreach ($orders['data'] as $order) { ?>
                            <div class="card order_list">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                <div class="row top_row">
                                    <div class="col-xs-4"><b>Order Number: #<?php echo $order['order_id'] ?></b></div>
                                    <div class="col-xs-4 text-center">
                                        <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF') && $order['supplier']) { ?>
                                        <div class="badge"><?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-4 text-right">
                                        <?php if($order['urgent']) { ?>
                                            <div class="label label-warning">Urgent</div>
                                        <?php } ?>
                                        <div>
                                            <div class="badge"><?php echo date('Y-m-d', strtotime($order['created_at'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php foreach ($order['order_products'] as $product) { ?>
                                <div class="product_list_row">
                                    <div class="row product_row">
                                        <div class="col-xs-4 col-sm-2">
                                            <img width="100" src="<?php echo $product['image'] ?>" />
                                        </div>
                                        <div class="col-xs-8 col-sm-10">
                                            <strong><?php echo $product['name'] ?></strong><br>
                                            <i><?php echo $product['model'] ?></i>
                                            <div class="options-label">
                                                <?php $units = 0; ?>
                                                <?php foreach ($product['order_product_quantities'] as $quantity) { 
                                                    if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            } 
                                                    ?>
                                                    <?php foreach ($quantity['product_options'] as $key => $option) { 
                                                        
                                                        if($option['static'] !== 'static') { 
                                                            $units += $quantity['order_quantity'];
                                                            ?>
                                                        <div class="box-label">
                                                            <?php echo $option['name'] ?> - <?php echo $option['value'] ?> = <?php echo $quantity['order_quantity']; ?>
                                                        </div>
                                                    <?php } } ?>
                                                <?php } ?>
                                                <div class="box-label">
                                                    T. Units = <?php echo $units ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="row action_row">
                                    <?php if(session('role') != 'ADMIN' && session('role') != 'STAFF') { ?>
                                    <div class="col-xs-6 col-sm-8 text-right">
                                        <button type="button" class="btn btn-default collapse-comment" data-target="summary<?php echo $order['order_id'] ?>"><b><?php echo number_format($order['total'], 2); ?></b></button>
                                    </div>
                                    <div class="col-xs-6 col-sm-2">
                                        <?php if($order['cancelled_status'] && $order['cancelled_status']['status'] == 0){ ?>
                                        <button type="button" class="btn btn-danger form-control disabled" disabled>Requested</button>
                                        <?php } else { ?>
                                        <button type="button" data-order-id="<?php echo $order['order_id'] ?>" data-supplier-id="<?php echo $order['order_supplier']['user_id'] ?>" class="btn btn-danger form-control btn-cancel-confirmed-order">Cancel</button>
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-6 col-sm-2">
                                        <a href="<?php echo URL::to('/purchase_manage/ship/' . $order['order_id']) ?>"><button type="button" class="btn btn-success form-control">Ship</button></a>
                                    </div>
            
                                    <?php } else { 
                                    $class = 'col-xs-6 col-sm-12';
            
                                    if($order['cancelled_status'] && $order['cancelled_status']['status'] == 0){ 
                                        $class = 'col-xs-6 col-sm-8'; ?>
                                    <div class="col-xs-6 col-xs-4">
                                        <label class="btn-block text-danger">Supplier Cancelled Order</label>
                                        <button type="button" class="btn btn-default" data-toggle="modal" href='#modal-oder-comment<?php echo $order['order_id'] ?>'>Comment</button>
                                        <button type="button" name="update_request" class="btn btn-success btn-accept" value="accept" data-order-id="<?php echo $order['order_id'] ?>" data-action="{{ URL::to('/purchase_manage/confirmed/update_request') }}"><b>Accept</b></button>
                                        <button type="button" name="update_request" class="btn btn-danger btn-reject" value="reject" data-order-id="<?php echo $order['order_id'] ?>" data-action="{{ URL::to('/purchase_manage/confirmed/update_request') }}"><b>Reject</b></button>
                                    </div>
                                    <div class="modal fade" id="modal-oder-comment<?php echo $order['order_id'] ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Reason</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <p><?php echo $order['cancelled_status']['reason'] ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }else if(@$order['cancelled_status']['status'] == 2){
                                        $class = 'col-xs-6 col-sm-8'; ?>
                                    <div class="col-xs-6 col-xs-4">
                                        <button type="button" class="btn btn-danger disabled" disabled>Request Rejected</button>
                                    </div>
                                    <?php } ?>
                                    
                                    <div class="<?php echo $class ?> text-right">
                                        <button type="button" class="btn btn-default collapse-comment" data-target="summary<?php echo $order['order_id'] ?>"><b><?php echo number_format($order['total'],2); ?></b></button>
                                    </div>
                                    <?php } ?>
                                </div>
                                <!--  comment code start here  -->
                                <?php foreach ($order['order_histories'] as $history) { ?>
                                    <div>
                                        <label><?php echo $history['name'] ?>:</label>
                                        <i><?php echo $history['comment'] ?></i>
                                        <i style="float: right;"><?php echo $history['created_at']; ?></i>
                                    </div>
                                <?php } ?>
                                <form action="<?php echo URL::to('/purchase_manage/update_awaiting_action_order') ?>" method="post">
                                  {{csrf_field()}}
                                  <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                  <div class="row approval-comment" style="-webkit-display:-webkit-flex;-webkit-flex-wrap:wrap;-ms-display:-ms-flexbox;-ms-flex-wrap:wrap;display:flex;flex-wrap:wrap;flex-direction:row;">
                                    <div class="col-xs-10">
                                      <label class="control-label">Admin Reply:</label>
                                      <textarea name="instruction" class="form-control" cols="5" rows="3" required=""></textarea>
                                      <div class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-xs-2">
                                      <button type="submit" name="submit" value="save-comment" class="btn btn-block btn-success" style="position: absolute;left: 0;bottom: 0;width: 85%;">Submit</button>
                                    </div>
                                  </div>
                                </form>
                                <!--  comment code start here  -->
                                <div id="summary<?php echo $order['order_id'] ?>" class="summary-panel">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <table class="table">
                                                <tr>
                                                    <th>Option</th>
                                                    <th>Order Quantity</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                </tr>
                                                <?php foreach ($order['order_products'] as $product) { ?>
                                                    <?php foreach ($product['order_product_quantities'] as $quantity) { 
                                                        if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            } 
                                                        ?>
                                                        <tr>
                                                        <?php if($quantity['product_options']) { ?>
                                                        <?php foreach ($quantity['product_options'] as $key => $option) { if($option['static'] !== 'static') { ?>
                                                            <td><?php echo $option['name'] . ' - ' . $option['value']; ?></td>
                                                        <?php } } ?>
                                                        <?php }else{ ?>
                                                            <td>-</td>
                                                        <?php } ?>
                                                            <td><?php echo $quantity['order_quantity'] ?></td>
                                                            <?php if($quantity['order_quantity'] > 0) { ?>
                                                            <td><?php echo number_format($quantity['price'],2); ?></td>
                                                            <td><?php echo number_format($quantity['total'],2); ?></td>
                                                            <?php }else{ ?>
                                                            <td><div class="label label-danger">Not Available</div></td>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-5 col-xs-offset-7">
                                            <table class="table">
                                                <?php foreach ($order['order_totals'] as $value) { ?>
                                                <tr>
                                                    <th><?php echo $value['code'] ?></th>
                                                    <td><?php echo number_format($value['value'],2); ?></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </div>
                                    </div>
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

