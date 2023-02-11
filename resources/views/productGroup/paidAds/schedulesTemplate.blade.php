
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
          /* width: 235px;
      min-width: 235px; */
      margin: auto;
      /* display: inline-block; */
      width: max-content;
      min-width: 177px;
      max-width: auto;
      border: 0px !important;
      max-width: max-content !important;
      left: 0;
      white-space: pre-line;
    }  
    .calendar-col {
      /* width: 104;
      min-width: 138.7px;
      max-width: 104; */
      width: max-content;
      min-width: 86.7px;
      max-width: none;
      border-bottom: 1px solid #323131 !important;
    }  
    .range-text {
          margin-left: 35px;
    }
    .current-td {
      background-color: #00800021 !important;
    }
    .previous-td {
      background-color: #ff00002b;
    }
    .next-td {
      background-color: #ffa5004d !important;
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
    
    .tab-links .active {
      color:green;
      -webkit-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
      -moz-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
      box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
    }
    .list-inner a {
      padding: 15px;
  }
  select.bs-select-hidden, select.selectpicker{
    display: inline-block !important;
  }
  .text-tag {
    font-size: 15px;
  }
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
      padding: 4px;
  }
  .next-campaign a h5{
    color: #2196f3 !important;
    font-weight: 700;
  }
  .next-campaign p{
    margin-bottom: 0px;
  }
  .template-section table>thead>tr>th {
    font-size: 14px !important;
    font-weight: 700 !important;
  }
  .current-campaign h5, .next-campaign h5{
    color: #129d0a !important;
  }
  .table-td-text a {
    color: #23bfd1;
    font-weight: 500;
  }
  .current-section, .next-section {
    border: 1px solid black;
  }
  .fa-circle-o-notch {
    font-size: 20px;
  }
  /* .no-data-msge {
    color: red;
  } */
  </style>
   <div class="">
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
     <div class="block-header">
      <div class="clearfix"></div>
  
    </div>
  <div class="box">
    <div class="panel panel-default" id="print-report">
      <div class="panel-body">
      <div class="col-4 new-setting-btn" style="display:inline-block; color:green;"> 
            <h5 id="success" style="display: none;">Schedule changed successfully.</h5>
          </div>   
        <div class="table-responsive template-section">
        <div class="wrapper">
          <caption class="text-center">
            @if($campaign_current)
               <span class="current-campaign"> <h5>{{$campaign_current->campaign_name}}</h5></span>  
            @endif
               <span class="next-campaign" style="display: none;">
                {{-- @if(count($product_pro_posts_next) > 0) --}}
                    @if($campaign_next)
                      <h5>{{$campaign_next->campaign_name}}</h5>
                    @else 
                      <p class="text-center">Please first create 'Campaign' for comming schedule</p>
                      <a href="{{route('performance.marketing.save.add.chat', ['comming', $id])}}"><h5>Create Campaign</h5></a>
                    @endif
                {{-- @endif --}}
              </span>
          </caption>
          <table class="table"  style="border: 1px solid #2196f3">
            
          <input type="hidden" name="main_id" value="{{$id}}">
              <thead >
               <tr id="head_row" style="background-color:#fafafa">
  
                 
                  <th colspan="7" style="border-right: 1px solid #2e2d2d;" class="top-row current-section"><center>Current</center></th>
                  </span>
                  <span>
                  <th scope="col" colspan="3" class="top-row next-main-th next-section" style="display: none;"><center>Coming</center></th>
                  </span>
                  
                </tr>
                <tr id="head_row" style="background-color:#fafafa">
  
                  <th scope="col" class="sticky-col third-col"><center>Category</center></th>
                 
                  <th scope="col" class="calendar-col current-section"><center>Active Ads</center></th>
                  <th scope="col" class="calendar-col current-section"><center>Paused Ads</center></th>
                  <th scope="col" class="calendar-col current-section" style="border-right: 1px solid ;"><center>Budget </center></th>
                  <th scope="col" class="calendar-col current-section" style="border-right: 1px solid;"><center>Chat</center></th>
                  <th scope="col" class="calendar-col current-section" style="border-right: 1px solid;"><center>Cost per chat</center></th>
                  <th scope="col" class="calendar-col current-section" style="border-right: 1px solid;"><center>Average Cost</center></th>
                  </span>
                  <span>
                  <th scope="col" class="calendar-col next-th next-section" style="display: none;"><center>Active Ads</center></th>
                  <th scope="col" class="calendar-col next-th next-section" style="display: none;"><center>Budget</center></th>
                  
                  </span>
                  
                </tr>
              </thead>
  
              <tbody id="body-dataa">
              @php $socials = implode(",", $template_socials); @endphp
  
              @if(count($product_pro_posts) > 0)
              @php $start = ''; $end = ''; $next_start = ''; $next_end = ''; 
             @endphp
                  @foreach(@$product_pro_posts as $key=>$product_pro_post)
                  
                    @php $group = '';$chat_recived = 0; $per_chat_cost = ''; $row_chat_history = 0; $av_cost = 0; $post_range = 0 ; $sCates = explode(',',$product_pro_post->sub_category_id);@endphp
                  @if(count(array_filter($sCates)) == 0 || in_array($category->sub_category_id, $sCates))
                  <tr id="" class="current-section" style="border-top: 1px solid gray !important;">
                          <td class="argn-popup-td sticky-col third-col" style="vertical-align: middle;"><center><label>{{$product_pro_post->category}} </label> {{($product_pro_post->subCategory) ? '('.$product_pro_post->subCategory->name.')' : ''}}</center></td>
  
                          <?php if($action == 'history') { 
                            foreach($history_blocks as $history_block) {
                            ?>
                          <td class="argn-popup-td previous-td" style="vertical-align: middle;">
                            <center>
                              <table style="width:100%">
                              @foreach($product_pro_post->previous_post as $k => $previous_post)
                              @if(@$previous_post->posting == 1 && $previous_post->date == $history_block->date)
                              <tr>
                                <td style="border-top: none" class="table-td-text">
                                  {{$previous_post->group_code}} 
                                </td>
                                
                              </tr>
                              @endif
                              @endforeach
                            </table>
                            </center>
                          </td>
                          <td class="argn-popup-td previous-td" style="vertical-align: middle;">
                            <center>
                                <table style="width:100%">
                                @php $code = ''; @endphp
                                @foreach($product_pro_post->previous_post as $k => $previous_post)
                                @if(@$previous_post->posting == 0 && $previous_post->date == $history_block->date)
                               
                               <tr>
                                <td style="" class="table-td-text">
                                 <center>  {{$previous_post->group_code}} </center>
                                </td>
                              </tr>
                                
                                @endif
                                @endforeach
                              </table>
                            </center>
                          </td>
                          <td class="argn-popup-td previous-td current-section table-td-text" style="vertical-align: middle;">
                          <center><span class="text-tag">{{(count($product_pro_post->previous_post) > 0) ? $product_pro_post->budget : ''}}</span></center>
                          </td>
                          <?php } } else {?>
                          <td class="argn-popup-td current-td current-section" style="vertical-align: middle;">
                           <center>
                             
                            @if(isset($product_pro_post->productPostes) && count($product_pro_post->productPostes) > 0)

                              @foreach(@$product_pro_post->productPostes as $k => $current_post)
                              
                              @php
                              $start = $current_post->date;
                              $end = $current_post->last_date;
                               $group_color_class = "light-red";
                               $chat_recived = $current_post->chat_recieved;
                               $post_range   = $current_post->range;
                              $designed = 0;
                              $posted = 0;
                              if( $current_post->designed ==1 && $current_post->posted != 1 ){
                                $group_color_class = "light-orange";
                                $designed = 1;
                                $posted = 0;
                              }elseif( $current_post->designed ==1 && $current_post->posted == 1 ){
                                $group_color_class = "light-green";
                                $designed = 1;
                                $posted = 1;
                              }
                              if($chat_recived > 0) {
                              $per_chat_cost = $product_pro_post->budget / $chat_recived;
                              $per_chat_cost = round($per_chat_cost, 4);
                              }else {
                                $per_chat_cost= '';
                              }
                              foreach ($current_post->chatHistories as $key => $value) {
                                   $row_chat_history = $row_chat_history + $value->chat;
                              }
                              if($row_chat_history > 0) {
                                $range = explode(' ', $post_range);
                                if($post_range != 'Ongoinig') {
                                  $av_cost = $product_pro_post->budget * $range[0] / $row_chat_history;
                                }
                              }
                              @endphp
                              @if($current_post->posting == 1)
                                <div style="border-top: none" class="table-td-text">
                                <center>
                                @if($product_pro_post->is_deleted == 0)
                                <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="{{($current_post->posting == 0) ? 'Stoped' : 'Running' }}" class="{{($current_post->posting == 0) ? 'cls-stop' : '' }} {{$group_color_class}} " id="schedule_row_{{$current_post->id}}" onclick="scheduleProduct('{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->promotion_product_type_id}}', '{{$current_post->group_code}}','{{$current_post->group_id}}','{{$current_post->id}}','{{$socials}}', '{{$current_post->date}}','{{$current_post->last_date}}','{{$store}}','{{$current_post->range}}', 'current', '{{$group_id}}','{{$selected_cate}}', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                  <span class="code-{{$current_post->group_code}} text-tag">{{$current_post->group_code}}</span>
                                  </a>                          
                                  @else
                                <a href="javascript:;" data-toggle="tooltip" data-placement="top" title="Deleted" >{{$current_post->group_code}}</a>
                                @endif
                                
                                </center>
                                </div>
                              @php break @endphp
                              @endif
                              @endforeach
                              @else
                              <?php if(!$start || $start == '') {
  
                                $start = $templates->start_date;
                              }
                              if(!$end || $end == '') {
                                $end = $templates->end_date;
                              }
  
                                ?>
                                  <div style="" class="table-td-text">
                                  <center>
                                 @if(@$campaign_current)   
                                  <a href="javascript:;" data-placement="top" title="" id="schedule_row_" onclick="scheduleNewPostForEmptyDay({{@$campaign_current->id}},'{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->category_id}}','{{$product_pro_post->promotion_product_type_id}}','{{$socials}}', '{{$store}}','{{$product_pro_post->range}}','{{($product_pro_post->budget) ? $product_pro_post->budget : 0}}','current','{{$group_id}}','{{$selected_cate}}','{{$start}}','{{$end}}', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                  <span class="current-new-{{$key}}"><i class="icon icon-plus-circle "></i></span></a>
                                  @else 
                                  <i class="icon icon-plus-circle"></i>
                                  <h5>No Campaign</h5>
                                  @endif
                                  </center>
                                  </div>
                              @endif
                              
                          </center>
                          </td>
                          <td class="argn-popup-td current-td current-section" style="vertical-align: middle;"><center>
                          
                          @php  $code = ''; $group_codee = ''; @endphp
                              @foreach($product_pro_post->productPostes as $k => $current_post)
                                @if($current_post->posting == 1)
                                @php $group_codee = $current_post->group_code;@endphp
                                @endif
                                @if($current_post->posting == 0)
                                <?php
                                if($code == $current_post->group_code) {
                                  continue;
                                }
                                else{
                                  $code = $current_post->group_code ;
                                  ?>
                               <div class="puased-code-{{$key}}{{$id}}">
                                <div style="" class="table-td-text ">
                                 <center> <span class="text-tag"> {{$current_post->group_code}} </span> </center>
                                </div>
                              </div>
                              <?php }?>
                              
                              @endif
                              @endforeach
                              
                          </center></td>
                          <td class="argn-popup-td current-td current-section table-td-text" style="vertical-align: middle;"><center>
                         <span class="text-tag"> {{(count($product_pro_post->productPostes) > 0) ? $product_pro_post->budget : ''}}</span>
                        </center></td>
                        @php 
                        
                          @endphp
                        <td class="argn-popup-td current-td current-section table-td-text" style="vertical-align: middle;"><center>
                          <span class="text-tag">{{$chat_recived}}</span>
                        </center></td>
                        <td class="argn-popup-td current-td current-section table-td-text" style="vertical-align: middle;"><center>
                          <span class="text-tag">{{$per_chat_cost}}</span>
                        </center></td>
                        <td class="argn-popup-td current-td current-section table-td-text" style="vertical-align: middle;"><center>
                          <span class="text-tag">{{round($av_cost, 4)}}</span>
                        </center>
                      </td>

                          <td class="argn-popup-td next-td table-td-text next-section" style="vertical-align: middle; display:none;"><center>
                            <span class="text-tag">{{(count($product_pro_post->productPostes) > 0) ? $product_pro_post->budget : ''}}</span>
                            </center></td>
                         <?php } ?>
                        
                        
  
                  </tr>
                  @endif
                  @endforeach
                  @else
                  <tr id="tr_" style="border-top: 1px solid gray" class="current-section">
  
                  <td colspan="7" class="column text-center no-data-msge" colspan="">
                      <center><label>No setting template found..</label></center>
                  </td>
                  </tr>
                  @endif
                  {{-- Next Section  --}}

                  @if(count($product_pro_posts_next) > 0)
              @php $start = ''; $end = ''; $next_start = ''; $next_end = ''; 
             @endphp
                  @foreach(@$product_pro_posts_next as $key=>$product_pro_post)
                  
                    @php $group = '';$chat_recived = 0; $per_chat_cost = ''; $row_chat_history = 0; $av_cost = 0; $post_range = 0 ; $sCates = explode(',',$product_pro_post->sub_category_id);@endphp
                  @if(count(array_filter($sCates)) == 0 || in_array($category->sub_category_id, $sCates))
                  <tr id="" class="next-section" style="border-top: 1px solid gray !important; display:none;">
                          <td class="argn-popup-td sticky-col third-col" style="vertical-align: middle;"><center><label>{{$product_pro_post->category}} </label> {{($product_pro_post->subCategory) ? '('.$product_pro_post->subCategory->name.')' : ''}}</center></td>
                          
                          <td class="argn-popup-td next-td next-section" style="vertical-align: middle; display:none;"><center>
                            
                            @php $next_start = ''; $next_end = ''; @endphp
                            @if(isset($product_pro_post->productPostes) && count($product_pro_post->productPostes) > 0)
                              @foreach($product_pro_post->productPostes as $k => $next_post)
                              @php 
                              $next_start = $next_post->date;
                              $next_end = $next_post->last_date;
                              $group_color_class = "light-red";
                              $designed = 0;
                              $posted = 0;
                              if( $next_post->designed ==1 && $next_post->posted != 1 ){
                                $group_color_class = "light-orange";
                                $designed = 1;
                                $posted = 0;
                              }elseif( $next_post->designed ==1 && $next_post->posted == 1 ){
                                $group_color_class = "light-green";
                                $designed = 1;
                                $posted = 1;
                              }
                              @endphp
                              @if($next_post->posting == 1)
                              <div>
                                <div style="border-top: none" class="table-td-text">
                                <center>
                                @if($product_pro_post->is_deleted == 0)
                                <a href="javascript:;" title="{{($next_post->posting == 0) ? 'Stoped' : 'Running' }}" class="{{($next_post->posting == 0) ? 'cls-stop' : '' }} {{$group_color_class}} " id="schedule_row_{{$next_post->id}}" onclick="scheduleProduct('{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->promotion_product_type_id}}', '{{$next_post->group_code}}','{{$next_post->group_id}}','{{$next_post->id}}','{{$socials}}', '{{$next_post->date}}','{{$next_post->last_date}}','{{$store}}','{{$next_post->range}}', 'next', '{{$group_id}}','{{$selected_cate}}', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                  <span class="code-next-{{$next_post->group_code}} text-tag">{{$next_post->group_code}}</span>
                                  </a>                        
                                  @else
                                <a href="javascript:;" data-toggle="tooltip" data-placement="top" title="Deleted" >{{$next_post->group_code}}</a>
                                
                                @endif
                                </center>
                                </div>
                              </div>
                              @php break @endphp
                              @endif
                              @endforeach
                              @else
                              <?php if(!$next_start || $next_start == '') {
  
                                  $next_start = $templates->start_date;
                                  }
                                  if(!$next_end || $next_end == '') {
                                  $next_end = $templates->end_date;
                                  }
                                  ?>
                              <div>
                                  <div style="" class="table-td-text">
                                  <center>
                                    @if(@$campaign_next) 
                                    <a href="javascript:;" data-placement="top" title="" class="next-{{$key}}{{$id}} change-btn-{{$key}}" id="schedule_row_" onclick="scheduleNewPostForEmptyDay({{@$campaign_next->id}},'{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->category_id}}','{{$product_pro_post->promotion_product_type_id}}','{{$socials}}', '{{$store}}','{{$product_pro_post->range}}','{{$product_pro_post->budget}}','next','{{$group_id}}','{{$selected_cate}}','{{$next_start}}','{{$next_end}}', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                    <span class="next-new-{{$key}}"><i class="icon icon-plus-circle"></i></span></a>
                                   @else 
                                    <i class="icon icon-plus-circle"></i>
                                    @endif
                                  </center>
                                  </div>
                                </div>
                              @endif
                          </center></td>
                          
                          <td class="argn-popup-td next-td table-td-text next-section" style="vertical-align: middle; display:none;"><center>
                            <span class="text-tag">{{(count($product_pro_post->productPostes) > 0) ? $product_pro_post->budget : ''}}</span>
                            </center></td>
                        
                        
  
                  </tr>
                  @endif
                  @endforeach
                  @else
                  <tr id="tr_" style="border-top: 1px solid gray; display:none;" class="next-section">
  
                  <td colspan="7" class="column text-center no-data-msge" colspan="">
                      <center><label>No setting template found..</label></center>
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
    <!--  Setting modal end -->
    <div class="modal fade setting_view_modal" id="setting_view_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 75%">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Change Schedule/ <span id="changed-group"></span></h5>
          <button type="button" class="close close-modal" onclick="closeModal('setting_view_modal')" aria-label="Close">
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
  function printReport() {
    
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
          'background-color:#fafafa !important;'+
          '}' +
          '.new-setting-btn {' +
            'display: none !important;' +
            '}' +
            '.range-text {' +
              'margin-left: 35px;'+
            '}'+
            '.current-td {'+
              'background-color: #00800021;'+
            '}'+
            '.previous-td {'+
              'background-color: #ff00002b;'+
            '}' +
            '.next-td {'+
             ' background-color: #ffa5004d;'+
            '}'+
            '.end-date{'+
              'color: black;'+
            '}'+
          '</style>';
          
     htmlToPrint += divToPrint.outerHTML;
    //  console.log(htmlToPrint);return;
    var newWin=window.open('','Print-Window');
  
    newWin.document.open();
  
    newWin.document.write('<html><body onload="window.print()">'+htmlToPrint+'</body></html>');
  
    newWin.document.close();
  }
  
  
  
  
  $('.close-modal').on('click', function() {
    $('.modal-content-loader').css('display', 'none');
    $('#schedule_view_content').html('');
  });
  
  </script>
  @endpush
  <style type="text/css">
    .td-valign{
      vertical-align: middle !important;
    }
  </style>