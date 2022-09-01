@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">
                            <form name="form_stock_level" id="form_stock_level" action="{{route('stock.report')}}" method="get">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="control-label">Product SKU</label>
                                        <input type="text" name="product_sku" id="product_sku" list="product_skus" class="form-control" value="<?php if(isset($old_input['product_sku'])) { echo $old_input['product_sku']; } ?>" autocomplete="off" placeholder="Product SKU">
                                        <datalist id="product_skus"></datalist>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="status">Product Model</label>
                                        <input type="text" name="product_model" id="product_model" list="product_models" class="form-control" autocomplete="off" value="<?php if(isset($old_input['product_model'])) {echo $old_input['product_model']; } ?>" placeholder="Product Model">
                                            <datalist id="product_models"></datalist>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-6">
                                        <label class="control-label">From Date</label>
                                        <input type="text" name="from_date" id="date_added" class="date-time-picker form-control" autocomplete="off" placeholder="From Date"  data-options='{
                                            "timepicker":false,
                                            "format":"Y-m-d"
                                            }' value="<?php if(isset($old_input['from_date'])) { echo $old_input['from_date']; } ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">To Date</label>
                                        <input type="text" name="to_date" id="date_modified" class="date-time-picker form-control" data-options='{
                                            "timepicker":false,
                                            "format":"Y-m-d"
                                            }' autocomplete="off" placeholder="To Date" value="<?php if(isset($old_input['to_date'])) { echo $old_input['to_date']; } ?>">
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
                    <div class="card no-b">
                        <div class="panel-heading">
                            Stock Reports
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="border: 1px solid #3f51b5">
                    
                             <thead >
                    
                              <tr 
                              style="background-color: #3f51b5;color:white"
                              >
                               <th scope="col"><center></center></th>
                               <th scope="col"><center></center></th>
                                 <th scope="col"><center></center></th>
                                 <th scope="col"><center>
                                    <div class="pull-right" style="float: right;">
                                        
                                    </div>
                            </center></th>
                    
                               </tr>
                    
                             </thead>
                    
                             <tbody>
                <?php if($orders['data']) { ?>
                    <form name="form-delete-orders" id="form-delete-orders" method="post" action="<?php echo URL::to('/purchase_manage/purchase_orders') ?>">
                    {{ csrf_field() }}
                    <?php foreach ($orders['data'] as $order) { 
                        $checkbox = false;
                        if($order['order_status_id'] < 2){
                             $checkbox = true;
                        }
                        ?>
                    <div class="card order_list">
                        <div class="row top_row">
                            <?php if($checkbox) { ?>
                            <div class="col-xs-1">
                                <div>
                                  <label for="checkbox-<?php echo $order['order_id'] ?>"><input type="checkbox" name="delete_orders[]" id="checkbox-<?php echo $order['order_id'] ?>" value="<?php echo $order['order_id'] ?>" class="chk-col-green">
                                   
                                </div>
                                <?php if($order['order_status_id'] < 1) { ?>
                                <div>
                                    <a href="" class="btn btn-info"><i class="fa fa-pencil"></i></a>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            <div class="col-xs-2"><b>Order Number: #<?php echo $order['order_id'] ?></b>
                                <?php if($order['supplier']) { ?>
                                <div class="badge">
                                    <?php echo ucfirst($order['order_supplier']['firstname'] . " " . $order['order_supplier']['lastname']) ?>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="<?php echo $checkbox ? 'col-xs-7' : 'col-xs-8' ?> text-center">
                                {{-- @include('purchase_management.order_progress_bar') --}}
                            </div>
                            <div class="col-xs-2 text-right">
                                <?php if($order['urgent']) { ?>
                                    <div class="label label-warning">Urgent</div>
                                <?php } ?>
                                <div>
                                    <div class="badge"><?php echo $order['created_at'] ?></div>
                                </div>
                            </div>
                        </div>
                        <?php if(empty($order['shipped_orders'])) { ?>
                        <!-- <div class="row top_row">
                            <div class="col-xs-4"><b>Order Number: #<?php echo $order['order_id'] ?></b></div>
                            <div class="col-xs-4 text-center">
                                <?php if($order['order_status_id'] == 2 || $order['order_status_id'] == 0  ) {
                                    $index = $order['order_status_id'];
                                } else {
                                    $index = $order['order_status_id'] - 1;
                                } ?>
                                <?php if($order_statuses[$index]['name'] !== 'Cancelled') { ?>
                                <div class="label label-success"><?php echo $order_statuses[$index]['name'] ?></div>
                                <?php } ?>
                            </div>
                            <div class="col-xs-4 text-right">
                                <?php if($order['urgent']) { ?>
                                    <div class="label label-warning">Urgent</div>
                                <?php } ?>
                            </div>
                        </div> -->
                        <?php foreach ($order['order_products'] as $product) { ?>
                        <div class="product_list_row">
                            <div class="row product_row">
                                <div class="col-xs-4 col-sm-2">
                                    {{-- <img width="100" src="<?php echo $product['image'] ?>" /> --}}
                                </div>
                                <div class="col-xs-6 col-sm-8">
                                    <strong><?php echo $product['name'] ?></strong><br>
                                    <i><?php echo $product['model'] ?></i>
                                </div>
                                </p>
                                <div class="col-xs-2 col-sm-2">
                                    {{-- <button type="button" class="btn btn-default form-control btn-collapse collapse-product-option" data-target="product-option<?php echo $order['order_id'] . $product['product_id'] ?>">Details</button> --}}
                                    <button class="btn btn-default form-control collapse-product-option" type="button" data-toggle="collapse" data-target="#product-option<?php echo $order['order_id'] . $product['product_id'] ?>" aria-expanded="false" aria-controls="collapseExample">
                                        Details
                                      </button>
                                </div>
                            </div>
                            <div id="product-option<?php echo $order['order_id'] . $product['product_id'] ?>" class="options_row table-responsive collapse">
                                <table class="table">
                                    <?php $i = 0; 
                                    foreach ($order['order_product_quantities'] as $quantity) { $i++; ?>
                                        <tr class="single_option_row">
                                            <?php foreach ($quantity['product_options'] as $option) { ?>
                                            <td class="col-xs-2">
                                                <?php if($i == 1) { ?>
                                                <label class="control-label"><?php echo $option['name'] ?></label>
                                                <?php } ?>
                                                <div><input type="text" class="form-control" value="<?php echo $option['value'] ?>" readonly></div>
                                            </td>
                                            <?php } ?>
                                            <?php if($order['order_status_id'] !== $status_cancel) { ?>
                                            <td class="col-xs-2">
                                                <?php if($i == 1) { ?>
                                                <label class="control-label">Quantity</label>
                                                <?php } ?>
                                                <div><input type="text" class="form-control" value="<?php echo $quantity['quantity'] ?>" readonly></div>
                                            </td>
                                            <?php } ?>
                                            <td class="col-xs-2">
                                                <?php if($i == 1) { ?>
                                                <label class="control-label">Order Quantity</label>
                                                <?php } ?>
                                                <div><input type="text" class="form-control" value="<?php echo $quantity['order_quantity'] ?>" readonly></div>
                                            </td>
                                            <?php if($order['order_status_id'] === $status_cancel) { ?>
                                            <td class="col-xs-2">
                                                <?php if($i == 1) { ?>
                                                <label class="control-label">Remain Quantity</label>
                                                <?php } ?>
                                                <div><input type="text" class="form-control" value="<?php echo $quantity['remain_quantity'] ?>" readonly></div>
                                            </td>
                                            <?php } ?>
                                            <?php if($quantity['order_quantity'] > 0) { ?>
                                            <td class="col-xs-2">
                                                <?php if($i == 1) { ?>
                                                <label class="control-label">Price</label>
                                                <?php } ?>
                                                <div><input type="text" class="form-control price" value="<?php echo number_format($quantity['price'],2) ?>" readonly/></div>
                                            </td>
                                            <td class="col-xs-2">
                                                <?php if($i == 1) { ?>
                                                <label class="control-label">Sum</label>
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
                                    </table>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="row instruction_row">
                            <div class="col-xs-12 col-sm-8">
                                <?php if($order['link']) { ?>
                                <div>
                                    <input type="text" name="supplier_link" value="<?php echo $order['link'] ?>" class="form-control copy_to_clipboard" placeholder="Supplier Link" readonly/>
                                </div>
                                <?php } ?>
                                <div class="row button-row">
                                    <div class="col-xs-12 col-sm-4">
                                        <button type="button" class="btn btn-default form-control collapse-comment" data-target="history<?php echo $order['order_id'] ?>">Comment</button>
                                    </div>
                                    <div class="col-sm-8"></div>
                                </div>
                                <div id="history<?php echo $order['order_id'] ?>" class="history-panel">
                                <?php foreach ($order['order_histories'] as $history) { ?>
                                    <div>
                                        <label><?php echo $history['name'] ?>:</label>
                                        <i><?php echo $history['comment'] ?></i>
                                        <i style="float: right;"><?php echo $history['created_at']; ?></i>
                                    </div>
                                <?php } ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-4 total_column">
                                <div class="row">
                                    <div class="col-xs-7 col-sm-6">
                                        <?php foreach ($order['order_totals'] as $key => $value) { ?>
                                        <label><?php echo $value['code'] ?></label>
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
                        <?php } ?> 
                        <?php if($order['shipped_orders']) { ?>
                        <?php foreach ($order['shipped_orders'] as $shipped_order) { ?>
                        <div class="card order_list">
                            <div class="row top_row collapse-product-option" data-target="product-<?php echo $shipped_order['shipped_id'] ?>">
                                <div class="col-xs-4"><b>Order Number: #<?php echo $shipped_order['shipped_id'] ?></b></div>
                                <div class="col-xs-4 text-center">
                                    <?php if($shipped_order['status'] == 5){ ?>
                                    <div class="label label-danger">
                                        <?php echo $shipped_order_statuses[$shipped_order['status']]; ?>
                                    </div>
                                    <?php }else{ ?>
                                    <div class="label label-success">
                                        <?php echo $shipped_order_statuses[$shipped_order['status']]; ?>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="col-xs-4 text-right">
                                    <?php if($shipped_order['status'] == 2){ ?>
                                    <div class="badge">Shipped To: <?php echo ucfirst($shipped_order['shipped']) ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div id="product-<?php echo $shipped_order['shipped_id'] ?>" class="collapsible-content">
                                <?php foreach ($shipped_order['order_products'] as $shipped_product) { 
                                  ?>
                                <div class="product_list_row">
                                    <div class="row product_row">
                                        <div class="col-xs-4 col-sm-2">
                                            {{-- <img width="100" src="<?php echo $shipped_product['image'] ?>" /> --}}
                                        </div>
                                        <div class="col-xs-6 col-sm-8">
                                            <strong><?php echo $shipped_product['name'] ?></strong><br>
                                            <i><?php echo $shipped_product['model'] ?></i>
                                        </div>
                                        <div class="col-xs-2 col-sm-2">
                                            <button type="button" class="btn btn-default form-control btn-collapse collapse-product-option" data-target="product-option<?php echo $shipped_order['shipped_id'] . $shipped_product['product_id'] ?>">Details</button>
                                        </div>
                                    </div>
                                    <div id="product-option<?php echo $shipped_order['shipped_id'] . $shipped_product['product_id'] ?>" class="options_row table-responsive collapsible-content">
                                        <table class="table">
                                        <?php $i = 0; 
                                        foreach ($shipped_order['order_product_quantities'] as $shipped_quantity) { 
                                                if( $shipped_order['status'] == 5 && $shipped_quantity['quantity'] < 1 ) continue;
                                          $i++; ?>
                                            <tr class="single_option_row">
                                                <?php foreach ($shipped_quantity['product_options'] as $shipped_option) { ?>
                                                <td class="col-xs-1">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label"><?php echo $shipped_option['name'] ?></label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control" value="<?php echo $shipped_option['value'] ?>" readonly></div>
                                                </td>
                                                <?php } ?>
                                                {{-- new work start================== --}}
                                                  <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Order Quantity</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control" value="<?php echo $shipped_quantity['quantity'] ?>" size="5" readonly></div>
                                                  </td>
                                                  <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Confirm Quantity</label>
                                                    <?php } ?>
                                                    {{-- <div><input type="text" class="form-control" value="<?php echo $shipped_quantity['confirm_qty'] ?>" size="5" readonly></div> --}}
                                                  </td>
                                                {{-- new work end================== --}}
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Shipped Quantity</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control received_quantity" value="<?php echo $shipped_quantity['quantity'] ?>" readonly></div>
                                                </td>
                                                <td class="col-xs-2">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Received Quantity</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control received_quantity" value="<?php echo $shipped_quantity['received_quantity'] ?>" readonly></div>
                                                </td>
                                                <td class="col-xs-1">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Price</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control price" value="<?php echo number_format($shipped_quantity['price'],2) ?>" readonly/></div>
                                                </td>
                                                <td class="col-xs-1">
                                                    <?php if($i == 1) { ?>
                                                    <label class="control-label">Sum</label>
                                                    <?php } ?>
                                                    <div><input type="text" class="form-control sum" value="<?php echo number_format($shipped_quantity['total'],2) ?>" readonly/></div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </table>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="row instruction_row">
                                    <div class="col-xs-12 col-sm-8">
                                        <?php if($order['link']) { ?>
                                        <div>
                                            <input type="text" name="supplier_link" value="<?php echo $order['link'] ?>" class="form-control copy_to_clipboard" placeholder="Supplier Link" readonly/>
                                        </div>
                                        <?php } ?>
                                        <div class="row button-row">
                                            <div class="col-xs-12 col-sm-4">
                                                <button type="button" class="btn btn-default form-control collapse-comment" data-target="history<?php echo $order['order_id'] ?><?php echo $shipped_order['shipped_id'] ?>">Comment</button>
                                            </div>
                                            <div class="col-sm-8"></div>
                                        </div>
                                        <div id="history<?php echo $order['order_id'] ?><?php echo $shipped_order['shipped_id'] ?>" class="history-panel">
                                        <?php foreach ($order['order_histories'] as $history) { ?>
                                            <div>
                                                <label><?php echo $history['name'] ?>:</label>
                                                <i><?php echo $history['comment'] ?></i>
                                                <i style="float: right;"><?php echo $history['created_at']; ?></i>
                                            </div>
                                        <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 total_column">
                                        <div class="row">
                                            <div class="col-xs-7 col-sm-6">
                                                <?php foreach ($shipped_order['order_totals'] as $key => $total_value) { ?>
                                                <label><?php echo $total_value['code'] ?></label>
                                                <?php } ?>
                                            </div>
                                            <div class="col-xs-5 col-sm-6">
                                                <?php foreach ($shipped_order['order_totals'] as $key => $total_value) { ?>
                                                <input type="text" value="<?php echo number_format($total_value['value'],2) ?>" class="form-control" readonly/>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } } ?>
                        <!-  to be shipped condition start-------------------------------------------------------------  -->
                          @if( $order['order_status_id'] == 4 )
                          <div class="card order_list">
                            <div class="row top_row collapse-product-option" data-target="product-{{$order['order_id']}}">
                                <div class="col-xs-4"><b>Order Number: #<?php echo $order['order_id'] ?></b></div>
                                <div class="col-xs-4 text-center">
                                    <div class="label label-warning">To Be Shipped</div>
                                </div>
                                <div class="col-xs-4 text-right">
                                </div>
                            </div>
                            <div id="product-{{ $order['order_id']}}" class="collapsible-content">
                              <?php if($order['total'])
                              { 
                                  foreach ($order['order_products'] as $product) { ?>
                                <div class="product_list_row">
                                    <div class="row product_row">
                                        <div class="col-xs-4 col-sm-2">
                                            {{-- <img width="100" src="<?php echo $product['image'] ?>" /> --}}
                                        </div>
                                        <div class="col-xs-8 col-sm-10">
                                            <strong><?php echo $product['name'] ?></strong><br>
                                            <i><?php echo $product['model'] ?></i>
                                            <div class="options-label">
                                                <?php $total_quantity = 0; 
                                                foreach ($order['order_product_quantities'] as $quantity) { 
                                                    $quant = $quantity['order_quantity'] - $quantity['shipped_quantity'];
                                                    ?>
                                                    <?php if( $quant > 0 ) 
                                                    { $total_quantity += $quant; ?>
                                                        <div class="box-label">
                                                            <?php echo @$product['products_sizes'][0]['value']?> - <?php echo @$product['products_sizes'][1]['value'] ?> = <?php echo $quant; ?>
                                                        </div>
                                                    <?php }  ?>
                                                <?php } ?>
                                                <div class="box-label">
                                                    T. Units = <?php echo @$total_quantity ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              <?php } } ?>
                            </div>
                          </div>
                          @endif 
                        <!-  to be shipped condition End---------------------------------------------------------------  -->
                    </div>
                    <?php } ?>
                    </form>
                    <?php } else { ?>
                    <div class="alert alert-info">No Orders Found!</div>
                    <?php } ?>
                    
                     </tbody>
                     
                    </table>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

