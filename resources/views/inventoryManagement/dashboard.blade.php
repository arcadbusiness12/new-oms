@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">
                            <form name="form_stock_level" id="form_stock_level" action="" method="get">
                                {{ csrf_field() }}
                                <div class="row">
                                <div class="col-md-2">
                                <input id="product_sku" name="product_sku" list="product_skus" value="{{ @$old_input['product_sku'] }}" type="text" placeholder="Search By SKU" class="form-control" autocomplete="off">
                                <datalist id="product_skus"></datalist>
                                </div>
                                <div class="col-md-2">
                                <input id="product_model" name="product_model" list="product_models" value="{{@$old_input['product_model']}}" type="text" placeholder="Search By Model" class="form-control" autocomplete="off">
                                <datalist id="product_models"></datalist>
                                </div>
                                <div class="col-md-2">
                                <input type="text" class="form-control" name="sku_range_from" value="{{ @$old_input['sku_range_from'] }}" placeholder="Range From">
                                </div>
                                <div class="col-md-2">
                                <input type="text" class="form-control" name="sku_range_to" value="{{ @$old_input['sku_range_to'] }}" placeholder="Range To">
                                </div>
                                <div class="col-md-2">
                                <select name="bystatus" id="bystatus" class="form-control">
                                    <option value="">Search By Status</option>
                                    <option value="0" {{ (@$old_input['bystatus']=='0') ? "selected" : "" }}>Disable</option>
                                    <option value="1" {{ (@$old_input['bystatus']=='1') ? "selected" : "" }}>Enable</option>
                                    <option value="2" {{ (@$old_input['bystatus']=='2') ? "selected" : "" }}>Finished</option>
                                </select>
                                </div>
                                <div class="col-md-2">
                                <select name="by_type" id="by_type" class="form-control">
                                    <option value="">Search By Status</option>
                                    {{-- @forelse($product_types as $key => $row)
                                    <option value="{{ $row->id }}" {{ (@$old_input['by_type']== $row->id ) ? "selected" : "" }} >{{ $row->name }}</option>
                                    @empty
                                    @endforelse --}}
                                </select>
                                </div>    
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-3 pull-right">
                                <input type="submit" name="search_inv_dashboard" class="btn btn-sm btn-primary" value="Search">
                                <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button>
                                @if( session('user_group_id') == 1 )
                                    {{-- <a href="{{ route('inventory_manage.inventorySyncReport') }}" class="btn btn-success">Sync Report</a> --}}
                                @endif
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
                            Inventory Dashboard
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
                               <th scope="col"><center>Image</center></th>
                               <th scope="col"><center>Quantity</center></th>
                                 <th scope="col"><center>Status</center></th>
                                 <th scope="col"><center>Action</center></th>
                    
                               </tr>
                    
                             </thead>
                    
                             <tbody>
                              @php
                              $sum=0;
                              $index = 0;
                              $tab_bg_color = "#eee !important";
                              $tab_bor_color = "#eee";
                              @endphp
                              @if($products->count())
                    
                              @foreach($products as $key=>$product)
                              @php $index = $key; @endphp
                              <tr id="tr_{{$product->id}}" style="border-top: 1px solid gray">
                    
                                 <td class="col-sm-2 img-td">
                                  <table class="table table-hover">
                                    <thead style="background-color: {{ $tab_bg_color }}">
                                      <th><center><label><strong>{{$product->option_name}}</strong></label></center></th>
                                      <th><center><label><strong>{{$product->sku}}</strong></label></center></th>
                                    </thead>
                                  </table> 
                                  
                                  <img src="{{URL::asset('uploads/inventory_products/'.$product->image)}}" class="img-responsive img-thumbnail" />
                              </td>
                                 <td class="column col-sm-6" style="vertical-align: unset;">
                                  <center>
                                    <table class="table table-hover">
                                      <thead style="background-color: {{ $tab_bg_color }};">
                                        <th><center><label><strong>{{ ($product->omsOptions) ? $product->omsOptions->option_name : ""}}</strong></label></center></th>
                                        <th><center><label><strong>Available</strong></label></center></th>
                                        <th><center><label><strong>Onhold</strong></label></center></th>
                                        <th><center><label><strong>Packed</strong></label></center></th>
                                        <th><center><label><strong>Rack</strong></label></center></th>
                                        <th><center><label><strong>Shelf</strong></label></center></th>
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
                                      <tr>
                                        <td><center>{{ $val->omsOptionDetails->value }}</center></td>
                                        <td style="
                                        font-weight: bold;
                                        font-size: 14px;
                                    "><center>{{$val->available_quantity}}</center></td>
                                        <td>
                                          <center>
                                          <a href="javascript:void(0)" onclick="getOrderDeailsStatus({{ $val->product_id }},{{ $val->option_id }},{{ $val->option_value_id }},0)" data-toggle="modal" data-target=".orderDetailsModal" class="onhold-td{{$val->product_option_id}}">{{$val->onhold_quantity}}</a>
                                          @if(session('role') == 'ADMIN')
                                          <a href="javascript:void(0)" class="onhold-edit-btn{{$val->product_option_id}}" onclick="editFormShow({{$val->product_option_id}}, {{ $val->product_id }},{{ $val->option_id }},{{ $val->option_value_id }}, {{$val->onhold_quantity}}, {{$index}})" style="padding-left:12px;"><i class="fa fa-edit" title="Edit"></i></a>
                                          <div class="form-div{{ $val->product_option_id }}"></div>
                                          @endif
                                        </center>
                                        
                                        </td>
                                        <td><center>{{$val->pack_quantity}}</center></td>
                                        <td><center>{{$val->rack}}</center></td>
                                        <td><center>{{$val->shelf}}</center></td>
                                      </tr>
                                      @php 
                                      @$sum_available_quantity += $val->available_quantity;
                                      @$sum_onhold_quantity    += $val->onhold_quantity;
                                      @$sum_pack_quantity      += $val->pack_quantity;
                                      @$sum_shipped_quantity   += $val->shipped_quantity;
                                      @$sum_delivered_quantity += $val->delivered_quantity;
                                      @endphp
                                      @endforeach
                                      <tr style="background-color: {{ $tab_bg_color }};">
                                        <td><center><strong>Total</strong></center></td>
                                        <td><center><strong>{{ $sum_available_quantity }}</strong></center></td>
                                        <td class="onhold-sum{{$index}}"><center><strong>{{ $sum_onhold_quantity }}</strong></center></td>
                                        <td><center><strong>{{ $sum_pack_quantity }}</strong></center></td>
                                        <td  colspan="2"><center><strong>{{ $product->row }}</strong></center></td>
                                      </tr>
                                    </table>
                                  </center>
                                </td>
                            <!--<td  class="td-valign"><center><b>{{$sum}}</b></center></td>
                            <td  class="td-valign">
                              <table class="table">
                                <thead>
                                  <th>Rack</th>
                                  <th>Shelf</th>
                                </thead>
                                @forelse($product->ProductsSizes as $key=>$loc)
                                <tr>
                                  <td>{{ $loc->rack }}</td>
                                  <td>{{  $loc->shelf }}</td>
                                </tr>
                                @empty
                                <small>location not found.</small>
                                @endforelse
                              </table>
                              <center><b>{{ $product->row }}</b></center>
                            </td>-->
                            <td class="column col-sm-1 td-valign"><center>
                            @if( session('user_group_id') == 1 )
                            <select class="form-control" id="product_change_status" onchange="changeProductStatusAjax(this.value,{{ $product->product_id }})">
                              <option value="0" {{ ($product->status==0) ? "selected" : "" }}>Disable</option>
                              <option value="1" {{ ($product->status==1) ? "selected" : "" }}>Enable</option>
                              @if( $sum == 0 )
                              <option value="2" {{ ($product->status==2) ? "selected" : "" }}>Finished</option>
                              @endif
                            </select>
                            @endif
                            </center></td>
                            <td class="column col-sm-1 td-valign"><center>
                            <?php
                             $OmsUserGroupModel = new \App\Models\Oms\OmsUserGroupModel;
                             $dashboard_option = $OmsUserGroupModel::inventory_management_dashboard_option_routes_for_lable();
                             ?>
                                <?php if(session('role') == 'ADMIN' || (array_key_exists('inventory_manage/dashboard/details', json_decode(session('access'), true)))) {?>
                              <a onclick="viewInventory('{{ $product->sku }}')" class="btn btn-sm" data-toggle="modal" data-target=".porduct_view_modal" title="view">
                                <i class="icon icon-eye icon-2x" aria-hidden="true" title="Full Details" data-toggle="tooltip"></i>
                            </a><br>
                                <?php } ?>
                                <?php if(session('role') == 'ADMIN' || (array_key_exists('inventory_manage/dashboard/location', json_decode(session('access'), true)))) { ?>
                              <a onclick="editLocation('{{ $product->product_id }}')" data-toggle="modal" data-target=".porduct_location_modal" title="Location" class="btn btn-sm" >
                                <i class="icon icon-map-marker icon-2x" aria-hidden="true" title="Location" data-toggle="tooltip"></i>
                            </a><br>
                              <?php } ?>
                              
                              <?php if(session('role') == 'ADMIN' || (array_key_exists('inventory_manage/dashboard/edit', json_decode(session('access'), true)))) {?>
                              <a onclick="editInventory('{{ $product->product_id }}')" data-toggle="modal" data-target=".edit_inventory_modal" title="Edit" class="btn  btn-sm">&nbsp;
                                <i class="icon icon-pencil-square-o icon-2x" aria-hidden="true" title="Edit" data-toggle="tooltip"></i>
                            </a><br>
                              <?php } ?>
                              <?php if(session('role') == 'ADMIN' || (array_key_exists('inventory_manage/dashboard/delete', json_decode(session('access'), true)))) {?>
                              <a href="{{url('/inventory_manage/dashboard/'.$product->product_id)}}" title="Delete" class="btn btn-sm" onclick="return confirm('Are you sure, you want to delete this product from Inventory.')">
                                <i class="icon icon-trash-o fa-2x" aria-hidden="true" title="Delete" data-toggle="tooltip"></i>
                            </a><br>
                              
                              <?php } ?>
                              <?php if(session('role') == 'ADMIN' || (array_key_exists('inventory_manage/dashboard/print', json_decode(session('access'), true)))) {?>
                              <a onclick="processPopupData({{ json_encode($product) }},{{  $product->product_id }})" class="btn btn-xs" data-toggle="modal" data-target="#printModal" >
                                <i class="icon icon-print fa-2x" aria-hidden="true" title="Print stock label" data-toggle="tooltip"></i>
                            </a><br>
                              <?php } ?>
                           </center>
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

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
<!-- product view modal start -->
<div class="modal fade porduct_view_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Product details</h5>
          <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button> -->
        </div>
        <div class="modal-body" id="porduct_view_content">
          ...
        </div>
        <div class="modal-footer">
                  <button type="button" class="btn btn-warning history-btn">History</button>
          <button type="button" class="btn btn-danger dismiss-btn" data-dismiss="modal">Close</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
        <div class="table-responsive" id="product_option_orders"></div>
        <div class="table-responsive" id="history-tbl" style="max-height: 300px;display: none;">
              <table class="table table-bordered table-hover">
                  <thead style="background-color: #eee;">
                      <tr>
                          <th class="text-center">User Name</th>
                          <th class="text-center">History</th>
                          <th class="text-center">Reason</th>
                          <th class="text-center">Date</th>
                      </tr>
                  </thead>
                  <tbody class="history">
                  <tr class="loaderr">
                    
                  </tr>
                  </tbody>
              </table>
              
          </div>
          <div class="msge text-center" style="display: none;">
              <p class="spinner-border text-muted" >No history found..</p>
           </div>
           <div class="history-load text-center" style="display: none;">
              <p class="spinner-border text-muted" >history loadding..</p>
           </div>
      </div>
    </div>
  </div>
  <!-- product view modal end -->
@endsection

@push('scripts')
<script>
    function changeProductStatusAjax(status,product_id){
        $.ajax({
            url: '{{route("change.product.status")}}',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: 'product_id='+product_id+'&status='+status,
            success: function (data) {
                if (data['status']) {
                    $(".toast-action").data('title', 'Action Done!');
                    $(".toast-action").data('type', 'success');
                    $(".toast-action").data('message', data['msg']);
                    $(".toast-action").trigger('click');
                } else {
                    $(".toast-action").data('title', 'Went Wrong!');
                    $(".toast-action").data('type', 'error');
                    $(".toast-action").data('message', data['msg']);
                    $(".toast-action").trigger('click');
                }
            }
        });
  }

  function viewInventory(sku){
      var url = "{{ route('view.inventory.product.details', ':sku') }}";
      url     = url.replace(':sku', sku);
      console.log(url);
    $.ajax({
      type: 'GET',
      url: url,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      cache: false,
      success: function (data) {
        $('#porduct_view_content').html(data);
      }
    });
  }
</script>
@endpush