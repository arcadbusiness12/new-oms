<div class="modal fade" id="forward_for_awb_generation_options" tabindex="-1" role="dialog" style="display: none;">
    <form id="awb_generation_options_from" method="post">
        {{ csrf_field() }}
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="largeModalLabel">Select Order Status to be updated and Shipping Provider</h4>
                </div>
                <hr />
                <div class="modal-body  col-black ">
                    <div class="form-float">
                        <div class="col-sm-5 col-grid text-black">
                            <label class="form-label" for="open_cart_order_status">Order Status</label>
                            <select required="required" name="open_cart_order_status" id="open_cart_order_status" class="form-control" data-live-search="true">
                                <option></option>
                                @foreach($orderStatus as $status)
                                <option {{ $status['order_status_id'] === 15 ? 'selected' : '' }} value="{{ $status['order_status_id']}}">{{$status['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="form-float">
                        <div class="col-sm-5 col-grid text-black" id="shipping-provider-select1">
                            <label class="form-label" for="shipping_providers">Shipping Provider</label>
                            <select name="shipping_providers" id="shipping_providers" class="form-control" data-live-search="true">
                                @foreach($shippingProviders as $provider)
                                <option value="{{ $provider['shipping_provider_id']}}_{{ $provider['name']}}">{{$provider['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="col-lg-12 col-sm-12 col-grid loader_contanier d-none">
                        <span class="p-b-10"> Please wait generating airwaybills ...</span>
                    </div>

                </div>
                <div class="clearfix col-lg-12 col-xs-12 response" style="color:white"></div>
                <div class="clearfix modal-footer m-t-30">

                    <button form-action-data="{{URL::to('orders/forward/for/shipping')}}" id='confirm_awb_generation' type="button" class="btn btn-success waves-effect">Confirm Generation</button>
                    <button type="button" class="btn btn-danger waves-effect close-btn" data-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </form>
</div>
