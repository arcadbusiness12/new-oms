@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3" id="accounts">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-12 col-sm-12" id="result_summary">
                    
<div class="card no-b">
    <div class="panel-heading">
     WithDraw Requests
     <span class="pull-right" style="float:right;">
        <button type="button" class="btn btn-primary" id="open-payment-modal">Payment</button>
    </span>
    </div>
   
 <div class="panel panel-default panel_order_list">
     <div class="panel-body">
         <table class="table" style="border: 1px solid #3f51b5">
             <thead  style="background-color: #3f51b5;color:white">
                 <th class="text-center">User</th>
                 <th class="text-center">Withdraw</th>
                 <th class="text-center">Date</th>
                 <th class="text-center">Status</th>
                 <th class="text-center">Action</th>
             </thead>
             <?php 
             if($requests) { 
                 $requests1 = $requests->toArray();
                 ?>
                <?php foreach ($requests1['data'] as $request) { ?>
                <tr class="text-center">
                    <td><?php echo @$request['user']['firstname'] ?></td>
                    <td><?php echo number_format(@$request['amount'],2) ?></td>
                    <td><?php echo date('d-m-Y', strtotime(@$request['created_at'])); ?></td>
                    <td>
                        <?php if(@$request['status'] == 0){ ?>
                        <div class="badge badge-warning" style="font-size: 12px;"><b>Pending</b></div>
                        <?php } else if(@$request['status'] == 1){ ?>
                        <div class="badge badge-success" style="font-size: 12px;"><b>Approved</b></div>
                        <?php } else { ?>
                        <div class="badge badge-danger" style="font-size: 12px;"><b>Rejected</b></div>
                        <?php }?>
                    </td>
                    <td>
                        <?php if(@$request['status'] == 0){ ?>

                    <form action="<?php echo URL::to('/PurchaseManagement/update/withdraw/request/status') ?>" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="hidden" name="request_id" value="<?php echo @$request['request_id'] ?>" />
                            <input type="hidden" name="amount" value="<?php echo @$request['amount'] ?>" />
                            <input type="hidden" name="user_id" value="<?php echo @$request['user_id'] ?>" />
                            <button type="submit" name="submit" value="approve" class="btn btn-success active">Approve</button>
                            <button type="submit" name="submit" value="reject" class="btn btn-danger active">Reject</button>
                            <button type="button" name="pay" class="btn btn-info pay-payment active">Pay</button>
                        </div>

                    </form>
                        <?php } else { ?>
                            <label>-</label>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                    <td colspan="5" class="text-center">No Requests Found!</td>
                </tr>
                <?php } ?>
         </table>
     </div>
     <div class="account-summary-pagination-area text-left">
         {{ @$requests->links() }}
     </div>
 </div>
 </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="payment-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <b><h4 class="modal-title">Payment</h4></b>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="<?php echo URL::to('/PurchaseManagement/withdraw/payment') ?>" method="post" name="payment-form" id="payment-form" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="row">
                    <div class="response-message"></div>
                    <div class="col-xs-12">
                        <label class="control-label">Supplier</label>
                        <div>
                            <select name="supplier" class="form-control">
                                <?php foreach ($requests['suppliers'] as $supplier) { ?>
                                    <option value="<?php echo $supplier['user_id'] ?>"><?php echo $supplier['firstname'] . ' ' . $supplier['lastname'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label class="control-label">Amount</label>
                        <div>
                            <input type="text" pattern="^(\d*\.)?\d+$" title="Enter valid price" name="amount" class="form-control" required />
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label class="control-label">Date</label>
                        <div>
                            <input type="date" name="date" class="form-control" required />
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label class="control-label">Receipt (Optional)</label>
                        <div>
                            <input type="file" name="receipt" class="form-control" required />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="send_payment" id="send_payment" class="btn btn-primary">Send Payment</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript" src="{{URL::asset('assets/js/purchase_management.js') }}?_=<?php echo time() ?>"></script>
<link rel="stylesheet" href="{{URL::asset('assets/css/purchase.css') }}">
<script>
</script>
@endpush

