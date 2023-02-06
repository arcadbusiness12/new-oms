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
                            Shipped orders
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
                                <form action="<?php echo route('update.deliver') ?>" method="post" onsubmit="$('#btn_deliever_submit').hide()">
                                    {{csrf_field()}}
                                    <input type="hidden" name="shipped_id" value="<?php echo $order['shipped_id'] ?>" />
                                    <input type="hidden" name="shipped_order_id" value="<?php echo $order['shipped_order_id'] ?>" />
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>" />
                                    <div class="row top_row">
                                        <div class="col-xs-3 col-sm-3 text-black mb-3"><b>Order Number: #<?php echo $order['shipped_id'] ?></b></div>
                                        <?php if($order['supplier']) { ?>
                                        <div class="col-xs-3 col-sm-3 text-center "><div class="badge badge-secondary font-weight-bold">Supplier : <?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?></div></div>
                                        <?php } ?>
                                        <div class="col-xs-3 col-sm-3 text-center"><div class="badge badge-secondary font-weight-bold">Shipped From : <?php echo ucfirst($order['shipped']) ?></div></div>
                                        <div class="col-xs-3 col-sm-3 text-right">
                                            <?php if($order['urgent']) { ?>
                                                <div class="badge badge-warning orange darken-1" style="font-size: 15px;">Urgent</div>
                                            <?php } ?>
                                            <?php if($order['ship_by_sea'] == 1) { ?>
                                                <div class="badge badge-success darken-1" style="font-size: 15px;">Ship By Sea</div>
                                            <?php } else { ?>
                                                <div class="badge badge-primary" style="font-size: 15px;">Ship By Air</div>
                                            <?php  } ?>
                                            <div>
                                                <div class="badge badge-secondary font-weight-bold"><?php echo date('Y-m-d', strtotime($order['created_at'])) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php foreach ($order['order_products'] as $product_key => $product) { ?>
                                    <div class="product_list_row">
                                        <div class="row product_row">
                                            <div class="col-xs-4 col-sm-2">
                                                <img width="100" src="<?php echo $product['image'] ?>" />
                                            </div>
                                            <div class="col-xs-6 col-sm-8">
                                                <strong><?php echo $product['name'] ?></strong><br>
                                                <i><?php echo $product['model'] ?></i>
                                            </div>
                                            <div class="col-xs-2 col-sm-2 text-black">
                                                <strong><?php echo $product['sku'] ?></strong><br>
                                            </div>
                                        </div>
                                        <div class="shipped_product_row table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <td class="col-xs-6">
                                                        <?php if(isset($product['options']) && isset($product['options']['static'])) { ?>
                                                        <label class="font-weight-bold" style="font-size: 14px;"><?php echo $product['options']['static']['name'] ?> - <?php echo $product['options']['static']['value'] ?></label>
                                                        <?php } else { ?>
                                                        <label>&nbsp;</label>
                                                        <?php } ?>
                                                        <div>
                                                        <?php if(isset($product['options'])) { ?>
            
                                                        <?php if(isset($product['options']['static']) && count($product['options']) == 1) {  ?>
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][model]" value="<?php echo $product['model'] ?>">
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][static][order_product_quantity_id]" value="<?php echo $product['options']['static']['order_product_quantity_id'] ?>">
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][static][product_option_id]" value="<?php echo $product['options']['static']['product_option_id'] ?>">
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][static][option_name]" value="<?php echo $product['options']['static']['name'] ?>">
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][static][product_option_value_id]" value="<?php echo $product['options']['static']['product_option_value_id'] ?>">
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][static][option_value]" value="<?php echo $product['options']['static']['value'] ?>">
                                                            <input type="text" value="<?php echo $product['options']['static']['name'] ?> - <?php echo $product['options']['static']['value'] ?> = <?php echo $product['options']['static']['quantity'] ?>" class="form-control" readonly/>
                                                        <?php }else{ ?>
                                                            <?php foreach ($product['options'] as $key => $option) { ?>
                                                                <?php if($key !== 'static') { ?>
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][model]" value="<?php echo $product['model'] ?>">
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][<?php echo $key ?>][order_product_quantity_id]" value="<?php echo $option['order_product_quantity_id'] ?>">
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][<?php echo $key ?>][product_option_id]" value="<?php echo $option['product_option_id'] ?>">
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][<?php echo $key ?>][option_name]" value="<?php echo $option['name'] ?>">
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][<?php echo $key ?>][product_option_value_id]" value="<?php echo $option['product_option_value_id'] ?>">
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][<?php echo $key ?>][option_value]" value="<?php echo $option['value'] ?>">
                                                                <input type="text" value="<?php echo $option['name'] ?> - <?php echo $option['value'] ?> = <?php echo $option['quantity'] ?>" class="form-control" readonly/>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php } ?>
            
                                                        <?php } else { ?>
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][model]" value="<?php echo $product['model'] ?>">
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][0][order_product_quantity_id]" value="<?php echo $product['order_product_quantity_id'] ?>">
                                                            <input type="text" value="<?php echo $product['quantity'] ?>" class="form-control" readonly/>
                                                        <?php } ?>
                                                        </div>
                                                    </td>
                                                    <td class="col-xs-6">
                                                        <label class="font-weight-bold" style="font-size: 14px;">Received Quantity</label>
                                                        <div class="single_option_row">
                                                        <?php if(isset($product['options'])) { ?>
            
                                                        <?php if(isset($product['options']['static']) && count($product['options']) == 1) {  ?>
                                                            <input type="text" pattern="^[0-9][0-9]*$" title="Enter greater than or equal to 0" max="<?php echo $product['options']['static']['quantity'] ?>" name="product[<?php echo $product['product_id'] ?>][options][static][received_quantity]" class="form-control" required />
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][static][old_received_quantity]" value="<?php echo $product['options']['static']['quantity'] ?>"/>
                                                        <?php }else{ ?>
                                                            <?php foreach ($product['options'] as $key => $value) { ?>
                                                                <?php if($key !== 'static') { ?>
                                                                <input type="text" pattern="^[0-9][0-9]*$" title="Enter greater than or equal to 0" max="<?php echo $value['quantity'] ?>" name="product[<?php echo $product['product_id'] ?>][options][<?php echo $key ?>][received_quantity]" class="form-control" required />
                                                                <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][<?php echo $key ?>][old_received_quantity]" value="<?php echo $value['quantity'] ?>" />
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        
                                                        <?php } else { ?>
                                                            <input type="text" pattern="^[0-9][0-9]*$" title="Enter greater than or equal to 0" name="product[<?php echo $product['product_id'] ?>][options][0][received_quantity]" class="form-control" required />
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][options][0][old_received_quantity]" value="<?php echo $product['quantity'] ?>" />
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
                                            <div class="row button-row mb-2">
                                                <div class="col-xs-12 col-sm-4">
                                                    <button type="button" class="btn btn-default form-control active collapse-comment" data-target="history<?php echo $order['shipped_order_id'] ?>">Comment</button>
                                                </div>
                                                <!--<div class="col-xs-12 col-sm-2" align="center">
                                                    <div>
                                                        <input type="checkbox" name="dressfair" id="dressfair<?php echo $order['shipped_order_id'] ?>" class="chk-col-green">
                                                        <label for="dressfair<?php echo $order['shipped_order_id'] ?>">Dressfair</label>
                                                    </div>
                                                </div>-->
                                                <div class="col-xs-12 col-sm-6">
                                                    <button type="submit" name="submit" value="update_order_stock" id="btn_deliever_submit" class="btn btn-success active form-control">Update Order & Stock</button>
                                                </div>
                                            </div>
                                            <div id="history<?php echo $order['shipped_order_id'] ?>" class="history-panel p-2 mb-4 text-black">
                                            <?php foreach ($order['order_histories'] as $history) { ?>
                                                <div>
                                                    <label class="font-weight-bold"><?php echo $history['name'] ?>:</label>
                                                    <i><?php echo $history['comment'] ?></i>
                                                    <i style="float: right;"><?php echo date('Y-m-d', strtotime($history['created_at']))?></i>
                                                </div>
                                            <?php } ?>
                                                <div>
                                                    <label class="control-label font-weight-bold">Admin Reply:</label>
                                                    <textarea name="instruction" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 text-black">
                                            <div class="fancy-radio col-xs-6 col-sm-6 float-left">
                                                <input type="radio" name="print_label" id="big<?php echo $order['shipped_order_id'] ?>" value="big" oninvalid="$('.print_msg').text('Please Select any one label');" checked="checked" required />
                                                <label class="font-weight-bold" style="font-size:14px;" for="big<?php echo $order['shipped_order_id'] ?>">Big Label</label>
                                            </div>
                                            <div class="fancy-radio col-xs-6 col-sm-6 float-left">
                                                <input type="radio" name="print_label" id="small<?php echo $order['shipped_order_id'] ?>" value="small" oninvalid="$('.print_msg').text('Please Select any one label');" required />
                                                <label class="font-weight-bold" style="font-size:14px;" for="small<?php echo $order['shipped_order_id'] ?>">Small Label</label>
                                            </div>
                                            <div class="text-danger print_msg"></div><br>
                                            @if(session('user_group_id')==1)
                                              <div class="total_box mr-3 mt-4 p-2">
                                                  <h4><span class="font-weight-bold text-black"> Summary </span></h4>
                                                  <?php foreach ($order['order_totals'] as $total) { ?>
                                                      <div class="row">
                                                          <div class="col-xs-6 col-sm-6"><b> <?php echo $total['code'] ?></b></div>
                                                          <div class="col-xs-6 col-sm-6  font-weight-bold mt-2 text-right mt-2"><b><?php echo number_format($total['value'],2)  ?></b></div>
                                                      </div>
                                                  <?php } ?>
                                              </div>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php } ?>
                            <?php } else { ?>
                            <div class="alert alert-info">No Order Found!</div>
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


