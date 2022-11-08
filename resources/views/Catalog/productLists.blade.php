@extends('layouts.app')

@section('content')
<style>
    .type-name {
        color: green;
        font-weight: bold;
    }
    .icon-bar-chart {
      font-size: 20px;
    }
</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">


            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    
                    <div class="card no-b form-box">
                        @if(session()->has('success'))
                            <div class="alert alert-success">
                                {{ session()->get('success') }}
                            </div>
                        @endif

                        <div class="card-header white">
                            <form name="by_group_type" id="by_group_type" action="" method="get">
                                <input type="hidden" name="page" class="group_type_page_no">
                                {{ csrf_field() }}
                                <div class="row">
                                <div class="col-md-2">
                                <input name="search_for" type="hidden" class="form-control" value="groupPage">
                                <input name="g_name" type="text" class="form-control" value="{{@$old_input['g_name']}}" placeholder="Search by group">
                                </div>
                                <div class="col-md-2">
                                <select class="custom-select form-control" name="type" id="product_change_status">
                                      <option value="">Search By Type</option>
                                      @foreach($types_for_organic as $type)
                                      <option value="{{$type->id}}" <?php if(@$old_input['type'] == $type->id ){ echo 'selected';} else { echo '';} ?>>{{$type->name}}</option>
                                      @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                <select class="custom-select form-control" name="cate" id="product_change_status" id="main_cate_val" onchange="getSubCategories(this.value)">
                                      <option value="">By Category</option>
                                      @foreach($main_categories as $cate)
                                      <option value="{{$cate->id}}" <?php if(@$old_input['cate'] == $cate->id ){ echo 'selected';} else { echo '';} ?>>{{$cate->name}}</option>
                                      @endforeach
                                    </select>
                                </div>
                
                                <div class="col-md-2" id="sub_catess">
                                <select name="sub_cate" class="custom-select form-control">
                                  <option value="">Sub category</option>
                                </select>
                                </div>
                
                                <div class="col-md-2" id="sub_catess">
                                  <select name="product_status" class="custom-select form-control">
                                    <option value="yes" <?php if(@$old_input['product_status'] == 'yes' ){ echo 'selected';} else { echo '';} ?> >Enable</option>
                                    <option value="no" <?php if(@$old_input['product_status'] == 'no' ){ echo 'selected';} else { echo '';} ?>>Disable</option>
                                  </select>
                                  </div>
                
                                <div class="col-md-2">
                                  <button type="submit" class="btn btn-sm btn-primary"><i class="icon icon-filter"></i>Search</button>
                                  <!-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> -->
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
                            Product Listing
                          </div>
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                           <table class="table" width="100%" style="border-left: 1px solid #3f51b5; border-right: 1px solid #3f51b5;">

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
                              <tr id="tr_{{$product->id}}" style="border-top: 1.5px solid #3f51b5;">
      
                              {{-- <td class="td-valign"><div> <input type="checkbox" class="sub_chk" data-id="{{$product->product_id}}"></div></td> --}}
                              <!--<td class="td-valign"><input type="text" size="8" value="{{ $product->product_id }}"></td>
                                <td class="td-valign"><center><b>{{$product->sku}}<br>{{$product->option_name}}</b></center></td>-->
                                
                                <td class="col-sm-2 text-center">
                                  <table class="table table-hover" style="margin-bottom: 4px;">
                                    <thead style="background-color: {{ $tab_bg_color }}">
                                      <th class="tab-th"><center><label>{{$product->name}}</label></center></th>
                                      
                                    </thead>
                                   
                                  </table> 
                                  <img src="{{URL::asset('uploads/inventory_products/'.$product['products'][0]->image)}}" class="img-responsive img-thumbnail group-pro" height="120"/> 
                                  <h5 class="type-name">{{@$product->producType['name']}} </h5>
                                  
                              </td>
      
                                <td class="column col-sm-6" style="vertical-align: top;">
                                    {{-- <table class="" style="display: inline-block;">
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
                                      
                                    </table> --}}
                                @foreach($product['products'] as $key=>$productt)
                                  
                                    <table class="" style="display: inline-block;">
                                      <thead style="background-color: {{ $tab_bg_color }};">
                                        <th><center><label>{{$productt->sku}}</label></center></th>
                                      </thead>
                                      @php 
                                        $sum=0; 
                                        @$sum_available_quantity = 0;
                                      @endphp
                                      
                                      @foreach($productt->productDescriptions as $key=>$val)
                                      <tr>
                                        <td style="
                                        font-size: 13px;
                                    "><center>
                                      <a href="{{route('product.listing.details', [$productt->product_id, $val->store->id])}}"> {{$val->store->name}}  </a>
                                    </center></td>
                                        
                                      </tr>
                                      @php 
                                      @$sum_available_quantity += $val->available_quantity;
                                      @endphp
                                      @endforeach
                                      <tr style="background-color: {{ $tab_bg_color }}">
                                        <!-- <td><center><strong>Total</strong></center></td> -->
                                        <td><center><strong><button class="btn btn-primary btn-xs add-details" id="add-details" data-id="{{$productt->product_id}}" data-modal="#selectStoreModel">Add Details</button></strong></center></td>
                                      </tr>
                                    </table>
                                @endforeach
                                </td>
                                
                            <td class="column col-sm-2 td-valign" style="padding-top: 50px;vertical-align: top;"><center>
                            <?php if(session('role') == 'ADMIN' || (array_key_exists('change/group/type', $user_perm ))) {
                             
                              ?>
                            @if(!$product->category_id)
                            <select class="custom-select form-control" id="product_change_status" disabled onchange="addMainCategory(this.value,{{ $product->id }})">
                            
                              <option value="">Category</option>
                              @foreach($main_categories as $cate)
                              <option value="{{$cate->id}}" {{ ($product->category_id== $cate->id) ? "selected" : "" }}>{{$cate->name}}</option>
                              @endforeach
                            </select><br><br>
                            <div class="sub_categs" id="sub_categs{{$product->id}}">
                            </div>
                          @else
                          <select class="custom-select form-control" id="product_change_status" onchange="addSubCategory(this.value,{{ $product->id }})">
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
                          <br><br>
                          @if( session('role') == 'ADMIN' || (array_key_exists('assign/group/attribute', $user_perm)) )
                          <a href="{{route('assign.attributes.form', [$product->id,$product->category_id])}}" class="btn btn-sm" style="width: 149px;" data-group="{{ $product->name }}_{{ $product->id }}">
                            <button type="button" class="btn btn-primary btn-sm" style="float: left;
                            width: 127px;"> Attributes </button></a><br>
                        @endif 
                            <!-- <button type="button" onclick="productDetails({{$product->id}})" class="btn btn-info" class="btn btn-sm" data-toggle="modal" data-target=".porduct_view_modal" title="view">Promotion</button> -->
      
                          </center>
                        </td>
                        
                        <td class="column col-xs-2 td-valign" style=""><center>
                            <?php $OmsUserGroupModel = new \App\Models\Oms\OmsUserGroupModel;
                              $promotion_options = $OmsUserGroupModel::oms_group_page_options_routes();
                              $color = ($product->size_chart_value_count > 0) ? 'green' : 'red';
                            ?>
                            
                            <?php if( session('user_group_id') == 1 || session('user_group_id') == 5 ) { ?>
                              <a href="javascript:void(0)" class="btn btn-sm btn-size-chart" data-group="{{ $product->name }}_{{ $product->id }}">
                                <i class="icon icon-bar-chart icon-2x" aria-hidden="true" title="" data-toggle="tooltip" data-original-title="Size Chart" style="color: {{$color}} {{$product->size_chart_exist_count}}"></i>
                              </a><br>
                            <?php } ?>
                              
                            <?php if(session('role') == 'ADMIN' || array_key_exists('change/group/type', $user_perm) ) {?>
                            <select class="custom-select form-control" id="product_change_status" onchange="changeGroupType(this.value,{{ $product->id }})">
                            <option value="">Select Type</option>
                            @foreach($types_for_organic as $type)
                            <option value="{{$type->id}}" {{ ($product->product_type_id== $type->id) ? "selected" : "" }}>{{$type->name}}</option>
                            @endforeach
                          </select><br><br>
                          <?php } ?>
      
                          @if( session('user_group_id') == 1 )
                            <select class="custom-select form-control" id="product_change_status" onchange="changeProductStatusAjax(this.value,{{ $product->id }})">
                              <option value="0" {{ (@$product['products'][0]->status==0) ? "selected" : "" }}>Disable</option>
                              <option value="1" {{ (@$product['products'][0]->status==1) ? "selected" : "" }}>Enable</option>
                              <option value="2" {{ (@$product['products'][0]->status==2) ? "selected" : "" }}>Finished</option>
                             
                            </select>
                            @endif
                            {{-- <br><br>
                          @if( session('role') == 'ADMIN' || array_key_exists('assign/group/attribute', $user_perm) )
                            <button type="button" class="btn btn-success">Attributes</button>
                            @endif --}}
      
                          </center>
                        </td>
                        
                      </tr>
                      <tr style="border: none;">
                        <td style="border: none;"></td>
                        <td colspan="2" style="border: none;">
                            
                          <span class="product-success msges_success{{$product->id}}" style="display:none; color:green; font-weight:bold;"></span>
                        </td>
                      </tr>
                          
                        @endif
                      @endforeach
      
                      @endif
      
                    </tbody>
      
                    </table>
                    <div class="row pull-right">
                        <div class="col-xs-12">
                            <?php echo $groupProducts->appends(@$old_input)->render(); ?>
                        </div>
                    </div>
                    <div id="gr">
                    
                    </div>

            </div>

            
                    </div>
                </div>

                
            </div>
        </div>
    </div>
</div>

<!--  size chart modal start -->
<div class="modal fade size_chart_modal" id="size_chart_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 85%">
    <div class="modal-content" >
      <div class="modal-header" style="text-align: center;
      background-color: #3f51b5;
      color: white;
      border: 1px solid white;">
         <h5 class="modal-title" id="sizeChartModalTitle" style="display: inline-block;color:white"></h5> 
        <button type="button" class="close close-popup" data-dismiss="modal" aria-label="Close" style="color: white;">
           <span aria-hidden="true">&times;</span>
        </button>
        <span id="top-title"></span>
      </div>
      <div class="modal-body" >
        <form method="post" id="frm_update_size_chart" action="">
          {{csrf_field()}}
          <input type="hidden" name="group_name" id="group_name">
          <div class="row text-center" id="size_chart_popup_content">
          </div>
          <div class="row">
            <div class="col-sm-8">
              <span class="text-success active" style="color: green; font-weight:bold;"></span>
            <span class="text-error active" style="color: red;font-weight:bold;"></span>
            </div>
            
            <div class="col-sm-4">
              <button type="submit" class="btn btn-success active btn-block">SAVE</button>
            </div>
            
          </div>
        </form>
      <div class="modal-footer col-sm-12">
      </div>
    </div>
  </div>
</div>
</div>


<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>

 <!-- product location modal start -->
 <div class="modal iconde porduct_location_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Add & Edit options</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="porduct_location_content">
          <div class="text-center" id="loader">
            
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
      </div>
    </div>
  </div>
  <!-- product location modal end -->

  <!-- product location modal start -->
 <div class="modal iconde selectStoreModel" id="selectStoreModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title text-black" id="exampleModalCenterTitle">Select store and continue</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="porduct_location_content">
        <div class="text-center" id="loader">
          <input type="hidden" id="single-product" value="" >
          <select name="store" id="selected-store" class="form-control custom-select">
            <option value="">Select Store</option>
            @foreach($stores as $store)
              <option value="{{$store->id}}">{{$store->name}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <a type="button" class="btn btn-primary" id="store-continue">Continue</a>
      </div>
    </div>
  </div>
</div>
<!-- product location modal end -->
@endsection

@push('scripts')
<script>
    function addMainCategory(cate, group) {
  if(cate && group) {
    $.ajax({
    url: "{{route('add.main.category.to.group', ['cate' => '"+cate+"', 'group' => '"+group+"',])}}",
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

function addSubCategory(cate, group) {
  if(cate && group) {
    let url = "{{ route('add.sub.category.to.group', [":cate",":product"]) }}";
    console.log(url);
    url = url.replace(":cate", cate);
    url = url.replace(":product", group);
    $.ajax({
    url: url,
    type: "GET",
    cache: false,
    success: function(resp) {
      if(resp.status) {
        $('.msges_success'+group).text(resp.message);
        $('.msges_success'+group).css('display', 'block')
        // $('html, body').animate({ scrollTop: $('#msges_success').offset().top }, 'slow');
        setTimeout(() => {
          $('.msges_success'+group).css('display', 'none');
        },5000);
      }
    }
  })
  }
  
}
function changeGroupType(type, group) {
  if(type && group) {
    let url = "{{route('change.group.type', [":type",":group"])}}";
    url = url.replace(":type", type);
    url = url.replace(":group", group);
    $.ajax({
    url: url,
    type: "GET",
    cache: false,
    success: function(resp) {
      if(resp.status) {
        $('.msges_success'+group).text(resp.message);
        $('.msges_success'+group).css('display', 'block');
        // $('html, body').animate({ scrollTop: $('#msges_success').offset().top }, 'slow');
        setTimeout(() => {
          $('.msges_success'+group).css('display', 'none');
        },5000);
      }
    }
  })
  }
  
}
function changeProductStatusAjax(status,product_id){
    // console.log(product_id); return;
    $.ajax({
      url: "{{route('group.change.product.status')}}",
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
  
  
  $(document).on('click','.btn-size-chart',function(){
    $('#size_chart_popup_content').html("<h4><i>Loading...</i></h4>");
    var group_name = $(this).attr('data-group');
    $('#group_name').val( group_name );
    $('#sizeChartModalTitle').text('Size For '+group_name.split('_')[0]);
    $('#size_chart_modal').modal('toggle');
    $.ajax({
      url: "{{route('get.product.size.chart')}}",
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
      url: "{{route('update.product.size.chart')}}",
      type: "POST",
      data: form_data,
      cache: false,
      complete: function(){
        $this.find(':submit').text("SAVE").prop("disabled",false);
      },
      success: function(result) {
        $('.text-success').text('Size chart updated successfully');
        setTimeout(() => {
          $('.text-success').text('');
          $('#size_chart_modal').modal('toggle');
        }, 2500);
        
      },error: function(error) {
          
      }
    });
  });
  
  $('.add-details').on('click', function() {
    console.log("Oj=kl");
    $($(this).data('modal')).modal();
    $('#single-product').val($(this).data('id'));
  });
  
  $('#store-continue').on('click', function() {
    var store = $('#selected-store').find(':selected').val();
    var product = $('#single-product').val();
    window.location.href = "{{url('/Catalog/product/listing/details/')}}/" +product + "/" + store;
    console.log($('#selected-store').find(':selected').val());
  });
</script>
@endpush