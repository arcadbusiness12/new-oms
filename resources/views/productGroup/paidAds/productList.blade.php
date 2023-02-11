@extends('layouts.app')

@section('content')
<style>
    .campaign-list-div {
        max-height: 175px;
        overflow-x: auto;
        margin-top: 12px; 
    }
    .td-valign {
        vertical-align: middle !important;
    }
    .td-valign-product {
      vertical-align: revert !important;
    }
</style>
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
                                <th scope="col"><center>Group</center></th>
                                <th scope="col"><center>Products</center></th>
                                <th scope="col"><center>Type</center></th>
                                </tr>
        
                                </thead>
        
                                <tbody id="body-data">
                                    @php
                                    $sum=0;
                                    $tab_bg_color = "#eee";
                                    $tab_bor_color = "#eee";
                                    @endphp
                                    @if(@$groupProducts)
            
                                    @foreach(@$groupProducts as $key=>$product)
                                    @if(count(@$product->products) > 0)
                                    <tr id="tr_{{$product->id}}" style="border-top: 1px solid gray">
            
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
            
                                    <td class="column col-sm-6 td-valign-product" >
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
                                      
                                  <td class="column col-sm-2 td-valign"><center>
                                  <!-- <select class="form-control" id="product_change_status" onchange="selectedSiteSocialPosts(this.value)">
                                  <option value="">Schedule post</option>
                                  @foreach($stores as $store)
                                  <option value="{{$store->id}}" >{{$store->name}}</option>
                                  @endforeach
                                </select> -->
                                <?php if(session('role') == 'ADMIN' || (array_key_exists('get/setting/template', json_decode(session('access', true))))) { ?>
                                @if($product->product_type_id)
                                <select class="form-control schedule_post custom-select" id="product_change_status" onchange="getSettingTemplate(this.value, '{{$product->id}}', '{{$product->product_type_id}}','{{$product->category_name}}')">
                                  <option value="">Schedule post</option>
                                  @foreach($stores as $store)
                                  <option value="{{$store->id}}" >{{$store->name}}</option>
                                  @endforeach
                                </select>
                                @endif
                                <?php } ?>
                                  <!-- <button type="button" onclick="productDetails({{$product->id}})" class="btn btn-info" class="btn btn-sm" data-toggle="modal" data-target=".porduct_view_modal" title="view">Promotion</button> -->
            
                                </center>
                              </td>
                            </tr>
                            
                            <tr style="border-top: 2px solid white;">
                            <td colspan="3">
                               <div class="table-responsive promo-table history-tbl-{{$product->id}}" id="history-tbl-{{$product->id}}">
                                @if(isset($product->histories) && count($product->histories) > 0) 
                                  <table class="table table-bordered table-hover">
                                 
                                      <thead style="background-color: #eee;">
                                          <tr>
                                              <th class="text-center">Store</th>
                                              <th class="text-center">Campaign</th>
                                              <input type="hidden" name="store" value="" id="store_{{$product->id}}">
                                              <th class="text-center">Date/Time</th>
                                              @foreach($socials as $social)
                                              <th class="text-center">{{$social->name}}</th>
                                              @endforeach
                                          </tr>
                                      </thead>
                                      <tbody class="history">
                                    @if(count($product->histories) > 0) 
                                      @foreach($product->histories as $history)
                                        @foreach($history as $hi)
                                        <tr>
            
                                              <td class="text-center td-vertical">
                                                <p>{{$hi['store']['name']}}</p>
                                              </td>
                                              <td>
                                                @if(count($hi['main_setting']['paidAdsCampaigns']) > 0)
                                                 @php $campaigns = json_encode($hi['main_setting']['paidAdsCampaigns']); @endphp
                                                 @php $compgn = ''; $date = ''; $c_date = '';@endphp
                                                   @foreach($hi['campaigns'] as $cmpaigns)
                                                        @if(@$cmpaigns->campaign['status'] == 1)
                                                        @php $compgn = $cmpaigns->campaign['campaign_name']; $date = $cmpaigns->campaign['start_date'];
                                                          break;
                                                        @endphp
                                                        
                                                        @endif
                                                    @endforeach
            
                                                    @foreach($hi['campaigns'] as $cmpaigns)
                                                        @php $c_name = ''; $c_date = '' @endphp
                                                        @if(@$cmpaigns->campaign['status'] == 1)
                                                          @php $c_name = $cmpaigns->campaign['campaign_name']; 
                                                            $c_date = $cmpaigns->campaign['start_date'];
                                                            break; 
                                                          @endphp
            
                                                        @elseif(@$cmpaigns->campaign['status'] == 2)
                                                          @php $c_name = $cmpaigns->campaign['campaign_name'];
                                                            $c_date = $cmpaigns->campaign['start_date'];
                                                            break; 
                                                          @endphp
                                                        @else
                                                          @if(count($hi['campaigns']) > 0) 
                                                          @php $c_name = @$hi['campaigns'][0]->campaign['campaign_name']; 
                                                          $c_date = $hi['campaigns'][0]->campaign['start_date'];
                                                          @endphp
                                                          @endif
                                                        @endif
                                                    @endforeach
            
                                                 {{-- <p><a href="javascript:;" onclick="showCampaign({{$campaigns}})">Campaigns</a></p> --}}
                                                 {{-- <a href="javascript:;" class="">{{$hi['main_setting']['paidAdsCampaigns'][0]['campaign_name']}}</a>   --}}
                                                 <a href="javascript:;" class="" style="font-size: 14px;">{{$c_name ? $c_name : $hi['main_setting']['paidAdsCampaigns'][0]['campaign_name']}}</a>  
                                                 {{-- {{ $hi['main_setting']['paidAdsCampaigns'][0]['start_date'] ? '|' : '' }}  --}}
                                                 <span style="font-size: 12px;"> - {{$c_date ? $c_date : $hi['main_setting']['paidAdsCampaigns'][0]['start_date']}}</span>
                                                 {{-- <span style="font-size: 12px;">{{$date}}</span> --}}
                                                 <a href="#demo{{$hi['id']}}" class="" data-toggle="collapse">
                                                   <i class="fa fa-list" aria-hidden="true" style="font-size: 20px;
                                                   float: right;color: lightseagreen;"></i> 
                                                  </a> 
                                                 <div id="demo{{$hi['id']}}" class="collapse campaign-list-div">
                                                  <ul>
                                                    @foreach($hi['campaigns'] as $cmpaigns)
                                                        @php $color = ''; @endphp 
                                                        @if(@$cmpaigns->campaign['status'] == 1)
                                                        @php; $color = 'green'; @endphp
                                                        @endif
                                                        @if(@$cmpaigns->campaign['status'] == 2)
                                                        @php $color = 'orange'; @endphp
                                                        @endif
                                                        @if(@$cmpaigns->campaign['campaign_name'])
                                                         <li class="{{$color}}"><strong>{{$cmpaigns->campaign['campaign_name']}}</strong> <span style="font-size: 12px;">{{$cmpaigns->campaign['start_date'] ? '-' : ''}} {{$cmpaigns->campaign['start_date']}}</span></li>
                                                        @endif
                                                    @endforeach
                                                  </ul>
                                                </div>
                                                 </div>
                                                @endif
                                              </td>
                                              <td class="text-center tbl-top-td">
                                                <p>{{ @$c_date ? date('Y-F-d', strtotime(@$c_date)) : date('Y-F-d', strtotime(@$hi['date']))}}</p>
                                              </td>
                                              @foreach($socials as $social)
                                              <td class="text-center td-vertical"><input type="checkbox" disabled <?php if(in_array($social->id, explode(',', $hi['socials'])) ) { echo 'checked'; }else{ echo '';} ?>></td>
                                              @endforeach
                                          </tr>
                                          @endforeach
                                          @endforeach
                                        @endif
                                      </tbody>
                                  </table>
                                  
                                  @endif
                                 </div> 
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

