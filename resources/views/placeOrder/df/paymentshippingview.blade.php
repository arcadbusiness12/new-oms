<form name="payment-shipping-form" id="payment-shipping-form">
    {{csrf_field()}}
    <div class="cart-totals" style="padding: 37px;">
        <div class="row">
            <div class="col-7 payment_shipping_div">
                <div class="error-messages"></div>
                <div class="form-group">
                    <label class="control-label">Shipping Method</label>
                    <div class="input-group">
                        <select name="shipping_method" class="form-control">
                            <option value="">-- Please Select --</option>
                            <?php if ($shipping_methods) { ?>
                                <?php foreach ($shipping_methods as $key => $value) { ?>
                                    <optgroup label="<?php echo $value['title'] ?>">
                                        <?php foreach ($value['quote'] as $k => $v) { ?>
                                        <option value="<?php echo $v['code'] ?>" <?php if($shipping_method == $v['code']) { ?> selected="selected" <?php } ?>><?php echo $v['title'] ?> - <?php echo $v['text'] ?></option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            <?php } ?>
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
                        <select name="payment_method" class="form-control">
                            <option value="">-- Please Select --</option>
                            <?php if ($payment_methods) { ?>
                                <?php foreach ($payment_methods as $key => $value) {
                                  if($value['code']=='ccavenuepay' ||  $value['code']=='e_wallet_payment') continue;
                                  //if( $e_wallet_balance == 0 && $value['code']=='e_wallet_payment' ) continue;
                                  ?>
                                <option value="<?php echo $value['code']; ?>" <?php if($payment_method == $value['code'] || $value['code'] == 'cod') { ?> selected="selected" <?php } ?>><?php echo $value['title']; ?></option>
                                <?php } ?>
                            <?php } ?>
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
                    <textarea name="comment" class="form-control" placeholder="Write Comment Here..."></textarea>
                </div>
            </div>
            <div class="col-5">
                <table class="table">
                    <?php foreach ($totals as $key => $total) { ?>
                        <tr>
                            <td><b>{{ $key }}</b></td>
                            <td>{{ $total }}</td>
                        </tr>
                    <?php } ?>
                </table>
                <div class="form-group">
                    <button type="button" name="generate_exchange" id="confirm-order" class="btn btn-primary btn-block">Place Order</button>
                </div>
            </div>
        </div>
    </div>
</form>
