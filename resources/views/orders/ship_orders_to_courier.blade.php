@extends('layouts.app')
@section('title', 'Home')
@section('content')
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12 col-sm-12 col-grid">
         <form method="post" id='ship_orders_to_courier' action="{{ route('orders.ship.orders.to.courier') }}">
            <div class="card p-3 text-black">
                <div class="row container-fluid pt-4">
                    <div class="block-header col-lg-3 col-md-3 col-sm-3 pt-2">
                        <h4>Scan before shipping to Courier</h4>
                    </div>
                    <div class="block-header col-lg-3 col-md-3 col-sm-3">
                        <div class="form-group">
                            <div class="form-line">
                                {{csrf_field()}}
                                <input autocomplete="off" type="text" id="orderId" name="orderId" autofocus class="form-control" placeholder="Scan by order ID">
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{--card end--}}
                @if(Session::has('message'))
                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                @endif
                <div class="card no-b mt-3">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-6 pt-2">
                            Ready Shipments for Courier.
                            </div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success btn-sm float-right">Update Status to Shipped</button>
                            </div>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr style="background-color: #3f51b5;color:white">
                                <th></th>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Customer</th>
                                <th>Date Added</th>
                                <th>Date Modified</th>
                                <th>Telephone</th>
                                <th>Email</th>
                                <th>Total</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody id="ship_to_courier_scan">
                        </tbody>
                    </table>
                </div> {{--card end--}}
            </form>
        </div>
    </div>
</div>
{{--  @include("orders.popup_generate_awb")  --}}
@endsection
@push('scripts')
<script>
$(document).ready(function() {
  $('#orderId').val("");
  $('#ship_orders_to_courier').on('keypress', function(e) {
      var code = e.keyCode || e.which;
      if (code === 13) {
          e.preventDefault();
          // alert($("#orderId").val());
          fetchOrderDetails($("#orderId").val());
      }
  }); // key press

}); // document ready
var shipp_counter = 1;
var shipping_list = [];

function fetchOrderDetails(orderID) {
  var orderId = $('#orderId').val();
  //serial number code start
  var exist = false;
  // $('.col-order_id').each(function(key, row) {
  //     let prev_order_id = $(this).html();
  //     if (prev_order_id == orderId) {
  //         exist = true;
  //         alert("Order # " + orderId + " already scanned.");
  //     }
  // });
  if (shipping_list.includes(orderId)) {
      exist = true;
      alert("AWB # " + orderId + " already scanned.");
  }
  if (exist) {
      return false;
  }
  //serial number code end
  $.ajax({
      method: 'POST',
      // url: APP_URL + '/orders/getOrderDetail',
      // data: { orderId: orderId },
      url: APP_URL + '/orders/get/order/id/from/airwaybill',
      data: { airwaybillno: orderId },
      headers: {
          'X-CSRF-Token': $('input[name="_token"]').val()
      },
     success:function(response) {
        console.log(response);
        $('#ship_to_courier_scan').prepend(response);
        $('.sn_' + orderId).html(shipp_counter++);
        $('.scan-response-container').removeClass('hidden');
        $('#orderId').val('');
        if (response != "") {
            shipping_list.push(orderId);
            $(".ship-scanned-counter").html('<span class="pull-right animate__animated animate__shakeX">' + shipping_list.length + '</span>');
        }
     }
  });
}
</script>
@endpush
