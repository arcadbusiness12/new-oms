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
    /* width: 102%; */
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
    background:#8ace8a21 !important;
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
    max-width: 95% !important;
    height: 160px !important;
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
.month-row {
  padding-left: 15px;
}
.alerts-border {
    border: 2px #ff0000 solid;
    animation: blink 1s;
    animation-iteration-count: 5;
}

@keyframes blink { 50% { border-color:#fff ; }  }
</style>
<section class="content">
    <div class="container-fluid">
      
        <div class="error-messages"></div>
        <div class="block-header">
            <div class="col-sm-2">
            <h2>New Arrival</h2>
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
                          {{-- <form method="get" action="{{route('new.designer.product.image')}}">
                            <input type="hidden" name="previous_month" value="{{$previousMonth}}">
                            <input type="hidden" name="current_month" value="{{$currentMonth}}">
                            <button type="submit" name="previous" value="previous" class="btn btn-info previous-btn {{($active_tab == 'previous') ? 'btn-month-active' : ''}}">Previous Month</button>

                            <button type="submit" name="current" value="current" class="btn btn-info previous-btn {{($active_tab == 'current') ? 'btn-month-active' : ''}}">Current Month</button>
                          </form> --}}
                          <div class="row">
                            <div class="col-12 month-row m-2">
                              <div class="col-xs-2 form-div">
                                <a href="{{URL::to('/performance/employee-performance/designer/new/product/image/pending')}}" class="<?php if(stripos(Request::path(), 'performance/employee-performance/designer/new/product/image/pending') === 0) {?> btn-month-active <?php } ?> link-tabs btn btn-info"><div class="tab-box">Pending</div></a>
                              </div>
                              <div class="col-xs-2 form-div">
                                <form method="get" action="{{route('new.designer.product.image')}}">
                                  <input type="hidden" name="previous_month" value="{{$previousMonth}}">
                                  <input type="hidden" name="current_month" value="{{$currentMonth}}">
                                  {{-- <button type="submit" name="previous" value="previous" class="btn btn-info previous-btn {{($active_tab == 'previous') ? 'btn-month-active' : ''}}">Previous Month</button> --}}
  
                                  <button type="submit" name="current" value="current" class="btn btn-info previous-btn {{($active_tab == 'current') ? 'btn-month-active' : ''}}">Current Month</button>
                                </form>
                              </div>
                              @foreach($previousMonths as $k => $month)
                              <div class="col-xs-2 form-div">
                                <form method="get" action="{{route('new.designer.product.image')}}">
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
                                          <th scope="col" class="calendar-col" style="width:7% !important;font-weight: 700;"><center>{{$day['display_date']}}</center></th>
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
                                                  <td class="argn-popup-td posting-status" style="vertical-align: middle;border-right: 1px solid #2296f3;">
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
                                                       $designed = 0;
                                                       $posted = 0;
                                                       $diabale_action = "";
                                                       $scheduled_id = $new_arrivals_data[$day['hiddn_date']][$i]['group_id'];
                                                       $group_name = $new_arrivals_data[$day['hiddn_date']][$i]['group_name'];
                                                      //  break;
                                                      @endphp 
                                                      
                                                    @endif

                                                  @endforeach
                                                  <td class="argn-popup-td posting-status" style="vertical-align: middle; width:7% !important;border-right: 1px solid darkgray;">
                                                    <center>
                                                  
                                                  @if($exist)
                                                  <a href="javascript:;" onclick="viewProductDetails('{{$scheduled_id}}','{{$group_name}}')"><div class="row group-name light-red post-title{{$scheduled_id}}">
                                                    
                                                    {{ @$group_name }} 
                                                    {{-- {{$i}} --}}
                                                     {{-- {{$day['hiddn_date']}} {{$k}} --}}
                                                  </div></a>
                                                  <div class="row">
                                                    <div class="col-sm-12 btn-designed-icon{{$scheduled_id}}">
                                                      
                                                        @if( $diabale_action == "" )
                                                          @if( $desining_permission )
                                                            <a href="javascript:;" onclick="postOrDesign('{{$scheduled_id}}','{{$group_name}}')" class="btn btn-xs btn-info btn-designed btn-designed{{$scheduled_id}}">Design</a>
                                                          @endif
                                                        @else
                                                          <button class="btn btn-xs btn-secondary " {{ $diabale_action  }}>Design</button>
                                                        @endif
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
          <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;margin-top:18px;">Product Details <span id="changed-group" style="color: green;"></span></h5>
          <button type="button" class="close close-modal close-product-details-modale" aria-label="Close">
             <span aria-hidden="true">&times;</span>
          </button>
          <span id="top-title"></span>
        </div>
        <div class="modal-body" >
          <div class="detail-div"></div>
          <div class="row text-center loader-teg" style="display: none;">
            Please wait while image is loading..
            
          </div>
      </div>
    </div>
  </div>
  </div>
</section>

@endsection
@push('scripts')

{{-- <link rel="stylesheet" href="{{URL::asset('assets/plugins/select2/select2.min.css') }}">
<script src="{{URL::asset('assets/plugins/select2/select2.full.min.js') }}"></script> --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    var left = $('.wrapper').width();
    console.log(left);
    $('.wrapper').scrollLeft(3225);
  });
  $('.close-product-details-modale').on('click', function() {
    $('#new_product_detail_modal').modal('toggle');
  })
  function showUserProgress(user_id){
    console.log();
    $('.all-user-progress').hide();
    $('#progress_details_'+user_id).show();
    $('.all-user-progress-button').removeClass('active');
    $('#sale_person_name'+user_id).addClass('active');
  }
  
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
  $('#new_product_detail_modal').modal('toggle');
  $('.loader-tag').css('display', 'block');
  if(group) {
    $.ajax({
        url: "{{url('/performance/view/new/arrival/product/detail')}}/"+ group,
        type: "GET",
        cache: false,
        success: function(resp) {
          if(resp.status) {
          $('.loader-tag').css('display', 'none');
            var html = '';
            html += '<div class="col-xs-12">';
            resp.products.forEach(function callback(v, k) {
              var cl = (v.designed == 0) ? 'alerts-border' : '';
              var url = '';
              url = APP_URL + "/uploads/inventory_products/"
              url = url + v.image;
              html += '<div class="col-xs-4 col-grid product-block post-title">' +'\n'+
                                    '<img id="uploadable" src="'+url+'" class="img-thumbnail '+cl+'" width="100%" height="150px">' +'\n'+
                                    '<strong></strong>' +'\n'+
                                    '<div class="row">' +'\n'+
                                        '<div class="col-sm- btn-designed-icon text-center" style="padding-top: 3px;">'+'\n'+
                                            '<span><strong>'+v.sku+' | '+v.option_name+'</strong></span>'+'\n'+
                                        '</div>'+'\n'+
                                      '</div>'+'\n'+
                                  '</div>';
            });
            html += '</div>';
            $('.detail-div').html(html);
            
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
 function postOrDesign(id, action) {
    var middelText = '';
    var btn = '';
    $('.btn-designed'+id).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
    $('.btn-designed'+id).prop('disabled', true);
    $('.btn-designed').prop('disabled', true);
    if(id) {
      $.ajax({
        url: "{{url('/performance/employee-performance/designer/design/new/arrival/product/image')}}/"+ id,
        type: "GET",
        cache: false,
        success: function(resp) {
          if(resp.status) {
            $('.alert-success').css('display', 'block');
            $('.alert-success').text(resp.mesg);
            $('.post-title'+id).addClass('light-green');
            $('.btn-designed'+id).remove();
            $('.btn-designed-icon'+id).html('<i class="fa fa-check-circle green success-fs " title="'+action+'"></i>');
            $('.btn-designed').prop('disabled', false);

            setTimeout(() => {
              $('.alert-success').css('display', 'none');
              $('.alert-success').text('');
            }, 4000)
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
</script> 
@endpush