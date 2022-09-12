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
                                    <form action="<?php echo route('add.to.ship') ?>" method="post" id="ship_order">
                                        {{csrf_field()}}
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                        <input type="hidden" name="urgent" value="<?php echo $order['urgent'] ?>" />
                                        <input type="hidden" name="link" value="<?php echo $order['link'] ?>" />
                                        <div class="row top_row">
                                            <div class="col-sm-8 text-black"><b>Order Number: #<?php echo $order['order_id'] ?></b></div>
                                            <div class="col-sm-4 mb-2 text-right">
                                                <?php if($order['urgent']) { ?>
                                                    <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><b>Urgent</b></div>
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
                                                    <div class="options-label text-black ">
                                                        <?php if(isset($product['options'])) {  ?>
                                                        <?php foreach ($product['options'] as $key => $option) {  ?>
                                                        <?php if($key !== 'static') { ?>
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
                                                    <tr class="single_option_row">
                                                        <td class="col-xs-6">
                                                            <?php if(isset($product['options']) && isset($product['options']['static'])) { ?>
                                                            <label class="text-black"><strong> <?php echo $product['options']['static']['name'] ?> - <?php echo $product['options']['static']['value'] ?></strong></label>
                                                            <?php } else { ?>
                                                            <label>&nbsp;</label>
                                                            <?php } ?>
                                                            <div>
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][type]" value="<?php echo $product['type'] ?>">
                                                            <?php if(isset($product['options'])) {  ?>
                
                                                            <?php if(isset($product['options']['static']) && count($product['options']) == 1) {  ?>
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][option][static][order_product_quantity_id]" value="<?php echo $option['order_product_quantity_id'] ?>">
                                                                <input type="text" value="<?php echo $option['name'] ?> - <?php echo $option['value'] ?> = <?php echo $option['quantity'] ?>" class="form-control" readonly/>
                                                            <?php }else{ ?>
                                                                <?php foreach ($product['options'] as $key => $option) { ?>
                                                                    <?php if($key !== 'static') { ?>
                                                                    <input type="hidden" name="product[<?php echo $product['product_id'] ?>][option][<?php echo $key ?>][order_product_quantity_id]" value="<?php echo $option['order_product_quantity_id'] ?>">
                                                                    <input type="text" value="<?php echo $option['name'] ?> - <?php echo $option['value'] ?> = <?php echo $option['quantity'] ?>" class="form-control" readonly/>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                            
                                                            <?php } else { ?>
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][option][0][order_product_quantity_id]" value="<?php echo $product['order_product_quantity_id'] ?>">
                                                                <input type="text" value="<?php echo $product['quantity'] ?>" class="form-control" readonly/>
                                                            <?php } ?>
                                                            </div>
                                                        </td>
                                                        <td class="col-xs-6">
                                                            <label class="text-black"><strong> Shipped Quantity </strong></label>
                                                            <div class="single_option_row">
                                                            <?php if(isset($product['options'])) {  ?>
                
                                                            <?php if(isset($product['options']['static']) && count($product['options']) == 1) {  ?>
                                                                <input type="text" pattern="^[0-9][0-9]*$" title="Enter greater than or equal to 0" name="product[<?php echo $product['product_id'] ?>][option][static][shipped_quantity]" class="form-control" required />
                                                            <?php }else{ ?>
                                                                <?php foreach ($product['options'] as $key => $value) { ?>
                                                                    <?php if($key !== 'static') { ?>
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
                                                        <button type="button" class="btn btn-default active form-control collapse-comment" data-target="history<?php echo $order['order_id'] ?>">Comment</button>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4 text-black">
                                                        <input type="hidden" name="submit" value="ship_order" />
                                                        <button type="submit" class="btn btn-success active form-control" id="submit-ship-order">Ship Order</button>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4 text-black amout-input" style="display: none;">
                                                        <label class="control-label text-lable"><strong> Add Local Shipping To Forwarder </strong></label>
                                                        <div>
                                                            <input type="text" pattern="^(\d*\.)?\d+$" title="Enter valid price" name="local_cost" step="any" placeholder="Amount" class="form-control amount-input-field" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="history<?php echo $order['order_id'] ?>" class="history-panel mt-2">
                                                <?php foreach ($order['orderHistories'] as $history) { ?>
                                                    <div class="pl-4 pr-4 mt-2 text-black">
                                                        <label><strong><?php echo $history['name'] ?>: </strong></label>
                                                        <i><?php echo $history['comment'] ?></i>
                                                    </div>
                                                <?php } ?>
                                                    <div class="pl-4 pr-4 mt-2 mb-2 text-black">
                                                        <label class="control-label text-black "><strong>Supplier Reply:</strong> </label>
                                                        <textarea name="instruction" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4 text-black">
                                                <input type="text" name="shipping_name" placeholder="Shipping Name" class="form-control" required />
                                                <input type="text" name="tracking_number" placeholder="Tracking Number" class="form-control mb-2" required />
                                                <div class="fancy-radio mt-2">
                                                    <input type="radio" name="shipped" id="forwarder" class="shippedTo" data-action="forwarder" value="forwarder" oninvalid="$('.shipped_msg').text('Please Select any one shipping');" required />
                                                    <label for="forwarder"><strong> Shipped To Forwarder </strong></label>
                                                </div>
                                                <div class="fancy-radio">
                                                    <input type="radio" name="shipped" id="dubai" class="shippedTo" data-action="dubia" value="dubai" oninvalid="$('.shipped_msg').text('Please Select any one shipping');" required />
                                                    <label for="dubai"><strong> Shipped To Dubai </strong></label>
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
                <button type="submit" class="btn btn-success active submit-cancel-confirmed-order">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript" src="{{URL::asset('assets/js/purchase_management.js') }}"></script>
@endpush

