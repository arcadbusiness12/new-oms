<div class="modal fade" id="excelSheetModal" tabindex="-1" role="dialog" aria-labelledby="excelSheetModalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="excelSheetModalModalLabel" style="color:red">
            </h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-bs-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body" >
            <form  method='post' id='excel-form' action="{{ URL::to('/accounts/save/pending/receipts') }}">

                 <input type='hidden' name='_token' value='{{ csrf_token() }}'>
                <table class="table table-bordered" style="border: 1px solid #3379b7">
                    <thead style="background-color: #3379b7; color:white">
                        <tr>
                        <td align="center"><b>SN</b></td>
                        <td align="center"><b>Order Id</b></td>
                        <td align="center"><b>AWB No</b></td>
                        <td align="center"><b>Amount</b></td>
                        <td align="center"><b>Courier Delivered</b></td>
                        <td align="center"><b>OMS Delivered</b></td>
                        <td align="center"><b>Payment Recieved</b></td>
                        </tr>
                    </thead>
                    <tbody id="excelSheetModal_content" >

                    </tbody>
                </table>
            </form>
        </div>
        <div class="modal-footer">
        </div>
        </div>
    </div>
    </div>
