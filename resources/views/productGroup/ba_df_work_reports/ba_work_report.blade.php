@extends('layouts.app')

@section('content')
<style>
    .light-red{
    /* background: #ff00002b; */
  }
  .light-orange{
    background:#ffa5003d;
  }
  .light-green{
    background:#00800021;
  }
  .view {
    margin: auto;
    width: 100%;
  }
  .wrapper table td, .wrapper table th{
    z-index: 1 !important;
    vertical-align: middle !important;
   }
  .wrapper {
    position: relative;
    overflow: auto;
    border: 1px solid black;
    white-space: nowrap;
  }
  
  .sticky-col {
    position: -webkit-sticky;
    position: sticky;
    z-index: 2 !important;
    background-color: lightgray !important;
    color: black;
    border: 1px solid black !important;
    
  }
  
  .first-col {
    width: 78px;
    min-width: 78px;
    max-width: 78px;
    left: 0px;

  }
  
  .second-col {
    width: 91;
    min-width: 91;
    max-width: 91;
    left: 78;
  }
  .third-col {
    width: 69px;
    min-width: 69px;
    max-width: auto;
    left: 169;
  }  
  .calendar-col {
    width: 104;
    min-width: 104;
    max-width: 104;
  } 

  .entry-content {
    width: 400px;
    height: 250px;  
    margin: 0 auto;
    margin-top: 40px;
    -webkit-column-count: 3;
    overflow-y: auto;
    border: 1px solid;
    padding: 0 15px;
} 
</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 col-sm-12">

                    <div class="card no-b">
                      <div class="tbl-title">
                        <a href="{{ route('ba.work.history',[$id,$store,$post_type]) }}" class="btn btn-success pull-left" style="display:block;">Switch to History</a>
                        <br><br>
                        <h5 style="">{{($store == 1) ? 'Business Acade' : 'DressFair'}} work of {{$templates->title}}</h5>
                      </div>
                      
                    <div class="main-div"  style="
                    overflow: overlay;">
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                          {{-- <div class="table-responsive"> --}}
                           <div id="status_changed_msg" style="display: none"></div>
                           
                           <table class="table"  style="border: 1px solid #2196f3">
                            <input type="hidden" name="main_id" value="{{$id}}">
                                <thead >
                                  <tr id="head_row" style="background-color:lightgray">
                    
                                    <th scope="col" class="sticky-col first-col"><center>Post</center></th>
                                    <th scope="col" class="sticky-col second-col"><center>Type </center></th>
                                    <th scope="col" class="sticky-col third-col"><center>Category</center></th>
                                    @if(@$days)
                                    @foreach($days as $k => $day)
                                    <th scope="col" class="calendar-col calendar_{{$day['hiddn_date']}} {{($day['hiddn_date'] < date('Y-m-d')) ? 'print_hidden' : 'print_show'}}" id="calendar_{{$day['hiddn_date']}}" style="min-width: 77px;"><center>{{$day['display_date']}}</center></th>
                                    @if($day['hiddn_date'] == $next_seven_day)
                                    @break
                                    @endif
                                    @endforeach
                                    @endif
                                  </tr>
                                    @php $socials = implode(",", $template_socials); @endphp
                                </thead>
                    
                                <tbody>
                                @if(count($product_pro_posts) > 0)
                    
                                    @foreach(@$product_pro_posts as $key=>$product_pro_post)
                                    
                                      @php $group = ''; @endphp
                                    <tr id="" style="border-top: 1px solid gray !important;">
                                            <td class="argn-popup-td sticky-col first-col" style="vertical-align: middle;"><center><label>{{ date('h:i a', strtotime($product_pro_post->schedule_time)) }}</label></center></td>
                                            <td class="argn-popup-td sticky-col second-col" style="vertical-align: middle;"><center><label>{{$product_pro_post->type->name}}</label></center></td>
                                            <td class="argn-popup-td sticky-col third-col" style="vertical-align: middle;"><center><label>{{$product_pro_post->category}}</label></center></td>
                                           
                                            @foreach(@$days as $k => $day)
                                            @php
                                            $exist = false; 
                                            $test = '';
                                            @endphp
                                            @foreach(@$pro_posts as $post)
                                              @if($product_pro_post->schedule_time == $post->time && $day['hiddn_date'] == date('Y-m-d', strtotime($post->date)) && $product_pro_post->id == $post->setting_id && $id == $post->main_setting_id && $product_pro_post->promotion_product_type_id == $post->product_type_id)
                                                @php
                                                 $exist = true;
                                                 $multi_post = [];
                                                 $group_color_class = "light-red";
                                                $designed = 0;
                                                $posted = 0;
                                                if( $post->designed ==1 && $post->posted != 1 ){
                                                  $group_color_class = "light-orange";
                                                  $designed = 1;
                                                  $posted = 0;
                                                }elseif( $post->designed ==1 && $post->posted == 1 ){
                                                  $group_color_class = "light-green";
                                                  $designed = 1;
                                                  $posted = 1;
                                                }
                                                $scheduled_id = $post->id;
                                                                           
                                                 $group_name = $post->group['name'];
                                                 $post_id = $post->id;
                                                 $group_id = $post->group_id;
                                                 if(count($post->promo_cate_posts) > 0) {
                                                   foreach ($post->promo_cate_posts as $key => $value) {
                                                     $m_post = array_push($multi_post, $value->group_code);
                                                    //  echo $value;
                                                   }
                                                  //  print_r($multi_post); die;
                                                  $group_name = implode(',',$multi_post);
                                                 }
                                                 if(!isset($post->group['name'])) {
                                                   $group_name = $post->group_code;
                                                 }
                                                @endphp
                                              @endif
                                            @endforeach
                                            <td class="argn-popup-td {{ ($exist) ? $group_color_class : '' }} {{($day['hiddn_date'] < date('Y-m-d')) ? 'print_hidden' : 'print_show'}}" style="vertical-align: middle;border: 1px solid darkgray;">
                                              <center>
                                           
                                            @if($exist)
                                            <span class="row group-nam">
                                              @if($product_pro_post->is_deleted == 0)
                                              <a href="javascript:;" data-toggle="modal" data-target=".setting_view_modal" id="schedule_row_{{$post_id}}" data-toggle="tooltip" data-placement="top" title="<?php if($designed == 1 && $posted == 0){echo 'Designed';}elseif($designed == 1 && $posted == 1){echo 'Designed & Posted';} ?>" onclick="changeSchedule('{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->promotion_product_type_id}}', '{{$group_name}}','{{$group_id}}','{{$post_id}}','{{$socials}}','{{$day['hiddn_date']}}', '{{$store}}', '{{$product_pro_post->schedule_time}}')">{{$group_name}}</a>
                                              @else
                                              <a href="javascript:;" data-toggle="tooltip" data-placement="top" title="Deleted" >{{$group_name}}</a>
                                              @endif
                                              </span>
                                            @else
                                             @if($product_pro_post->is_deleted == 0)
                                              <a href="javascript:;" data-toggle="modal" data-target=".setting_view_modal" id="empty_schedule_row_{{$key}}{{$day['hiddn_date']}}" onclick="getNewForEmptyDaySchedule('{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->category_id}}','{{$product_pro_post->promotion_product_type_id}}', '{{$socials}}','{{$day['hiddn_date']}}', '{{$store}}', '{{$product_pro_post->schedule_time}}')"><i class="fa fa-plus-circle"></i></a>
                                             @else
                                              -
                                            @endif
                                           @endif
                                              </center> 
                                            </td>
                                            @if($day['hiddn_date'] == $next_seven_day)
                                            @break
                                            @endif
                                            @endforeach
                    
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr id="tr_" style="border-top: 1px solid gray">
                    
                                    <td class="column text-center" colspan="{{count($days )+2}}">
                                        <center><label>No post found..</label></center>
                                    </td>
                                    </tr>
                                    @endif
                              </tbody>
                           </table>

                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  Store social post modal end -->
