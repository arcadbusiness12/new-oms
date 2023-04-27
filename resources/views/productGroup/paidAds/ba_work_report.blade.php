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
  
  .t-c-text {
    margin-top: 28px;
  }
  .t-c-text label{
    font-weight: 700;
    font-size: 15px;
  }

  .sticky-col {
    position: -webkit-sticky;
    position: sticky;
    z-index: 2 !important;
    background-color: #ffffff !important;
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
    font-weight: 700 !important;
    /* left: 0px; */

  }
  
  .second-col {
    width: 555px;
    min-width: 155px;
    max-width: 100px;
    font-weight: 700 !important;
    left: 0;
  }
  .third-col {
    /* width: 69px;
    min-width: 69px; */
        width: 235px;
    min-width: 235px;
    max-width: auto;
    left: 155;
    font-weight: 700 !important;
  }  
  .calendar-col {
    width: 104;
    min-width: 100.7px;
    max-width: 104;
    border-bottom: 1px solid #323131 !important;

    font-weight: 700 !important;
  }  
  #head_row th {
    font-size: 14px !important;
  }
  .range-text {
        margin-left: 35px;
  }
  .current-td {
    background-color: #00800021 !important;
    border-right: 1px solid #3f51b5;
    border-bottom: 1px solid #3f51b5;
  }
  .previous-td {
    background-color: #ff00002b;
  }
  .next-td {
    background-color: #ffa5004d !important;
    border-right: 1px solid #3f51b5;
    border-bottom: 1px solid #3f51b5;
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
    padding: 2px;
    padding-left: 9px;
  }
  .user-helper-dropdown {
    box-shadow: -1px 2px 2px 0px;
    width: 72%;
  }
  .list-active a{
    color:green;
    border:1px solid green;
    border-radius: 10px;
    -webkit-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
    -moz-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
    box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
  }
  /* #schedule_view_content {
    margin-top: 38px;
  } */
  .av-cost {
    font-size: 18px;
    color: black;
  }
  .text-section {
    font-size: 15px;
  }
  .warring-text,.out-stock-text {
    font-size: 12px;
    color: white;
    background-color: red;
    position: relative;
  }
  .list-second-col {
    margin-top: 41px;
    margin-bottom: 10px;
  }
  .list-name {
    border: 1px solid lightgray;
    border-radius: 12px;
  }
  .out-stock-q {
    background-color: rgba(295,0,0,0.5);
    /* border: 2px solid red; */
  }
  .error-rmark {
    border: 2px solid red; 
  }
  caption {
  display: table-caption;
  text-align: center;
  font-size: 22px;
}
.d-count {
  color: green;
}
.out-stock-days-count {
    background-color: #f1092c;
    color: white !important;
    border-radius: 50%;
    position: absolute;
    /* margin-left: 95px !important; */
    margin-top: -15px !important;
    width: 2%;
    text-align: center;
}
.caption-text {
  color: #f1092c;
}
.errorr-input {
  border: 2px solid #f1092c;
}
/* Switch style  */
.switch {
  position: relative;
  display: inline-block;
  width: 78px;
  height: 25px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #0fd93b;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(52);
  -ms-transform: translateX(52px);
  transform: translateX(52px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.p-ads {
  /* position: absolute; */
  
}
.btn-sp {
  padding: 1px 7px;
}
.btn-single-stop {
  display: none;
  position: relative;
    left: 0px;
    top: -17px;
    color: red;
}
.btn-single-run {
  padding-left: 7px;
  display: none;
}
.active-ad {
  color: green;
  font-weight: 700;
}
.in-active {
  color:#f1092c;
}
.next-active-td {
  background-color: #00800021 !important;
}
.campaign-btn {
  padding-top: 4px;
}
.error-required {
  border: 1px solid red !important;
}
.bootstrap-select {
  width: 152px !important;
}
.cmpn-budget-text {
  color: #2b982a;
}
.dropdown-menu>li>a {
    padding: 7px 18px;
    color: #666;
    -moz-transition: all 0.5s;
    -o-transition: all 0.5s;
    -webkit-transition: all 0.5s;
    transition: all 0.5s;
    font-size: 14px;
    line-height: 25px;
}
.user-helper-dropdown {
    box-shadow: -1px 2px 2px 0px;
    width: 72%;
  }
  .out-stock-count {
    font-size: 16px !important;
    width: 24px !important;
    height: 24px !important;
    line-height: 24px !important;
    top: -20px !important;
    right: -20px !important;
  }
  .btn-active {
    border: 2px solid green;
    box-shadow: 1px 1px 2px 2px green;
  }
  .select-setting {
    color: green !important;
    font-weight: 700;
  }
  </style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">

          <div class="row mb-4">
            <div class="col-md-12 col-sm-12">
              
              
                          <div class="col-sm-1 col-md-1 list-inner col-grid">
                              {{--  <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>  --}}
                              <a href="{{ route('performance.marketing.save.add.chat', ['current',$id]) }}" class=""> <button class="btn btn-outline-secondary btn-sm {{($action == 'current') ? 'btn-active' : ''}}" style="background: greenyellow;font-weight:bold;">Current</button> </a>
                          </div>
                          <div class="col-sm-1 col-md-1 list-inner col-grid">
                            <a href="{{ route('performance.marketing.save.add.chat', ['comming',$id]) }}"> <button class="btn btn-outline-secondary btn-sm {{($action == 'comming') ? 'btn-active' : ''}}" style="background: #fefe0a;font-weight:bold;">Comming</button> </a>
                        </div>
                        
            </div>
        </div>

            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">
                              @forelse($ad_types as $key => $ad_type)
                              <div class="col-sm-3 col-md-3 list-inner col-grid">
                                <div class="btn-group user-helper-dropdown">
                                  {{--  <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>  --}}
                                <a href="javascript:void()"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">{{  $ad_type->name }} <i class="icon icon-keyboard_arrow_down"></i></a>
                                  <ul class="dropdown-menu pull-right" style="margin-top:1px !important">
                                      @forelse($ad_type->paidAdsSettings as $key=>$paid_ads_setting)
                                      <li><a href="{{ route('performance.marketing.save.add.chat', [$action,$paid_ads_setting->id]) }}" class="{!! $id == $paid_ads_setting->id ? 'select-setting' : '' !!}">{{ $paid_ads_setting->setting_name }} {!! $id == $paid_ads_setting->id ? '<i class="icon icon-check" style="color:green;"></i>' : ""  !!} </a></li>
                                      @empty
                                      @endforelse
                                  </ul>
                                </div>
                              </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Paid Ads Chats
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          <div class="col-12 mb-4">
                          <div class="col-5 col-grid text-black" style="display:inline-block;padding-top: 8px;padding-left: 0;">
                            <h5 style="display:inline-block;color:black;">{{@$templates->setting_name}} ({{@$templates->title}})</h5>
                            </div>
                            <div class="col-sm-3 list-inner col-grid text-black" style="padding-top: 8px;">
                              {{--  <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>  --}}
                              <span class="range-text font-weight-bold">Campaign budget:  <label class="cmpn-budget-text">{{ @$templates->campaign_budget }}</label></span>
                              
                          </div>
                            <div class="col-sm-4 list-inner col-grid mt-2" style="">
                              <div class="btn-group user-helper-dropdown">
                                {{--  <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>  --}}
                              <a href="javascript:void()"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Current Campaign History <i class="icon icon-keyboard_arrow_down"></i></a>
                                <ul class="dropdown-menu pull-right" style="margin-top:1px !important">
                                    @foreach ($current_campaign_history as $campaign)
                                     <li style="border-bottom: 1px solid gainsboro !important;margin-bottom: 5px;padding-bottom: 5px;">
                                         <a href=""> {{$campaign->campaign_name}} <i class="icon icon-check"></i> </a>
                                    </li>
                                    @endforeach
                                    
                                </ul>
                              </div>
                            </div>
                            
                    </div>
                          @if(count($product_pro_posts) > 0 || count($product_next_pro_posts))
                            <div class="col-12">
                              <span class="text-black"><label class="font-weight-bold">Budget Type: </label> {{@$templates->budgetType['name']}}</span>,
                              <span class="range-text text-black"><label class="font-weight-bold">Range: </label> {{ @$templates->range }}</span>,
                              <span class="range-text text-black"><label class="font-weight-bold">Ad Type: </label> {{ @$templates->adsType->name }}</span>,
                              <span class="range-text text-black"><label class="font-weight-bold">User: </label> {{ @$templates->user->firstname }} {{ @$templates->user->lastname }}</span>
                              @if(@$templates->ads_type_id != 1)
                              @php $er = ''; $er_flag = false; @endphp
                                @if($errors->has('chat_entry_date'))
                                  @php $er = 'errorr-input'; $er_flag = true; @endphp
                                  
                                @endif
                              <span class="range-text text-black"><label class="font-weight-bold">Date:</label>
                                
                                <input type="text" name="entry_date" id="date_from" class="datepicker {{@$er}}" autocomplete="off" data-dtp="dtp_igs2I" onchange="selectEntryDate(this.value)" value="" placeholder="Date" required>
                                @if($er_flag)
                                <span class="text-danger date-error">{{$errors->first('chat_entry_date')}}</span>
                                @endif
                                
                              </span>
                              @else 
                              <span class="range-text text-black"><label class="font-weight-bold">{{(@$campaign_current->start_date) ? 'Current Campaign Start Date:' : ''}} </label> {{@$campaign_current->start_date}}</span>
                              @endif
                            </div>
                            @php
                            $estimated_cost_per_ad_type_alloted = @$templates->estimated_cost_per_ad_type;
                            @endphp
                          @endif
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                         <div class="wrapper">
                            <form method="post" action="{{url('/performance/marketing/save/paid/ad/chat')}}">
                              {{ csrf_field() }}
                            <table class="table"  style="border: 1px solid #2196f3">
                            <input type="hidden" name="main_id" value="{{$id}}">
                            <input type="hidden" name="action_status" value="{{$action}}">
                            <input type="hidden" name="user_id" value="{{@$templates->user_id}}">
                            <input type="hidden" name="campaign_current" value="{{@$campaign_current->id}}">
                            <input type="hidden" name="campaign_next" value="{{@$campaign_next->id}}">
                            
                            <input type="hidden" name="chat_entry_date" id="chat_entry_date" value="">
                                <thead >
                                  <tr id="head_row" style="">
                    
                                    <th scope="col" colspan="2" class="sticky-col first-top text-right" style="" class="top-row"></th>
                                    @if($action == 'current')
                                      <span>
                                       <th colspan="{{(@$templates->ads_type_id == 1) ? '5' : '9'}}" style="text-align:center;" class="top-row">
                                         <span>Current</span> 
                                         {{-- @if(@$templates->ads_type_id == 1) --}}
                                         <span class="btn-stop"><button type="button" class="btn btn-danger btn-sp btn_stop btn-sm" onclick="showStopBtns()">Stop</button> </span> 
                                         <div class="campaign-btn">
                                          @if($campaign_current)
                                            <h5 class="text-black">{{$campaign_current->campaign_name}}</h5>
                                         
                                          @endif
                                         </div> 
                                         {{-- @endif --}}
                                         
                                       </th>
                                       </span>
                                       @else
                                       <span>
                                       <th scope="col" colspan="4" class="top-row">
                                          <center>
                                            {{-- @if(@$templates->ads_type_id == 1) --}}
                                            <span class="btn-stop"><button type="button" class="btn btn-success btn-sp btn_run" onclick="showActiveBtns()">Active</button> </span>
                                            {{-- @endif  --}}
                                            Coming <br>
                                            {{-- @if(@$templates->ads_type_id == 1) --}}
                                            <div class="campaign-btn">
                                              @if($campaign_next)
                                                <h5 class="text-black">{{$campaign_next->campaign_name}}</h5>
                                              @else 
                                                <input type="button" name="create-campaign" class="btn btn-info active" value="Create Campaign" data-toggle="tooltip" data-placement="top" data-bs-toggle="modal" data-bs-target="#create_campaign_modal" onclick="createCampaign('{{@$templates->setting_name}}', {{$id}}, '{{date('y_m_d')}}')">
                                              @endif
                                              {{-- @endif --}}
                                            </div> 
                                          </center>
                                          
                                          </div>
                                        </th>
                                       </span>
                                       @endif
                                     </tr>
                                  <tr id="head_row" style="background-color: #048304;
                                  color: white;">
                    
                                    <th scope="col" class="sticky-col second-col" style="background-color: #048304 !important;color: white;"><center>Ad Set </center></th>
                                    <th scope="col" class="sticky-col second-col" style="background-color: #048304 !important;color: white;"><center>Type </center></th>
                                    <th scope="col" class="sticky-col third-col" style="background-color: #048304 !important;color: white;"><center>Category</center></th>
                                    @if($action == 'current')
                                    @if( @$templates->ads_type_id != 1 ) 
                                      <th scope="col" class="calendar-col"><center>Active/In-Active</center></th>
                                    @endif
                                    <th scope="col" class="calendar-col"><center>Paused</center></th>
                                    <th scope="col" class="calendar-col"><center>Active</center></th>
                                    <th scope="col" class="calendar-col"><center>@if( @$templates->ads_type_id != 1 ) Budget Alloted @else Budget @endif</center></th>
                                    <th scope="col" class="calendar-col"><center>Remarks</center></th>
                                    @if( @$templates->ads_type_id != 1 )
                                    <th scope="col" class="calendar-col"><center>Budget Used</center></th>
                                    <th scope="col" class="calendar-col" style="border-right: 1px solid;"><center>Result</center></th>
                                    <th scope="col" class="calendar-col" style="border-right: 1px solid;"><center>Cost per result</center></th>
                                    {{-- <th scope="col" class="calendar-col" style="border-right: 1px solid;"><center>Average Cost</center></th> --}}
                                    @endif
                                    <th scope="col" class="calendar-col" style="border-right: 1px solid;"><center>Creative Type</center></th>
                                    @else
                                    <th scope="col" class="calendar-col"><center>Schedule</center></th>
                                    <th scope="col" class="calendar-col"><center>Budget</center></th>
                                    @endif
                                    </span>
                                    
                                    
                                  </tr>
                                </thead>
                    
                                <tbody id="body-dataa">
                                @php $socials =   $template_socials != "" ? implode(",", $template_socials) : ""; @endphp
                               <?php if( $action == 'current' ) { 
                                if(count($product_pro_posts) > 0) {?>
                                @php $start = ''; $end = ''; $next_start = ''; $next_end = ''; $total_budget_alloted = 0; 
                                    $schedule_budget = 0;$chat_form = false;
                                @endphp
                                    @foreach(@$product_pro_posts as $key => $product_pro_post)
                    
                                      @php $group = ''; $group_name = ''; $group_code = ''; $chat_recived = 0; $row_chat_history = 0; $av_cost = 0; 
                                      $chat_history = ''; $group_id = null ; $current_post_id = null;
                                      $start = null; $end = null; $post_range = 0;
                                      
                                      @endphp
                                    <tr id="" style="border-top: 1px solid gray !important;">
                                            <td class="argn-popup-td sticky-col second-col t-c-text" style="vertical-align: middle;"><center><label>{{@$product_pro_post->ad_set_name}} </label></center></td>
                                            <td class="argn-popup-td sticky-col second-col t-c-text" style="vertical-align: middle;"><center><label>{{@$product_pro_post->type->name}}</label></center></td>
                                            {{-- <td class="argn-popup-td sticky-col second-col" style="vertical-align: middle;"><center><label>{{@$product_pro_post->type->name}}</label></center></td> --}}
                                            <td class="argn-popup-td sticky-col third-col t-c-text" style="vertical-align: middle;"><center><label>{{$product_pro_post->category}}</label> {{($product_pro_post->subCategory) ? '('.$product_pro_post->subCategory->name.')' : ''}}</center></td>
                                            @if($action == 'current')
                                              @if( @$templates->ads_type_id != 1 )
                                                <td class="argn-popup-td current-td" style="vertical-align: middle;">
                                                  <label class="switch">
                          
                                                    <input type="checkbox" name="status" class="status-switch{{$key}}" onchange="changeStatus({{$product_pro_post->id}}, {{$key}})" {{($product_pro_post->is_active == 0) ? 'checked' : ''}}>
                                                    
                                                    <span class="slider round"></span>
                                                  </label>
                                                </td>
                                              @endif
                                            <td class="argn-popup-td current-td" style="vertical-align: middle;"><center>
                                             
                                                    <div style="" class="table-td-text current-pause{{$key}}">
                                                    <center> 
                                              @php  $code = '';
                                                
                                              @endphp
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
                                                            
                                                            <span class="text-section" data-toggle="tooltip" data-placement="top" title="Paused">{{$ad_row->group_code}}</span><br>
                                                            {{-- <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="click to check stock details" data-target=".setting_view_modal" onclick="showQuantityCounts('{{$ad_row->out_products}}', '{{$ad_row->out_stock_days}}')"> --}}
                                                               @if($ad_row->out_stock_days && $ad_row->out_stock_days > 0)
                                                                  {{-- <span class="out-stock-text">Out of stock</span> --}}
                                                                  <span class="out-stock-days-count" data-toggle="tooltip" data-placement="top" title="It was out of stock for {{$ad_row->out_stock_days}} days">{{($ad_row->out_stock_days > 0) ? $ad_row->out_stock_days : 0}}</span>
                                                                 @endif
                                                            {{--</a> --}}
                                                        <?php }?>
                                                          
                                                      @endif
                                                    @endif
                                                    @endforeach
                                                    <!-- @if( $group_name == "" )
                                                   <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="Add Schedule" class="" data-target=".setting_view_modal" id="schedule_row_" onclick="getNewForEmptyDaySchedule('{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{@$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->category_id}}','{{$product_pro_post->promotion_product_type_id}}','{{$socials}}', '{{$store}}','{{$product_pro_post->range}}','{{$product_pro_post->budget}}','{{$start}}','{{$end}}', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                                    <i class="fa fa-plus-circle"></i></a>
                                                   @endif -->
                                                     </center>
                                                    </div>
                                           </td>
                    
                                            <td class="argn-popup-td current-td" style="vertical-align: middle;">
                                           
                                                  <div style="border-top: none" class="table-td-text">
                                                  <center>
                                                    @php $budget = ''; @endphp
                                                    @forelse($pro_posts as $k => $ad_row)
                                                      @if($product_pro_post->id == $ad_row->setting_id && $id == $ad_row->main_setting_id && $product_pro_post->promotion_product_type_id == $ad_row->product_type_id && $product_pro_post->range == $ad_row->range)
                                                      
                                                        @php @endphp
                                                        @if($ad_row->posting == 1)                                    
                                                        @php  
                                                                $budget = $ad_row->budget; 
                                                                $post_id = $ad_row->id;
                                                                $group_name   = $ad_row->group_code;
                                                                $group_id   = $ad_row->group_id;
                                                                $current_post_id = $ad_row->id;
                                                                $start   = $ad_row->date;
                                                                $end   = $ad_row->last_date;
                                                                $post_range   = $ad_row->range;
                                                                $chat_recived = $ad_row->chat_recieved;
                                                                $posting = $ad_row->posting;
                                                                $chat_history = json_encode($ad_row->chatHistories);
                                                                $group_code = $ad_row->group_code;
                                                                foreach ($ad_row->chatHistories as $value) {
                                                                  $row_chat_history = $row_chat_history + $value->chat;
                                                                }
                                                                if($row_chat_history > 0) {
                                                                  $range = explode(' ', $post_range);
                                                                  if($post_range != 'Ongoinig') {
                                                                    $av_cost = $product_pro_post->budget * $range[0] / $row_chat_history;
                                                                  }
                                                                }
                                                                if($ad_row->is_active_paid_ads == 1) {
                                                                  $total_budget_alloted += $ad_row->budget;
                                                                }
                                                            @endphp
                                                            <div class="current-ad{{$key}}">
                                                            <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="Running" class="text-section current-code{{$key}} p-ads {{($ad_row->is_active_paid_ads == 1) ? 'active-ad' : 'in-active'}}" data-target=".setting_view_modal" id="schedule_row_" onclick="changeSchedule('{{$id}}','{{$product_pro_post->id}}','{{@$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->promotion_product_type_id}}', '{{$ad_row->group_code}}','{{$group_id}}','{{$current_post_id}}','{{$socials}}', '{{$start}}', '{{$store}}','{{$post_range}}', 'current', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                                            {{ $ad_row->group_code }}
                                                            @if($av_cost > $product_pro_post->budget)
                                                              <span class="warring-text">Need to change</span><br>
                                                            @endif
                                                          </a>  
                                                          <a href="javascript:;" class="btn-single-stop" data-toggle="tooltip" data-placement="top" title="Stop this ad" onclick="stopAd({{$current_post_id}},{{@$id}},{{@$product_pro_post->id}},{{$ad_row->post_duration}},'{{$ad_row->group_code}}',{{$key}}, {{@$campaign_current->id}})"><i class="icon icon-times-circle" aria-hidden="true"></i></a>
                                                          @if($ad_row->is_active_paid_ads == 0)
                                                          <a href="javascript:;" class="btn-check-current{{$key}}" data-toggle="tooltip" data-placement="top" title="Click to active this ad" onclick="activeAd({{$current_post_id}},{{@$id}},{{@$product_pro_post->id}},{{$ad_row->post_duration}},'{{$ad_row->group_code}}',1,{{$key}},{{$ad_row->budget}},{{@$campaign_current->id}})">
                                                            <i class="icon icon-check-square-o" aria-hidden="true"></i>
                                                          </a>
                                                          @endif
                                                          @if($ad_row->stock == 0)
                                                          <br>
                                                          <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#stock_detail_modal" onclick="showQuantityCounts('{{$group_id}}', '{{$ad_row->out_stock_days}}')">
                                                            
                                                                <span class="out-stock-text">Out of stock
                                                                  @if($ad_row->out_stock_days && $ad_row->out_stock_days > 0)
                                                                  <span class="badge badge-danger badge-mini rounded-circle out-stock-count font-weight-bold" data-toggle="tooltip" data-placement="top" title="It has been out of stock for {{$ad_row->out_stock_days}} days">{{$ad_row->out_stock_days}}</span>
                                                                  @endif

                                                                </span>
                                                                
                                                          </a>
                                                        </div>
                                                          @endif
                                                            @break;
                                                          @endif
                                                        @endif
                                                    @empty
                                                      
                                                    @endforelse
                                                    <?php if(!$start || $start == '') {
                    
                                                      $start = $templates->start_date;
                                                    }
                                                    if(!$end || $end == '') {
                                                      $end = $templates->end_date;
                                                    }
                        
                                                      ?>
                                                    @if( $group_name == "" )
                                                   <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="Add Schedule" class="" data-target=".setting_view_modal" id="schedule_row_" onclick="getNewForEmptyDaySchedule({{@$templates->ads_type_id}},{{$campaign_current ? $campaign_current->id : 0}},'{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->category_id}}','{{$product_pro_post->promotion_product_type_id}}','{{$socials}}', '{{$store}}','{{$product_pro_post->range}}','{{($product_pro_post->budget) ? $product_pro_post->budget : 0}}','current','{{$start}}','{{$end}}', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                                    <i class="icon icon-plus-circle"></i></a>
                                                   @endif
                                                  </center>
                                                </div>
                                            </td>
                                            @php  
                                              if($chat_recived > 0) {
                                                $per_chat_cost = $product_pro_post->budget / $chat_recived;
                                                $per_chat_cost = round($per_chat_cost, 4);
                                              }else {
                                                $per_chat_cost= '';
                                              }
                                              @endphp
                                          
                                          <td class="argn-popup-td current-td table-td-text" style="vertical-align: middle;">
                                            <center>
                                              <div class="current-budget{{$key}}">
                                              <span class="text-section"> {{ $budget }} </span>
                                              </div>
                                            </center>
                                          </td>
                                          <td class="argn-popup-td current-td table-td-text" style="min-width: 182px;">
                                            @if(@$post_id)
                                              <input type="hidden" name="post_id" id="post{{$key}}" class="form-control" value="{{$post_id}}">
                                              <input type="hidden" name="remark_action" id="remark_action{{$key}}" value="post">
                                              {{-- <input type="text" name="new_value" class="form-control" value="14"> --}}
                                              <textarea class="form-control" name="remark" id="remark{{$key}}">{{$ad_row->remark}}</textarea>
                                            {{-- <div class="col-sm-1"> --}}
                                              <button type="button" style="width:100%;padding:0px;" class="btn btn-success active savebnt{{$key}}" onclick="saveRemark({{$key}})">Save</button>
                                            {{-- </div> --}}
                                            @else 
                                              <input type="hidden" name="post_id" id="post{{$key}}" class="form-control" value="{{$product_pro_post->id}}">
                                              <input type="hidden" name="remark_action" id="remark_action{{$key}}" value="setting">
                                              {{-- <input type="text" name="new_value" class="form-control" value="14"> --}}
                                              <textarea class="form-control" name="remark" id="remark{{$key}}">{{$product_pro_post->remark}}</textarea>
                                            {{-- <div class="col-sm-1"> --}}
                                              <button type="button" style="width:100%;padding:0px;" class="btn btn-success active savebnt{{$key}}" onclick="saveRemark({{$key}})">Save</button>
                                            {{-- </div> --}}
                                            @endif
                                          </td>
                                          @if( @$templates->ads_type_id != 1 )
                                          <td class="argn-popup-td current-td table-td-text" style="vertical-align: middle;">
                                            <center>
                                              {{-- <input type="hidden" name="group[{{ $product_pro_post->id }}]" value="{{ $group_name }}" size="4"> --}}
                                              <input type="hidden" name="setting[{{ $product_pro_post->id }}]" value="{{ $group_name }}" size="4">
                                              {{-- <span>Current </span> --}}
                                              <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="Current Chat" class="text-section" data-target=".setting_view_modal" id="schedule_row_" onclick="changeSchedule( '{{$chat_history}}', '{{ $product_pro_post->budget }}','{{$id}}','{{$product_pro_post->id}}','{{@$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->promotion_product_type_id}}', '{{$group_code}}','{{$group_id}}','{{$current_post_id}}','{{$socials}}', '{{$start}}', '{{$end}}','{{$store}}','{{$post_range}}', 1,'current', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                                {{ ($chat_recived > 0) ? $chat_recived : '' }}
                                              </a>  
                                              {{-- <input type="text" name="chat[{{ $product_pro_post->id }}]" size="8" value=""> --}}
                                             
                                              <input type="text" name="used_budget[{{ $product_pro_post->id }}]" size="8" value="{{$product_pro_post->budget_used}}">
                                            </center>
                                          </td>
                                          
                                          <td class="argn-popup-td current-td table-td-text" style="vertical-align: middle;">
                                            <center>
                                              <input type="hidden" name="setting[{{ $product_pro_post->id }}]" value="{{ $group_name }}" size="4">
                                              {{-- <span>Current </span> --}}
                                              <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="Current Chat" class="text-section" data-target=".setting_view_modal" id="schedule_row_" onclick="changeSchedule( '{{$chat_history}}', '{{ $product_pro_post->budget }}','{{$id}}','{{$product_pro_post->id}}','{{@$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->promotion_product_type_id}}', '{{$group_code}}','{{$group_id}}','{{$current_post_id}}','{{$socials}}', '{{$start}}', '{{$end}}','{{$store}}','{{$post_range}}', 1,'current', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                                {{ ($chat_recived > 0) ? $chat_recived : '' }}
                                              </a>  
                                              <input type="text" name="chat[{{ $product_pro_post->id }}]" size="8" value="{{$product_pro_post->result}}">
                                              
                                            </center>
                                          </td>
                                          
                                          
                                          
                                          <td class="argn-popup-td current-td table-td-text" style="vertical-align: middle;">
                                            <center>
                                              <span class="text-section"> {{ ($product_pro_post->budget_used && $product_pro_post->result) ? round($product_pro_post->budget_used/$product_pro_post->result, 5) : 0 }} </span> <br>
                                              <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#chat_history_modal" onclick="showAllResults({{$product_pro_post->adResultHistories}})"><span style="font-size: 12px;">Show All</span></a>
                                            </center>
                                          </td>
                                          
                                          @endif
                                          <td class="argn-popup-td current-td table-td-text" style="vertical-align: middle;">
                                            <center>
                                           <span class="text-section"> {{@$product_pro_post->creativeType['name']}} </span>
                                          </center>
                                          </td>
                                          @endif
                                          
                    
                                    </tr>
                                    @endforeach
                                    @php
                                        $total_budget_used = 0;
                                        $total_results = 0;
                                        $total_cost_per_chat = 0;
                                        $date = '';
                                        $chat_user = '';
                                        if( $campaign_current ){
                                          $chat_user = @$campaign_current->chatResults[0]->user->username;
                                          $total_budget_used  = @$campaign_current->chatResults[0]->budget_used;
                                          $total_results  = @$campaign_current->chatResults[0]->results;
                                          $total_cost_per_chat =  ( $total_results > 0 ) ? $total_budget_used/$total_results : 0;
                                          $date = @$campaign_current->chatResults[0]->date;
                                        }else {
                                          $chat_user = @$templates->chatResults[0]->user->username;
                                          $total_budget_used  = @$templates->chatResults[0]->budget_used;
                                          $total_results  = @$templates->chatResults[0]->results;
                                          $total_cost_per_chat =  ( $total_results > 0 ) ? $total_budget_used/$total_results : 0;
                                          $date = @$templates->chatResults[0]->date;
                                        }
                                        $sechedule_total_budget_used = 0;
                                        $sechedule_total_results = 0;
                                        $sechedule_total_cost_per_chat = 0;
                                        $sechedule_date = '';
                                        
                                    @endphp
                                    <?php if( @$templates->ads_type_id == 1 ) {?>
                                    <tr>
                                      <td colspan="5" style="">
                                        <table>
                                          <caption style="font-size:14px;">Current Ads Chat</caption>
                                      {{-- <input type="hidden" name="user_id" value="{{ @$templates->user_id  }}"> --}}
                                      <td align="center">
                                        <br>
                                        User {{($chat_user != '') ? '('.$chat_user.')' : ''}}<br>
                                        <select name="user_id">
                                          @foreach($users as $u) 
                                            <option value="{{$u->user_id}}" {{($u->user_id == @$templates->user_id) ? 'selected' : ''}}>{{$u->username}}</option>
                                          @endforeach
                                        </select>
                                      </td>
                                      <td align="center">
                                        <br>Date<br>
                                        <input type="text" name="date" id="date_from" class="datepicker" autocomplete="off" data-dtp="dtp_igs2I" size="12" value="{{ $date }}" placeholder="Date">
                                      </td>
                                      <td>Budget <br>Alloted<br><input type="text" name="total_budget_alloted" id="total_budget_alloted" value="{{ $total_budget_alloted }}"  minlength="0" size="7" readonly></td>
                                      <td>Budget<br> Used:<br><input type="text" name="total_budget_used" id="total_budget_used" value="{{ $total_budget_used }}"   minlength="0" size="7" onkeyup="calculatePerCost('')"></td>
                                      <td><br>Result:<br><input type="text" name="total_chat_received" id="total_chat_received" value="{{ $total_results }}" minlength="0" size="7" onkeyup="calculatePerCost('')"></td>
                                      <td>Cost/Result<br> Alloted:<br><input type="text" name="total_cost_per_chat_alloted" value="{{ @$estimated_cost_per_ad_type_alloted }}" minlength="0" size="7" readonly></td>
                                     <td><br>Cost/Result:<br><input type="text" name="total_cost_per_chat" id="total_cost_per_chat" value="{{ number_format($total_cost_per_chat,4) }}" minlength="0" size="7" readonly></td>
                                     <td >@if( count($product_pro_posts) > 0 )
                                      <br>
                                      <input type="submit" class="btn btn-success pull-right active" name="update_chat" value="Update Chat">
                                     @endif</td>
                                    </table>
                                    </td>
                                    </tr>
                                    <?php } }else {?>
                                    <tr id="tr_" style="border-top: 1px solid gray">
                    
                                    <td class="column text-center" colspan="{{count($days )+2}}">
                                        <center><label>No Running Paid Ad found.</label></center>
                                    </td>
                                    </tr>
                                    <?php }
                                  } ?>


                                    {{-- Comming Section  --}}
                                  @if( $action == 'comming' )
                                    @if(count($product_next_pro_posts) > 0)
                                    @php $start = ''; $end = ''; $next_start = ''; $next_end = ''; $total_budget_alloted = 0; 
                                        $schedule_budget = 0;$chat_form = false;
                                    @endphp
                                    @foreach(@$product_next_pro_posts as $key => $product_pro_post)
                    
                                      @php $group = ''; $group_name = ''; $group_code = ''; $chat_recived = 0; $row_chat_history = 0; $av_cost = 0; 
                                      $chat_history = ''; $group_id = null ; $current_post_id = null;
                                      $start = null; $end = null; $post_range = 0;
                                      
                                      @endphp
                                    <tr id="" style="border-top: 1px solid gray !important;">
                                            <td class="argn-popup-td sticky-col second-col t-c-text" style="vertical-align: middle;"><center><label>{{@$product_pro_post->ad_set_name}} </label></center></td>
                                            <td class="argn-popup-td sticky-col second-col t-c-text" style="vertical-align: middle;"><center><label>{{@$product_pro_post->type->name}}</label></center></td>
                                            {{-- <td class="argn-popup-td sticky-col second-col" style="vertical-align: middle;"><center><label>{{@$product_pro_post->type->name}}</label></center></td> --}}
                                            <td class="argn-popup-td sticky-col third-col t-c-text" style="vertical-align: middle;"><center><label>{{$product_pro_post->category}}</label> {{($product_pro_post->subCategory) ? '('.$product_pro_post->subCategory->name.')' : ''}}</center></td>
                                           
                                          
                                          {{-- schedule design start --}}
                                          
                                          <td class="argn-popup-td next-td next-td{{$key}} testing" style="vertical-align: middle;">
                                            
                                                 <div style="border-top: none" class="table-td-text">
                                                 <center>
                                                   @php
                                                      $next_group_name = "";
                                                      $next_start = ''; $next_end = ''; $budget = '';
                                                      $n_b_td = 0;
                                                   @endphp
                                                   @forelse($next_post_data as $next_post_row)
                                                      @php $budget = ''; $isActive = '';@endphp
                                                     @if( $product_pro_post->id == $next_post_row->setting_id && $product_pro_post->promotion_product_type_id == $next_post_row->product_type_id )
                                                      
                                                       @php
                                                          if($next_post_row->is_active_paid_ads == 1) {
                                                              $chat_form = true;
                                                              $schedule_budget += $next_post_row->budget;
                                                              $n_b_td = 1;
                                                              $isActive = 'next-active-a';
                                                              echo "<script>$('.next-td'+$key).addClass('next-active-td');alert('Yesssssss')</script>";
                                                          }
                                                          
                                                                $budget = $next_post_row->budget;
                                                                $next_start = $next_post_row->date; 
                                                                $next_end   = $next_post_row->last_date;
                                                                $next_group_name   = $next_post_row->group_code;
                                                                $group_id   = $next_post_row->group_id;
                                                                $next_post_id = $next_post_row->id;
                                                                $next_start   = $next_post_row->date;
                                                                $next_end   = $next_post_row->last_date;
                                                                $post_range   = $next_post_row->range;
                                                                $chat_recived = $next_post_row->chat_recieved;
                                                                $posting = $next_post_row->posting;
                                                            @endphp
                                                            @if($product_pro_post->is_deleted == 0)
                                                            <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="{{($next_post_row->is_active_paid_ads == 1) ? 'Activated' : 'In-Active'}}" class="text-section {{$isActive}} td-a{{$key}}" data-target=".setting_view_modal" id="schedule_row_" onclick="changeSchedule('{{$id}}','{{$product_pro_post->id}}','{{@$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->promotion_product_type_id}}', '{{@$next_post_row->group_code}}','{{$group_id}}','{{$next_post_id}}','{{$socials}}', '{{$next_start}}', '{{$store}}','{{$post_range}}', 'next', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')" style="font-weight: 700;">
                                                              {{ $next_group_name }} 
                                                              
                                                            @else
                                                              <a href="javascript:;" data-toggle="tooltip" data-placement="top" title="Deleted" >{{$next_post_row->group_code}}</a>
                                                          
                                                          @endif
                                                          </a>  
                                                          <a href="javascript:;" class="{{($next_post_row->is_active_paid_ads == 0) ? 'btn-single-run' : ''}} btn-check{{$key}}" style="display: none;" data-toggle="tooltip" data-placement="top" title="Click to active this ad" onclick="activeAd({{$next_post_id}},{{@$id}},{{@$product_pro_post->id}},{{$next_post_row->post_duration}},'{{$next_post_row->group_code}}',2,{{$key}},{{$next_post_row->budget}},{{@$campaign_next->id}})">
                                                            <i class="icon icon-check-square-o" aria-hidden="true"></i>
                                                          </a>
                                                       @break;
                                                     @endif
                                                   @empty
                                                    
                                                   @endforelse
                                                   <?php if(!$next_start || $next_start == '') {
                    
                                                    $next_start = $templates->start_date;
                                                    }
                                                    if(!$next_end || $next_end == '') {
                                                    $next_end = $templates->end_date;
                                                    }
                                                    ?>
                                                   @if( $next_group_name == "" )
                                                   <a href="javascript:;" data-toggle="modal" data-toggle="tooltip" data-placement="top" title="" class="" data-target=".setting_view_modal" id="schedule_row_" onclick="getNewForEmptyDaySchedule({{@$templates->ads_type_id}},{{($campaign_next ? $campaign_next->id : 0)}},'{{$key}}','{{$id}}','{{$product_pro_post->id}}','{{@$product_pro_post->type->name}}','{{$product_pro_post->category}}','{{$product_pro_post->category_id}}','{{$product_pro_post->promotion_product_type_id}}','{{$socials}}', '{{$store}}','{{$product_pro_post->range}}','{{$product_pro_post->budget}}','next','{{$next_start}}','{{$next_end}}', '{{($product_pro_post->subCategory) ? $product_pro_post->subCategory->id : null}}')">
                                                    <i class="icon icon-plus-circle"></i></a>
                                                   @endif
                                                 </center>
                                                </div>
                                           </td>
                                           <td class="argn-popup-td next-td next-td{{$key}} table-td-text {{($n_b_td == 1) ? 'next-active-td' : ''}}" style="vertical-align: middle;">
                                            <center>
                                              {{@$budget}}
                                            </center>
                                          </td>
                                          {{-- schedule design end --}}
                    
                                    </tr>
                                    @endforeach
                                    @php
                                        $total_budget_used = 0;
                                        $total_results = 0;
                                        $total_cost_per_chat = 0;
                                        $date = '';
                                        $chat_user = '';
                                        $sechedule_total_budget_used = 0;
                                        $sechedule_total_results = 0;
                                        $sechedule_total_cost_per_chat = 0;
                                        $sechedule_date = '';
                                        if(@$campaign_next->schedulechatResults) {
                                          $sechedule_total_budget_used  = $campaign_next->schedulechatResults->budget_used;
                                          $sechedule_total_results  = $campaign_next->schedulechatResults->results;
                                          $sechedule_total_cost_per_chat =  ( $sechedule_total_results > 0 ) ? $sechedule_total_budget_used/$sechedule_total_results : 0;
                                          $sechedule_date = $campaign_next->schedulechatResults->date;
                                        }else {
                                          $sechedule_total_budget_used  = @$templates->schedulechatResults->budget_used;
                                          $sechedule_total_results  = @$templates->schedulechatResults->results;
                                          $sechedule_total_cost_per_chat =  ( $sechedule_total_results > 0 ) ? $sechedule_total_budget_used/$sechedule_total_results : 0;
                                          $sechedule_date = @$templates->schedulechatResults->date;
                                        }
                                    @endphp
                                    @if( @$templates->ads_type_id == 1 )
                                    <tr>
                                    {{-- Next ads form  --}}
                                    
                                    <td style="" colspan="5">
                                        <table id="sechedule_form" style="display: {{($chat_form) ? 'inline-block' : 'none'}}">
                                          <caption style="font-size:14px;">Schedule Ads Chat</caption>
                                      <td align="right">
                                        <br>
                                        Date<br>
                                        <input type="text" name="schedule_date" id="date_to" class="datepicker" autocomplete="off" data-dtp="dtp_igs2I" value="{{ $sechedule_date }}" placeholder="Date">
                                      </td>
                                      <td>Budget<br> Alloted<br><input type="text" name="schedule_total_budget_alloted" id="schedule_total_budget_alloted" value="{{ @$schedule_budget }}"  minlength="0" size="7" readonly></td>
                                      <td>Budget<br> Used:<input type="text" name="schedule_total_budget_used" id="sechedule_total_budget_used" value="{{ $sechedule_total_budget_used }}"   minlength="0" size="7" onkeyup="calculatePerCost('sechedule_')"></td>
                                      <td><br>Result:<input type="text" name="schedule_total_chat_received" id="sechedule_total_chat_received" value="{{ $sechedule_total_results }}" minlength="0" size="7" onkeyup="calculatePerCost('sechedule_')"></td>
                                      <td>Cost/Result<br> Alloted:<input type="text" name="schedule_total_cost_per_chat_alloted" value="{{ @$estimated_cost_per_ad_type_alloted }}" minlength="0" size="7" readonly></td>
                                    <td><br>Cost/Result:<input type="text" name="schedule_total_cost_per_chat" id="sechedule_total_cost_per_chat" value="{{ number_format($sechedule_total_cost_per_chat,4) }}" minlength="0" size="7" readonly></td>
                                    <td >@if( count($product_pro_posts) > 0 )
                                      <br>
                                      <input type="submit" class="btn btn-info pull-right active" name="schedule_chat" value="Update Chat">
                                    @endif</td>
                                    </table>
                                  </td>
                                    </tr>
                                    @endif
                                    @else
                                    <tr id="tr_" style="border-top: 1px solid gray">
                    
                                    <td class="column text-center" colspan="{{count($days )+2}}">
                                        <center><label>No Paid Ad Template Found.</label></center>
                                    </td>
                                    </tr>
                                    @endif

                                    @endif
                              </tbody>
                           </table>
                           <td >
                            @if( @$templates->ads_type_id != 1 )  
                            <input type="submit" class="btn btn-success pull-right" name="update_chat" value="Update Chat">
                          @endif</td> 
                          </form>
                      </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('employeePeerformance.marketting.models');
<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>

@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    setTimeout(() => {
      var br = document.getElementById('date_from').style.border='1px solid grey';
      $('.date-error').text('');
    },3500);
    $('.datepicker').datetimepicker({format: "Y-m-d",'timepicker':false})
  });

  $('.next-active-a').parent().parent().parent().addClass('next-active-td');

  function calculatePerCost(v){
    var total_budget_used   =  $('#'+v+'total_budget_used').val() || 0;
    var total_chat_received =  $('#'+v+'total_chat_received').val() || 0;
    var total_cost_per_chat = total_budget_used/total_chat_received;
    $('#'+v+'total_cost_per_chat').val(total_cost_per_chat.toFixed(4));
  }

 function showQuantityCounts(group, catopn) {
  // var detail = JSON.parse(details);
  console.log(catopn);
  // $('#stock_detail_modal').modal('show');
  // $('.history').html('');
  $('#exampleModalCenterTitle').text('Stock Details');
  $('#top-title').text('');
  $('#schedule_view_content').css('display', 'none');
  $('.th-textf').text('Size/Color');
  $('.th-textl').text('Quantity');
  
  if(group) {
    $('.history-loader').css('display', 'block');
    $.ajax({
      url: "{{url('/performance/get/out/stock/paid/ad/detail')}}/"+group,
      type: 'GET',
      cache: false,
      success: function(resp) {
        $('.history-loader').css('display', 'none');
        $('.body-data').html(resp);
        if(catopn > 0) {
          var dc = (catopn > 0) ? catopn : 0;
          $('.caption-text').html('<span class="caption-text"><strong>It has been out of stock for '+dc+' days</strong></span>');
        }
      }
    })
  }
}

