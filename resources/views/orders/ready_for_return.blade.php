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
                        <div class="row action_button_row" style="display:none">
                            <div class="col-sm-2 exchange_action" style="display:none">
                                <button class="btn btn-info active" id="btn_exchange">Exchange</button>
                            </div>
                            {{--  <div class="col-sm-2 reship_action" style="display:none">
                                <button class="btn btn-warning active" id="btn_reship">Reship</button>
                            </div>  --}}
                        </div>
                        <div class="panel-heading">Ready For Return Orders</div>

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
                                {{--  <th scope="col">&nbsp;</th>  --}}
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
                                    @php
                                        if( $order->OmsOrder?->generatedCourier?->name != ""  ){
                                            $courier = $order->OmsOrder?->generatedCourier?->name;
                                            $courier_link = '<a href="javascript:void(0)" onclick="trackOrderCourier('.$order->order_id.','.$courier.')" data-toggle="modal" data-target="#courierTrackingModal"><span class="badge badge-warning blue darken-1">'.$courier.'</span></a>';
                                        }else{
                                            $courier_link =  $order->OmsOrder?->assignedCourier?->name;
                                        }
                                    @endphp
                                    <tr class="row_{{ $order->order_id }}">
                                        {{--  <td class="col-sm-1"><input type="checkbox" class="order_checkbox" order-status="{{ $order->omsOrder?->oms_order_status }}" value="{{ $order->order_id }}" /></td>  --}}
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
                                            <td colspan="11">
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
                                                </table>
                                                </center>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr  class="order-action row_{{ $order->order_id }}" style="border-bottom: 7px solid #e9e9e9 !important">
                                        <td colspan="11">
                                            <div class="row">
                                                <div class="col-1">
                                                    <button class="btn btn-info btn-sm active" data-orderid="{{ $order->order_id }}" data-store="{{ $order->store  }}" id="order_history" data-toggle="modal" data-target="#historyModal">History</button>
                                                </div>
                                                    <div class="col-1">
                                                        @if( $order->omsOrder?->reship == "-1" )
                                                            <span class="badge badge-success green darken-1">Reship Request sent.</span>
                                                        @else
                                                            <button data-orderid={{ $order->order_id }} data-store={{ $order->store }} class="btn btn-primary active btn-reship-checkbox btn-sm" id="btn-reship-checkbox" style="display: block;">Reship</button>
                                                        @endif
                                                    </div>
                                                <div class="col-sm-7">
                                                </div>
                                                <div class="col-sm-3">
                                                    <form action="{{ route('orders.ready.for.return') }}" method="POST" >
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="order_id_for_return" value="{{ $order->order_id }}" />
                                                        <input type="hidden" name="store_id" value="{{ $order->store }}" />
                                                        <input type="hidden" name="admin_comment" id="admin_approve_comment_{{ $order->order_id }}"  />
                                                        <button  type="submit" class="btn btn-success" name="approve_return_button" id="approve_return_button_{{ $order->order_id }}" value="approve_return_button" style="display:none">Approve Return</button>
                                                        <a href="javascript:void()" class="btn btn-success btn-sm active float-right" onclick="approveComments({{ $order->order_id }})">Approve Return</a>
                                                      </form>
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
{{--  @include('orders.edit_customer_popup')  --}}
{{--  @include('orders.popup_forwordpicklist_courier')  --}}
@include('orders.popup_courier_tracking')
@include('orders.popup_order_details')
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
    function approveComments(order_id){
        swal({
          title: "Enter details for this action.",
          text: " ",
          type: "input",
          showCancelButton: true,
          cancelButtonColor: "#DD6B55",
          closeOnConfirm: true,
          animation: "slide-from-top",
          inputPlaceholder: "Write comment."
        },
        function(inputValue){
          //alert(inputValue);
          $('#admin_approve_comment_'+order_id).val(inputValue);
           if ( inputValue === false) {
           }else{
              $('#approve_return_button_'+order_id).click();
           }
        });
      }
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
     } //End of if (inputValue === false || inputValue === "")
}); //end of swal

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
</script>
@endpush
