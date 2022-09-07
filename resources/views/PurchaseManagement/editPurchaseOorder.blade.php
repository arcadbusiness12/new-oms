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
                                <form action="<?php echo route('update.purchase.order') ?>" method="post">
                                    {{csrf_field()}}
                                    <input type="hidden" name="order_id" value="<?php echo $order->order_id ?>" />
                                    <div class="row top_row">
                                        <div class="col-sm-3 text-black"><b>Order Number: #<?php echo $order->order_id ?></b></div>
                                        <div class="col-sm-3 text-center">
                                            <?php if($order['orderSupplier']) { ?>
                                            <div class="badge badge-secondary"><strong> <?php echo ucfirst($order['orderSupplier']['firstname'] . " " . $order['orderSupplier']['lastname']) ?></strong></div>
                                            <?php } ?>
                                        </div>
            
                                        <div class="col-sm-3 text-center text-black mb-2">
                                        @if($order['order_status_id'] == 0)
                                            <select name="supplier" class="form-control">
                                                <?php foreach ($suppliers as $supplier) { ?>
                                                    <option value="<?php echo $supplier['user_id'] ?>" @if($supplier['user_id'] == $order['orderSupplier']['user_id']) selected="selected" : "" @endif><?php echo $supplier['firstname'] .' '. $supplier['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        @endif
                                        </div>
            
                                        <div class="col-sm-3 text-right mb-2">
                                            <?php if($order['urgent']) { ?>
                                                <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><strong> Urgent </strong></div>
                                            <?php } ?>
                                            <div>
                                                <div class="badge badge-secondary"><strong> <?php echo date('Y-m-d', strtotime($order['created_at'])) ?></strong></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php foreach ($order['orderProducts'] as $product) { ?>
                                    <div class="product_list_row">
                                        <div class="row product_row">
                                            <div class="col-xs-4 col-sm-2">
                                                <img width="100" src="<?php echo $product['image'] ?>" />
                                            </div>
                                            <div class="<?php echo (session('role') == 'ADMIN') ? 'col-xs-6 col-sm-8' : 'col-xs-8 col-sm-10' ?>">
                                                <?php if($product['type'] == 'manual') { ?> 
                                                    
                                                    <?php if($product['model']) { ?>
                                                    <input type="text" name="product[<?php echo $product['product_id'] ?>][model]" class="form-control" value="<?php echo $product['model'] ?>" />
                                                    <?php }else {?>
                                                        <input type="text" name="product[<?php echo $product['product_id'] ?>][name]" class="form-control" placeholder="Add title" value="<?php echo $product['name'] ?>" />
                                                  <?php  }  ?>
                                                <?php } else { ?>
                                                    <strong><?php echo $product['name'] ?></strong><br>
                                                    <i><?php echo $product['model'] ?></i>
                                                <?php }  ?>
                                            </div>
                                        </div>
            
                                        <div id="product-option<?php echo $order['order_id'] . $product['product_id'] ?>" class="options_row table-responsive">
                                            <table class="table">
                                            <?php $i = 0; foreach ($product['orderProductQuantities'] as $quantity) { 
                                                if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            }
                                                $i++; ?>
                                                <tr class="single_option_row">
                                                    <?php foreach ($quantity['productOptions'] as $option) { ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label"><strong> <?php echo $option['name'] ?> </strong></label>
                                                        <?php } ?>
                                                        <?php if($option['options']) { ?>
                                                        <select name="product[<?php echo $product['product_id'] ?>][option][<?php echo $option['order_product_option_id'] ?>]" class="form-control">
                                                        <?php foreach ($option['options'] as $key => $op) { ?>
                                                        <option value="<?php echo $op['option_value_id'] ?>" <?php if($option['product_option_value_id'] == $op['option_value_id']) { ?> selected="selected" <?php } ?> ><?php echo $op['name'] ?></option>
                                                        <?php } ?>
                                                        </select>
                                                        <?php }else{ ?>
                                                        <div><input type="text" class="form-control" value="<?php echo $option['value'] ?>" readonly></div>
                                                        <?php } ?>
                                                    </td>
                                                    <?php } ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label"><strong> Quantity </strong></label>
                                                        <?php } ?>
                                                        <div>
                                                            <input type="text" name="product[<?php echo $product['product_id'] ?>][quantity][<?php echo $quantity['order_product_quantity_id'] ?>]" class="form-control" value="<?php echo $quantity['quantity'] ?>">
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </table>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="row instruction_row">
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="row button-row">
                                                <div class="col-xs-12 col-sm-5">
                                                    <button type="button" class="btn btn-default form-control collapse-comment" data-target="history<?php echo $order['order_id'] ?>">Comment</button>
                                                </div>
                                                <div class="col-sm-8"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                        </div>
                                        <div class="col-xs-6 col-sm-4">
                                            <button type="submit" name="submit" value="update-purchase-order" class="btn form-control btn-primary">Update</button>
                                        </div>
                                        <div class="col-xs-12 mt-2">
                                            <div id="history<?php echo $order['order_id'] ?>" class="history-panel">
                                            <?php foreach ($order['orderHistories'] as $history) { ?>
                                                <div class="p-2 text-black">
                                                    <label><strong> <?php echo $history['name'] ?>: </strong><?php echo $history['comment'] ?></label>
                                                    <input type="hidden" name="history[id]" value="<?php echo $history['order_history_id'] ?>" />
                                                    {{-- <textarea name="history[comment][]" class="form-control"><?php echo $history['comment'] ?></textarea> --}}
                                                </div>
                                            <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php } else { ?>
                            <div class="alert alert-info">No Orders Found!</div>
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