function createCampaign(template, main_id, date) {
  console.log(template+'_'+date);
  console.log(main_id);
  $('.modal-content-loader').show();
  // $('#create_campaign_modal').modal();
  // $('#campaign-input').val(template+'_'+date);
  $('#campaign-main-id').val(main_id);
  $.ajax({
    url: "{{url('/productgroup/get/paid/ads/template/for/compaign')}}/"+main_id,
    type: "GET",
    data: {campaign_name: template+'_'+date},
    cache: false,
    beforSend: function() {
      $('.modal-content-loader').show();
    },
    complete: function(error) {
      $('.modal-content-loader').hide();
    }
  }).then(function(template) {
    $('.modal-template-content').html(template);
    console.log(template);
  });
}

$('#btn-save-campaign').on('click', function() {
  $.ajax({
    url: "{{url('/performance/create/paid/ads/campaign')}}",
    type: "POST",
    data: $('#campaign-form').serialize(),
    cache: false,
    beforeSend: function() {
      $('#btn-save-campaign').html('<i class="icon icon-spin icon-circle-o-notch"></i>');
      $('#btn-save-campaign').prop('disabled', true);
    },
    complete: function() {
      $('#btn-save-campaign').html('Create');
      $('#btn-save-campaign').prop('disabled', false);
    },
    error: function(error) {
      console.log(error);
      if(error.responseText.indexOf('user') !== -1) {
        console.log("User Error");
        $('#campaign-input').addClass('error-required');
        $('.alert-error-campaign').text('User selection is required');
        setTimeout(() => {
          $('#campaign-input').removeClass('error-required');
          $('.alert-error-campaign').text('');
        }, 2500);
      }
      if(error.responseText.indexOf('campaign') !== -1) {
        $('#campaign-input').addClass('error-required');
        $('.alert-error-campaign').text('Campaign name is required');
        setTimeout(() => {
          $('#campaign-input').removeClass('error-required');
          $('.alert-error-campaign').text('');
        }, 2500);
      }
      if(error.responseText.indexOf('no_template') !== -1) {
        console.log("Yessssssss");
        $('#error-msge').text('Please select template rows');
        setTimeout(() => {
          $('#error-msge').text('');
        }, 2500);
      }
    }
  }).then(function(resp) {
    if(resp.status) {
      location.reload();
    }else {
      if(resp.exist) {
        $('.alert-error-campaign').text('Campaign already exist with '+resp.campaign+' same name');
        setTimeout(() => {
          $('.alert-error-campaign').text('');
        }, 3500);
      }
      if(resp.no_template) {
        console.log("Yessssssss Ok");
        $('#error-msge').text('Please select template rows');
        setTimeout(() => {
          $('#error-msge').text('');
        }, 3500);
      }
    }
  })
});

