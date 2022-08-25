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
  
  <!-- print lable Modal code start-->
<div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Print Stock Label</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <form method="post" target="_blank" action="" id="frm_print" data-url="{{ route('inventory.print.pending.stock.label') }}">
        {{ csrf_field() }}
      <div class="modal-body" >
          <table class="table table-bordered" style="border:1px solid #0d5793">
              <thead style="background: #0d5793; color:white">
                  <tr>
                  <td align="center"><b>Type</b></td>
                  <td align="center"><b>Size</b></td>
                  <td align="center"><b>Quantity</b></td>
                  <td align="center"><b>No of Print</b></td>
                  </tr>
              </thead>
              <tbody id="printModal_content">
  
              </tbody>
              <tfoot style="border:none !important">
                <td colspan="2">
                  <p>Select Label Size</p>
                  <select class="form-control" name="label_type">
                      <option value="big">Big</option>
                      <option value="small">Small</option>
                  </select>
                </td>
                <td colspan="2">
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <input type="submit" value="Print" class="btn btn-success">
                </div>
                </td>
              </tfoot>
          </table>
            
      </div>
      {{-- <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <input type="submit" value="Print" class="btn btn-success">
      </div> --}}
    </form>
  
      </div>
  </div>
  </div>
  
  <!-- print lable Modal code end-->