@extends('layouts.app')

@section('content')
<style>
   .area_type_option{
    width: 110px !important;
  }
  .alert-success {
    position: sticky;
    /* Define at what point you would like it to stay fixed */
    top: 0; 
    z-index: 999;
  }
  .btn-success {
    background-color: green !important;
  }
</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">


            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">

                    <div class="card no-b form-box">
                        @if(session()->has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                            </div>
                        @endif

                        <div class="card-header white">
                            <div class="panel-body">
                                <form name="filter_reports" id="filter_reports" method="get" action="{{ route('employee-performance.commission.report') }}">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                            <div class="form-line">
                                            <label class="form-label">From</label>
                                                  <input type="text" name="date_from" id="date_from" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" value="{{ @$old_input['date_from'] != "" ? $old_input['date_from'] : date('Y-m').'-01'  }}">
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                            <div class="form-line">
                                            <label class="form-label">To</label>
                                                  <input type="text" name="date_to" id="date_to" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" value="{{ @$old_input['date_to'] != "" ? $old_input['date_to'] : date('Y-m-d')  }}">
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                              <div class="form-line">
                                                   <label class="form-label" for="status">User</label> 
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
                        <form action="<?php echo route('shipping.providers') ?>" method="post" name="form-setting">
                            {{ csrf_field() }}
                          <div class="table-responsive">
                            
                            <table class="table table-hover">
                                <thead style="background-color: #3379b7; color:white">
                                  
                                  <tr>
                                    <!-- order section start-->
                                    <th class="right_border"><center>Name</center></th>
                                    <th class="right_border"><center>Target</center></th>
                                    <th class="right_border"><center>Site/App Chat</center></th>
                                    <th class="right_border"><center>Placed</center></th>
                                    <th class="right_border"><center>Shipped</center></th>
                                    <th class="right_border"><center>Delivered</center></th>
                                    <th class="right_border"><center>Approve Order</center></th>
                                    <th class="right_border"><center>Cancel Order</center></th>
                                    <th class="right_border"><center>Approve Exchange</center></th>
                                    <!-- order section end-->
                                    <!-- Amount section start-->
                                    {{-- <th class="right_border"><center>Target</center></th>
                                    <th class="right_border"><center>Total</center></th>
                                    <th class="right_border"><center>Shipped</center></th>
                                    <th class="right_border"><center>Delivered</center></th> --}}
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
                                    @foreach ($data as $row)
                                    @php
                                    
                                        $placed_order    = $row->total_order;
                                        $shipped_orders  = $row->shipped_orders;
                                        $delivered_order = $row->delivered_order;
                                        $total_approve_orders   = @$row->total_approve_orders;
                                        $total_approve_exchange = @$row->total_approve_exchange;
                                        if( isset($row->chat_details) ){
                                          $total_chats     = $row->chat_details->total_chats;
                                          $total_budget_used    = $row->chat_details->total_budget_used;
                                          $cost_per_result  = $total_budget_used/$total_chats;
                                          $conversion_rate  =  $total_chats == 0 ? 0 : ($placed_order/$total_chats) * 100;
                                          $delivered_conversion_rate  = $total_chats == 0 ? 0 : ($delivered_order/$total_chats) * 100;
                                          $cost_per_placed_order =  $placed_order == 0 ? 0 :  ($total_budget_used/$placed_order);
                                          $cost_per_delivered_order =  $delivered_order == 0 ? 0 : ($total_budget_used/$delivered_order);
                                        }
                                        $monthly_order_target = $row->daily_order_target*$daysWithoutHoliday;
                                        $total_amount = $row->BAAmountTotal + $row->DFAmountTotal;
                                        $total_delivered_amount = $row->BADeliveredAmountTotal + $row->DFDeliveredAmountTotal;
                                        $total_shipped_amount = $row->BAShippedAmountTotal + $row->DFShippedAmountTotal;
                                        $avg_order_value = $total_amount/$row->total_order;
                                        $delivery_success = $row->delivered_order/$row->total_order *100;
                                        //
                                        if($delivery_success >= 77 && $delivery_success < 79) {
                                          $amount = 200;
                                        }elseif($delivery_success >= 79  && $delivery_success < 82) {
                                          $amount = 500; 
                                        }elseif($delivery_success >= 82) {
                                          $amount = 1000; 
                                        }else {
                                          $amount = 0; 
                                        }
                                        $commission_on_delivered_amount = ($row->commission_on_delivered_amount/26) * $daysWithoutHoliday;
                                        $above_target_amount            = $total_delivered_amount - $commission_on_delivered_amount;
                                        $above_target_orders            = $row->total_order - $monthly_order_target;
                                        $del_succ_cell_class = "btn-danger";
                                        if( $delivery_success >= $comm_settings->commission_qualify_delivery_success ){
                                          $del_succ_cell_class = "btn-success";
                                        }else if( $delivery_success >= $comm_settings->minimum_delivery_success ){
                                          $del_succ_cell_class = "btn-warning";
                                        }
                                        $commission_amount = 0;
                                        //commission calculation
                                        // if( $comm_settings && count($comm_settings->commission_conditions_amount) > 0 && $delivery_success >= $comm_settings->commission_qualify_delivery_success ){
                                        //   foreach( $comm_settings->commission_conditions_amount as $key => $setting_amount ){
                                        //     if( $above_target_amount >= $setting_amount->amount_from && $above_target_amount <= $setting_amount->amount_to  ){
                                        //       $commission_amount =  $setting_amount->commission;
                                        //       break;
                                        //     }
                                        //   }
                                        // }
                                    @endphp
                                        <tr style="border-bottom:1px solid black">
                                            <td width="10%">{{ $row->firstname." ".$row->lastname }}</td>
                                            {{-- <td align="center" >{{ $row->daily_order_target }}</td> --}}
                                            <td align="center">{{ $monthly_order_target }}</td>
                                            <td align="center">{{ @$row->customer_chats }}</td>
                                            <td align="center" >{{ $placed_order }}</td>
                                            <td align="center">{{ $shipped_orders }}</td>
                                            <td align="center">{{ $delivered_order }}</td>
                                            <td align="center">{{ $total_approve_orders }}</td>
                                            <td align="center">{{ @$row->total_cancel_order }}</td>
                                            <td align="center">{{ $total_approve_exchange }}</td>
                                            {{-- <td align="center" width="10%">{{ number_format($commission_on_delivered_amount) }}</td> --}}
                                            {{-- <td align="center" width="10%">{{ number_format($avg_order_value) }}</td> --}}
                                            {{-- <td align="center">{{ $total_amount }}</td> --}}
                                            {{-- <td align="center">{{ $total_shipped_amount }}</td> --}}
                                            {{-- <td align="center">{{ $total_delivered_amount }}</td> --}}
                                            <td align="center" class="{{ $del_succ_cell_class }}"> {{  number_format($delivery_success,2) }} </td>
                                            {{-- <td align="center" width="10%">{{ number_format($above_target_amount) }}</td> --}}
                                            <td align="center" >
                                              {{ $amount }}
                                               {{-- <span class="pull-right more_details">+</span> --}}
                                              </td>
                                        </tr>
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
                                                  <th><center>Conversion Rate</center></th>
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
                                              @if($row->total_order >= $monthly_order_target)
                                                <p style="color:green">Acheive Order Target.</p>
                                              @endif
                                              @if($row->total_order <= $monthly_order_target)
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
                                                  <p style="color:green">Qualified for {{ $commission_amount }} commission.</p>
                                                @endif
                                                @if( $above_target_orders > 0 )
                                                  <p style="color:green">Qualified for {{ $comm_settings->per_order_above_target_commission_amount }}  per order above order target.</p>
                                                  <p style="color:green">{{ $comm_settings->per_order_above_target_commission_amount }} x {{ $above_target_orders }}  =  {{ ($comm_settings->per_order_above_target_commission_amount * $above_target_orders) }}</p>
                                                @endif
                                              @endif
                                          </td>
                                        </tr>
                                    @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">No data found!</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            
                        </div>
                        </form>
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
<script type="text/javascript">
$(document).ready(function() {
    var old_input = <?php echo count($old_input)?>;
    if(old_input == 0) {
        $('#search_filter').click();
    }
})

</script>
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
@endpush