function getNewForEmptyDaySchedule(ad_type,campaign,row,main_setting_id,setting_id,type, category,category_ids, group_type,socials,store, range,budget, action = null, start_date = null, end_date = null, sub_category=null) {
  
    if(campaign == 0 && ad_type == 1) {
      $('#schedule_view_content').html('<div class="text-center"><h5 class="text-danger">Pease first create campaign!</h5></div>');
      $('.modal-content-loader').css('display', 'none');
      return false;
    }
    $('.modal-content-loader').css('display', 'block');
    if(!start_date) {
       start_date = new Date().toISOString().slice(0, 10)
    }
    if(!end_date) {
      var today = new Date();
      end_date = new Date().toISOString().slice(0, 10);
      today.setDate(today.getDate()+1);
      end_date = today.toISOString().slice(0, 10);
    }
    console.log(end_date);
  $.ajax({
    url: "{{url('/productgroup/promotion/get/new/schedule/For/empty/paid/ads')}}/"+campaign +"/"+ row +"/" +main_setting_id +"/"+setting_id +"/"+type +"/"+ category  +"/"+ category_ids +"/"+group_type +"/"+socials +"/"+ store+ "/" +2+"/"+range+"/"+ budget+"/"+ action+"/"+ start_date+"/"+ end_date+"/"+sub_category,
    type: "GET",
    cache: false,
    success: function(resp) {
      $('.modal-content-loader').css('display', 'none');
      
        $('#schedule_view_content').html(resp);
    }
  })
}

