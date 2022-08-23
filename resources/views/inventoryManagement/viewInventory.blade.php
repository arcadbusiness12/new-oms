<form action="<?php echo URL::to('/inventory_manage/update_add_stock') ?>" method="post" name="update_stock">
    {{ csrf_field() }}
  <div class="row">
   <div class="col-12 col-md-12 col-sm-12">
    <div class="col-sm-3" style="display: inline-block; float: left;">
        {{-- <img src="{{URL::asset('uploads/inventory_products/'.$product['image'])}}" width="100%"> --}}
                <img src="{{URL::asset('uploads/inventory_products/'.$product['image'])}}" width="100%">
                <b>{{ $product['option_name'] }}</b>  <span class="pull-right"><b>{{ $product['sku'] }}</b></span>
                <input type="hidden" name="product_id" value="{{$product['product_id']}}" />
    </div>
    <div class="col-sm-9" style="display: inline-block; float: left;">

        <div class="table-responsive">
                <table class="table" style="width: 100%" >
                        <?php if($product['options'] && count($product['options']) > 1 && isset($product['options']['static'])) { ?>
                        <td class="col-xs-4 text-center" style="vertical-align: initial">
                            <br>
                            <label><?php echo $product['options']['static']['name'] ?> : <?php echo $product['options']['static']['value'] ?></label>
                            <?php unset($product['options']['static']); ?>
                        </td>
                        <?php $colspan = '3'; } else { $colspan = '4'; } ?>
                                <tr style="background-color: #e6ffe6; border:1px solid black">
                                    <td class="col-xs-2 text-center" style="padding: 4px; border-right:1px solid;"><b>Size</b></td> 
                                    <td class="col-xs-2 text-center" style="padding: 4px; border-right:1px solid;"><b>Available.Qty</b></td>
                                    <td class="col-xs-2 text-center" style="padding: 4px; border-right:1px solid;"><b>Onhold.Qty</b></td>
                                    <td class="col-xs-2 text-center" style="padding: 4px; border-right:1px solid;"><b>Packed.Qty</b></td>
                                    <td class="col-xs-2 text-center" style="padding: 4px; border-right:1px solid;"><b>Shipped.Qty</b></td>
                                    <td class="col-xs-2 text-center" style="padding: 4px; border-right:1px solid;"><b>Deliver.Qty</b></td>
                                    <td class="col-xs-2 text-center" style="padding: 4px; border-right:1px solid;"><b>Return.Qty</b></td>
                                    <td class="col-xs-2 text-center" style="padding: 4px; border-right:1px solid;"><b>Updated.Qty</b></td>
                                    <td class="col-xs-2 text-center" style="padding: 4px; border-right:1px solid;"><b>Rack</b></td>
                                    <td class="col-xs-2 text-center" style="padding: 4px; border-right:1px solid;"><b>Shelf</b></td>
                                </tr>
                                @foreach($product_options as $options)
                                <tr class=" update_fields">
                                    <td class="col-xs-2 text-center" style="padding: 3px;">{{$options->value}}</td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;">{{$options->available_quantity}}</td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;">{{$options->onhold_quantity}}</td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;">{{$options->pack_quantity}}</td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;">{{$options->shipped_quantity}}</td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;"><a href="javascript:void()" onclick="showProductOptionOrders({{$options->product_id}},{{$options->option_id}},{{$options->option_value_id}})">{{$options->delivered_quantity}}</a></td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;">{{$options->return_quantity}}</td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;">{{$options->updated_quantity}}</td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;">{{$options->rack}}</td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;">{{$options->shelf}}</td>
                                </tr>
                                @php
                                @$tot_aq += $options->available_quantity;
                                @$tot_onq += $options->onhold_quantity;
                                @$tot_packq += $options->pack_quantity;
                                @$tot_shipq += $options->shipped_quantity;
                                @$tot_delq += $options->delivered_quantity;
                                @$tot_retq += $options->return_quantity;
                                @endphp
                                @endforeach
                                <tr class=" update_fields" style="background-color: #e6ffe6; border:1px solid black">
                                    <td class="col-xs-2 text-center" style="padding: 3px;"><strong>Total:</strong></td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;"><strong>{{$tot_aq}}</strong></td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;"><strong>{{$tot_onq}}</strong></td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;"><strong>{{$tot_packq}}</strong></td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;"><strong>{{$tot_shipq}}</strong></td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;"><strong>{{$tot_delq}}</strong></td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;"><strong>{{$tot_retq}}</strong></td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;"><strong>Row</strong></td>
                                    <td class="col-xs-2 text-center" style="padding: 3px;"><strong>{{ $product['row'] }}</strong></td>
                                </tr>
                </table>
        </div>
                <!--</td>
        </tr>
    </table>-->
    </div>
  </div>
</div>
</form>

<!-- History Table -->

