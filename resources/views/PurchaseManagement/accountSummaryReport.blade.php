@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3" id="accounts">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="card no-b">
                                <div class="card-header white">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <form name="filter_orders" id="filter_orders" method="get" action="<?php echo route('accounts') ?>">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <div class="form-line">
                                                                <select name="supplier" id="supplier" class="form-control custom-select select_supplier">
                                                                    <option value=""></option>
                                                                    <?php foreach ($users as $user) { ?>
                                                                    <option value="<?php echo $user['user_id'] ?>" ><?php echo $user['name'] ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-6">
                                                        <input type="text" name="order_id" id="order_id" placeholder="Search By Order No" class="form-control select_supplier">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div> 
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12" id="result_summary">
                    
            </div>
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

