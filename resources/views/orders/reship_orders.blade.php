@extends('layouts.app')

@section('content')

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
                                    <tr class="row_{{ $order->order_id }}">
                                        <td class="col-sm-1"><center>{{ $order->order_id }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->firstname }} {{ $order->lastname }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center><span class="badge badge-warning blue darken-1">{{ $order->courier_name }}</span></center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->date_added }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->date_modified }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->telephone }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->email }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->total }} </center></td>
                                    </tr>
                                    <tr class="row_{{ $order->order_id }}">
                                        <td>&nbsp;</td>
                                        <td colspan="2"><strong>Address:</strong>{{ $order->payment_area }},{{ $order->payment_address_1 }},{{ $order->shipping_address_2 }}</td>
                                        <td colspan="1"><strong>City:</strong>{{ $order->shipping_city ? $order->shipping_city : $order->shipping_zone }}</td>
                                        <td colspan="5" >@include('orders.order_progress_bar')</td>
                                    </tr>
                                    @if( $order->orderd_products )
                                        <tr class="row_{{ $order->order_id }}">
                                            <td colspan="8">
                                                <center>
                                                <table width="100%" style="font-size:12px;">
                                                    @foreach ( $order->orderd_products as $ordered_product )
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td><img src="{{ $ordered_product?->product_details?->image }}" /></td>
                                                            <td>{{ $ordered_product->name }}<br>
                                                                @forelse ($ordered_product->order_options as $order_option )
                                                                    <strong>{{ $order_option->name }} :</strong> {{ $order_option->value }}
                                                                @empty
                                                                @endforelse
                                                            </td>
                                                            <td>{{ $ordered_product->model }}</td>
                                                            <td>{{ $ordered_product->quantity }}</td>
                                                            <td>{{ $ordered_product->price }}</td>
                                                            <td>{{ $ordered_product->total }}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                                </center>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr  class="order-action" class="row_{{ $order->order_id }}" style="border-bottom: 7px solid #e9e9e9 !important">
                                        <td>
                                          <a class="btn btn-warning darken-1" data-orderid="{{ $order->order_id }}" data-store="{{ $order->store  }}" id="order_history" data-toggle="modal" data-target="#historyModal">History</a>
                                        </td>
                                        <td colspan="10">
                                            <div class="row">
                                                <div class="col-1">
                                                </div>
                                                @if ( $order->oms_order_status == 3 )
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
                                                            <a href="javascript:void(0)" class="waves-effect waves-blue btn btn-success" onclick="confirmReship({{ $order->order_id }})">Approve Reship</a>
                                                        </div>
                                                    </form>
                                                @endif
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
@include('orders.edit_customer_popup')
@include('orders.popup_forwordpicklist_courier');
@include('orders.popup_courier_tracking');
@include('orders.popup_order_details');
@include('orders.popup_order_activity_log');
@include('orders.ccvaneue_details');
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
