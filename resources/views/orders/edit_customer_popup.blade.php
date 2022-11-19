<!-- print lable Modal code start-->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit customer details</h5>
        {{--  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>  --}}
      </div>
      <form method="post" action="{{ url('/orders/update-customer-details') }}" id="frm_address">
        {{ csrf_field() }}
        <input type="hidden" name="order_id" id="order_id">
        <div class="modal-body">
          <table class="table table-bordered" style="border:1px solid #0d5793">
            {{--  <thead style="background: #3774a6; color:white">
                  <tr>
                    <td align="center"><b>Fields</b></td>
                    <td align="center"><b>Values</b></td>
                  </tr>
                </thead>  --}}
            <tbody id="address_model_content">
              <tr>
                <td align="center" width="10%">Name</td>
                <td><input type="text" name="name" class="form-control" id="name"></td>
              </tr>
               <tr>
                <td align="center" width="10%">Mobile/Phone</td>
                <td><input type="text" name="telephone" class="form-control" id="telephone"></td>
              </tr>
              <tr>
                <td align="center" width="5%">Address</td>
                <td><input type="text" name="address_1" class="form-control" id="address_1"></td>
              </tr>
              <tr>
                <td align="center">Street,Building</td>
                <td><input type="text" name="street_building" class="form-control" id="street_building"></td>
              </tr>
              <tr>
                <td align="center">Villa,Flat</td>
                <td><input type="text" name="villa_flat" class="form-control" id="villa_flat"></td>
              </tr>
              <tr>
                <td align="center">Area</td>
                <td id="">
                  <input type="text" name="area" id="area" list="areas" class="form-control" autocomplete="off" value="" placeholder="Search Area">
                  <datalist id="areas"></datalist>
                </td>
              </tr>
              <tr>
                <td align="center">City</td>
                <td><input type="text" name="city" class="form-control" id="city"></td>
              </tr>
              <tr>
                <td align="center">Google Map</td>
                <td><input type="text" name="google_map" class="form-control" id="google_map"></td>
              </tr>
              <input type="hidden" id="store" name="store">
            </tbody>
          </table>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger active" data-dismiss="modal">Close</button>
          <input type="submit" value="Update" class="btn btn-success active">
        </div>
      </form>
    </div>
  </div>
</div>
<!-- print lable Modal code end-->
@push('scripts')
<script>
  $(document).on('click', '.btn-edit-customer-adress', function (event) {
    var order_id = $(this).attr('data-orderid');
    var store = $(this).attr('data-store');
    $("#frm_address #order_id").val(order_id);
    $.ajax({
      method: "GET",
      url: APP_URL + "/orders/update-customer-details",
      data: {
        order_id: order_id,
        store: store
      },
      dataType: 'json',
      cache: false,
      headers: {
        'X-CSRF-Token': $('input[name="_token"]').val()
      },
    }).done(function (data) {
      if( typeof data == 'object' ){
        $('#address_1').val( data.payment_address_1 );
        $('#street_building').val( data.shipping_street_building );
        $('#villa_flat').val( data.shipping_villa_flat );
        var html = '';
        data.areas.forEach(function(k,v) {
          html += '<option value="'+k+'">'+k+'</option>';
        });
        // var nhtml = select_s+html+select_e;
        $('#areas').html( html );
        $('#area').val( data.shipping_city_area );
        $('#city').val( data.shipping_city );
        $('#google_map').val( data.google_map_link );
        $('#name').val( data.firstname );
        $('#frm_address input[name="telephone"]').val( data.mobile );
        $('#store').val( data.store );
      }

    }); // End of Ajax
  });
</script>
@endpush
