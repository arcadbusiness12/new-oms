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
                              <input type="hidden" name="page" class="group_type_page_no">
                              {{ csrf_field() }}
                              <div class="row">
                              <div class="col-md-2">
                              <input name="search_for" type="hidden" class="form-control" value="groupPage">
                              <input name="g_name" type="text" class="form-control" value="{{@$old_input['g_name']}}" placeholder="Search by group">
                              </div>
                              <div class="col-md-2">
                              <select class="form-control" name="type" id="product_change_status">
                                    <option value="">Search By Type</option>
                                    @foreach($types_for_organic as $type)
                                    <option value="{{$type->id}}" <?php if(@$old_input['type'] == $type->id ){ echo 'selected';} else { echo '';} ?>>{{$type->name}}</option>
                                    @endforeach
                                  </select>
                              </div>
                              <div class="col-md-2">
                              <select class="form-control" name="cate" id="product_change_status" id="main_cate_val" onchange="getSubCategories(this.value)">
                                    <option value="">By Category</option>
                                    @foreach($main_categories as $cate)
                                    <option value="{{$cate->id}}" <?php if(@$old_input['cate'] == $cate->id ){ echo 'selected';} else { echo '';} ?>>{{$cate->name}}</option>
                                    @endforeach
                                  </select>
                              </div>
              
                              <div class="col-md-2" id="sub_catess">
                              <select name="sub_cate" class="form-control">
                                <option value="">Sub category</option>
                              </select>
                              </div>
              
                              <div class="col-md-2" id="sub_catess">
                                <select name="product_status" class="form-control">
                                  <option value="yes" <?php if(@$old_input['product_status'] == 'yes' ){ echo 'selected';} else { echo '';} ?> >Enable</option>
                                  <option value="no" <?php if(@$old_input['product_status'] == 'no' ){ echo 'selected';} else { echo '';} ?>>Disable</option>
                                </select>
                                </div>
              
                              <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i>Search</button>
                                <!-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> -->
                              </div> 
                              </div>
                              @if(session('user_group_id') == 1)
                                <div class="row">
                                  <div class="col-md-12 text-right" >
                                    {{-- <a href="{{route('generate.auto.group')}}" class="btn btn-success">Auto Generate</a> --}}
                                  </div>  
                                </div>
                              @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Products
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                          {{-- <div class="table-responsive"> --}}
                           <div id="status_changed_msg" style="display: none"></div>
                           
                            <table class="table" width="100%" style="border: 1px solid #3f51b5">

                                <thead >
        
                                <tr style="background-color: #3f51b5;color:white">
        
                                {{-- <th scope="col"><center></center></th> --}}
                                <!-- <th scope="col"><center>ID</center></th> -->
                                <th scope="col"><center>Group</center></th>
                                {{-- <th scope="col"><center>SKU / Color</center></th> --}}
                                <!-- <th scope="col"><center>Color</center></th>-->
                                <th scope="col"><center>Products</center></th>
                                    <th scope="col"><center>Category</center></th>
                                    <th scope="col"><center>Type</center></th>
                                    <!-- <th scope="col"><center>Action</center></th> -->
        
                                </tr>
        
                                </thead>
        
                                <tbody id="body-data">
                                @php
                                $sum=0;
                                $tab_bg_color = "#eee";
                                $tab_bor_color = "#eee";
                                $user_perm = json_decode(session('access'),true);
                                //echo "<pre>"; print_r($groupProducts->toArray()); die;
                                @endphp
                                @if(@$groupProducts)
        
                                @foreach(@$groupProducts as $key=>$product)
                                @if(count(@$product->products) > 0)
                                <tr id="tr_{{$product->id}}" style="border-bottom: 1px solid white;">
        
                                {{-- <td class="td-valign"><div> <input type="checkbox" class="sub_chk" data-id="{{$product->product_id}}"></div></td> --}}
                                <!--<td class="td-valign"><input type="text" size="8" value="{{ $product->product_id }}"></td>
                                    <td class="td-valign"><center><b>{{$product->sku}}<br>{{$product->option_name}}</b></center></td>-->
                                    
                                    <td class="col-sm-2 text-center">
                                        <table class="table table-hover group-title">
                                            <thead style="background-color: {{ $tab_bg_color }}">
                                            <th class="tab-th"><center><label><a href="javascript:void()">{{$product->name}}</a></label></center></th>
                                            
                                            </thead>
                                        
                                        </table> 
                                        <img src="{{URL::asset('uploads/inventory_products/'.$product['products'][0]->image)}}" class="img-responsive img-thumbnail group-pro" height="120"/> 
                                        <h5 class="type-name">{{@$product->producType['name']}} </h5>
                                        
                                    </td>
        
                                    <td class="column col-sm-6 td-valign" >
                                        <table class="" style="display: inline-block;">
                                        <thead style="background-color: {{ $tab_bg_color }};">
                                            <th><center><label>{{ ($product['products'][0]->omsOptions) ? $product['products'][0]->omsOptions->option_name : "Color"}}</label></center></th>
                                            
                                        </thead>
                                        @foreach($product['products'][0]->ProductsSizes as $key=>$val)
                                        <tr>
                                            <td><center>{{ $val->omsOptionDetails->value }}</center></td>
                                            
                                        </tr>
                                        @endforeach
                                        <tr  style="background-color: {{ $tab_bg_color }}">
                                        <td><center><strong>Total</strong></center></td>
                                        </tr>
                                        
                                        </table>
                                    @foreach($product['products'] as $key=>$productt)
                                    
                                        <table class="" style="display: inline-block;">
                                        <thead style="background-color: {{ $tab_bg_color }};">
                                            <th><center><label>{{$productt->sku}}</label></center></th>
                                        </thead>
                                        @php 
                                            $sum=0; 
                                            @$sum_available_quantity = 0;
                                        @endphp
                                        
                                        @foreach($productt->ProductsSizes as $key=>$val)
                                        <tr>
                                            <td style="
                                            font-size: 13px;
                                        "><center>{{$val->available_quantity}}</center></td>
                                            
                                        </tr>
                                        @php 
                                        @$sum_available_quantity += $val->available_quantity;
                                        @endphp
                                        @endforeach
                                        <tr style="background-color: {{ $tab_bg_color }}">
                                            <!-- <td><center><strong>Total</strong></center></td> -->
                                            <td><center><strong>{{ $sum_available_quantity }}</strong></center></td>
                                        </tr>
                                        </table>
                                    @endforeach
                                    </td>
                                    
                                <td class="column col-sm-2 td-valign" ><center>
                                <?php if(session('role') == 'ADMIN' || (array_key_exists('change/group/type', $user_perm ))) {
                                
                                ?>
                                {{-- <button type="button" class="btn btn-success landing_page_listing_modal" data-group-id="{{$product->id}}" data-sku="{{$product->name}}" style="margin-bottom: 60px;">Assign To Deals</button> --}}
                                @if(!$product->category_id)
                                <select class="form-control custom-select" id="product_change_status" onchange="addMainCategory(this.value,{{ $product->id }})">
                                
                                <option value="">Category</option>
                                @foreach($main_categories as $cate)
                                <option value="{{$cate->id}}" {{ ($product->category_id== $cate->id) ? "selected" : "" }}>{{$cate->name}}</option>
                                @endforeach
                            </select><br><br>
                            <div class="sub_categs" id="sub_categs{{$product->id}}">
                            </div>
                            @else
                            <select class="form-control custom-select" id="product_change_status" onchange="addSubCategory(this.value,{{ $product->id }})">
                                @if($product->category)
                                <option value="">Sub Category</option>
                                @foreach($product->category->subCategories as $cate)
                                <option value="{{$cate->id}}" {{ ($product->sub_category_id== $cate->id) ? "selected" : "" }}>{{$cate->name}}</option>
                                @endforeach
                                @else
                                <option value="">No Category</option>
                                @endif
                            </select>
                            @endif
                            <?php } ?>
                                <!-- <button type="button" onclick="productDetails({{$product->id}})" class="btn btn-info" class="btn btn-sm" data-toggle="modal" data-target=".porduct_view_modal" title="view">Promotion</button> -->
        
                            </center>
                            </td>
                            
                            <td class="column col-sm-2 td-valign"><center>
                                <?php $OmsUserGroupModel = new \App\Models\Oms\OmsUserGroupModel;
                                $promotion_options = $OmsUserGroupModel::oms_group_page_options_routes();
                                $color = ($product->size_chart == 1) ? 'green' : 'red';
                                ?>
                                
                                <?php if( session('user_group_id') == 1 || session('user_group_id') == 5 ) { ?>
                                <a href="javascript:void(0)" class="btn btn-sm btn-size-chart" data-group="{{ $product->name }}_{{ $product->id }}"><i class="icon-bar-chart icon-5x" aria-hidden="true" title="" data-toggle="tooltip" data-original-title="Size Chart" style="color: {{$color}};font-size: 22px;"></i></a><br>
                                <?php } ?>
                                
                                <?php if(session('role') == 'ADMIN' || (array_key_exists('prices/update-site-prices', $user_perm))) { ?>
                                <a href="javascript:void(0)" class="btn btn-sm btn-update-price" data-group="{{ $product->name }}"><i class="icon-dollar fa-2x" aria-hidden="true" title="" data-toggle="tooltip" data-original-title="Site Prices" style="font-size: 22px;"></i></a><br>
                                <?php } ?>
                                <?php if(session('role') == 'ADMIN' || (array_key_exists('prices/update-site-promotion-prices',  $user_perm ))) {?>
                                <a href="javascript:void(0)" class="btn btn-sm btn-update-promotion-prices" data-group="{{ $product->name }}"><i class="icon-percent fa-2x" aria-hidden="true" title="" data-toggle="tooltip" data-original-title="Website Promotion" style="font-size: 22px;"></i></a><br>                      
                                <?php } ?>
                                <?php if(session('role') == 'ADMIN' || array_key_exists('change/group/type', $user_perm) ) {?>
                                <select class="form-control custom-select" id="product_change_status" onchange="changeGroupType(this.value,{{ $product->id }})">
                                <option value="">Select Type</option>
                                @foreach($types_for_organic as $type)
                                <option value="{{$type->id}}" {{ ($product->product_type_id== $type->id) ? "selected" : "" }}>{{$type->name}}</option>
                                @endforeach
                            </select><br><br>
                            <?php } ?>
        
                            @if( session('user_group_id') == 1 )
                                <select class="form-control custom-select" id="product_change_status" onchange="changeProductStatusAjax(this.value,{{ $product->id }})">
                                <option value="0" {{ (@$product['products'][0]->status==0) ? "selected" : "" }}>Disable</option>
                                <option value="1" {{ (@$product['products'][0]->status==1) ? "selected" : "" }}>Enable</option>
                                <option value="2" {{ (@$product['products'][0]->status==2) ? "selected" : "" }}>Finished</option>
                                
                                </select>
                                @endif
                                <!-- <button type="button" onclick="productDetails({{$product->id}})" class="btn btn-info" class="btn btn-sm" data-toggle="modal" data-target=".porduct_view_modal" title="view">Promotion</button> -->
                            </center>
                            </td>
                        </tr>
                        <tr style="border-top: 1px solid white;border-bottom: 1px solid white;">
                            <td></td>
                            <td colspan="2">
                                
                            <b><span class="product-success msges_success{{$product->id}}" style="display: none;"></span></b>
                            </td>
                        </tr>
                            <tr>
                                <td colspan="2" style="b">
                                    @if( count($product['purchase_history']) && ( session('user_group_id') == 1 || session('user_group_id') == 8 ) )
                                    <table class="table table-bordered pull-right" style="width:57%">
                                        <tbody>
                                        @foreach ( $product['purchase_history'] as $key1 => $purchase_history )
                                            @if( session('user_group_id') == 1 || (session('user_group_id') != 1 &&  $purchase_history->current_status == "Shipped") )
                                            <tr class="more_purchase_history" >
                                                @if( session('user_group_id') == 1 )
                                                <td valign="middle"><center><a href="{{ url('purchase_manage/purchase_orders')}}?order_id={{ $purchase_history->order_id }}" target="_blank"><strong>{{ $purchase_history->order_id }}</strong></a></center></td>
                                                @endif
                                                <td valign="middle"><center>{{ str_replace(',',', ',$purchase_history->all_model) }}</center></td>
                                                <td valign="middle"><center><strong>{{ $purchase_history->current_status }}</strong></center></td>
                                            </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @endif
                                </td>
                                <td colspan="2">
                                @if( session('user_group_id') == 1 )
                                    <a href="{{ url('PurchaseManagement/purchase/orders')}}?product_sku={{ $product->name }}" target="_blank" class="btn btn-info btn-sm active">Purchase History</a>
                                @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
        
                        @endif
        
                        </tbody>
        
                        </table>

                        <?php echo $groupProducts->appends(@$old_input)->render(); ?>
                         {{-- </div> --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@include('productGroup.modals')
@endsection

@push('scripts')
<script>

$(document).on('click','.btn-size-chart',function(){
    $('#size_chart_popup_content').html("<h4><i>Loading...</i></h4>");
    var group_name = $(this).attr('data-group');
    $('#group_name').val( group_name );
    $('#sizeChartModalTitle').text(group_name.split('_')[0]);
    $('#size_chart_modal').modal('toggle');
    $.ajax({
      url: "{{url('/productgroup/get/product/size/chart')}}",
      type: "GET",
      data: {group_name:group_name},
      cache: false,
      success: function(result) {
        // if(result.status) {
          // console.log(group_name.split('_')[0]);
          $('#size_chart_popup_content').html(result);
        // }
        if(result.status == 'notconnect') {
          console.log(result);
          $('#size_chart_popup_content').html('<span class="text-error">'+result.mesge+'</span>');
        }
        
      },error: function(error) {
          console.log(error);
      }
    });
  });

  $(document).on('submit','#frm_update_size_chart',function(e){
    e.preventDefault();
    var $this = $(this);
    var form_data = $this.serialize();
    // console.log(form_data); return;
    $this.find(':submit').text("loading").prop("disabled",true);
    $.ajax({
      url: "{{url('/productgroup/update/product/size/chart')}}",
      type: "POST",
      data: form_data,
      cache: false,
      complete: function(){
        $this.find(':submit').text("SAVE").prop("disabled",false);
      },
      success: function(result) {
        $('.text-success').text('Size chart updated successfully');
        setTimeout(() => {
          $('#size_chart_modal').modal('toggle');
          $('.text-success').text('');
        }, 2500);
        
      },error: function(error) {
          
      }
    });
  });
  

  $(document).on('click','.btn-update-price',function(){
    $('#update_prices_popup_content').html("<h4><i>Loading...</i></h4>");
    var group_name = $(this).attr('data-group');
    $('#site_prices_group_name').val( group_name );
    $('#update_price_modal').modal('toggle');
    $.ajax({
      url: "{{url('/productgroup/update/site/prices')}}",
      type: "GET",
      data: {group_name:group_name},
      cache: false,
      success: function(result) {
        $('#update_prices_popup_content').html(result);
      },error: function(error) {
          console.log(error);
      }
    });
  });

   // promotion code start
   $(document).on('click','.btn-update-promotion-prices',function(){
    $('#update_promotion_prices_popup_content').html("<h4><i>Loading...</i></h4>");
    var group_name = $(this).attr('data-group');
    $('#promotion_group_name').val( group_name );
    $('#update_promoton_price_modal').modal('toggle');
    $('.date-time-picker').datepicker();
    $.ajax({
      url: "{{url('/productgroup/prices/update/site/promotion/prices')}}",
      type: "GET",
      data: {promotion_group_name:group_name},
      cache: false,
      success: function(result) {
        $('#update_promotion_prices_popup_content').html(result);
      },error: function(error) {
          console.log(error);
      }
    });
  });

  $(document).on('submit','#frm_update_site_prices',function(e){
    e.preventDefault();
    var $this = $(this);
    var form_data = $this.serialize();
    $this.find(':submit').text("loading").prop("disabled",true);
    $.ajax({
      url: "{{url('/productgroup/update/site/prices')}}",
      type: "POST",
      data: form_data,
      cache: false,
      complete: function(){
        $this.find(':submit').text("SAVE").prop("disabled",false);
      },
      success: function(result) {
        console.log(result);
        if(result.status) {
              $(".toast-action").data('title', 'Action Done!');
              $(".toast-action").data('type', 'success');
              $(".toast-action").data('message', 'Prices updated successfully.');
              $(".toast-action").trigger('click');
              $('.price-error').html('');
              $('.meta-title-error').html('');
              $('.name-error').html('');
          }
        $('#update_prices_popup_content').html(result);
        $('#update_price_modal').modal('toggle');
      },error: function(error) {
          
      }
    });
  });

  $(document).on('submit','#frm_update_site_promotion_prices',function(e){
    e.preventDefault();
    var $this = $(this);
    var form_data = $this.serialize();
    $this.find(':submit').text("loading").prop("disabled",true);
    $.ajax({
      url: "{{url('/productgroup/prices/update/site/promotion/prices')}}",
      type: "POST",
      data: form_data,
      cache: false,
      complete: function(){
        $this.find(':submit').text("SAVE").prop("disabled",false);
      },
      success: function(result) {
          if(result.status == 'updated') {
            $(".toast-action").data('title', 'Action Done!');
            $(".toast-action").data('type', 'success');
            $(".toast-action").data('message', result.meassage);
            $(".toast-action").trigger('click');
            $('.price-error').html('');
            $('.meta-title-error').html('');
            $('.name-error').html('');
          }
          if(result.status == 'updateError') {
              $('.promotion-text-error').text(result.meassage);
              setTimeout(() => {
                  $('.promotion-text-error').text('');
              }, 5000);
              return;
          }
        $('#update_promotion_prices_popup_content').html(result);
        $('#update_promoton_price_modal').modal('toggle');
      },error: function(error) {
          
      }
    });
  });

  function changeGroupType(type, product) {
  if(type && product) {
    $.ajax({
    url: "{{url('/productgroup/change/group/type')}}/"+type + '/' + product,
    type: "GET",
    cache: false,
    success: function(resp) {
      if(resp.status) {
        $('.msges_success'+product).text(resp.message);
        $('.msges_success'+product).css('display', 'block');
        // $('.msges_success').attr('tabindex', -1).focus();
        // $('html, body').animate({ scrollTop: $('#msges_success').offset().top }, 'slow');
        setTimeout(() => {
          $('.msges_success'+product).css('display', 'none');
        },5000);
      }
    }
  })
  }
  
}
function changeProductStatusAjax(status,product_id){
    // console.log(product_id); return;
    $.ajax({
      url: "{{url('/productgroup/product/group/change/product/status')}}",
      type: 'POST',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      data: 'group_id='+product_id+'&status='+status,
      success: function (data) {
        if (data['status']) {
          $('.msges_success'+product_id).text(data.msgs);
          $('.msges_success'+product_id).css('display', 'block');
        // $('.msges_success').attr('tabindex', -1).focus();
        // $('html, body').animate({ scrollTop: $('#msges_success').offset().top }, 'slow');
          setTimeout(() => {
            $('.msges_success'+product_id).css('display', 'none');
          },5000);
        } else {
          $('#status_changed_msg').html("Error while product changing status.").fadeIn('slow').addClass("alert alert-danger").delay(3000).fadeOut('slow');
        }
      }
    });
  }

  function addMainCategory(cate, group) {
  if(cate && group) {
    $.ajax({
    url: "{{url('/productgroup/add/main/category/to/group')}}/"+cate + '/' + group,
    type: "GET",
    cache: false,
    success: function(resp) {
      if(resp.status) {
        var html = '';
        resp.sub_cates.forEach(function callback(value) {
            html += '<option value="'+value.id+'">'+value.name+'</option>';
          });
          var s_html = '<select name="sub_cates" id="sub_catess" class="form-control" onchange="addSubCategory(this.value, '+group+')"><option value="">Sub category</option>';
          var e_html = '</select>';
          var h = s_html+html+e_html;
        $('#sub_categs'+group).html(h);
        $('.msges_success'+group).text(resp.message);
        $('.msges_success'+group).css('display', 'block');
        // $('.msges_success').attr('tabindex', -1).focus();
        // $('html, body').animate({ scrollTop: $('#msges_success').offset().top }, 'slow');
        setTimeout(() => {
          $('.msges_success'+group).css('display', 'none');
        },5000);
      }
    }
  })
  }
  
}

function addSubCategory(cate, product) {
  if(cate && product) {
    $.ajax({
    url: "{{url('/productgroup/add/sub/category/to/group')}}/"+cate + '/' + product,
    type: "GET",
    cache: false,
    success: function(resp) {
      if(resp.status) {
        $('.msges_success'+product).text(resp.message);
        $('.msges_success'+product).css('display', 'block');
        // $('.msges_success').attr('tabindex', -1).focus();
        // $('html, body').animate({ scrollTop: $('#msges_success').offset().top }, 'slow');
        setTimeout(() => {
          $('.msges_success'+product).css('display', 'none');
        },5000);
      }
    }
  })
  }
  
}
</script>
@endpush