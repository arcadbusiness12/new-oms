@extends('layouts.app')
@section('title', 'Pick List View')
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
                        <div class="panel-heading">
                            <div class="col-sm-10 col-grid">
                                Pick List
                            </div>
                            <div class="col-sm-2 col-grid">
                                <a href="javascrip:void()" onclick="$('#frm_print_pick_list').submit()" class="btn btn-success btn-sm float-right">Print Picking list</a>
                            </div>
                        </div>
                        <form method="get" target="_blank" id="frm_print_pick_list">
                            {{--  <<button type="submit" class="btn btn-success pull-right">Print Picking list</button>  --}}
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="font-size: 13px !important">
                             <thead>
                              <tr style="background-color: #3f51b5;color:white">
                                <td><input name="o_id[]" value="20000030" type="checkbox" id="md_checkbox_20000030" class="chk-col-green fwd-ordr-generate-awb-checkbox"></td>
                                <th scope="col"><center>Order Id</center></th>
                                <th scope="col"><center>Customer</center></th>
                                <th scope="col"><center>Store</center></th>
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
                                        <td class="col-sm-1"><input name="o_id[]" value="{{ $order->order_id }}" type="checkbox" id="md_checkbox_{{ $order->order_id }}" class="chk-col-green fwd-ordr-generate-awb-checkbox"></td>
                                        <td class="col-sm-1"><center>{{ $order->order_id }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->firstname }} {{ $order->lastname }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center><span class="badge badge-warning blue darken-1">{{ $order->courier_name }}</span><span class="badge orange darken-1"><strong>{{  $order->omsStore->name  }}</strong></span></center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->created_at }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->omsOrder->updated_at }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->mobile }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->email }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->total_amount }} </center></td>
                                    </tr>
                                    <tr class="row_{{ $order->order_id }}">
                                        <td>@if( $order->omsOrder?->picklist_print == 1 )<span class="badge badge-success green darken-1"><strong>Printed<strong></span> @endif</td>
                                        <td colspan="2"><strong>Address:</strong>{{ $order->shipping_city_area }},{{ $order->shipping_address_1 }},{{ $order->shipping_street_building }},{{ $order->shipping_villa_flat }}</td>
                                        <td colspan="2"><strong>City:</strong>{{ $order->shipping_city }}</td>
                                        <td colspan="4" >
                                            <div class="normal-order-progress">
                                                @include('orders.order_progress_bar')</td>
                                            </div>
                                        </td>
                                    </tr>
                                    @if( $order->orderProducts )
                                        <tr class="row_{{ $order->order_id }}" style="border-bottom: 7px solid #e9e9e9 !important">
                                            <td colspan="9">
                                                <center>
                                                <table width="100%" style="font-size:12px;">
                                                    @foreach ( $order->orderProducts as $ordered_product )
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 5%; border:1px solid red"><img src="{{ URL::asset('uploads/inventory_products/'.$ordered_product?->product?->image) }}" /></td>
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
                                                </table>
                                                </center>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @endif
                             </tbody>
                            </table>
                        {{  $data->appends(@$old_input)->render() }}
                    </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@include('orders.edit_customer_popup')
@include('orders.popup_forwordpicklist_courier')
@include('orders.popup_courier_tracking')
@include('orders.popup_order_details')
@include('orders.popup_order_activity_log')
@include('orders.ccvaneue_details')
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