function changeSchedule(main_setting_id,setting_id,type, category, group_type, group_code,group_id,post_id,socials,date,store,range, action, sub_category=null) {
  
  $('.modal-content-loader').css('display', 'block');
  $('#changed-group').text(group_code);
  $.ajax({
    url: "{{url('/productgroup/promotion/get/new/schedule')}}/"+main_setting_id +"/"+setting_id +"/"+type +"/"+ category +"/"+group_type +"/"+ group_code+"/"+ group_id +"/"+ post_id+"/"+socials+ "/" +date +"/"+ store+ "/" +2 +"/"+range+"/"+action+"/"+sub_category,
    type: "GET",
    cache: false,
    success: function(resp) {
      $('.modal-content-loader').css('display', 'none');
      
        $('#schedule_view_content').html(resp);
    }
  })
}

function showStopBtns() {
  $('.btn-single-stop').toggle();
}
function showActiveBtns() {
  $('.btn-single-run').toggle();
}

function activeAd(post,main_setting,template,current_duration,code,action,index,budget,capaign) {
  // console.log(budget);return;
  if(main_setting && template) {
      swal({
        title: "Are you sure want to activate?",
        text: "Please ensure and then confirm!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "Yes, activate it",
        cancelButtonText: "No, cancel!",
        reverseButtons: !0
    },function (e) {
        if (e === true) {
            $.ajax({
            url: "{{url('/performance/activate/single/comming/paid/ads')}}/"+ main_setting + "/"+ template +"/"+current_duration +"/"+post +"/"+ capaign,
            type: "GET",
            cache: false,
            success: function(resp) {
              console.log(resp);
              if(resp.status) {
                if(resp.current_flag) {
                  location.reload();
                }
                $('.tooltip').css('display', 'none');
                if(action == 1) {
                  $('.btn-check-current'+index).remove();
                  $('.current-code'+index).removeClass('in-active');
                  $('.current-code'+index).css('color', 'green');
                  $('.current-code'+index).css('font-weight','700');
                  
                  var v = $('#total_budget_alloted').val();
                  var total_b_allot = parseFloat(v)+parseFloat(budget);
                  $('#total_budget_alloted').val(total_b_allot);
                }else {
                  console.log('Yesssss Come');
                  $('.next-td'+index).addClass('next-active-td');
                  $('.btn-check'+index).remove();
                  $('#sechedule_form').css('display', 'inline-block');
                  var v = $('#schedule_total_budget_alloted').val();
                  var total_b_allot = parseFloat(v)+parseFloat(budget);
                  $('#schedule_total_budget_alloted').val(total_b_allot);
                }
                
              }else {
                if(resp.errorm) {
                  $('.stop-run-error').text(resp.errorm);
                  $('#chat_warring_modal').modal('show');
                  $('#chat_warring_mesge').html('<div class="text-center"><h5 class="text-danger">'+resp.errorm+'</h5></div>');
                }else {
                  $('.stop-run-error').text('Somthing went wrong, try again..');
                }
                setTimeout(() => {
                  $('.stop-run-error').text('');
                }, 3500);
              }
            }
          })
      }else {
          e.dismiss;
        }

    }, function (dismiss) {
        return false;
    })
  }
}

