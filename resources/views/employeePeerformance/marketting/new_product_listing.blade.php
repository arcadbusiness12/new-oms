@extends('layouts.app')

@section('content')
<style>
    .success-fs{
      font-size: 15px;
    }
    .green{
      color: green;
      cursor: pointer;
    }
    .red{
      color:red;
      cursor: pointer;
    }
    .bootstrap-select{
      width: 100px !important;
      height: 25px !important;
    }
    .group-name{
      width: 116%;
      margin-top: -11px;
      padding: 11px;
      margin-bottom: 4px;
    }
    .light-red{
      background: #ff00002b;
    }
    .light-orange{
      background:#ffa5003d;
    }
    .light-green{
      background:#00800021;
    }
    }
    .tab-links a.active {
      color:green;
      border:1px solid green;
      -webkit-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
      -moz-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
      box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
    }
    .img-thumbnail {
      /* width: auto; */
      max-width: 119px !important;
      height: 119px !important;
    }
    .product_row {
      border: 1px solid lightgray;
      margin-bottom: 10px;
      padding: 10px 0 10px 0;
  }
   .product_row .options-label {
      border: 1px solid #dddddd;
  }
   .product_row .options-label .box-label {
      display: inline-block;
      padding: 6px 10px;
  }
  .btn-month-active {
    background-color: green !important;
    color: white !important;
  }
  .form-div {
    display: inline-block;
      float: left;
      margin-right: 5px;
  }
  .btn-div {
      max-width: 9% !important;
  }
  .month-row {
    padding-left: 15px;
  }
  .alerts-border {
      border: 2px #ff0000 solid;
      animation: blink 1s;
      animation-iteration-count: 5;
  }
  .account-tab .active {
      border-bottom: 2px solid green !important;
      background-color: #aeffae;
    }