<!--  Store social post modal end -->
<div class="modal fade store_social_view_modal" id="promotion_setting_modal" abindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold" id="exampleModalCenterTitle" style="display: inline-block;">Promotion</h5>
        <span id="top-title" class="font-weight-bold"></span>
      </div>
      <div class="modal-body" >
        <form id="promo_form" name="promo_form" method="post">
          {{csrf_field()}}
        <div id="setting_view_contentt">
        <div class="modal-content-loader"></div>
      </div>
      <div class="modal-footer">
        <span class="text-right" id="error_mesge" style="color:red;margin-right: 263px;">  </span>
        <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
        <button type="button" class="btn btn-danger promotion-dismiss" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>
</div>
<!-- 
  Store social post Modal code end-->

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@include('productGroup.modals')
@endsection

@push('scripts')
<script>
  $(document).ready(function(){
  $(".nav-tabs a").click(function(){
    $(this).tab('show');
  });
  getSubCategories(<?php echo @$old_input['cate'] ?>)
});

function getSettingTemplate(store, group, type,selected_cate) {
    $('.modal-content-loader').css('display', 'none');
    if(store) {
      $('#top-title').text(getStoreName(store));
      $('.store_social_view_modal').modal('show');
      $.ajax({
        url: "{{url('/productgroup/get/setting/template')}}/"+store +'/' +group +'/'+ type +'/'+ selected_cate,
        type: "GET",
        cache: false,
        success: function(resp) {
          $('#setting_view_contentt').html(resp)
        }
      })
    }
  }
  function getStoreName(store) {
  return (store == 1) ? 'BusinessArcade' : 'DressFair';
}

