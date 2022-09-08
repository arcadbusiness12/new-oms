@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    @include('purchaseManagement.orderTabs')
                    
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
                            New Orders
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                           <div id="status_changed_msg" style="display: none"></div>
                           <?php if($orders['data']) {  ?>
                            <?php foreach ($orders['data'] as $order) { ?>
                            <div class="card order_list mb-4">
                                <form action="<?php echo route('update.awaiting.action.order') ?>" method="post">
                                    {{csrf_field()}}
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                    <div class="row top_row">
                                        <div class="col-sm-4 col-grid text-black mb-4 mt-2"><b>Order Number: #<?php echo $order['order_id'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            
                                                <a href="{{route('edit.purchase.orders', $order['order_id'])}}" class="btn btn-info"><i class="icon icon-pencil"></i></a>
                                            
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
                                    <?php foreach ($order['order_products'] as $product) { 
                                        
                                        ?>
                                    <div class="product_list_row">
                                        <div class="row product_row">
                                            <div class="col-xs-4 col-sm-2">
                                                <img width="100" src="<?php echo $product['image'] ?>" />
                                            </div>
                                            <div class="<?php echo ((session('role') == 'ADMIN' || session('role') == 'STAFF')) ? 'col-xs-6 col-sm-8' : 'col-xs-8 col-sm-10' ?>">
                                                <strong><?php echo $product['name'] ?></strong><br>
                                                <i><?php echo $product['model'] ?></i>
                                            </div>
                                            <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF')) { ?>
                                            <div class="col-xs-2 col-sm-2">
                                                <button type="button" class="btn btn-default form-control btn-collapse collapse-product-option" data-toggle="collapse" data-target="#product-option<?php echo $order['order_id'] . $product['product_id'] ?>" aria-expanded="false" aria-controls="collapseExample">
                                                    Details
                                                </button>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF')) { ?> id="product-option<?php echo $order['order_id'] . $product['product_id'] ?>" <?php } ?> class="<?php echo ((session('role') == 'ADMIN' || session('role') == 'STAFF')) ? 'options_row table-responsive collapse' : 'options_row table-responsive' ?>">
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
                                                        <label class="control-label"><strong> <?php echo $option['name'] ?> </strong></label>
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
                                                    <?php if((session('role') != 'ADMIN' && session('role') != 'STAFF')) { ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label"><strong> Order Quantity </strong></label>
                                                        <?php } ?>
                                                        <div>
                                                            <input type="text" pattern="^[0-9][0-9]*$" title="Enter greater than or equal to 0" name="quantity[<?php echo $quantity['order_product_quantity_id'] ?>][order_quantity]" <?php if($order['status'] == 'update') { ?> value="<?php echo $quantity['order_quantity'] ?>" <?php } ?> class="form-control order_quantity" required>
                                                            <input type="hidden" name="quantity[<?php echo $quantity['order_product_quantity_id'] ?>][old_order_quantity]" value="<?php echo $quantity['quantity'] ?>" />
                                                        </div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label"><strong> Price </strong></label>
                                                        <?php } ?>
                                                        <div><input type="text" pattern="^(\d*\.)?\d+$" title="Enter valid price" name="quantity[<?php echo $quantity['order_product_quantity_id'] ?>][price]" <?php if($order['status'] == 'update') { ?> value="<?php echo $quantity['price'] ?>" <?php } ?> class="form-control price" required/></div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label"><strong> Sum </strong></label>
                                                        <?php } ?>
                                                        <div><input type="text" name="quantity[<?php echo $quantity['order_product_quantity_id'] ?>][total]" <?php if($order['status'] == 'update') { ?> value="<?php echo $quantity['total'] ?>" <?php } ?> class="form-control sum" readonly/></div>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                            </table>
                                            @if( isset($product['new_arrival']) && $product['new_arrival'] )
                                              <tr>
                                                <td>&nbsp;<strong>Listing Link : </strong></td>
                                                <td colspan="3"><input type="text" name="product_listing_link[{{ $product['model'] }}]" placeholder="Enter product detials link" size="120" value="{{ $product['listing_link'] }}" required></td>
                                              </tr>
                                            @endif
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php if(session('role') != 'ADMIN' && session('role') != 'STAFF') { ?>
                                    <div class="row instruction_row ">
                                        <div class="col-xs-12 col-sm-8">
                                            <div class="supplier_link">
                                                <input type="text" name="supplier_link" <?php if($order['status'] == 'update') { ?> value="<?php echo $order['link'] ?>" <?php } ?> class="form-control" placeholder="Supplier/Purchase Link" required/>
                                            </div>
                                            <div class="row button-row mt-4 mb-4">
                                                <div class="col-xs-12 col-sm-4">
                                                    <button type="button" class="btn btn-default form-control collapse-comment" data-toggle="collapse" data-target="#history<?php echo $order['order_id'] ?>" aria-expanded="true" aria-controls="collapseExample">Comment</button>
                                                </div>
                                                <div class="col-xs-6 col-sm-4">
                                                    <?php if($order['cancelled_status'] && $order['cancelled_status']['status'] == 0){ ?>
                                                    <button type="button" class="btn btn-danger form-control disabled" disabled>Requested</button>
                                                    <?php } else { ?>
                                                    <button type="button" data-order-id="<?php echo $order['order_id'] ?>" data-supplier-id="<?php echo $order['order_supplier']['user_id'] ?>" class="btn btn-danger form-control btn-cancel-awaiting-order">Cancel</button>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-6 col-sm-4">
                                                    <?php if($order['status'] == 'insert') { ?>
                                                    <button type="submit" name="submit" value="update_awaiting_action" class="btn btn-primary form-control">Submit</button>
                                                    <?php } else { ?>
                                                    <button type="submit" name="submit" value="update_quantity_price" class="btn btn-info form-control">Update</button>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div id="history<?php echo $order['order_id'] ?>" class="history-panel collapse  p-2 mb-2">
                                            <?php foreach ($order['order_histories'] as $history) { ?>
                                                <div class="text-black">
                                                    <label><strong> <?php echo $history['name'] ?>: </strong></label>
                                                    <i><?php echo $history['comment'] ?></i>
                                                    <i style="float: right;"><?php echo date('Y-m-d', strtotime($history['created_at'])); ?></i>
                                                </div>
                                            <?php } ?>
                                                <div class="row approval-comment text-black" style="-webkit-display:-webkit-flex;-webkit-flex-wrap:wrap;-ms-display:-ms-flexbox;-ms-flex-wrap:wrap;display:flex;flex-wrap:wrap;flex-direction:row;">
                                                    <div class="col-sm-10 col-grid text-black">
                                                        <label class="control-label"><strong> Supplier Reply: </strong></label>
                                                        <textarea name="instruction" class="form-control" rows="3" comment-from="Supplier" data-order-id="<?php echo $order['order_id'] ?>"></textarea>
                                                        <div class="error-message text-danger"></div>
                                                    </div>
                                                    <div class="col-sm-2 col-grid text-black">
                                                        <button type="button" class="btn btn-block btn-success submit-comment" data-action="{{route('add.approval.comment')}}" style="left: 0;bottom: 0;margin-top:75px;">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 total_column">
                                            <div class="row">
                                                <div class="col-xs-7 col-sm-6 text-black">
                                                    <label><b> Sub Total </b></label>
                                                    <label><b> Local Express Cost </b></label>
                                                    <label><b> Total </b></label>
                                                </div>
                                                <div class="col-xs-5 col-sm-6">
                                                    <input type="86text" name="totals[Sub Total][sub_total]" <?php if($order['status'] == 'update') { ?> value="<?php echo @$order['order_totals'][0]['value'] ?>" <?php } ?> class="form-control sub_total" readonly/>
                                                    <input type="text" pattern="^(\d*\.)?\d+$" title="Enter valid price" name="totals[Local Express Cost][local_express_cost]" <?php if($order['status'] == 'update') { ?> value="<?php echo @$order['order_totals'][1]['value'] ?>" <?php } ?> step="any" class="form-control local_express_cost" required/>
                                                    <input type="text" name="totals[Total][total]" <?php if($order['status'] == 'update') { ?> value="<?php echo @$order['order_totals'][2]['value'] ?>" <?php } ?> class="form-control total" readonly/>
                                                    <input type="hidden" name="total" class="form-control total" readonly/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } else { ?>
                                    <div class="row instruction_row mt-4">
                                        <div class="col-xs-6 col-sm-6 ">
                                            <div class="row button-row">
                                                <div class="col-xs-12 col-sm-4">
                                                    <button type="button" class="btn btn-default form-control collapse-comment" data-toggle="collapse" data-target="#history<?php echo $order['order_id'] ?>" aria-expanded="true" aria-controls="collapseExample">Comment</button>
                                                </div>
                                                <div class="col-sm-8"></div>
                                            </div>
                                        </div>
                                        
                                        <?php if($order['cancelled_status'] && $order['cancelled_status']['status'] == 0){ 
                                            $class = 'col-xs-6 col-sm-8'; ?>
                                        <div class="col-xs-6 col-sm-4" style="padding-bottom: 10px;">
                                            <button type="button" class="btn btn-default" data-toggle="modal" href='#modal-oder-comment<?php echo $order['order_id'] ?>'>Comment</button>
                                            <button type="button" name="update_request" class="btn btn-success btn-accept" value="accept" data-order-id="<?php echo $order['order_id'] ?>" data-action="{{ route('awaiting.action.update.request') }}"><b>Accept</b></button>
                                            <button type="button" name="update_request" class="btn btn-danger btn-reject" value="reject" data-order-id="<?php echo $order['order_id'] ?>" data-action="{{ route('awaiting.action.update.request') }}"><b>Reject</b></button>

                                            <label class="btn-block text-danger text-center" style="font-size: 14px;"><strong> Supplier Cancelled Order </strong></label>
                                        </div>
                                        <div class="modal fade" id="modal-oder-comment<?php echo $order['order_id'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Reason</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-black"><?php echo $order['cancelled_status']['reason'] ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php }else if(@$order['cancelled_status']['status'] == 2){ ?>
                                        <div class="col-xs-6 col-sm-2">
                                            <button type="button" class="btn btn-danger disabled" disabled>Request Rejected</button>
                                        </div>
                                        <div class="col-xs-6 col-sm-2">
                                            <button type="submit" name="submit" value="cancel" formnovalidate class="btn btn-danger form-control submit-cancel-order">Cancel</button>
                                        </div>
                                        <?php }else{ ?>
                                        <div class="col-xs-6 col-sm-2">
                                            <button type="submit" name="submit" value="cancel" formnovalidate class="btn btn-danger form-control submit-cancel-order">Cancel</button>
                                        </div>
                                        <?php } ?>
            
                                        <div class="col-xs-6 col-sm-2">
                                            <button type="submit" name="submit" value="delete-order" formnovalidate class="btn form-control btn-danger btn-delete-order" data-order-id="<?php echo $order['order_id'] ?>">Delete Orders</button>
                                        </div>
                                        <div class="col-xs-12  pt-4 pb-4">
                                            <div id="history<?php echo $order['order_id'] ?>" class="history-panel p-3 text-black collapse show">
                                            <?php foreach ($order['order_histories'] as $history) { ?>
                                                <div>
                                                    <label><strong> <?php echo $history['name'] ?>: </strong></label>
                                                    <i><?php echo $history['comment'] ?></i>
                                                    <i style="float: right;"><?php echo date('Y-m-d', strtotime($history['created_at'])); ?></i>
                                                </div>
                                            <?php } ?>
                                                <div class="row approval-comment text-black" style="-webkit-display:-webkit-flex;-webkit-flex-wrap:wrap;-ms-display:-ms-flexbox;-ms-flex-wrap:wrap;display:flex;flex-wrap:wrap;flex-direction:row;">
                                                    <div class="col-sm-10">
                                                        <label class="control-label"><strong> Admin Reply: </strong></label>
                                                        <textarea name="instruction" class="form-control" cols="5" rows="3" required=""></textarea>
                                                        <div class="error-message text-danger"></div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button type="submit" name="submit" value="save-comment" class="btn btn-block btn-success" style="position: absolute;left: 0;bottom: 0;width: 85%;">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
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

