@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="body-card">
                            <div class="panel-heading">
                            Confirmed Order Details
                            <a href="{{route('get.to.be.shipped')}}" id="search_filter" class="btn btn-primary pull-right" style="float: right;"><i class="icon icon-arrow-circle-left icon-lg"></i></a>
                          </div>
                          <?php if($order) { ?>
                            <div class="card order_list">
                                <div class="row top_row">
                                    <div class="col-xs-8 col-sm-8 text-black mb-2 mt-2"><b>Order Number: #<?php echo $order['order_id'] ?></b></div>
                                    <div class="col-xs-4 col-sm-4 text-right text-right">
                                        <?php if($order['urgent']) { ?>
                                            <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><b>Urgent</b> </div>
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
                                                <?php foreach ($product['quantities'] as $quantity) { 
                                                     if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            } 
                                                    ?>
                                                    <?php foreach ($quantity['product_options'] as $key => $option) { 
                                                        if($option['static'] !== 'static') { ?>
                                                        <div class="box-label text-black">
                                                            <?php echo $option['name'] ?> - <?php echo $option['value'] ?> = <?php echo $quantity['order_quantity'] - $quantity['shipped_quantity']; ?>
                                                        </div>
                                                    <?php } } ?>
                                                <?php } ?>
                                                <div class="box-label text-black">
                                                    T. Units = <?php echo $product['unit'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shipped_product_row table-responsive">
                                        <table class="table">
                                        <?php $i = 0; foreach ($product['quantities'] as $quantity) { 
                                            if($product['product_id'] != $quantity['order_product_id']) {
                                                                                continue;
                                                                            }
                                            // $quantity = $quantity['order_quantity'] - $quantity['shipped_quantity'];
                                            $i++; ?>
                                            <?php if($quantity['quantity'] > 0) { ?>
                                            <tr class="single_option_row">
                                                <?php foreach ($quantity['product_options'] as $option) { ?>
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label"><strong> <?php echo $option['name'] ?> </strong></label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control" value="<?php echo $option['value'] ?>" readonly></div>
                                                </td>
                                                <?php } ?>
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label"><strong> Quantity </strong></label>
                                                    <?php } ?>
                                                    <?php $tobequantity = $quantity['order_quantity'] - $quantity['shipped_quantity']; ?>
                                                    <div><input type="text" class="form-control" value="<?php echo $tobequantity ?>" readonly></div>
                                                </td>
                                                <?php if($quantity['quantity'] > 0) { ?>
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
                                                <?php } else { ?>
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">&nbsp;</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control not-available" value="Not Available" disabled /></div>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                        <?php } ?>
                                        </table>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="row instruction_row">
                                    <div class="col-xs-12 col-sm-8">
                                        <?php if($order['link']) { ?>
                                        <div class="ml-3 mb-2">
                                            <input type="text" name="supplier_link" value="<?php echo $order['link'] ?>" class="form-control copy_to_clipboard" placeholder="Supplier Link" readonly/>
                                        </div>
                                        <?php } ?>
                                        <div class="row button-row">
                                            <div class="col-xs-12 col-sm-4">
                                                <button type="button" class="btn btn-default form-control active text-black collapse-comment" data-target="history<?php echo $order['order_id'] ?>">Comment</button>
                                            </div>
                                        </div>
                                        <div id="history<?php echo $order['order_id'] ?>" class="history-panel mt-2 text-black">
                                        <?php foreach ($order['orderHistories'] as $history) { ?>
                                            <div class="purchase-history-contnet">
                                                <label class="font-weight-bold"><?php echo $history['name'] ?>:</label>
                                                <i><?php echo $history['comment'] ?></i>
                                                <i style="float: right;"><?php echo date('Y-m-d', strtotime($history['created_at'])); ?></i>
                                            </div>
                                        <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 total_column">
                                        <div class="row">
                                            <div class="col-xs-7 col-sm-6 text-black">
                                                <?php foreach ($order['orderTotals'] as $key => $value) { ?>
                                                <label class="font-weight-bold"><?php echo $value['code'] ?></label>
                                                <?php } ?>
                                            </div>
                                            <div class="col-xs-5 col-sm-6">
                                                <?php foreach ($order['orderTotals'] as $key => $value) { ?>
                                                <input type="text" value="<?php echo number_format($value['value'],2) ?>" class="form-control" readonly/>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

