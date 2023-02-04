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
                            @include('return.return_search_section')
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">All Return Orders</div>

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
                                        <td class="col-sm-1"><center>{{ $order->order_id }}-2</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->placeOrder?->firstname }} {{ $order->placeOrder?->lastname }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center><span class="badge badge-warning blue darken-1">{{ $order->courier_name }}</span><span class="badge orange darken-1"><strong>{{  $order->omsStore->name  }}</strong></span></center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->created_at }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->updated_at  }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->placeOrder?->mobile }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->placeOrder?->email }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center> - </center></td>
                                    </tr>
                                    <tr class="row_{{ $order->order_id }}">
                                        <td>&nbsp;</td>
                                        <td colspan="2"><strong>Address:</strong>{{ $order->placeOrder?->shipping_city_area }},{{ $order->placeOrder?->shipping_address_1 }},{{ $order->placeOrder?->shipping_street_building }},{{ $order->placeOrder?->shipping_villa_flat }}</td>
                                        <td colspan="2"><strong>City:</strong>{{ $order->placeOrder?->shipping_city }}</td>
                                        <td colspan="3" >
                                             <div class="normal-order-progress">
                                                @include('return.return_progress_bar')
                                              </div>
                                        </td>
                                    </tr>
                                    @if( $order->returnProducts )
                                        <tr class="row_{{ $order->order_id }}">
                                            <td colspan="8">
                                                <center>
                                                <table width="100%" style="font-size:12px;">
                                                    @foreach ( $order->returnProducts as $ordered_product )
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
                                                <div class="col-1">
                                                    <button class="btn btn-sm active btn-warning" data-orderid="{{ $order->order_id }}" data-store="{{ $order->store  }}" id="order_history" data-toggle="modal" data-target="#historyModal">History</button>
                                                </div>
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
{{--  @include('orders.edit_customer_popup')
@include('orders.popup_forwordpicklist_courier')
@include('orders.popup_courier_tracking')
@include('orders.popup_order_details')
@include('orders.ccvaneue_details')  --}}
@include('orders.popup_order_activity_log')

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
    $(document).on('change','.order_checkbox',function(){
        var $this = $(this);
        var order_id  = $this.val();
        //alert( $this.attr('order-status') );
        if( $this.is(':checked') && $this.attr('order-status') == 3 ){
            $('.action_button_row, .exchange_action, .reship_action').show();
            //$('.exchange_action').show();
            //$('.reship_action').show();
        }else{
            $('.action_button_row, .exchange_action, .reship_action').hide();
        }

    });
    $(document).on('click','#btn_exchange',function(){
        var checked_order_id = $('.order_checkbox:checked').val();
        //alert(checked_order_id);
        $('.product_'+checked_order_id).show();
    });
    $(document).on('change','.check_product_for_exchange',function(){
        var $this = $(this);
        var order_id = $this.attr('order-id');
        //alert(order_id);
        if( $('.product_'+order_id).is(':checked') ){
            $('#create_exchange_'+order_id).show();
        }else{
            $('#create_exchange_'+order_id).hide();
        }
    });
    //------------
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
    {{--  reship code start  ------------------------------}}
  $(document).on('click', '#btn-reship-checkbox', function(event) {
    var order_id = $(this).attr('data-orderid');
    var store = $(this).attr('data-store');
   swal({
      title: "Are you sure?",
      text: "If you want to send Reship request for this order then type reason in below box. ",
      type: "input",
      showCancelButton: true,
      closeOnConfirm: true,
      animation: "slide-from-top",
      inputPlaceholder: "Write comment."
    },
    function(inputValue){
      console.log(inputValue);
      if (inputValue === false || inputValue === ""){
          console.log("first if");
          return false;
      }else{
        var orignal_text = $('#btn-reship-checkbox').text();
        $('#btn-reship-checkbox').text('wait').prop('disabled',true);
        $.ajax({
          method: "POST",
          url: APP_URL + "/orders/reship",
          data: {order_id:order_id,store:store,comment:inputValue},
          dataType: 'json',
          cache: false,
          headers:
          {
              'X-CSRF-Token': $('input[name="_token"]').val()
          },
        }).done(function (data)
        {
          if(data.status){
              // $('#msg').addClass('alert alert-success').html(data.msg);
              $('#msg').html(data.msg).fadeIn('slow').addClass("alert alert-success").delay(3000).fadeOut('slow');
              location.reload();
          }else{
              // $('#msg').addClass('alert alert-danger').html(data.msg);
              $('#msg').html(data.msg).fadeIn('slow').addClass("alert alert-danger").delay(3000).fadeOut('slow');
          }
          $('#btn-reship-checkbox').text(orignal_text).prop('disabled',false);
        }); // End of Ajax
  }
});

});
{{--  reship code end  --}}
function getOrderHistory(phone) {
    //$('.history').html('Loading...');
      $.ajax({
          method: 'POST',
          url: APP_URL + '/orders/get/user/order/history',
          data: {phone:phone},
          cache: false,
          headers: {
              'X-CSRF-Token': $('input[name="_token"]').val()
          },
      }).done(function (data) {
          var html = '';
          $('.history-order-tab').html('');
          if(data.length > 0) {

              data.forEach(function (v) {
                html +=  '<tr><td>'+v.order_id+'</td><td>'+v.address+'</td><td>'+v.courier+'</td><td>'+v.status+'</td><td>'+v.total+'</td><td>'+v.date_added+'</td></tr>'
              })
          }else{
              html += '<tr> <td colspan="6" class="spinner-border text-muted" >No History..</td> </tr>';
          }
          $('.history-order-tab').html(html);
      })
  }
  $(".forward_order_to_oms").on('click', function() {
    console.log("Ok");
    var id_to_remove = ".row_" + $(this).attr("order_id");
    var order_id = $(this).attr("order_id");
    swal({ title: "<h2>Please Wait forwarding order...</h3>", html: true, text: loader, showConfirmButton: false });
    var courier_id = 0;
    if ($('#courier_id').length) {
        var courier_id = $('#courier_id').val();
    }
    $.ajax({
        method: "POST",
        url: $("#forward_to_queue_form_" + order_id).attr("action"),
        data: $("#forward_to_queue_form_" + order_id).serialize() + "&courier_id=" + courier_id
    }).done(function(response) {
        if(response.status == false && response.courier == 'no') {
            $('.worning-message').text('Please select courier.');
            $('.worning-message').css('color', 'red');
            $('.worning-message').css('font-size', '18px');
            swal.close();
            setTimeout(() => {
                $('.worning-message').text('');
            },5000);
            return false;
        }
        if (response !== "") {
            swal({ title: "<h2>Error!</h3>", html: true, text: response, type: "error" });
            //$('[data-toggle="tooltip"]').tooltip('destroy');
            $('.popup_btn_forword').addClass('d-none');
            $('#courierModal').modal('hide');
        } else {
            //$('[data-toggle="tooltip"]').tooltip('destroy');
            $(id_to_remove).remove();
            swal("Success!", "Order Forwarded", "success");
            setTimeout(function() {
                swal.close();
            }, 1500);
            $('.popup_btn_forword').addClass('d-none');
            $('#courierModal').modal('hide');
        }

    }); // End of Ajax


}); // forward_order_to_oms click
</script>
@endpush