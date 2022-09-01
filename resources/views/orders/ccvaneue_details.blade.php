<div class="modal fade" id="ccvaneueDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Prepaid Payment Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <div class="modal-body" style="color:black">
      <h5>ORDER ID: <span id="popup-oirder_id"></span></h5>
          <div class="table-responsive">
            <h5><span id="popup-oirder_id"></span></h5>
            <table class="table table-bordered table-hover">
                <thead style="background:lightblue">
                    <tr>
                        <th class="text-center">Tracking Id</th>
                        <th class="text-center">Bank Ref No </th>
                        <th class="text-center">Payment Mode</th>
                        <th class="text-center">Card Name </th>
                        <th class="text-center">Order Status</th>
                        <th class="text-center">Amount </th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Address </th>
                        <th class="text-center">Contact</th>
                        <th class="text-center">bank Qsi No </th>
                        <th class="text-center">bank Receipt No</th>
                    </tr>
                </thead>
                <tbody id="detail_data">

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