@extends('layout.theme')

@section('title', 'Home')

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
    background-color: #fafafa !important;
    color: black;
    border: 1px solid black !important;
    
  }
  .first-top {
    border: none !important;
    
    left: 0;
  }
  .first-col {
    width: 40px;
    min-width: 77px;
    max-width: 77px;
    /* left: 0px; */

  }
  
  .second-col {
    width: 555px;
    min-width: 155px;
    max-width: 100px;
    left: 0;
  }
  .third-col {
    /* width: 69px;
    min-width: 69px; */
        width: 235px;
    min-width: 235px;
    max-width: auto;
    left: 155;
  }  
  .calendar-col {
    width: 104;
    min-width: 138.7px;
    max-width: 104;
    border-bottom: 1px solid #323131 !important;
  }  
  .range-text {
        margin-left: 35px;
  }
  .current-td {
    background-color: #00800021;
  }
  .previous-td {
    background-color: #ff00002b;
  }
  .next-td {
    background-color: #ffa5004d;
  }
  .end-date{
    color: black;
  }
  .table-td-text {
    font-size: 1.4em !important;
  }
  .top-row {
    border-bottom: 1px solid #2e2d2d !important;
  }
  .list-inner a{
    padding: 15px;

  }
  .list-active a{
    color:green;
    border:1px solid green;
    border-radius: 10px;
    -webkit-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
    -moz-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
    box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
  }
  
</style>
<section class="content">
 <div class="container-fluid">
   @if(session()->has('error'))
   <div class="alert alert-danger">
     {{ session()->get('error') }}
   </div>
   @endif
   @if(session()->has('success'))
   <div class="alert alert-success">
     {{ html_entity_decode(session()->get('success')) }}
   </div>
   @endif
  <div class="box">
    <div class="panel panel-default">
      <div class="panel-body">
        @forelse($all_lists as $key => $list)
          <div class="col-sm-2 list-inner {{ $id == $list->id ? 'list-active' : ''  }}"><a href="{{ route('employee-performance.marketing.save-add-chat',$list->id) }}">{{  $list->setting_name }}-{{ $list->user['firstname'] }}</a></div>
        @empty
        @endforelse
      </div>
    </div>
  </div>
