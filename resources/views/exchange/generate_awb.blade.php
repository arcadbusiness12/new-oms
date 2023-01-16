@extends('layouts.app')
@section('title', 'Home')
@section('content')
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12 col-sm-12 col-grid">
            <div class="card p-3 text-black">
                <div class="row panel panel-default container-fluid">
                    <div class="block-header col-lg-3">
                        <h4>Scan Barcode to Generate AWB</h4>
                    </div>
                    <div class="block-header col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <form name="generateAWB" id="generateAWB"  method="POST">
                            <input type="text" name="barcode" id="barcode" class="form-control" autocomplete="off" placeholder="Scan Barcode" />
                        </form>
                    </div>
                </div>
            </div>
            <div class="card no-b mt-3">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading text-right">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="pull-left text-left s-16 text-black">
                                            <strong>Airway Bill Generation</strong>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="generate-awb-btn pull-right">
                                            <button data-toggle="modal" data-target="#forward_for_awb_generation_options" class="btn btn-success waves-effect pull-left active btn-generate-awb">Generate AirwayBills</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="body table-responsive in-process-orders">
                                <table class="table orders-table">
                                    <thead>
                                        <tr  style="background-color: #3f51b5;color:white">
                                            <th>&nbsp;</th>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th></th>
                                            <th>Date Added</th>
                                            <th>Date Modified</th>
                                            <th>Telephone</th>
                                            <th>Email</th>
                                            <th>Total</th>
                                            <th>Option</th>
                                        </tr>
                                    </thead>
                                    <tbody id="order_detail">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{--card end--}}
        </div>
    </div>
</div>
@include("exchange.popup_generate_awb")
@endsection
@push('scripts')
    <script>
      $('.btn-generate-awb').on('click', function() {
          $.ajax({
              url: "{{url('/get/shipping/providers')}}/"+ $('#order_shipping_type').val(),
              type: 'GET',
              cache: false,
              success: function(resp) {
              var select = '<select name="shipping_providers" id="shipping_providers" class="form-control" data-live-search="true">'
              var op = '<option value="0">-Select Courier From List-</option>';
              resp.providers.forEach(function(v) {
                  console.log(v.name);
                  op += '<option value="'+v.shipping_provider_id+'_'+v.name+'">'+v.name+'</option>';
              });
              var endselect = '</select>';
              var shtml = select + op + endselect;
              console.log(shtml);
              $('#shipping-provider-select1').html(shtml);
              }

          });
      });
      var awb_order_counter = 1;
      $('#barcode').focus();
      $('#generateAWB').submit(function(e) {
          e.preventDefault();
          var orderId = $('#barcode').val();
          //serial number code start
          var exist = false;
          $('.col-order_id').each(function(key, row) {
              let prev_order_id = $(this).html();
              if (prev_order_id == orderId) {
                  exist = true;
                  alert("Order # " + orderId + " already scanned.");
              }
          });
          if (exist) {
              return false;
          }
          //serial number code end
          $.ajax({
              method: 'POST',
              url: APP_URL + '/exchange/get/exchange/detail',
              data: { orderId: orderId },
              headers: {
                  'X-CSRF-Token': $('input[name="_token"]').val()
              },
              success: function(response) {
                $('#order_detail').prepend(response);
                $('.sn_' + orderId).html(awb_order_counter++);
                $('#barcode').val('');
             }
          });
      });
      //confirm button
      $("#confirm_awb_generation").click(function() {
        $(this).prop('disabled', true);
        reset_forward_order_for_awb_generation_modal();
        $(".loader_contanier").removeClass("d-none");
        var orderIDs = new Array();
        $("tbody input.fwd-ordr-generate-awb-checkbox[type=checkbox]:checked").each(function() {
            orderIDs.push($(this).val());
        });
        $.ajax({
            method: "POST",
            url: $(this).attr("form-action-data"),
            data: {
                orderIDs: orderIDs,
                open_cart_order_status: $('#open_cart_order_status').val(),
                shipping_providers: $('#shipping_providers').val()
            },
            headers: {
                'X-CSRF-Token': $('input[name="_token"]').val()
            }
        }).done(function(response) {
            $(".loader_contanier").addClass("d-none");
            $(".response").html(response.data);
            if (response.success === true) {
                $(orderIDs).each(function(index, value) {
                    var id_to_remove = ".row_" + value;
                    $(id_to_remove).fadeOut(300, function() { $(this).remove(); });
                });
                window.open(APP_URL + '/exchange/awb', '_blank');
                //$('#forward_for_awb_generation_options').modal('toggle');
            }
            $("#confirm_awb_generation").removeProp('disabled');
        }); // End of Ajax

    }); // Confirm awb generation click
    function reset_forward_order_for_awb_generation_modal() {
        $(".loader_contanier").addClass("d-none");
        $(".response").html('');
    }
    </script>
@endpush

