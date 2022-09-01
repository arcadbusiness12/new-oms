<div class="modal fade" id="courierTrackingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Order Tracking with courier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <div class="modal-body" style="color:black">
          <div class="table-responsive" >
            <table class="table table-bordered table-hover">
                <thead style="background:lightblue">
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
            <table class="table table-bordered table-hover">
                <thead style="background:lightblue">
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