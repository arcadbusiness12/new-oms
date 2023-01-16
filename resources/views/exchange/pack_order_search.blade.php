<div class="col-xs-12">
    <div class="card no-b">
        <div class="panel panel-default">
            <div class="panel-body table-responsive">
                <?php if($order) { ?>
                <form action="<?php echo URL::to('/exchange/update/pack') ?>" method="post" name="update_quantity" id="form_update_quantity">
                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                    <input type="hidden" name="store" value="{{ $order->store }}">
                    {{ csrf_field() }}
                    <table class="table">
                        <tr class="order_list_title_row" style="background-color: #3f51b5;color:white">
                            <td class="col-xs-4 text-center order_list_title">Order ID</td>
                            <!-- <td class="col-xs-3 text-center order_list_title">Status</td> -->
                            <td class="col-xs-4 text-center order_list_title">Total</td>
                            <td class="col-xs-4 text-center order_list_title">Date Added</td>
                        </tr>
                        <tr class="order_list">
                            <td class="col-xs-4 text-center">{{ $order->order_id }}</td>
                            <!-- <td class="col-xs-3 text-center"><?php echo $order['status'] ? '<label class="label label-success">Delivered</label>' : '<label class="label label-warning">Shipped</label>' ?></td> -->
                            <td class="col-xs-4 text-center">{{ $order->total_amount }}<?php echo $order['total'] ?></td>
                            <td class="col-xs-4 text-center">{{ $order->created_at }}</td>
                        </tr>
                        <?php $packed = 0;
                            $bar_code = '';
                            foreach ($order->exchangeProducts as $key => $orderProduct) {
                            $bar_code = $orderProduct->product_id.$orderProduct->productOption?->option_value_id;
                            ?>
                        <tr class="product_list">
                            <td class="col-xs-2 text-center"><img src="{{ URL::asset('uploads/inventory_products/'.$orderProduct->product?->image) }}" width="100"/></td>
                            <td class="col-xs-6" colspan="1">
                                {{ $orderProduct->name }}
                                <br><br>
                                <div style="display: flex">
                                    <div class="col-xs-3 p-l-0">
                                        @if(  $orderProduct->product?->option_value > 0  )
                                                <strong>{{ $orderProduct->option_name }}</strong> : {{ $orderProduct->option_value }} ,
                                        @endif
                                        <label style="margin-right: 10px;margin-bottom: 0;"><strong>Color : </strong> {{ $orderProduct->product?->option_name }}</label>
                                    </div>
                                    {{--  @if( $option['manual_checkable'] && session('user_group_id') != 1 )
                                      @php
                                        $disable_checkbox = 'onclick="return false"';
                                        //$disable_checkbox = '';
                                      @endphp
                                    @else
                                      @php
                                        $disable_checkbox = "";
                                      @endphp
                                    @endif  --}}
                                    <?php if($orderProduct->quantity) { ?>
                                    <div class="col-xs-9 p-l-0">
                                        <?php for ($i=1; $i <= $orderProduct->quantity; $i++) { ?>
                                        <input type="checkbox" name="packed[{{ $orderProduct->product_id }}][{{ $orderProduct->productOption?->option_value_id }}][]" id="packed{{ $orderProduct->id . $bar_code . $i }}" value="{{ $bar_code }}" data-barcode="{{ $bar_code }}" class="chk-col-green product-pack-bacode-checkbox" required="required" {!! @$disable_checkbox !!}>
                                        {{--<label for="packed<?php echo $product['order_product_id'] . $option['barcode'] . $i ?>" style="margin-bottom: 0;"></label>--}}
                                        <?php } ?>
                                    </div>
                                    <?php } else { ?>
                                    <div class="col-xs-9 p-l-0">
                                        <label class="label label-success">Packed</label>
                                    </div>
                                    <?php } ?>
                                </div>
                            </td>
                            <td class="col-xs-4 text-center">{{ $orderProduct->sku }}</td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="6">
                                <div class="row_update_picked">
                                    <div class="col-xs-12 col-sm-6 col-grid text-center">
                                        <button type="submit" name="submit" id="submit" class="btn btn-success active" value="update_picked" data-value="<?php echo 'PICK'.$order['order_id'] ?>" style="margin-top: 45px;">Click To Pack</button>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-grid text-center">
                                        <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="PICK_ORDER" jsbarcode-textmargin="0" jsbarcode-fontoptions="bold" jsbarcode-height="100" jsbarcode-displayValue="false"></svg>
                                    </div>
                                </div>
                            </td>
                        </tr>
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
