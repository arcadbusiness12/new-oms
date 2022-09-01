<div class="modal fade" id="courierModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Courier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="row text-center" >
        <div class="clo-sm-12">
          <span class="worning-message"></span>
        </div>
      </div>
      <div class="modal-body">
        
        <div class="col-sm-8" id="shipping-provider-select">
         <select name="courier_id" class="form-control " id="courier_id" onchange="showForwordButton(this.value)">
           <option value="0">-Select Courier From List-</option>
           @forelse($couriers as $key => $courier)
             <option value="{{ $courier->shipping_provider_id }}">{{ $courier->name }}</option>

           @empty
            <option>No data found.</option>
           @endforelse
         </select>
        </div>
        <div class="col-sm-3">
         <button class="btn btn-success forward_order_to_oms popup_btn_forword hidden">Forword</button>
        </div>
      </div>
      <div class="modal-footer">
        {{--  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>  --}}
        {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        
        
      </div>
      <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-bordered table-hover">
                <thead style="background-color: #eee;">
                    <tr>
                        {{-- <th class="text-center">User</th> --}}
                        <th class="text-center">Order ID</th>
                        <th class="text-center">Address</th>
                        {{-- <th class="text-center">Customer</th> --}}
                        <th class="text-center">Courier</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Date</th>
                    </tr>
                </thead>
                <tbody class="history">
                <tr class="loaderr"  style="display: none;">
                  <td colspan="6" class="spinner-border text-muted" >History Loadding..</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function showForwordButton(courier_id){
    courier_id = parseInt(courier_id);
    if( courier_id > 0 ){
      $('.popup_btn_forword').removeClass('hidden');
    }else{
      $('.popup_btn_forword').addClass('hidden');
    }
  }
</script>