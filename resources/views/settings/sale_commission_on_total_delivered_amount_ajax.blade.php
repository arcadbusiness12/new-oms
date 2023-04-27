  <table style="width:100%; color:white">
                                <thead>
                                  <tr>
                                    <!-- order section start-->
                                    {{--  <th><center>Target</center></th>  --}}
                                    <th><center>Chat</center></th>
                                    <th><center>Placed</center></th>
                                    <th><center>Shipped</center></th>
                                    <th><center>Delivered</center></th>
                                    <!-- order section end-->
                                    <!-- Amount section start-->
                                    {{--  <th><center>Target</center></th>  --}}
                                    {{--  <th><center>Total</center></th>  --}}
                                    <th><center>Ship Amt</center></th>
                                    <th><center>Delv Amt</center></th>
                                    <th align="center"><a href="{{ route('commission.sale.saleOnTotalDeliveredAmount') }}" style="width:100%; color:white" title="Click for full report"><i class="material-icons">more</i></a></th>

                                    <!-- Amount section end-->
                                    <!-- Commission section start-->
                                    {{--  <th><center>Succ Rate</center></th>
                                    <th><center>Amount</center></th>  --}}
                                    <!-- Commission section end-->
                                  </tr>
                                </thead>
                                <tbody>
                                    @if($row)
                                    @php
                                        $placed_order    = $row->total_order;
                                        $shipped_orders  = $row->shipped_orders;
                                        $delivered_order = $row->delivered_order;
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
                                        if( $comm_settings && count($comm_settings->commission_conditions_amount) > 0 && $delivery_success >= $comm_settings->commission_qualify_delivery_success ){
                                          foreach( $comm_settings->commission_conditions_amount as $key => $setting_amount ){
                                            if( $above_target_amount >= $setting_amount->amount_from && $above_target_amount <= $setting_amount->amount_to  ){
                                              $commission_amount =  $setting_amount->commission;
                                              break;
                                            }
                                          }
                                        }
                                    @endphp
                                        <tr>
                                            {{--  <td width="10%">{{ $row->firstname." ".$row->lastname }}</td>  --}}
                                            {{-- <td align="center" >{{ $row->daily_order_target }}</td> --}}
                                            {{--  <td align="center">{{ $monthly_order_target }}</td>  --}}
                                            <td align="center">{{ @$row->chat_details->total_chats }}</td>
                                            <td align="center" >{{ $placed_order }}</td>
                                            <td align="center">{{ $shipped_orders }}</td>
                                            <td align="center">{{ $delivered_order }}</td>
                                            {{--  <td align="center" width="10%">{{ number_format($commission_on_delivered_amount) }}</td>  --}}
                                            {{-- <td align="center" width="10%">{{ number_format($avg_order_value) }}</td> --}}
                                            {{--  <td align="center">{{ $total_amount }}</td>  --}}
                                            <td align="center">{{ $total_shipped_amount }}</td>
                                            <td align="center">{{ $total_delivered_amount }}</td>
                                            {{--  <td align="center" class="{{ $del_succ_cell_class }}"> {{  number_format($delivery_success,2) }} </td>  --}}
                                            {{-- <td align="center" width="10%">{{ number_format($above_target_amount) }}</td> --}}
                                            {{--  <td align="center" >{{ $commission_amount }} <span class="pull-right more_details">+</span></td>  --}}
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">No data found!</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

@push('scripts')

@endpush