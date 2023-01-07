@extends('layouts.app')
@section('title', 'Home')
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
        margin-top: 15px;
    }
</style>
@if( !$store_data )
    <h2>Store not found.</h2>
    @php
        die();
    @endphp
@endif
    <input type="hidden" id="txtbox_store_id" value="{{ $store_data->id  }}">
    <div class="container-fluid relative animatedParent animateOnce my-3">
        <h4>Place Order in {{ $store_data->name }} For Order no 200033</h4>
        <div class="row row-eq-height my-3 mt-3">
            <div class="col-md-12 col-sm-12 place_order text-black">
                <div class="card" style="border:1px dashed red">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            These Products will be return from customer.
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-8">
                                    <table class="table">
                                        @if( is_array($data) && count($data) > 0 )
                                        <tbody>
                                            @php
                                             $total_exchange_amount = 0;
                                            @endphp
                                            @foreach ($data as $key=>$product )
                                                @php
                                                    $image = $product['product']['image'];
                                                    $total_exchange_amount +=  $product['total'];
                                                @endphp
                                                <tr>
                                                    <td style="width: 11%;"><img src="{{ URL::asset('uploads/inventory_products/'.$image) }}" /></td>
                                                    <td>{{ $product['name'] }}<br>
                                                        @if(  $product['product']['option_value'] > 0  )
                                                            <strong>{{ $product['option_name'] }}</strong> : {{ $product['option_value'] }} ,
                                                        @endif
                                                        <strong>Color : </strong>{{ $product['product']['option_name'] }}</td>
                                                        <td>{{ $product['sku'] }}</td>
                                                        <td><input type="number" value="{{ $product['quantity'] }}" id="exchange_product_quantity{{ $product['product_option_id'] }}" onkeyup="calculateExchangeTotal({{ $product['product_option_id'] }},{{ $product['price'] }})" onchange="calculateExchangeTotal({{ $product['product_option_id'] }},{{ $product['price'] }})" size="5" min="1" max="{{ $product['quantity'] }}" /></td>
                                                        <td>{{ $product['price'] }}</td>
                                                        <td><input type="text" value="{{ $product['total'] }}" class="exchange_product_total" id="exchange_product_total{{ $product['product_option_id'] }}" size="7"  readonly /> </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-sm-4" style="text-align: center;">
                                    <h3>Total Customer Amount</h3>
                                     <input type="text" id="tatal_exchange_amount" value="{{ $total_exchange_amount }}" style="text-align: center; height: 39px;" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                        {{--  <div class="col-4 col-grid">
                                            <input type="text" name="product_title" id="product_title" list="product_names" class="form-control" autocomplete="off" value="" placeholder="Product Title">
                                            <datalist id="product_names"></datalist>
                                        </div>  --}}
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
                            <form name="product_cart" id="frm_add_to_cart">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-12 product_search_table p-40">

                                    </div>
                                </div>
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
                        <div class="panel-body collapse" id="step-2">
                            <div class="alert alert-danger d-none" id="alert_error_cart">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <span></span>
                            </div>
                            <div id="step-2-cart" class="p-40">
                            </div>
                        </div>
                      </div>{{--panel panel-default end --}}
                    </div>{{--card end--}}
                    <div class="card">
                      <div class="panel panel-default">
                        <div class="panel-heading" data-toggle="collapse" data-target="#step-3" id="heading-step-3">
                            3. Search Customer
                        </div>
                        <div class="panel-body collapse" id="step-3">
                            <div id="alert-response"></div>
                            <div class="p-40">
                                <form name="filter_customers" id="filter_customers" method="get">
                                {{csrf_field()}}
                                    <input type="hidden" name="type" />
                                    <div class="col-sm-3 col-grid">
                                        <input type="text" name="name" id="customer_name" placeholder="Name" class="form-control" />
                                    </div>
                                    <div class="col-sm-3 col-grid col-grid">
                                        <input type="text" name="number" id="customer_mobile" placeholder="Mobile Number" class="form-control" />
                                    </div>
                                    <div class="col-sm-3 col-grid">
                                        <input type="text" name="email" id="customer_email" placeholder="Email Address" class="form-control" />
                                    </div>
                                    <div class="col-sm-3 col-grid">
                                        <button type="submit" name="search" class="btn btn-primary search_customer">
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
                            <div class="customer_search_table p-40">

                            </div>
                        </div>
                      </div> {{--panel panel-default end --}}
                    </div>{{--card end--}}
                    {{--<div class="card">
                      <div class="panel panel-default">
                        <div class="panel-heading" data-toggle="collapse" data-target="#step-4">
                            <div class="pull-left">4: Confirm Address</div>
                            <div class="cart-loader"></div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-collapse collapse panel-body hidden" id="step-4"></div>
                      </div>
                    </div>card end--}}
                    <div class="card">
                      <div class="panel panel-default">
                        <div class="panel-heading" data-toggle="collapse" data-target="#step-5" id="heading-step-5">
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
var customer = {};
</script>
{{--  <link rel="stylesheet" href="{{URL::asset('assets/plugins/select2/select2.min.css') }}">
<script src="{{URL::asset('assets/plugins/select2/select2.full.min.js') }}"></script>  --}}
<script type="text/javascript" src="{{URL::asset('assets/js/place_exchange.js') }}?_=<?php echo time() ?>"></script>

{{--  $(function() {
    $('select').selectpicker('destroy');
});  --}}

@endpush