<div class="box">
  <div class="panel panel-default" id="print-report">
    <div class="panel-body">
      <div class="col-12">
        <div class="col-5" style="display:inline-block; width:35%">
        <h5 style="display:inline-block;">{{@$templates->setting_name}} ({{@$templates->title}})</h5>
        </div>
        <div class="col-4" style="display:inline-block;width:42%;">
            
        </div>
      </div>
      @if(count($product_pro_posts) > 0)
        <div class="col-12">
          <span><label>Budget Type: </label> {{@$templates->budgetType['name']}}</span>,
          <span class="range-text"><label>Range: </label> {{ @$templates->range }}</span>,
          <span class="range-text"><label>Ad Type: </label> {{ @$templates->adsType->name }}</span>,
          <span class="range-text"><label>User: </label> {{ @$templates->user->firstname }} {{ @$templates->user->lastname }}</span>
        </div>
      @endif
    <div class="col-4 new-setting-btn" style="display:inline-block; color:green;"> 
          <h5 id="success" style="display: none;">Schedule changed successfully.</h5>
        </div>   
      <div class="table-responsive">
      <div class="wrapper">
        <form method="post" action="">
          {{ csrf_field() }}
        <table class="table"  style="border: 1px solid #2196f3">
        <input type="hidden" name="main_id" value="{{$id}}">
            <thead >
             {{--  <tr id="head_row" style="background-color:#fafafa">

             <th scope="col" colspan="2" class="sticky-col first-top text-right" style="" class="top-row"></th>
                <th scope="col" colspan="3" style="border-right: 1px solid #2e2d2d;" class="top-row"><center>Previous</center></th>
                
               
                <th colspan="3" style="border-right: 1px solid #2e2d2d;" class="top-row"><center>Current</center></th>
                </span>
                <span>
                <th scope="col" colspan="3" class="top-row"><center>Coming</center></th>
                </span>
                
                
              </tr>  --}}
              <tr id="head_row" style="background-color:#fafafa">

                <th scope="col" class="sticky-col second-col"><center>Type </center></th>
                <th scope="col" class="sticky-col third-col"><center>Category</center></th>
                
                <th scope="col" class="calendar-col"><center>Active</center></th>
                <th scope="col" class="calendar-col"><center>Paused</center></th>
                <th scope="col" class="calendar-col"><center>Budget</center></th>
                <th scope="col" class="calendar-col" style="border-right: 1px solid;"><center>Chat</center></th>
                <th scope="col" class="calendar-col"><center>Schedule</center></th>
                <th scope="col" class="calendar-col"><center>Budget</center></th>

                </span>
                
                
              </tr>
            </thead>

            <tbody id="body-dataa">
            @php $socials =   $template_socials != "" ? implode(",", $template_socials) : ""; @endphp
            @if(count($product_pro_posts) > 0)
            @php $start = ''; $end = ''; $next_start = ''; $next_end = ''; @endphp
                @foreach(@$product_pro_posts as $key=>$product_pro_post)
                
                  @php $group = ''; $group_name = ''; $chat_recived = 0;  @endphp
                <tr id="" style="border-top: 1px solid gray !important;">
                        <td class="argn-popup-td sticky-col second-col" style="vertical-align: middle;"><center><label>{{$product_pro_post->type->name}}</label></center></td>
                        <td class="argn-popup-td sticky-col third-col" style="vertical-align: middle;"><center><label>{{$product_pro_post->category}}</label> {{($product_pro_post->subCategory) ? '('.$product_pro_post->subCategory->name.')' : ''}}</center></td>

                        <td class="argn-popup-td current-td" style="vertical-align: middle;">
                         <center>
                            <table style="width:100%">
                            <tr>
                              <td style="border-top: none" class="table-td-text">
                              <center>
                                @forelse($pro_posts as $ad_row)
                                  @if($product_pro_post->id == $ad_row->setting_id && $id == $ad_row->main_setting_id && $product_pro_post->promotion_product_type_id == $ad_row->product_type_id && $product_pro_post->range == $ad_row->range)
                                    @if($ad_row->posting == 1)                                    
                                    @php
                                            $group_name   = $ad_row->group_code;
                                            $group_id   = $ad_row->group_id;
                                            $current_post_id = $ad_row->id;
                                            $start   = $ad_row->date;
                                            $end   = $ad_row->last_date;
                                            $post_range   = $ad_row->range;
                                            $chat_recived = $ad_row->chat_recieved;
                                            $posting = $ad_row->posting;
                                        @endphp
                                        <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="Running" class="" data-target=".setting_view_modal" id="schedule_row_" onclick="changeSchedule('{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->promotion_product_type_id}}', '{{$ad_row->group_code}}','{{$group_id}}','{{$current_post_id}}','{{$socials}}', '{{$start}}', '{{$end}}','{{$store}}','{{$post_range}}', 1,'current', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                        {{ $ad_row->group_code }} 
                                      </a>  
                                        @break;
                                      @endif
                                    @endif
                                @empty
                                  
                                @endforelse
                                @if( $group_name == "" )
                               <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="Add Schedule" class="" data-target=".setting_view_modal" id="schedule_row_" onclick="getNewForEmptyDaySchedule('{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->category_id}}','{{$product_pro_post->promotion_product_type_id}}','{{$socials}}', '{{$store}}','{{$product_pro_post->range}}','{{($product_pro_post->budget) ? $product_pro_post->budget : 0}}','{{$start}}','{{$end}}', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                <i class="fa fa-plus-circle"></i></a>
                               @endif
                              </center>
                              </td>
                            </tr>
                          </table>
                         </center>
                        </td>
                        <td class="argn-popup-td current-td" style="vertical-align: middle;"><center>
                          <center>
                          <table style="width:100%">
                              <tr>
                                <td style="" class="table-td-text">
                                <center> 
                          @php  $code = ''; @endphp
                                @foreach($pro_posts as $ad_row)
                                  @if($product_pro_post->id == $ad_row->setting_id && $id == $ad_row->main_setting_id && $product_pro_post->promotion_product_type_id == $ad_row->product_type_id && $product_pro_post->range == $ad_row->range)
                                    @if($ad_row->posting == 0)
                                      <?php
                                      if($code == $ad_row->group_code) {
                                        continue;
                                      }
                                      else{
                                        $code = $ad_row->group_code ;
                                        ?>
                                    <tr>
                                      <td style="" class="table-td-text">
                                      <center> {{$ad_row->group_code}} </center>
                                      </td>
                                    </tr>
                                    <?php }?>
                                      
                                  @endif
                                @endif
                                @endforeach
                                <!-- @if( $group_name == "" )
                               <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="Add Schedule" class="" data-target=".setting_view_modal" id="schedule_row_" onclick="getNewForEmptyDaySchedule('{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->category_id}}','{{$product_pro_post->promotion_product_type_id}}','{{$socials}}', '{{$store}}','{{$product_pro_post->range}}','{{$product_pro_post->budget}}','{{$start}}','{{$end}}', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                <i class="fa fa-plus-circle"></i></a>
                               @endif -->
                                 </center>
                                </td>
                              </tr>
                            </table>
                          </center>
                       </td>
                      <td class="argn-popup-td current-td table-td-text" style="vertical-align: middle;">
                        <center>
                          {{ $product_pro_post->budget }}
                        </center>
                      </td>
                      <td class="argn-popup-td current-td table-td-text" style="vertical-align: middle;">
                        <center>
                          <input type="hidden" name="group[{{ $product_pro_post->id }}]" value="{{ $group_name }}" size="4">
                          <input type="text" name="chat[{{ $product_pro_post->id }}]" size="4" value="{{ $chat_recived }}">
                        </center>
                      </td>
                      {{-- schedule design start --}}
                      <td class="argn-popup-td next-td" style="vertical-align: middle;">
                        <center>
                           <table style="width:100%">
                           <tr>
                             <td style="border-top: none" class="table-td-text">
                             <center>
                               @php
                                  $next_group_name = "";
                               @endphp
                               @forelse($next_post_data as $next_post_row)
                                  @if( $next_start == "" OR $next_end == "")
                                    @php
                                      $next_start = $next_post_row->date; 
                                      $next_end   = $next_post_row->last_date; 
                                    @endphp
                                  @endif
                                 @if( $product_pro_post->id == $next_post_row->setting_id && $product_pro_post->promotion_product_type_id == $next_post_row->product_type_id )
                                   @php
                                       $next_start = $next_post_row->date; 
                                       $next_end   = $next_post_row->last_date;
                                       $next_group_name   = $next_post_row->group_code;
                                   @endphp
                                   @php
                                            $group_name   = $next_post_row->group_code;
                                            $group_id   = $next_post_row->group_id;
                                            $current_post_id = $next_post_row->id;
                                            $start   = $next_post_row->date;
                                            $end   = $next_post_row->last_date;
                                            $post_range   = $next_post_row->range;
                                            $chat_recived = $next_post_row->chat_recieved;
                                            $posting = $next_post_row->posting;
                                        @endphp
                                        <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="Running" class="" data-target=".setting_view_modal" id="schedule_row_" onclick="changeSchedule('{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->promotion_product_type_id}}', '{{$ad_row->group_code}}','{{$group_id}}','{{$current_post_id}}','{{$socials}}', '{{$start}}', '{{$end}}','{{$store}}','{{$post_range}}', 1,'current', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                        {{ $next_group_name }} 
                                      </a>  
                                   @break;
                                 @endif
                               @empty
                                
                               @endforelse
                               @if( $next_group_name == "" )
                               <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="" class="" data-target=".setting_view_modal" id="schedule_row_" onclick="getNewForEmptyDaySchedule('{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->category_id}}','{{$product_pro_post->promotion_product_type_id}}','{{$socials}}', '{{$store}}','{{$product_pro_post->range}}','{{$product_pro_post->budget}}','{{$next_start}}','{{$next_end}}', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                <i class="fa fa-plus-circle"></i></a>
                               @endif
                             </center>
                             </td>
                           </tr>
                         </table>
                        </center>
                       </td>
                       <td class="argn-popup-td next-td table-td-text" style="vertical-align: middle;">
                        <center>
                          {{ $ad_row->budget }}
                        </center>
                      </td>
                      {{-- schedule design end --}}

                </tr>
                @endforeach
                @else
                <tr id="tr_" style="border-top: 1px solid gray">

                <td class="column text-center" colspan="{{count($days )+2}}">
                    <center><label>No Running Paid Ad found.</label></center>
                </td>
                </tr>
                @endif
          </tbody>
       </table>
       @if( count($product_pro_posts) > 0 )
        <input type="submit" class="btn btn-success pull-right" name="update-chat" value="Update Chat">
       @endif
      </form>
      </div>