.account-tab .active {
    border-bottom: 2px solid green !important;
    background-color: #aeffae;
}
.tab-links a {
    text-decoration: none;
    border: 1px solid gray;
    padding: 5px 10px;
    text-transform: uppercase;
    text-align: center;
    font-size: 13px;
    float: left;
    margin-right: 5px;
}
  @keyframes blink { 50% { border-color:#fff ; }  }
  </style>
  <section class="content">
      <div class="container-fluid">
        
          <div class="error-messages"></div>
          <div class="block-header">
              <div class="col-sm-4 col-grid">
              <h2>New Arrival Lists</h2>
              </div>
              <div class="col-sm-8 col-grid tab-links account-tab">
  
                <a href="{{URL::to('performance/employee-performance/marketer/product/listing')}}" class="<?php if(stripos(Request::path(), 'employee-performance/marketer/product/listing') === 0 && @$action != 'image' && @$action != 'enable') {?> active <?php } ?> link-tabs"><div class="tab-box">List</div></a>
                 
                <a href="{{URL::to('performance/employee-performance/marketer/product/listing/image')}}" class="<?php if(stripos(Request::path(), 'employee-performance/marketer/product/listing/image') === 0) {?> active <?php } ?> link-tabs"><div class="tab-box">Image</div></a>
                  
                <a href="{{URL::to('performance/employee-performance/marketer/product/listing/enable')}}" class="<?php if(stripos(Request::path(), 'employee-performance/marketer/product/listing/enable') === 0) {?> active <?php } ?> link-tabs"><div class="tab-box">Enable</div></a>
              </div>
              <div class="clearfix"></div>
          </div>
          <div class="row">
            <div class="col-sm-12 tab-links">
                
          </div>
          </div>
          <div class="row">
              <div class="col-xs-12">
                  <div class="card">
                      @if ($errors->any())
                        <div class="alert alert-warning">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                      @endif
                      @php
                      @endphp
                      @if(Session::has('query_status'))
                        <div class="alert alert-success">
                          {{ Session::get('query_status') }}
                        </div>
                        
                      @endif
                      <div class="alert alert-success" style="display: none;">
                      </div>
                      <div class="alert alert-warning" style="display: none;">
                      </div>
                      @if(Session::has('promo_status'))
                        <div class="alert alert-warning">
                          {{ Session::get('promo_status') }}
                        </div>
                      @endif
                      <div class="panel panel-default">
                          <div class="panel-body">
                            <div class="row mt-2 mb-2">
                            <div class="col-12 month-row">
  
                              <div class="col-2 form-div btn-div">
                                <a href="{{URL::to('/performance/employee-performance/marketer/product/listing/pending')}}" class="<?php if(stripos(Request::path(), 'employee-performance/marketer/product/listing/pending') === 0) {?> btn-month-active <?php } ?> link-tabs btn btn-info"><div class="tab-box">Pending</div></a>
                              </div>
                              <div class="col-2 form-div" style="max-width: 12%;">
                                <form method="get" action="{{route('employee-perfomance.marketer.product.listing', ($action != 'pending') ? $action : '')}}">
                                  <input type="hidden" name="previous_month" value="{{$previousMonth}}">
                                  <input type="hidden" name="current_month" value="{{$currentMonth}}">
                                  {{-- <button type="submit" name="previous" value="previous" class="btn btn-info previous-btn {{($active_tab == 'previous') ? 'btn-month-active' : ''}}">Previous Month</button> --}}
  
                                  <button type="submit" name="current" value="current" class="btn btn-info previous-btn {{($active_tab == 'current') ? 'btn-month-active' : ''}}">Current Month</button>
                                </form>
                              </div>
                              @foreach($previousMonths as $k => $month)
                                <div class="col-2 form-div btn-div">
                                    <form method="get" action="{{route('employee-perfomance.marketer.product.listing', ($action != 'pending') ? $action : '')}}">
                                    <input type="hidden" name="previous_month" value="{{$month['month']}}">
                                    <input type="hidden" name="current_month" value="{{$currentMonth}}">
                                    <button type="submit" name="previous" value="{{$month['name']}}" class="btn btn-info previous-btn {{($active_tab == $month['name']) ? 'btn-month-active' : ''}}">{{$month['name']}}</button>
    
                                    </form>
                                    </div>
                              @endforeach
                            </div>
                          </div>
                            
  
                            
                                {{csrf_field()}}
                                <div class="table-responsive">
                                  <div class="wrapper">
                                  <table class="table"  style="border: 1px solid #2196f3; width:68%;">
                                    {{-- <input type="hidden" name="main_id" value="{{$id}}"> --}}
                                        <thead >
                                          <tr id="head_row" style="background-color:lightgray">
                                            
                                            @if($action == 'pending') {{-- head for pending list --}}
                                              @foreach(@$new_arrivals_data as $k => $pending_posts)
                                              {{-- @if($k != '') --}}
                                              <th scope="col" class="calendar-col"><center>{{ date('D-d-F', strtotime($k)) }}</center></th>
                                              {{-- @endif --}}
                                              @endforeach
                                            @else 
                                              @if(@$days)
                                              @foreach($days as $k => $day)
                                              <th scope="col" class="calendar-col"><center>{{$day['display_date']}}</center></th>
                                              @endforeach
                                              @endif
                                            @endif
                                          </tr>
                                  
                                        </thead>
                                  
                                        <tbody>
                                            @php
                                              $desining_permission = true;
                                              $posting_permission = true;
                                              $diabale_action = 5;
                                              $assignedp = [];
                                            @endphp
                                            
                                              @php $group = ''; $counter = 0 ;@endphp
                                                  
                                              @for ($i = 0; $i < $row_num; $i++)
                                              @php $pending_group_name = ''; @endphp
                                              {{-- @foreach(@$new_arrivals_data as $key => $post1) --}}
                                                @php $counter+1 ;@endphp
                                            <tr id="" style="border-top: 1px solid gray !important;">
  
                                              {{-- td for pending list --}}
                                            @if($action == 'pending') {{-- td for pending list --}}
                                              @foreach(@$new_arrivals_data as $k => $pending_posts)
  
                                               @foreach(@$pending_posts as $pending_post)
                                                @php
                                                    $pending_exist = false;
                                                      $scheduled_id = 0;
                                                    @endphp
                                                      @if($k == $pending_post->confirm_date && !in_array($pending_post->group_name, $assignedp))
                                                        @php
                                                         $pending_exist = true;
                                                         $pending_group_color_class = "light-red";
                                                         $pending_designed = $pending_post->designed;
                                                         $pending_posted = 0;
                                                         $pending_diabale_action = "";
                                                         $pending_scheduled_id = $pending_post->group_id;
                                                         $pending_group_name = $pending_post->group_name;
                                                         array_push($assignedp, $pending_post->group_name);
                                                         break;
                                                        @endphp 
                                                        
                                                      @endif
                                                  
                                                 @endforeach 
                                                      <td class="argn-popup-td posting-status" style="vertical-align: middle;">
                                                        <center>
                                                      
                                                      @if($pending_exist)
                                                      <a href="javascript:;" onclick="viewProductDetails('{{$pending_scheduled_id}}','{{$pending_group_name}}')"><div class="row group-name light-red post-title{{$pending_scheduled_id}}">
                                                        
                                                        {{ @$pending_group_name }} 
                                                        {{-- {{$i}} --}}
                                                         {{-- {{$day['hiddn_date']}} {{$k}} --}}
                                                      </div></a>
                                                      <div class="row">
                                                        <div class="col-sm-12 btn-designed-icon{{$pending_scheduled_id}}">
                                                          {{-- @if($designed == 1) --}}
                                                            @if( $diabale_action == "" )
                                                              @if( $desining_permission )
                                                                <a href="javascript:;" onclick="postOrDesign('{{$pending_scheduled_id}}','{{$pending_group_name}}')" class="btn btn-xs btn-info btn-designed btn-designed{{$pending_scheduled_id}}">List</a>
                                                              @endif
                                                            @else
                                                              {{-- <button class="btn btn-xs btn-secondary " {{ $diabale_action  }}>Design</button> --}}
                                                            @endif
                                                            {{-- @else 
                                                            <span>Wait for design</span>
                                                            @endif --}}
                                                        </div>
                                                      </div>
                                                      @else
                                                        -
                                                      @endif
                                                        </center>
                                                          
                                                      </td>
                                               @endforeach
                                              
                                              @else
  
                                              @foreach(@$days as $k => $day)
                                              @foreach(@$new_arrivals_data as $post)
                                              @php
                                                    $exist = false;
                                                      $scheduled_id = 0;
                                                    @endphp
                                                      @if(@$new_arrivals_data[$day['hiddn_date']][$i])
                                                        @php
                                                         $exist = true;
                                                         $group_color_class = "light-red";
                                                         $designed = $new_arrivals_data[$day['hiddn_date']][$i]['designed'];
                                                         $posted = 0;
                                                         $diabale_action = "";
                                                         $scheduled_id = $new_arrivals_data[$day['hiddn_date']][$i]['group_id'];
                                                         $group_name = $new_arrivals_data[$day['hiddn_date']][$i]['group_name'];
                                                         $photo_shoot_id = $new_arrivals_data[$day['hiddn_date']][$i]['photo_shoot_id'];
                                                         $photo_shoot = $new_arrivals_data[$day['hiddn_date']][$i]['photo_shoot'];
                                                         $assigned_to_photoshoot = $new_arrivals_data[$day['hiddn_date']][$i]['assigned_to_photoshoot'];
                                                        //  $is_image_upload = $new_arrivals_data[$day['hiddn_date']][$i]['is_image_upload'];
                                                         $product_image = $new_arrivals_data[$day['hiddn_date']][$i]['product_image'];
                                                         $enable_product = $new_arrivals_data[$day['hiddn_date']][$i]['enable_product'];
                                                         $product_list = $new_arrivals_data[$day['hiddn_date']][$i]['product_list'];
                                                         $listing_checked = $new_arrivals_data[$day['hiddn_date']][$i]['listing_checked'];
                                                         $upload_image_checked = $new_arrivals_data[$day['hiddn_date']][$i]['upload_image_checked'];
                                                         $ba_price = $new_arrivals_data[$day['hiddn_date']][$i]['ba_price'];
                                                         $df_price = $new_arrivals_data[$day['hiddn_date']][$i]['df_price'];
                                                        //  break;
                                                        @endphp 
                                                        
                                                      @endif
  
                                                    @endforeach
                                                    <td class="argn-popup-td posting-status product-section-td{{$scheduled_id}}" style="vertical-align: middle; width:7% !important">
                                                      <center>
                                                    
                                                    @if($exist)
                                                    <a href="javascript:;" onclick="viewProductDetails('{{$scheduled_id}}','{{$group_name}}')"><div class="row group-name light-red post-title{{$scheduled_id}}">
                                                      
                                                      {{ @$group_name }}
                                                      {{-- {{$i}} --}}
                                                       {{-- {{$day['hiddn_date']}} {{$k}} --}}
                                                    </div></a>
                                                    <div class="row">
                                                      <div class="col-sm-12 btn-designed-icon{{$scheduled_id}}">
                                                        {{-- @if($designed == 1) --}}
                                                          @if( $diabale_action == "" )
                                                            @if( $desining_permission )
                                                             @if($action != 'image' && $action != 'enable')
                                                              <a href="javascript:;" onclick="listNewProduct('{{$scheduled_id}}','{{$group_name}}','product_list','List')" class="btn btn-xs btn-info btn-designed btn-List{{$scheduled_id}}">List</a>
                                                             @endif
                                                              @if($action == 'image')
                                                              @if(!$photo_shoot_id && $assigned_to_photoshoot == 0) 
                                                                <a href="javascript:;" onclick="listNewProduct('{{$scheduled_id}}','{{$group_name}}','assigned_to_photoshoot','Photoshot')" style="margin-bottom: 12px" class="btn btn-xs btn-info btn-designed btn-Photoshot{{$scheduled_id}}">Photoshot</a><br>
                                                                
                                                                <a href="javascript:;" onclick="listNewProduct('{{$scheduled_id}}','{{$group_name}}','assigned_to_photoshoot','Designer')" class="btn btn-xs btn-primary btn-designed btn-Designer{{$scheduled_id}}">Designer</a>
                                                              @endif
                                                              @if($assigned_to_photoshoot == 1 && $product_image == 0) 
                                                                <a href="javascript:;" onclick="listNewProduct('{{$scheduled_id}}','{{$group_name}}','product_image','Upload Image')" style="margin-bottom: 12px" class="btn btn-xs btn-info btn-designed btn-Upload-Image{{$scheduled_id}}">Upload Image</a>
                                                              @endif
                                                              @endif
                                                              @if($action == 'enable') 
                                                              <div class="done-actions">
                                                                <div class="col-4" style="padding-left: 0px; max-with:77% !important;">
                                                                  <strong>Listing</strong>
                                                                </div>
                                                                <div class="last-action-section listing_checked-{{$scheduled_id}}">
                                                                  @if($listing_checked == 1)
                                                                    <i class="fa fa-check" aria-hidden="true" style="font-size: 14px;"></i>
                                                                  @else 
                                                                    <div class="col-sm-3 col-grid list-check-btn" style="padding-right: 16px;padding-left: 9px;">
                                                                      
                                                                      <a href="javascript:;" onclick="listNewProduct('{{$scheduled_id}}','{{$group_name}}','listing_checked','Checked')" style="margin-bottom: 12px" class="btn btn-xs btn-success btn-designed btn-list-check{{$scheduled_id}}" style="padding: 0px 5px;">
                                                                        <i class="fa fa-check" aria-hidden="true" style="font-size: 14px;"></i>
                                                                      </a>
                                                                    </div>
                                                                    <div class="col-sm-3 col-grid" style="padding-right: 0px;padding-left: 9px;">
                                                                      <a href="javascript:;" onclick="listClose('{{$scheduled_id}}','{{$group_name}}','listing_checked','list')" style="margin-bottom: 12px" class="btn btn-xs btn-danger btn-designed btn-list-close{{$scheduled_id}}" style="padding: 0px 4px;">
                                                                        <i class="fa fa-close" style="font-size:14px"></i>
                                                                      </a>
                                                                    </div>
                                                                  @endif
                                                               </div>
                                                             </div>
  
                                                            <div class="done-actions">
                                                              <div class="col-4" style="padding-left: 0px; max-with:77% !important">
                                                                <strong>Image</strong>
                                                              </div>
                                                              <div class="last-action-section upload_image_checked-{{$scheduled_id}}">
                                                              @if($upload_image_checked == 1)
                                                              <i class="fa fa-check" aria-hidden="true" style="font-size: 14px;"></i>
                                                              @else 
                                                              <div class="col-sm-3" style="padding-right: 16px;padding-left: 9px;">
                                                                
                                                                <a href="javascript:;" onclick="listNewProduct('{{$scheduled_id}}','{{$group_name}}','upload_image_checked','Checked')" style="margin-bottom: 12px" class="btn btn-xs btn-success btn-designed btn-upload_image_checked{{$scheduled_id}}" style="padding: 0px 5px;">
                                                                  <i class="fa fa-check" aria-hidden="true" style="font-size: 14px;"></i>
                                                                </a>
                                                              </div>
                                                              <div class="col-sm-3 final-action" style="padding-right: 0px;padding-left: 9px;">
                                                                <a href="javascript:;" onclick="listClose('{{$scheduled_id}}','{{$group_name}}','upload_image_checked','image')" style="margin-bottom: 12px" class="btn btn-xs btn-danger btn-designed btn-image-close{{$scheduled_id}}" style="padding: 0px 4px;">
                                                                  <i class="fa fa-close" style="font-size:14px"></i>
                                                                </a>
                                                              </div>
                                                              </div>
                                                              @endif
                                                            </div>
  
                                                            <div class="done-actions">
                                                              @if($ba_price == 1 && $df_price == 1)
                                                              <div class="col-sm-4" style="padding-right: 32px;padding-left: 0px;">
                                                                <strong>Price</strong>
                                                              </div>
  
                                                              <div class="col-sm-3" style="padding-right: 16px;padding-left: 9px;">
                                                                
                                                                {{-- <a href="javascript:;" onclick="listNewProduct('{{$scheduled_id}}','{{$group_name}}','enable_product','Enable')" style="margin-bottom: 12px" class="btn btn-xs btn-success btn-designed btn-Upload-Image{{$scheduled_id}}" style="padding: 0px 5px;"> --}}
                                                                  <i class="fa fa-check" aria-hidden="true" style="font-size: 14px;"></i>
                                                                {{-- </a> --}}
                                                                
                                                              </div>
                                                              @else
                                                                {{-- <a href="javascript:;" onclick="listNewProduct('{{$scheduled_id}}','{{$group_name}}','enable_product','Enable')" style="margin-bottom: 12px" class="btn btn-xs btn-success btn-designed btn-Upload-Image{{$scheduled_id}}" style="padding: 0px 5px;"> --}}
                                                                 <strong style="color: red;" data-toggle="tooltip" data-placement="top" title="Update Price In Opencart"> Update Price </strong>
                                                                {{-- </a>  --}}
                                                                @endif
                                                              {{-- <div class="col-sm-3" style="padding-right: 0px;padding-left: 9px;">
                                                                <a href="javascript:;" onclick="listNewProduct('{{$scheduled_id}}','{{$group_name}}','enable_product','Enable')" style="margin-bottom: 12px" class="btn btn-xs btn-danger btn-designed btn-Upload-Image{{$scheduled_id}}" style="padding: 0px 4px;">
                                                                  <i class="fa fa-close" style="font-size:14px"></i>
                                                                </a>
                                                              </div> --}}
                                                            </div>
                                                            @php $btn_display = 'none'; $text_display = 'none'; @endphp
                                                            @if($listing_checked == 1 && $upload_image_checked == 1 && $ba_price == 1 && $df_price == 1)
                                                            @php $btn_display = 'block'; @endphp
                                                            @else 
                                                            @php $text_display = 'block'; @endphp
                                                            @endif
                                                            <div class="col-sm-12 btn-enbled{{$scheduled_id}}" style="display:{{$btn_display}}">
                                                              <a href="javascript:;" onclick="listNewProduct('{{$scheduled_id}}','{{$group_name}}','enable_product','Enable')" style="margin-bottom: 12px;" class="btn btn-xs btn-success btn-designed btn-Upload-Image{{$scheduled_id}}">
                                                                Enable
                                                              </a>
                                                            </div>
                                                            <div class="col-sm-12 btn-disabled{{$scheduled_id}}" style="display:{{$text_display}}">
                                                              <a href="javascript:;" style="margin-bottom: 12px;" disabled class="btn btn-xs btn-danger btn-designed btn-Upload-Image{{$scheduled_id}}">Enable</a>
                                                            </div>
                                                              @endif
                                                            @endif
                                                          @else
                                                            <button class="btn btn-xs btn-secondary " {{ $diabale_action  }}>Design</button>
                                                          @endif
                                                          {{-- @else 
                                                          <span>Wait for design</span>
                                                          @endif --}}
                                                      </div>
                                                    </div>
                                                    @else
                                                      -
                                                    @endif
                                                      </center>
                                                        
                                                    </td>
                                                    @endforeach
                                                  @endif
                                                  </tr>
                                                    {{-- @endforeach --}}
                                              @endfor
                                  
                                      </tbody>
                                   </table>
                                  </div>
                                </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  
      <!--  Setting modal end -->
    <div class="modal fade new_product_detail_modal" id="new_product_detail_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content" >
          <div class="modal-header text-center">
            <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Product Details <span id="changed-group" style="color: green;"></span></h5>
            <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
            <span id="top-title"></span>
          </div>
          <div class="modal-body" >
            <div class="detail-div"></div>
            <div class="row text-center loader-teg" style="display: none;">
              Please wait while image is loading..
              
            </div>
          <div class="modal-footer">
          </div>
        </div>
      </div>
    </div>
    </div>
  </section>
@endsection
@push('scripts')
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<link rel="stylesheet" href="{{URL::asset('public/assets/css/purchase.css?_=' . time()) }}">
<script type="text/javascript">
$(document).ready(function() {
    var left = $('.wrapper').width();
    console.log(left);
    $('.wrapper').scrollLeft(3225);
  });
  {{-- $(document).on('mouseover','.posting-status',function(){
    console.log("fired");
    $(this).find('span').show();
  })
  $(document).on('mouseout','.posting-status',function(){
    console.log("fired");
    $(this).find('span').hide();
  }) --}}
  function showOption(uid){
    //$('#option'+uid).show();
    console.log(uid);
  }
  function hideOption(uid){
    console.log(uid);
    //$('#option'+uid).hide();
  }
  function showUserProgress(user_id){
    console.log();
    $('.all-user-progress').hide();
    $('#progress_details_'+user_id).show();
    $('.all-user-progress-button').removeClass('active');
    $('#sale_person_name'+user_id).addClass('active');
  }
  {{--  function confirmDailyProgress(user_id){
    //console.log();
  }  --}}
  $(document).ready(function() {
    $("#status_published").select2({
        placeholder: "-Select Product-",
        width:'270%'
    });
    //=============
    $("#catalog_product_add").select2({
      placeholder: "-Select Product-",
      width:'270%'
  });
  });

function viewProductDetails(group, name) {
  console.log(group+" "+name);
  $('#new_product_detail_modal').modal('toggle');
  $('.loader-tag').css('display', 'block');
  if(group) {
    $.ajax({
        url: "{{url('/performance/new/arrival/product/list/detail')}}/"+ group,
        type: "GET",
        cache: false,
        success: function(resp) {
          console.log(resp);
          if(resp.status) {

          $('.loader-tag').css('display', 'none');
            var html = '';
            html += '<div class="col-xs-12"><span style="color:red">'+resp.cate_error+'</span>';
            resp.products.forEach(function callback(v, k) {
              console.log(v);
              var supplier_link = '';
              if( v.supplier_link  ){
                supplier_link = "<a href='"+v.supplier_link+"' target='_blank'>"+v.supplier_link+"</a>";
              }
              var option_html = '';
              v.products_sizes.forEach(function callback(op_v, op_k) {
                
                option_html += '<div class="box-label"><span>'+op_v.oms_option_details.value+'<sup style="color:green; font-weight:bolder;font-family: system-ui">'+op_v.available_quantity+'</sup></span></div> | ';
              });
              var cl = (v.listing == 0 && v.designed == 1) ? 'alerts-border' : '';
              var url = '';
              url = APP_URL + "/uploads/inventory_products/"
              url = url + v.image;
              html += '<div class="row product_row '+cl+'"><div class="col-xs-4 col-sm-2 product-block post-title">' +'\n'+
                                    '<img id="uploadable" src="'+url+'" class="img-thumbnail" width="145" height="100px">' +'\n'+
                                    '</div>' +'\n'+
                                    '<div class="col-xs-8 col-sm-10" style="padding-top: 28px;">' +'\n'+
                                      '<span><strong>'+v.sku+' | '+v.option_name+'</strong></span>'+'\n'+
                                        '<div class="options-label btn-designed-icon" style="padding-top: 3px;">'+option_html+'</div>'+'\n'+
                                      '</div>'+'\n'+
                                      '<div class="col-sm-10 col-sm-offset-2">'+supplier_link+'</div>'+
                                  '</div>';
            });
            html += '</div>';
            $('.detail-div').html(html);
            // $('.alert-success').css('display', 'block');
            // $('.alert-success').text(resp.mesg);
            // $('.post-title'+id).addClass(bgColor);
            // $('.btn-'+action+id).remove();
            // $('.btn-'+action+'-icon'+id).html('<i class="fa fa-check-circle green success-fs " title="'+action+'"></i>');
            // $('.btn-'+action).prop('disabled', false);

            // setTimeout(() => {
            //   $('.alert-success').css('display', 'none');
            //   $('.alert-success').text('');
            // }, 4000)
          }else {
            $('.alert-warning').css('display', 'block');
            $('.alert-warning').text('Somethings went wrong please try again.');
            setTimeout(() => {
              $('.alert-warning').css('display', 'none');
              $('.alert-warning').text('');
            }, 4000)
          }
          
        }
      });
  }
}
 function listNewProduct(id, group_name = null, action, button) {
    var middelText = '';
    var btn = '';
    var clsBtn = button.replace(" ", "-");
    if(button == 'Checked') {
      $('.btn-'+action+id).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('.btn-'+action+id).prop('disabled', true);
    }else {
      $('.btn-'+clsBtn+id).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('.btn-'+clsBtn+id).prop('disabled', true);
    }
    
    $('.btn-designed').prop('disabled', true);
    if(id) {
      $.ajax({
        url: "{{url('/performance/employee-performance/marketer/listing/new/product')}}/"+ id +"/"+ action +"/"+ button,
        type: "GET",
        cache: false,
        success: function(resp) {
          console.log(resp);
          // return;
          if(resp.status) {
            if(resp.button == 'Checked') {
              $('.'+action+'-'+id).html('<i class="fa fa-check" aria-hidden="true" style="font-size: 14px;"></i>');
              if(resp.pending_verify < 1) {
                $('.btn-enbled'+id).css('display','block');
                $('.btn-disabled'+id).css('display','none');
              }
            }else {
              $('.post-title'+id).addClass('light-green');
              $('.btn-'+clsBtn+id).remove();
              $('.btn-designed-icon'+id).html('<i class="fa fa-check-circle green success-fs " title="'+group_name+'"></i>');
            }
            $('.alert-success').css('display', 'block');
            $('.alert-success').text(resp.mesg);
            
            $('.btn-designed').prop('disabled', false);

            setTimeout(() => {
              $('.alert-success').css('display', 'none');
              $('.alert-success').text('');
            }, 5000)
          }else {
            $('.btn-'+clsBtn+id).html(button);
            $('.btn-'+clsBtn+id).prop('disabled', false);
            $('.btn-'+clsBtn+id).prop('disabled', false);
            if(resp.button == 'Checked') {
              $('.btn-'+action+id).html('<a href="javascript:;" style="margin-bottom: 12px" class="btn btn-xs btn-success btn-designed"><i class="fa fa-check" aria-hidden="true" style="font-size: 14px;"></i></a>');
            }
            ba = '';
            df = '';
            $('.alert-warning').css('display', 'block');
            // $('.alert-warning').text('Somethings went wrong please try again.');
            if(resp.quantity_flag) {
              var mesge = 'There are no quantity in stock.';
            }
            if(!resp.ba_flag && !resp.df_flag && !resp.quantity_flag) {
              var mesge = 'In Business Arcade <strong style="color:#f80904;">'+resp.bamissing_products.join()+'</strong> and in Dressfair <strong style="color:#f80904;">'+resp.dfmissing_products.join()+'</strong>';
            }
            if(!resp.ba_flag && resp.df_flag && !resp.quantity_flag) {
              var mesge = 'In Business Arcade <strong style="color:#f80904;">'+resp.bamissing_products.join()+'</strong>';
            }
            if(!resp.df_flag && resp.ba_flag && !resp.quantity_flag) {
              var mesge = 'In Dressfair <strong style="color:#f80904;">'+resp.dfmissing_products.join()+'</strong>';
            }
            
            var is_are = (!resp.ba_flag && !resp.df_flag || (resp.bamissing_products.length > 1 || resp.dfmissing_products.length > 1)) ? 'are' : 'is';
            if(resp.quantity_flag) {
              var w_megs = mesge;
            }else {
              var w_megs = mesge +' '+is_are+' not found, please make sure these products are listed or the sku '+is_are+' connected.';
            }
            
            $('.alert-warning').html(w_megs);
            $("html, body").animate({ scrollTop: 0 }, 'slow');
            setTimeout(() => {
              $('.alert-warning').css('display', 'none');
              $('.alert-warning').text('');
              $("html, body").animate({scrollTop: $('.btn-designed-icon'+id).offset().top - $(window).height()/2}, 'slow');
            }, 10000)
          }
          
        }
      });
    }
  }

  function listClose(id, group_name = null, action, button) {
    var middelText = '';
    var btn = '';
    var clsBtn = button.replace(" ", "-");
    $('.btn-'+button+'-close'+id).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
    $('.btn-'+button+'-close'+id).prop('disabled', true);
    $('.btn-'+button+'-close'+id).prop('disabled', true);
    if(id) {
      $.ajax({
        url: "{{url('/employee-performance/marketer/close/product/listing')}}/"+ id +"/"+ action +"/"+ button,
        type: "GET",
        cache: false,
        success: function(resp) {
          console.log(resp);
          // return;
          if(resp.status) {
            $('.alert-success').css('display', 'block');
            $('.alert-success').text(resp.mesg);
            $('.post-title'+id).addClass('light-green');
            $('.btn-'+button+'-close'+id).remove();
            $('.btn-'+button+'-close'+id).prop('disabled', false);
            $('.product-section-td'+id).html('-');
            $('.product-section-td'+id).css('background-color', 'rgb(237 104 104 / 44%)');
            setTimeout(() => {
              $('.alert-success').css('display', 'none');
              $('.alert-success').text('');
            }, 4000)
          }else {
            $('.alert-warning').html('Opps! Something went wrong please try again.');
            $("html, body").animate({ scrollTop: 0 }, 'slow');
            setTimeout(() => {
              $('.alert-warning').css('display', 'none');
              $('.alert-warning').text('');
              $("html, body").animate({scrollTop: $('.btn-designed-icon'+id).offset().top - $(window).height()/2}, 'slow');
            }, 10000)
          }
          
        }
      });
    }

  }
</script>
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
@endpush