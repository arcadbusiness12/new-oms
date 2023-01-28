@extends('layouts.app')

@section('content')
<style>
    .sw-theme-circles>ul.step-anchor:before {
        //top: 36%!important;
        width: 68%!important;
        margin-left: 39px!important;
    }
</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">
                            @include('orders.order_search_section')
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        {{--  <div class="panel-heading">Inventory Dashboard</div>  --}}
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif

                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="font-size: 13px !important">
                             <thead>
                              <tr style="background-color: #3f51b5;color:white">
                                <th scope="col"><center>Order Id</center></th>
                                <th scope="col"><center>Customer</center></th>
                                <th scope="col"><center>Status</center></th>
                                <th scope="col"><center>Date Added</center></th>
                                <th scope="col"><center>Date Modified</center></th>
                                <th scope="col"><center>Telephone</center></th>
                                <th scope="col"><center>Email</center></th>
                                <th scope="col"><center>Total</center></th>
                               </tr>
                             </thead>
                             <tbody>
                                @if($data->count())
                                @foreach($data as $key=>$order)
                                    @php
                                    if( $order->OmsOrder?->generatedCourier?->name != ""  ){
                                        $courier = $order->OmsOrder?->generatedCourier?->name;
                                        $courier_link = '<a href="javascript:void(0)" onclick="trackOrderCourier('.$order->order_id.','.$courier.')" data-toggle="modal" data-target="#courierTrackingModal"><span class="badge badge-warning blue darken-1">'.$courier.'</span></a>';
                                    }else{
                                        $courier_link =  $order->OmsOrder?->assignedCourier?->name;
                                    }
                                    @endphp
                                    <tr class="row_{{ $order->order_id }}">
                                        <td class="col-sm-1"><center>{{ $order->order_id }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->firstname }} {{ $order->lastname }}</center></td>
                                        @if( $order->OmsOrder?->generatedCourier?->name != ""  )
                                            @php
                                                $courier = $order->OmsOrder?->generatedCourier?->name;
                                            @endphp
                                            <td class="column col-sm-1 td-valign"><center><a href='javascript:void(0)' onclick='trackOrderCourier({{ $order->order_id }},{{ $order->store }},"{{ $courier }}",0)'  data-toggle='modal' data-target='#courierTrackingModal'><span class='badge badge-warning blue darken-1'>{{ $courier }}</span></a><span class="badge orange darken-1"><strong>{{  $order->omsStore->name  }}</strong></span></center></td>
                                        @else
                                            <td class="column col-sm-1 td-valign"><center>{{ $order->OmsOrder?->assignedCourier?->name }}<span class="badge orange darken-1"><strong>{{  $order->omsStore->name  }}</strong></span></center></td>
                                        @endif
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->date_added }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->OmsOrder?->updated_at}} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->mobile }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->email }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->total_amount }} </center></td>
                                    </tr>
                                    <tr class="row_{{ $order->order_id }}">
                                        <td>&nbsp;</td>
                                        <td colspan="2"><strong>Address:</strong>{{ $order->payment_area }},{{ $order->payment_address_1 }},{{ $order->shipping_address_2 }}</td>
                                        <td colspan="1"><strong>City:</strong>{{ $order->shipping_city ? $order->shipping_city : $order->shipping_zone }}</td>
                                        <td colspan="5" >@include('orders.order_progress_bar')</td>
                                    </tr>
                                    @if( $order->orderProducts )
                                        <tr class="row_{{ $order->order_id }}">
                                            <td colspan="8">
                                                <center>
                                                    <table width="100%" style="font-size:12px;">
                                                        @foreach ( $order->orderProducts as $ordered_product )
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td><input type="checkbox" class="product_{{ $order->order_id }} check_product_for_exchange" name="ordered_product_ids[{{ $ordered_product->product_option_id }}]" order-id={{ $order->order_id }} value="{{ $ordered_product->product_id }}" style="display:none" /></td>
                                                                <td style="width: 5%;"><img src="{{ URL::asset('uploads/inventory_products/'.$ordered_product?->product?->image) }}" /></td>
                                                                <td>{{ $ordered_product->name }}<br>
                                                                    @if(  $ordered_product->product?->option_value > 0  )
                                                                        <strong>{{ $ordered_product->option_name }}</strong> : {{ $ordered_product->option_value }} ,
                                                                    @endif
                                                                    <strong>Color : </strong>{{ $ordered_product->product?->option_name }}</td>
                                                                <td>{{ $ordered_product->sku }}</td>
                                                                <td>{{ $ordered_product->quantity }}</td>
                                                                <td>{{ $ordered_product->price }}</td>
                                                                <td>{{ $ordered_product->total }}</td>
                                                            </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td colspan="11"><input type="submit" id="create_exchange_{{ $order->order_id }}" class="float-right btn btn-sm btn-success active" value="Create Exchange" style="display:none"></td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr  class="order-action" class="row_{{ $order->order_id }}" style="border-bottom: 7px solid #e9e9e9 !important">
                                        <td></td>
                                        <td>
                                          <a class="btn btn-warning active" data-orderid="{{ $order->order_id }}" data-store="{{ $order->store  }}" id="order_history" data-toggle="modal" data-target="#historyModal">History</a>
                                        </td>
                                        <td colspan="10">
                                            <div class="row">
                                                <div class="col-1">
                                                </div>
                                                    <form  id="frm_{{ $order->order_id }}" method="post" action="{{ url('orders/reship-orders') }}" class="form-inline">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{$order->order_id}}" />
                                                        <input type="hidden" name="store" value="{{$order->store}}" />
                                                        <div class="col-2">
                                                            <span id="error_span" style="color: red"></span>
                                                            <select name="reassign_courier" id="reassign_courier_{{ $order->order_id }}" required>
                                                                <option value="0">Assign Courier</option>
                                                                @forelse($couriers as $key => $courier)
                                                                    <option value="{{ $courier->shipping_provider_id }}">{{ $courier->name }}</option>
                                                                @empty
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                        <div class="col-1">
                                                            <a href="javascript:void(0)" class="waves-effect waves-blue btn btn-success active" onclick="confirmReship({{ $order->order_id }})">Approve Reship</a>
                                                        </div>
                                                    </form>
                                            </div>
                                        </td>
                                     </tr>
                                @endforeach
                                @endif
                             </tbody>
                            </table>
                        {{  $data->appends(@$old_input)->render() }}
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
{{--  @include('orders.edit_customer_popup')  --}}
{{--  @include('orders.popup_forwordpicklist_courier')  --}}
@include('orders.popup_courier_tracking')
{{--  @include('orders.popup_order_details')  --}}
@include('orders.popup_order_activity_log')
{{--  @include('orders.ccvaneue_details')  --}}
<style>
.table td {
    border-top: none !important;
}
thead, tbody, tfoot, tr, td, th {
    border: none !important;
}
</style>
@endsection

@push('scripts')
<script>
    function confirmReship(order_id){
        var courier = $("#reassign_courier_"+order_id).val();
        if( courier < 1  ){
          $('#error_span').html("Select courier");
          setTimeout(function(){
            $('#error_span').html("");
          }, 5000);
          return;
        }

        return swal({
            title: "Are you sure, you want to reship this order.",
            // text: "You want to cancel this order?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, I am sure!',
            cancelButtonText: "No, I don't want!",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            // alert(isConfirm);
            if(isConfirm){
                //ok button clicked
                $('#frm_'+order_id).submit();
            }else{
                //cancel button clicked
            }
        });
     }
</script>
@endpush
