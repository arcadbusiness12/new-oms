@extends('layouts.app')

@section('content')
<style>
  td {
    vertical-align: middle !important;
  }
  .right_border{
    border-right: 1px solid white !important;
  }
  .right_border_bk{
    border-right: 1px solid black !important;
    border-bottom: 1px solid black !important;
  }
  .more_details{
    font-weight: bolder;
    font-size: 17px;
    cursor: pointer;
  }
  .font-red{
    color: red;
  }
  .btn-success {
    background-color: green !important;
  }
  .btn-danger {
    background-color: #ed5564 !important;
  }
  .btn-warning {
    background-color: #fcce54 !important;
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
                            <div class="panel-body">
                                <form name="filter_reports" id="filter_reports" method="get" action="{{ route('commission.sale.saleOnTotalDeliveredAmount') }}">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                              <div class="form-line">
                                                <select name="by_duration" id="by_duration" class="form-control show-tick" data-live-search="true" onchange="showDates()">
                                                  <option value="today"     {{ ("today"==@$old_input['by_duration']) ? "selected" : "" }}>Today</option>
                                                  <option value="yesterday" {{ ("yesterday"==@$old_input['by_duration']) ? "selected" : "" }}>Yesterday</option>
                                                  <option value="thisweek"  {{ ("thisweek"==@$old_input['by_duration']) ? "selected" : "" }}>This week</option>
                                                  <option value="lastweek"  {{ ("lastweek"==@$old_input['by_duration']) ? "selected" : "" }}>Last week</option>
                                                  <option value="thismonth" {{ ("thismonth"==@$old_input['by_duration']) ? "selected" : "" }}>This Month</option>
                                                  <option value="lastmonth" {{ ("lastmonth"==@$old_input['by_duration']) ? "selected" : "" }}>Last Month</option>
                                                  <option value="custom"    {{ ("custom"==@$old_input['by_duration']) ? "selected" : "" }}>custom</option>
                                                </select>
                                              </div>
                                          </div>
                                        </div>
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                              <div class="form-line">
                                                  {{--  <label class="form-label" for="status">Status</label>  --}}
                                                  <select name="user" id="user" class="form-control show-tick" data-live-search="true">
                                                      <option value="">-Select user-</option>
                                                      @forelse($staffs as $key => $value)
                                                        <option value="{{ $value->user_id }}" {{ ($value->user_id==@$old_input['user']) ? "selected" : "" }} >{{ $value->firstname." ".$value->lastname }}</option>
                                                      @empty
                                                      @endforelse
                                                  </select>
                                              </div>
                                          </div>
                                        </div> 
                                    </div>
                                    <div class="row custom_duration" <?php if(@$old_input['by_duration'] != 'custom'){ ?> style="display:none" <?php } ?> >
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                            <div class="form-line">
                                            <label class="form-label">From</label>
                                                  <input type="text" name="date_from" id="date_from" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" value="{{ @$old_input['date_from'] != "" ? $old_input['date_from'] : '' }}">
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                            <div class="form-line">
                                            <label class="form-label">To</label>
                                                  <input type="text" name="date_to" id="date_to" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" value="{{ @$old_input['date_to'] != "" ? $old_input['date_to'] : ''  }}">
                                            </div>
                                          </div>
                                        </div>
                                    </div>
                                    <button type="submit" id="search_filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> Filter</button>
                                </form>
                            </div>
                        </div>
                </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Report
                          </div>
                          <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background-color: #3379b7; color:white">
                                  <tr>
                                    <th rowspan="2" class="right_border"><center>Name</center></th>
                                    <th colspan="5" class="right_border"><center>Orders</center></th>
                                    <th colspan="4" class="right_border"><center>Amount</center></th>
                                    <th colspan="2" class="right_border"><center>Commission</center></th>
                                  </tr>
                                  <tr>
                                    <!-- order section start-->
                                    <th class="right_border"><center>Target</center></th>
                                    <th class="right_border"><center>Chat</center></th>
                                    <th class="right_border"><center>Placed</center></th>
                                    <th class="right_border"><center>Shipped</center></th>
                                    <th class="right_border"><center>Delivered</center></th>
                                    <!-- order section end-->
                                    <!-- Amount section start-->
                                    <th class="right_border"><center>Target</center></th>
                                    <th class="right_border"><center>Total</center></th>
                                    <th class="right_border"><center>Shipped</center></th>
                                    <th class="right_border"><center>Delivered</center></th>
                                    <!-- Amount section end-->
                                    <!-- Commission section start-->
                                    <th class="right_border"><center>Succ Rate</center></th>
                                    <th class="right_border"><center>Amount</center></th>
                                    <!-- Commission section end-->
                                  </tr>
                                    {{-- <tr>
                                        <th>Name</th>
                                        <th><center>DAILY TARGET</center></th>
                                        <th><center>MONTHLY TARGET</center></th>
                                        <th><center>MONTHLY AMOUNT TARGET</center></th>
                                        <th><center>ORDERS PLACED</center></th>
                                        <th><center>ORDERS SHIPPED</center></th>
                                        <th><center>AVG ORDER VALUE</center></th>
                                        <th><center>TOTAL AMOUNT</center></th>
                                        <th><center>SHIPPED AMOUNT</center></th>
                                        <th><center>DELIVERED AMOUNT</center></th>
                                        <th><center>SUCCESS RATE</center></th>
                                        <th><center>ABOVE TARGET AMOUNT</center></th>
                                        <th><center>COMMISSION</center></th>
                                    </tr> --}}
                                </thead>
                                <tbody>
                                    @if($data)
                                    @php
                                      $gtotal_order_target    = 0;
                                      $gtotal_order_chats     = 0;
                                      $gtotal_order_placed    = 0;
                                      $gtotal_order_shipped   = 0;
                                      $gtotal_order_delivered = 0;
                                      $gtotal_delivered_amount_target =0;
                                      $gtotal_amount_placed = 0;
                                      $gtotal_amount_shipped = 0;
                                      $gtotal_amount_delivered = 0;
                                      $gtotal_ba_site_orders  = 0;
                                      $gtotal_ba_oms_orders   = 0;
                                      $gtotal_ba_app_android_orders = 0;
                                      $gtotal_df_site_orders        = 0;
                                      $gtotal_df_oms_orders         = 0;
                                      $gtotal_df_app_android_orders = 0;
                                      $gtotal_ba_oms_budget  = 0;
                                      $gtotal_df_oms_budget  = 0;
                                      $ba_online_budget_used = 0;
                                      $df_online_budget_used = 0;
                                    @endphp
                                    @foreach ($data as $row)
                                    @php
                                        $ba_site_orders             = $row->ba_site_orders;
                                        $ba_site_fbAd_orders             = $row->ba_site_fbAd_orders;
                                        $ba_oms_orders              = $row->ba_oms_orders;
                                        $ba_site_cancel_orders = $row->ba_site_cancel_orders;
                                        $ba_app_android_orders = $row->ba_app_android_orders;
                                        $ba_app_android_direct_orders = $row->ba_app_android_direct_orders;
                                        $ba_app_android_fbAd_orders = $row->ba_app_android_fbAd_orders;
                                        $ba_app_android_pushNt_orders = $row->ba_app_android_pushNt_orders;
                                        $df_site_orders             = $row->df_site_orders;
                                        $df_site_fbAd_orders             = $row->df_site_fbAd_orders;
                                        $df_oms_orders              = $row->df_oms_orders;
                                        $df_site_cancel_orders          = $row->df_site_cancel_orders;
                                        $df_app_android_orders = $row->df_app_android_orders;
                                        $df_app_android_direct_orders = $row->df_app_android_direct_orders;
                                        $df_app_android_fbAd_orders = $row->df_app_android_fbAd_orders;
                                        $df_app_android_pushNt_orders = $row->df_app_android_pushNt_orders;

                                        $placed_order          = $row->total_order;
                                        $shipped_orders        = $row->shipped_orders;
                                        $delivered_order       = $row->delivered_order;
                                        $target                = $row->daily_order_target;
                                        $total_chats       = 0;
                                        $total_budget_used =0;
                                        $budget_store = 0;
                                        $ba_budget_used = 0;
                                        $df_budget_used = 0;
                                        if( isset($row->chat_details) ){
                                          $total_chats     = $row->chat_details->total_chats;
                                          $total_budget_used    = $row->chat_details->total_budget_used;
                                          $ba_budget_used    = $row->chat_details->ba_total_budget_used;
                                          $df_budget_used    = $row->chat_details->df_total_budget_used;
                                          $budget_store = $row->chat_details->mainSetting->store_id;
                                          $cost_per_result  =  $total_chats == 0 ? 0 : $total_budget_used/$total_chats;
                                          $conversion_rate  =  $total_chats == 0 ? 0 : ($placed_order/$total_chats) * 100;
                                          $delivered_conversion_rate  = $total_chats == 0 ? 0 : ($delivered_order/$total_chats) * 100;
                                          $cost_per_placed_order =  $placed_order == 0 ? 0 :  ($total_budget_used/$placed_order);
                                          $cost_per_delivered_order =  $delivered_order == 0 ? 0 : ($total_budget_used/$delivered_order);
                                        }
                                        $monthly_order_target = $target*$daysWithoutHoliday;
                                        if( $row->user_id == 76 ){ //76 online customer
                                          $monthly_order_target = $target*$all_days;
                                          $ba_online_budget_used += $ba_budget_used;
                                          $df_online_budget_used += $df_budget_used;
                                        }
                                        $total_amount = $row->BAAmountTotal + $row->DFAmountTotal;
                                        $total_delivered_amount = $row->BADeliveredAmountTotal + $row->DFDeliveredAmountTotal;
                                        $total_shipped_amount = $row->BAShippedAmountTotal + $row->DFShippedAmountTotal;
                                        $avg_order_value = $total_amount/$placed_order;
                                        $delivery_success = $delivered_order/$placed_order *100;
                                        //
                                        $commission_on_delivered_amount = $row->commission_on_delivered_amount;
                                        $above_target_amount            = $total_delivered_amount - $commission_on_delivered_amount;
                                        $above_target_orders            = $placed_order - $monthly_order_target;
                                        $del_succ_cell_class = "btn-danger";
                                        if( $delivery_success >= $comm_settings->commission_qualify_delivery_success ){
                                          $del_succ_cell_class = "btn-success";
                                        }else if( $delivery_success >= $comm_settings->minimum_delivery_success ){
                                          $del_succ_cell_class = "btn-warning";
                                        }
                                        $commission_amount = 0;
                                        //commission calculation
                                        if( $above_target_amount > 0 &&  $row->commission_on_delivered_amount > 0 && $delivery_success >= $comm_settings->commission_qualify_delivery_success ){
                                          $commission_amount = $above_target_amount/100;
                                        }
                                        

                                    @endphp
                                        <tr style="border-bottom:1px solid black">
                                            <td width="10%">{{ $row->firstname." ".$row->lastname }}</td>
                                            {{-- <td align="center" >{{ $target }}</td> --}}
                                            <td align="center">{{ $monthly_order_target }}</td>
                                            <td align="center">{{ $total_chats }}</td>
                                            <td align="center" >{{ $placed_order }}</td>
                                            <td align="center">{{ $shipped_orders }}</td>
                                            <td align="center">{{ $delivered_order }}</td>
                                            <td align="center" width="10%">{{ number_format($commission_on_delivered_amount) }}</td>
                                            {{-- <td align="center" width="10%">{{ number_format($avg_order_value) }}</td> --}}
                                            <td align="center">{{ number_format($total_amount) }}</td>
                                            <td align="center">{{ number_format($total_shipped_amount) }}</td>
                                            <td align="center">{{ number_format($total_delivered_amount) }}</td>
                                            <td align="center" class="{{ $del_succ_cell_class }}"> {{  number_format($delivery_success,2) }} </td>
                                            {{-- <td align="center" width="10%">{{ number_format($above_target_amount) }}</td> --}}
                                            <td align="center" >{{ number_format($commission_amount) }} <span class="pull-right more_details">+</span></td>
                                        </tr>
                                        @if( $row->user_id == 76 )
                                          <tr id="user_det{{ $row->user_id }}" style="display:none; border:1px solid green !important;" class="user_det">
                                            <td colspan="6">
                                              <table class="table table-borderd">
                                                <thead style="background-color: #3379b7; color:white">
                                                  <tr>
                                                    <th><center>BA</center></th>
                                                    <th><center>Site</center></th>
                                                    <th><center>Budget</center></th>
                                                    <th><center>App</center></th>
                                                    <th><center>Budget</center></th>
                                                    <th><center>Cost/Order</center></th>
                                                    <th><center>Cost/Delivered</center></th>
                                                  </tr>
                                                </thead>
                                                <tbody>
                                                  <tr>
                                                    <th><center>Total Orders</center></th>
                                                    <td align="center">{{ $ba_site_orders }}</td>
                                                    <td align="center">{{ $ba_budget_used }}</td>
                                                    <td align="center">{{ $ba_app_android_orders }}</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>Direct Orders</center></th>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">{{ $ba_app_android_direct_orders  }}</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>FB Ad Orders</center></th>
                                                    <td align="center">{{ $ba_site_fbAd_orders }}</td>
                                                    <td align="center">-</td>
                                                    <td align="center">{{ $ba_app_android_fbAd_orders }}</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>G Ad Orders</center></th>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>Push Notifi</center></th>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">{{ $ba_app_android_pushNt_orders }}</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>Refferel Ord</center></th>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>Abandoned</center></th>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                            </td>
                                            <td colspan="6">
                                              <table class="table table-borderd">
                                                <thead style="background-color: #3379b7; color:white">
                                                  <tr>
                                                    <th><center>DF</center></th>
                                                    <th><center>Site</center></th>
                                                    <th><center>Budget</center></th>
                                                    <th><center>App</center></th>
                                                    <th><center>Budget</center></th>
                                                    <th><center>Cost/Order</center></th>
                                                    <th><center>Cost/Delivered</center></th>
                                                  </tr>
                                                </thead>
                                                <tbody>
                                                  <tr>
                                                    <th><center>Total Orders</center></th>
                                                    <td align="center">{{ $df_site_orders }}</td>
                                                    <td align="center">{{ $df_budget_used }}</td>
                                                    <td align="center">{{ $df_app_android_orders }}</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>Direct Orders</center></th>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">{{ $df_app_android_direct_orders }}</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>FB Ad Orders</center></th>
                                                    <td align="center">{{ $df_site_fbAd_orders }}</td>
                                                    <td align="center">-</td>
                                                    <td align="center">{{ $df_app_android_fbAd_orders }}</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>G Ad Orders</center></th>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>Push Notifi</center></th>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">{{ $df_app_android_pushNt_orders }}</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>Refferel Ord</center></th>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>Abandoned</center></th>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                    <td align="center">-</td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                            </td>
                                          </tr>
                                        @else
                                          <tr id="user_det{{ $row->user_id }}" style="display:none; border:1px solid green !important;" class="user_det">
                                            <td colspan="2">
                                                @if(isset($row->chat_details))
                                                <table class="table table-borderd">
                                                  <tbody>
                                                    <tr>
                                                      <th><center>Total Chat</center></th>
                                                      <td align="center">{{ $total_chats }}</td>
                                                    </tr>
                                                    <tr>
                                                      <th><center>Budget Used</center></th>
                                                      <td align="center">{{ $total_budget_used }}</td>
                                                    </tr>
                                                    <tr>
                                                      <th><center>Cost Per Result</center></th>
                                                      <td align="center"> {{ number_format($cost_per_result,3) }} </td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                                @endif
                                            </td>
                                            <td colspan="2">
                                              @if(isset($row->chat_details))
                                              <table class="table table-borderd">
                                                <tbody>
                                                  <tr>
                                                    <th><center>Conversion Rate/chat</center></th>
                                                    <td align="center">{{ number_format($conversion_rate,3) }}</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>D.Conversion Rate</center></th>
                                                    <td align="center">{{ number_format($delivered_conversion_rate,3) }}</td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>Cost Per Order</center></th>
                                                    <td align="center"> {{ number_format($cost_per_placed_order,3) }} </td>
                                                  </tr>
                                                  <tr>
                                                    <th><center>Cost Per D.Order</center></th>
                                                    <td align="center"> {{ number_format($cost_per_delivered_order,3) }} </td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                              @endif
                                            </td>
                                            <td colspan="2">
                                                @if($delivery_success >= $comm_settings->minimum_delivery_success)
                                                  <p style="color:green">Reach minimum delivery success rate.</p>
                                                @else
                                                  <p style="color:red">Below minimum delivery success rate.</p>
                                                @endif
                                                @if($placed_order >= $monthly_order_target)
                                                  <p style="color:green">Acheive Order Target.</p>
                                                @endif
                                                @if($placed_order <= $monthly_order_target)
                                                  <p style="color:red">Below Order Target.</p>
                                                @endif
                                                @if( $above_target_amount < 0 )
                                                  <p style="color:red">Below Deliver Amount Target.</p>
                                                @endif
                                                @if( $above_target_amount > 0 )
                                                  <p style="color:green">Acheive Deliver Amount Target.</p>
                                                @endif
                                                @if( $delivery_success >= $comm_settings->commission_qualify_delivery_success AND $above_target_amount > 0 )
                                                  @if( $commission_amount > 0 )
                                                    <p style="color:green">Qualified for {{ number_format($commission_amount) }} commission.</p>
                                                  @endif
                                                  @if( $above_target_orders > 0 )
                                                    <p style="color:green">Qualified for {{ $comm_settings->per_order_above_target_commission_amount }}  per order above order target.</p>
                                                    <p style="color:green">{{ $comm_settings->per_order_above_target_commission_amount }} x {{ $above_target_orders }}  =  {{ ($comm_settings->per_order_above_target_commission_amount * $above_target_orders) }}</p>
                                                  @endif
                                                @endif
                                            </td>
                                          </tr>
                                        @endif
                                        @php
                                        $gtotal_order_target    += $monthly_order_target;
                                        $gtotal_order_chats     += $total_chats;
                                        $gtotal_order_placed    += $placed_order;
                                        $gtotal_order_shipped   += $shipped_orders;
                                        $gtotal_order_delivered += $delivered_order;
                                        $gtotal_delivered_amount_target   += $commission_on_delivered_amount;
                                        $gtotal_amount_placed    += $total_amount;
                                        $gtotal_amount_shipped   += $total_shipped_amount;
                                        $gtotal_amount_delivered += $total_delivered_amount;
                                        ////summary total
                                        // for ba
                                        $gtotal_ba_site_orders        += $ba_site_orders;
                                        $gtotal_ba_oms_orders         += $ba_oms_orders;
                                        $gtotal_ba_app_android_orders += $ba_app_android_orders;
                                        $gtotal_df_site_orders        += $df_site_orders;
                                        $gtotal_df_oms_orders         += $df_oms_orders;
                                        $gtotal_df_app_android_orders += $df_app_android_orders;
                                        if( $budget_store == 1 ){
                                          $gtotal_ba_oms_budget  += $total_budget_used;
                                        }else if( $budget_store == 2 ){
                                          $gtotal_df_oms_budget  += $total_budget_used;
                                        }
                                        
                                        @endphp
                                    @endforeach
                                    @php
                                      $gtotal_delivery_success  =  $gtotal_order_placed > 0 ? $gtotal_order_delivered/$gtotal_order_placed * 100 : 0;
                                      //for ba
                                      $cost_per_ba_oms_order  =  $gtotal_ba_oms_orders == 0 ? 0 :  ($gtotal_ba_oms_budget/$gtotal_ba_oms_orders);
                                      $cost_per_ba_site_order =  $gtotal_ba_site_orders == 0 ? 0 :  ($ba_online_budget_used/$gtotal_ba_site_orders);
                                      //for df
                                      $cost_per_df_oms_order  =  $gtotal_df_oms_orders == 0 ? 0 :  ($gtotal_df_oms_budget/$gtotal_df_oms_orders);
                                      $cost_per_df_site_order =  $gtotal_df_site_orders == 0 ? 0 :  ($ba_online_budget_used/$gtotal_df_site_orders);
                                    @endphp
                                    <tr style="background-color: #3379b7; color:white">
                                      <td align="center" class="right_border"><strong>Total</strong></td>
                                      <td align="center" class="right_border"><strong>{{ number_format($gtotal_order_target) }}</strong></td>
                                      <td align="center" class="right_border"><strong>{{ number_format($gtotal_order_chats) }}</strong></td>
                                      <td align="center" class="right_border"><strong>{{ number_format($gtotal_order_placed) }}</strong></td>
                                      <td align="center" class="right_border"><strong>{{ number_format($gtotal_order_shipped)  }}</strong></td>
                                      <td align="center" class="right_border"><strong>{{ number_format($gtotal_order_delivered) }}</strong></td>
                                      <td align="center" class="right_border"><strong>{{ number_format($gtotal_delivered_amount_target) }}</strong></td>
                                      <td align="center" class="right_border"><strong>{{ number_format($gtotal_amount_placed) }}</strong></td>
                                      <td align="center" class="right_border"><strong>{{ number_format($gtotal_amount_shipped) }}</strong></td>
                                      <td align="center" class="right_border"><strong>{{ number_format($gtotal_amount_delivered) }}</strong></td>
                                      <td align="center" class="right_border"><strong>{{ number_format($gtotal_delivery_success,2) }}</strong></td>
                                    </tr>
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">No data found!</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if( session('user_group_id') != 12 )
                          <table class="table" style="width:50%; margin-top:4px;">
                            <thead style="background-color: #3379b7; color:white">
                              <th align="center"></th>
                              <th align="center"></th>
                              <th align="center" class="right_border"><center>Orders</center></th>
                              <th align="center" class="right_border"><center>Budget</center></th>
                              <th align="center" class="right_border"><center>Cost/Order</center></th>
                              <th align="center" class="right_border"><center>Cost/Delivered</center></th>
                            </thead>
                            <tbody>
                              <tr>
                                <td rowspan="4" class="right_border_bk" align="center"><strong>BA</strong></td>
                              </tr>
                              <tr>
                                <td  class="right_border_bk" align="center"><strong>Site</strong></td>
                                <td align="center" class="right_border_bk">{{ $gtotal_ba_site_orders }}</td>
                                <td align="center" class="right_border_bk">{{ $ba_online_budget_used }}</td>
                                <td align="center" class="right_border_bk">{{ number_format($cost_per_ba_site_order,1) }}</td>
                                <td align="center" class="right_border_bk">-</td>
                              </tr>
                              <tr>
                                <td class="right_border_bk" align="center"><strong>APP</strong></td>
                                <td align="center" class="right_border_bk">{{ $gtotal_ba_app_android_orders }}</td>
                                <td align="center" class="right_border_bk">-</td>
                                <td align="center" class="right_border_bk">-</td>
                                <td align="center" class="right_border_bk">-</td>
                              </tr>
                              <tr>
                                <td class="right_border_bk" align="center"><strong>OMS</strong></td>
                                <td align="center" class="right_border_bk">{{ $gtotal_ba_oms_orders }}</td>
                                <td align="center" class="right_border_bk">{{ $gtotal_ba_oms_budget }}</td>
                                <td align="center" class="right_border_bk">{{ number_format($cost_per_ba_oms_order,2) }}</td>
                                <td align="center" class="right_border_bk">-</td>
                              </tr>
                              <tr>
                                <td rowspan="4" class="right_border_bk" align="center"><strong>DF</strong></td>
                              </tr>
                              <tr>
                                <td  class="right_border_bk" align="center"><strong>Site</strong></td>
                                <td align="center" class="right_border_bk">{{ number_format($gtotal_df_site_orders) }}</td>
                                <td align="center" class="right_border_bk">{{ $df_online_budget_used }}</td>
                                <td align="center" class="right_border_bk">{{ number_format($cost_per_df_site_order,1) }}</td>
                                <td align="center" class="right_border_bk">-</td>

                              </tr>
                              <tr>
                                <td class="right_border_bk" align="center"><strong>APP</strong></td>
                                <td align="center" class="right_border_bk">{{ $gtotal_df_app_android_orders }}</td>
                                <td align="center" class="right_border_bk">-</td>
                                <td align="center" class="right_border_bk">-</td>
                                <td align="center" class="right_border_bk">-</td>
                              </tr>
                              <tr>
                                <td class="right_border_bk" align="center"><strong>OMS</strong></td>
                                <td align="center" class="right_border_bk">{{ $gtotal_df_oms_orders }}</td>
                                <td align="center" class="right_border_bk">{{ number_format($gtotal_df_oms_budget) }}</td>
                                <td align="center" class="right_border_bk">{{ number_format($cost_per_df_oms_order,2) }}</td>
                                <td align="center" class="right_border_bk">-</td>
                              </tr>
                            </tbody>
                          </table>
                        @endif

            </div>
           

                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
<script>
  $(document).on('click','.more_details',function(){
    var element_html = $(this).html();
    //$('.user_det').hide();
    if( element_html === "+" ){
     $(this).html('-');
     $(this).parent().parent().next('tr').show();
    }else if( element_html === "-" ){
     $(this).html('+');
     $(this).parent().parent().next('tr').hide();
    }
  });
  function showDates(){
    var duration = $('#by_duration').val();
    //alert(duration);
    if( duration == "custom" ){
      $('.custom_duration').show();
    }else{
      $('.custom_duration').hide();
    }
  }
</script>
@endpush
