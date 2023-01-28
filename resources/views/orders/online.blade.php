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
                        <div class="panel-heading">Online Orders</div>

                        {{--  <div class="panel-heading">Inventory Dashboard</div>  --}}
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif

                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="font-size: 14px !important; color:black !important">
                             <thead>
                              <tr style="background-color: #3f51b5;color:white">
                                <th scope="col">&nbsp;</th>
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
                                        <td class="col-sm-1"><input type="checkbox" class="order_checkbox" order-status="{{ $order->omsOrder?->oms_order_status }}" value="{{ $order->order_id }}" /></td>
                                        <td class="col-sm-1"><center>{{ $order->order_id }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->firstname }} {{ $order->lastname }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center><span class="badge orange darken-1"><strong>{{  $order->omsStore->name  }}</strong></span></center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->created_at }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->OmsOrder?->updated_at  }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->mobile }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->email }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->total_amount }} </center></td>
                                    </tr>
                                    <tr class="row_{{ $order->order_id }}">
                                        <td>&nbsp;</td>
                                        <td colspan="2"><strong>Address:</strong>{{ $order->shipping_city_area }},{{ $order->shipping_address_1 }},{{ $order->shipping_street_building }},{{ $order->shipping_villa_flat }}</td>
                                        <td colspan="2"><strong>City:</strong>{{ $order->shipping_city }}</td>
                                        <td colspan="4" >
                                             <div class="normal-order-progress">
                                                @include('orders.order_progress_bar')
                                              </div>
                                        </td>
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
                                    <tr  class="order-action row_{{ $order->order_id }}" style="border-bottom: 7px solid #e9e9e9 !important">
                                        <td colspan="11">
                                                    <div class="row">
                                                        {{--  <div class="col-1">
                                                            <button class="btn btn-sm active btn-warning" data-orderid="{{ $order->order_id }}" data-store="{{ $order->store  }}" id="order_history" data-toggle="modal" data-target="#historyModal">History</button>
                                                        </div>  --}}
                                                        @if ( $order->omsOrder?->oms_order_status < 2 )
                                                             @if( (!empty($created_by) && $created_by->user_id == session('user_id') ) ||  session('role')=='ADMIN')
                                                                <div class="col-1">
                                                                    <a  href="javascript:void(0)" class="waves-effect waves-blue active" data-toggle="tooltip" data-placement="top" data-original-title="Cancel Order">
                                                                        <form action="{{URL::to('orders/cancel-order')}}" id="cancel_order_form_{{ $order->order_id }}">
                                                                            {{csrf_field()}}
                                                                            <input type="hidden" name="order_id" value="{{ $order->order_id }}" />
                                                                            <input type="hidden" name="store" value="{{ $order->store }}" />
                                                                            <button order_id="{{ $order->order_id }}" type="button" class="btn btn-danger btn-sm active cancel-order">
                                                                                Cancel Order
                                                                            </button>
                                                                        </form>
                                                                    </a>
                                                                </div>
                                                                <div class="col-1">
                                                                    <a data-orderid={{ $order->order_id }} data-store={{ $order->store }} data-toggle="modal" data-target="#addressModal"
                                                                    class="btn btn-info btn-sm active btn-edit-customer-adress">Edit Details</a>
                                                                </div>
                                                            @endif
                                                        @endif
                                                        <div class="col-sm-7">
                                                        </div>
                                                        @if ( $order->omsOrder?->oms_order_status != 5 )
                                                            <div class="col-sm-2">
                                                                    <form action="{{ route('orders.online.approve') }}" method="POST">
                                                                        {{csrf_field()}}
                                                                        <input type="hidden" name="order_id" value="{{$order->order_id}}" />
                                                                        <input type="hidden" name="oms_store" value="{{$order->store}}" />
                                                                        <input type="submit" value="Approve" class="btn btn-success">
                                                                    </form>
                                                            </div>
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
{{--  @include('orders.popup_forwordpicklist_courier')
@include('orders.popup_courier_tracking')
@include('orders.popup_order_details')
@include('orders.popup_order_activity_log')
@include('orders.ccvaneue_details')--}}

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
$(".cancel-order").on('click', function() {
    var order_id = $(this).attr("order_id");
    var id_to_remove = ".row_" + order_id;
    swal({
            title: "Are you sure?",
            text: "You want to cancel this order?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, I am sure!',
            cancelButtonText: "No, I don't want!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
            if (isConfirm) {
                swal({ title: "<h2>Please Wait canceling order...</h3>", html: true, text: loader, showConfirmButton: false });
                $.ajax({
                    method: "POST",
                    url: $("#cancel_order_form_" + order_id).attr("action"),
                    data: $("#cancel_order_form_" + order_id).serialize()
                }).done(function(response) {
                    if (response.success == false) {
                        swal({ title: "<h2>Error!</h3>", html: true, text: response.error.message, type: "error" });
                        //$('[data-toggle="tooltip"]').tooltip('destroy');
                    } else {
                        //$('[data-toggle="tooltip"]').tooltip('destroy');
                        $(id_to_remove).remove();
                        swal("Success!", "Order Canceled", "success");
                        setTimeout(function() {
                            swal.close();
                        }, 1500);
                    }
                }); // End of Ajax

            } else {
                swal.close();
                //swal("Cancelled", "Your process is canceled", "error");
            }
        });
});
</script>
{{--  cancel order end  --}}
<!-- Sweet alert css -->
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<!-- SweetAlert Plugin Js -->
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
@endpush
