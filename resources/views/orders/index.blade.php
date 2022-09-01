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
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->courier_name }}</center></td>
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
                                    <tr  class="order-action" style="border-bottom: 7px solid #e9e9e9 !important">
                                        <td><a class="btn btn-sm btn-warning" data-orderid="{{ $order->order_id }}" data-store="{{ $order->store  }}" id="order_history" data-toggle="modal" data-target="#historyModal">History</a></td>
                                        <td colspan="4">
                                            @if( (!empty($created_by) && $created_by->user_id == session('user_id') ) ||  session('role')=='ADMIN')
                                                <div class="row">
                                                    <div class="col-2">
                                                        <a  href="javascript:void(0)" class="waves-effect waves-blue" data-toggle="tooltip" data-placement="top" data-original-title="Cancel Order">
                                                            <form action="{{URL::to('orders/cancel-order')}}" id="cancel_order_form_{{ $order->order_id }}">
                                                                {{csrf_field()}}
                                                                <input type="hidden" name="order_id" value="{{ $order->order_id }}" />
                                                                <input type="hidden" name="store" value="{{ $order->store }}" />
                                                                <button order_id="{{ $order->order_id }}" type="button" class="btn btn-danger btn-sm cancel-order">
                                                                    Cancel Order
                                                                </button>
                                                            </form>
                                                        </a>
                                                    </div>
                                                    <div class="col-2">
                                                        <a data-orderid={{ $order->order_id }} data-store={{ $order->store }} data-toggle="modal" data-target="#addressModal"
                                                        class="btn btn-info btn-sm  btn-edit-customer-adress">Edit Details</a>
                                                    </div>
                                                </div>
                                            @endif
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
    {{--  cancel order end  --}}
</script>
@endpush
