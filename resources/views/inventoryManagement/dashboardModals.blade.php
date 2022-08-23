<!-- product view modal start -->
<div class="modal fade porduct_view_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Product details</h5>
          <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button> -->
        </div>
        <div class="modal-body" id="porduct_view_content">
          ...
        </div>
        <div class="modal-footer">
                  <button type="button" class="btn btn-warning history-btn">History</button>
          <button type="button" class="btn btn-danger dismiss-btn" data-dismiss="modal">Close</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
        <div class="table-responsive" id="product_option_orders"></div>
        <div class="table-responsive" id="history-tbl" style="max-height: 300px;display: none;">
              <table class="table table-bordered table-hover">
                  <thead style="background-color: #eee;">
                      <tr>
                          <th class="text-center">User Name</th>
                          <th class="text-center">History</th>
                          <th class="text-center">Reason</th>
                          <th class="text-center">Date</th>
                      </tr>
                  </thead>
                  <tbody class="history">
                  <tr class="loaderr">
                    
                  </tr>
                  </tbody>
              </table>
              <div class="msge text-center" style="display: none;">
                No history found..
             </div>
             <div class="history-load text-center" style="display: none;">
                <p class="spinner-border text-muted" ></p>
             </div>
          </div>
          
      </div>
    </div>
  </div>
  <!-- product view modal end -->
  <!-- product location modal start -->
<div class="modal fade porduct_location_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Edit Location</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="porduct_location_content">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
      </div>
    </div>
  </div>
  <!-- product location modal end -->
<!-- edit inventory modal start -->
<div class="modal fade edit_inventory_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Edit Inventory</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="edit_inventory_content">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
      </div>
    </div>
  </div>
  <!-- edit invertory modal end -->  