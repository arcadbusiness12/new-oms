@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            
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
                            Edit Orders
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                           <div id="status_changed_msg" style="display: none"></div>

                           <?php if(Session::has('message')) { ?>
                            <div class="alert <?php echo Session::get('alert-class', 'alert-info') ?> alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <?php echo Session::get('message') ?>
                            </div>
                            <?php } ?>
                            <?php if($order) { ?>
                                <div class="card order_list">
                                    <form action="<?php echo URL::to('/purchase_manage/add_to_ship') ?>" method="post" id="ship_order">
                                        {{csrf_field()}}
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                        <input type="hidden" name="urgent" value="<?php echo $order['urgent'] ?>" />
                                        <input type="hidden" name="link" value="<?php echo $order['link'] ?>" />
                                        <div class="row top_row">
                                            <div class="col-xs-8"><b>Order Number: #<?php echo $order['order_id'] ?></b></div>
                                            <div class="col-xs-4 text-right">
                                                <?php if($order['urgent']) { ?>
                                                    <div class="label label-warning">Urgent</div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php foreach ($order['orderProducts'] as $product_key => $product) { ?>
                                        <div class="product_list_row">
                                            <div class="row product_row">
                                                <div class="col-xs-4 col-sm-2">
                                                    <img width="100" src="<?php echo $product['image'] ?>" />
                                                </div>
                                                <div class="col-xs-8 col-sm-10">
                                                    <strong><?php echo $product['name'] ?></strong><br>
                                                    <i><?php echo $product['model'] ?></i>
                                                    <div class="options-label">
                                                        <?php if(isset($product['options'])) {  ?>
                                                        <?php foreach ($product['options'] as $key => $option) {  ?>
                                                        <?php if($option['static'] !== 'static') { ?>
                                                            <div class="box-label">
                                                                <?php echo $option['name'] ?> - <?php echo $option['value'] ?>
                                                            </div>
                                                        <?php } ?>
                                                        <?php } ?>
                                                        <?php } ?>
                                                        <div class="box-label">
                                                            T. Units = <?php echo $product['unit'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="shipped_product_row table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <td class="col-xs-6">
                                                            <?php if(isset($product['options']) && isset($product['options']['static'])) { ?>
                                                            <label><?php echo $product['options']['static']['name'] ?> - <?php echo $product['options']['static']['value'] ?></label>
                                                            <?php } else { ?>
                                                            <label>&nbsp;</label>
                                                            <?php } ?>
                                                            <div class="">
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][type]" value="<?php echo $product['type'] ?>">
                                                            <?php if(isset($product['options'])) {  ?>
                
                                                            <?php if(isset($product['options']['static']) && count($product['options']) == 1) {  ?>
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][option][static][order_product_quantity_id]" value="<?php echo $option['order_product_quantity_id'] ?> static">
                                                                <input type="text" value="<?php echo $option['name'] ?> - <?php echo $option['value'] ?> = <?php echo $option['quantity'] ?>" class="form-control" readonly/>
                                                            <?php }else{ ?>
                                                                <?php foreach ($product['options'] as $key => $option) { ?>
                                                                    <?php if($option['static'] !== 'static') { ?>
                                                                    <input type="hidden" name="product[<?php echo $product['product_id'] ?>][option][<?php echo $key ?>][order_product_quantity_id]" value="<?php echo $option['order_product_quantity_id'] ?> not-static">
                                                                    <input type="text" value="<?php echo $option['name'] ?> - <?php echo $option['value'] ?> = <?php echo $option['quantity'] ?> " class="form-control" readonly/>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                            
                                                            <?php } else { ?>
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][option][0][order_product_quantity_id]" value="<?php echo $product['order_product_quantity_id'] ?> else">
                                                                <input type="text" value="<?php echo $product['quantity'] ?>" class="form-control" readonly/>
                                                            <?php } ?>
                                                            </div>
                                                        </td>
                                                        <td class="col-xs-6">
                                                            <label>Shipped Quantity</label>
                                                            <div class="single_option_row">
                                                            <?php if(isset($product['options'])) {  ?>
                
                                                            <?php if(isset($product['options']['static']) && count($product['options']) == 1) {  ?>
                                                                <input type="text" pattern="^[0-9][0-9]*$" title="Enter greater than or equal to 0" name="product[<?php echo $product['product_id'] ?>][option][static][shipped_quantity]" class="form-control" required />
                                                            <?php }else{ ?>
                                                                <?php foreach ($product['options'] as $key => $value) { ?>
                                                                    <?php if($option['static'] !== 'static') { ?>
                                                                    <input type="text" pattern="^[0-9][0-9]*$" title="Enter greater than or equal to 0" name="product[<?php echo $product['product_id'] ?>][option][<?php echo $key ?>][shipped_quantity]" class="form-control" required />
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                            
                                                            <?php } else { ?>
                                                                <input type="text" pattern="^[0-9][0-9]*$" title="Enter greater than or equal to 0" name="product[<?php echo $product['product_id'] ?>][option][0][shipped_quantity]" class="form-control" required />
                                                            <?php } ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="row instruction_row">
                                            <div class="col-xs-12 col-sm-8">
                                                <div class="row button-row">
                                                    <div class="col-xs-12 col-sm-4">
                                                        <button type="button" class="btn btn-default form-control collapse-comment" data-target="history<?php echo $order['order_id'] ?>">Comment</button>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4">
                                                        <input type="hidden" name="submit" value="ship_order" />
                                                        <button type="submit" class="btn btn-success form-control" id="submit-ship-order">Ship Order</button>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4">
                                                        <label class="control-label">Add Local Shipping To Forwarder</label>
                                                        <div>
                                                            <input type="text" pattern="^(\d*\.)?\d+$" title="Enter valid price" name="local_cost" step="any" placeholder="Amount" class="form-control" required="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="history<?php echo $order['order_id'] ?>" class="history-panel">
                                                <?php foreach ($order['orderHistories'] as $history) { ?>
                                                    <div>
                                                        <label><?php echo $history['name'] ?>:</label>
                                                        <i><?php echo $history['comment'] ?></i>
                                                    </div>
                                                <?php } ?>
                                                    <div>
                                                        <label class="control-label">Supplier Reply:</label>
                                                        <textarea name="instruction" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <input type="text" name="shipping_name" placeholder="Shipping Name" class="form-control" required />
                                                <input type="text" name="tracking_number" placeholder="Tracking Number" class="form-control" required />
                                                <div class="fancy-radio">
                                                    <input type="radio" name="shipped" id="forwarder" value="forwarder" oninvalid="$('.shipped_msg').text('Please Select any one shipping');" required />
                                                    <label for="forwarder">Shipped To Forwarder</label>
                                                </div>
                                                <div class="fancy-radio">
                                                    <input type="radio" name="shipped" id="dubai" value="dubai" oninvalid="$('.shipped_msg').text('Please Select any one shipping');" required />
                                                    <label for="dubai">Shipped To Dubai</label>
                                                </div>
                                                <div class="text-danger shipped_msg"></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <?php } else { ?>
                                <div class="alert alert-info">No Order Found!</div>
                                <?php } ?>
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
            <form action="{{ route('supplier.cancelled.awaiting.action.order.request') }}" method="post">
                {{ csrf_field() }}
            <div class="modal-header">
                <h4 class="modal-title">Reason For Cancel</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="order_id" />
                <input type="hidden" name="supplier" />
                <textarea name="comment" rows="5" class="form-control" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success submit-cancel-confirmed-order">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript" src="{{URL::asset('assets/js/purchase_management.js') }}"></script>
@endpush

