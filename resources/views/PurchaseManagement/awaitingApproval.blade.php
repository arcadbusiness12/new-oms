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
                            Awaiting Approval
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                           <div id="status_changed_msg" style="display: none"></div>
                           
                           <?php if($orders['data']) { ?>
                            <?php foreach ($orders['data'] as $order) { ?>
                            <div class="card order_list mb-4">
                                <form action="<?php echo route('update.awaiting.approval.order') ?>" method="post" class="form-awaiting-approval">
                                    {{csrf_field()}}
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                     <div class="row top_row">
                                        <div class="col-sm-4 col-grid text-black mb-4 mt-2"><b>Order Number: #<?php echo $order['order_id'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            
                                                <a href="{{route('edit.purchase.orders', $order['order_id'])}}" class="btn btn-primary active"><i class="icon icon-pencil"></i></a>
                                            
                                        </b>
                                    </div>
                                        <div class="col-sm-4 text-center col-grid text-black mb-4 mt-2">
                                            <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF') && $order['supplier']) { ?>
                                            <div class="badge badge-secondary font-weight-bold"><b><?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?></b></div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-sm-4 text-right col-grid text-black mb-4 mt-2">
                                            <?php if($order['urgent']) { ?>
                                                <div class="badge badge-warning orange darken-1" style="font-size: 15px;"><b>Urgent</b></div>
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
                                                <button type="button" class="btn btn-default active form-control btn-collapse active collapse-product-option" data-toggle="collapse" data-target="#product-option<?php echo $order['order_id'] . $product['product_id'] ?>" aria-expanded="false" aria-controls="collapseExample">Details</button>
                                            </div>
                                        </div>
                                        <div id="product-option<?php echo $order['order_id'] . $product['product_id'] ?>" class="options_row table-responsive collapse">
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
                                                        <label class="control-label"><strong> <?php echo $option['name'] ?></strong></label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control" value="<?php echo $option['value'] ?>" readonly></div>
                                                    </td>
                                                    <?php } ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label"><strong> Quantity </strong></label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control" value="<?php echo $quantity['quantity'] ?>" readonly></div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label"><strong> Order Quantity </strong></label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control order_quantity" value="<?php echo $quantity['order_quantity'] ?>" readonly></div>
                                                    </td>
                                                    <?php if($quantity['order_quantity'] > 0) { ?>
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
                                              @if( isset($product['new_arrival']) && $product['new_arrival'] )
                                              <tr>
                                                <td>&nbsp;<strong>Listing Link : </strong></td>
                                                <td colspan="3"><input type="text" name="product_listing_link[{{ $product['model'] }}]" value="{{ $product['listing_link'] }}" placeholder="Enter product detials link" size="120"></td>
                                              </tr>
                                            @endif
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
                                                <div class="col-xs-6 col-sm-4">
                                                    <button type="submit" name="submit" value="cancel" formnovalidate class="btn btn-danger active form-control submit-cancel-order">Cancel</button>
                                                </div>
                                                <div class="col-xs-6 col-sm-4">
                                                    <button type="submit" name="submit" value="approve" class="btn btn-success active form-control">Approve</button>
                                                </div>
                                            </div>
                                            <div id="history<?php echo $order['order_id'] ?>" class="history-panel  p-2 mb-2">
                                            <?php foreach ($order['order_histories'] as $history) { ?>
                                                <div class="text-black">
                                                    <label><strong><?php echo $history['name'] ?>: </strong></label>
                                                    <i><?php echo $history['comment'] ?></i>
                                                    <i style="float: right;"><?php echo date('Y-m-d', strtotime($history['created_at'])); ?></i>
                                                </div>
                                            <?php } ?>
                                                <div class="row approval-comment" style="-webkit-display:-webkit-flex;-webkit-flex-wrap:wrap;-ms-display:-ms-flexbox;-ms-flex-wrap:wrap;display:flex;flex-wrap:wrap;flex-direction:row;">
                                                    <div class="col-sm-10 text-black">
                                                        <label class="control-label"><strong>Admin Reply: </strong></label>
                                                        <textarea name="instruction" class="form-control" rows="3" comment-from="Admin" data-order-id="<?php echo $order['order_id'] ?>"></textarea>
                                                        <div class="error-message text-danger"></div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button type="button" class="btn btn-block btn-success active submit-comment" style="position: absolute;left: 0;bottom: 0;width: 85%;">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 total_column">
                                            <div class="row">
                                                <div class="col-xs-7 col-sm-6 text-black">
                                                    <?php foreach ($order['order_totals'] as $key => $value) { ?>
                                                    <label><b><?php echo $value['code'] ?></b></label>
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