function stopAd(post,main_setting,setting,current_duration,code,index,campaign) {
  console.log(index);
  if(main_setting && setting) {
      swal({
        title: "Are you sure want to stop?",
        text: "Please ensure and then confirm because it will stop permenently!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "Yes, stop it",
        cancelButtonText: "No, cancel!",
        reverseButtons: !0
    },function (e) {
        if (e === true) {
            $.ajax({
            url: "{{url('/performance/stop/single/paid/ads')}}/"+ post + "/" + main_setting + "/"+ setting +"/"+current_duration +"/"+campaign,
            type: "GET",
            cache: false,
            success: function(resp) {
              console.log(resp);
              if(resp.status && resp.exist) {
                $('.tooltip').css('display', 'none');
                $('.current-ad'+index).css('color', 'green');
                $('.current-ad'+index).text('Paused');
                $('.current-pause'+index).text(code);
                setTimeout(() => {
                  $('.current-ad'+index).text('');
                  $('.current-budget'+index).text('');
                }, 2500);
              }else if(resp.status && resp.chat) {
                  console.log("Yes");
                  $('.tooltip').css('display', 'none');
                  $('#chat_warring_modal').modal('show');
                  $('#chat_warring_mesge').html('<div class="text-center"><h5 class="text-danger">Pease first enter the current campaign today chat!</h5></div>');
                 
              } else {
                  setTimeout(() => {
                    location.reload();
                  }, 1500);
                
              }
            }
          })
      }else {
          e.dismiss;
        }

    }, function (dismiss) {
        return false;
    })
  }
}

