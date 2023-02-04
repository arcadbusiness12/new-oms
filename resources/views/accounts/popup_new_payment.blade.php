<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            {{--  <h5 class="modal-title" id="exampleModalLabel">Order Activity Log</h5>  --}}
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="paymentModal_content">
          <form method="post">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-8">
                <input type="text" class="form-control" name="payment_description" placeholder="Enter Description" required>
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-sm-8">
               <input type="number" name="payment_amount" class="form-control" placeholder="amount" required>
              </div>

            </div>
            <br>
            <div class="row">
              <div class="col-sm-10">
                <input type="submit" name="payment_submit" value="Submit"  id="payment_submit" class="btn active btn-success float-right">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
            {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
            {{-- <button type="button" class="btn btn-success" onclick="printContent('print_able')">Print</button> --}}
            {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
        </div>
    </div>
    </div>