</div>
</div>
</div>
</div>

</div>
  <!--  Setting modal end -->
  <div class="modal fade setting_view_modal" id="promotion_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 60%">
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Test Change Schedule/ <span id="changed-group"></span></h5>
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
</section>



@endsection
@push('scripts')

<script>

$(document).ready(function(){
console.log("OKkkkkkkkkkkkkkk");
// var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
// removeItemButton: true,
// maxItemCount:5,
// searchResultLimit:5,
// renderChoiceLimit:5
// });
$('.selectpicker').selectpicker();
var left = $('.wrapper').width();console.log(left);
$('.wrapper').scrollLeft(3225);

});
function getRange(value, range) {
  if(range != 'Ongoinig') {
    var day = range.split(' ');
    day = day[0]-1;
    // console.log(day);
    value = new Date(value);
    var someDate = new Date(value.getTime() + day*24*60*60*1000) //number  of days to add,
    var end_date = someDate.toISOString().substr(0,10);
    $('#date_modified').val(end_date);
  }
}
function changeSchedule(main_setting_id,setting_id,type, category, group_type, group_code,group_id,post_id,socials,date,end_date,store,range, duration, action, sub_category = null) {
  console.log(range);
    $('.modal-content-loader').css('display', 'block');
  $('#changed-group').text(group_code);
  $.ajax({
    url: "{{url('/promotion/get/auto/new/schedule')}}/"+main_setting_id +"/"+setting_id +"/"+type +"/"+ category +"/"+group_type +"/"+ group_code+"/"+ group_id +"/"+ post_id+"/"+socials+ "/" +date +"/"+end_date +"/"+ store+ "/" +2 +"/"+range+"/"+duration+"/"+action+"/"+sub_category,
    type: "GET",
    cache: false,
    success: function(resp) {
      $('.modal-content-loader').css('display', 'none');
      
        $('#schedule_view_content').html(resp);
    }
  })
}
function getNewForEmptyDaySchedule(row,main_setting_id,setting_id,type, category,category_ids, group_type,socials,store, range,budget, start_date = null, end_date = null, sub_category=null) {
    $('.modal-content-loader').css('display', 'block');
  $.ajax({
    url: "{{url('/promotion/get/new/schedule/For/marketing/empty/ads')}}/"+row +"/" +main_setting_id +"/"+setting_id +"/"+type +"/"+ category  +"/"+ category_ids +"/"+group_type +"/"+socials +"/"+ store+ "/" +2+"/"+range+"/"+ budget+"/"+ start_date+"/"+ end_date+"/"+sub_category,
    type: "GET",
    cache: false,
    success: function(resp) {
      console.log(resp);
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
<style type="text/css">
  .td-valign{
    vertical-align: middle !important;
  }
</style>