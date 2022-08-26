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
                                        Showing {{($page->currentpage()-1) * $page->perpage()+1}} to {{(($page->currentpage()-1)*$page->perpage())+$page->count()}} of <strong>{{$page->total()}}</strong>
                                    </div>
                            </center></th>
                    
                               </tr>
                    
                             </thead>
                    
                             <tbody>
                              @php
                              $sum=0;
                              $index = 0;
                              $tab_bg_color = "#eee !important";
                              $tab_bor_color = "#eee";
                              @endphp
                              @if($products)
                    
                              @foreach($products as $key=>$product)
                              @php $index = $key; @endphp
                              <tr>
                                <td class="col-xs-12" colspan="5">
                                    <table class="table  " >
                                        <thead style="background-color: {{ $tab_bg_color }};">
                                            <tr style="background-color: #eee; font-weight:bold"> 
                                                <td  class="text-center"><b class="pull-left" style="float: left;">{{ $product->sku }}</b><b class="pull-right" style="float: right;">{{ $product->option_name }}</b></td>
                                                <td  class="text-center">Size</td>
                                                <td  class="text-center">Available Qty</td>
                                                <td  class="text-center">Onhold Qty</td>
                                                <td  class="text-center">Packed Qty</td>
                                                <td  class="text-center">Shipped Qty</td>
                                                <td  class="text-center">Delivered Qty</td>
                                                <td  class="text-center">Return Qty</td>
                                            </tr>
                                        </thead>
                                        @php
                                        $tot_available_quantity=0;
                                        $tot_onhold_quantity = 0;
                                        $tot_pack_quantity = 0;
                                        $tot_shipped_quantity = 0;
                                        $tot_delivered_quantity = 0;
                                        $tot_return_quantity = 0;
                                        @endphp
                                        @foreach ($product->ProductsSizes as $key=> $option)
                                        <tr>
                                            @if($key==0)
                                            <td rowspan="{{ count($product->ProductsSizes) }}" class="col-sm-2 text-center"><img src="{{ URL::asset('uploads/inventory_products/'.$product->image) }}" style="width: 100%; height: 140px;" class="img-responsive"></td>
                                            @endif
                                            <td class="text-center">{{isset($option->omsOptionDetails->value) ? $option->omsOptionDetails->value: '' }}</td>
                                            <td class="text-center"><label>{{  $option->available_quantity }}</label></td>
                                            <td class="text-center">{{  $option->onhold_quantity }}</td>
                                            <td class="text-center">{{  $option->pack_quantity }}</td>
                                            <td class="text-center">{{  $option->shipped_quantity }}</td>
                                            <td class="text-center">{{  $option->delivered_quantity }}</td>
                                            <td class="text-center">{{  $option->return_quantity }}</td>
                                        </tr>
                                        @php
                                        $tot_available_quantity += $option->available_quantity;
                                        $tot_onhold_quantity += $option->onhold_quantity;
                                        $tot_pack_quantity += $option->pack_quantity;
                                        $tot_shipped_quantity += $option->shipped_quantity;
                                        $tot_delivered_quantity += $option->delivered_quantity;
                                        $tot_return_quantity += $option->return_quantity;
                                        @endphp
                                        @endforeach
                                        <tr style="background-color:#f5f5f5;">
                                            <td></td>
                                            <td class="text-center"><b>Total:</b></td>
                                            <td class="text-center"><b>{{ $tot_available_quantity }}</b></td>
                                            <td class="text-center"><b>{{ $tot_onhold_quantity }}</b></td>
                                            <td class="text-center"><b>{{ $tot_pack_quantity }}</b></td>
                                            <td class="text-center"><b>{{ $tot_shipped_quantity }}</b></td>
                                            <td class="text-center"><b>{{ $tot_delivered_quantity }}</b></td>
                                            <td class="text-center"><b>{{ $tot_return_quantity }}</b></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                       @endforeach
                    
                       @endif
                    
                     </tbody>
                     
                    </table>
                    <?php echo $products->appends(@$old_input)->render(); ?> 
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