<div class="modal fade setting_view_modal" id="promotion_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 60%">
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Change Schedule/ <span id="changed-group"></span></h5>
        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
        </button>
        <span id="top-title"></span>
      </div>
      <div class="modal-body" >
      <div class="modal-content-loader"></div>
        <div id="schedule_view_content">
        
        
      </div>
      <div class="modal-footer">
      </div>
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
console.log("Ok");
// $('.selectpicker').selectpicker('val', [510,511]);
// var cate = value.category_id.split(',');
//               $('.selectpicker'+value.id).selectpicker('val', cate);
// var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
// removeItemButton: true,
// maxItemCount:5,
// searchResultLimit:5,
// renderChoiceLimit:5
// });
// $('.selectpicker').selectpicker();
// var left = $('.wrapper').width();
//     console.log(left);
// $('.wrapper').scrollLeft(3225);
// $('.calendar_'+formattedDate).scrollr = 500;

});
function testing() {
  var one = 4;
  var two = 1;
  var three = '1,5';
  $.ajax({
    url: "{{url('promotion/ba/work')}}/"+one +"/"+ two+ "/" +three,
    type: 'GET',
    cache: false,
    success: function(re) {
      console.log("re");
    }
  })
}
function printReport(current_date) {
   $('.print_hidden').css('display','none');
  //  console.log(previous); return;
   var divToPrint=document.getElementById('print-report');
   var htmlToPrint = '' +
        '<style type="text/css">' +
        // 'table {' +
        // 'border-collapse: collapse;' +
        // '}'+
        // '.argn-popup-td {' +
        // 'border: 1px solid !important' +
        // '}'+
        '#head_row {' +
        'background-color:lightgray !important;'+
        '}' +
        '.new-setting-btn {' +
          'display: none !important;' +
          '}' +
          '.light-orange{'+
          'background:#ffa5003d;'+
          '}'+
          '.light-green{'+
          'background:#00800021;'+
          '}'+
        '</style>';
        
   htmlToPrint += divToPrint.outerHTML;
  //  console.log(htmlToPrint);return;
  var newWin=window.open('','Print-Window');

  newWin.document.open();

  newWin.document.write('<html><body onload="window.print()">'+htmlToPrint+'</body></html>');

  newWin.document.close();
  $('.print_hidden').css('display','revert')
}