function scheduleNewPostForEmptyDay(campaign,row,main_setting_id,setting_id,type, category,category_ids, group_type,socials,store, range,budget, action, selected_group, selected_cate, start_date = null, end_date = null, sub_category=null) {
  
  var _token = document.querySelector('meta[name="csrf-token"]').content;
    $('.modal-content-loader').css('display', 'block');
    if(action == 'current') {
      $('.current-new-'+row).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('.current-new-'+row).prop('disabled', true); 
    }else {
      $('.next-new-'+row).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('.next-new-'+row).prop('disabled', true);
    }
    
  $.ajax({
    url: "{{url('/productgroup/save/change/schedule')}}",
    type: "POST",
    data: {_token:_token,campaign,main_setting:main_setting_id,setting:setting_id,category:selected_cate,group_type:group_type,socials:socials,time:range,budget:budget,store:store,post_type:2,row:row,action:action,type:type,selected_category:selected_cate,schedule_group:selected_group,date:start_date,last_date:end_date,sub_category:sub_category},
    cache: false,
    success: function(respo) {
      console.log(respo);
      if(respo.status == true) {
            if(action == 'current') {
              $('.current-new-'+row).prop('disabled', false);
              $('.current-new-'+row).html(respo.code);
              $('.current-'+respo.row+respo.id).prepend(respo.old_code);
            }else {
              $('.next-new-'+row).prop('disabled', false);
              $('.next-new-'+row).html(respo.code);
            } 
              loadDetails(selected_group, 1);           
            $('.puased-code-'+row).prepend(respo.group_code);
      }else if(respo.status == 'no_quantity') {
        if(action == 'current') {
          $('.current-new-'+row).html('<i class="icon icon-plus-circle "></i>');  
        }else {
          $('.next-new-'+row).html('<i class="icon icon-plus-circle "></i>');
        }
        
        $('#schedule_error').text(respo.mesge);
          $('#schedule_error').css('display', 'inline');
          setTimeout(() => {
          $('#schedule_error').css('display', 'none');
        }, 3500);
      }else {
        if(action == 'current') {
          $('.current-new-'+row).html('<i class="icon icon-plus-circle "></i>');  
        }else {
          $('.next-new-'+row).html('<i class="icon icon-plus-circle "></i>');
        }
          $('#schedule_error').text("The code "+respo.code+" already posted in "+respo.exist_date);
          $('#schedule_error').css('display', 'inline');
          setTimeout(() => {
          $('#schedule_error').css('display', 'none');
        }, 3500);
      }
    }
  });
}

function scheduleProduct(campaign,row,main_setting_id,setting_id,type, category, group_type, group_code,group_id,post_id,socials,date,last_date,store,range, action,selected_group, selected_cate, sub_category=null) {
  // console.log(selected_group);
    var _token = document.querySelector('meta[name="csrf-token"]').content;
    $('.modal-content-loader').css('display', 'block');
    $('#changed-group').text(group_code);
    if(action == 'current') {
      $('.code-'+group_code).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('.code-'+group_code).prop('disabled', true);
    }else {
      $('.code-next-'+group_code).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('.code-next-'+group_code).prop('disabled', true);
    }
    
  $.ajax({
    url: "{{url('/productgroup/save/change/schedule')}}",
    type: "POST",
    data: {_token:_token,campaign:campaign,main_setting:main_setting_id,setting:setting_id,post_id:post_id,category:category,group_type:group_type,socials:socials,date:date,last_date:last_date,time:range,store:store,post_type:2,action:action,type:type,selected_category:selected_cate,schedule_group:selected_group,sub_category:sub_category},
    cache: false,
    success: function(respo) {
      console.log(respo);
      if(respo.status == true) {
        $('.code-'+group_code).prop('disabled', false);
        console.log(action);
            if(action == 'current') {
              $('.code-'+group_code).html(respo.code);
              $('.current-'+respo.row+respo.id).prepend(respo.old_code);
            }else {
              $('.code-next-'+group_code).html(respo.code);
            }
              console.log("Selected = "+selected_group);            
            loadDetails(selected_group, 1);   
            $('.puased-code-'+group_code).prepend(respo.group_code);
      }else if(respo.status == 'no_quantity') {
        $('.code-'+group_code).html(group_code);
        $('#schedule_error').text(respo.mesge);
          $('#schedule_error').css('display', 'inline');
          setTimeout(() => {
          $('#schedule_error').css('display', 'none');
        }, 3500);
      }else {
         $('.code-'+group_code).html(group_code);
          $('#schedule_error').text("The code "+respo.code+" already posted in "+respo.exist_date);
          $('#schedule_error').css('display', 'inline');
          setTimeout(() => {
          $('#schedule_error').css('display', 'none');
        }, 3500);
      }
    }
  });
}

function getSubCategories(cate, sub = null) {
  if(cate) {

    $.ajax({
      url: "{{url('/productgroup/sub/categories/for/paid/setting')}}/"+cate,
      type: "GET",
      cache: false,
      success: function(respo) {
        console.log(respo);
        var html = '';
        if(respo.status) {
          respo.cates.forEach(function callback(value) {
            if(value.id == sub) {
              var select = 'selected';
            }else {
              var select = '';
            }
            html += '<option value="'+value.id+'" '+select+'>'+value.name+'</option>';
          });
        }
        console.log(html);
        var s_html = '<select name="sub_cates" id="sub_catess" class="form-control"><option value="">Sub category</option>';
        var e_html = '</select>';
        var h = s_html+html+e_html;
        $('#sub_catess').html(h);
      }
    })
  }
}

</script>
@endpush