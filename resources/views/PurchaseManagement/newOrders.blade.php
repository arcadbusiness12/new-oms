@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">
                            <form name="form_stock_level" id="form_stock_level" action="{{route('purchase.orders')}}" method="get">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="control-label">Order ID</label>
                                        <input type="text" name="order_id" id="order_id" class="form-control" value="<?php if(isset($old_input['order_id'])) { echo $old_input['order_id']; } ?>" autocomplete="off" placeholder="Order ID">
                                        
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">Product Title</label>
                                        <input type="text" name="product_title" id="product_title" class="form-control" value="<?php if(isset($old_input['product_title'])) { echo $old_input['product_title']; } ?>" autocomplete="off" placeholder="Product Title">
                                        
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">Product SKU</label>
                                        <input type="text" name="product_sku" id="product_sku" class="form-control" value="<?php if(isset($old_input['product_sku'])) { echo $old_input['product_sku']; } ?>" autocomplete="off" placeholder="Product SKU">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="status">Product Model</label>
                                        <input type="text" name="product_model" id="product_model" class="form-control" autocomplete="off" value="<?php if(isset($old_input['product_model'])) {echo $old_input['product_model']; } ?>" placeholder="Product Model">
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-6">
                                        <label class="control-label">Order Type</label>
                                        <select name="order_type" class="form-control">
                                            <option value="">Select Type</option>
                                            <option value="1" <?php if(isset($old_input['order_type']) && $old_input['order_type'] == '1') { ?> selected="selected" <?php } ?> >Urgent</option>
                                            <option value="0" <?php if(isset($old_input['order_type']) && $old_input['order_type'] == '0') { ?> selected="selected" <?php } ?> >Normal</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 text-right">
                                        <br>
                                        <button type="submit" id="search_filter" class="btn btn-primary">Search</button>
                                        <!-- <button type="button" onclick="$('#add_product_to_order').attr('action', '<?php echo URL::to('/inventory_manage/reportExport'); ?>').submit();" class="btn btn-danger">Export</button> -->
                                        <!-- <button type="button" id="subimt-place-order" class="btn btn-info">Order</button> -->
                                    </div>
                            </div>
                           
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="body-card">
                        <div class="panel-heading">
                            Stock Reports
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                           <div id="status_changed_msg" style="display: none"></div>
                           <?php if($orders['data']) {  ?>
                            <?php foreach ($orders['data'] as $order) { ?>
                            <div class="card order_list">
                                <form action="<?php echo URL::to('/purchase_manage/update_awaiting_action_order') ?>" method="post">
                                    {{csrf_field()}}
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                    <div class="row top_row">
                                        <div class="col-xs-4"><b>Order Number: #<?php echo $order['order_id'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            
                                                {{-- <a href="{{URL::to('/purchase_manage/purchase_orders/edit/'.$order['order_id'])}}" class="btn btn-info"><i class="fa fa-pencil"></i></a> --}}
                                            
                                        </b>
                                    </div>
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
                                                {{-- <img width="100" src="<?php echo $product['image'] ?>" /> --}}
                                            </div>
                                            <div class="<?php echo ((session('role') == 'ADMIN' || session('role') == 'STAFF')) ? 'col-xs-6 col-sm-8' : 'col-xs-8 col-sm-10' ?>">
                                                <strong><?php echo $product['name'] ?></strong><br>
                                                <i><?php echo $product['model'] ?></i>
                                            </div>
                                            <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF')) { ?>
                                            <div class="col-xs-2 col-sm-2">
                                                <button type="button" class="btn btn-default form-control btn-collapse collapse-product-option" data-target="product-option<?php echo $order['order_id'] . $product['product_id'] ?>">Details</button>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div <?php if((session('role') == 'ADMIN' || session('role') == 'STAFF')) { ?> id="product-option<?php echo $order['order_id'] . $product['product_id'] ?>" <?php } ?> class="<?php echo ((session('role') == 'ADMIN' || session('role') == 'STAFF')) ? 'options_row table-responsive collapsible-content' : 'options_row table-responsive' ?>">
                                            <table class="table">
                                            <?php $i = 0; foreach ($order['order_product_quantities'] as $quantity) { $i++; ?>
                                                <tr class="single_option_row">
                                                    <?php foreach ($quantity['product_options'] as $option) { ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label"><?php echo $option['name'] ?></label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control" value="<?php echo $option['value'] ?>" readonly></div>
                                                    </td>
                                                    <?php } ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Quantity</label>
                                                        <?php } ?>
                                                        <div><input type="text" class="form-control" value="<?php echo $quantity['quantity'] ?>" readonly></div>
                                                    </td>
                                                    <?php if((session('role') != 'ADMIN' && session('role') != 'STAFF')) { ?>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Order Quantity</label>
                                                        <?php } ?>
                                                        <div>
                                                            <input type="text" pattern="^[0-9][0-9]*$" title="Enter greater than or equal to 0" name="quantity[<?php echo $quantity['order_product_quantity_id'] ?>][order_quantity]" <?php if($order['status'] == 'update') { ?> value="<?php echo $quantity['order_quantity'] ?>" <?php } ?> class="form-control order_quantity" required>
                                                            <input type="hidden" name="quantity[<?php echo $quantity['order_product_quantity_id'] ?>][old_order_quantity]" value="<?php echo $quantity['quantity'] ?>" />
                                                        </div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Price</label>
                                                        <?php } ?>
                                                        <div><input type="text" pattern="^(\d*\.)?\d+$" title="Enter valid price" name="quantity[<?php echo $quantity['order_product_quantity_id'] ?>][price]" <?php if($order['status'] == 'update') { ?> value="<?php echo $quantity['price'] ?>" <?php } ?> class="form-control price" required/></div>
                                                    </td>
                                                    <td class="col-xs-2">
                                                        <?php if($i == 1) { ?>
                                                        <label class="control-label">Sum</label>
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
                                    <div class="row instruction_row">
                                        <div class="col-xs-12 col-sm-8">
                                            <div>
                                                <input type="text" name="supplier_link" <?php if($order['status'] == 'update') { ?> value="<?php echo $order['link'] ?>" <?php } ?> class="form-control" placeholder="Supplier/Purchase Link"/>
                                            </div>
                                            <div class="row button-row">
                                                <div class="col-xs-12 col-sm-4">
                                                    <button type="button" class="btn btn-default form-control collapse-comment" data-target="history<?php echo $order['order_id'] ?>">Comment</button>
                                                </div>
                                                <div class="col-xs-6 col-sm-4">
                                                    <?php if($order['cancelled_status'] && $order['cancelled_status']['status'] == 0){ ?>
                                                    <button type="button" class="btn btn-danger form-control disabled" disabled>Requested</button>
                                                    <?php } else { ?>
                                                    <button type="button" data-order-id="<?php echo $order['order_id'] ?>" data-supplier-id="<?php echo $order['supplier']['user_id'] ?>" class="btn btn-danger form-control btn-cancel-awaiting-order">Cancel</button>
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
                                            <div id="history<?php echo $order['order_id'] ?>" class="history-panel">
                                            <?php foreach ($order['history'] as $history) { ?>
                                                <div>
                                                    <label><?php echo $history['name'] ?>:</label>
                                                    <i><?php echo $history['comment'] ?></i>
                                                    <i style="float: right;"><?php echo $history['created_at']; ?></i>
                                                </div>
                                            <?php } ?>
                                                <div class="row approval-comment" style="-webkit-display:-webkit-flex;-webkit-flex-wrap:wrap;-ms-display:-ms-flexbox;-ms-flex-wrap:wrap;display:flex;flex-wrap:wrap;flex-direction:row;">
                                                    <div class="col-xs-10">
                                                        <label class="control-label">Supplier Reply:</label>
                                                        <textarea name="instruction" class="form-control" rows="3" comment-from="Supplier" data-order-id="<?php echo $order['order_id'] ?>"></textarea>
                                                        <div class="error-message text-danger"></div>
                                                    </div>
                                                    <div class="col-xs-2">
                                                        <button type="button" class="btn btn-block btn-success submit-comment" style="position: absolute;left: 0;bottom: 0;width: 85%;">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 total_column">
                                            <div class="row">
                                                <div class="col-xs-7 col-sm-6">
                                                    <label>Sub Total</label>
                                                    <label>Local Express Cost</label>
                                                    <label>Total</label>
                                                </div>
                                                <div class="col-xs-5 col-sm-6">
                                                    <input type="text" name="totals[Sub Total][sub_total]" <?php if($order['status'] == 'update') { ?> value="<?php echo @$order['totals'][0]['value'] ?>" <?php } ?> class="form-control sub_total" readonly/>
                                                    <input type="text" pattern="^(\d*\.)?\d+$" title="Enter valid price" name="totals[Local Express Cost][local_express_cost]" <?php if($order['status'] == 'update') { ?> value="<?php echo @$order['totals'][1]['value'] ?>" <?php } ?> step="any" class="form-control local_express_cost" required/>
                                                    <input type="text" name="totals[Total][total]" <?php if($order['status'] == 'update') { ?> value="<?php echo @$order['totals'][2]['value'] ?>" <?php } ?> class="form-control total" readonly/>
                                                    <input type="hidden" name="total" class="form-control total" readonly/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } else { ?>
                                    <div class="row instruction_row">
                                        <div class="col-xs-6 col-sm-4">
                                            <div class="row button-row">
                                                <div class="col-xs-12 col-sm-4">
                                                    <button type="button" class="btn btn-default form-control collapse-comment" data-target="history<?php echo $order['order_id'] ?>">Comment</button>
                                                </div>
                                                <div class="col-sm-8"></div>
                                            </div>
                                        </div>
                                        
                                        <?php if($order['cancelled_status'] && $order['cancelled_status']['status'] == 0){ 
                                            $class = 'col-xs-6 col-sm-8'; ?>
                                        <div class="col-xs-6 col-xs-4" style="padding-bottom: 10px;">
                                            <label class="btn-block text-danger">Supplier Cancelled Order</label>
                                            <button type="button" class="btn" data-toggle="modal" href='#modal-oder-comment<?php echo $order['order_id'] ?>'>Comment</button>
                                            <button type="button" name="update_request" class="btn btn-success btn-accept" value="accept" data-order-id="<?php echo $order['order_id'] ?>" data-action="{{ URL::to('/purchase_manage/awaiting_action/update_request') }}"><b>Accept</b></button>
                                            <button type="button" name="update_request" class="btn btn-danger btn-reject" value="reject" data-order-id="<?php echo $order['order_id'] ?>" data-action="{{ URL::to('/purchase_manage/awaiting_action/update_request') }}"><b>Reject</b></button>
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
                                        <?php }else if(@$order['cancelled_status']['status'] == 2){ ?>
                                        <div class="col-xs-6 col-sm-2">
                                            <button type="button" class="btn btn-danger disabled" disabled>Request Rejected</button>
                                        </div>
                                        <div class="col-xs-6 col-sm-2">
                                            <button type="submit" name="submit" value="cancel" formnovalidate class="btn btn-danger form-control submit-cancel-order">Cancel</button>
                                        </div>
                                        <?php }else{ ?>
                                        <div class="col-xs-6 col-sm-4">
                                            <button type="submit" name="submit" value="cancel" formnovalidate class="btn btn-danger form-control submit-cancel-order">Cancel</button>
                                        </div>
                                        <?php } ?>
            
                                        <div class="col-xs-6 col-sm-4">
                                            <button type="submit" name="submit" value="delete-order" formnovalidate class="btn form-control btn-danger btn-delete-order" data-order-id="<?php echo $order['order_id'] ?>">Delete Orders</button>
                                        </div>
                                        <div class="col-xs-12">
                                            <div id="history<?php echo $order['order_id'] ?>" class="history-panel">
                                            <?php foreach ($order['order_histories'] as $history) { ?>
                                                <div>
                                                    <label><?php echo $history['name'] ?>:</label>
                                                    <i><?php echo $history['comment'] ?></i>
                                                    <i style="float: right;"><?php echo $history['created_at']; ?></i>
                                                </div>
                                            <?php } ?>
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

