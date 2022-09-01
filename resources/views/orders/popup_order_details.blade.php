<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        {{--  <h5 class="modal-title" id="exampleModalLabel">Select Products to Return.</h5>  --}}
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
        <h5>Select Products to Return.</h5>
          {{--  <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-bordered table-hover">
                <thead style="background-color: #eee;">
                    <tr>
                        <th class="text-center">Order ID</th>
                        <th class="text-center">Address</th>
                        <th class="text-center">Courier</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Date</th>
                    </tr>
                </thead>
                <tbody class="content">
                <tr class="loaderr"  style="display: none;">
                  <td colspan="6" class="spinner-border text-muted" >History Loadding..</td>
                </tr>
                </tbody>
            </table>
          </div>  --}}
          <div class="orderDetailsModal_content"></div>
        </div>
      </div>
      <div class="modal-footer">
        {{--  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>  --}}
        {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        
        
      </div>

    </div>
  </div>
</div>