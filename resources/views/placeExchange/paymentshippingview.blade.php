<form name="payment-shipping-form" id="payment-shipping-form">
    {{csrf_field()}}
    <div class="cart-totals" style="padding: 37px;">
        <div class="alert alert-danger confirm_error d-none"></div>
        <div class="row">
            <div class="col-7 payment_shipping_div">
                <div class="error-messages"></div>
                <div class="form-group">
                    <label class="control-label">Shipping Method</label>
                    <div class="input-group">
                        <select name="shipping_method" id="sb_shipping_method" class="form-control">
                            <option value="">-- Please Select --</option>
                               @if ($shipping_methods)
                                @foreach ($shipping_methods as $key => $value)
                                        <option value="{{ $value->id }}" @selected( $value->id == @$shipping_method['id'] )>{{ $value->name }}</option>
                                @endforeach
                                @endif
                        </select>
                        <span class="input-group-btn">
                            <button type="button" id="button-shipping-method" class="btn btn-primary">Apply</button>
                        </span>
                    </div>
                </div>
                @if( $e_wallet_balance > 0 )
                  <div class="form-group">
                    <div class="col-11" style="border:1px solid lightgray"><b>Customer has amount {{ $e_wallet_balance }} AED in his/her E-Wallet.</b><br>&nbsp;</div>
                    <div class="col-1">
                      <span class="input-group-btn">
                        <button type="button" id="button-use-e-wallet" class="btn btn-primary">Apply</button>
                      </span>
                    </div>
                  </div>
                @endif
                <div class="form-group">
                    <label class="control-label">Payment Method</label>
                    <div class="input-group">
                        <select name="payment_method" id="sb_payment_method" class="form-control">
                            <option value="">-- Please Select --</option>
                            @if ($payment_methods)
                                @foreach ($payment_methods as $key => $value)
                                <option value="{{ $value->id }}" @selected( $value->id == @$payment_method['id'] ) >{{ $value->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="input-group-btn">
                            <button type="button" id="button-payment-method" class="btn btn-primary">Apply</button>
                        </span>
                    </div>
                </div>
                {{--  <div class="form-group">
                    <label class="control-label">Coupon Code</label>
                    <div class="input-group">
                        <input type="text" name="coupon_code" class="form-control" placeholder="Enter Coupon Code" />
                        <span class="input-group-btn">
                            <button type="button" id="button-coupon-code" class="btn btn-primary">Apply</button>
                        </span>
                    </div>
                </div>  --}}
                <div class="form-group">
                    <label class="control-label">Comment</label>
                    <textarea name="comment" id="comment" class="form-control" placeholder="Write Comment Here..."></textarea>
                </div>
            </div>
            <div class="col-5">
                <table class="table">
                        @php
                            $g_total = 0;
                        @endphp
                        @foreach ($totals as $key => $total)
                            @php
                            $g_total += $total;
                            @endphp
                        <tr>
                            <td><b>{{ $key }}</b></td>
                            <td>{{ $total }}</td>
                        </tr>
                        @endforeach

                        <tr>
                            <td><b>Total:</b></td>
                            <td><strong>{{ ( $g_total < 1 ) ? 0 : $g_total }}<strong></td>
                        </tr>
                </table>
                <div class="form-group">
                    <button type="button" name="generate_exchange" id="confirm-order" class="btn btn-primary btn-block">Place Order</button>
                </div>
            </div>
        </div>
    </div>
</form>
