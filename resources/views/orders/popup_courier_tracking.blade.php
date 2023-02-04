<div class="modal fade" id="courierTrackingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Order Tracking with courier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <div class="modal-body" style="color:black">
          <div class="table-responsive" >
            <table class="table table-bordered table-hover" style="border:1px solid #3f51b5">
                <thead style="background: #3f51b5; color:white">
                    <tr>
                        <th class="text-center">AWB #</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Shipment Address</th>
                    </tr>
                </thead>
                <tbody id="shipment_data">

                </tbody>
            </table>
          </div>
          <div class="table-responsive">
            <h4>Details</h4>
            <table class="table table-bordered table-hover" style="border:1px solid #3f51b5">
                <thead style="background: #3f51b5; color:white">
                    <tr>
                        <th class="text-center">Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Details</th>
                    </tr>
                </thead>
                <tbody id="activity_data">

                </tbody>
            </table>
          </div>
      </div>
      <div class="modal-footer">
        {{--  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>  --}}
        {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}


      </div>

    </div>
  </div>
</div>
<script>
    function trackOrderCourier(order_id,store_id,courier_name,order_type){
        $('#shipment_data').html('Loading...');
        $('#activity_data').html('Loading...');
        //order_type can be 0 for normal order 1, for exchange, 2 for return
          $.ajax({
            method: "POST",
            url: APP_URL + "/orders/track/courier",
            data: {order_id:order_id,courier_name:courier_name,store_id:store_id,order_type:order_type},
            dataType: 'json',
            cache: false,
            headers:
              {
                  'X-CSRF-Token': $('input[name="_token"]').val()
              },
          }).done(function (res)
          {
            if(res.data){
              var data = res.data;
              var shipment_row = "<tr>";
                  shipment_row += "<td class='text-center'>"+data.awb_number+"</td>";
                  shipment_row += "<td class='text-center'>"+data.current_status+"</td>";
                  shipment_row += "<td class='text-center'>"+data.status_datetime+"</td>";
                  shipment_row += "<td class='text-center'>"+data.shipment_address+"</td>";
                  shipment_row += "</tr>";
              $('#shipment_data').html(shipment_row);
              var activity_rows = "";
              for(const row of data.activity){
                  activity_rows += "<tr>";
                  activity_rows += "<td class='text-center'>"+row.datetime+"</td>";
                  activity_rows += "<td class='text-center'>"+row.status+"</td>";
                  activity_rows += "<td class='text-center'>"+row.details+"</td>";
                  activity_rows += "</tr>";
              }
              $('#activity_data').html(activity_rows);
            }

          }); // End of Ajax
      }
</script>
