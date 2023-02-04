<div class="col-xs-12">
    <div class="card">
        <div class="panel panel-default">
            <div class="panel-body table-responsive">
                @if($order)
                <form action="{{ route('exchange.update.return') }}" method="post" name="update_quantity" id="form_update_quantity">
                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                    <input type="hidden" name="oms_store" value="{{ $order->store }}">
                    {{ csrf_field() }}
                    <table class="table">
                        <tr class="order_list_title_row">
                            <td class="col-xs-3 text-center order_list_title">Order ID</td>
                            <!-- <td class="col-xs-3 text-center order_list_title">Status</td> -->
                            {{--  <td class="col-xs-3 text-center order_list_title">Total</td>  --}}
                            <td class="col-xs-3 text-center order_list_title">Date Added</td>
                            <td class="col-xs-3 text-center order_list_title">Print Label</td>
                        </tr>
                        <tr class="order_list">
                            <td class="col-xs-3 text-center">{{  $order->order_id }}</td>
                            {{--  <td class="col-xs-3 text-center"><?php echo $order['total'] ?></td>  --}}
                            <td class="col-xs-3 text-center">{{ $order->created_at }}</td>
                            <td class="col-xs-3 text-center"><a href="{{ URL::to('/exchange/print/label/' . $order->order_id) }}" target="_blank"><button type="button" class="btn btn-primary">Print</button></a></td>
                        </tr>
                        <?php $returned = 0;
                        //echo "<pre>"; print_r($order->order_products->toArray()); die;
                        foreach ($order->order_products as $key => $product) {
                          $barcode = "";
                            ?>
                        <tr class="product_list">
                            <td class="col-xs-2 text-center"><img src="{{ URL::asset('uploads/inventory_products/'.$product->product->image) }}" width="100"/></td>
                            <td class="col-xs-6" colspan="2">
                                {{ $product->sku }}
                                <br><br>
                                <div style="display: flex">
                                    <div class="col-xs-3 p-l-0">
                                        <label style="margin-right: 10px;margin-bottom: 0;">{{ $product->option_name }} - {{ $product->option_value }}</label>
                                    </div>
                                    @if( $product->quantity )
                                    <div class="col-xs-9 p-l-0">
                                        <?php for ($i=1; $i <= $product->quantity; $i++) { ?>
                                        <input type="checkbox" name="returned[{{ $product->product_id }}][{{ $product->productOption->option_value_id }}][]" id="returned{{ $product->product_id . $barcode . $i }}" value="{{ $barcode }}" data-barcode="{{ $barcode }}" class="chk-col-green product-return-bacode-checkbox" required="required">
                                        <?php } ?>
                                    </div>
                                    @else
                                    <div class="col-xs-9 p-l-0">
                                        <label class="label label-success">Returned</label>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="col-xs-4 text-center">{{ $product->sku }}</td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="6">
                                <div class="row row_update_returned d-none">
                                    <div class="col-sm-6 text-center">
                                        <button type="submit" name="submit" id="submit" class="btn active btn-success" value="update_returned" data-value="{{  'RETURN'.$order->order_id }}" style="margin-top: 45px;">Shipment Returned</button>
                                    </div>
                                    <div class="col-sm-6 text-center">
                                        <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="RETURN_ORDER" jsbarcode-textmargin="0" jsbarcode-fontoptions="bold" jsbarcode-height="100" jsbarcode-displayValue="false"></svg>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
                @else
                    <div class="alert alert-danger text-center">Order Not Found!</div>
                @endif
            </div>
        </div>
    </div>
</div>
<style>
    [type="checkbox"]:not(:checked), [type="checkbox"]:checked{
        left: unset!important;
    }
</style>
