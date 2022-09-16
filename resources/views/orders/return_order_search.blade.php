<div class="col-xs-12">
    <div class="card">
        <div class="panel panel-default">
            <div class="panel-body table-responsive">
                <?php if($order && $order['products']) { ?>
                <form action="{{ route('orders.update.return.order') }}" method="post" name="update_quantity" id="form_update_quantity">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>">
                    <input type="hidden" name="oms_store" value="{{ $order['oms_store'] }}">
                    {{ csrf_field() }}
                    <table class="table">
                        <tr class="order_list_title_row">
                            <td class="col-xs-3 text-center order_list_title">Order ID</td>
                            <!-- <td class="col-xs-3 text-center order_list_title">Status</td> -->
                            <td class="col-xs-3 text-center order_list_title">Total</td>
                            <td class="col-xs-3 text-center order_list_title">Date Added</td>
                            <td class="col-xs-3 text-center order_list_title">Print Label</td>
                        </tr>
                        <tr class="order_list">
                            <td class="col-xs-3 text-center"><?php echo $order['order_id'] ?></td>
                            <!-- <td class="col-xs-3 text-center"><?php echo $order['status'] ? '<label class="label label-success">Delivered</label>' : '<label class="label label-warning">Shipped</label>' ?></td> -->
                            <td class="col-xs-3 text-center"><?php echo $order['total'] ?></td>
                            <td class="col-xs-3 text-center"><?php echo $order['date'] ?></td>
                            <td class="col-xs-3 text-center"><a href="<?php echo URL::to('/orders/print_label/' . $order['order_id']) ?>" target="_blank"><button type="button" class="btn btn-primary">Print</button></a></td>
                        </tr>
                        <?php $returned = 0; foreach ($order['products'] as $key => $product) { ?>
                        <tr class="product_list">
                            <td class="col-xs-2 text-center"><img src="<?php echo $product['image'] ?>" width="100"/></td>
                            <td class="col-xs-6" colspan="2">
                                <?php echo $product['name'] ?>
                                <br><br>
                                <?php foreach ($product['options'] as $option) { ?>
                                <div style="display: flex">
                                    <div class="col-xs-3 p-l-0">
                                        <label style="margin-right: 10px;margin-bottom: 0;"><?php echo $option['option'] ?></label>
                                    </div>
                                    @if( $option['manual_checkable'] && session('user_group_id') != 1 )
                                      @php
                                        $disable_checkbox = 'onclick="return false"';
                                        //$disable_checkbox = '';
                                      @endphp
                                    @else
                                      @php
                                        $disable_checkbox = "";
                                      @endphp
                                    @endif
                                    <?php if($option['quantity']) { ?>
                                    <div class="col-xs-9 p-l-0">
                                        <?php for ($i=1; $i <= $option['quantity']; $i++) { ?>
                                        <input type="checkbox" name="returned[<?php echo $product['product_id'] ?>][<?php echo $option['option_value_id'] ?>][]" id="returned<?php echo $product['order_product_id'] . $option['barcode'] . $i ?>" value="<?php echo $option['barcode'] ?>" data-barcode="<?php echo $option['barcode'] ?>" class="chk-col-green product-return-bacode-checkbox" {!! $disable_checkbox !!} required="required">
                                        <!--<label for="returned<?php echo $product['order_product_id'] . $option['barcode'] . $i ?>" style="margin-bottom: 0;"></label>-->
                                        <?php } ?>
                                    </div>
                                    <?php } else { ?>
                                    <div class="col-xs-9 p-l-0">
                                        <label class="label label-success">Returned</label>
                                    </div>
                                    <?php } ?>
                                </div>
                                <br>
                                <?php } ?>
                            </td>
                            <td class="col-xs-4 text-center"><?php echo $product['model'] ?></td>
                        </tr>
                        <?php } ?>
                        <?php if(count($order['products']) != $returned){ ?>
                        <tr>
                            <td colspan="6">
                                <div class="row row_update_returned d-none">
                                    <div class="col-sm-6 text-center">
                                        <button type="submit" name="submit" id="submit" class="btn active btn-success" value="update_returned" data-value="<?php echo 'RETURN'.$order['order_id'] ?>" style="margin-top: 45px;">Shipment Returned</button>
                                    </div>
                                    <div class="col-sm-6 text-center">
                                        <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="RETURN_ORDER" jsbarcode-textmargin="0" jsbarcode-fontoptions="bold" jsbarcode-height="100" jsbarcode-displayValue="false"></svg>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </form>
                <?php }else{ ?>
                <div class="alert alert-danger text-center">Order Not Found!</div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<style>
    [type="checkbox"]:not(:checked), [type="checkbox"]:checked{
        left: unset!important;
    }
</style>
