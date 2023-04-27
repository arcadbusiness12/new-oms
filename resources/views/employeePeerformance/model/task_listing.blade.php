@extends('layout.theme')
@section('title', 'Home')
@section('content')
<style>
  .btn-designed{
    padding: 5px;
    margin: 3px;
    width: 100%;
  }
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
    margin-top: -8px;
    padding: 20px;
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
      @php
      $user_perms        = json_decode(session('access'),true);
      @endphp
        <div class="error-messages"></div>
        <div class="block-header">
            <div class="col-sm-2">
            <h2>Model duty lists</h2>
            </div>
            
            <div class="clearfix"></div>
        </div>
        <div class="row">
          <div class="col-sm-12 tab-links">
            <!-- <a href="{{ route('df.orders') }}" target="_blank"><div class="tab-box">DressFair</div></a> -->
       
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
                          <div class="row">
                          <div class="col-12 month-row">
                            <div class="col-2 form-div">
                              <form method="get" action="{{route('employee-performance.model.product-shoot')}}">
                                <input type="hidden" name="previous_month" value="{{$previousMonth}}">
                                <input type="hidden" name="current_month" value="{{$currentMonth}}">
                                {{-- <button type="submit" name="previous" value="previous" class="btn btn-info previous-btn {{($active_tab == 'previous') ? 'btn-month-active' : ''}}">Previous Month</button> --}}

                                <button type="submit" name="current" value="current" class="btn btn-info previous-btn {{($active_tab == 'current') ? 'btn-month-active' : ''}}">Current Month</button>
                              </form>
                            </div>
                            @foreach($previousMonths as $k => $month)
                            <div class="col-2 form-div">
                              <form method="get" action="{{route('employee-performance.model.product-shoot')}}">
                                <input type="hidden" name="previous_month" value="{{$month['month']}}">
                                <input type="hidden" name="current_month" value="{{$currentMonth}}">
                                <button type="submit" name="previous" value="{{$month['name']}}" class="btn btn-info previous-btn {{($active_tab == $month['name']) ? 'btn-month-active' : ''}} {{ ($k > 1) ? 'hidden-xs' : '' }}">{{$month['name']}}</button>

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
                                          @if(@$days)
                                          @foreach($days as $k => $day)
                                          <th scope="col" class="calendar-col" style="width:20% !important"><center>{{$day['display_date']}}</center></th>
                                          @endforeach
                                          @endif
                                        </tr>
                                
                                      </thead>
                                
                                      <tbody>
                                          @php
                                            $shoot = "";
                                            $group_color_class = "";
                                            $desining_permission = true;
                                            $posting_permission = true;
                                            $diabale_action = 5;
                                            $posting_details = [];
                                          @endphp
                                          
                                            @php $group = ''; $counter = 0 ;@endphp
                                                
                                            @for ($i = 0; $i < $row_num; $i++)
                                                
                                            {{-- @foreach(@$new_arrivals_data as $key => $post1) --}}
                                              @php $counter+1 ;@endphp
                                          <tr id="" style="border-top: 1px solid gray !important;">
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
                                                       $shoot = $new_arrivals_data[$day['hiddn_date']][$i]['video_shoot'];
                                                       $posted = 0;
                                                       $diabale_action = "";
                                                       $scheduled_id    = $new_arrivals_data[$day['hiddn_date']][$i]['product_group_id'];
                                                       $group_name      = $new_arrivals_data[$day['hiddn_date']][$i]['product_group_name'];
                                                       $posting_details = $new_arrivals_data[$day['hiddn_date']][$i]['modelingPosting'];
                                                       
                                                       $task_id = $new_arrivals_data[$day['hiddn_date']][$i]['id'];
                                                      //  break;
                                                      @endphp 
                                                      
                                                    @endif

                                                  @endforeach
                                                  @php
                                                      if( $shoot == 1 ){
                                                       $group_color_class = "light-orange";
                                                      }
                                                      if( count($posting_details) > 0 ){
                                                       $group_color_class = "light-green";
                                                      }
                                                  @endphp
                                                  <td class="argn-popup-td posting-status" style="vertical-align: middle; width:15% !important">
                                                    <center>
                                                  
                                                  @if($exist)
                                                  <a href="javascript:;" onclick="viewProductDetails('{{$scheduled_id}}','{{$group_name}}')">
                                                    <div class="row group-name {{ $group_color_class  }}  post-title{{$scheduled_id}}">
                                                    <strong>{{ @$group_name }}</strong> 
                                                    {{-- {{$i}} --}}
                                                     {{-- {{$day['hiddn_date']}} {{$k}} --}}
                                                    </div>
                                                  </a>
                                                    <div class="col-sm-12 btn-designed-icon{{$scheduled_id}}">
                                                      @if( $shoot == 0 )
                                                          @if( session('user_group_id') == 1 || isset($user_perms['employee-performance/model/save-shoot-data']) )
                                                            <a href="javascript:;" onclick="shootProduct('{{$scheduled_id}}','{{$group_name}}',{{ $task_id }})" class="btn btn-xs btn-warning btn-designed btn-designed{{$scheduled_id}}"> <i class="material-icons">camera_alt</i> Shoot</a>
                                                          @else
                                                            <span class="label label-danger">Shoot permission denied.</span>
                                                          @endif
                                                      @else
                                                          @forelse($social_channel as $key => $val)
                                                            @php
                                                            $social_already_posted = 0;
                                                            $material_icon = "";
                                                            $button_color = "";
                                                            if( $val->id == 1 ){
                                                              $material_icon = '<i class="material-icons">facebook</i>';
                                                              $button_color = "#4267B2";                                                           
                                                            }elseif($val->id == 2){
                                                              $material_icon = '<i class="material-icons">tiktok</i>'; 
                                                              $button_color = "#000000";                                                           
                                                            }elseif($val->id == 3){
                                                              $material_icon = '<i class="fa fa-pinterest-p"></i>';
                                                              $button_color = "#E60023";                                                           
                                                            }elseif($val->id == 4){
                                                              $material_icon = '<i class="fa fa-instagram"></i>';
                                                              $button_color = "#8a3ab9";                                                           
                                                            }elseif($val->id == 5){
                                                              $material_icon = '<i class="fa fa-instagram"></i>';
                                                              $button_color = "#000000";
                                                              $button_color = "#8a3ab9";                                                           
                                                            }elseif($val->id == 6){
                                                              $material_icon = '<i class="fa fa-youtube-play"></i>';
                                                              $button_color = "#FF0000";                                                           
                                                            }
                                                            if( count($posting_details) > 0 ){
                                                              //echo $group_name;
                                                              //echo "<pre>"; print_r($posting_details->toArray());
                                                              foreach($posting_details as $posting_key => $posting_value){
                                                                if( $posting_value->promotion_social_id == $val->id ){
                                                                  $social_already_posted = 1;
                                                                  break;
                                                                }
                                                              }
                                                            }
                                                            @endphp
                                                              @if($social_already_posted)
                                                                <span class="label label-success">{{ $val->name  }}</span>
                                                              @else
                                                                @if( session('user_group_id') == 1 || isset($user_perms['employee-performance/model/save-shoot-posting']) )
                                                                  <a href="javascript:;" onclick="postProducts('{{$scheduled_id}}','{{$group_name}}',{{ $task_id }},{{ $val->id }})" style="background:{{ $button_color }} !important" class="btn btn-xs btn-info btn-designed btn-designed{{$scheduled_id.$val->id}}">{!! $material_icon !!} {{ $val->name  }} </a>
                                                                @else
                                                                  <span class="label label-danger">Posting permission denied.</span>
                                                                  @break;
                                                                @endif
                                                              @endif
                                                          @empty
                                                            <p>No social media found.</p>
                                                          @endforelse
                                                      @endif
                                                    </div>
                                                  @else
                                                    -
                                                  @endif
                                                    </center>
                                                      
                                                  </td>
                                                  @endforeach
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
function postProducts(product_group_id,product_group_name,task_id,social_id){
  var id = product_group_id;
  $.ajax({
    url: "{{ route('employee-performance.model.saveShootPosting') }}",
    type: "POST",
    data: { product_group_id : product_group_id,product_group_name:product_group_name,task_id:task_id,social_id:social_id },
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    cache: false,
    success: function(resp) {
      console.log(resp);
      // return;
      if(resp.status) {
        $('.alert-success').css('display', 'block');
        $('.alert-success').text(resp.msg);
        $('.post-title'+id).addClass('light-green');
        $('.btn-designed'+id+social_id).remove();
        $('.btn-designed').prop('disabled', false);

        setTimeout(() => {
          $('.alert-success').css('display', 'none');
          $('.alert-success').text('');
        }, 4000)
      }else {
        
      }
      
    }
  });
}
function shootProduct(product_group_id,product_group_name,task_id){
  var id = task_id;
  $.ajax({
    url: "{{ route('employee-performance.model.saveShootData') }}",
    type: "POST",
    data: { product_group_id : product_group_id,product_group_name:product_group_name,task_id:task_id },
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    cache: false,
    success: function(resp) {
      console.log(resp);
      // return;
      if(resp.status) {
        $('.alert-success').css('display', 'block');
        $('.alert-success').text(resp.msg);
        $('.post-title'+id).addClass('light-green');
        $('.btn-designed'+id).remove();
        $('.btn-designed').prop('disabled', false);

        setTimeout(() => {
          $('.alert-success').css('display', 'none');
          $('.alert-success').text('');
        }, 4000)
        location.reload();
      }else {
        
      }
      
    }
  });
}
function viewProductDetails(group, name) {
  console.log(group+" "+name);
  $('#new_product_detail_modal').modal('toggle');
  $('.loader-tag').css('display', 'block');
  if(group) {
    $.ajax({
        url: "{{url('/new/arrival/product/list/detail')}}/"+ group,
        type: "GET",
        cache: false,
        success: function(resp) {
          if(resp.status) {

          $('.loader-tag').css('display', 'none');
            var html = '';
            html += '<div class="col-xs-12">';
            resp.products.forEach(function callback(v, k) {
              // console.log(v.products_sizes);
              var option_html = '';
              v.products_sizes.forEach(function callback(op_v, op_k) {
                
                option_html += '<div class="box-label"><span>'+op_v.oms_option_details.value+'</span></div> | ';
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
 function postOrDesign(id, action = null) {
    console.log(action);
    var middelText = '';
    var btn = '';
    $('.btn-designed'+id).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
    $('.btn-designed'+id).prop('disabled', true);
    $('.btn-designed').prop('disabled', true);
    if(id) {
      $.ajax({
        url: "{{url('/employee-performance/marketer/listing/new/product')}}/"+ id,
        type: "GET",
        cache: false,
        success: function(resp) {
          console.log(resp);
          // return;
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
            $('.btn-designed'+id).html('List');
            $('.btn-designed'+id).prop('disabled', false);
            ba = '';
            df = '';
            $('.alert-warning').css('display', 'block');
            // $('.alert-warning').text('Somethings went wrong please try again.');
            if(!resp.ba_flag && !resp.df_flag) {
              var mesge = 'In Business Arcade <strong style="color:#f80904;">'+resp.bamissing_products.join()+'</strong> and in Dressfair <strong style="color:#f80904;">'+resp.dfmissing_products.join()+'</strong>';
            }
            if(!resp.ba_flag && resp.df_flag) {
              var mesge = 'In Business Arcade <strong style="color:#f80904;">'+resp.bamissing_products.join()+'</strong>';
            }
            if(!resp.df_flag && resp.ba_flag) {
              var mesge = 'In Dressfair <strong style="color:#f80904;">'+resp.dfmissing_products.join()+'</strong>';
            }
            var is_are = (!resp.ba_flag && !resp.df_flag || (resp.bamissing_products.length > 1 || resp.dfmissing_products.length > 1)) ? 'are' : 'is';
           
            var w_megs = mesge +' '+is_are+' not found, please make sure these products are listed or the sku '+is_are+' connected.';
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
</script> 
@endpush