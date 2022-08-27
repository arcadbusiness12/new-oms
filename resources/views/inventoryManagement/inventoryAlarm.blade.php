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
                            <div class="col-sm-6 card-head" >
                                Stock Reports
                            </div>
                            <div class="col-sm-6 btn-card-head text-right">
                                <form method="post" action="<?php echo route('order.out.stock.product') ?>">
                                    {{ csrf_field() }}
                                    <div class="order-form">

                                    </div>
                                   <button type="submit" id="order-btn" class="btn btn-success" disabled style="margin-right: 17px;">Order</button>
                                       
                                </form>
                            </div>
                          </div>
                         
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                           <table class="table" width="100%" style="border: 1px solid #2196f3">

                            <thead >
                   
                             <tr style="background-color: #2196f3;color:white">
                              <th scope="col"><center>Image</center></th>
                              <th scope="col"><center>Quantity</center></th>
                                <th scope="col"><center>Status</center></th>
                   
                              </tr>
                   
                            </thead>
                   
                            <tbody>
                             @php
                             $sum=0;
                             $tab_bg_color = "#eee";
                             $tab_bor_color = "#eee";
                             @endphp
                             @foreach($products as $key=>$product)
                             <tr id="tr_{{$product->id}}" style="border-top: 1px solid gray">
                   
                                <td class="col-sm-2" style="vertical-align: text-top;">
                                 <table class="table table-hover">
                                   <thead style="background-color: {{ $tab_bg_color }}">
                                       <th><center>
                                        {{-- <input class="form-check-input " type="checkbox"> --}}
                                       <input type="checkbox" name="select_products"  value="{{$product->product_id}}" class="select-product" style="width: 20px;
                                   height: 20px;">        
                                       </center></th>
                                     <th><center><label>{{$product->option_name}}</label></center></th>
                                     <th><center><label>{{$product->sku}}</label></center></th>
                                   </thead>
                                 </table> 
                                 
                                 <img src="{{URL::asset('uploads/inventory_products/'.$product->image)}}" class="img-responsive img-thumbnail" />
                                
                             </td>
                   
                               
                                <td class="column col-sm-6">
                                 <center>
                                   <table class="table table-hover">
                                     <thead style="background-color: {{ $tab_bg_color }};">
                                       <th><center><label>{{ ($product->omsOptions) ? $product->omsOptions->option_name : ""}}</label></center></th>
                                       <th><center><label>Available</label></center></th>
                                       <th><center><label>Minimum</label></center></th>
                                       <th><center><label>Onhold</label></center></th>
                                       <th><center><label>Packed</label></center></th>
                                     </thead>
                                     @php 
                                       $sum=0; 
                                       @$sum_available_quantity = 0;
                                       @$sum_onhold_quantity    = 0;
                                       @$sum_pack_quantity      = 0;
                                       @$sum_shipped_quantity   = 0;
                                       @$sum_delivered_quantity = 0;
                                     @endphp
                                     @foreach($product->ProductsSizes as $key=>$val)
                                     <?php 
                                     if($val->out_qty && $val->out_qty == 1) {$bColor = 'out-stock';} else {
                                         $bColor = '';
                                     }?>
                                     <tr class="{{$bColor}}">
                                       <td class="{{$bColor}}"><center>{{ $val->omsOptionDetails->value }}</center></td>
                                       <td class="{{$bColor}}" style="
                                       font-weight: bold;
                                       font-size: 14px;
                                   "><center>{{$val->available_quantity}}</center></td>
                                   <td class="{{$bColor}}" style="font-weight: bold;font-size: 14px;"><center>{{$val->minimum_quantity}}</center></td>
                                       <td class="{{$bColor}}"><center>{{$val->onhold_quantity}}</center></td>
                                       <td class="{{$bColor}}"><center>{{$val->pack_quantity}}</center></td>
                                     </tr>
                                     @php 
                                     @$sum_available_quantity += $val->available_quantity;
                                     @$sum_onhold_quantity    += $val->onhold_quantity;
                                     @$sum_pack_quantity      += $val->pack_quantity;
                                     @$sum_shipped_quantity   += $val->shipped_quantity;
                                     @$sum_delivered_quantity += $val->delivered_quantity;
                                     @$minimum_quantity += $val->minimum_quantity;
                                     @endphp
                                     @endforeach
                                     <tr style="background-color: {{ $tab_bg_color }};">
                                       <td><center><strong>Total</strong></center></td>
                                       <td><center><strong>{{ $sum_available_quantity }}</strong></center></td>
                                       <td><center><strong>{{ $minimum_quantity }}</strong></center></td>
                                       <td><center><strong>{{ $sum_onhold_quantity }}</strong></center></td>
                                       <td><center><strong>{{ $sum_pack_quantity }}</strong></center></td>
                                     </tr>
                                   </table>
                                 </center>
                               </td>
                           <td class="column col-sm-1 td-valign"><center>
                           @if( session('user_group_id') == 1 )
                           <select class="form-control" id="product_change_status" disabled onchange="changeProductStatusAjax(this.value,{{ $product->product_id }})">
                             <option value="0" {{ ($product->status==0) ? "selected" : "" }}>Disable</option>
                             <option value="1" {{ ($product->status==1) ? "selected" : "" }}>Enable</option>
                             @if( $sum == 0 )
                             <option value="2" {{ ($product->status==2) ? "selected" : "" }}>Finished</option>
                             @endif
                           </select>
                           @endif
                           </center></td>
                      </tr>
                      @endforeach
                   
                   
                    </tbody>
                   
                   </table>
                    <?php if($products) { ?>
                      <div class="text-right">
                        <?php echo $products->appends(@$old_input)->render(); ?>
                      </div>
                      <?php } ?>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        $('.select-product').on('change', function() {
            var html = '';
            $.each($("input[name='select_products']:checked"), function(){    
                html += '<input type="hidden" name="order_product[]" value="'+$(this).val()+'" />'
            });
            $('.order-form').html(html);
            if($("input[name='select_products']:checked").length > 0) {
            $('#order-btn').attr('disabled', false);
            }else {
            $('#order-btn').attr('disabled', true);
            }
        })
    </script>
@endpush

