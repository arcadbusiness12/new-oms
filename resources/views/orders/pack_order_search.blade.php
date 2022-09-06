<div class="col-xs-12">
    <div class="card no-b">
        <div class="panel panel-default">
            <div class="panel-body table-responsive">
                <?php if($order) { ?>
                <form action="<?php echo URL::to('/orders/update/pack/order') ?>" method="post" name="update_quantity" id="form_update_quantity">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?>">
                    <input type="hidden" name="store" value="<?php echo $order['store'] ?>">
                    {{ csrf_field() }}
                    <table class="table">
                        <tr class="order_list_title_row">
                            <td class="col-xs-4 text-center order_list_title">Order ID</td>
                            <!-- <td class="col-xs-3 text-center order_list_title">Status</td> -->
                            <td class="col-xs-4 text-center order_list_title">Total</td>
                            <td class="col-xs-4 text-center order_list_title">Date Added</td>
                        </tr>
                        <tr class="order_list">
                            <td class="col-xs-4 text-center"><?php echo $order['order_id'] ?></td>
                            <!-- <td class="col-xs-3 text-center"><?php echo $order['status'] ? '<label class="label label-success">Delivered</label>' : '<label class="label label-warning">Shipped</label>' ?></td> -->
                            <td class="col-xs-4 text-center"><?php echo $order['total'] ?></td>
                            <td class="col-xs-4 text-center"><?php echo $order['date'] ?></td>
                        </tr>
                        <?php $packed = 0; foreach ($order['products'] as $key => $product) { ?>
                        <tr class="product_list">
                            <td class="col-xs-2 text-center"><img src="<?php echo $product['image'] ?>" width="100"/></td>
                            <td class="col-xs-6" colspan="1">
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
                                        <input type="checkbox" name="packed[<?php echo $product['product_id'] ?>][<?php echo $option['option_value_id'] ?>][]" id="packed<?php echo $product['order_product_id'] . $option['barcode'] . $i ?>" value="<?php echo $option['barcode'] ?>" data-barcode="<?php echo $option['barcode'] ?>" class="chk-col-green product-pack-bacode-checkbox" required="required" {!! $disable_checkbox !!}>
                                        <!--<label for="packed<?php echo $product['order_product_id'] . $option['barcode'] . $i ?>" style="margin-bottom: 0;"></label>-->
                                        <?php } ?>
                                    </div>
                                    <?php } else { ?>
                                    <div class="col-xs-9 p-l-0">
                                        <label class="label label-success">Packed</label>
                                    </div>
                                    <?php } ?>
                                </div>
                                <br>
                                <?php } ?>
                            </td>
                            <td class="col-xs-4 text-center"><?php echo $product['model'] ?></td>
                        </tr>
                        <?php } ?>
                        <?php if(count($order['products']) != $packed){ ?>
                        <tr>
                            <td colspan="6">
                                <div class="row_update_picked">
                                    <div class="col-xs-12 col-sm-6 col-grid text-center">
                                        <button type="submit" name="submit" id="submit" class="btn btn-success" value="update_picked" data-value="<?php echo 'PICK'.$order['order_id'] ?>" style="margin-top: 45px;">Shipment Picked</button>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-grid text-center">
                                        <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="PICK_ORDER" jsbarcode-textmargin="0" jsbarcode-fontoptions="bold" jsbarcode-height="100" jsbarcode-displayValue="false"></svg>
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
