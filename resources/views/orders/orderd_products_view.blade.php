<table class="table products">
        {{--	<the        ad>
        <tr>
        <th>Product Image<        /th>
        <th>Product</th>
    <th>Product	Model</th>
<th    >Quantity</th>
    <th>Unit Price</th>
    <th>Total</th>
		</tr>
	</thead> --}}
    <tbody>
    @foreach ($order['orderd_products'] as $pkey => $product )
    <tr class="col-black">
        <td class="row_product_checkbox_{{ $product['order_id'] }}">
          <label for="md_checkbox_{{ $product['order_product_id'] }}" product-checkbox-for="{{ $product['order_id'] }}" style="display: none;"><input name="product_id[]" value="{{ $product['order_product_id'] }}" data-value-quantity="{{$product['quantity']}}" type="checkbox" id="md_checkbox_{{ $product['order_product_id'] }}" class="chk-col-green exchange-order-btn-generate-checkbox form-control" product-checkbox-for="{{ $product['order_id'] }}" style="display: none;  width:10px" disabled></label>

        </td>
        <td width="5%"><img width="100" src="{{$product['product_details']['image']}}" /></td>
        <td>
            {{ $product['name'] }}
            @if (count($product['order_options']) > 0)
            <div class="m-t-5">
                @foreach ($product['order_options'] as $option)
                @if ($product['order_product_id'] == $option['order_product_id'])
                <span>{{$option['name']}} : </span><strong>{{$option['value']}} </strong>
                @if(!$loop->last)
                <label>|</label>
                @endif
                @endif
                @endforeach
            </div>
            @endif
        </td>
        <td>{{ $product['model'] }}</td>
        <td>{{$product['quantity']}}</td>
        <td>{{$product['price']}}</td>
        <td>{{$product['total']}}</td>
        @if( $pkey == 0 && $order['ewallet_used'] != "" )
          <td align="right" style="border:1px solid red; vertical-align: middle;" width="11%">
             @forelse ($order['orderd_totals'] as $orderd_total)
               <p><small> {{ $orderd_total['title'] }}:
               {{ number_format($orderd_total['value'],2) }}</small></p>
             @empty
            @endforelse
          </td>
        @endif

    </tr>
    @endforeach
</tbody>
</table>



