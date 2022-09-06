@extends('layouts.app')
@section('content')
<style>
.place_order  .panel-heading{
    background:#fff !important;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}
.place_order .panel-default{
    margin-top:5px !important;
}
.place_order .card{
    margin-top: 5px;
}
</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12 col-sm-12 place_order text-black">
            <div class="card">
                <div class="panel panel-default">
                    <div class="panel-heading" data-toggle="collapse" data-target="#step-1">
                        1: Select Product(s)
                    </div>
                    <div class="panel-body collapse in" id="step-1">
                        <form name="filter_products" id="filter_products" method="get">
                            {{csrf_field()}}
                            <div id="alert-response"></div>
                            <div class="row">
                                <label class="col-2 control-label" for="input-product" style="margin: 0;text-align: right;padding-top: 7px;">Choose Product</label>
                                <div class="col-10 col-grid">
                                    <div class="col-4 col-grid">
                                        <input type="text" name="product_title" id="product_title" list="product_names" class="form-control" autocomplete="off" value="" placeholder="Product Title">
                                        <datalist id="product_names"></datalist>
                                    </div>
                                    <div class="col-4 col-grid">
                                        <input type="text" name="product_model" id="product_model" list="product_models" class="form-control" autocomplete="off" value="" placeholder="Product Model">
                                        <datalist id="product_models"></datalist>
                                    </div>
                                    <div class="col-2 col-grid">
                                        <button type="submit" id="search_filter" class="btn btn-primary pull-right">
                                            <i class="fa fa-filter"></i>
                                            Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form name="product_cart" id="product_search">
                            {{csrf_field()}}
                            <table class="table product_search_table"></table>
                        </form>
                    </div>
                  </div> {{--panel panel-default end --}}
                </div> {{--card end--}}
                <div class="card">
                  <div class="panel panel-default">
                    <div class="panel-heading" data-toggle="collapse" data-target="#step-2">
                        <div class="pull-left">2: Cart</div>
                        <div class="cart-loader"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body hidden collapse" id="step-2">
                        <div class="text-danger text-center text-uppercase font-16"><b>Cart is Empty!</b></div>
                        </div>
                  </div>{{--panel panel-default end --}}
                </div>{{--card end--}}
                <div class="card">
                  <div class="panel panel-default">
                    <div class="panel-heading" data-toggle="collapse" data-target="#step-3">
                        3. Search Customer
                    </div>
                    <div class="panel-body collapse" id="step-3">
                        <div id="alert-response"></div>
                        <div>
                            <form name="filter_customers" id="filter_customers" method="get">
                            {{csrf_field()}}
                                <input type="hidden" name="type" />
                                <div class="col-sm-3 col-grid">
                                    <input type="text" name="name" placeholder="Name" class="form-control" />
                                </div>
                                <div class="col-sm-3 col-grid col-grid">
                                    <input type="text" name="number" placeholder="Mobile Number" class="form-control" />
                                </div>
                                <div class="col-sm-3 col-grid">
                                    <input type="text" name="email" placeholder="Email Address" class="form-control" />
                                </div>
                                <div class="col-sm-3 col-grid">
                                    <button type="button" name="search" class="btn btn-primary search_customer">
                                        <i class="fa fa-filter"></i>
                                        Search
                                    </button>
                                    <button type="button" name="new_customer" class="btn btn-primary search_customer">
                                        <i class="fa fa-plus"></i>
                                        New Customer
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="customer_search_table p-t-50">

                        </div>
                    </div>
                  </div> {{--panel panel-default end --}}
                </div>{{--card end--}}
                <div class="card">
                  <div class="panel panel-default">
                    <div class="panel-heading" data-toggle="collapse" data-target="#step-4">
                        <div class="pull-left">4: Confirm Address</div>
                        <div class="cart-loader"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-collapse collapse panel-body hidden" id="step-4"></div>
                  </div> {{--panel panel-default end --}}
                </div>{{--card end--}}
                <div class="card">
                  <div class="panel panel-default">
                    <div class="panel-heading" data-toggle="collapse" data-target="#step-5">
                        <div class="pull-left">5: Payment & Shipping</div>
                        <div class="cart-loader"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-collapse collapse panel-body hidden" id="step-5"></div>
                  </div>{{--panel panel-default end --}}
                </div> {{-- card no-b  --}}
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
var token = '';
var cookies = '';
var cart_totals = '';
var payment_methods = '';
var shipping_methods = '';
var shipping_method = '';
var payment_method = '';
var api_id = "<?php echo $api['api_id'] ?>";
var username = "<?php echo $api['username'] ?>";
var api_key = "<?php echo $api['key'] ?>";
var store_id = "<?php echo $api['store_id'] ?>";
var currency = "<?php echo $api['currency'] ?>";
var customer = {};
var ajax_url = "<?php echo env('APP_OPENCART_URL') ?>";
var order_success_redirect = "<?php echo $api['order_success_redirect'] ?>";
$(function() {
    $('select').selectpicker('destroy');
    $('select').select2();
});
</script>
<link rel="stylesheet" href="{{URL::asset('assets/plugins/select2/select2.min.css') }}">
<script src="{{URL::asset('assets/plugins/select2/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/place_order.js') }}?_=<?php echo time() ?>"></script>
@endpush