function selectEntryDate(value) {
    console.log(value);
    $('#chat_entry_date').val(value);
  }

  function showAllResults(history) {
    // console.log(history);
    if(history.length > 0) {
      var tbody = '';
      history.forEach(function callback(value, index) {
        tbody += '<tr><td class="text-center">'+value.budget_used+'</td><td class="text-center">'+value.results+'</td><td class="text-center">'+value.cost_per_result+'</td><td class="text-center">'+value.date+'</td></tr>'
        
          // total_chat = total_chat+value.chat;
        });
        }else {
          tbody += '<tr><td colspan="4" class="text-center">No history availible..</td></tr>'
        }
        
        // console.log(budget_range/total_chat);
        // $('#chat_history_modal').modal('toggle');
        $('.chat_history').html(tbody);
  }

  function saveRemark(index) {
    var post = $('#post'+index).val();
    var rmark = $('#remark'+index).val();
    var raction = $('#remark_action'+index).val();
    if(rmark == '') {
      $('#remark'+index).addClass('error-rmark');
      setTimeout(() => {
        $('#remark'+index).removeClass('error-rmark');
      },2500);
    }else {
      $('.savebnt'+index).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('.savebnt'+index).prop('disabled', true);
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
        url: "{{url('/performance/save/paid/post/remark')}}",
        type: "POST",
        data: {_token:CSRF_TOKEN,post:post,rmark:rmark,action:raction},
        cache: false,
        success: function(resp) {
            $('.savebnt'+index).html('Saved');
            if(resp.status) {
                            $(".toast-action").data('title', 'Action Done!');
                            $(".toast-action").data('type', 'success');
                            $(".toast-action").data('message', 'Remarks saved successfully.');
                            $(".toast-action").trigger('click');
                        } else {
                            $(".toast-action").data('title', 'Went Wrong!');
                            $(".toast-action").data('type', 'error');
                            $(".toast-action").data('message', 'Something went wrong.');
                            $(".toast-action").trigger('click');
                        }
            setTimeout(() => {
            $('.savebnt'+index).html('Save');
            $('.savebnt'+index).prop('disabled', false);
            }, 2500);
        }
      })
    }
    console.log(post);
  }

  function changeStatus(post, index) {
    console.log($('.status-switch'+index).is(':checked'));
    if($('.status-switch'+index).is(':checked')) {
      var status = 0;
    }else {
      var status = 1;
    }
    $.ajax({
        url: "{{url('/performance/change/status/paid/ad/setting')}}/"+post +"/"+ status,
        type: "GET",
        cache: false,
        success: function(resp) {
            console.log(resp);
        }
      });
    
  }
</script>
@endpush