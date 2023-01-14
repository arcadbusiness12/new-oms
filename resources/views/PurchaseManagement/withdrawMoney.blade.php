@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3" id="accounts">
            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-4 col-sm-4 offset-4 mt-5" id="result_summary">
                        
                        <div class="card no-b mt-5" style="box-shadow: 1px 1px 5px 5px;">
                                <div class="panel-heading">
                                    <div class="pull-left" style="width: 50%;float: left;font-weight: 600;">Withdraw Money</div>
                                    <div class="pull-right" style="width: 50%;text-align: right;float: left;font-weight: 600;"><b>Balance: <?php echo (isset($balance->balance)) ? number_format($balance->balance,2) : 0.00; ?></b></div>
                                    <div class="clearfix"></div>
                                </div>
                                <?php if(Session::has('message')) { ?>
                                <div class="alert <?php echo Session::get('alert-class', 'alert-success') ?> alert-dismissible">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <?php echo Session::get('message') ?>
                                </div>
                                <?php } ?>
                                <div class="panel-body">
                                    <form action="<?php echo route('withdraw.money') ?>" method="post">
                                        {{ csrf_field() }}
                                        <div class="form-group form-float mt-4 ml-2 mr-2">
                                            <div class="form-line">
                                                <input type="text" pattern="^(\d*\.)?\d+$" title="Enter valid price" name="amount" class="form-control amount-field" id="amount-field" placeholder="Enter Amount" required/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" name="submit" value="withdraw_money" class="btn btn-block btn-success">Withdraw</button>
                                        </div>
                                    </form>
                                </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript" src="{{URL::asset('assets/js/purchase_management.js') }}?_=<?php echo time() ?>"></script>
<link rel="stylesheet" href="{{URL::asset('assets/css/purchase.css') }}">
<script>
    $(document).ready(function() {
        $('#amount-field').focus();
    })

</script>
@endpush

