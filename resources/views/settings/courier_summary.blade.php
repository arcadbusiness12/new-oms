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
  .btn-danger {
    background: #ec4152 !important;
  }
  .btn-success {
    background: green !important;
  }
  .btn-warning {
    background: #f3c547 !important;
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
                              <form name="filter_reports" id="filter_reports" method="get" action="{{ route('commission.sale.courierSummary') }}">
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
                                          <select name="search_by_courier">
                                            <option value="">-Courier-</option>
                                             @foreach( $couriers as $courier)
                                              <option value="{{ $courier->shipping_provider_id }}">{{ $courier->name }}</option>
                                             @endforeach
                                          </select>
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
                          <table class="table table-hover">
                            <thead style="background-color: #3379b7; color:white">
                              <tr>
                                <th rowspan="2" class="right_border"><center>Name</center></th>
                                <th colspan="5" class="right_border"><center>Orders</center></th>
                                <th colspan="3" class="right_border"><center>Amount</center></th>
                              </tr>
                              <tr>
                                <!-- order section start-->
                                <th class="right_border"><center>Total</center></th>
                                <th class="right_border"><center>Shipped</center></th>
                                <th class="right_border"><center>Rec.Back</center></th>
                                <th class="right_border"><center>Returned</center></th>
                                <th class="right_border"><center>Delivered</center></th>
                                <!-- order section end-->
                                <!-- Amount section start-->
                                <th class="right_border"><center>Total</center></th>
                                <th class="right_border"><center>Shipped</center></th>
                                <th class="right_border"><center>Delivered</center></th>
                                <!-- Amount section end-->
                                <!-- Commission section start-->
                                <th class="right_border"><center>Success Rate</center></th>
                                <!-- Commission section end-->
                              </tr>
                            </thead>
                            <tbody>
                                @if($data)
                                @php
                                  $target = 0;
                                  $gtotal_order_placed = 0;
                                  $gtotal_order_retured = 0;
                                  $gtotal_order_target = 0;
                                  $gtotal_order_shipped = 0;
                                  $gtotal_order_delivered = 0;
                                  $gtotal_order_returned = 0;
                                  $gtotal_amount_total = 0;
                                  $gtotal_amount_shipped = 0;
                                  $gtotal_receive_back_orders = 0;
                                  $gtotal_amount_delivered = 0;
                                @endphp
                                @foreach ($data as $row)
                                @php
                                    $tot_exchange          = $row->tot_exchange;
                                    $tot_exchange_shipped  = $row->tot_exchange_shipped;
                                    $tot_exchange_delivered= $row->tot_exchange_delivered;
                                    $placed_order          = $row->total_order + $tot_exchange;
                                    $normal_order          = $row->normal_orders;
                                    $reship_order          = $row->reship_orders;
                                    $shipped_orders        = $row->shipped_orders + $tot_exchange_shipped;
                                    $receive_back_orders   = $row->receive_back_orders;
                                    $delivered_order       = $row->delivered_order + $tot_exchange_delivered;
                                    $returned_order        = $row->returned_orders;
                                   
                                   
                                    $total_amount = $row->BAAmountTotal + $row->DFAmountTotal;
                                    $total_delivered_amount = $row->BADeliveredAmountTotal + $row->DFDeliveredAmountTotal;
                                    $total_shipped_amount = $row->BAShippedAmountTotal + $row->DFShippedAmountTotal;
                                    $avg_order_value = $total_amount/$placed_order;
                                    $delivery_success = $delivered_order/$placed_order *100;
                                    if( $delivery_success < 70 ){
                                      $del_succ_cell_class = "btn-danger";
                                    }else if( $delivery_success >= 70 && $delivery_success < 75  ){
                                      $del_succ_cell_class = "btn-warning";
                                    }else if( $delivery_success >= 75 ){
                                      $del_succ_cell_class = "btn-success";
                                    }
                                   
                                    $commission_amount = 0;
                                    //commission calculation
                                @endphp
                                    <tr style="border-bottom:1px solid black">
                                        <td width="10%">{{ $row->name }}</td>
                                        <td align="center">{{ $placed_order }}</td>
                                        <td align="center">{{ $shipped_orders }}</td>
                                        <td align="center">{{ $receive_back_orders }}</td>
                                        <td align="center">{{ $returned_order }}</td>
                                        <td align="center">{{ $delivered_order }}</td>
                                        <td align="center">{{ number_format($total_amount) }}</td>
                                        <td align="center">{{ number_format($total_shipped_amount) }}</td>
                                        <td align="center">{{ number_format($total_delivered_amount) }}</td>
                                        <td align="center" class="{{ $del_succ_cell_class }}"> {{  number_format($delivery_success,2) }} <span class="pull-right more_details">+</span> </td>
                                    </tr>
                                      <tr id="user_det{{ $row->shipping_provider_id }}" style="display:none; border:1px solid green !important;" class="user_det">
                                        <td colspan="2">
                                            <table class="table table-borderd">
                                              <tbody>
                                                <tr>
                                                  <th><center>Normal:</center></th>
                                                  <td align="center">{{ $normal_order }} </td>
                                                  <th><center>Reship:</center></th>
                                                  <td align="center">{{ $reship_order }}</td>
                                                  <th><center>Exchange:</center></th>
                                                  <td align="center">{{ $tot_exchange }}</td>
                                                </tr>
                                                <!-- <tr>
                                                  <th><center>Reship</center></th>
                                                  <td align="center">{{ $reship_order }}</td>
                                                </tr> -->
                                              </tbody>
                                            </table>
                                        </td>
                                        <!-- <td colspan="2">
                                            
                                            
                                        </td> -->
                                      </tr>
                                    @php
                                    $gtotal_order_placed     += $placed_order;
                                    $gtotal_order_shipped    += $shipped_orders;
                                    $gtotal_receive_back_orders    += $receive_back_orders;
                                    $gtotal_order_retured    += $returned_order;
                                    $gtotal_order_delivered  += $delivered_order;
                                    //amount total
                                    $gtotal_amount_total     += $total_amount;
                                    $gtotal_amount_shipped   += $total_shipped_amount;
                                    $gtotal_amount_delivered += $total_delivered_amount;
                                    ////summary total
                                    
                                    
                                    @endphp
                                @endforeach
                                @php
                                  $gtotal_delivery_success  =  $gtotal_order_delivered > 0 ? $gtotal_order_delivered/$gtotal_order_placed * 100 : 0;
                                  //$gtotal_delivery_success  =  0;
                                @endphp
                                <tr style="background-color: #3379b7; color:white">
                                  <td align="center" class="right_border"><strong>Total</strong></td>
                                  <td align="center" class="right_border"><strong>{{ number_format($gtotal_order_placed)  }}</strong></td>
                                  <td align="center" class="right_border"><strong>{{ number_format($gtotal_order_shipped)  }}</strong></td>
                                  <td align="center" class="right_border"><strong>{{ number_format($gtotal_receive_back_orders)  }}</strong></td>
                                  <td align="center" class="right_border"><strong>{{ number_format($gtotal_order_retured)  }}</strong></td>
                                  <td align="center" class="right_border"><strong>{{ number_format($gtotal_order_delivered) }}</strong></td>
                                  <td align="center" class="right_border"><strong>{{ number_format($gtotal_amount_total) }}</strong></td>
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
  $(document).ready(function() {
    var input = <?php echo count($old_input) ?>;
    if(input == 0) {
      $('#search_filter').click();
    }
    
  });
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
