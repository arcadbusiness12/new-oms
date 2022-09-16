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
                            <div class="card order_list mb-4">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                <div class="row top_row">
                                    <div class="col-sm-4 col-grid text-black mb-4 mt-2"><b>Order Number: #<?php echo $order['order_id'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </b>
                                    </div>
                                    <div class="col-sm-4 text-center col-grid text-black mb-4 mt-2">
                                        <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF') && $order['supplier']) { ?>
                                        <div class="badge badge-secondary"><b><?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?></b></div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-4 text-right col-grid text-black mb-4 mt-2">
                                        <?php if($order['urgent']) { ?>
                                            <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><b>Urgent</b></div>
                                        <?php } ?>
                                        <div>
                                            <div class="badge badge-secondary"><b><?php echo date('Y-m-d', strtotime($order['created_at'])) ?></b></div>
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
                                                <?php foreach ($product['quantities'] as $quantity) { 
                                                    if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            } 
                                                    ?>
                                                    <?php foreach ($quantity['productOptions'] as $key => $option) { 
                                                        
                                                        if($option['static'] !== 'static') { 
                                                            ?>
                                                        <div class="box-label text-black">
                                                            <?php echo $option['name'] ?> - <?php echo $option['value'] ?> = <?php echo $quantity['order_quantity']; ?>
                                                        </div>
                                                    <?php } } ?>
                                                <?php } ?>
                                                <div class="box-label text-black">
                                                    T. Units = <?php echo $product['unit'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="row action_row mt-2">
                                    <?php if(session('role') != 'ADMIN' && session('role') != 'STAFF') { ?>
                                    <div class="col-xs-6 col-sm-8 text-right">
                                        <button type="button" class="btn btn-default active active collapse-comment" data-target="summary<?php echo $order['order_id'] ?>"><b><?php echo number_format($order['total'], 2); ?></b></button>
                                    </div>
                                    <div class="col-xs-6 col-sm-2">
                                        <?php if($order['cancelled_status'] && $order['cancelled_status']['status'] == 0){ ?>
                                        <button type="button" class="btn btn-danger active form-control disabled" disabled>Requested</button>
                                        <?php } else { ?>
                                        <button type="button" data-order-id="<?php echo $order['order_id'] ?>" data-supplier-id="<?php echo $order['order_supplier']['user_id'] ?>" class="btn btn-danger active form-control btn-cancel-confirmed-order">Cancel</button>
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-6 col-sm-2">
                                        <a href="<?php echo route('ship.order', $order['order_id']) ?>"><button type="button" class="btn btn-success active form-control">Ship</button></a>
                                    </div>
            
                                    <?php } else { 
                                    $class = 'col-xs-6 col-sm-12';
            
                                    if($order['cancelled_status'] && $order['cancelled_status']['status'] == 0){ 
                                        $class = 'col-xs-6 col-sm-8'; ?>
                                    <div class="col-xs-6 col-sm-4">
                                        <div class="ml-4">
                                            <label class="btn-block text-danger"><strong> Supplier Cancelled Order {{session('role')}}</strong></label>
                                            <button type="button" class="btn btn-default active" data-toggle="modal" href='#modal-oder-comment<?php echo $order['order_id'] ?>'>Comment</button>
                                            <button type="button" name="update_request" class="btn btn-success active btn-accept" value="accept" data-order-id="<?php echo $order['order_id'] ?>" data-action="{{ route('update.confirmed.approval.order') }}"><b>Accept</b></button>
                                            <button type="button" name="update_request" class="btn btn-danger active btn-reject" value="reject" data-order-id="<?php echo $order['order_id'] ?>" data-action="{{ route('update.confirmed.approval.order') }}"><b>Reject</b></button>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="modal-oder-comment<?php echo $order['order_id'] ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Reason</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    
                                                </div>
                                                <div class="modal-body">
                                                    <p><?php echo $order['cancelled_status']['reason'] ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }else if(@$order['cancelled_status']['status'] == 2){
                                        $class = 'col-xs-6 col-sm-8'; ?>
                                    <div class="col-xs-6 col-sm-4">
                                        <button type="button" class="btn btn-danger active disabled" disabled>Request Rejected</button>
                                    </div>
                                    <?php } ?>
                                    
                                    <div class="<?php echo $class ?> text-right">
                                        <button type="button" class="btn btn-default active collapse-comment mr-4"><b><?php echo number_format($order['total'],2); ?></b></button>
                                    </div>
                                    <?php } ?>
                                </div>
                                <!--  comment code start here  -->
                                
                                <form action="<?php echo route('update.awaiting.action.order') ?>" method="post">
                                    <?php foreach ($order['order_histories'] as $history) { ?>
                                        <div class="pl-4 pr-4 mt-2 text-black">
                                            <label><strong><?php echo $history['name'] ?>:</strong></label>
                                            <i><?php echo $history['comment'] ?></i>
                                            <i style="float: right;"><?php echo date('Y-m-d', strtotime($history['created_at'])); ?></i>
                                        </div>
                                    <?php } ?>
                                  {{csrf_field()}}
                                  <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                  <div class="row approval-comment pl-4 pr-4 pb-4" style="-webkit-display:-webkit-flex;-webkit-flex-wrap:wrap;-ms-display:-ms-flexbox;-ms-flex-wrap:wrap;display:flex;flex-wrap:wrap;flex-direction:row;">
                                    <div class="col-sm-10 text-black">
                                      <label class="control-label"><strong> Admin Reply: </strong></label>
                                      <textarea name="instruction" class="form-control" cols="5" rows="3" required=""></textarea>
                                      <div class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-sm-2 text-black">
                                      <button type="submit" name="submit" value="save-comment" class="btn btn-block active btn-success" style="position: absolute;left: 0;bottom: 0;width: 85%;">Submit</button>
                                    </div>
                                  </div>
                                </form>
                                <!--  comment code start here  -->
                                <div class="col-sm-12 mb-4">
                                    <div class="col-sm-4">

                                        <button type="button" class="btn btn-secondary collapse-comment mr-4" data-target="summary<?php echo $order['order_id'] ?>"><b>Summary</b></button>
                                    </div>
                                </div>
                                <div id="summary<?php echo $order['order_id'] ?>" class="summary-panel collapse ml-4 mr-4">
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
                                                    <?php foreach ($product['quantities'] as $quantity) { 
                                                        if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            } 
                                                        ?>
                                                        <tr>
                                                        <?php if($quantity['productOptions']) { ?>
                                                        <?php foreach ($quantity['productOptions'] as $key => $option) { 
                                                            if($option['static'] !== 'static') { ?>
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
                                        <div class="col-sm-5 offset-7">
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
<div class="modal fade" id="model-cancel-order">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('confirmed.order.cancelled') }}" method="post">
                {{ csrf_field() }}
            <div class="modal-header">
                <h4 class="modal-title">Reason For Cancel</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="order_id" />
                <input type="hidden" name="supplier" />
                <input type="hidden" name="confirmed_action" />
                <textarea name="comment" rows="5" class="form-control" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success active submit-cancel-confirmed-order">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript" src="{{URL::asset('assets/js/purchase_management.js') }}"></script>
@endpush

