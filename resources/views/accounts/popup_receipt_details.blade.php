<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            {{--  <h5 class="modal-title" id="exampleModalLabel">Order Activity Log</h5>  --}}
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="receiptModal_content">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" onclick="printContent('print_able')">Print</button>
            {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
        </div>
    </div>
</div>