function changeSchedule(main_setting_id,setting_id,type, category, group_type, group_code,group_id,post_id,socials,date,store,time) {
    $('.modal-content-loader').css('display', 'block');
  $('#changed-group').text(group_code);
  if(group_id) {
    group_id = group_id;
    // request_url = "{{url('/organic/promotion/get/new/schedule')}}/"+main_setting_id +"/"+setting_id +"/"+type +"/"+ category +"/"+group_type +"/"+ group_code+"/"+ group_id +"/"+ post_id+"/"+socials+ "/" +date +"/"+ store+ "/" +1+"/"+ time;
  }else {
    group_id = 0;
    // request_url = "{{url('/organic/multiple/promotion/get/new/schedule')}}/"+main_setting_id +"/"+setting_id +"/"+type +"/"+ category +"/"+group_type +"/"+ group_code +"/"+ post_id+"/"+socials+ "/" +date +"/"+ store+ "/" +1+"/"+ time;
  }
  console.log(post_id);
  $.ajax({
    url: request_url = "{{url('/organic/promotion/get/new/schedule')}}/"+main_setting_id +"/"+setting_id +"/"+type +"/"+ category +"/"+group_type +"/"+ group_code+"/"+ group_id +"/"+ post_id+"/"+socials+ "/" +date +"/"+ store+ "/" +1+"/"+ time,
    type: "GET",
    cache: false,
    success: function(resp) {
      $('.modal-content-loader').css('display', 'none');
      
        $('#schedule_view_content').html(resp);
    }
  })
}

function getNewForEmptyDaySchedule(row,main_setting_id,setting_id,type, category,category_ids, group_type,socials,date,store,time) {
    $('.modal-content-loader').css('display', 'block');
    console.log("Yes here");
  $.ajax({
    url: "{{url('/productgroup/organic/promotion/new/schedule/For/empty/day')}}/"+row +"/" +main_setting_id +"/"+setting_id +"/"+type +"/"+ category  +"/"+ category_ids +"/"+group_type +"/"+socials+ "/" +date +"/"+ store+ "/" +1+"/"+ time,
    type: "GET",
    cache: false,
    success: function(resp) {
      $('.modal-content-loader').css('display', 'none');
      
        $('#schedule_view_content').html(resp);
    }
  })
}
$('.close-modal').on('click', function() {
  $('.modal-content-loader').css('display', 'none');
  $('#schedule_view_content').html('');
})


</script>
@endpush